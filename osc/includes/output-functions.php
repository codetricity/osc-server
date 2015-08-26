<?php

// NCTech CONFIDENTIAL
// Copyright (c) 2014-2015 NCTech Ltd.
// All Rights Reserved.

// NOTICE:  All information contained herein is, and remains the property of
// NCTech.  Dissemination of this information or reproduction of this material
// is strictly forbidden unless prior written permission is obtained from
// NCTech.

// turn off all error reporting
error_reporting(0);

function format_results($functionName, $results) {
  $output = array(
    "name" => $functionName,
    "state" => "done"
  );
  if ($results !== "" && $results !== array()) {
    $output['results'] = $results;
  }
  $out = json_encode($output, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
  $out = str_replace(array('"###INT###', '###INT###"'), "", $out);
  return $out;
}

function format_error($functionName, $errorCode, $message) {
  global $error;
  $output = array(
    "name" => $functionName,
    "state" => "error",
    "error" => array(
      "code" => $errorCode,
      "message" => $message,
      "details" => $error
    )
  );
  return json_encode($output, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}

function format_in_progress($functionName, $id, $percentComplete=0) {
  $output = array(
    "name" => $functionName,
    "state" => "inProgress",
    "progress" => array(
      "completion" => $percentComplete
    ),
    "id" => "$id"
  );
  return json_encode($output, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
}

?>