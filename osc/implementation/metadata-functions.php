<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

function get_metadata($uri) {
  // check if file exists
  if (!is_file('photos/' . $uri . '-full.jpg')) {
    return false;
  }
  // get metadata
  $exif = exif_read_data('photos/' . $uri . '-full.jpg');
  $exifresults = array(
    "ExifVersion" =>"0220",
    "ImageDescription" => "Sample image by NCTech",
    "DateTime" => "2015:01:01 11:22:33",
    "ImageWidth" => $exif['COMPUTED']['Width'],
    "ImageLength" => $exif['COMPUTED']['Height'],
    "ColorSpace" => 1,
    "Orientation" => 1,
    "Flash" => 32,
    "FocalLength" => 14.95,
    "WhiteBalance" => 0,
    "ExposureTime" => 0.001,
    "FNumber" => 2.67,
    "ExposureProgram" => 0,
    "ISOSpeedRatings" => 400,
    "ShutterSpeedValue" => 0.001,
    "ApertureValue" => 2.67,
    "GPSProcessingMethod" => 0,
    "GPSLatitudeRef" => "N",
    "GPSLatitude" => 55.9238,
    "GPSLongitudeRef" => "W",
    "GPSLongitude" => 3.2094,
    "Make" => "NCTech",
    "Model" => "iSTAR Pulsar",
    "Software" => "2.1.0",
    "Copyright" => "NCTech Ltd. 2015"
  );
  $xmpresults = array(
    "ProjectionType" => "equirectangular",
    "UsePanoramaViewer" => true,
    "PoseHeadingDegrees" => 42.0,
    "CroppedAreaImageWidthPixels" => $exif['COMPUTED']['Width'],
    "CroppedAreaImageHeightPixels" => $exif['COMPUTED']['Height'],
    "FullPanoWidthPixels" => $exif['COMPUTED']['Width'],
    "FullPanoHeightPixels" => $exif['COMPUTED']['Height'],
    "CroppedAreaLeftPixels" => 0,
    "CroppedAreaTopPixels" => 0
  );
  return array(
    "exif" => $exifresults,
    "xmp" => $xmpresults
  );
}

?>