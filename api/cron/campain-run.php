<?php
require_once __DIR__ . '/../connect-inc.php';

$sql = "SELECT *
        FROM gd_scheduledJob
            JOIN gd_client ON gd_scheduledJob.client_id = gd_client.id
            JOIN gd_scheduleGroup ON gd_scheduledJob.scheduleGroupId = gd_scheduleGroup.sgid
            JOIN gd_contact ON gd_scheduledJob.contactId = gd_contact.cid
            JOIN gd_schedule ON gd_scheduleGroup.parentId = gd_schedule.sid
            JOIN gd_template ON gd_scheduleGroup.templateId = gd_template.tid
        WHERE postTime < NOW()
            AND gd_client.status = ?
            AND gd_scheduleGroup.status = ?
            AND gd_contact.status = ?
            AND gd_schedule.status = ?
            AND gd_template.status = ?
            AND gd_scheduledJob.status = ?";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(1, 'Active');
$stmt->bindValue(2, 'Active');
$stmt->bindValue(3, 'Active');
$stmt->bindValue(4, 'Active');
$stmt->bindValue(5, 'Active');
$stmt->bindValue(6, 'Pending');
$stmt->execute();

$results = $stmt->fetchAll();

foreach ($results as $row) {
    $from = $row['clientEmail'];
    $to = $row['email'];
    $additionalParam = json_decode($row['additionalParam'], true);
    $postTime = new DateTime($row['postTime']);

    if ($additionalParam['reminderText']) {
        $subject = "Reminder Message from you to you";

        $headers = "From: " . $to . "\r\n";
        $headers .= "Reply-To: " . $to . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $message = "<html><body>";
        $message .= "<h3>Hi " . $row['clientName'] . "</h3>";
        $message .= "<h4>This is your reminder email genereated by you.</h4>";
        $message .= "<hr>";
        $message .= "<p>" . $additionalParam['reminderText'] . "</p>";
        $message .= "</body></html>";
        mail($to, $subject, $message, $headers);
    }

    if ($additionalParam['sendEmail']) {
        $emailID = md5($to);
        $someRandomData = md5($emailID);
        $moreRandomData = md5($someRandomData);
        $unSubscribeLink = "http://wizard.graphicdetail.co.nz/followup-wizard/unsubscribe.php?user=$someRandomData&ref=$emailID&token=$moreRandomData&d=$row[client_id]&email=$to";

        $subject = $row['subject'];

        $headers = "From: $from \r\n";
        $headers .= "Reply-To: $to\r\n";

        if ($additionalParam['sendCopy']) {
            $headers .= "CC: $from\r\n";
        }

        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $message = "<html><body>";
        $message .= "<h3>Hi " . $row['name'] . "</h3>";
        $message .= "<p>" . $row['msgBody'] . "</p>";
        $message .= "--- <br> From,";
        $message .= "<p>" . $row['emailSign'] . "</p>";
        $message .= "<p style='text-align: center'> Dont like the emails <a href='$unSubscribeLink'>Unsubscribe here</a></p>";
        $message .= "</body></html> <hr>";

        if (mail($to, $subject, $message, $headers)) {
            $updateStatus="Completed";
            $updateMessage="The email was delivered to the client on " . $postTime->format("M-d H:i:s");
        } else {        // Error sending email occured
        $updateStatus="Error";
            $updateMessage="An unexpected error occured delivering the email status code #$row[contactId]a$row[client_id]w$row[scheduleGroupId]";
        }

        // Update the gd_scheduleGroup database
        try {
            $sql = "UPDATE gd_scheduledJob SET status=?, statusDescription=?
                        WHERE contactId=? AND client_id=? AND scheduleGroupId=?";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $updateStatus);
            $stmt->bindValue(2, $updateMessage);
            $stmt->bindValue(3, $row['contactId']);
            $stmt->bindValue(4, $row['client_id']);
            $stmt->bindValue(5, $row['scheduleGroupId']);
            $stmt->execute();
        } catch (PDOException $e) {
            $error[] = $e->getMessage()
                            . '= Additional Message - Client-ID: ' . $row['client_id']
                            . ' Contact-ID: ' . $row['contactId']
                            . ' Schedule-Group-ID: ' . $row['scheduleGroupId']
                            . ' Datetime of error ' . time();
        }
    }
}


if (isset($error)) {
    $err = "INSERT INTO gd_error(client_id, generator, errorText) VALUE (?, ?,?)";
    $stmt = $pdo->prepare($err);
    $errorStr = implode(",", $error);
    $stmt->bindValue(1, 0);
    $stmt->bindValue(2, "campain-run");
    $stmt->bindValue(3, $errorStr);
    $stmt->execute();
} else {
    $now = new DateTime();
    $err = "INSERT INTO gd_error(client_id, generator, errorText) VALUE (?, ?,?)";
    $stmt = $pdo->prepare($err);
    $errorStr = implode(",", $error);
    $stmt->bindValue(1, 0);
    $stmt->bindValue(2, "campain-run-log");
    $stmt->bindValue(3, "NO ERROR - Campaign last run at " . $now->format('d-m-y H:i:s'));
    $stmt->execute();
}
