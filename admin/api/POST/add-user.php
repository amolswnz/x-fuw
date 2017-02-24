<?php
  require_once __DIR__ . '/../../../api/connect-inc.php';
  // require_once __DIR__ . '/../Session.php';

  // $session = Session::getInstance();
  // $sessionId = $session->id;
  $keys = implode(",", array_keys($_POST));
  $questionMarks = placeholders($_POST);

  try {
    $sql = "INSERT INTO gd_client ($keys) VALUES ($questionMarks)";
    $stmt = $pdo->prepare($sql);
    $count=1;
    foreach ($_POST as $value) {
        $stmt->bindValue($count++, $value);
    }
    $stmt->bindValue(4, md5($_POST['pwd']));
    $stmt->execute();
    $lastId = $pdo->lastInsertId();     // Returns last inserted id
    echo json_encode(array("success" => "Client added to database."));
  } catch(PDOException $e) {
    echo json_encode(array("error" => "The client detail already exists in database."));
  }
