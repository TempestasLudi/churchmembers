<?php

/**
 * Processor to handle request to upload a photo
 */
class PhotoUploadProcessor extends AbstractProcessor {

  /**
   * Directory where fileuploads are placed by the file upload script.
   */
  private $sourceDir = 'includes/fileuploader/server/uploads/';

  /**
   * Processes the photo upload request.
   */
  public function processRequest() {
    $targetFile = PHOTO_URL . ($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id) . '_' . md5($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id) . '.jpg';
    $targetPath = BASE_PATH . $targetFile;
    $sourcePath = BASE_PATH . $this->sourceDir . $_POST['PHOTO_FILE'];

    if (isset($_POST['PHOTO_FILE']) && file_exists($sourcePath)) {

      if (rename($sourcePath, $targetPath)) {
        chmod($targetPath, 0600);
        $this->database->editDataVerify($_SESSION['CURRENT-VIEW']['CURRENT_MEMBER']->MEMBER_id, 'MEMBER_id', 'members', 'MEMBER_photo', $targetFile);
        return;
      }
    }
    trigger_error(__("Photo upload has failed"), E_USER_ERROR);
  }

}

?>