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
                        <h1 class="page-header">View Signature</h1>
                    </div>
                 </div>
                 <?php
                   $data = require_once 'api/GET/profile.php';
                 ?>
                 <div class="panel panel-default">
                   <div class="panel-body">
                     <?php echo $data['emailSign'];  ?>
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
