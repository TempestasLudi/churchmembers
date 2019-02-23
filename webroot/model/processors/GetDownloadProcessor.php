<?php

/**
 * This processor handles a request for getting a file.
 */
class GetDownloadProcessor extends AbstractProcessor {

  private $_file;
  private $file;
  private $_downloaddir = DOWNLOAD_PATH;
  private $downloaddir;
  private $fileextenstion;
  private $filecontent;
  private $request;

  public function processRequest() {

    if (empty($_REQUEST['file'])) {
      header("HTTP/1.0 404 Not Found");
      die();
    }
    $this->downloadFile();
  }

  public function createDownload($filecontent, $fileextenstion) {
    // Create random filename
    $letters = 'aA1bB2cC3dD4eEf5Fg6Gh7Hi8Ij9Jk0KlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ';
    srand((double) microtime() * 1000000);
    $string = '';
    for ($i = 1; $i <= rand(4, 12); $i++) {
      $q = rand(1, 62);
      $string = $string . $letters[$q];
    }

    // Delete old downloads
    $weekago = strtotime("-1 week");
    $handle = opendir($this->_downloaddir);
    while ($dir = readdir($handle)) {
      if (is_dir($this->_downloaddir . $dir)) {
        if ($dir !== "." && $dir !== "..") {

          $files = glob($this->_downloaddir . $dir . '/*', GLOB_MARK);
          foreach ($files as $file) {
            if ((filemtime($file) < $weekago)) {
              $php_status = @unlink($file);
            }
          }
          @rmdir($this->_downloaddir . $dir);
        }
      }
    }

    closedir($handle);

    $this->filename = $string . '.' . $fileextenstion;
    $this->encodeddir = crc32($this->filename);
    $this->downloaddir = $this->_downloaddir . $this->encodeddir;
    mkdir($this->downloaddir, 0777);

    $this->filecontent = $filecontent;

    return $this->writeFile();
  }

  private function writeFile() {
    $handle = fopen($this->downloaddir . '/' . $this->filename, "w+");
    $return = array('status' => 'start');

    if (is_writable($this->downloaddir . '/' . $this->filename)) {

      if (fwrite($handle, $this->filecontent) === FALSE) {
        trigger_error(serialize(array("errtype" => "E_USER_FAILWRITEFAIL")), E_USER_ERROR);
        $return['status'] = 'error';
      } else {
        $return['filename'] = $this->filename;
        $return['url'] = BASE_URL . 'download/' . $this->filename;
        $return['msg'] = '<h2>' . __("Selected file can be downloaded") . '</h2>' .
                '</br></br>' .
                __('For our and your own (data) protection, please don\'t send this document per E-mail. Instead copy the following link into your mail if you want to distribute this document. The download will stay for a week on the server');
        $return['status'] = 'finish';
      }

      fclose($handle);
    } else {
      trigger_error(serialize(array("errtype" => "E_USER_FAILWRITEFAIL")), E_USER_ERROR);
      $return['status'] = 'error';
    }

    return $return;
  }

  private function downloadFile() {
    $this->_file = $_REQUEST['file'];
    $this->cleanInput();
    $this->setHeaders();
    $this->startDownload();
  }

  private function cleanInput() {
    $this->file = preg_replace("/[^a-zA-Z0-9\/_|+ .-]/", '', $this->_file);
    $this->filelocation = DOWNLOAD_PATH . crc32($this->file) . '/' . $this->file;
  }

  private function setHeaders() {
    /* Required for IE, otherwise Content-disposition is ignored */
    if (ini_get('zlib.output_compression'))
      ini_set('zlib.output_compression', 'Off');

    /* Output HTTP headers that force "Save As" dialog */
    header("Pragma: public, no-cache");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=" . $this->file . ";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . @filesize($this->filelocation));
  }

  private function startDownload() {
    /* Prevent the script from timing out for large files */
    set_time_limit(0);

    /* Send the entire file using @ to ignore all errors */
    @readfile($this->filelocation);

    /* Exit immediately so no garbage follows the file contents */
    exit;
  }

}

?>