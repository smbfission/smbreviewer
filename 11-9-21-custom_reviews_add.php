<?php
ob_start();
include_once("header.php");
include_once("sidebar.php");
require_once('core/tools.php');

if (isset($_REQUEST['id']) && (int)$_REQUEST['id'] != 0 ) {




$row = $db->getCustomReviewByIdUserId($_REQUEST['id'],$_SESSION['user_id']);

if ($row == null) {
  header("HTTP/1.1 301 Moved Permanently");
  header('location:'.$_SERVER['HTTP_REFERER']);
  exit();
}}

/**
 * Get Calipio URL (in loom_url column)
 * Parse the link to get identifier and password
 */

$calipio_identifier = '';
$calipio_password = '';
if( isset($row['loom_url']) && !empty($row['loom_url']) ) {
  $temp_url = explode('/', $row['loom_url']);
  $temp_url = explode('#', end($temp_url));
  $calipio_identifier = $temp_url[0];
  $calipio_password = $temp_url[1];
}
?>


<style media="screen">

.img-container i.fa {
  position: absolute;
left: 11.2rem;
top: 0rem;
color: green;
font-size: 2.3rem;
}

</style>
<link rel="stylesheet" href="/assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.css" />
          <section role="main" class="content-body">
					<header class="page-header">
						<h2>  Custom Reviews</h2>

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
                                                Create Custom Reviews
                              </h2>
            								<p class="text-muted font-13 m-b-15">
                                                <!-- Description Goes Here <br /> -->
                                                 Create your own Custom Reviews &nbsp;
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



									<!-- <form class="form-horizontal form-bordered" method="get"> -->
                    <form action="/action.php?action=custom_reviews" class="form-horizontal form-bordered" method="post" enctype="multipart/form-data">

                                        <?php
                                        if (@$row['photo']!="") {
                                            $photo_required="";
                                        } else {
                                            $photo_required="required";
                                        }
                                       ?>

                      <div class="form-group">
											  <label class="col-md-3 control-label">Review Picture <i>(<b>Upload limit:</b> 2MB | <b>Recommended dimensions:</b> 150px w x 150px h)</i></label>
											  <div class="col-md-6">
  												<div class="fileupload fileupload-new" data-provides="fileupload">
  													<div class="input-append <?=(trim($row['facebook_id'])==""?:'hidden')?>">
  														<div class="uneditable-input">
  															<i class="fa fa-file fileupload-exists"></i>
  															<span class="fileupload-preview"></span>
  														</div>
  														<span class="btn btn-default btn-file">
  															<span class="fileupload-exists">Change</span>
  															<span class="fileupload-new">Select file</span>
  															<input name="photo" type="file" accept="image/gif, image/jpeg, image/png" <?php echo ""; ?> />
  													    <input name="hidden_photo" type="hidden" value="<?php echo $row['photo']; ?>" />
  														</span>
  														<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
  													</div>
  												</div>
                          <?php if (@$row['photo']!=""): ?>
                            <div class="img-container">


                            <img src="<?= (filter_var($row['photo'], FILTER_VALIDATE_URL) ? $row['photo'] : "/uploads/".$row['photo'] )  ?>" class="img-thumbnail" style="max-height: 100px;" />
                            <?=(trim($row['facebook_id'])!=''? '<i class="fa fa-check-circle"></i>': '') ?>


                            </div>
                            <br /><br />

                          <?php endif; ?>
											  </div>
										  </div>

                                        <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault">Reviewer Name</label>
											<div class="col-md-6">
                        <input class="form-control" name="name" value="<?php echo @$row['name'] ?>" type="text" required="" placeholder="i.e: Jason Mike" <?=(trim($row['facebook_id'])==""?:'readonly')?>>
											</div>
										</div>

                                        <?php
                                        if (!isset($row['rating'])) {
                                            $row['rating']=4.5;
                                        }
                                        ?>
                                        <div class="form-group">
											<label class="col-md-3 control-label">Rating</label>
											<div class="col-md-6">
												<div data-plugin-spinner data-plugin-options='{ "value":<?php echo @$row['rating'] ?>, "step": 1, "min": 1, "max": 5 }'>
													<div class="input-group" style="width:150px;">
														<input type="text" name="rating" class="spinner-input form-control" maxlength="5" readonly>
														<div class="spinner-buttons input-group-btn <?=((int)$row['capture_reviews_id']==0?:'hidden')?>">
															<button type="button" class="btn btn-default spinner-up">
																<i class="fa fa-angle-up"></i>
															</button>
															<button type="button" class="btn btn-default spinner-down">
																<i class="fa fa-angle-down"></i>
															</button>
														</div>
													</div>
												</div>
											</div>
										</div>

                                        <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault">Review Content</label>
											<div class="col-md-6">
												<textarea class="form-control" name="review" placeholder="Write your review here" required="" <?=((int)$row['capture_reviews_id']==0?:'readonly')?>><?php echo @$row['review'] ?></textarea>
											</div>
										</div>
                                        <?php
                                        if (!isset($row['date'])) {
                                            $row['date']=date("Y-m-d");
                                        }
                                        ?>
                    <div class="form-group">
											<label class="col-md-3 control-label">Review Date</label>
											<div class="col-md-6">
												<div class="input-group">
													<span class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</span>
													<input type="text" name="date" value="<?php echo date("m/d/Y", strtotime($row['date'])); ?>" data-plugin-datepicker class="form-control" required="" <?=((int)$row['capture_reviews_id']==0?:'readonly')?>>
												</div>
											</div>
										</div>
										
										<div class="form-group <?=( isset($row['loom_url']) && $row['loom_url'] != '' ?'':'hidden')?>">
										    <label class="col-md-3 control-label" for="inputDefault">Video review</label>
											<div class="col-md-6" id="embed-container">
												
											</div>
										</div>



                    <div class="form-group">
                      <label class="col-md-3 control-label">Review icon <i>(<b>Upload limit:</b> 2MB | <b>Recommended dimensions:</b> 30px w x 30px h)</i></label>
                      <div class="col-md-6">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                          <div class="input-append">
                            <div class="uneditable-input">
                              <i class="fa fa-file fileupload-exists"></i>
                              <span class="fileupload-preview"></span>
                            </div>
                            <span class="btn btn-default btn-file">
                              <span class="fileupload-exists">Change</span>
                              <span class="fileupload-new">Select file</span>
                              <input name="icon" type="file" accept="image/gif, image/jpeg, image/png" <?php echo ""; ?> />
                              <input name="hidden_icon" type="hidden" value="<?= $row['icon']; ?>" />
                            </span>
                            <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                          </div>
                        </div>
                        <?php if (@$row['icon']!=""): ?>
                          <img src="<?= (filter_var($row['icon'], FILTER_VALIDATE_URL) ? $row['icon'] : "/uploads/custom_reviews/".$row['icon'] )  ?>" class="img-thumbnail" style="max-height: 50px;" />
                          <br /><br />

                        <?php endif; ?>
                      </div>
                    </div>

                    <div class="form-group"  value="<?=$row['tags']?>">
                      <label class="col-md-3 control-label">Tags</label>
                      <div class="col-md-6">

                          <input type="text" value="<?= $row['tags']?> " data-role="tagsinput" name="tags" class="form-control">


                      </div>
                    </div>

                    <?php if (trim($row['feedback_text'])!=''): ?>
                      <div class="form-group" >
                        <label class="col-md-3 control-label">Feedback text</label>
                        <div class="col-md-6 form-control-static ">

                            <?=$row['feedback_text']?>

                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if ((int)$row['capture_reviews_id']>0): ?>
                      <div class="form-group" >
                        <label class="col-md-3 control-label">Capture review campaign</label>
                        <div class="col-md-6 form-control-static">
                            <a class="" target="_blank" href="/capture_reviews_add?id=<?=$row['capture_reviews_id']?>"><?=$row['capture_reviews_title']?></a>
                        </div>
                      </div>
                    <?php endif; ?>

                    <div class="form-group">
											<label class="col-md-3 control-label" for="inputDisabled"></label>
											<div class="col-md-6 ">
												<input name="id" type="hidden" value="<?php echo @$_REQUEST['id']; ?>" />
                      <button type="submit" name="submit" value="submit"  class="btn  btn-fill btn-success"><span class="ace-icon fa fa-save bigger-120"></span> Save</button>
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
		<script src="/assets//vendor/jquery/jquery.js"></script>
		<script src="/assets//vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="/assets//vendor/bootstrap/js/bootstrap.js"></script>
		<script src="/assets//vendor/nanoscroller/nanoscroller.js"></script>
		<script src="/assets//vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="/assets//vendor/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
		<script src="/assets//vendor/magnific-popup/magnific-popup.js"></script>
		<script src="/assets//vendor/jquery-placeholder/jquery.placeholder.js"></script>

		<!-- Specific Page Vendor -->
		<script src="/assets//vendor/jquery-autosize/jquery.autosize.js"></script>
		<script src="/assets//vendor/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
        <script src="/assets//vendor/fuelux/js/spinner.js"></script>

		<!-- Theme Base, Components and Settings -->
		<script src="/assets//javascripts/theme.js"></script>

		<!-- Theme Custom -->
		<script src="/assets//javascripts/theme.custom.js"></script>

		<!-- Theme Initialization Files -->
		<script src="/assets//javascripts/theme.init.js"></script>

    <script type="text/javascript">

    $('input[data-role="tagsinput"]').tagsinput({
      confirmKeys: [13, 32, 44]
    });
    </script>
    
    <script type="text/javascript">
	    
	    async function addEmbed() {
        document.getElementById('embed-container').innerHTML = '';
        embedStr = document.createElement('script');
        embedStr.setAttribute('src','https://calipio.com/app/embeddable.js?<?php echo $calipio_identifier; ?>#<?php echo $calipio_password; ?>');
        document.getElementById('embed-container').append(embedStr);
        }
        
        $(document).ready(function() {
        	addEmbed();
        });
	</script>

	</body>
</html>
