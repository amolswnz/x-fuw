<?php
  date_default_timezone_set('Pacific/Auckland');  // Set NZ TimeZone
  try {
    $interval = DateInterval::createFromDateString($_POST['textDate']);

    // $data = "";
    // if($interval->m != 0)
    //   $data .= $interval->m . " Month ";
    // if($interval->d != 0)
    //   $data .= $interval->d . " Days ";
    // if($interval->h != 0)
    //   $data .= $interval->h . " Hours ";
    // if($interval->i != 0)
    //   $data .= $interval->i . " Minutes ";
    // if($data == "")
    //   throw new Exception;

    if($interval->m != 0 && $interval->d != 0 && $interval->h != 0 && $interval->i != 0)
      throw new Exception;
    $str = "Valid data "
        . $interval->m . " month - "
        . $interval->d . " days - "
        . $interval->h . " hours - "
        . $interval->i . " minutes";
    echo json_encode(array("success" => $str));
  } catch (Exception $e) {
    echo json_encode(array("error" => "Invalid datetime format given in the string representation"));
  }
