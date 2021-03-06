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
                      <h1 class="page-header">Create New Signature</h1>
                      <h4>This email signature can automatically added to each email/event.</h4>
                    </div>
                </div>
                <div class="col-lg-12">
                    <?php include 'inc-messages.php'; ?>
                </div>
                <form id="frm">
                  <div id="editor" name="test">
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
<link rel="stylesheet" href="https://cdn.quilljs.com/1.0.0/quill.snow.css">
<script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>
<style media="screen"> #editor { height: 200px; width: 100%; } </style>
<script type="text/javascript">
  var quill = new Quill('#editor', {
          modules: { toolbar: [
                        [ { header: [1, 2, 3, 4, 5, 6, false] }],
                        [ 'bold', 'italic', 'underline'], ['link', 'image'] ] },
                    bounds: document.body,
                    theme: 'snow' });
  $("#frm").submit(function(event) {
    event.preventDefault();
    $.ajax({
      url: 'api/POST/sign.php',
      type: 'POST',
      dataType: 'json',
      data: { sign: quill.root.innerHTML }
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
