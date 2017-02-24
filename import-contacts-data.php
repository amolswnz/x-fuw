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
                    <h1 class="page-header">Import email addresses</h1>
                 </div>
                </div>
                <div class="row">
                  <div class="col-lg-12">
                    <h3>File format type name, email, phone, company</h3>
                    <form action="import-data-next.php" method="post" enctype="multipart/form-data">
                      <div class="form-group">
                        <label for="file">Upload file</label>
                        <input type="file" class="form-control" name="csv" placeholder="">
                      </div>
                      <div class="form-group" id="formActions">
                        <label for="actions"></label>
                        <button type="submit" class="btn btn-primary" id="finished">Upload</button>
                      </div>
                    </form>
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
