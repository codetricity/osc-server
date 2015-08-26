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

require_once('includes/output-functions.php');
require_once('includes/progress-functions.php');
require_once('includes/session-functions.php');

// get the incoming request
$input_json = file_get_contents('php://input');

// decode it
$input = json_decode($input_json, true);
if ($input === null)
{
  // decoding failed, must be malformed input, so ignore
  echo format_error("camera.commands.status", "clientError",
    "malformed input, could not parse JSON");
  exit;
}

if (!isset($input['id'])) {
  echo format_error("camera.commands.status", "clientError",
    "required input field id not given");
  exit;
}
$id = $input['id'];

// check for invalid input
unset($input['id']);
if (!empty($input)) {
  reset($input);
  $name = key($input);
  echo format_error("camera.commands.status", "invalidParameterName",
    "parameter " . $name . " does not exist");
  exit;
}
// get progress
$progress = check_picture_progress($id);
if ($progress === false) {
  echo format_error("camera.commands.status", "invalidParameterValue",
    "command id " . $id . " not recognised");
  exit;
}
keepalive_sessions();
// return results
if ($progress['done'] === "error") {
  // capture failed
  echo format_error("camera.takePicture", "serverError",
    "failed to capture image");
} else if ($progress['done'] === true) {
  // capture finished
  $results = array(
    "fileUri" => $progress['uri']
  );
  echo format_results("camera.takePicture", $results);
} else {
  // capture still in progress
  echo format_in_progress("camera.takePicture", $id,
    $progress['percentComplete']);
}

?>