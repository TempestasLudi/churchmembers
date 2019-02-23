<?php
/**
 * A class to print the mappings form, a select box to choose a database column to map to a cvs field.
 */
class MappingsForm {

  private $labelIgnoreFirstLine;
  private $labelCreateAddresses;
  private $database;
  private $noShow;

  /**
   * Initializes a new MappingsForm
   */
  public function __construct() {
    $this->database = new Database();
    $this->labelCreateAddresses = 'Create grouped addresses based on same address';
    $this->labelIgnoreFirstLine = ' Ignore first line, it contains column headers';
    $this->noShow = array('ADR_id', 'ADR_archive', 'ADR_familyname', 'MEMBER_id');
  }

  /**
   * Prints the form in HTML
   */
  function printMappingsForm($firstLine,  $separator) {
    $lineParts = explode($separator, $firstLine);
    $columnsMembers = $this->database->getColumnNames('members');
    $columnsAddresses = $this->database->getColumnNames('addresses');
    $columns = array_merge($columnsMembers, $columnsAddresses);
    $columns = array_diff($columns, $this->noShow);
    $columns = array_values($columns);
    ?>

<form action="#" id="mappingsform">
  <input type="checkbox" name="columnheaders" value="true" /><?php print($this->labelIgnoreFirstLine)?><br />
  <input type="checkbox" name="createaddresses" value="true" /><?php print($this->labelCreateAddresses)?><br /><br />
  <input type="hidden" name="separator" value="<?php print($separator) ?>" />
  <table>
    <tr>
      <th>CSV field</th>
      <th>Database field</th>
    </tr>

        <?php
        for($i=0; $i<max(count($columns), count($lineParts)); $i++) {
          $linePart = $i<count($lineParts) ? $lineParts[$i] :'&nbsp;';
          $databaseField = $i<count($columns) ? $columns[$i] :'&nbsp;';
          ?>

    <tr>
      <td><input type="text" value="<?php print($linePart) ?>" /></td>
      <td><?php $this->printDatabaseOption($columns, $i) ?></td>
    </tr>

          <?php
        }
        ?>

  </table>
  <button type="button" onclick="postForm('admin', 'uploadcsv', 'admintabs-3', 'mappingsform')">Import data</button>
</form>

    <?php
  }

  /**
   * Prints a selectbox with all database fields provided, the $currentIndex will be the selected value.
   */
  function printDatabaseOption($fieldNames, $currentIndex) {
    print("<select name='fields[$currentIndex]'>");
    foreach($fieldNames as $fieldName) {
      if($fieldName == $fieldNames[$currentIndex]) {
        print("<option selected='true'>");
      } else {
        print('<option>');
      }
      print($fieldName);
      print('</option>');
    }
    print('</select>');
  }
}
?>