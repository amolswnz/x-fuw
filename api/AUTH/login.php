<?php
  require_once __DIR__ . '/../connect-inc.php';
  require_once __DIR__ . '/../Session.php';

  $sql = "SELECT * FROM gd_client WHERE clientEmail = ? AND pwd = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(1, $_POST['clientEmail']);
  $stmt->bindValue(2, md5($_POST['pwd']));
  $stmt->execute();

  $userDetails =  $stmt->fetch();
  if($userDetails) {
      if($userDetails['status'] == "Active") {
          $session = Session::getInstance();
          $session->id = $userDetails['id'];
          $session->name = $userDetails['clientName'];
          $message = "User active and login successfully.";
      }
      else if($userDetails['status'] == "Inactive") {
          $message = "Sorry! Your account has not be activated. Plese contact Administrator.";
      }
      else if($userDetails['status'] == "Deleted") {
          $message = "Sorry! Your account has been deleted by Administrator.";
      }
      echo json_encode(array(strtolower($userDetails['status']) => $message));
  }
  else
      echo json_encode(array('error'=> "Sorry! Your email and password combination do not match."));
