<?php if(isset($_GET['success'])) { ?>
    <div class='alert alert-success' role='alert'>
        Success! The last operation completed successfully.
    </div>
<?php } else if(isset($_GET['fail'])){ ?>
    <div class='alert alert-danger' role='alert'>
        ERROR! Please check error log for details.
    </div>
<?php } ?>
