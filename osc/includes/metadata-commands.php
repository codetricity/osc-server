<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

require_once('includes/output-functions.php');
require_once('implementation/metadata-functions.php');

function command_get_metadata($uri) {
  // get metadata
  $metadata = get_metadata($uri);
  if ($metadata === false) {
    return format_error("camera.getMetadata", "invalidParameterValue",
      "image with given ID does not exist");
  }
  // output results
  return format_results("camera.getMetadata", $metadata);
}

?>