<?php
  require_once __DIR__ . '/../connect-inc.php';
  require_once __DIR__ . '/../Session.php';

  $session = Session::getInstance();
  $sessionId = $session->id;

  $sql = "SELECT * FROM gd_template WHERE client_id = ? AND status = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(1, $sessionId);
  $stmt->bindValue(2, 'Active');
  $stmt->execute();
  // var_dump($stmt->fetchAll());
  return $stmt->fetchAll();
