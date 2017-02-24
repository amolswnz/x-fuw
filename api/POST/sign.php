<?php
  require_once __DIR__ . '/../connect-inc.php';
  require_once __DIR__ . '/../Session.php';

  $session = Session::getInstance();
  $sessionId = $session->id;

  try {
    $sql = "UPDATE gd_client SET emailSign = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $_POST['sign']);
    $stmt->bindValue(2, $sessionId);
    $stmt->execute();
    echo json_encode(array("success" => "Email signature updated."));
  } catch(PDOException $e) {
    echo json_encode(array("error" => "Please check error log for details."));
  }
