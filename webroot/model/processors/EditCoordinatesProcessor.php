<?php

/**
 * This processor handles a request for updating the coordinates for an address.
 */
class EditCoordinatesProcessor extends AbstractProcessor {

  public function processRequest() {

    ///////////////////////// GET CURRENT ADDRESS INFO /////////////////////////
    $address = $_SESSION['CURRENT-VIEW']['CURRENT_ADDRESS'];

    ///////////////////////// GET COORDINATES /////////////////////////
    $coordinates = $this->database->getLatLon($address->ADR_street, $address->ADR_number, $address->ADR_zip, $address->ADR_city);

    ///////////////////////// UPDATE ADDRESS COORDINATES /////////////////////////
    $result = $this->database->editDataNoVerify($address->ADR_id, 'ADR_id', 'addresses', 'ADR_lat', $coordinates['lat']);
    $result = $this->database->editDataNoVerify($address->ADR_id, 'ADR_id', 'addresses', 'ADR_lng', $coordinates['lon']);
  }

}

?>