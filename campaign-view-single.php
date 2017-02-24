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
                        <h1 class="page-header">View Campaign</h1>
                    </div>
                </div>
                <?php
                  $scheduleGroupIdid = $_GET['sgid'];
                  $contactId = $_GET['cid'];
                  $data = require_once 'api/GET/campaign-single.php';
                ?>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <H4>Campaign Title: <?php echo $data['title']?></H4>
                  </div>
                  <div class="panel-body">
                    <?php
                      foreach ($data as $key => $value) {
                        echo "<em>$key</em> : $value ";
                        echo "<hr>";
                      }
                     ?>
                  </div>
                </div>

            </div>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
<?php require_once 'inc-footer.php'; ?>
</body>
</html>
