<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

require_once('includes/output-functions.php');
require_once('implementation/constants.php');

function db_connect_sessions() {
  // connect to database
  global $database_path;
  try {
    $db = new SQLite3($database_path . 'session-database.sqlite');
  } catch (Exception $e) {
    return false;
  }
  // create table if it doesn't already exist
  $query = "CREATE TABLE IF NOT EXISTS sessions(lastUpdated INTEGER, timeout " .
    "INTEGER, finalised BOOLEAN, id INTEGER PRIMARY KEY AUTOINCREMENT);";
  if ($db->exec($query) === false) {
    return false;
  }
  // close any sessions that have timed out
  $query = "UPDATE sessions SET finalised = 1 WHERE lastUpdated < (strftime" .
    "('%s', CURRENT_TIMESTAMP) - timeout)";
  $db->exec($query);
  // return database handle
  return $db;
}

function keepalive_sessions() {
  // connect to database
  if (($db = db_connect_sessions()) === false) {
    return;
  }
  // update any other sessions
  $query = "UPDATE sessions SET lastUpdated = (strftime" .
    "('%s', CURRENT_TIMESTAMP)) where finalised = 0";
  $db->exec($query);
}

function get_current_session_id() {
  // connect to database
  if (($db = db_connect_sessions()) === false) {
    return '';
  }
  // check for existing non-old sessions
  $query = "SELECT id FROM sessions WHERE finalised = 0 ORDER BY id DESC";
  $currentSession = $db->querySingle($query);
  if ($currentSession !== false) {
    return $currentSession;
  }
  return '';
}

function validate_session($sessionId, $caller) {
  $currentSession = get_current_session_id();
  if (!is_int($currentSession) || !is_string($sessionId) ||
      intval($sessionId) !== $currentSession) {
    echo format_error("$caller", "invalidParameterValue",
      "session ID invalid");
    exit;
  }
}

?>