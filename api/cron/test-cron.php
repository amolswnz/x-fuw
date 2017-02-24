<?php
require_once __DIR__ . '/../connect-inc.php';

$d = new DateTime();

$error[] = "test1- " . $d->format('H:i:s T');
$error[] = "test2- " . $d->format('H:i:s T');
$error[] = "test3- " . $d->format('H:i:s T');

$err = "INSERT INTO gd_error(client_id, generator, errorText) VALUE (?, ?,?)";
$stmt = $pdo->prepare($err);
$errorStr = implode(",", $error);
$stmt->bindValue(1, 0);
$stmt->bindValue(2, "test-cron");
$stmt->bindValue(3, $errorStr);
$stmt->execute();
