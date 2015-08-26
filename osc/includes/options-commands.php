<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

require_once('implementation/options-functions.php');
require_once('includes/output-functions.php');
require_once('includes/session-functions.php');

function command_get_options($session, $requestedOptions) {
  // check session id is valid
  validate_session($session, "camera.getOptions");
  // convert requested options values to keys for easier removal
  // (php oddity - unset much easier with keys than values)
  if (!is_array($requestedOptions)) {
    $requestedOptions = array();
  }
  $requestedOptions = array_flip($requestedOptions);
  // get current options
  $options = get_options();
  if ($options === false) {
    return format_error("camera.getOptions", "serverError",
      "unable to get camera options");
  }
  // generate results
  $results = array();
  if (array_key_exists("captureMode", $requestedOptions)) {
    $results["captureMode"] = "image";
    unset($requestedOptions["captureMode"]);
  }
  if (array_key_exists("captureModeSupport", $requestedOptions)) {
    $results["captureModeSupport"] = array("image");
    unset($requestedOptions["captureModeSupport"]);
  }
  if (array_key_exists("exposureProgram", $requestedOptions)) {
    $results["exposureProgram"] = $options["exposureProgram"];
    unset($requestedOptions["exposureProgram"]);
  }
  if (array_key_exists("exposureProgramSupport", $requestedOptions)) {
    $results["exposureProgramSupport"] = array(1, 2);
    unset($requestedOptions["exposureProgramSupport"]);
  }
  if (array_key_exists("iso", $requestedOptions)) {
    $results["iso"] = $options["iso"];
    unset($requestedOptions["iso"]);
  }
  if (array_key_exists("isoSupport", $requestedOptions)) {
    $results["isoSupport"] = array(
      100, 200, 400, 800, 1600, 3200, 6400, 12800, 25600
    );
    unset($requestedOptions["isoSupport"]);
  }
  if (array_key_exists("shutterSpeed", $requestedOptions)) {
    $results["shutterSpeed"] = $options["shutterSpeed"];
    unset($requestedOptions["shutterSpeed"]);
  }
  if (array_key_exists("shutterSpeedSupport", $requestedOptions)) {
    $results["shutterSpeedSupport"] = $options["shutterSpeedSupport"];
    unset($requestedOptions["shutterSpeedSupport"]);
  }
  if (array_key_exists("aperture", $requestedOptions)) {
    $results["aperture"] = 2.67;
    unset($requestedOptions["aperture"]);
  }
  if (array_key_exists("apertureSupport", $requestedOptions)) {
    $results["apertureSupport"] = array(2.67);
    unset($requestedOptions["apertureSupport"]);
  }
  if (array_key_exists("whiteBalance", $requestedOptions)) {
    $results["whiteBalance"] = $options["whiteBalance"];
    unset($requestedOptions["whiteBalance"]);
  }
  if (array_key_exists("whiteBalanceSupport", $requestedOptions)) {
    $results["whiteBalanceSupport"] = array(
      "auto", "daylight", "shade", "cloudy-daylight", "incandescent",
      "fluorescent", "_custom"
    );
    unset($requestedOptions["whiteBalanceSupport"]);
  }
  if (array_key_exists("exposureCompensation", $requestedOptions)) {
    $results["exposureCompensation"] = 0;
    unset($requestedOptions["exposureCompensation"]);
  }
  if (array_key_exists("exposureCompensationSupport", $requestedOptions)) {
    $results["exposureCompensationSupport"] = array(0);
    unset($requestedOptions["exposureCompensationSupport"]);
  }
  if (array_key_exists("fileFormat", $requestedOptions)) {
    $results["fileFormat"] = $options["fileFormat"];
    unset($requestedOptions["fileFormat"]);
  }
  if (array_key_exists("fileFormatSupport", $requestedOptions)) {
    $results["fileFormatSupport"] = array(  
      array(
        "type" => "_jpegNctri",
        "width" => 8000,
        "height" => 4000
      ),
      array(
        "type" => "_jpegNctri",
        "width" => 5600,
        "height" => 2800
      ),
      array(
        "type" => "jpeg",
        "width" => 8000,
        "height" => 4000
      ),
      array(
        "type" => "jpeg",
        "width" => 5600,
        "height" => 2800
      )
    );
    unset($requestedOptions["fileFormatSupport"]);
  }
  if (array_key_exists("exposureDelay", $requestedOptions)) {
    $results["exposureDelay"] = $options["exposureDelay"];
    unset($requestedOptions["exposureDelay"]);
  }
  if (array_key_exists("exposureDelaySupport", $requestedOptions)) {
    $results["exposureDelaySupport"] = array(0, 5, 10, 20, 30, 60);
    unset($requestedOptions["exposureDelaySupport"]);
  }
  if (array_key_exists("sleepDelay", $requestedOptions)) {
    $results["sleepDelay"] = $options["sleepDelay"];
    unset($requestedOptions["sleepDelay"]);
  }
  if (array_key_exists("sleepDelaySupport", $requestedOptions)) {
    $results["sleepDelaySupport"] = array(5, 15, 30, 60, 300, 600, 65535);
    unset($requestedOptions["sleepDelaySupport"]);
  }
  if (array_key_exists("offDelay", $requestedOptions)) {
    $results["offDelay"] = $options["offDelay"];
    unset($requestedOptions["offDelay"]);
  }
  if (array_key_exists("offDelaySupport", $requestedOptions)) {
    $results["offDelaySupport"] = array(300, 600, 1800, 3600, 65535);
    unset($requestedOptions["offDelaySupport"]);
  }
  if (array_key_exists("totalSpace", $requestedOptions)) {
    $results["totalSpace"] = $options["totalSpace"];
    unset($requestedOptions["totalSpace"]);
  }
  if (array_key_exists("remainingSpace", $requestedOptions)) {
    $results["remainingSpace"] = $options["remainingSpace"];
    unset($requestedOptions["remainingSpace"]);
  }
  if (array_key_exists("remainingPictures", $requestedOptions)) {
    $results["remainingPictures"] = $options["remainingPictures"];
    unset($requestedOptions["remainingPictures"]);
  }
  if (array_key_exists("gpsInfo", $requestedOptions)) {
    $results["gpsInfo"] = $options["gpsInfo"];
    unset($requestedOptions["gpsInfo"]);
  }
  if (array_key_exists("dateTimeZone", $requestedOptions)) {
    $results["dateTimeZone"] = $options["dateTimeZone"];
    unset($requestedOptions["dateTimeZone"]);
  }
  if (array_key_exists("hdr", $requestedOptions)) {
    $results["hdr"] = $options["hdr"];
    unset($requestedOptions["hdr"]);
  }
  if (array_key_exists("hdrSupport", $requestedOptions)) {
    $results["hdrSupport"] = $options["hdrSupport"];
    unset($requestedOptions["hdrSupport"]);
  }
  if (array_key_exists("exposureBracket", $requestedOptions)) {
    $results["exposureBracket"] = $options["exposureBracket"];
    unset($requestedOptions["exposureBracket"]);
  }
  if (array_key_exists("exposureBracketSupport", $requestedOptions)) {
    $results["exposureBracketSupport"] = $options["exposureBracketSupport"];
    unset($requestedOptions["exposureBracketSupport"]);
  }
  if (array_key_exists("gyro", $requestedOptions)) {
    $results["gyro"] = $options["gyro"];
    unset($requestedOptions["gyro"]);
  }
  if (array_key_exists("gyroSupport", $requestedOptions)) {
    $results["gyroSupport"] = true;
    unset($requestedOptions["gyroSupport"]);
  }
  if (array_key_exists("gps", $requestedOptions)) {
    $results["gps"] = $options["gps"];
    unset($requestedOptions["gps"]);
  }
  if (array_key_exists("gpsSupport", $requestedOptions)) {
    $results["gpsSupport"] = $options["gpsSupport"];
    unset($requestedOptions["gpsSupport"]);
  }
  if (array_key_exists("imageStabilization", $requestedOptions)) {
    $results["imageStabilization"] = "off";
    unset($requestedOptions["imageStabilization"]);
  }

  if (array_key_exists("imageStabilizationSupport", $requestedOptions)) {
    $results["imageStabilizationSupport"] = array("off");
    unset($requestedOptions["imageStabilizationSupport"]);
  }
  if (array_key_exists("_audioEnabled", $requestedOptions)) {
    $results["_audioEnabled"] = $options["_audioEnabled"];
    unset($requestedOptions["_audioEnabled"]);
  }
  if (array_key_exists("_showPreview", $requestedOptions)) {
    $results["_showPreview"] = $options["_showPreview"];
    unset($requestedOptions["_showPreview"]);
  }
  if (array_key_exists("_customWhiteBalance", $requestedOptions)) {
    $results["_customWhiteBalance"] = $options["_customWhiteBalance"];
    unset($requestedOptions["_customWhiteBalance"]);
  }
  if (array_key_exists("_autoExposureMode", $requestedOptions)) {
    $results["_autoExposureMode"] = $options["_autoExposureMode"];
    unset($requestedOptions["_autoExposureMode"]);
  }
  if (array_key_exists("_autoExposureModeSupport", $requestedOptions)) {
    $results["_autoExposureModeSupport"] = $options["_autoExposureModeSupport"];
    unset($requestedOptions["_autoExposureModeSupport"]);
  }
  if (array_key_exists("_lightType", $requestedOptions)) {
    $results["_lightType"] = $options["_lightType"];
    unset($requestedOptions["_lightType"]);
  }
  if (array_key_exists("_lightTypeSupport", $requestedOptions)) {
    $results["_lightTypeSupport"] = $options["_lightTypeSupport"];
    unset($requestedOptions["_lightTypeSupport"]);
  }
  // check no non-existent options requested
  $requestedOptions = array_flip($requestedOptions);
  if (!empty($requestedOptions)) {
    reset($requestedOptions);
    $name = current($requestedOptions);
    return format_error("camera.getOptions", "invalidParameterName",
      "option name " . $name . " does not exist");
  }
  // return results
  return format_results("camera.getOptions", array("options" => $results));
}

