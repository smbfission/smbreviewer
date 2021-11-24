<?php
include_once("header.php");
include_once("sidebar.php");


if ($_SESSION['type']!=1) {
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: /campaign.php");
  exit();
}

if(isset($_REQUEST['business_email']) ){

    $db->updateGeneralSettings($_POST);
    $_SESSION['msg'] = "Paypal Business Email is saved";
}

$row = $db->getGeneralSettings();

?>

<section role="main" class="content-body">
					<header class="page-header">
						<h2>Paypal Settings</h2>

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
                                                Paypal Credentials
                                            </h2>
            								<p class="text-muted font-13 m-b-15">
                                                <!-- Description Goes Here <br /> -->
                                                &nbsp;
                                            </p>
            							</div>

                                        <?php
                                        if(isset($_SESSION['msg']) && $_SESSION['msg']!=""){
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
											<label class="col-md-3 control-label" for="inputDefault">Business Email: </label>
											<div class="col-md-6">
                                                <input class="form-control" name="business_email" id="business_email" value="<?php echo $row['business_email']; ?>" type="text" required/>
											</div>
										</div>

    									<div class="row">
    									    <div class="col-md-3" ></div>
    										<div class="col-md-9">
    											<button type="submit" name="submit" value="submit"  class="btn btn-fill btn-success "><span class="ace-icon fa fa-save bigger-120"></span> Save</button>
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
