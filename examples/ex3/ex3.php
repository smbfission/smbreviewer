<!doctype html>
<html class="fixed">

<head>
	<meta charset="UTF-8">
	<title>
		<?=$data['page_title']?>
	</title>
	<link rel="shortcut icon" href="/assets/images/favicon.ico">
	<meta name="keywords" content="facebook reviews, google my business reviews, smbreviewer, review management" />
	<meta name="description" content="Please leave <?=$data['name_of_business']?> a your review, we would love to get your feedback.">
	<meta name="author" content="<?=$data['name_of_business']?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	<style media="screen">

body {
	background: gray;
}


.feedback-form{
    background:#fff;
    margin-top:7%;
    margin-bottom:3%;
}
.feedback-form .form-control{
    border-radius: 1rem;
}
.feedback-image{
   text-align:center; 
}
.feedback-image img{
    border-radius:6rem;
    width:11%;
    margin-top:-2%;
    transform:fixed;
}
.feedback-form form{
    padding:14%;
}
.feedback-form .form .row{
    margin-bottom:-7%; 
}
.feedback-form h3{
    margin-bottom:8%;
    margin-top:-10%;
    text-align:center;
    color:#0062cc
}
.feedback-form .btnsubmit{
    width:50%;
    border:none;
    border-radius:1rem;
    padding:1.5%;
    Background:red;
    font-weight:600;
    color:#fff;
    cursor:pointer;
}

	.cointainer input.star {
		display: none;
	}
	
	#review_form label.star {
		float: right;
		padding: 10px;
		font-size: 36px;
		color: #444;
		transition: all .2s;
	}
	
	#review_form input.star:checked ~ label.star:before {
		content: '\f005';
		color: #FD4;
		transition: all .25s;
	}
	
	#review_form input.star-5:checked ~ label.star:before {
		color: #FE7;
		text-shadow: 0 0 20px #952;
	}
	
	#review_form input.star-1:checked ~ label.star:before {
		color: #F62;
	}
	
	#review_form label.star:hover {
		transform: rotate(-15deg) scale(1.3);
	}
	
	#review_form label.star:before {
		content: '\f006';
		font-family: FontAwesome;
	}
	
	span.areviews-reviews-count {
		color: white;
		background-color: #8d5fa8;
		border: 1px solid #8d5fa8;
		margin: 0 0px 0 4px;
		border-radius: 40px;
		text-align: center;
		font-size: 0.80rem;
		padding: 0.1em 0.5em;
		line-height: 15px;
		min-width: 20px;
		top: -8px;
		position: relative;
	}
	
	.areviewer-reviews {
		width: 100%;
		padding: 0;
	}
	
	.areviewer-reviews .areviewer-reviews-list {
		padding: 0;
	}
	
	.areviewer-reviews li {
		border: 1px solid #ccc;
		margin-bottom: 10px;
		border-radius: 2px;
	}
	
	.areviewer-reviews .areviews-head {
		display: flex;
		border-bottom: 1px solid #8d5fa8;
		align-items: center;
		height: 40px;
		justify-content: space-between;
	}
	
	.areviewer-reviews .areviews-head .areviews-rating {
		padding: 0;
	}
	
	.areviewer-reviews .areviews-head .areviews-date {
		padding: 0;
		font-size: 0.84rem;
		text-align: center;
		overflow: hidden;
		max-height: 100%;
	}
	
	
	.areviewer-reviews .areviews-head .areviews-rating-5::before {
		content: '\f005\f005\f005\f005\f005';
		font-family: FontAwesome;
		color: #FD4;
	}
	
	.areviewer-reviews .areviews-head .areviews-rating-4::before {
		content: '\f005\f005\f005\f005\f006';
		font-family: FontAwesome;
		color: #FD4;
	}
	
	.areviewer-reviews .areviews-head .areviews-rating-3::before {
		content: '\f005\f005\f005\f006\f006';
		font-family: FontAwesome;
		color: #FD4;
	}
	
	.areviewer-reviews .areviews-head .areviews-rating-2::before {
		content: '\f005\f005\f006\f006\f006';
		font-family: FontAwesome;
		color: #FD4;
	}
	
	.areviewer-reviews .areviews-head .areviews-rating-1::before {
		content: '\f005\f006\f006\f006\f006';
		font-family: FontAwesome;
		color: #FD4;
	}
	
	.areviewer-reviews .areviews-head .areviews-rating-0::before {
		content: '\f006';
		font-family: FontAwesome;
		color: #FD4;
	}
	
	.review-container-step-2,
	.review-container-step-3,
	.review-container-step-4 {
		display: none;
	}
	
	
	
	.reviews {
		background-color: #f5f5f5;
		border-radius: 2rem;
		padding: 2rem;
		background: #f5f5f5;
		float: none;
		margin: 2rem auto;
		display: flex;
		justify-content: center;
	}
	
	.reviews >div {
		width: 100%;
	}
	
	.loader {
		width: 20px;
		height: 20px;
		border-radius: 100%;
		position: relative;
		margin: 0 auto;
	}
	/* LOADER 1 */
	
	.loader:before,
	.loader:after {
		content: "";
		position: absolute;
		top: -0%;
		left: -10%;
		width: 100%;
		height: 100%;
		border-radius: 100%;
		border: .3rem solid transparent;
		border-top-color: #3498db;
	}
	
	.loader:before {
		z-index: 100;
		animation: spin 1s infinite;
	}
	
	.loader:after {
		border: .3rem solid #ccc;
	}
	
	@keyframes spin {
		0% {
			-webkit-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		}
		100% {
			-webkit-transform: rotate(360deg);
			-ms-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		}
	}
	
	
	
	
	.user-photo {
		display: block;
	}
	
	.user-photo img {
		max-width: 50px;
		border-radius: 50%;
		/* border: 1px solid #0000003b; */
		box-shadow: 0 0 2px 2px #0000003b;
	}
	
	.drag-container {
		min-height: 0px;
		border: transparent 1px #f6d3d3;
		border-radius: 4px;
		margin: 1px;
	}
	
	h1 {
		font-size: 2.5rem;
	}
	
	.review-container-step-3 h2 {
		padding: 1rem;
	}
	
	.review-container-step-3 form {
		padding: 2rem;
	}
	
	.review-container-step-3 .review-text-share,
	.review-container-step-3 ul {
		padding: 1rem;
	}
	
	
	
	.fbVerified {
		display: flex;
		align-items: flex-end;
	}
	
	.fbVerified .fa-check-circle {
		position: absolute;
		left: 60px;
		color: green;
	}
	/*
    .fbVerified .fa-facebook {
      background-color: #0f90f2;
      border-radius: 50%;
      height: 40px;
      width: 40px;


    }
    .fbVerified .fa-facebook:before {

  font-size: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;

} */
	
	.verified-logo {
		/* width: 90px; */
		width: 50px;
		padding: 0 0 0 5px;
		border-radius: 10px;
	}
	
	.input-group-addon.verified .fa {
		color: #327ab7;
	}
	
	.input-group-addon.verified .fa {
		color: #327ab7;
	}
	
	.input-group-addon.verified .fa-check-circle {
		color: green;
		position: absolute;
		top: 5px;
		left: 22px;
		box-shadow: 0 0 2px 0 #ccc;
		border-radius: 50%;
		background-color: #ccc;
	}
	
	.input-group-addon.verified.email {
		cursor: pointer;
	}
	
	.input-group-addon.email:not(.verified) {
		cursor: pointer;
	}
	
	.video-responsive {
		overflow: hidden;
		padding-bottom: 56.25%;
		position: relative;
		height: 0;
		display: block;
		margin: 0 auto;
	}
	
	.video-responsive iframe {
		left: 0;
		top: 0;
		height: 100%;
		width: 100%;
		position: absolute;
	}
	
	.review-container-step-4 h4 {
		padding: 1em;
	}
	
	#loom-container {
	    width: 100%;
	}
	
	#loom-record-button{ 
	    padding: 3px;
	    margin: -5px;
	    display: block;
	    background-color: white;
	    border-radius: 5px;
	    font-weight: bold;
	    font-size: 18pt;
	    margin-top: 10 px;
	}
	
	#loom-record-button i {
	    color: red;
	    margin-right: 10px;
	}
	
	.share-social-media {
	    width: 100%;
	    display: flex;
	    flex-wrap: wrap;
	    
	    justify-content: center;
	    align-items: middle;
	    margin-bottom: 20px;
	}
	
	.share-social-media div {
	    margin: 4px;
	}
	
	.share-container {
	    margin-right: 10px;
	}
	
	.share-title {
	    font-size: 15pt;
	    margin-top: 30px;
	}
	
	#share-facebook {
	    margin-bottom: -5px;
	}
	
	a.twitter-share-button {
      background-color: #1DA1F2;
      text-decoration: none;
      color: white;
      border-radius: 2px;
      padding: 3px;
      font-size: 9pt;
    }
	</style>
	
