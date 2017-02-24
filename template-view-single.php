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
                        <h1 class="page-header">View Template</h1>
                    </div>
                </div>
                <?php
                  $id = $_GET['id'];
                  $data = require_once 'api/GET/template-single.php';
                  $profile = require_once 'api/GET/profile.php';
                ?>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    Template Name
                  </div>
                  <div class="panel-body">
                    <?php echo $data['templateName'] ?>
                  </div>
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    Subject
                  </div>
                  <div class="panel-body">
                    <?php echo $data['subject'] ?>
                  </div>
                </div>

                <div class="panel panel-default">
                  <div class="panel-heading">
                    Message Body
                  </div>
                  <div class="panel-body">
                    <?php echo $data['msgBody'] ?>
                    <br>
                    ------------
                    <br>
                    <?php echo $profile['emailSign'];  ?>
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
