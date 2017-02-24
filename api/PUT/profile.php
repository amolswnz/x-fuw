<?php
  require_once __DIR__ . '/../connect-inc.php';
  require_once __DIR__ . '/../Session.php';

  $session = Session::getInstance();
  $sessionId = $session->id;

  try {
    $sql = "UPDATE gd_client SET clientName = ?, clientEmail = ?, accessId = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $_POST['clientName']);
    $stmt->bindValue(2, $_POST['clientEmail']);
    $stmt->bindValue(3, $_POST['accessId']);
    $stmt->bindValue(4, $sessionId);

      // CHECK ACCESS ID WITH OUR DATABASE

    $stmt->execute();
    echo json_encode(array("success" => "Profile updated."));
  } catch(PDOException $e) {
    echo json_encode(array("error" => "Please check error log for details."));
  }