function inList($needle, $haystack) {
  foreach ($haystack as $hayblade) {
    if ($hayblade === $needle) {
      return true;
    }
  }
  return false;
}

function command_set_options($session, $optionsToSet) {
  // check session id is valid
  validate_session($session, "camera.setOptions");
  // get current options
  $options = get_options();
  // start checking parameters
  if (isset($optionsToSet['wifiPassword'])) {
    if (mb_strlen($optionsToSet['wifiPassword']) >= 8 && 
    mb_strlen($optionsToSet['wifiPassword']) <= 63 && 
      mb_strpos($optionsToSet['wifiPassword'], " ") === false) {
var_dump($optionsToSet['wifiPassword']);
      $options['wifiPassword'] = $optionsToSet['wifiPassword'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "wifi password too short or too long or contains invalid characters");
    }
    unset($optionsToSet['wifiPassword']);
  }
  if (isset($optionsToSet['exposureProgram'])) {
    if (inList($optionsToSet['exposureProgram'], array(1, 2))) {
      $options['exposureProgram'] = $optionsToSet['exposureProgram'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - exposureProgram");
    }
    unset($optionsToSet['exposureProgram']);
  }
  if (isset($optionsToSet['iso'])) {
    if (is_integer($optionsToSet['iso']) && 
        inList($optionsToSet['iso'], array(
          100, 200, 400, 800, 1600, 3200, 6400
        ))) {
      // set option on camera
      $options['iso'] = $optionsToSet['iso'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - iso");
    }
    unset($optionsToSet['iso']);
  }
  if (isset($optionsToSet['shutterSpeed'])) {
    if (is_numeric($optionsToSet['shutterSpeed']) && 
        inList($optionsToSet['shutterSpeed'], array(
          0.001, 0.002, 0.004, 0.008, 0.016666,
          0.033333, 0.066666, 0.125, 0.25, 0.5, 1, 2
        ))) {
      // set option on camera
      $options['shutterSpeed'] = $optionsToSet['shutterSpeed'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - shutterSpeed");
    }
    unset($optionsToSet['shutterSpeed']);
  }
  if (isset($optionsToSet['whiteBalance'])) {
    if (is_string($optionsToSet['whiteBalance']) && 
        inList($optionsToSet['whiteBalance'], array(
          "auto", "daylight", "shade", "cloudy-daylight", "incandescent",
          "fluorescent", "_custom"
        ))) {
      // set option on camera
      $options['whiteBalance'] = $optionsToSet['whiteBalance'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - whiteBalance");
    }
    unset($optionsToSet['whiteBalance']);
  }
  if (isset($optionsToSet['fileFormat'])) {
    if (is_array($optionsToSet['fileFormat']) &&
        isset($optionsToSet['fileFormat']['type']) &&
        inList($optionsToSet['fileFormat']['type'], array("jpeg", "_jpegNctri")) &&
        isset($optionsToSet['fileFormat']['width']) &&
        isset($optionsToSet['fileFormat']['height']) &&
	inList(array($optionsToSet['fileFormat']['width'],$optionsToSet['fileFormat']['height']), array(array(8000,4000), array(6000,3000), array(5600,2800), array(5000,2500), array(4000,2000), array(2000,1000)))) {
      // set option on camera
      $options['fileFormat'] = $optionsToSet['fileFormat'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - fileFormat");
    }
    unset($optionsToSet['fileFormat']);
  }
  if (isset($optionsToSet['exposureDelay'])) {
    if (is_integer($optionsToSet['exposureDelay']) && 
        inList($optionsToSet['exposureDelay'], array(0, 5, 10, 20, 30, 60))) {
      // set option on camera
      $options['exposureDelay'] = $optionsToSet['exposureDelay'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - exposureDelay");
    }
    unset($optionsToSet['exposureDelay']);
  }
  if (isset($optionsToSet['sleepDelay'])) {
    if (is_integer($optionsToSet['sleepDelay']) && 
        inList($optionsToSet['sleepDelay'], array(
          5, 15, 30, 60, 300, 600, 65535
        ))) {
      // set option on camera
      $options['sleepDelay'] = $optionsToSet['sleepDelay'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - sleepDelay");
    }
    unset($optionsToSet['sleepDelay']);
  }
  if (isset($optionsToSet['offDelay'])) {
    if (is_integer($optionsToSet['offDelay']) && 
        inList($optionsToSet['offDelay'], array(300, 600, 1800, 3600, 65535))) {
      // set option on camera
      $options['offDelay'] = $optionsToSet['offDelay'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - offDelay");
    }
    unset($optionsToSet['offDelay']);
  }
  if (isset($optionsToSet['gpsInfo'])) {
    if (is_array($optionsToSet['gpsInfo']) &&
        isset($optionsToSet['gpsInfo']['lat']) &&
        is_numeric($optionsToSet['gpsInfo']['lat']) &&
        (($optionsToSet['gpsInfo']['lat'] >= -180 &&
        $optionsToSet['gpsInfo']['lat'] <= 180) || 
        $optionsToSet['gpsInfo']['lat'] == 65535) &&
        isset($optionsToSet['gpsInfo']['lng']) &&
        is_numeric($optionsToSet['gpsInfo']['lng']) &&
        (($optionsToSet['gpsInfo']['lng'] >= -180 &&
        $optionsToSet['gpsInfo']['lng'] <= 180) || 
        $optionsToSet['gpsInfo']['lng'] == 65535)) {
      // set option on camera
      $options['gpsInfo'] = $optionsToSet['gpsInfo'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - gpsInfo");
    }
    unset($optionsToSet['gpsInfo']);
  }
  if (isset($optionsToSet['dateTimeZone'])) {
    if (is_string($optionsToSet['dateTimeZone']) &&
      date_create_from_format(
        "Y:m:d G:i:sP", $optionsToSet['dateTimeZone']
      ) !== false) {
      // set option on camera
      $options['dateTimeZone'] = $optionsToSet['dateTimeZone'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - dateTimeZone");
    }
    unset($optionsToSet['dateTimeZone']);
  }
  if (isset($optionsToSet['hdr'])) {
    if (is_bool($optionsToSet['hdr'])) {
      // set option on camera
      $options['hdr'] = $optionsToSet['hdr'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - hdr");
    }
    unset($optionsToSet['hdr']);
  }
  if (isset($optionsToSet['exposureBracket'])) {
    if (is_array($optionsToSet['exposureBracket']) &&
        (isset($optionsToSet['exposureBracket']['shots']) &&
          ($optionsToSet['exposureBracket']['shots'] === 1 ||
            $optionsToSet['exposureBracket']['shots'] === 5) &&
          isset($optionsToSet['exposureBracket']['increment']) &&
          $optionsToSet['exposureBracket']['increment'] === 1) ||
        (isset($optionsToSet['exposureBracket']['autoMode']) &&
          $optionsToSet['exposureBracket']['autoMode'] === true)) {
      // set option on camera
      $options['exposureBracket'] = $optionsToSet['exposureBracket'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - exposureBracket");
    }
    unset($optionsToSet['exposureBracket']);
  }
  if (isset($optionsToSet['gyro'])) {
    if (is_bool($optionsToSet['gyro'])) {
      // set option on camera
      $options['gyro'] = $optionsToSet['gyro'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - gyro");
    }
    unset($optionsToSet['gyro']);
  }
  if (isset($optionsToSet['gps'])) {
    if (is_bool($optionsToSet['gps'])) {
      // set option on camera
      $options['gps'] = $optionsToSet['gps'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - gps");
    }
    unset($optionsToSet['gps']);
  }
  if (isset($optionsToSet['_lightType'])) {
    if (is_string($optionsToSet['_lightType']) && 
        inList($optionsToSet['_lightType'], array(
          "Normal", "Fluorescent EU", "Fluorescent US"
        ))) {
      // set option on camera
      $options['_lightType'] = $optionsToSet['_lightType'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - _lightType");
    }
    unset($optionsToSet['_lightType']);
  }
  if (isset($optionsToSet['_audioEnabled'])) {
    if (is_bool($optionsToSet['_audioEnabled'])) {
      // set option on camera
      $options['_audioEnabled'] = $optionsToSet['_audioEnabled'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - _audioEnabled");
    }
    unset($optionsToSet['_audioEnabled']);
  }
  if (isset($optionsToSet['_showPreview'])) {
    if (is_bool($optionsToSet['_showPreview'])) {
      // set option on camera
      $options['_showPreview'] = $optionsToSet['_showPreview'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - _showPreview");
    }
    unset($optionsToSet['_showPreview']);
  }
  if (isset($optionsToSet['_customWhiteBalance'])) {
    if (is_array($optionsToSet['_customWhiteBalance']) &&
        isset($optionsToSet['_customWhiteBalance']['red']) &&
        in_numeric($optionsToSet['_customWhiteBalance']['red']) &&
        $optionsToSet['_customWhiteBalance']['red'] >= 0 &&
        $optionsToSet['_customWhiteBalance']['red'] <= 100 &&
        isset($optionsToSet['_customWhiteBalance']['green']) &&
        in_numeric($optionsToSet['_customWhiteBalance']['green']) &&
        $optionsToSet['_customWhiteBalance']['green'] >= 0 &&
        $optionsToSet['_customWhiteBalance']['green'] <= 100 &&
        isset($optionsToSet['_customWhiteBalance']['blue']) &&
        in_numeric($optionsToSet['_customWhiteBalance']['blue']) &&
        $optionsToSet['_customWhiteBalance']['blue'] >= 0 &&
        $optionsToSet['_customWhiteBalance']['blue'] <= 100) {
      // set option on camera
      $options['_customWhiteBalance'] = $optionsToSet['_customWhiteBalance'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - _customWhiteBalance");
    }
    unset($optionsToSet['_customWhiteBalance']);
  }
  if (isset($optionsToSet['_autoExposureModeType'])) {
    if (is_string($optionsToSet['_autoExposureModeType']) && 
        inList($optionsToSet['_autoExposureModeType'], array(
          "Outdoors", "Indoors", "Front Outdoors", "Front Indoors"
        ))) {
      // set option on camera
      $options['_autoExposureModeType'] =
        $optionsToSet['_autoExposureModeType'];
    } else {
      return format_error("camera.setOptions", "invalidParameterValue",
        "bad or malformed input value - _autoExposureModeType");
    }
    unset($optionsToSet['_autoExposureModeType']);
  }
  // check no non-existent parameters set
  if (!empty($optionsToSet)) {
    reset($optionsToSet);
    $name = key($optionsToSet);
    return format_error("camera.setOptions", "invalidParameterName",
      "option name " . $name . " does not exist");
  }
  // try to set options
  if (!set_options($options)) {
    return format_error("camera.setOptions", "serverError",
      "unable to set options on camera");
  }
  // return results
  return format_results("camera.setOptions", array());
}

?>