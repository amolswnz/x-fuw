<?php
  require_once __DIR__ . '/../connect-inc.php';
  require_once __DIR__ . '/../Session.php';

  $session = Session::getInstance();
  $sessionId = $session->id;

  // Adding session id to post - reason check below insert query
  $_POST['client_id'] = $sessionId;

  $keys = implode(",", array_keys($_POST));
  $questionMarks = placeholders($_POST);

  try {
    $sql = "INSERT INTO gd_template ($keys) VALUES ($questionMarks)";
    $stmt = $pdo->prepare($sql);
    $count=1;
    foreach ($_POST as $value) {
        $stmt->bindValue($count++, $value);
    }
    $stmt->execute();
    $lastId = $pdo->lastInsertId();     // Returns last inserted id
    echo json_encode(array("success" => "Template added to db."));
  } catch(PDOException $e) {
    echo json_encode(array("error" => "Please check error log for details."));
  }
