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
	@import url("https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;900&family=Source+Serif+Pro:ital,wght@0,400;1,300;1,400&display=swap");

:root {
  --cl-black: #122432;
  --cl-white-0: #ffffff;
  --cl-white-1: #f8f9fb;
  --cl-accent: #09f;
}

* {
  box-sizing: border-box;
  padding: 0;
  margin: 0;
}

/* TYPOGRAPHY */

html {
  font-size: 62.5%;
}

body {
  font-size: 2rem;
  font-family: "Source Sans Pro", sans-serif;
  background-color: var(--cl-white-1);
  overflow: auto;
}

h1 {
  font-family: "Source Serif Pro", serif;
  font-style: italic;
  font-size: 3em;
  font-weight: 400;
  text-transform: uppercase;
}

span {
  display: block;
  font-size: 0.75em;
  text-align: center;
  margin: 0.5em 0;
}

button {
  cursor: pointer;
}

textarea {
  resize: none;
}

/* COMPONENTS */

.wrapper {
  max-width: 1920px;
  margin: 0 auto;
}

.btn {
  background-color: var(--cl-accent);
  color: var(--cl-white-0);
  border: none;
  border-radius: 5px;
  padding: 0.5em 1em;

  transition: all 0.1s ease-in-out;
}

.btn:hover {
  transform: scale(95%);
}

.btn--outline {
  background-color: unset;
  color: var(--cl-black);
  border: 1px solid var(--cl-accent);
}

.btn--main {
  background-color: var(--cl-black);
  width: 100%;
  padding: 1em 2em;
  text-transform: uppercase;
  margin-top: 1em;
}

.form__item {
  margin-bottom: 1.5em;
  display: flex;
  flex-direction: column;
}

.form__label {
  display: block;
  margin-bottom: 0.5em;
}

.form__input {
  display: block;
  background-color: var(--cl-white-1);
  border-radius: 5px;
  outline: none;
  border: 1px solid var(--cl-black);

  height: 2.25em;
  width: 100%;
  padding: 0.5em;

  font-family: inherit;
  font-size: 0.85em;
}

/* SECTIONS */

.empty-div--dark {
  height: 50vh;
  width: 100%;
  background-color: var(--cl-black);
  border-radius: 0 0 20px 20px;
  position: fixed;
  z-index: -1;
}

.wrapper {
  display: flex;
}

.aside {
  width: 55%;
  height: 100vh;
  top: 0;
  align-self: flex-start;
  position: sticky;
  display: grid;
  grid-template-rows: repeat(2, 1fr);
}

.title {
  color: var(--cl-white-0);
  padding: 2em;

  grid-row: 1 / 2;
  align-self: end;
  justify-self: end;
  text-align: right;
}

.subtitle {
  grid-row: 2 / 3;
  align-self: center;
  justify-self: end;
  padding: 2em;
  width: 70%;
}

.main {
  width: 45%;
  margin: 4em;
  padding: 2em;

  background-color: var(--cl-white-0);
  border-radius: 25px;
  box-shadow: 0 2.8px 2.2px rgba(0, 0, 0, 0.034),
    0 6.7px 5.3px rgba(0, 0, 0, 0.048), 0 12.5px 10px rgba(0, 0, 0, 0.06),
    0 22.3px 17.9px rgba(0, 0, 0, 0.072), 0 41.8px 33.4px rgba(0, 0, 0, 0.086),
    0 100px 80px rgba(0, 0, 0, 0.12);

  display: flex;
  flex-direction: column;
  align-items: center;
}

.logo {
  height: 4em;
  margin-bottom: 2em;
}

.form__star {
  margin-bottom: 2em;
}
.form__star label:first-child {
  margin-bottom: -1em;
}

.star {
  display: inline-block;
  position: relative;
  height: 50px;
  line-height: 50px;
  font-size: 2.75em;
  text-align: center;
}

.star label {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  cursor: pointer;
}

.star label:last-child {
  position: static;
}

.star label:nth-child(1) {
  z-index: 5;
}

.star label:nth-child(2) {
  z-index: 4;
}

.star label:nth-child(3) {
  z-index: 3;
}

.star label:nth-child(4) {
  z-index: 2;
}

.star label:nth-child(5) {
  z-index: 1;
}

.star label input {
  position: absolute;
  top: 0;
  left: 0;
  opacity: 0;
}

.star label .icon {
  float: left;
  color: transparent;
}

.star label:last-child .icon {
  color: #000;
}

.star:not(:hover) label input:checked ~ .icon,
.star:hover label:hover input ~ .icon {
  color: var(--cl-accent);
}

.star label input:focus:not(:checked) ~ .icon:last-child {
  color: #000;
  text-shadow: 0 0 5px var(--cl-accent);
}

#form-image {
  display: flex;
  flex-direction: column;
  align-items: center;

  border: 1px dashed var(--cl-black);
  border-radius: 5px;
  padding: 2em;

  font-size: 0.85em;
}

#form-image span:first-child {
  margin: 0;
}

.form__record label {
  display: flex;
  justify-content: start;
  flex-wrap: wrap;
  line-height: 1;
}

.form__record span {
  font-size: 0.75em;
  font-style: italic;
}

