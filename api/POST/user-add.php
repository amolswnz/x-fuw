<?php
require_once __DIR__ . '/../connect-inc.php';
require_once __DIR__ . '/../Session.php';
$session = Session::getInstance();
$sessionId = $session->id;

$currentInsertedIds = array();
foreach($insertData as $row) {
    $keys = implode(",", array_keys($row));
    $questionMarks = placeholders($row);
    try {
        $sql = "INSERT INTO gd_contact($keys) VALUES ($questionMarks)
                        ON DUPLICATE KEY UPDATE dateRegistered = NOW()";
        $stmt = $pdo->prepare($sql);
        $count=1;
        foreach ($row as $value) {
            $stmt->bindValue($count++, $value);
        }
        $stmt->execute();
        $currentInsertedIds[] = $pdo->lastInsertId();     // Returns last inserted id
    } catch (PDOException $e) {
        $errorx[] = $e->getMessage();
    }
}

foreach ($currentInsertedIds as $lastInsertId) {
    $sql = "SELECT * FROM gd_scheduleGroup WHERE client_id = ? AND status = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $sessionId);
    $stmt->bindValue(2, 'Active');
    $stmt->execute();
    $allSchedules = $stmt->fetchAll();

    $count = 0;
    foreach ($allSchedules as $schedule) {
        $absolute = DateTime::createFromFormat('Y-m-d H:i:s', $schedule['timeToSend']) ? true : false;

        $regDate = new DateTime();
        $insertArray[$count]['scheduleGroupId'] = $schedule['sgid'];

        if ($absolute) {
            $now = new DateTime();
            $postTime = DateTime::createFromFormat('Y-m-d H:i:s', $schedule['timeToSend']);
            $insertArray[$count]['status'] = 'Pending';
            $insertArray[$count]['statusDescription'] = 'The email schedule is pending for this job.';
            if ($postTime < $now) {
                $insertArray[$count]['status'] = '404';
                $insertArray[$count]['statusDescription'] = 'This user registered after the mail event was passed.';
            }
        } else {
            $newPostTime = $regDate->modify($schedule['timeToSend']);
            $insertArray[$count]['status'] = 'Pending';
            $insertArray[$count]['statusDescription'] = 'The email schedule is pending for this job.';
            $insertArray[$count]['postTime'] = $newPostTime->format('Y-m-d H:i');
        }
        $count++;
    }

    if (! isset($insertArray)) {
        $error[] = "No schedules found when this $lastInsertId id user was added into the database for client $sessionId.";
    } else {
        // Insert all scheduled data into the scheduled job database
        foreach ($insertArray as $row) {
            try {
                $sqlx = "INSERT INTO gd_scheduledJob(contactId, client_id, scheduleGroupId, postTime, status, statusDescription) VALUE (?,?,?,?,?,?)";
                $stmt = $pdo->prepare($sqlx);
                $stmt->bindValue(1, $lastInsertId);
                $stmt->bindValue(2, $sessionId);
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
        $stmt->bindValue(1, $sessionId);
        $stmt->bindValue(2, "add-user");
        $stmt->bindValue(3, $errorStr);
        $stmt->execute();
    }
}
