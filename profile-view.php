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
                        <h1 class="page-header">View Profile</h1>
                    </div>
                </div>
                <?php
                  $data = require_once 'api/GET/profile.php';
                ?>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    Details
                  </div>
                  <div class="panel-body">
                    <p><em>Name:</em>  <?php echo $data['clientName'] ?></p>
                    <p><em>Email:</em>  <?php echo $data['clientEmail'] ?></p>
                    <p><em>Access Token: </em>  <?php echo $data['accessId'] ?></p>
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
