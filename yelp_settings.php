<?php
include_once("header.php");
include_once("sidebar.php");


//
// if(isset($_REQUEST['submit']) && isset($_REQUEST['yelp_api_key']) && trim($_REQUEST['yelp_api_key'])==''){
//   $_SESSION['msg_type']='danger';
//   $_SESSION['msg'] = "Yelp API Key cannot be blank";
// }
//
// else

if (isset($_REQUEST['submit']) && isset($_REQUEST['yelp_api_key'])) {
  $_SESSION['msg_type'] = 'success';

  if ($db->checkPlanExpired($_SESSION['user_id'])) {

    $db->setActivePlan([
      'plan_id' => $db->getProfilePlans($_SESSION['user_id'])[0]['id'],
      'user_id' => $_SESSION['user_id']
    ]);
    $db->updateUserPlanParams([
      'plan_id' => $db->getProfilePlans($_SESSION['user_id'])[0]['id'],
      'user_id' => $_SESSION['user_id']
    ]);
  }

  $sql = "UPDATE `user` set  `yelp_api_key` = case when `advanced_settings`=1 then  '" . $_REQUEST['yelp_api_key'] . "' else NULL end where `id` = '" . $_SESSION['user_id'] . "'";
  mysqli_query($conn, $sql);
  $_SESSION['msg'] = "Yelp API Key is saved";
} else if (isset($_REQUEST['reset']) && $_REQUEST['reset'] == 'reset') {
  $_SESSION['msg_type'] = 'info';
  $sql = "UPDATE `user` SET `yelp_api_key` = NULL where `id` = '" . $_SESSION['user_id'] . "'";
  mysqli_query($conn, $sql);
  $_SESSION['msg'] = "Yelp API Key is restored to default";
}


$row = $db->getUserProfile($_SESSION['user_id']);

?>

<section role="main" class="content-body">
  <header class="page-header">
    <h2>Yelp Settings</h2>

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
                Yelp App Credentials
              </h2>
              <p class="text-muted font-13 m-b-15">
                <!-- Description Goes Here <br /> -->
                &nbsp;
              </p>
            </div>

            <?php
            if (!isset($_SESSION['msg_type'])) {
              $_SESSION['msg_type'] = 'info';
            }
            if (isset($_SESSION['msg']) && $_SESSION['msg'] != "") {
            ?>
              <div class="col-sm-12">
                <div class="alert alert-<?= $_SESSION['msg_type'] ?>"><?= $_SESSION['msg'] ?></div>
              </div>
            <?php
              unset($_SESSION['msg']);
            }
            ?>
          </div>


          <form action="" method="post" class="form-horizontal form-bordered">
            <?php if ((int)$row['advanced_settings'] == 0) : ?>
              <div class="d-none hidden">

              <?php endif; ?>
              <div class="form-group">
                <label class="col-md-3 control-label" for="inputDefault">Yelp API Key: </label>
                <div class="col-md-6">
                  <input class="form-control" name="yelp_api_key" id="yelp_api_key" value="<?php echo @$row['yelp_api_key']; ?>" type="text" placeholder="<?= ((@$row['use_default_credentials'] == 1) ? "default value" : "enter yelp API key") ?>" />
                </div>
              </div>

              <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-9">
                  <button type="submit" name="submit" value="submit" class="btn  btn-fill btn-success "><span class="ace-icon fa fa-save bigger-120"></span> Save</button>
                  <button type="submit" name="reset" value="reset" class="btn  btn-fill btn-danger "><span class="ace-icon fa fa-refresh bigger-120"></span> Reset</button>
                </div>
              </div>
              <?php if ((int)$row['advanced_settings'] == 0) : ?>
              </div>
              <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                  <div class="btn bg-success ">
                    Default api key value is used
                  </div>
                  <!-- <button type="submit" name="submit" value="submit"  class="btn  btn-fill btn-success "><span class="ace-icon fa fa-refresh bigger-120"></span> Connect app</button> -->
                </div>
              </div>

            <?php endif; ?>
          </form>

          <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-9">
              <br />
              <h5>Need Help? <a href="/tutorial/" target="_blank">Click</a> to see tutorials that will help you to embed your Yelp reviews.</h5>
            </div>
          </div>

        </div>
      </section>
    </div>
  </div>
  <!-- end: page -->
</section>
</div>

</section>

<!-- Vendor -->
<script src="/assets/vendor/jquery/jquery.js"></script>
<script src="/assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
<script src="/assets/vendor/bootstrap/js/bootstrap.js"></script>
<script src="/assets/vendor/nanoscroller/nanoscroller.js"></script>
<script src="/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="/assets/vendor/magnific-popup/magnific-popup.js"></script>
<script src="/assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>

<!-- Specific Page Vendor -->
<script src="/assets/vendor/jquery-autosize/jquery.autosize.js"></script>
<script src="/assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>

<!-- Theme Base, Components and Settings -->
<script src="/assets/javascripts/theme.js"></script>

<!-- Theme Custom -->
<script src="/assets/javascripts/theme.custom.js"></script>

<!-- Theme Initialization Files -->
<script src="/assets/javascripts/theme.init.js"></script>

</body>

</html>