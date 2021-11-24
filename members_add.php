<?php
ob_start();
include_once("header.php");
include_once("sidebar.php");

if ($_SESSION['type'] != 1) {
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: /campaign/");
  exit();
}

?>

<link rel="stylesheet" href="/assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.css" />
<section role="main" class="content-body">
  <header class="page-header">
    <h2>Members Add / Edit</h2>

    <div class="right-wrapper pull-right" style="display:none;">
      <ol class="breadcrumbs">
        <li>
          <a href="index.html">
            <i class="fa fa-home"></i>
          </a>
        </li>
        <li><span>Tables</span></li>
        <li><span>Advanced</span></li>
      </ol>

      <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
  </header>

  <div class="row">
    <div class="col-lg-12">
      <section class="panel">
        <div class="panel-body">

          <div class="row">
            <div class="col-sm-12">
              <h2 class="h2 mt-none mb-sm text-dark text-bold">
                Members
              </h2>
              <p class="text-muted font-13 m-b-15">
                <!-- Description Goes Here <br /> -->
                &nbsp;
              </p>
            </div>


            <?php
            if (isset($_SESSION['msg']) && $_SESSION['msg'] != "") {
            ?>
              <div class="col-sm-12">
                <div class="alert alert-info"><?php echo $_SESSION['msg']; ?></div>
              </div>
            <?php
              unset($_SESSION['msg']);
            }
            ?>
          </div>


          <?php

          $sql = "SELECT * FROM `user` where id = '" . @$_REQUEST['id'] . "'";
          $result = $conn->query($sql);
          $row = $result->fetch_assoc();

          $cur_user_plan = $db->getLastUserPlan(@$_REQUEST['id'])['plan_id'];
          ?>



          <form action="/action.php?action=member" class="form-horizontal form-bordered" method="post">

            <div class="form-group">
              <label class="col-md-3 control-label" for="inputDefault">Name</label>
              <div class="col-md-6">
                <input class="form-control" name="name" value="<?php echo @$row['name'] ?>" type="text">
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-3 control-label" for="inputDefault">Email</label>
              <div class="col-md-6">
                <input class="form-control" name="email" value="<?php echo @$row['email'] ?>" type="email">
              </div>
            </div>


            <div class="form-group">
              <label class="col-md-3 control-label" for="inputDefault">Phone</label>
              <div class="col-md-6">
                <input class="form-control" name="phone" value="<?php echo @$row['phone'] ?>" type="phone">
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-3 control-label" for="inputDefault">Choose Plan</label>
              <div class="col-md-4">
                <select class="form-control" id="plan" name="plan">

                  <option value="0" disabled>No plans available</option>

                  <?php foreach ($db->getAllPlans() as $plan) : ?>

                    <option <?= ($plan['id'] == $cur_user_plan  ? "selected" : "") ?> <?= ((int)$plan['active'] == 0 ? " disabled=\"true\"" : "") ?> value="<?= $plan['id'] ?>"><?= $plan['title'] ?></option>

                  <?php endforeach; ?>

                </select>
              </div>
              <div class="col-md-2">
                <a class="btn btn-info" data-toggle="collapse" href="#plans_history" role="button" aria-expanded="false" aria-controls="plans_history">
                  Show history
                </a>
              </div>
              <div class="clearfix">

              </div>
              <div class="collapse" id="plans_history">
                <div class="col-md-1">
                </div>
                <div class="col-md-8">

                  <table class="table" id="plans_history_table">
                    <thead>
                      <tr>
                        <th scope="col">Creation date</th>
                        <th scope="col">Plan</th>
                        <th scope="col">Date start</th>
                        <th scope="col">Date end</th>

                      </tr>
                    </thead>
                    <tbody>


                      <?php $p = 0;
                      $user_plans = $db->getUserPlans($row['id']);
                      ?>
                      <?php if (@count($user_plans) > 0) : ?>
                        <?php foreach ($user_plans as $user_plan) : ?>
                          <?php $p++; ?>
                          <tr class="<?= ((round((strtotime($user_plan['date_stop']) - time()) / (60 * 60 * 24)) > 0 && $p == 1)  ? "success" : "text-danger text-striked"); ?>" id="history<?= $user_plan['id'] ?>">
                            <td>
                              <?= date('Y-m-d H:i:s', strtotime($user_plan['creation_date'] . ' + ' . (int)$_SESSION['tzo'] . ' minutes')) ?>
                            </td>
                            <td>
                              <?= $user_plan['title'] ?>
                            </td>
                            <td class="date_start">
                              <span>
                                <?= date('Y-m-d', strtotime($user_plan['date_start'])) ?>
                              </span>
                            </td>
                            <td class="date_stop">
                              <span><?= date('Y-m-d', strtotime($user_plan['date_stop'])) ?></span>
                              <?= ($p == 1 ? '<i data-toggle="modal" data-target="#change_dates" role="button"  class="fa fa-calendar"></i>' : '') ?>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-3 control-label" for="inputDefault">Status</label>
              <div class="col-md-4">
                <select class="form-control" name="status">
                  <option <?= (int)$row['status'] == 0 ? 'selected' : '' ?> value="0">Blocked</option>
                  <option <?= (int)$row['status'] == 1 ? 'selected' : '' ?> value="1">Active</option>
                  <option <?= (int)$row['status'] == 2 ? 'selected' : '' ?> value="2">Pending</option>
                </select>
              </div>
              <div class="clearfix">
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-3 control-label" for="inputDefault">Type</label>
              <div class="col-md-4">
                <select class="form-control" name="type">
                  <option <?= (isset($row['type']) && (int)$row['type'] == 1) ? 'selected' : '' ?> value="1">Administrator</option>
                  <option <?= (isset($row['type']) && (int)$row['type'] == 2) ? 'selected' : '' ?> value="2">Member</option>
                  <option <?= (isset($row['type']) && (int)$row['type'] == 3) ? 'selected' : '' ?> value="3">Editor</option>
                </select>
              </div>
              <div class="clearfix">
              </div>
            </div>

            <div class="form-group">
              <label class="col-md-3 control-label" for="inputDefault"></label>
              <div class="col-md-6">
                <input type="hidden" name="id" value="<?php echo @$row['id']; ?>">
                <button type="submit" name="submit" value="submit" class="btn  btn-fill btn-success "><span class="ace-icon fa fa-save bigger-120"></span> Save</button>
              </div>
            </div>

          </form>
        </div>
      </section>
    </div>
  </div>
  <!-- end: page -->
