<?php
    require_once 'api/connect-inc.php';
    $sql = "SELECT * FROM gd_contact WHERE client_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $_GET['d']);
    $stmt->execute();
    $results = $stmt->fetchAll();
    // var_dump($results);
    $contactId = 0;
    foreach ($results as $row) {
        if($_GET['ref'] == md5($row['email']))
            $contactId = $row['cid'];
    }
    if($contactId) {
        $sql = "UPDATE gd_contact SET status=? WHERE cid = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, 'Inactive');
        $stmt->bindValue(2, $contactId);
        $stmt->execute();
        echo "<h1> Your $_GET[email] address has been removed from our records</h1>";
    } else {
        echo "<h1>Sorry, No record for $_GET[email] not found </h1>";
    }
