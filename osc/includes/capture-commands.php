<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

require_once('implementation/capture-functions.php');
require_once('includes/output-functions.php');
require_once('includes/session-functions.php');
require_once('includes/progress-functions.php');

function command_take_picture($sessionId) {
  // check session id is valid
  validate_session($sessionId, "camera.takePicture");
  // take picture
  $picture = capture_image();
  if ($picture === false) {
    return format_error("camera.takePicture", "serverError",
      "unable to capture image");
  }
  // log command in progress and get command ID to return
  $id = log_picture_in_progress($picture['uri']);
  // return progress update
  return format_in_progress("camera.takePicture", $id);
}

?>