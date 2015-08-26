<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

require_once('implementation/constants.php');

function db_connect_options() {
  // connect to database
  global $database_path;
  try {
    $db = new SQLite3($database_path . 'options-database.sqlite');
  } catch (Exception $e) {
    return false;
  }
  // create table if it doesn't already exist
  $query = "CREATE TABLE IF NOT EXISTS options(
    lastUpdated INTEGER,
    timeout INTEGER,
    finalised BOOLEAN,
    id INTEGER PRIMARY KEY AUTOINCREMENT
    );";
  if ($db->exec($query) === false) {
    return false;
  }
  // return database handle
  return $db;
}

function get_options() {
  // connect to database
  if (($db = db_connect_options()) === false) {
    return format_error("camera.getOptions", "failure",
      "unable to connect to options database");
  }
  // generate options array
  $options = array();
  $options["exposureProgram"] = 1;
  $options["iso"] = 400;
  $options["shutterSpeed"] = 0.001;
  $options["shutterSpeedSupport"] = array(
    0.000125, 0.00025, 0.0005, 0.001, 0.002, 0.004, 0.008, 0.016666,
    0.033333, 0.066666, 0.125, 0.25, 0.5, 1, 2, 4, 8, 15, 30, 60, 90, 120
  );
  $options["whiteBalance"] = "auto";
  $options["fileFormat"] = array(
    "type" => "jpeg",
    "width" => 10000,
    "height" => 5000
  );
  $options["exposureDelay"] = 5;
  $options["sleepDelay"] = 60;
  $options["offDelay"] = 65535;
  $options["totalSpace"] = 68719476736;
  $options["remainingSpace"] = 123456789;
  $options["remainingPictures"] = 314;
  $options["gpsInfo"] = array(
    "lat" => 55.9238,
    "lng" => -3.2094
  );
  $options["dateTimeZone"] = date("Y:m:d G:i:s+0:00");
  $options["hdr"] = true;
  $options["hdrSupport"] = true;
  $options["exposureBracket"] = array(
    "autoMode" => true
  );
  $options["exposureBracketSupport"] = array(
    "autoMode" => true,
    "shotsSupport" => [3],
    "incrementSupport" => [1]
  );
  $options["gyro"] = true;
  $options["gps"] = true;
  $options["gpsSupport"] = true;
  $options["_audioEnabled"] = true;
  $options["_customWhiteBalance"] = array(
    "red" => 100,
    "green" => 90,
    "blue" => 80
  );
  $options["_autoExposureMode"] = "indoors";
  $options["_autoExposureModeSupport"] = array(
    "outdoors", "indoors", "front_outdoors", "front_indoors"
  );
  $options["_lightType"] = "fluorescent_EU";
  $options["_lightTypeSupport"] = array(
    "normal", "fluorescent_EU", "fluorescent_US"
  );
  return $options;
}

function set_options($options) {
  return true;
}

?>