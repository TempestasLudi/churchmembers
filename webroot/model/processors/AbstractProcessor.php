<?php

/**
 * The AbstractProcessor handles some basic features as securitye etc.
 */
abstract class AbstractProcessor extends ProcessRequest {

  /**
   * Represents the database.
   */
  protected $database;

  /**
   * Initializes a new Processor, automatically called when constructing a subtype.
   */
  public function __construct() {

  }

  /**
   * Handles the request and prints the output.
   * All subprocessors should implement this function.
   */
  public abstract function processRequest();

  /**
   * Returns 'fam', 'mr', 'ms' for an address
   */
  public function familynameStart($address) {
    $gender = ($address->MEMBER_gender == 'male') ? __("Mr") : __("Ms");
    $start = ($address->_COUNT > 1) ? __("Fam.") : $gender;
    $start = ($address->_COUNT === 0) ? '' : $start;
    return $start;
  }

}

?>