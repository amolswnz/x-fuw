<?php require_once 'inc-header.php';  ?>
<body>
  <div id="wrapper">
    <!-- Navigation -->
    <?php require_once 'inc-nav.php'; ?>
      <!-- Page Content -->
      <div id="page-wrapper">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <h1 class="page-header">View Templates</h1>
            </div>
            <?php
              $data = require_once 'api/GET/template-all.php';
              $count = 1;
              echo "&emsp;";  // The template does not show up if this line is not included
            ?>
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                      <th>#</th>
                      <th>Template Name</th>
                      <th>Subject</th>
                      <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                foreach($data as $row) {
                  echo "<tr> <td>$count</td>
                        <td><a href='template-view-single.php?id=$row[tid]'> $row[templateName]</a></td>
                        <td>$row[subject]</td> ";
                 ?>
                 <td>
                   <a href="template-update.php?id=<?php echo $row['tid'] ?>" class="btn btn-info">Update</a>
                   <a href="template-delete.php?id=<?php echo $row['tid'] ?>" class="btn btn-danger">Delete</a>
                 </td>
                 </tr>
                 <?php $count++;
                  } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
  </div>
  <?php require_once 'inc-footer.php'; ?>
</body>
</html>
