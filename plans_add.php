<?php
ob_start();
include_once("header.php");
include_once("sidebar.php");


if ($_SESSION['type']!=1) {
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: /campaign/");
  exit();
}

include_once('core/plugnpaid.php');
$plugnpaid_products= PlugNPaid::apiCall("products/list", "GET", [])['products'];


?>
<link rel="stylesheet" href="/assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.css" />
          <section role="main" class="content-body">
					<header class="page-header">
						<h2>Plans Add / Edit</h2>

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
                                                Plans
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
                                    <?php

                      $row = $db->getPlanById(@$_REQUEST['id']);
        							?>
                      <form action="/action.php?action=plans" method="post" class="form-horizontal form-bordered">

                      <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault">Title</label>
											<div class="col-md-6">
                                                <input class="form-control" name="title" value="<?php echo @$row['title'] ?>" type="text">
											</div>
										</div>

                      <div class="form-group">
  											<label class="col-md-3 control-label" for="inputDefault">Description</label>
  											<div class="col-md-6">
                                                  <input class="form-control" name="description" value="<?php echo @$row['description'] ?>" type="text">
  											</div>
										  </div>
                      <div class="form-group">
  											<label class="col-md-3 control-label" for="">Pricing frequency</label>
  											<div class="col-md-6">
                                                  <input class="form-control" name="pricing_frequency" value="<?= @$row['pricing_frequency'] ?>" type="text">
  											</div>
										  </div>

                    <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault"># of campaigns</label>
											<div class="col-md-6">
                                                <input class="form-control" name="no_of_campaigns" value="<?php echo @$row['no_of_campaigns'] ?>" type="text">
											</div>
										</div>
                    <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault">Use default credentials</label>
											<div class="col-md-6">
                          <input class="form-control-static " name="use_default_credentials" <?= (int)$row['use_default_credentials'] ==1 ? 'checked' : '' ?> type="checkbox">

											</div>
										</div>


                    <div class="form-group">
                      <label class="col-md-3 control-label" for="inputDefault">Limit for reviews</label>
                      <div class="col-md-6">
                        <div class="col-md-3">
                                                  <input class="form-control" name="fb_reviews_cnt" value="<?php echo @$row['fb_reviews_cnt'] ?>" type="text">
                                                  <span class="help-block text-secondary">Facebook</span>
                        </div>
                    		<div class="col-md-3">
                                                  <input class="form-control" name="google_reviews_cnt" value="<?php echo @$row['google_reviews_cnt'] ?>" type="text">
                                                  <span class="help-block text-secondary">Google</span>
  											</div>
  											<div class="col-md-3">
                                                  <input class="form-control" name="yelp_reviews_cnt" value="<?php echo @$row['yelp_reviews_cnt'] ?>" type="text">
                                                  <span class="help-block text-secondary">Yelp</span>
  											</div>
                        <div class="col-md-3">
                                                  <input class="form-control" name="custom_reviews_cnt" value="<?php echo @$row['custom_reviews_cnt'] ?>" type="text">
                                                  <span class="help-block text-secondary">Custom</span>
  											</div>
										  </div>
										</div>

                    <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault">Amount</label>
											<div class="col-md-6">
                                                <input class="form-control" name="amount" value="<?php echo @$row['amount'] ?>" type="text">
											</div>
										</div>

                    <div class="form-group">
                      <label class="col-md-3 control-label" for="inputDefault">Features list </label>
                      <div class="col-md-6">
                          <textarea class="form-control input-sm" name="features_list" rows="8" cols="80"><?php echo @$row['features_list'] ?></textarea>

                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-md-3 control-label" for="">Pricing button</label>
                      <div class="col-md-6">
                                                <input class="form-control" name="pricing_button" value="<?= @$row['pricing_button'] ?>" type="text">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-md-3 control-label" for="">Pricing column classes</label>
                      <div class="col-md-6">
                        <input class="form-control" name="pricing_column_classes" value="<?= @$row['pricing_column_classes'] ?>" type="text">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-md-3 control-label" for="">Pricing ribbon</label>
                      <div class="col-md-6">
                        <input class="form-control" name="pricing_ribbon" value="<?= @$row['pricing_ribbon'] ?>" type="text">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-md-3 control-label" for="">Plug&Paid Product ID </label>
                      <div class="col-md-6">
                        <select class="form-control" name="plugnpaid_product_id">

                          <option value="0" disabled <?= ((int)$row['plugnpaid_product_id']==0  ? "selected" : "" )?>>Not linked</option>

                          <?php  foreach ($plugnpaid_products as $product): ?>

                            <option <?= ($product['id']==@$row['plugnpaid_product_id']  ? "selected" : "" ) ?> value="<?=$product['id']?>" ><?=$product['name']?></option>

                          <?php endforeach; ?>

                          </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-md-3 control-label" for="">Plug&Paid plug link</label>
                      <div class="col-md-6">
                        <input class="form-control" name="plugnpaid_plug_link" value="<?= @$row['plugnpaid_plug_link'] ?>" type="text">
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-md-3 control-label" for="inputDefault">Active</label>
                      <div class="col-md-6">
                        <input class="form-control-static " name="active" <?= (int)$row['active'] ==1 ?'checked':'' ?> type="checkbox">
                      </div>
                    </div>
                    <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault"></label>
											<div class="col-md-6">
                                                <input type="hidden" name="id" value="<?php echo @$row['id']; ?>">
    											<button type="submit" name="submit" value="submit"  class="btn  btn-fill btn-success "><span class="ace-icon fa fa-save bigger-120"></span> Save</button>
											</div>
										</div>

    								</form>
                                </div>
                            </div>
                        </div>


                    </div> <!-- container -->

                </div> <!-- content -->



            </div>

        </div>
        <!-- END wrapper -->


<?php
function post_fb($url,$method,$body=""){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if($method == "post"){
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    else{
        curl_setopt($ch, CURLOPT_HTTPGET, true );
    }
    return curl_exec($ch);
}
?>

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