</head>

<?php
    @session_start();
    require_once('core/database.php');
    require_once('core/tools.php');
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    
    $ids= Tools::decrypt_link($_REQUEST['id']);
    $capture_reviews_id=(int)$ids;
    
    $db = new Database();
    $nReviews = $db->getNumberOfThisMonthsReviews();
    $reviewData = $db->getCaptureReviewsById($capture_reviews_id);
    $userId = $reviewData["user_id"];
    $user = $db->getUserProfile($userId);
    // 1 is free 
    // 3 premium
    $planId = $user["plan_id"];
    
    $canRecordReview = true;
    if($nReviews["numberOfReviews"] >= 15 && $planId == 1) {
        $canRecordReview = false;
        $reviewData['type'] = 'text';
    }
    
    //var_dump($reviewData);
    
?>

<body>
<div class ="container feedback-form">
        
        
		<form method="POST">
		 <h3>Drop Us a Feedback</h3><br>
		 <h3>We'd love to hear your thoughts and experience with your service. Please share your experience with us.</h3>
		 <div class="form-group"></div>
		
		 <div class="row">

		 <div class="col-md-6">
			 
			 <div class="form-group">
				 <input type="text" class="form-control" placeholder="Your Name">
			  </div>
			  
			  <div class="form-group">
				 <input type="text" class="form-control" placeholder="Your Mobile Number">
			  </div>
			  <div class="form-group">
				 <p>Did You Get Your Answer?</p>
				 Yes<input type="radio" name="1">
				 No<input type="radio" name="1">
			  </div>
		  
			  <div class="form-group">
			 <label for="name">Your Image
					
			 </label>
				 </div>
			   <div class="drag-container" >
				  
				 <input id="file" type="file" name="file" class="hidden" accept=".jpg, .jpeg, .png" />
				 <div class="upload-area text-center"  id="uploadfile">
					   
				 </div>
			   </div>
			   &nbsp;
			   &nbsp;
			   &nbsp;
			  <textarea name="txtMsg" class="form-control" placeholder="Your Message Or Suggetion"
			  style="width: 600px;height:150px;margin-bottom:8px;"></textarea>
			  <div class="form-group">
				 <input type="submit" name="btnsubmit" class="btnsubmit" value="Submit" style="outline:none;">
			 </div>
		 </div>
			<div class="col-md-6">
			 <div class="form-group">
				 <input type="text" class="form-control" placeholder="Your Email">
				 <p>Are You Satisfied With Our Services?</p>
				 Yes<input type="radio" name="2">
				 No<input type="radio" name="2">
			  </div>
			  <p>How many Stars Do you Want to give us</p>
			  <div class="stars"  data-toggle="popover" data-placement="bottom">
				 <input class="star star-5" id="review_form-star-5" type="radio" name="star" value="5"/>
				 <label class="star star-5" for="review_form-star-5" ></label>
				 <input class="star star-4" id="review_form-star-4" type="radio" name="star" value="4"/>
				 <label class="star star-4" for="review_form-star-4"></label>
				 <input class="star star-3" id="review_form-star-3" type="radio" name="star" value="3"/>
				 <label class="star star-3" for="review_form-star-3"></label>
				 <input class="star star-2" id="review_form-star-2" type="radio" name="star" value="2"/>
				 <label class="star star-2" for="review_form-star-2"></label>
				 <input class="star star-1" id="review_form-star-1" type="radio" name="star" value="1"/>
				 <label class="star star-1" for="review_form-star-1"></label>
		   </div>
		   &nbsp;<br>
		   <!-- Loom implementation -->
		   <div class="form-group <?=($data['type']=='text' || $canRecordReview == false? 'hidden':'')?>" id="loom-container">
			 <label for="loom-video-url">Record Your Review <i style="font-size: 10pt; font-weight: 100">(best used on desktop)</i></label>
			 <input type="hidden" id="loom-video-url" name="loomUrl" value="">
			 <button type="button" id="loom-record-button"><i class="fa fa-circle" aria-hidden="true"></i>Click to record</button>
			 <div id="embed-container" style="margin-top: 20px"></div>
		 </div>
		  <!-- starting  step 3 -->

		  <div class=" review-container-step-3">


			 <div class="col-12">
				  <h1   style="font-weight:bold;" class="text-center primary-font-color primary-font-family"><?=$data['name_of_business']?></h1>
			 </div>
			 <div class="col-12">
				<h2  style="font-weight:bold;" class="text-center primary-font-color primary-font-family"><h2>Will You Do Us a Favor?</h2></h2>
			 </div>
			 <div  class="secondary-font-color secondary-font-family col-12">
				 <span class="secondary-font-color secondary-font-family">
					 <h4>
						  <b><span style="text-decoration:underline;font-weight:bold;"></span>Your review has been copied to your clipboard</b>, 
						  if you would be graciously willing, could you share your experience with other people like yourself by pasting your review in the review directory listed below? It's a great way to help other people like yourself and we would greatly 
						  appreciate it!
					 </h4>
				 </span>


	 </form>

   </div>

               <input name="capture_reviews_token" type="hidden" value="<?= $form_token; ?>">
               <div class="text-center">
                    <button type="submit" class="btn btn-primary" id="submit-button-first-step" name="button"><div class="loader hidden"></div><b>SUBMIT REVIEW</b></button>
               </div>

               <span class="areviewer-form-error text-danger"></p>
               </form>
                   <!-- <p class="text-center text-muted " >&copy; Copyright <?=date('Y')?>. All rights reserved.  <a href="https://www.smbreviewer.com" target="_blank">SMBreviewer</a>.</p> -->
                   <div class="text-center text-muted col-md-12 "><?=$data['footer_text']?></div>
             </div>
          <!-- ending step ! -->


             <!-- starting step 2 -->
             <div class=" review-container-step-2">

               <?php if (trim($data['logo'])!=''): ?>
                 <img class="img-responsive center-block" src="/uploads/capture_reviews/<?=$data['logo']?>" alt="">
                 <?php else: ?>
                   <h1 class="text-center"><?=$data['name_of_business']?></h1>
               <?php endif; ?>

               <h2 class="primary-font-color primary-font-family text-center">Please Provide Your Feedback </h2>
									<p class="secondary-font-color secondary-font-family">
										<h3 class="secondary-font-color secondary-font-family">Thank you so much for your review, are there any specific comments or feedback that you would like to send to management regarding your experience, and how we could have improved your experience and your review of us?</h3></p>
									<form class="form" id="feedback_form" method="post">
										<div class="form-group">
											<label for="message">
												<h3>Enter Your Feedback Message</h3> </label>
											<textarea name="message" rows="8" class="form-control" data-toggle="popover" data-placement="bottom"></textarea>
										</div>
										<div class="col-md-12 review-text-feedback">
											<label>Your Review:</label>
											<p></p>
										</div>
										<input name="capture_reviews_token" type="hidden" value="<?= $form_token; ?>">
										<div class="text-center">
											<button type="submit" class="btn btn-primary" name="button">
												<div class="loader hidden"></div> SUBMIT FEEDBACK</button>
										</div> <span class=" areviewer-form-error text-danger"></p>
               </form>
               <!-- <p class="text-center text-muted " >&copy; Copyright <?=date('Y')?>. All rights reserved.  <a href="https://www.smbreviewer.com" target="_blank">SMBreviewer</a>.</p> -->
               <div class="text-center text-muted col-md-12 "><?=$data['footer_text']?></div>

             </div>

          <!-- ending step 2 -->


             <!-- starting step 3 -->

             <div class=" review-container-step-3">


                <div class="col-12">
                     <h1   style="font-weight:bold;" class="text-center primary-font-color primary-font-family"><?=$data['name_of_business']?></h1>
                </div>
                <div class="col-12">
                   <h2  style="font-weight:bold;" class="text-center primary-font-color primary-font-family"><h2>Will You Do Us a Favor?</h2></h2>
                </div>
                <div  class="secondary-font-color secondary-font-family col-12">
                    <span class="secondary-font-color secondary-font-family">
                        <h4>
                             <b><span style="text-decoration:underline;font-weight:bold;"></span>Your review has been copied to your clipboard</b>, 
                             if you would be graciously willing, could you share your experience with other people like yourself by pasting your review in the review directory listed below? It's a great way to help other people like yourself and we would greatly 
                             appreciate it!
						</h4>
					</span>
			</div>
			<div class="col-12">
				<form class="form text-center" id="share_form" method="post">
					<div class="col-md-12 review-text-share <?= isset($reviewData['type']) && $reviewData['type'] == 'video' ? '':'hidden' ?>">
						<label>
							<h3>Share your testimonial</h3>
						</label>
						<div id="embed-container-step-3" style="margin-top: 20px"></div>
						<p class="review-text"></p>
						<div>
							<h3 style="font-size: 19px"><b>Let Your Friends, Family, and Network know about Good Quality People.<br/> Click Below to Share Your Testimonial with Your Network.</b></h3>
						</div>
					</div>
					<div class="col-md-12 review-text-share <?= isset($reviewData['type']) && $reviewData['type'] == 'text' ? '':'hidden' ?>">
						<label>
						    <h3>Your Review</h3>
						</label>
                        <p class="review-text"></p>
                        <div><h3><b>Click on the review directory below of your choice to be directed to our page.</b></h3></div>
					</div>
					<div class="col-md-12 share-social-media <?= isset($reviewData['type']) && $reviewData['type'] == 'video' ? '':'hidden' ?>">
					    <div>
					        <iframe class="<?= isset($reviewData['share_facebook']) && $reviewData['share_facebook'] == 1 ? '':'hidden' ?>" id="share-facebook" width="67" height="20" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
					    </div>
					    <div class="<?= isset($reviewData['share_linkedin']) && $reviewData['share_linkedin'] == 1 ? '':'hidden' ?>">
					        <script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US</script>
                            <script type="IN/Share"></script>
					    </div>
                        <div>
                            <a id="share-twitter" class="twitter-share-button <?= isset($reviewData['share_twitter']) && $reviewData['share_twitter'] == 1 ? '':'hidden' ?>">
                                <i class="fa fa-twitter"></i> Tweet
                            </a>
                        </div>
					</div>
					<ul class="list-inline text-center center-block <?= isset($reviewData['type']) && $reviewData['type'] == 'text' ? '':'hidden' ?>">
						<?php if ($data['enable_review_directories_google']==1 && trim($data['review_directories_google'])!=''): ?>
							<li>
								<a target="_blank" href="http://www.g.page/<?=trim($data['review_directories_google'])?>/review"><img src="/assets/images/google-business-logo-1.png" alt="share on google"></a>
							</li>
							<?php endif; ?>
								<?php if ($data['enable_review_directories_facebook']==1 && trim($data['review_directories_facebook'])!=''): ?>
									<li>
										<a target="_blank" href="https://www.facebook.com/<?=trim($data['review_directories_facebook'])?>/reviews"><img src="/assets/images/facebook-logo-1.png" alt="share on facebook"> </a>
									</li>
									<?php endif; ?>
										<?php if ($data['enable_review_directories_yelp']==1 && trim($data['review_directories_yelp'])!=''): ?>
											<li>
												<a target="_blank" href="https://www.yelp.com/writeareview/biz/<?=trim($data['review_directories_yelp'])?>"><img src="/assets/images/yelp-logo-1.png" alt="share on yelp"> </a>
											</li>
											<?php endif; ?>
												<?php if ($data['enable_review_directories_custom']==1 && trim($data['review_directories_custom'])!=''): ?>
													<li>
														<a target="_blank" href="<?=trim($data['review_directories_custom'])?>">
															<?php if ($data['custom_logo']): ?> <img src="/uploads/capture_reviews/<?=$data['custom_logo']?>" alt="share on <?=$data['page_title']?>">
																<?php else: ?> share on
																	<?=$data['page_title']?>
																		<?php endif; ?>
														</a>
													</li>
													<?php endif; ?>
					</ul>
					<input name="capture_reviews_token" type="hidden" value="<?= $form_token; ?>">
					<button type="submit" class="btn btn-primary" name="button">
						<div class="loader hidden"></div><b>No Thanks</b></button>
				</form>
			</div>
			<!-- <p class="text-center text-muted " >&copy; Copyright <?=date('Y')?>. All rights reserved.  <a href="https://www.smbreviewer.com" target="_blank">SMBreviewer</a>.</p> -->
			<div class="text-center text-muted col-12 ">
				<?=$data['footer_text']?>
			</div>
		</div>
		<!-- ending step 3 -->
		<!-- starting step 4 -->
		<div class=" review-container-step-4">
			<h1 style="font-weight:bold;" class="text-center primary-font-color primary-font-family"><?=$data['name_of_business']?></h1>
			<h2 class="primary-font-color primary-font-family text-center">Thank You for Your Review!</h2>
			<?php if (trim($data['redirect_url'])!=''): ?>
				<h4 class="secondary-font-color secondary-font-family text-center">Please wait while we redirect you.</h4>
				<?php endif; ?>
					<!-- <p class="text-center text-muted " >&copy; Copyright <?=date('Y')?>. All rights reserved.  <a href="https://www.smbreviewer.com" target="_blank">SMBreviewer</a>.</p> -->
					<div class="text-center text-muted col-md-12 ">
						<?=$data['footer_text']?>
					</div>
		</div>
		<!-- ending step 4 -->
	</div>
	</div>
	
	<script type="text/javascript">
	if(global === undefined) {
		var global = window;
	}
	</script>
	<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<script type="module">
	    import * as loom from "https://www.unpkg.com/@loomhq/loom-embed@1.2.2/dist/esm/index.js?module";
	    import {isSupported, setup} from "/uploads/default_files/loom.js";
	    
        async function init() {
        	const root = document.getElementById("loom-container");
        	const button = root.querySelector(`#loom-record-button`);
        	if(button == null || !isSupported()) {
        		return;
        	}
        	const {
        		configureButton
        	} = await setup({
        		apiKey: "de1eeb69-dfd2-4ccf-8239-3c2e846b4800"
        	});
        	configureButton({
        		element: button,
        		hooks: {
        			onInsertClicked: (shareLink) => {
        				console.log("clicked insert");
        				console.log(shareLink);
        				document.getElementById("loom-video-url").value = shareLink.sharedUrl;
        				console.log(document.getElementById("loom-video-url").value);
        				addEmbed(document.getElementById("loom-video-url").value);
        			},
        			onStart: () => console.log("start"),
        			onCancel: () => console.log("cancelled"),
        			onComplete: () => console.log("complete"),
        		}
        	});
        }
        
        async function addEmbed(embedUrl) {
            let embedResponse = await loom.oembed(embedUrl);
        	console.log(embedResponse.html);
        	let embedContainer = $('#embed-container');
        	embedContainer.html(embedResponse.html);
        }
        
        $(document).ready(function() {
        	init();
        });
	</script>
	<script type="text/javascript">
	function getLoomUrl() {
	    var url = $('input[name="loomUrl"]').val();
	    var encodedUrl = url.replace(':', '%3A');
	    encodedUrl = encodedUrl.replace('/', '%2F');
	    
	    $('#share-facebook').attr('src', 'https://www.facebook.com/plugins/share_button.php?href=' + encodedUrl+'&layout=button&size=small&appId=269556227512901&width=67&height=20')
	    $('#share-twitter').attr('href', 'https://twitter.com/intent/tweet?text=Check%20out%20my%20review%20' + encodedUrl)
	    
	    console.log(url)
	    
	    <?php
	        if(
	            (isset($reviewData['type']) && $reviewData['type'] == 'video') &&
	            (isset($reviewData['share_facebook']) && $reviewData['share_facebook'] == 0) &&
	            (isset($reviewData['share_linkedin']) && $reviewData['share_linkedin'] == 0) &&
	            (isset($reviewData['share_twitter']) && $reviewData['share_twitter'] == 0)
	        ){
		        echo "
    		        if(url != '') {
    		            $('form').submit();
    		        }
		        ";
	        }
		?>
	    
	    //embed-container-step-3
	}
	
	$(document).ready(function() {
		$('form').submit(function(e) {
			e.preventDefault();
			//    e.returnValue = false;
			//  $('.stars').popover('hide');
			console.log('submitting ', $(this).attr('id'));
			var data = new FormData($(this)[0]);
			if($(this).attr('id') == 'review_form') {
				if(data.get('star') === null) {
					$('#submit-button-first-step').popover({
						html: true,
						content: '<i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i> Please add stars'
					}).popover('show');
					return false;
				} else {
					$('.stars').popover('hide');
				}
				if(data.get('name').trim() == '') {
					$('#submit-button-first-step').popover({
						html: true,
						content: '<i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i> Please enter your name'
					}).popover('show');
					return false;
				} else {
					$('input[name="name"]').popover('hide');
				}
				if(data.get('loomUrl').trim() == '' && !$('#loom-container').hasClass('hidden')) {
					$('#loom-record-button').popover({
						html: true,
						content: '<i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i> Please record your review'
					}).popover('show');
					return false;
				} else {
					$('input[name="loomUrl"]').popover('hide');
				}
				if(data.get('message').trim() == '' && $('#loom-container').hasClass('hidden')) {
					$('textarea[name="message"]').popover({
						html: true,
						content: '<i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i> Please enter your review'
					}).popover('show');
					return false;
				} else {
					$('textarea[name="message"]').popover('hide');
				}
			}
			if($(this).attr('id') == 'feedback_form') {
				data.append('step', 2);
				data.append('insert_id', $('#feedback_form').attr('insert_id'));
				data.append('google_updates', $('#feedback_form').attr('google_updates'));
			}
			if($(this).attr('id') == 'share_form') {
				data.append('step', 3);
				data.append('insert_id', $('#feedback_form').attr('insert_id'));
			}
			$('.loader').removeClass('hidden');
			$('button[type="submit"]').attr('disabled', true);
			if(($(this).attr('id') == 'review_form') && (data.get('file').name == '') && (typeof $('.user-photo img').attr('src') !== 'undefined')) {
				var request = new XMLHttpRequest();
				request.responseType = "blob";
				request.onload = function() {
					data.delete('file');
					data.append("file", request.response);
					makePostForm(data);
				}
				request.open('GET', $('.user-photo img').attr('src'));
				request.send();
			} else {
				makePostForm(data);
			}
		});
		$(document).on('click', '.input-group-addon.verified.email', function() {
			$(this).closest('.form-group').removeClass('has-success');
			$(this).removeClass('verified');
			$(this).find('.verified').remove();
			$(this).closest('.form-group').find('input').attr('readonly', false).val('');
		});
		$(document).on('click', '.input-group-addon.email:not(.verified)', function() {
			FB.api('/me', {
				fields: 'name, id, picture, email,first_name,last_name'
			}, function(response) {
				if(response.email) {
					$('input[name="email"]').val(response.email).attr('readonly', true);
					$('input[name="email"]').siblings('.input-group-addon').addClass('verified email').append('<i class="fa fa-check-circle verified" aria-hidden="true"></i>');
					$('input[name="email"]').closest('.form-group').addClass('has-success');
				}
			});
		});
	});

	function makePostForm(data) {
		let d = {};
		for(let [key, prop] of data) {
			d[key] = prop;
		}
		//console.log(d,document.referrer);
		if(window.self !== window.top) {
			//  console.log('ifrane',window.location.href);
			data.append('iframe', '1');
		} else {
			//  console.log('no ifrane');
		}
		$.ajax({
			type: 'post',
			//      url: window.location.href,
			data: data,
			cache: false,
			processData: false,
			contentType: false,
			success: function(response) {
				//      console.log(response);
				$('.loader').addClass('hidden');
				$('button[type="submit"]').attr('disabled', false);
				if(response.step_2) {
					$('#feedback_form .review-text-feedback>p').append($('#review_form textarea[name="message"]').val());
					$('#feedback_form').attr('insert_id', response.insert_id);
					if(response.google_updates) {
						$('#feedback_form').attr('google_updates', response.google_updates.updatedRange);
					}
					$('#feedback_form input[name="capture_reviews_token"]').val(response.capture_reviews_token);
					$('.review-container-step-1').hide();
					$('.review-container-step-2').show();
					$('.review-container-step-3').hide();
					console.log('step2');
					return;
				} else if(response.step_3) {
					$('#share_form .review-text-share>p').append($('#review_form textarea[name="message"]').val());
					$('#share_form input[name="capture_reviews_token"]').val(response.capture_reviews_token);
					$('.review-container-step-1').hide();
					$('.review-container-step-2').hide();
					$('.review-container-step-3').show();
					console.log('step3');
					document.execCommand("copy");
					var $temp = $("<input>");
					$("body").append($temp);
					$temp.val($('textarea[name="message"]').val()).select();
					document.execCommand("copy");
					$temp.remove();
					getLoomUrl()
					return;
				} else if(response.redirect_url) {
					$('.review-container-step-1').hide();
					$('.review-container-step-2').hide();
					$('.review-container-step-3').hide();
					$('.review-container-step-4').show();
					if(window.location.pathname != response.redirect_url) {
						window.location.href = response.redirect_url;
					}
					return;
				} else {
					$('.review-container-step-1').hide();
					$('.review-container-step-2').hide();
					$('.review-container-step-3').hide();
					$('.review-container-step-4').show();
				}
				// $('.review-container-step-1').show();
				// $('.review-container-step-2').hide();
				// $('.review-container-step-3').hide();
				return;
			},
			error: function(e) {
				console.log('errror', e);
			},
			complete: function() {
				console.log('complete');
			}
		});
	}

	function facebookConnected() {
		FB.login(function(response) {
			if(response.authResponse) {
				console.log('Welcome!  Fetching your information.... ');
				FB.api('/me', {
					fields: 'name, id, picture, email,first_name,last_name,link'
				}, function(response) {
					//   console.log(response);
					$('input[name="name"]').parent().append('<input type="hidden" name="facebook_id" value="' + response.id + '">');
					// console.log(response);
					$('input[name="name"]').val(response.name).attr('readonly', true);
					$('input[name="name"]').siblings('.input-group-addon ').addClass('verified').append('<i class="fa fa-check-circle" aria-hidden="true"></i>');
					$('input[name="name"]').closest('.form-group').addClass('has-success');
					if(response.email) {
						$('input[name="email"]').val(response.email).attr('readonly', true);
						$('input[name="email"]').siblings('.input-group-addon').addClass('verified email').append('<i class="fa fa-check-circle verified" aria-hidden="true"></i>');
						$('input[name="email"]').closest('.form-group').addClass('has-success');
					}
					var h = '<div class="fbVerified">';
					if(response.picture.data.url) {
						h += '<div class="user-photo"><img class ="fbimage" src="' + response.picture.data.url + '"><i class="fa fa-check-circle" aria-hidden="true" ></i></div>';
					}
					//  h+='<i class="fa fa-facebook" aria-hidden="true">[VERIFIED]</i>';
					//   h+='<img class ="verified-logo" src="/assets/images/fb_verified.png">';
					//  h+='<img class ="verified-logo" src="/assets/images/facebook-logo-verified2.png">';
					$('.FBVerify').parent().html(h);
					h += '</div>';
					$('.drag-container').hide();
				});
			} else {
				console.log('User cancelled login or did not fully authorize.');
			}
		}, {
			scope: 'public_profile,email'
		});
		return true;
	};
	$(function() {
		origin_text = $('.drag-container').find("h3").html();
		// preventing page from redirecting
		$(".drag-container").on("dragover", function(e) {
			e.preventDefault();
			e.stopPropagation();
			$('.drag-container').find("h3").text("Drag here");
		});
		// Drag enter
		$('.drag-container .upload-area').on('dragenter', function(e) {
			e.stopPropagation();
			e.preventDefault();
			$('.drag-container').find("h3").text("Drop");
		});
		// Drag over
		$('.drag-container .upload-area').on('dragover', function(e) {
			e.stopPropagation();
			e.preventDefault();
			$('.drag-container').find("h3").text("Drop");
		});
		// Drop
		$('.drag-container .upload-area').on('drop', function(e) {
			e.stopPropagation();
			e.preventDefault();
			$('.drag-container').find("h3").text("Uploading...");
			var file = e.originalEvent.dataTransfer.files[0];
			uploadData(file);
		});
		// Open file selector on div click
		$("#uploadfile").click(function() {
			$("#file").click();
		});
		// file selected
		$("#file").change(function() {
			$('.drag-container .upload-area h3').text("Uploading...");
			var file = $('#file').prop('files')[0];
			uploadData(file);
		});
	});

	function uploadData(file) {
		if(typeof file !== 'undefined') {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('.user-photo').html('<img src="' + e.target.result + '">');
			}
			reader.readAsDataURL(file);
		}
		$('.drag-container .upload-area h3').html(origin_text);
	}
	</script>
	<?=$template_footer?>
		<?php

     // echo Tools::console_log($db->isUserCurrentPlanDefault($data['user_id']));
     // echo Tools::console_log($data);

     ?>
</body>

</html>