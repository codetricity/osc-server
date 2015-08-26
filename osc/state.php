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

// get state
$state = get_camera_state();
if ($state === false) {
  echo format_error("camera.state", "serverError",
    "error getting state from camera");
  exit;
}

keepalive_sessions();

// send output
?>{
  "fingerprint":"<?php echo($state['fingerprint']); ?>",
  "state":{
    "sessionId":"<?php echo($state['sessionId']); ?>",
    "batteryLevel":<?php echo($state['batteryLevel']); ?>,
    "storageChanged":false
  }
}