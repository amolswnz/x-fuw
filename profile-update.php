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
                <div class="col-lg-12">
                    <?php include 'inc-messages.php'; ?>
                </div>
                <form id="frm">
                  <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="clientName" value="<?php echo $data['clientName']?>">
                  </div>
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" id="email" name="clientEmail" value="<?php echo $data['clientEmail']?>" readonly>
                  </div>
                  <div class="form-group">
                    <label for="accessId">Access Token</label>
                    <input type="text" class="form-control" id="accessId" name="accessId" value="<?php echo $data['accessId']?>">
                  </div>
                  <div class="form-group">
                    <label for="pwd">Password</label><br>
                    <a href="#">Change password</a>
                  </div>
                  <br>
                  <div class="form-group" id="formActions">
                    <label for="actions"></label>
                    <button type="submit" class="btn btn-primary" id="finished">Update</button>
                  </div>
                </form>

            </div>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
<?php require_once 'inc-footer.php'; ?>
<script type="text/javascript">
  $("#frm").submit(function(event) {
    event.preventDefault();
    $.ajax({
      url: 'api/PUT/profile.php',
      type: 'POST',
      dataType: 'json',
      data: $("#frm").serializeArray()
    })
    .done(function(done) {
        if(done.success) {
          window.location.search += 'success=1' ;
        } else {
          window.location.search += 'fail=1' ;
        }
    })
  });
</script>
</body>
</html>
