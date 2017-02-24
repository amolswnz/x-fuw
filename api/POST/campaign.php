<?php
  require_once __DIR__ . '/../connect-inc.php';
  require_once __DIR__ . '/../Session.php';

  $session = Session::getInstance();
  $sessionId = $session->id;

  // Insert the schedule
  $titleOfSchedule = array_shift($_POST);
  $sql = "INSERT INTO gd_schedule(title, client_id) VALUE(?, ?)";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(1, $titleOfSchedule);
  $stmt->bindValue(2, $sessionId);
  $stmt->execute();
  $parentId = $pdo->lastInsertId();

  $arrayCounter = 0;
  $previousIndex = 1;
  $insertDataArrayUnformatted = array(array());

  foreach ($_POST as $key => $value) {
    // Gets current key number eg from dateVersion_2 gives 2
    $currentIndex = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
    // Check if the current item belongs to same group or not
    // Done by checking previous and current index equality
    if($previousIndex != $currentIndex) {
      $previousIndex = $currentIndex;
      $arrayCounter++;
      // Increment array counter to add all the group variable in one array
    }
    // Removing number from the key name
    $keyNameClean = str_replace("_$currentIndex", "", $key);
    $insertDataArrayUnformatted[$arrayCounter][$keyNameClean] = $value;
  }

  $arrayCounter = 0;
  $insertData = array(array());
  $prevRowData = array(array());  // Temperary storage for wrtPrevDate calculations

  // Formatting the $insertDataArrayUnformatted
  foreach ($insertDataArrayUnformatted as $data) {
    // Consider date ordered in following priorities
      // Priority 1 - dateVersion
      // Priority 2 - textVersion
    if(isset($data['dateVersion']) && $data['dateVersion'] != "") {
        $absoluteDateTime = new DateTime($data['dateVersion']);
      $insertData[$arrayCounter]['timeToSend'] = $absoluteDateTime->format('Y-m-d H:i:s');
    }
    else {  // if(isset($data['textVersion']))
      $insertData[$arrayCounter]['timeToSend'] = $data['textVersion'];
    }

    // Additional parameters configuration
    $extraOptions = array();
    isset($data['sendEmail']) ? $extraOptions['sendEmail'] = true : $extraOptions['sendEmail'] = false;
    isset($data['sendCopy']) ?  $extraOptions['sendCopy'] = true : $extraOptions['sendCopy'] = false;
    if(isset($data['reminderText']) && !empty($data['reminderText']))
      $extraOptions['reminderText'] = $data['reminderText'];
    else
      $extraOptions['reminderText'] = false;
    // Addtional parameters store as json string - { sendEmail: true/false, sendCopy: true/false, sendReminder: false/Text }
    $insertData[$arrayCounter]['additionalParam'] = json_encode($extraOptions);

    $insertData[$arrayCounter]['templateId'] = $data['templateId'];      // templateId
    $insertData[$arrayCounter]['parentId'] = $parentId;     // Every entry has same schedule title
    $insertData[$arrayCounter]['client_id'] = $sessionId;     // Every entry has same schedule title

    $arrayCounter++; // increasing array counter for insertData varible
  }

  $keys = implode(",", array_keys($insertData[0]));
  $questionMarks = placeholders($insertData[0]);

  foreach ($insertData as $data) {
    $sql = "INSERT INTO gd_scheduleGroup ($keys) VALUES ($questionMarks)";
    $stmt = $pdo->prepare($sql);
    $count=1;
    foreach ($data as $value) {
        $stmt->bindValue($count++, $value);
    }
    $stmt->execute();
    $insertedIds[] = $pdo->lastInsertId();     // Returns last inserted id
  }

  // Get list of inserted schedules
  $inQuery = implode(',', array_fill(0, count($insertedIds), '?'));
  $stmt = $pdo->prepare("SELECT * FROM gd_scheduleGroup WHERE sgid IN($inQuery)");
  $stmt->execute($insertedIds);
  $allSchedules = $stmt->fetchAll();

  // Get list of all contacts - new schedules are to be populated for all the contacts present in the database
  $sql = "SELECT * FROM gd_contact WHERE status = ? AND client_id = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(1, 'Active');
  $stmt->bindValue(2, $sessionId);
  $stmt->execute();
  $allContacts = $stmt->fetchAll();
  if(!$allContacts) {
    echo json_encode(array("success" => "No contacts to schedule job."));
    die();
  }

  $count = 0;
  foreach ($allSchedules as $schedule) {
    // Is the absolute or relative time format is given
    $absolute = DateTime::createFromFormat('Y-m-d H:i:s', $schedule['timeToSend']) ? true : false;

    foreach ($allContacts as $contact) {
      $regDate = new DateTime($contact['dateRegistered']);
      $insertArray[$count]['contactId'] = $contact['cid'];
      $insertArray[$count]['scheduleGroupId'] = $schedule['sgid'];

      $now = new DateTime();
      if($absolute) { // Directly enter the DateTime to the database
        $postTime = DateTime::createFromFormat('Y-m-d H:i:s', $schedule['timeToSend']);
        if($postTime < $now) { // Scheduled post time is less than now
          $insertArray[$count]['status'] = '404';
          $insertArray[$count]['statusDescription'] = 'This user registered after the mail event was passed.';
        } else {
          $insertArray[$count]['status'] = 'Pending';
          $insertArray[$count]['statusDescription'] = 'The email schedule is pending for this job.';
        }
        $insertArray[$count]['postTime'] = $postTime->format('Y-m-d H:i');
      } else {  // If absolute time given then add the scheduled timeToSend to registration DateTime of the user
        $newPostTime = $regDate->modify($schedule['timeToSend']);
        if($newPostTime < $now) { // Scheduled post time is less than now
          $insertArray[$count]['status'] = '404';
          $insertArray[$count]['statusDescription'] = 'This event was scheduled at a time in the past after the user is registered.';
        } else {
          $insertArray[$count]['status'] = 'Pending';
          $insertArray[$count]['statusDescription'] = 'The email schedule is pending for this job.';
        }
        $insertArray[$count]['postTime'] = $newPostTime->format('Y-m-d H:i');
      }
      $count++;
     }
   }

  // Insert all scheduled data into the scheduled job database
  foreach ($insertArray as $row) {
    try {
      $sqlx = "INSERT INTO gd_scheduledJob(contactId, scheduleGroupId, postTime, status, statusDescription, client_id) VALUE (?,?,?,?,?,?)";
      $stmt = $pdo->prepare($sqlx);
      $stmt->bindValue(1, $row['contactId']);
      $stmt->bindValue(2, $row['scheduleGroupId']);
      $stmt->bindValue(3, $row['postTime']);
      $stmt->bindValue(4, $row['status']);
      $stmt->bindValue(5, $row['statusDescription']);
      $stmt->bindValue(6, $sessionId);
      $stmt->execute();
    } catch(PDOException $e) {
      $error[] = $e->getMessage();
    }
  }

  if(isset($error)) {
    $err = "INSERT INTO gd_error(client_id, generator, errorText) VALUE (?, ?,?)";
    $stmt = $pdo->prepare($err);
    $errorStr = implode(",", $error);
    $stmt->bindValue(1, $sessionId);
    $stmt->bindValue(2, "post-campaign");
    $stmt->bindValue(3, $errorStr);
    $stmt->execute();
    echo json_encode(array("error" => "Please check error log for details."));
  } else {
    echo json_encode(array("success" => "Jobs scheduled for executions."));
  }
