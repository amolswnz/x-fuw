<?php
  require_once __DIR__ . '/../connect-inc.php';
  require_once __DIR__ . '/../Session.php';

  $session = Session::getInstance();
  $sessionId = $session->id;
  
  if(isset($_POST['tid'])) {  // If request comes from campainer
    $scheduleGroupIdid = $_POST['sgid'];
    $contactId = $_POST['cid'];
  }
  /* else {
        $scheduleGroupIdid = contactId is coming from the page who requested this page ;
        $contactId = scheduleGroupId is coming from the page who requested this page ;
    } */

  $sql = "SELECT *
      FROM gd_scheduledJob
      JOIN gd_client ON gd_scheduledJob.client_id = gd_client.id
      JOIN gd_scheduleGroup ON gd_scheduledJob.scheduleGroupId = gd_scheduleGroup.sgid
      JOIN gd_contact ON gd_scheduledJob.contactId = gd_contact.cid
      JOIN gd_schedule ON gd_scheduleGroup.parentId = gd_schedule.sid
      JOIN gd_template ON gd_scheduleGroup.templateId = gd_template.tid
    WHERE
      contactId = $scheduleGroupIdid
      AND scheduleGroupId = $contactId
      AND postTime < NOW()
      AND gd_client.status = 'Active'
      AND gd_scheduleGroup.status = 'Active'
      AND gd_contact.status = 'Active'
      AND gd_schedule.status = 'Active'
      AND gd_template.status = 'Active'
      AND gd_scheduledJob.status = 'Pending'";
  $stmt = $pdo->prepare($sql);
  // $stmt->bindValue(1, $id);
  $stmt->execute();
  $data = $stmt->fetch();

  if(isset($_POST['tid'])) {  // If request comes from campainer
    echo json_encode($data);
  } else {
    return $data;
  }

// accessId
// additionalParam
// cid
// client_id
// clientEmail
// clientName
// company
// contactId
// dateCreated
// dateRegistered
// dateUpdated
// email
// emailSign
// id
// msgBody
// name
// parentId
// phone
// postTime
// pwd
// scheduleGroupId
// sgid
// sid
// status
// statusDescription
// subject
// templateId
// templateName
// tid
// timeToSend
// title
