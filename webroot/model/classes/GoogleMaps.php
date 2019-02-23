<?php
/*
  ChurchMembers is a php/ajax webbased crm application targeting churches.
  Through it they can administer members, addresses and groups.
  Copyright (C) 2011  goblin47 & thelionnl

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

set_time_limit(60);

/**
 * This class handles GoogleMaps
 */
class GoogleMaps {

  /**
   * get geocode lat/lon points for given address
   * @param string $address
   * @param string $zipcity
   * @param int $trycount
   * @return bool|array false if can't be geocoded, array or geocdoes if successful
   */
  public function geoGetCoords($address,$zipcity, $trycount = 1) {
    $searchlocation = ($address !== '') ? $address . ", " . $zipcity : $zipcity ;
    $_url = sprintf('https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=%s', rawurlencode($searchlocation));
    $_result = false;
    if($_result = file_get_contents($_url)) {
      $_result_parts = json_decode($_result);

      switch ($_result_parts->status) {

        case "OVER_QUERY_LIMIT":
          if ($trycount <= 10) {
            $trycount++;
            usleep(200000);// wait .2 sec
            $this->geoGetCoords($address,$zipcity, $trycount);
          } else {
            $_coords['lat'] = number_format(0, 7, '.', '');
            $_coords['lon'] = number_format(0, 7, '.', '');
            return $_coords;
          }
          break;

        case "ZERO_RESULTS":
          if ($trycount <= 20) {
            $trycount++;
            $this->geoGetCoords('',$zipcity,$trycount); // COORDS ARE APPROXIMATE, TRY TO FIND WITH ONLY ZIP + CITY
          } else {
            $_coords['lat'] = number_format(0, 7, '.', '');
            $_coords['lon'] = number_format(0, 7, '.', '');
            return $_coords;
          }
          break;

        case "OK":
          if (($_result_parts->results[0]->geometry->location_type === 'APPROXIMATE') && ($address !== '')) {
            $this->geoGetCoords('',$zipcity,$trycount); // COORDS ARE APPROXIMATE, TRY TO FIND WITH ONLY ZIP + CITY
          } else if (($_result_parts->results[0]->geometry->location_type === 'APPROXIMATE') && ($address === '')) {
            // COORDS ARE STILL APPROXIMATE
          }

          $_coords['lat'] = $_result_parts->results[0]->geometry->location->lat;
          $_coords['lon'] = $_result_parts->results[0]->geometry->location->lng;

          $_coords['lat'] = number_format($_coords['lat'], 7, '.', '');
          $_coords['lon'] = number_format($_coords['lon'], 7, '.', '');
          return $_coords;

          break;

        default:
          $_coords['lat'] = number_format(0, 7, '.', '');
          $_coords['lon'] = number_format(0, 7, '.', '');
          return $_coords;
          break;
      }
    }
  }
}
?>