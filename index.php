<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Follow Up Wizard</title>

    <!-- Bootstrap Core CSS -->
    <link href="theme/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="theme/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="theme/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="theme/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                    <?php if(isset($_GET['err'])) { ?>
                        <div class='alert alert-danger' role='alert'>
                            ERROR! You have been logged out due to security reason. <br>
                            Please login again to continue ...
                        </div>
                    <?php } ?>
                      <form role="form" method="POST" action="login.php" id="frm">
                          <fieldset>
                              <div class="form-group">
                                  <input class="form-control" placeholder="E-mail" name="clientEmail" type="email" autofocus>
                              </div>
                              <div class="form-group">
                                  <input class="form-control" placeholder="Password" name="pwd" type="password" value="">
                              </div>
                              <!-- Change this to a button or input when using this as a form -->
                              <button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
                          </fieldset>
                      </form>
                      <br>
                      <div id="msg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="theme/vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="theme/vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="theme/vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="theme/dist/js/sb-admin-2.js"></script>

</body>
<script type="text/javascript">
  $("#frm").submit(function(event) {
    event.preventDefault();
    $.ajax({
      url: 'api/AUTH/login.php',
      type: 'POST',
      dataType: 'json',
      data: $("#frm").serializeArray()
    })
    .done(function(data) {
      var alertClass, msg;
      if(data.active) {
        alertClass = 'alert alert-success';
        msg = data.active;
        window.location.href = "dashboard.php";
      }
      if(data.inactive) {
        alertClass = 'alert alert-info';
        msg = data.inactive;
      }
      if(data.error) {
        alertClass = 'alert alert-warning';
        msg = data.error;
      }
      if(data.deleted) {
        alertClass = 'alert alert-danger';
        msg = data.deleted;
      }
      $("#msg").html("<div class='" +  alertClass +  "' role='alert'>"  +  msg + "</div>")
        .show().delay(2000).fadeOut();
    });
  });
</script>
</html>
