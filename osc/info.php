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
require_once('implementation/info-functions.php');
require_once('includes/output-functions.php');
require_once('includes/session-functions.php');

// get camera info
$info = get_camera_info();
if ($info === false) {
  echo format_error("camera.info", "serverError",
    "error getting info from camera");
  exit;
}

keepalive_sessions();

// send output
?>{
  "manufacturer":"NCTech Imaging",
  "model":"<?php echo($info['modelName']); ?>",
  "serialNumber":"<?php echo($info['serialNumber']); ?>",
  "firmwareVersion":"<?php echo($info['firmwareVersion']); ?>",
  "supportUrl":"http://support.nctechimaging.com",
  "endpoints":{
    "httpPort":80,
    "httpUpdatesPort":80
  },
  "gps":true,
  "gyro":true,
  "uptime":<?php echo($info['uptime']); ?>,
  "api":[
    "/osc/info",
    "/osc/state",
    "/osc/checkForUpdates",
    "/osc/commands/execute",
    "/osc/commands/status"
  ]
}