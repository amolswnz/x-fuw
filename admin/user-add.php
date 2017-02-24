<!DOCTYPE html>
<html lang="en">
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
                        <h1 class="page-header">Add User</h1>
                    </div>
                </div>
                <form id="frm">
                    <div id="msg"></div>
                  <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="clientName" >
                  </div>
                  <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="clientEmail" >
                  </div>
                  <div class="form-group">
                    <label for="accessId">Access Token</label>
                    <input type="text" class="form-control" id="accessId" name="accessId" >
                  </div>
                  <div class="form-group">
                    <label for="pwd">Password</label>
                    <input type="password" class="form-control" id="pwd" name="pwd" >
                  </div>
                  <div class="form-group">
                    <label for="cpwd">Confirm Password</label>
                    <input type="password" class="form-control" id="cpwd" >
                  </div>
                  <br>
                  <div class="form-group" id="formActions">
                    <label for="actions"></label>
                    <button type="submit" class="btn btn-primary" id="finished">Add User</button>
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
      url: 'api/POST/add-user.php',
      type: 'POST',
      dataType: 'json',
      data: $("#frm").serializeArray()
    })
    .done(function(data) {
      var alertClass, msg;
      if(data.success) {
        alertClass = 'alert alert-success';
        msg = data.success;
        $('#frm')[0].reset();
      }
      if(data.error) {
        alertClass = 'alert alert-danger';
        msg = data.error;
      }
      $("#msg").html("<div class='" +  alertClass +  "' role='alert'>"  +  msg + "</div>")
        .show().delay(2000).fadeOut();
    });
  });
</script>
</body>
</html>
