<?php

// Warning: make sure there is nothing (even white space) before the opening
// php tag as this will cause http headers to be sent, along with the white
// space, before PHP starts executing.

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

// ensure result is sent as json
header('Content-Type: application/json; charset=utf-8');
header('X-XSRF-Protected: 1');
header('X-Content-Type-Options: nosniff');

require_once('includes/capture-commands.php');
require_once('includes/image-commands.php');
require_once('includes/metadata-commands.php');
require_once('includes/options-commands.php');
require_once('includes/session-commands.php');
require_once('includes/output-functions.php');
require_once('includes/session-functions.php');

// get the incoming request
$input_json = file_get_contents('php://input');

// decode it
$input = json_decode($input_json, true);
if ($input === null)
{
  // decoding failed, must be malformed input, so ignore
  echo format_error("camera.commands.execute", "clientError",
    "malformed input, could not parse JSON");
  exit;
}

function ensure_no_invalid_parameters($acceptable, $input, $caller) {
  $newArray = $input;
  foreach ($acceptable as $item) {
    unset($newArray[$item]);
  }
  if (!empty($newArray)) {
    reset($newArray);
    $name = key($newArray);
    echo format_error($caller, "invalidParameterName",
      "parameter " . $name . " is not valid here");
    exit;
  }
}

// check for invalid input
//ensure_no_invalid_parameters(array("name", "parameters"), $input,
//  "camera.commands.execute");

// start the appropriate action and output its result
$name = $input['name'];
$parameters = $input['parameters'];
switch($name) {

case "camera.startSession":
  if (!isset($parameters["timeout"])) {
    $parameters["timeout"] = 300;
  }
  ensure_no_invalid_parameters(array(
      "timeout"
    ), $parameters, $name);
  echo(command_start_session($parameters["timeout"]));
  break;

case "camera.updateSession":
  if (!isset($parameters["sessionId"])) {
    echo format_error("camera.updateSession", "missingParameter",
      "Required parameter sessionId not specified");
    exit;
  }
  if (!isset($parameters["timeout"])) {
    $parameters["timeout"] = 300;
  }
  ensure_no_invalid_parameters(array(
      "sessionId", "timeout"
    ), $parameters, $name);
  echo(command_update_session($parameters['sessionId'],
    $parameters['timeout']));
  break;

case "camera.closeSession":
  if (!isset($parameters["sessionId"])) {
    echo format_error("camera.closeSession", "missingParameter",
      "Required parameter sessionId not specified");
    exit;
  }
  ensure_no_invalid_parameters(array(
      "sessionId"
    ), $parameters, $name);
  echo(command_close_session($parameters["sessionId"]));
  break;

case "camera.takePicture":
  if (!isset($parameters["sessionId"])) {
    echo format_error("camera.takePicture", "missingParameter",
      "Required parameter sessionId not specified");
    exit;
  }
  ensure_no_invalid_parameters(array(
      "sessionId"
    ), $parameters, $name);
  keepalive_sessions();
  echo(command_take_picture($parameters["sessionId"]));
  break;

case "camera.listFiles":
  if (!isset($parameters["fileType"])) {
    echo format_error("camera.listFiles", "missingParameter",
      "Required parameter fileType not specified");
    exit;
  }
  if (!isset($parameters["entryCount"])) {
    echo format_error("camera.listFiles", "missingParameter",
      "Required parameter entryCount not specified");
    exit;
  } 
  if (!isset($parameters["startPosition"])) {
    $parameters["startPosition"] = 0;
  }
  if (!isset($parameters["maxThumbSize"])) {
    echo format_error("camera.listFiles", "missingParameter",
      "Required parameter maxThumbSize not specified");
    exit;
  }
  ensure_no_invalid_parameters(array(
      "entryCount", "maxThumbSize", "fileType", "startPosition"
    ), $parameters, $name);
  keepalive_sessions();
  echo(command_list_files($parameters["entryCount"], $parameters["maxThumbSize"],
    $parameters["fileType"], $parameters["startPosition"]));
  break;

case "camera.listImages":
  if (!isset($parameters["entryCount"])) {
    echo format_error("camera.listImages", "missingParameter",
      "Required parameter entryCount not specified");
    exit;
  }
  if (!isset($parameters["includeThumb"])) {
    $parameters["includeThumb"] = true;
  }
  if ($parameters["includeThumb"] === true && !isset($parameters["maxSize"])) {
    echo format_error("camera.listImages", "missingParameter",
      "Required parameter maxSize not specified");
    exit;
  }
  if (!isset($parameters["continuationToken"])) {
    $parameters["continuationToken"] = "";
  }
  ensure_no_invalid_parameters(array(
      "entryCount", "maxSize", "continuationToken", "includeThumb"
    ), $parameters, $name);
  keepalive_sessions();
  echo(command_list_images($parameters["entryCount"], $parameters["maxSize"],
    $parameters["continuationToken"], $parameters["includeThumb"]));
  break;

case "camera.delete":
  if (!isset($parameters["fileUri"])) {
    echo format_error("camera.delete", "missingParameter",
      "Required parameter fileUri not specified");
    exit;
  }
  ensure_no_invalid_parameters(array(
      "fileUri"
    ), $parameters, $name);
  keepalive_sessions();
  echo(command_delete($parameters["fileUri"]));
  break;

case "camera.getImage":
  if (!isset($parameters["fileUri"])) {
    echo format_error("camera.getImage", "missingParameter",
      "Required parameter fileUri not specified");
    exit;
  }
  if (!isset($parameters["maxSize"])) {
    $parameters["maxSize"] = 10000;
  }
  ensure_no_invalid_parameters(array(
      "fileUri", "maxSize"
    ), $parameters, $name);
  keepalive_sessions();
  echo(command_get_image($parameters["fileUri"], $parameters["maxSize"]));
  break;

case "camera.getMetadata":
  if (!isset($parameters["fileUri"])) {
    echo format_error("camera.getMetadata", "missingParameter",
      "Required parameter fileUri not specified");
    exit;
  }
  ensure_no_invalid_parameters(array(
      "fileUri"
    ), $parameters, $name);
  keepalive_sessions();
  echo(command_get_metadata($parameters["fileUri"]));
  break;

case "camera.getOptions":
  if (!isset($parameters["sessionId"])) {
    echo format_error("camera.getOptions", "missingParameter",
      "Required parameter sessionId not specified");
    exit;
  }
  if (!isset($parameters["optionNames"])) {
    echo format_error("camera.getOptions", "missingParameter",
      "Required parameter optionNames not specified");
    exit;
  }
  ensure_no_invalid_parameters(array(
      "sessionId", "optionNames"
    ), $parameters, $name);
  keepalive_sessions();
  echo(command_get_options($parameters["sessionId"],
    $parameters["optionNames"]));
  break;

case "camera.setOptions":
  if (!isset($parameters["sessionId"])) {
    echo format_error("camera.setOptions", "missingParameter",
      "Required parameter sessionId not specified");
    exit;
  }
  if (!isset($parameters["options"])) {
    echo format_error("camera.setOptions", "missingParameter",
      "Required parameter options not specified");
    exit;
  }
  ensure_no_invalid_parameters(array(
      "sessionId", "options"
    ), $parameters, $name);
  keepalive_sessions();
  echo(command_set_options($parameters["sessionId"], $parameters["options"]));
  break;

default:
  echo format_error("camera.commands.execute", "unknownCommand",
    "unrecognised command");
  break;
}

?>