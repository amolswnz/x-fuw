<?php require_once 'inc-header.php' ?>
<style media="screen">
  .preview {
    margin: 20px;
    padding: 10px;
    border: 1px solid #ccc;
  }
</style>
<body>
<div id="wrapper">
  <!-- Navigation -->
  <?php require_once 'inc-nav.php'; ?>
  <!-- Page Content -->
  <div id="page-wrapper">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Create Campaign</h1>
          <div class="panel-body">
            <div class="row">
              <div class="col-lg-9">
                  <?php include 'inc-messages.php'; ?>
                <form id="frm">
                  <div class="form-group">
                    <label for="title">Campaign Title</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Schedule title">
                  </div>
                  <div class="repeatingSection">
                    <hr>
                    <a href="#" style="display:none" class="btn btn-danger deleteAction">Delete this Action</a>
                    <div class="form-group">
                      <label for="textVersion_1">Select timeframes</label>
                      <div class="row">
                        <div class="col-xs-6">
                          <input type="text" class="form-control" id="textVersion_1" name="textVersion_1" placeholder="Timeframe to send">
                          <p class="help-block">eg. +2 days, +1 hour 30 minutes, +1 week</p>
                        </div>
                        <div class="col-xs-1">
                            OR
                        </div>
                        <div class="col-xs-5">
                          <input type="datetime-local" class="form-control" id="dateVersion_1" name="dateVersion_1" placeholder="datetime">
                          <p class="help-block">Set custom date and time</p>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="template_1">Select Template</label>
                      <select class="form-control" id="template_1" name="templateId_1" required>
                        <option disabled selected value>- Please select one -</option>
                        <?php
                          $results = require_once 'api/GET/template-all.php';
                          foreach ($results as $row) {
                              echo "<option value='{$row['tid']}'>{$row['templateName']}</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <h4>Preview</h4>
                    <div class="preview" id="templatePreview_1">
                      <h5 id="subject_1"></h5>
                      <p id="msgBody_1"></p>
                    </div>
                    <div id="additionalActions_1">
                      <div class="form-group">
                        <label class="checkbox-inline">
                          <input type="checkbox" id="sendEmail_1" name="sendEmail_1" value="true" checked> Send email to client
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" id="sendCopy_1" name="sendCopy_1" value="true"> Send email copy to me
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" id="sendReminder_1" name="sendReminder_1" value="true"> Remind me to do this
                        </label>
                      </div>
                      <div class="form-group" id="reminderTextDiv_1">
                        <label for="reminderText_1">Compose your reminder email</label>
                        <textarea class="form-control" rows="13" id="reminderText_1" name="reminderText_1"></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="form-group" id="formActions">
                    <label for="actions"></label>
                    <a href="#" class="btn btn-info addAction">Add Action</a>
                    <button type="submit" class="btn btn-primary" id="finished">Finish</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /#page-wrapper -->
</div>
    <!-- /#wrapper -->
<?php require_once 'inc-footer.php'; ?>
<script type="text/javascript">
  $(document).ready(function() {
    // Initially reminder textarea div is hidden unless clicked on remind me checkbox
    $("#reminderTextDiv_1").hide();
    $("#templatePreview_1").hide();
  });

  // Create new set of form inputs, if additional action is added
  $(".addAction").click(function(event) {
    var currentCount =  $('.repeatingSection').length;
    var newCount = currentCount+1;
    var repeatingFormGroup = $('.repeatingSection').last();
    // Create copy of repeatingFormGroup
    var newSection = repeatingFormGroup.clone();
    // Insert this into the DOM
    newSection.insertAfter(repeatingFormGroup).hide().show('slow');
    $("html, body").animate({ scrollTop: $(document).height() }, "slow");

    var currentElements = "[id*=_" + currentCount + "]";
    console.log(currentElements);
    newSection.find(currentElements).each(function(index, element) {
      // First check if name is present in the attribute or not
      if(element.name!=null) {
        // Change to new name
        $(element).attr('name', element.name.replace("_" + currentCount, "_" + newCount));
      }
      // Change the id - it should be last step
      $(element).attr('id', element.id.replace("_" + currentCount, "_" + newCount));
      element.value = '';   // Reset the value of the form field - clone function copies values too
    });

    // Reset the value of the form field - clone function copies values too
    // sendEmail by default needs to be checked
    $("#sendEmail_" + newCount).prop('checked', true);
    $("#sendCopy_" + newCount).prop('checked', false);
    $("#sendReminder_" + newCount).prop('checked', false);

    // Clears any generate template preview
    $("#subject_" + newCount).html('');
    $("#msgBody_" + newCount).html('');
    console.log("Removing has-errr");
    $("#textVersion_" + newCount).parent().removeClass('has-error');

    // Show delete button except for the first form group
    $('.deleteAction').slice(1).show();

    return false;
  });

  // Delete a section
  $(document).on('click','.deleteAction',function() {
    $(this).parent('div').remove();
    return false;
  });

  // Remind me textarea show
  $(document).on('click','[id^=sendReminder]',function() {
    var thisId = $(this).attr('id').match(/\d+$/)[0];
    $("#reminderTextDiv_" +  thisId).slideToggle('slow');
    // When remind me is checked, send to client is unchecked1
    $("#sendEmail_" + thisId).prop('checked', false);
  });

  // Show template preview
  $(document).on('change','[id^=template]',function() {
    var thisId = $(this).attr('id').match(/\d+$/)[0];
    $("#templatePreview_" + thisId).show();
    console.log($(this).val());
    $.ajax({
      url: 'api/GET/template-single.php',
      type: 'POST',
      dataType: 'json',
      data: { tid: $(this).val() }
    })
    .done(function(data) {
      console.log("success",data.subject);
      $("#subject_" + thisId).html(data.subject);
      $("#msgBody_" + thisId).html((data.msgBody).substring(0,100));
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });

  });

  // Track text date element changed event and display errors if any
  $(document).on('change','[id^=textVersion]',function() {
    var currentElement = $(this);
    currentElement.parent().removeClass('has-error');
    $.ajax({
      url: 'api/functions/validateInterval.php',
      type: 'POST',
      dataType: 'json',
      data: { textDate: $(this).val() }
    })
    .done(function(data) {
      if(data.error === undefined) {
        currentElement.parent().removeClass('has-error');
      } else {
        currentElement.parent().addClass('has-error');
      }
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });
  });

  $("#frm").submit(function(event) {
    event.preventDefault();
    console.log($("#frm").serializeArray());
    $.ajax({
      url: 'api/POST/campaign.php',
      type: 'POST',
      dataType: 'json',
      data: $("#frm").serializeArray()
    })
    .done(function(data) {
        if(data.success) {
          window.location.search += 'success=1' ;
        } else {
          window.location.search += 'fail=1' ;
        }
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });

  });
</script>
</body>
</html>
