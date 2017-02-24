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
                        <h1 class="page-header">Logout</h1>
                        <div class="alert alert-success">
                          <h3>Success: You have been looged out successfully !</h3>
                          <?php require_once 'api/AUTH/logout.php'; ?>
                          <h4>You will be redirected in <span id="count">5</span> second or you can close this tab safely.</h4>
                          <script type="text/javascript">
                            var counter = 5;
                            setInterval(function() {
                              counter--;
                              document.getElementById("count").innerHTML=counter;
                              if(counter == 0) {
                                window.location.href = "index.php";
                              }
                            }, 1000);
                          </script>
                        </div>
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
