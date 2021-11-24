<?php
ob_start();

require_once("core/database.php");

if (isset($_REQUEST['id'])) {
  $id = base64_decode($_REQUEST['id']);
  $db = new Database();
  $row = $db->getCustomReviewByReviewId($id);
  if ($row == null) {
    header("HTTP/1.1 301 Moved Permanently");
    header('location:'.$_SERVER['HTTP_REFERER']);
    exit();
  }
}



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
<!doctype html>
<html class="fixed">

  <head>
    <meta charset="UTF-8">
    <title>Video Review by <?php echo isset($row['name']) ? $row['name'] : ''; ?>: Here's My Review About <?=$data['name_of_business']?> </title>
    <link rel="shortcut icon" href="/assets/images/favicon.ico">
    <meta name="keywords" content="facebook reviews, google my business reviews, smbreviewer, review management" />
    <meta name="description" content="<?php echo isset($row['review']) ? $row['review'] : ''; ?>">
    <meta name="author" content="<?=$data['name_of_business']?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <style>
      .calipio-player-embed {
        width: 100% !important;
        max-width: 550px;
        margin: 20px auto;
      }
      #embed-container {
        margin-bottom: 20px;
      }
    </style>
    <style media="screen">

      .img-container i.fa {
        position: absolute;
      left: 11.2rem;
      top: 0rem;
      color: green;
      font-size: 2.3rem;
      }


    </style>
  </head>
  <body>
    <section class="body">
      <header class="header">
        <div class="logo-container">
          <a href="../" class="logo" style="font-size:25px;">
            <img src="data:image/webp;base64,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" alt="SMBreviewer" style="border:0px;" id="pagespeed_img_Ttu7HmxT_b1">
          </a>
        </div>
      </header>

      <div class="inner-wrapper text-center"> 
        <section role="main" class="content-body">
          <div class="row">
            <div class="col-lg-12">
              <section class="panel">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-sm-12">
                      <!--  VIDEO START  -->  
                      <div class="form-group <?=( isset($row['loom_url']) && $row['loom_url'] != '' ?'':'hidden')?>">
                        <div class="col-sm-12" id="embed-container">
                          <script src='https://calipio.com/app/embeddable.js?<?php echo $calipio_identifier; ?>#<?php echo $calipio_password; ?>'></script>
                        </div>
                      </div>
                       <!--  VIDEO END  -->  
                       <h2>&quot;<?php echo isset($row['review']) ? $row['review'] : ''; ?>&quot;</h3>
                       <h3 style="color: #ff761d;"><?php echo isset($row['name']) ? $row['name'] : ''; ?></h2>
                       
                    </div>
                  </div>
                </div>
              </section>  
            </div>
          </div>
        </section>
      </div>
    </section>
  </body>
</html>
<?php /*
          <section role="main" class="content-body">
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
  */ ?>  
  
