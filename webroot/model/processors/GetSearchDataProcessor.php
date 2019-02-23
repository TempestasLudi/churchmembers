<?php

/**
 * This processor handles a request for generating the advanced search results
 */
class GetSearchDataProcessor extends AbstractProcessor {

  public function processRequest() {

    $contentPlaceholders = "";
    $SEARCHtemplate = new TemplateParser("SEARCH", $contentPlaceholders, $this->database);
    print_r($SEARCHtemplate->parseOutput());
  }

}

?>