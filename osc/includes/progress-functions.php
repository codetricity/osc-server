<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

require_once('implementation/constants.php');
require_once('implementation/picture-progress-functions.php');

function db_connect_progress() {
  // connect to database
  global $database_path;
  try {
    $db = new SQLite3($database_path . 'progress-database.sqlite');
  } catch (Exception $e) {
    return false;
  }
  // create table if it doesn't already exist
  $query = "CREATE TABLE IF NOT EXISTS progress(uri, " .
    "id INTEGER PRIMARY KEY AUTOINCREMENT);";
  if ($db->exec($query) === false) {
    return false;
  }
  // return database handle
  return $db;
}

function log_picture_in_progress($uri) {
  // connect to db
  $db = db_connect_progress();
  // insert uri
  $query = "INSERT INTO progress(uri) " . 
    "values(:uri)";
  $statement = $db->prepare($query);
  $statement->bindValue(':uri', $uri, SQLITE3_TEXT);
  if ($statement->execute() === false) {
    return false;
  }
  $sessionId = $db->lastInsertRowID();
  // return id
  return $sessionId;
}

function check_picture_progress($id) {
  // connect to db
  $db = db_connect_progress();
  // get uri
  $query = "SELECT uri from progress WHERE id = :ID";
  $statement = $db->prepare($query);
  $statement->bindValue(':ID', intval($id), SQLITE3_INTEGER);
  if (($result = $statement->execute()) === false) {
    return false;
  }
  $row = $result->fetchArray();
  if ($row === false) {
    return false;
  }
  return lookup_picture_progress($row['uri']);
}

?>