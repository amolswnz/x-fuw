<?php require_once 'inc-header.php' ?>
<body>
    <div id="wrapper">
        <!-- Navigation -->
        <?php require_once 'inc-nav.php'; ?>
        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Imported email addresses</h1>
                    </div>
                </div>
<?php
  require_once 'api/connect-inc.php';
  require_once 'api/Session.php';

  $session = Session::getInstance();
  $sessionId = $session->id;

// UPLOADING FILE - START
  $validFile = true;
  if($_FILES['csv']['name']) {
  	if(!$_FILES['csv']['error']) {
  		$newFileName = $sessionId . '.csv';         //rename file
  		if($_FILES['csv']['size'] > (10240000)) {   // Not larger than 10 MB
  			$validFile = false;
  			$message['error'] = "Oops!  Your file\'s size is to large.";
  		}
  		if($validFile) {
  			move_uploaded_file($_FILES['csv']['tmp_name'], __DIR__ . '/temp/'.$newFileName);
  			$message['success'] = "Congratulations!  Your file was uploaded.";
  		}
  	}
  	else {       // set that to be the returned message
  		$message['errorWrite'] = "Ooops! Your upload triggered the following error: " . $_FILES['csv']['error'];
  	}
  }
  if(isset($message['error'])) { // Error uploading file
    var_dump($message);
    // The program should break here ---
  }
// UPLOADING FILE - END

$insertData=null;

// PARSING UPLOADED FILE - START
  $count = 0;
  $insertCounter = 0;
  $handle = fopen("temp/$newFileName", "r");
  $errorFile = fopen("temp/please-use-correct-data-$sessionId.csv", "w") or die("Unable to open file!");
  if($handle) {
    while(($line = fgets($handle)) !== false) {
      $count++;
      $line = str_replace(array("\r", "\n"), '', $line);  // Removes new lines characters
      $data = explode(",", $line);

      // 0 name 1 email 2 phone 3 company
      $regName = "/([A-Z])\w+/";
      if (isset($data[0]) && preg_match($regName, $data[0])) {
        $cleanRow = true;
      } else {
        // $error[] = "The first column has missing/incorrect information Name. -----> Skipping $count <-----";
        $cleanRow = false;
      }

      $regEmail = "/^\S+@\S+\.\S+$/";
      if (isset($data[1]) && preg_match($regEmail, $data[1])) {
        $cleanRow = true;
      } else {
        // $error[] = "The second column has missing/incorrect information Email. -----> Skipping $count <-----";
        $cleanRow = false;
      }

      $resultData[$count][0] = $cleanRow;
      $resultData[$count][1] = isset($data[0]) ? $data[0] :  "";
      $resultData[$count][2] = isset($data[1]) ? $data[1] :  "";
      $resultData[$count][3] = isset($data[2]) ? $data[2] :  "";
      $resultData[$count][4] = isset($data[3]) ? $data[3] :  "";

      if($cleanRow) {
        $insertData[$insertCounter]['name'] = isset($data[0]) ? $data[0] : null;
        $insertData[$insertCounter]['email'] = isset($data[1]) ? $data[1] : null;
        $insertData[$insertCounter]['phone'] = isset($data[2]) ? $data[2] : "";
        $insertData[$insertCounter]['company'] = isset($data[3]) ? $data[3] : "";
        $insertData[$insertCounter++]['client_id'] = $sessionId;
      } else {
        fwrite($errorFile, $line . "\r\n");
      }
    }
    fclose($handle);
  } else {
    $message['errorRead'] = "Ooops! Your file read caused an unexpceted error";
  }

  if(isset($error)) {
    $message['textError'] = $error;
    $message['fileLink'] = "<a href='temp/please-use-correct-data-$sessionId.csv' download>Download the error file </a>";    // Download file link
  }
  fclose($errorFile);
// PARSING UPLOADED FILE - END


// INSERTING VALID DATA IN DATABASE - START
  // Get all the fields names from the insertData array

  require_once 'api/POST/user-add.php';
// INSERTING VALID DATA IN DATABASE - END

  if(isset($errorx)) {
    $err = "INSERT INTO gd_error(client_id, generator, errorText) VALUE (?, ?,?)";
    $stmt = $pdo->prepare($err);
    $errorStr = implode(",", $errorx);
    $errorStr .= implode(",", $message);
    $stmt->bindValue(1, $sessionId);
    $stmt->bindValue(2, "import-data-next");
    $stmt->bindValue(3, $errorStr);
    $stmt->execute();
    $message['db'] = "Please check error log for details.";
  }
  if($message) {
                echo "<table class='table table-bordered table-hover'>";
                echo "<thead>
                  <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Company</th>
                  </tr>
                </thead>";
                $count = 0;
                foreach ($resultData as $row) {
                    $count++;
                    echo "<tr class='";
                    if(! $row[0])
                        echo "danger";
                    echo "'>
                    <td>$count</td>
                    <td>$row[1]</td>
                    <td>$row[2]</td>
                    <td>$row[3]</td>
                    <td>$row[4]</td>
                    </tr>";
                }
                echo " </table>";
            if(isset($message['fileLink']))
              echo "<h4>$message[fileLink]</h4>";
} ?>
                <h4><a href="import-contacts-data.php">Import more data</a><h4>
            </div>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
<?php require_once 'inc-footer.php'; ?>
</body>
</html>
