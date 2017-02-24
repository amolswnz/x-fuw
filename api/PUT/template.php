<?php
  require_once __DIR__ . '/../connect-inc.php';
  require_once __DIR__ . '/../Session.php';

  $session = Session::getInstance();
  $sessionId = $session->id;

  try {
    $sql = "UPDATE gd_template SET templateName = ?, subject = ?, msgBody = ? WHERE tid = ? AND client_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $_POST['templateName']);
    $stmt->bindValue(2, $_POST['subject']);
    $stmt->bindValue(3, $_POST['msgBody']);
    $stmt->bindValue(4, $_POST['tid']);
    $stmt->bindValue(5, $sessionId);

    $stmt->execute();
    echo json_encode(array("success" => "Template updated."));
  } catch(PDOException $e) {
    echo json_encode(array("error" => "Please check error log for details."));
  }
