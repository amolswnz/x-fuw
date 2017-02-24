<?php
  require_once __DIR__ . '/../Session.php';

  $session = Session::getInstance();
  $session->destroy();
  // echo json_encode(array("success"=>"You have been looged out successfully !"));
  return "You have been looged out successfully !";
