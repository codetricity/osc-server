<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

require_once('implementation/constants.php');

function db_connect_storage() {
  // connect to database
  global $database_path;
  try {
    $db = new SQLite3($database_path . 'storage-database.sqlite');
  } catch (Exception $e) {
    return false;
  }
  // create table if it doesn't already exist
  $query = "CREATE TABLE IF NOT EXISTS storageTokens(directoryPath, " .
    "lastEntrySent, id INTEGER PRIMARY KEY AUTOINCREMENT);";
  if ($db->exec($query) === false) {
    return false;
  }
  // return database handle
  return $db;
}

function resize_image($originalPath, $newPath, $maxSize) {
  $size = getimagesize($originalPath);
  // if original image isn't larger than requested, don't resize
  if ($size[0] <= $maxSize && $size[1] <= $maxSize) {
    return false;
  }
  // calculate new dimensions
  if ($size[0] > $size[1]) {
    // landscape
    $width = $maxSize;
    $height = round($maxSize * $size[1] / $size[0]);
  } else {
    // portrait
    $width = round($maxSize * $size[0] / $size[1]);
    $height = maxSize;
  }
  // rescale image
  $original = ImageCreateFromJPEG($originalPath);
  $new = ImageCreateTrueColor($width, $height);
  ImageCopyResampled($new, $original, 0, 0, 0, 0, $width, $height,
    $size[0], $size[1]);
  ImageDestroy($original);
  if ($newPath != "") {
    ImageJPEG($new, $newPath, 90);
    ImageDestroy($new);
    return true;
  } else {
    ob_start();
    ImageJPEG($new, NULL, 90);
    ImageDestroy($new);
    return ob_get_clean();
  }
}

function get_image($uri, $maxSize) {
  // get path to image
  $path = 'photos/' . $uri . '-full.jpg';
  // if requested size is small enough, use thumbnail not fullsize for speed
  if ($maxSize <= 400) {
    $path = 'photos/' . $uri . '-thumbnail.jpg';
  }
  // check path is valid
  if (!file_exists($path))
  {
    return false;
  }
  // scale image if needed
  $size = getimagesize($path);
  if ($size[0] > $maxSize || $size[1] > $maxSize) {
    resize_image($path, 'photos/' . $uri . '-resized.jpg', $maxSize);
    $path = 'photos/' . $uri . '-resized.jpg';
  }
  // send the jpeg to the user
  if (($handle = @fopen($path, "rb")) === false)
  {
    return false;
  }
  $imageData = fread($handle, filesize($path));
  fclose($handle);
  return $imageData;
}

function getGps($exifCoord, $hemi) {
  $degrees = count($exifCoord) > 0 ? gps2Num($exifCoord[0]) : 0;
  $minutes = count($exifCoord) > 1 ? gps2Num($exifCoord[1]) : 0;
  $seconds = count($exifCoord) > 2 ? gps2Num($exifCoord[2]) : 0;
  $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
  return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
}

function gps2Num($coordPart) {
  $parts = explode('/', $coordPart);
  if (count($parts) <= 0) {
    return 0;
  }
  if (count($parts) == 1) {
    return $parts[0];
  }
  return floatval($parts[0]) / floatval($parts[1]);
}

function get_list_of_all_images() {
  // scan directory
  $files = @glob("photos/*-full.jpg");
  if ($files === false) {
    return false;
  }
  rsort($files, SORT_NATURAL);
  // create list
  $list = array();
  foreach ($files as $file) {
    if ($file != "." && $file != "..") {
      $uri = str_replace("photos/", "", $file);
      $uri = str_replace("-full.jpg", "", $uri);
      $item = array(
        "uri" => $uri,
        "path" => $file
      );
      array_push($list, $item);
    }
  }
  return $list;
}

function log_continuation_value($finalValue) {
  $db = db_connect_storage();
  if ($db === false) {
    return false;
  }
  $query = "INSERT INTO storageTokens(directoryPath, lastEntrySent) " .
    "values('photos', :lastEntry)";
  $statement = $db->prepare($query);
  $statement->bindValue(':lastEntry', $finalValue, SQLITE3_TEXT);
  if ($statement->execute() === false) {
    return false;
  }
  $token = $db->lastInsertRowID();
  return "$token";
}

function get_starting_value($continuationToken) {
  $db = db_connect_storage();
  if ($db === false) {
    return false;
  }
  $query = "SELECT * from storageTokens WHERE id = :token";
  $statement = $db->prepare($query);
  if (!$statement->bindValue(':token', $continuationToken, SQLITE3_INTEGER)) {
    return false;
  }
  if (($result = $statement->execute()) === false) {
    return false;
  }
  $row = $result->fetchArray();
  if (!isset($row["lastEntrySent"])) {
    return false;
  }
  $start = $row["lastEntrySent"];
  $statement->close();
  return $start;
}

function get_image_list($startingValue, $numEntries) {
  // get images
  $files = get_list_of_all_images();
  if ($files === false) {
    return false;
  }
  // loop through all files, counting them and outputting them as necessary
  $count = 0;
  $entries = array();
  foreach ($files as $file) {
    // only take values above starting value, if provided
    if ($startingValue === "" || strnatcmp($file['uri'], $startingValue) < 0) {
      $count++;
      // only print file if we haven't reached entryCount limit
      if ($count <= $numEntries) {
        // get file info
        $exif = exif_read_data($file['path']);
        // create entry
        $entry = array(
          "uri" => $file['uri'],
          "dateTimeZone" => date("Y:m:d G:i:sP", intval($exif['FileDateTime'])),
          "width" => $exif['COMPUTED']['Width'],
          "height" => $exif['COMPUTED']['Height'],
          "name" => $file['uri'],
          "size" => filesize($file['path'])
        );
        // add gps info if present
        if (isset($exif['GPSLatitude']) && isset($exif['GPSLatitudeRef']) &&
         isset($exif['GPSLongitude']) && isset($exif['GPSLongitudeRef'])) {
          $entry["lat"] = getGps($exif['GPSLatitude'],
            $exif['GPSLatitudeRef']);
          $entry["lng"] = getGps($exif['GPSLongitude'],
            $exif['GPSLongitudeRef']);
        }
        // add entry to list of entries
        array_push($entries, $entry);
        // create continuation token if necessary
        $lastFile = $file['uri'];
      } else {
        // we have more files than requested
        $continuationToken = log_continuation_value($lastFile);
        return array(
          "entries" => $entries,
          "totalEntries" => $numEntries,
          "continuationToken" => $continuationToken
        );
      }
    }
  }
  return array(
    "entries" => $entries,
    "totalEntries" => $count
  );
}

function delete_image($uri) {
  if (!file_exists("photos/" . $uri . "-full.jpg")) {
    return false;
  }
  @unlink("photos/" . $uri . "-full.jpg");
  @unlink("photos/" . $uri . "-thumbnail.jpg");
}

?>