</section>
</div>
</section>


<div class="modal fade" id="change_dates" tabindex="-1" role="dialog" aria-labelledby="change_dates_label" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="change_dates_label">Edit plan period</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form>
        <div class="modal-body">
          <div class="alert" style="display:none;" role="alert"></div>
          <div class="form-group">
            <label class="col-form-label">Date start:</label>
            <input type="text" autocomplete="off" class="form-control" name="date_start">
          </div>
          <div class="form-group">
            <label class="col-form-label">Date stop:</label>
            <input type="text" autocomplete="off" class="form-control" name="date_stop">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Vendor -->
<script src="/assets/vendor/jquery/jquery.js"></script>
<script src="/assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
<script src="/assets/vendor/bootstrap/js/bootstrap.js"></script>
<script src="/assets/vendor/nanoscroller/nanoscroller.js"></script>
<script src="/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="/assets/vendor/magnific-popup/magnific-popup.js"></script>
<script src="/assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>

<!-- Specific Page Vendor -->
<!-- <script src="/assets/vendor/select2/select2.js"></script> -->
<script src="/assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>
<!-- <script src="/assets/vendor/jquery-datatables/extras/TableTools/js/dataTables.tableTools.min.js"></script> -->
<script src="/assets/vendor/jquery-datatables-bs3/assets/js/datatables.js"></script>

<!-- Theme Base, Components and Settings -->
<script src="/assets/javascripts/theme.js"></script>

<!-- Theme Custom -->
<script src="/assets/javascripts/theme.custom.js"></script>

<!-- Theme Initialization Files -->
<script src="/assets/javascripts/theme.init.js"></script>

<script>
  $('#plans_history_table').dataTable({
    "language": {
      "emptyTable": "User have no plans yet"
    },
    "bLengthChange": false,
    "searching": false,
    "paging": true,
    "pageLength": 5,
    "ordering": false,
    "info": false,
    fnDrawCallback: function(oSettings) {
      var totalPages = this.api().page.info().pages;
      if (totalPages == 1) {
        jQuery('.dataTables_paginate').hide();
      } else {
        jQuery('.dataTables_paginate').show();
      }
    }
  });

  $(document).on('shown.bs.modal', '#change_dates', function() {
    var date_start = Date.parse($('.date_start span').first().text().trim());
    var date_stop = Date.parse($('.date_stop span').first().text().trim());
    $(this).find('input').datepicker({
      format: "yyyy-mm-dd"
    }).on('changeDate', function(e) {
      $(this).datepicker('hide');
    });

    $(this).find('input[name="date_start"]').datepicker("setDate", new Date(date_start));
    $(this).find('input[name="date_stop"]').datepicker("setDate", new Date(date_stop));
  });

  $("#change_dates form").submit(function(e) {
    e.preventDefault();

    alert = $(this).find('.alert');
    alert.hide();
    alert.removeClass('alert-danger');
    alert.removeClass('alert-success');
    alert.html('');

    var d = $(this).serializeArray(),
      id = $('#plans_history_table tbody tr').first().attr('id');

    d.push({
      name: 'id',
      value: id
    });
    $.post("/action.php?action=update_user_plan_dates", d, function(data) {
      if (data.success) {
        if (data.message && data.message != '') {
          alert.addClass('alert-success');
          alert.html(data.message);
          alert.show();
          $('#plans_history_table tbody tr .date_start span').first().html(d[0].value);
          $('#plans_history_table tbody tr .date_stop span').first().html(d[1].value);

          if (
            Date.parse($('#plans_history_table tbody tr .date_stop span').first().text()) >= Date.now()
          ) {
            $('#plans_history_table tbody tr').first().removeClass("text-danger text-striked").addClass("success");
          } else {
            $('#plans_history_table tbody tr').first().removeClass("success").addClass("text-danger text-striked");
          }
        }
      } else {

        if (data.message && data.message != '') {
          alert.addClass('alert-danger');
          alert.html(data.message);
          alert.show();
        }
      }
    }, "json");
  });
</script>
</body>

</html>