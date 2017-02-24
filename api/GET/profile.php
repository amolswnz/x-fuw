<?php
  require_once __DIR__ . '/../connect-inc.php';
  require_once __DIR__ . '/../Session.php';

  $session = Session::getInstance();
  $sessionId = $session->id;

  $sql = "SELECT * FROM gd_client WHERE id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(1, $sessionId);
  $stmt->execute();
  return $stmt->fetch();
