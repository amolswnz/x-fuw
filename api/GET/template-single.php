<?php
  require_once __DIR__ . '/../connect-inc.php';
  require_once __DIR__ . '/../Session.php';

  $session = Session::getInstance();
  $sessionId = $session->id;

  // $id is coming from the page who requested this page
  if(isset($_POST['tid'])) {  // If request comes from campainer
    $id = $_POST['tid'];
  };

  $sql = "SELECT * FROM gd_template WHERE tid = ? AND client_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(1, $id);
  $stmt->bindValue(2, $sessionId);
  $stmt->execute();
  $data = $stmt->fetch();

  if(isset($_POST['tid'])) {  // If request comes from campainer
    echo json_encode($data);
  } else {
    return $data;
  }
