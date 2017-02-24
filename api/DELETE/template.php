<?php
  require_once __DIR__ . '/../connect-inc.php';
  require_once __DIR__ . '/../Session.php';

  $session = Session::getInstance();
  $sessionId = $session->id;

  try {
    $sql = "UPDATE gd_template SET status = ? WHERE tid = ? AND client_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, 'Inactive');
    $stmt->bindValue(2, $_POST['tid']);
    $stmt->bindValue(3, $sessionId);
    $stmt->execute();
    // $count = $stmt->rowCount();   // Returns number of rows updated
    echo json_encode(array("success" => "Template deleted."));
  } catch(PDOException $e) {
    echo json_encode(array("error" => "Please check error log for details."));
  }
