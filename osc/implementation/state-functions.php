<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

require_once('includes/session-functions.php');

function get_camera_state() {
  $state = get_current_session_id();
  return array(
    "batteryLevel" => 57,
    "sessionId" => $state,
    "fingerprint" => $state . ".57"
  );
}

?>