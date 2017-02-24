<?php
  require_once __DIR__ . '/../connect-inc.php';

  try {
      // AND accessId is . . . where to get and where to store
      $sql = "SELECT * FROM gd_client WHERE clientEmail = ?";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(1, $_POST['clientEmail']);
      $stmt->execute();
      $clientData = $stmt->fetch();
  } catch (PDOException $e) {
      $error[] = $e->getMessage();
  }

  if(!$clientData) {
    // Log error
    // echo "ERROR CLIENT DETAILS NOT FOUND IN OUR DATABASE";
    $err = "INSERT INTO gd_error(client_id, generator, errorText) VALUE (?, ?,?)";
    $stmt = $pdo->prepare($err);
    $errorStr = "ERROR CLIENT DETAILS NOT FOUND IN OUR DATABASE for the email " . $_POST['clientEmail'];
    $stmt->bindValue(1, '0');
    $stmt->bindValue(2, "add-user");
    $stmt->bindValue(3, $errorStr);
    $stmt->execute();
    die();
  }

  try {
      $sql = "INSERT INTO gd_contact (name, email, client_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE dateRegistered = NOW()";
      $stmt = $pdo->prepare($sql);
      $fullName = $_POST['first_name'] . " " . $_POST['last_name'];
      $stmt->bindValue(1, $fullName);
      $stmt->bindValue(2, $_POST['email']);
      $stmt->bindValue(3, $clientData['id']);
      $stmt->execute();
  } catch (PDOException $e) {
      $error[] = $e->getMessage();
  }
  $lastInsertId = $pdo->lastInsertId();



  $sql = "SELECT * FROM gd_scheduleGroup WHERE client_id = ? AND status = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(1, $clientData['id']);
  $stmt->bindValue(2, 'Active');
  $stmt->execute();
  $allSchedules = $stmt->fetchAll();

  $count = 0;
  foreach ($allSchedules as $schedule) {
      echo "<ul>";
      echo "<li>For <sup>$lastInsertId-$schedule[sgid]</sup> $schedule[timeToSend] <br> ";
      $absolute = DateTime::createFromFormat('Y-m-d H:i:s', $schedule['timeToSend']) ? true : false;
      echo $absolute ? "<em>ABSOLUTE</em>" : "RELATIVE";

      $regDate = new DateTime();
      $insertArray[$count]['scheduleGroupId'] = $schedule['sgid'];
      echo "<br>";

      if ($absolute) {
          $now = new DateTime();
          $postTime = DateTime::createFromFormat('Y-m-d H:i:s', $schedule['timeToSend']);
          $insertArray[$count]['status'] = 'Pending';
          $insertArray[$count]['statusDescription'] = 'The email schedule is pending for this job.';
          if ($postTime < $now) {
              echo "<strike> ***** " . $postTime->format('Y-m H:i') . " ***** </strike>";
              $insertArray[$count]['status'] = '404';
              $insertArray[$count]['statusDescription'] = 'This user registered after the mail event was passed.';
          }
          echo $insertArray[$count]['postTime'] = $postTime->format('Y-m-d H:i');
      } else {
          $newPostTime = $regDate->modify($schedule['timeToSend']);
          $insertArray[$count]['status'] = 'Pending';
          $insertArray[$count]['statusDescription'] = 'The email schedule is pending for this job.';
          $insertArray[$count]['postTime'] = $newPostTime->format('Y-m-d H:i');
          echo "> PostTime " . $newPostTime->format('M-d H:i');
      }
      $count++;
      echo "</ul>";
  }

  if (! isset($insertArray)) {
      $error[] = "No schedules found when this user was added into the database.";
  } else {
      // Insert all scheduled data into the scheduled job database
      foreach ($insertArray as $row) {
          try {
              $sqlx = "INSERT INTO gd_scheduledJob(contactId, client_id, scheduleGroupId, postTime, status, statusDescription) VALUE (?,?,?,?,?,?)";
              $stmt = $pdo->prepare($sqlx);
              $stmt->bindValue(1, $lastInsertId);
              $stmt->bindValue(2, $clientData['id']);
              $stmt->bindValue(3, $row['scheduleGroupId']);
              $stmt->bindValue(4, $row['postTime']);
              $stmt->bindValue(5, $row['status']);
              $stmt->bindValue(6, $row['statusDescription']);
              $stmt->execute();
          } catch (PDOException $e) {
              $error[] = $e->getMessage();
          }
      }
  }

  if (isset($error)) {
      $err = "INSERT INTO gd_error(client_id, generator, errorText) VALUE (?, ?,?)";
      $stmt = $pdo->prepare($err);
      $errorStr = implode(",", $error);
      $stmt->bindValue(1, $clientData['id']);
      $stmt->bindValue(2, "add-user");
      $stmt->bindValue(3, $errorStr);
      $stmt->execute();
  }
