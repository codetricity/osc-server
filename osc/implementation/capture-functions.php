<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

function get_unused_filename($path) {
  $images = glob("photos/*-full.jpg");
  if (rsort($images, SORT_NATURAL) && isset($images[0])) {
    $filename = str_replace("-full.jpg", "", $images[0]);
    $filename = str_replace("photos/", "", $filename);
    return intval($filename) + 1;
  }
  return "1";
}

function capture_image() {
  // choose random sample image and corresponding thumbnail
  $images = glob("photo-examples/*-full.jpg");
  $image = $images[array_rand($images)];
  $thumbnail = str_replace("-full.jpg", "-thumbnail.jpg", $image);
  // get new filename
  $newName = get_unused_filename("photos");
  // copy images to new location
  $newImage = "photos/" . $newName . "-full.jpg";
  $newThumbnail = "photos/" . $newName . "-thumbnail.jpg";
  // check if photos directory exists and create if needed
  if (!file_exists("photos") && !is_dir("photos")) {
    if (!mkdir("photos")) {
      $errors = error_get_last();
      echo "Error: ".$errors['type']."\n";
      echo $errors['message']."\n";
      return false;
    }    
  }
  // check if php copy succeeds 
  if (!copy($image, $newImage)) {
    $errors = error_get_last();
    echo "Error: ".$errors['type']."\n";
    echo $errors['message']."\n";
    return false;
  }
  if (!copy($thumbnail, $newThumbnail)) {
    $errors = error_get_last();
    echo "Error: ".$errors['type']."\n";
    echo $errors['message']."\n";
    return false;
  }
  // get URI
  $uri = str_replace("photos/", "", $newImage);
  $uri = str_replace("-full.jpg", "", $uri);
  // return results
  return array(
    "uri" => $uri
  );
}

?>