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

// includes
require_once('implementation/state-functions.php');
require_once('includes/session-functions.php');

// increase maximum execution time
set_time_limit(300);

// get the incoming request
$input_json = file_get_contents('php://input');
$input = json_decode($input_json, true);
if ($input === null)
{
  // decoding failed, must be malformed input, so ignore
  echo format_error("camera.checkForUpdates", "parseError",
    "malformed input, could not parse JSON");
  exit;
}

if (!isset($input['stateFingerprint'])) {
  echo format_error("camera.checkForUpdates", "missingParameter",
    "missing input parameter stateFingerprint");
  exit;
}

// get state
$state = get_camera_state();
if ($state === false) {
  echo format_error("camera.checkForUpdates", "serverError",
    "error getting state from camera");
  exit;
}

keepalive_sessions();

$timeout = 0;
if (isset($input['waitTimeout'])) {
  $timeout = max(0, intval($input['waitTimeout']));
  unset($input['waitTimeout']);
}

// if timeout too long, shorten it so we don't exceed max PHP execution time
if ($timeout > 240) {
  $timeout = 240;
}

// compare fingerprints, only return on timeout or state change
while ($input['stateFingerprint'] == $state['fingerprint'] &&
    time() < ($_SERVER['REQUEST_TIME'] + $timeout)) {
  sleep(1);
  // get state
  $state = get_camera_state();
  if ($state === false) {
    echo format_error("camera.checkForUpdates", "serverError",
      "error getting state");
    exit;
  }
}

// decide on appropriate throttle timeout
$throttleTimeout = 0;
if ($timeout < 30 && $input['stateFingerprint'] == $state['fingerprint']) {
  // short wait timeout and no state change, add some throttle timeout
  // (if state has changed, allow another state request immediately)
  $throttleTimeout = 30 - $timeout;
}

// check for invalid input
/*unset($input['stateFingerprint']);
if (!empty($input)) {
  reset($input);
  $name = key($input);
  echo format_error("camera.checkForUpdates", "invalidParameterName",
    "option name " . $name . " does not exist");
  exit;
}*/

keepalive_sessions();

// send output
?>{
  "stateFingerprint":"<?php echo($state['fingerprint']); ?>",
  "throttleTimeout":<?php echo($throttleTimeout); ?>
}