#record-btn {
  display: flex;
  justify-content: center;
  align-items: center;

  border-radius: 5px;
  border: 1px solid var(--cl-black);

  width: fit-content;
  padding: 1em;
  margin: 0 auto;

  font-size: 1em;

  transition: all 0.1s ease-in-out;
}

#record-btn:hover {
  transform: scale(95%);
}

.dot {
  width: 20px;
  height: 20px;
  margin-right: 0.5em;

  background-color: red;
  border: 0;
  border-radius: 35px;
  outline: none;
}

#form-review {
  height: auto;
}

@media screen and (max-width: 1200px) {
  .wrapper {
    flex-direction: column;
  }

  .aside {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: sticky;
    z-index: -1;
    width: 100%;
    height: auto;
    text-align: center;
  }

  .title {
    width: 100%;
    text-align: center;
  }

  .main {
    width: 80%;
    margin: 0 auto;
  }

  .subtitle {
    color: var(--cl-white-0);
    padding: 0;
    margin-bottom: 4em;
  }
}

@media screen and (max-width: 576px) {
  body {
    font-size: 1.4rem;
  }

  .main {
    padding: 1.5em;
  }
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
		<div class="container">
			<div class="empty-div--dark">
			</div>
			<div class="wrapper">
				<aside class="aside">
					<div class="title">
						<h1><?=$data['page_title']?></h1>
					</div>
					<div class="subtitle">
						<p><?=$data['description']?></p>
					
					</div>
				</aside>
				<div class="main">
					<h2 style="font-weight:bold;" class="text-center primary-font-color primary-font-family"><?=$data['name_of_business']?></h2>
					<?php if (trim($data['youtube'])!=''): ?>
					<div class="video-responsive">
						<iframe width="420" height="315" src="<?=trim($data['youtube'])?>" frameborder="0" allowfullscreen></iframe>
					</div>
					<?php else: ?>
						<?php if (trim($data['logo'])!=''): ?> <img class="img-responsive center-block" src="/uploads/capture_reviews/<?=$data['logo']?>" alt="">
							<?php endif; ?>
								<?php endif; ?>
					<form class="form" id="review_form" method="post">
						<div class="form__item form__star">
							<label class="form__label" for="form-stars">Your Rating</label>
							<div class="star" id="form-stars">
								<label>
									<input type="radio" name="stars" value="1" />
									<span class="icon">★</span>
								</label>
								<label>
									<input type="radio" name="stars" value="2" />
									<span class="icon">★</span>
									<span class="icon">★</span>
								</label>
								<label>
									<input type="radio" name="stars" value="3" />
									<span class="icon">★</span>
									<span class="icon">★</span>
									<span class="icon">★</span>
								</label>
								<label>
									<input type="radio" name="stars" value="4" />
									<span class="icon">★</span>
									<span class="icon">★</span>
									<span class="icon">★</span>
									<span class="icon">★</span>
								</label>
								<label>
									<input type="radio" name="stars" value="5" />
									<span class="icon">★</span>
									<span class="icon">★</span>
									<span class="icon">★</span>
									<span class="icon">★</span>
									<span class="icon">★</span>
								</label>
							</div>
						</div>
						<div class="form__item">
							<label for="form-name" class="form__label">Your Name</label>
							<input type="text" class="form__input" name="name" id="form-name">
							<span>or</span>
							<button class="btn" onclick="facebookConnected();">Verify with Facebook</button>
						</div>
						<div class="form__item">
							<label for="form-image" class="form__label">Your Photo</label>
							<input id="file" type="file" name="file" class="hidden" accept=".jpg, .jpeg, .png" />
							<div  id="form-image" class="upload-area text-center"  id="uploadfile">
								<span>Drag and Drop Here</span>
								<span>or</span>
								<button class="btn btn--outline">Browse Files</button>
							</div>
						</div>
						<div class="form__item form__record" <?=($data['type']=='text' || $canRecordReview == false? 'hidden':'')?>" id="loom-container">
							<label for="form-image" class="form__label">Record Your Video
								<span id="">(best used on desktop)</span>
							</label>
							<input type="hidden" id="loom-video-url" name="loomUrl" value="">
							<button type="button" id="record-btn loom-record-button">
								<div class="dot"></div> Click to record
							</button>
						</div>
						<div class="form__item">
							<label for="form-review" class="form__label <?=($data['type']=='video' && $canRecordReview == true ? 'hidden':'')?>">Your Review</label>
							<textarea type="text" class="form__input" name="review" id="form-review" rows="10"></textarea>
							<label class="<?=($data['type']=='text' || $canRecordReview == false ? 'hidden':'')?>" for="message">Additional comments:</label>
                        	<textarea name="message" rows="8" class="form-control"  data-toggle="popover" data-placement="bottom"></textarea>
						</div>

						<div class="form__item <?=(trim($data['reward'])=='' ? 'hidden':'')?>">
								<label for="form-email" class="form__label">Your Email</label>
								<input type="email" class="form__input" name="email" id="form-email">
								<span><?=$data['reward']?></span>
						</div>
						<input name="capture_reviews_token" type="hidden" value="<?= $form_token; ?>">
						<button class="btn btn--main" id="submit-button-first-step" name="button"> <div class="loader hidden"></div>submit review</button>
					</form>
				</div>
		
			</div>
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