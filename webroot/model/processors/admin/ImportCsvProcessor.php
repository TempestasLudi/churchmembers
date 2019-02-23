<?php

/**
 * Processor capable of importing a cvs as memebers into the database.
 */
class ImportCsvProcessor extends AdminProcessor {

  private $defaultSeparator = ',';
  private $targetDir = '../../includes/fileuploader/server/uploads/';

  /**
   * Processes the fileupload request.
   */
  public function processRequest() {
    // Handle uploading of file and print mapping form
    if (isset($_POST['CSV_FILE'])) {
      $_SESSION['CSV_FILE'] = $this->targetDir . $_POST['CSV_FILE'];
      chmod($_SESSION['CSV_FILE'], 0666);
      $file = fopen($_SESSION['CSV_FILE'], 'r');
      $firstLine = fgets($file);
      fclose($file);
      $mappingPrinter = new MappingsForm();
      $mappingPrinter->printMappingsForm($firstLine, $this->defaultSeparator);
    } elseif (isset($_POST['fields'])) {
      $offset = isset($_POST['columnheaders']) ? 1 : 0;
      $importer = new ImportExecutor($_SESSION['CSV_FILE'], $this->defaultSeparator, $_POST['fields'], $offset);
      if (isset($_POST['createaddresses'])) {
        $importer->addMembersCorrelated();
      } else {
        $importer->addMembersUncorrelated();
      }
      unset($_SESSION['CSV_FILE']);
      print("Number of addresses imported: " . $importer->countAddresses);
      print("<br />");
      print("Number of members imported: " . $importer->countMembers);
    }
  }

}

?>