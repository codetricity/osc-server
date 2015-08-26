<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

require_once('implementation/image-functions.php');
require_once('includes/output-functions.php');

function command_get_image($uri, $maxSize) {
  // check maxSize is valid
  if (!is_integer($maxSize) || $maxSize <= 0)
  {
    return format_error("camera.getImage", "invalidParameterValue",
      "maxSize invalid");
  }
  // limit max size to 10000
  if ($maxSize > 10000) {
    $maxSize = 10000;
  }
  // get image
  $image = get_image($uri, $maxSize);
  if ($image === false) {
    return format_error("camera.getImage", "invalidParameterValue", "image with given URI does not exist");
  }
  // send output as jpeg (header overrides previous setting "application/json"
  // provided headers have not already been sent)
  header('Content-Type: image/jpeg');
  return($image);
}

function command_list_images($entryCount, $maxSize, $continuationToken = "",
    $includeThumb = false) {
  // limit to 100 entries if not specified
  if ($entryCount <= 0 || $entryCount >= 100) {
    $entryCount = 100;
  }
  // retrieve starting value
  $start = "";
  if ($continuationToken != "") {
    $start = get_starting_value($continuationToken);
    if ($start === false) {
      return format_error("camera.listImages", "invalidParameterValue",
        "invalid continuation token");
    }
  }
  // get list of images
  $images = get_image_list($start, $entryCount);

  if ($images === false) {
    return format_error("camera.listImages", "serverError",
      "unable to retrieve images");
  }
  // retrieve thumbnails if requested
  if ($includeThumb !== false) {
    foreach ($images['entries'] as &$image) {
      $image['thumbnail'] = base64_encode(get_image($image['uri'], $maxSize));
    }
  }
  // return results
  return format_results("camera.listImages", $images);
}

function command_delete($uri) {
  if (delete_image($uri) === false) {
    return format_error("camera.delete", "invalidParameterValue",
      "image with given URI not found");
  }
  return format_results("camera.delete", array());
}

?>