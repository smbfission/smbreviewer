<?php
ob_start();
include_once("header.php");
include_once("sidebar.php");

if ($_SESSION['type']!=1) {
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: /campaign/");
  exit();
}



if (isset($_REQUEST['submit'])) {
    $db->updateGeneralSettings($_POST);
    $_SESSION['msg'] = "Settings saved successfully";
}

$db->initGeneralSettings();

$settings = $db->getGeneralSettings();

?>

<section role="main" class="content-body">
					<header class="page-header">
						<h2>General Settings</h2>

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
                                                General Settings
                                            </h2>
            								<p class="text-muted font-13 m-b-15">
                                                <!-- Description Goes Here <br /> -->
                                                &nbsp;
                                            </p>
            							</div>

                                        <?php
                                        if (isset($_SESSION['msg']) && $_SESSION['msg']!="") {
                                            ?>
                                            <div class="col-sm-12">
                                                <div class="alert alert-info"><?php echo $_SESSION['msg']; ?></div>
                                            </div>
                                            <?php
                                            unset($_SESSION['msg']);
                                        }
                                        ?>
                                    </div>


                                    <form action="" method="post" class="form-horizontal form-bordered">

                                        <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault">Privacy Policy URL: </label>
											<div class="col-md-6">
                                                <input class="form-control" name="privacy_policy_url" id="privacy_policy_url" value="<?php echo $settings['privacy_policy_url']; ?>" type="url" required/>
                                                <span style="font-size: 12px;" class="text-info">URL must be valid including http:// or https://</span>
											</div>
										</div>

                                        <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault">Terms and Conditions URL: </label>
											<div class="col-md-6">
                                                <input class="form-control" name="terms_conditions_url" id="v" value="<?php echo $settings['terms_conditions_url']; ?>" type="url" required/>
                                                  <span style="font-size: 12px;" class="text-info">URL must be valid including http:// or https://</span>
											</div>
										</div>

                    <div class="form-group">
                      <label class="col-md-3 control-label" for="inputDefault">Default Facebook app id: </label>
                      <div class="col-md-6">
                          <input class="form-control" name="default_fb_app_id" value="<?= $settings['default_fb_app_id']; ?>" />

                      </div>
                      <div class="clearfix"><br><br></div>
                      <label class="col-md-3 control-label" for="inputDefault">Default Facebook app secret: </label>
                      <div class="col-md-6">
                          <input class="form-control" name="default_fb_app_secret"  value="<?= $settings['default_fb_app_secret']; ?>" />

                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-md-3 control-label" for="inputDefault">Facebook app id for verify: </label>
                      <div class="col-md-6">
                          <input class="form-control" name="default_fb_verify_app_id" value="<?= $settings['default_fb_verify_app_id']; ?>" />

                      </div>
                      <div class="clearfix"><br><br></div>
                      <label class="col-md-3 control-label" for="inputDefault">Facebook app secret for verify: </label>
                      <div class="col-md-6">
                          <input class="form-control" name="default_fb_verify_app_secret"  value="<?= $settings['default_fb_verify_app_secret']; ?>" />

                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-md-3 control-label" for="inputDefault">Default Google Project Key: </label>
                      <div class="col-md-6">
                          <input class="form-control" name="default_google_key" value="<?= $settings['default_google_key']; ?>" />

                      </div>

                    </div>


                    <div class="form-group">
                      <label class="col-md-3 control-label" for="inputDefault">Default Google Client ID: </label>
                      <div class="col-md-6">
                          <input class="form-control" name="default_google_client_id" value="<?= $settings['default_google_client_id']; ?>" />

                      </div>

                    </div>


                    <div class="form-group">
                      <label class="col-md-3 control-label" for="inputDefault">Default Google Client secret : </label>
                      <div class="col-md-6">
                          <input class="form-control" name="default_google_client_secret" value="<?= $settings['default_google_client_secret']; ?>" />

                      </div>

                    </div>



                    <div class="form-group">
                      <label class="col-md-3 control-label" for="inputDefault">Default Yelp API key: </label>
                      <div class="col-md-6">
                          <input class="form-control" name="default_yelp_api_key" value="<?= $settings['default_yelp_api_key']; ?>" />
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-md-3 control-label" for="inputDefault">Default Plan:</label>
                      <div class="col-md-6">
                          <select class="form-control" name="default_plan_id">
                              <option value="0">No default plan</option>

                              <?php foreach ($db->getAllPlans() as $plan): ?>

                                <option <?= ($plan['id']==$settings['default_plan_id'] ? "selected" : "" ) ?> value="<?=$plan['id']?>"><?=$plan['title']?></option>

                              <?php endforeach; ?>

                            </select>
                      </div>
                    </div>


    									<div class="row">
    									    <div class="col-md-3" ></div>
    										<div class="col-md-9">
    											<button type="submit" name="submit" value="submit"  class="btn  btn-fill btn-success "><span class="ace-icon fa fa-save bigger-120"></span> Save</button>
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
