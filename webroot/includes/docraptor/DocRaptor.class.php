<?php

/**
 * DocRaptor
 *
 * @author Warren Krewenki
 * */
class DocRaptor {

  public $api_key;
  public $document_content;
  public $document_type;
  public $name;
  public $test;

  public function __construct($api_key = null) {
    if (!is_null($api_key)) {
      $this->api_key = $api_key;
    }
    $this->test = false;
    $this->setDocumentType('pdf');
    return true;
    ini_set("memory_limit", "128M");
  }

  public function setAPIKey($api_key = null) {
    if (!is_null($api_key)) {
      $this->api_key = $api_key;
    }
    return true;
  }

  public function setDocumentContent($document_content = null) {
    $this->document_content = $document_content;
    return true;
  }

  public function setDocumentType($document_type) {
    $document_type = strtolower($document_type);
    $this->type = $document_type == 'pdf' || $document_type == 'xls' ? $document_type : 'pdf';
    return true;
  }

  public function setName($name) {
    $this->name = $name;
    return true;
  }

  public function setTest($test = false) {
    $this->test = (bool) $test;
    return true;
  }

  private function createTempFile(){
    $this->tempfilename = TEMP_PATH.'_'.md5($this->name).'.tmp';
    $this->tempfilelocation  = BASE_URL . TEMP_URL . '_'. md5($this->name).'.tmp';
    $this->tempfile = fopen($this->tempfilename, 'w+');
    fwrite($this->tempfile, $this->document_content);
  }
  private function closeTempFile(){
    fclose($this->tempfile);
    unlink($this->tempfilename);
  }
  
  public function fetchDocument($filename = false) {
    $this->createTempFile();
    
    if ($this->api_key != '') {
      $url = "https://docraptor.com/docs?user_credentials=" . $this->api_key;
      $fields = array(
      //'doc[document_content]' => urlencode($this->document_content),
      'doc[document_url]' => $this->tempfilelocation,
      'doc[document_type]' => $this->type,
      'doc[name]' => $this->name,
      'doc[test]' => $this->test,
      'doc[prince_options][disallow_copy]' => true,
      'doc[prince_options][disallow_modify]' => true,
      'doc[prince_options][key_bits]' => 128,
      'doc[prince_options][encrypt]' => true,
      'doc[prince_options][version]' => '8.1'
      );
      $fields_string = "";
      foreach ($fields as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
      }
      rtrim($fields_string, '&');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, count($fields));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
      curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem");
      $result = curl_exec($ch);
        
      //close connection
      curl_close($ch);

      //remove temp file
      $this->closeTempFile();
      
      return $result;
    }
  }
}
?>