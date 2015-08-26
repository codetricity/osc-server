<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

require_once('includes/session-functions.php');

define("MAX_SESSION_LENGTH", 3600);

function command_start_session($timeout) {
  // connect to database
  if (($db = db_connect_sessions()) === false) {
    return format_error("camera.startSession", "serverError",
      "cannot connect to sessions database");
  }
  // check no existing non-old sessions
  $query = "SELECT COUNT(*) FROM sessions WHERE finalised = 0";
  $numSessions = $db->querySingle($query);
  if ($numSessions !== false && $numSessions !== 0) {
    return format_error("camera.startSession", "cameraInExclusiveUse",
      "device already in exclusive use, new session can't be started");
  }
  // create new session
  $query = "INSERT INTO sessions(lastUpdated, timeout, finalised) " . 
    "values(strftime('%s', CURRENT_TIMESTAMP), :timeout, 0)";
  $statement = $db->prepare($query);
  if (!is_int($timeout) || $timeout > MAX_SESSION_LENGTH || $timeout < 0) {
    $timeout = MAX_SESSION_LENGTH;
  }
  $statement->bindValue(':timeout', $timeout, SQLITE3_INTEGER);
  if ($statement->execute() === false) {
    return format_error("camera.startSession", "serverError",
      "unable to create new session");
  }
  $sessionId = $db->lastInsertRowID();
  // return the session id
  $results = array(
    "sessionId" => "$sessionId",
    "timeout" => $timeout
    );
  return format_results("camera.startSession", $results);
}

function command_update_session($sessionId, $timeout) {
  // connect to database
  if (($db = db_connect_sessions()) === false) {
    return format_error("camera.updateSession", "serverError",
      "cannot connect to sessions database");
  }
  // check if session exists
  $query = "SELECT COUNT(*) from sessions WHERE id = :sessionID";
  $statement = $db->prepare($query);
  $statement->bindValue(':sessionID', $sessionId, SQLITE3_INTEGER);
  if (($result = $statement->execute()) === false) {
    return format_error("camera.updateSession", "invalidParameterValue",
      "malformed session ID");
  }
  $row = $result->fetchArray();
  if ($row["COUNT(*)"] != 1) {
    return format_error("camera.updateSession", "invalidParameterValue",
      "session with given ID doesn't exist");
  }
  // update session
  $query = "UPDATE sessions SET lastUpdated = " . 
    "strftime('%s', CURRENT_TIMESTAMP), timeout = :timeout " . 
    "WHERE id = :sessionID AND finalised = 0";
  $statement = $db->prepare($query);
  $statement->bindValue(':sessionID', $sessionId, SQLITE3_INTEGER);
  if (!is_int($timeout) || $timeout > MAX_SESSION_LENGTH || $timeout < 0) {
    $timeout = MAX_SESSION_LENGTH;
  }
  $statement->bindValue(':timeout', $timeout, SQLITE3_INTEGER);
  if ($statement->execute() === false) {
    return format_error("camera.updateSession", "serverError",
      "unable to update session in database");
  }
  $numChanges = $db->changes();
  if ($numChanges != 1) {
    return format_error("camera.updateSession", "invalidParameterValue",
      "session with given ID is no longer active");
  }
  // return results
  $results = array(
    "sessionId" => "$sessionId",
    "timeout" => $timeout
    );
  return format_results("camera.updateSession", $results);
}

function command_close_session($sessionId) {
  // connect to database
  if (($db = db_connect_sessions()) === false) {
    return format_error("camera.closeSession", "serverError",
      "cannot connect to sessions database");
  }
  // check if session exists
  $query = "SELECT COUNT(*) from sessions WHERE id = :sessionID";
  $statement = $db->prepare($query);
  $statement->bindValue(':sessionID', $sessionId, SQLITE3_INTEGER);
  if (($result = $statement->execute()) === false) {
    return format_error("camera.closeSession", "invalidParameterValue",
      "malformed session ID");
  }
  $row = $result->fetchArray();
  if ($row["COUNT(*)"] != 1) {
    return format_error("camera.closeSession", "invalidParameterValue",
      "session with given ID doesn't exist");
  }
  // close session
  $query = "UPDATE sessions SET finalised = 1 WHERE id = :sessionID " .
    "AND finalised = 0";
  $statement = $db->prepare($query);
  $statement->bindValue(':sessionID', $sessionId, SQLITE3_INTEGER);
  if ($statement->execute() === false) {
    return format_error("camera.closeSession", "serverError",
      "cannot close session in database");
  }
  $numChanges = $db->changes();
  // return results
  if ($numChanges != 1) {
    return format_error("camera.closeSession", "invalidParameterValue",
      "session with given ID is no longer active");
  }
  return format_results("camera.closeSession", "");  
}

?>