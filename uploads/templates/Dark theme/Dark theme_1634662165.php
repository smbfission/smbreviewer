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
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet"> 
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
	<style media="screen">

	#review_form div.stars {
		width: min-content;
		display: inline-flex;
		flex-direction: row-reverse;
		justify-content: center;
	}
	
	#review_form input.star {
		display: none;
	}
	
	#review_form label.star {
		float: right;
		margin: 0 5px;
		transition: transform .2s;
		cursor: pointer;
	}

	#review_form label.star > svg{
		fill: #e5e5e5;
	}
	
	#review_form input.star:checked ~ label.star > svg {
		fill: #FFE605;
		transition: fill .25s, filter .25s;
	}
	
	#review_form input.star-5:checked ~ label.star > svg {
		filter: drop-shadow( 0px 5px 15px #FFE6056f);
	}
	
	#review_form input.star-1:checked ~ label.star > svg {
		fill: #FF5C05;
	}
	
	#review_form label.star:hover {
		transform: scale(1.3);
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
	
	.areviewer-reviews .areviews-body {
		min-height: 80px;
		padding: 20px 0;
	}
	
	.areviewer-reviews button {
		width: 100%;
		font-size: 0.9rem;
		white-space: normal;
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
	
	.primary-font-color {
		color: <?=$data['primary_font_color']?>;
	}
	
	.primary-font-family {
		font-family: <?=$data['primary_font_family']?>;
	}
	
	.secondary-font-color {
		color: <?=$data['secondary_font_color']?>;
	}
	
	.secondary-font-family {
		font-family: <?=$data['secondary_font_family']?>;
	}
	
	html *{
		font-family: 'Montserrat';
		font-weight: 400;
		color: #141414;
	}

	body{
		background-color: #F0F0F0;
		display: flex;
		flex-direction: column;
		width: 100vw;
		height: 100vh;
		justify-content: center;
		align-items: center;
		overflow: auto;
	}

	.refokus-logo{
		font-size: 4rem;
		font-weight: 400;
	}

	.refokus-logo-o{
		color: #FF5C05;
	}

	.container{
		display: flex;
		align-items: center;
		width: 100vw;
		height: 100vh;
		overflow: scroll;
		margin: 0;
		padding: 16px 0 16px 0;
	}

	/* conatiner */
	.reviews {
		border-radius: 12px;
		padding: 32px;
		background-color: #fff;
		background: #fff;
		float: none;
		width: 640px;
		height: min-content;
		min-height: min-content;
		display: flex;
		justify-content: center;
		box-shadow: 0 5px 15px #0004;
		margin: auto;
	}
	
	.reviews >div {
		width: 100%;
	}

	.review-container-step-1{
		display: flex;
		flex-direction: column;
		height: min-content;
	}
	
	.title-container{
		width: 80%;
		margin: 0 auto;
		text-align: center;
		padding: 0 16px;
	}
	
	.leftside-title-container{
		width: 96%;
		text-align: left;
		padding: 0 16px;
	}

	.leftside-title-container > .desc{
		text-indent: 10px;
	}

	.title{
		font-size: 22px;
		font-weight: 500;
		margin-top: 28px;
		margin-bottom: 10px
	}
	
	.desc{
		margin-top: 0;
		padding: 0 10px;
		font-size: 16px;
		font-weight: 300;
	}
	
	.form {
		padding: 0 16px;
		display: flex;
		flex-direction: column;
	}

	.form > div{
		margin-top: 28px;
		margin-bottom: 0;
	}

	.field-title{
		font-size: 16px;
		margin-bottom: 10px;
	}

	.field-input-group{
		width: max-content;
		margin: 0 auto;
	}

	.text-input-group{
		position: relative;
		width: 372px;
		margin: 0 auto;
	}
	
	.input-icon-container{
		position: absolute;
		top: 0;
		left: 0;
		display: flex;
		width:	60px;
		height: 100%;
		padding: 0;
		background-color: transparent;
		z-index: 3;
		justify-content: center;
		align-items: center;
		border: none;
	}

	.input-icon-container > svg {
		stroke: #141414;
		fill: transparent;
	}

	.text-input{
		padding: 12px;
		border: none;
		border-radius: 50px;
		box-shadow: 0 5px 15px #0002;
		width: 100%;
		padding-left: 60px;
		font-size: 16px;
		color: #141414;
	}

	.text-input::placeholder{
		color: #5B5B5B;
		opacity: 1;
	}

	input:focus, textarea:focus{
		outline: 1px solid #1974EC;
	}

	.text-input:placeholder-shown + .input-icon-container > svg {
		stroke: #5B5B5B !important; 
	}

	.input-sub-text{
		display: flex;
		font-size: 14px;
		margin-top: 10px;
		margin-left: 10px;
		align-items: center;
	}

	.fb-verify{
		display: flex;
		justify-content: center;
		align-items: center;
		background-color: #1974EC;
		border-radius: 50%;
		border: none;
		width: 24px;
		height: 24px;
		margin-left: 10px;
	}

	.drag-container {
		width: max-content;
		margin: 0 auto;
	}

	.upload-area{
		display: flex;
	}

	.image-upload-container{
		position: relative;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 84px;
		height: 84px;
		box-shadow: 0 5px 15px #0002;
		border-radius: 10px;
		cursor: pointer;
	}

	.image-upload-container > svg{
		fill: transparent;
		stroke: #5B5B5B;
		transition: stroke .2s;
	}

	.image-upload-container:hover > svg{
		stroke: #1974EC;
	}

	.user-photo img{
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		border-radius: 10px;
		object-fit: cover;
	}

	.upload-area > .field-title{
		width: 200px;
		text-align: left;
		margin: 10px;
		margin-right: 0;
		font-weight: 300;
		font-size: 14px;
	}

	.star-review-container{
		display: flex;
		flex-direction: column;
		width: fit-content;
		align-items: center;
		margin: auto;
	}

	.star-review-container > .field-title{
		display: block;
		width: min-content;
		white-space: nowrap;
	}

	.record-review-container{
		display: flex;
		flex-direction: column;
		align-items: center;
		height: min-content;
	}

	#loom-record-button{
		display: flex;
		background-color: transparent;
		border: none;
		border-radius: 10px;
		box-shadow: 0 5px 15px #0002;
		padding: 16px;
	}

	#loom-record-button > .round-recording-icon{
		background-color: #F84343;
		width: 18px;
		height: 18px;
		margin-right: 10px;
		border-radius: 100%;
	}

	#embed-container{
	    width: 100%;
	    margin: 0 auto;
		margin-top: 10px;
	}

	#embed-container:empty{
		margin-top: 0;
	}

	.text-review-container{
		display: flex;
		flex-direction: column;
	}
	
	.text-area{
		display: block;
		border: none;
		resize: none;
		border-radius: 10px;
		box-shadow: 0 5px 15px #0002;
		padding: 14px;
		widht: 100%;
		height: 150px;
		font-size: 14px;
		font-weight: 300;
	}

	.uneditable{
		color: #424242;
	}

	.reward-notice{
		font-weight: 300;
		text-align: center;
		font-size: 16px;
		padding: 0 10px;
		margin-bottom: 28px;
	}

	.submit-button{
		display: inline-block;
		background-color: #1974EC; 
		padding: 12px;
		border-radius: 50px;
		box-shadow: 0 5px 15px #1974EC4f;
		width: 372px;
		border: none;
		transition: box-shadow .2s;
		margin: 0 auto;
	}

	.submit-button > b{
		font-size: 16px;
		color: #fff;
		margin: 0 auto;
		font-weight: 400;
	}

	.submit-button:hover{
		box-shadow: 0 5px 15px #1974EC6f;
	}

	.next-button-container{
		margin-top: 28px;
	}

	.loader {
		width: 20px;
		height: 20px;
		border-radius: 100%;
		position: relative;
		margin: 0 auto;
	}

	.review-text-share{
		text-align: left;
	}

	.list-container{
		overflow-x: auto;
		height: 100%;
		margin: 0 !important;
	}
	
	.list-container ~ button{
		margin-top: 28px;
	}

	.link-box-list{
		display: flex;
		justify-content: center;
		gap: 28px;
		margin: auto;
		width: max-content;
	}

	.link-box-list > li:first-child{
		margin-left: 15px;
	}

	.link-box-list > li:last-child{
		margin-right: 15px;
	}

	.link-box-list > li {
		box-shadow: 0 5px 15px #0002;
		width: 130px;
		height: 130px;
		justify-content: center;
		align-items: center;
		border-radius: 10px;
		margin: 15px 0;
	}

	.link-box-list a {
		margin-top: 4%;
		width: 100%;
		height: 100%;
		display: flex;
		flex-direction: column;
		font-size: 11px;
		justify-content: center;
		align-items: center;
		gap: 10px;
		font-weight: 500;
		color: #5B5B5B;
		text-decoration: none;
	}

	.link-box-list svg {
		width: 45%;
		height: 45%;
	}

	.link-box-list .google-my-business-svg {
		fill: #5B5B5B;
	}

	.link-box-list .facebook-page-svg {
		fill: transparent;
		stroke: #5B5B5B;
	}

	.link-box-list .facebook-inner-svg-letter{
		fill: #5B5B5B;
	}

	.link-box-list .yelp-page-svg {
		fill:  #5B5B5B;
	}

	.link-box-list > li:hover > a {
		color: #1974EC;
	}

	.link-box-list > li:hover .google-my-business-svg{
		fill: #1974EC;
	}

	.link-box-list > li:hover .facebook-page-svg{
		stroke: #1974EC;
	}

	.link-box-list > li:hover .facebook-inner-svg-letter{
		fill: #1974EC;
	}

	.link-box-list > li:hover .yelp-page-svg{
		fill: #1974EC;
	}

	.footer-span{
		margin-top: 14px;
	}

	.copyright{
		font-size: 12px;
		font-weight: 300;
		color: #5b5b5b;
	}

	.stage{
		margin: 0 !important;
	}

	.stage > div{
		margin-top: 28px !important;

	}

	.navigation-container{
		width: 640px;
		background-color: #fff;
		padding: 32px;
		box-shadow: inset 0 15px 15px -15px #0004;
		margin: -32px;
		margin-top: 28px;
		border-radius: 0 0 12px 12px;
	}

	.navigation {
		display: flex;
		justify-content: center;
	}

	.nav-item{
		margin: 0 30px;
		cursor: pointer;
	}

	.nav-active  .nav-circle{
		background-color:  #1974EC80
	}

	.nav-completed  .nav-circle{
		background-color:  #1974EC
	}

	.nav-circle{
		width: 16px;
		height: 16px;
		background-color: #E5E5E5;
		border-radius: 100%;
		margin: 0 auto;
		margin-top: 8px;
	}

	@media only screen and (max-width: 640px) {

		.container{
			padding: 0;
			background-color: #fff;
		}

		.reviews {
			width: 100vw;
			border-radius: 0;
			box-shadow: none;
			padding: 8vw;
		}

		.refokus-logo{
			font-size: 3rem;
		}

		.title-container{
			width: 100%;
			/* padding: 0 8vw; */
		}

		.title{
			font-size: 1.75rem;
			margin-top: 20px;
		}

		.desc{
			padding: 0;
		}

		.form{
			padding: 0;
		}
	
		.text-input-group{
			width: 84vw;
		}

		.submit-button{
			width: 84vw;
			margin-bottom: 28px
		}
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
	
	button[type="submit"] {
		display: inline-flex;
		align-items: center;
	}
	
	h1 {
		font-size: 2.5rem;
	}
	
	.review-container-step-3 h2 {
		padding: 1rem;
	}
	
	.review-container-step-3 ul {
		padding: 0rem;
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
	
	.share-social-media .fa{
		color: #fff;
	}

	.share-social-media svg *{
		color: #fff !important;
	}

	.video-container{
		width: 80%;
		margin: 0 auto;
	}

	.video-responsive {
		overflow: hidden;
		margin: 0 auto;
		width: 100%;
		aspect-ratio: 16/9;
	}
	
	.img-responsive{
		display: block;
		width: 40%;
		margin: 0 auto;
		margin-top: 28px;
	}

	.review-container-step-4 h4 {
		padding: 1em;
	}
	
	#loom-container {
	    width: 100%;
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
    
    // var_dump($reviewData);
?>

<body>
	<div class="container">
		<div class="col-md-8 reviews ">
			<!-- starting step ! -->
			<div class="review-container-step-1">
				<div class="stage-business stage" id="stage-business"> 
					<h2 class='refokus-logo'><?=$data['name_of_business']?></h2>
					<?php if (trim($data['youtube'])!=''): ?> 
					<div class='video-container'>
						<iframe class="video-responsive" src="<?=trim($data['youtube'])?>" frameborder="0" allowfullscreen></iframe>
					</div>
					<?php else: ?>
						<?php if (trim($data['logo'])!=''): ?> 
							<img class="img-responsive center-block" src="/uploads/capture_reviews/<?=$data['logo']?>" alt="">
						<?php endif; ?>
					<?php endif; ?>
					<div class="title-container">
						<h1 class="text-center primary-font-color primary-font-family title"><?=$data['page_title']?></h1> 
						<h3 class="secondary-font-color secondary-font-family desc">
							<?=$data['description']?>
						</h3>
					</div>
					<div class="text-center next-button-container">
						<button type="button"  class="submit-button" name="button" onClick="GoToStage(1)"><b>INPUT REVIEWER INFORMATION</b></button>
					</div>
				</div>
				<form class="form" id="review_form" method="post">
				<div class="stage-reviewer stage hidden" id="stage-reviewer">
					<div class="title-container">
						<h1 class="text-center primary-font-color primary-font-family title">Reviewer Information</h1> 
						<h3 class="secondary-font-color secondary-font-family desc">
							Please input your name and optionally upload an image
						</h3>
					</div>
					<div class="form-group field-input-group">
						<div class="text-input-group">
							<input class="text-input" type="text" name="name" value="" placeholder="Your Name"  data-toggle="popover" data-placement="bottom" >
							<div class="input-group-addon input-icon-container">
								<svg xmlns="http://www.w3.org/2000/svg" width="18" height="20" viewBox="0 0 18 20">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 19v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 9a4 4 0 100-8 4 4 0 000 8z"/>
								</svg>
							</div>
						</div>
						<label for="name" class='input-sub-text'>or verify with facebook
							<button  type="button"  class="fb-verify" onclick="facebookConnected();">
							<svg width="9" height="16" viewBox="0 0 9 16" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M9 0H6.54545C5.46048 0 4.41994 0.421427 3.65275 1.17157C2.88555 1.92172 2.45455 2.93913 2.45455 4V6.4H0V9.6H2.45455V16H5.72727V9.6H8.18182L9 6.4H5.72727V4C5.72727 3.78783 5.81347 3.58434 5.96691 3.43431C6.12035 3.28429 6.32846 3.2 6.54545 3.2H9V0Z" fill="white"/>
							</svg>
							</button>
						</label>
                      </div>
                     <div class="drag-container" >
                         <input id="file" type="file" name="file" class="hidden" accept=".jpg, .jpeg, .png" />
                         <div class="upload-area text-center"  id="uploadfile">
							 <div class='image-upload-container'> 
								<svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M36.434 24.811v7.749a3.874 3.874 0 01-3.874 3.874H5.44a3.874 3.874 0 01-3.874-3.874V24.81M28.686 11.252L19 1.566l-9.686 9.686M19 1.566v23.245"/>
								</svg>
								<div class="user-photo"></div>
							 </div>
                             <h3 class='field-title'>Drag and Drop Your Photo Here or Click to Select an Image</h3>
                         </div>
                    </div>
					<div class="text-center next-button-container">
						<button type="button" class="submit-button" name="button" onClick="GoToStage(2)"><b>INPUT REVIEW</b></button>
					</div>
				</div>
				<div class="stage-review stage hidden" id="stage-review">
					<div class="title-container">
						<h1 class="text-center primary-font-color primary-font-family title">Review</h1> 
						<h3 class="secondary-font-color secondary-font-family desc">
							Please input the desired rating and review
						</h3>
					</div>
					<div class='star-review-container'>
						<h2 class='field-title'>Your rating</h2>
						<div class="stars"  data-toggle="popover" data-placement="bottom">
							<input class="star star-5" id="review_form-star-5" type="radio" name="star" value="5"/>
							<label class="star star-5" for="review_form-star-5"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="26" fill="currentColor" viewBox="0 0 28 26">
									<path d="M14 0l3.143 9.674h10.172l-8.23 5.979 3.144 9.673L14 19.347l-8.229 5.98 3.143-9.675L.685 9.675h10.172L14 0z"/>
								</svg></label>
							<input class="star star-4" id="review_form-star-4" type="radio" name="star" value="4"/>
							<label class="star star-4" for="review_form-star-4"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="26" fill="currentColor" viewBox="0 0 28 26">
									<path d="M14 0l3.143 9.674h10.172l-8.23 5.979 3.144 9.673L14 19.347l-8.229 5.98 3.143-9.675L.685 9.675h10.172L14 0z"/>
								</svg></label>
							<input class="star star-3" id="review_form-star-3" type="radio" name="star" value="3"/>
							<label class="star star-3" for="review_form-star-3"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="26" fill="currentColor" viewBox="0 0 28 26">
									<path d="M14 0l3.143 9.674h10.172l-8.23 5.979 3.144 9.673L14 19.347l-8.229 5.98 3.143-9.675L.685 9.675h10.172L14 0z"/>
								</svg></label>
							<input class="star star-2" id="review_form-star-2" type="radio" name="star" value="2"/>
							<label class="star star-2" for="review_form-star-2"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="26" fill="currentColor" viewBox="0 0 28 26">
									<path d="M14 0l3.143 9.674h10.172l-8.23 5.979 3.144 9.673L14 19.347l-8.229 5.98 3.143-9.675L.685 9.675h10.172L14 0z"/>
								</svg></label>
							<input class="star star-1" id="review_form-star-1" type="radio" name="star" value="1"/>
							<label class="star star-1" for="review_form-star-1"> 
								<svg xmlns="http://www.w3.org/2000/svg" width="28" height="26" fill="currentColor" viewBox="0 0 28 26">
									<path d="M14 0l3.143 9.674h10.172l-8.23 5.979 3.144 9.673L14 19.347l-8.229 5.98 3.143-9.675L.685 9.675h10.172L14 0z"/>
								</svg>
							</label>
						</div>
					</div>

                    <!-- Loom implementation -->
                    <div class="form-group record-review-container <?=($data['type']=='text' || $canRecordReview == false? 'hidden':'')?>" id="loom-container">
                        <label for="loom-video-url" class='field-title'>Record Your Review <i style="font-size: 10pt; font-weight: 100">(best used on desktop)</i></label>
                        <input type="hidden" id="loom-video-url" name="loomUrl" value="">
                        <button type="button" id="loom-record-button"><div class='round-recording-icon'></div>Click to record</button>
                        <div id="embed-container"></div>
                    </div>

                    <div class="form-group text-review-container">
						<label class="<?=($data['type']=='video' && $canRecordReview == true ? 'hidden':'')?> field-title" for="message">Your Review</label>
						<label class="<?=($data['type']=='text' || $canRecordReview == false ? 'hidden':'')?> field-title" for="message">Additional comments</label>
						<textarea name="message" rows="8" class="text-area"  data-toggle="popover" data-placement="bottom" class=''></textarea>
                    </div>
					<?php if ($data['reward'] !== ''): ?> 
						<div class="text-center next-button-container">
							<button type="button" class="submit-button" name="button" onClick="GoToStage(3)"><b>VIEW REWARD OFFER</b></button>
						</div>
					<?php else: ?>
						<div class="text-center">
								<button type="submit" class="submit-button" id="submit-button-first-step" name="button"><div class="loader hidden"></div><b>SUBMIT REVIEW</b></button>
						</div>
					<?php endif; ?>


				</div>
				<div class="stage-reward stage hidden" id="stage-reward">
						<?php if ($data['reward'] !== ''): ?> 
						<div class="title-container">
							<h1 class="text-center primary-font-color primary-font-family title">Reward</h1> 
							<h3 class="secondary-font-color secondary-font-family desc">
							<?=$data['reward']?>
							</h3>
						</div>	
						<div class="form-group">
							<div class="text-input-group">
								<input class="text-input" type="text" name="email" value="" placeholder="Your Email"  data-toggle="popover" data-placement="bottom" >
								<div class="input-group-addon input-icon-container">
									<svg xmlns="http://www.w3.org/2000/svg" width="18" height="20" viewBox="0 0 18 20">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 19v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 9a4 4 0 100-8 4 4 0 000 8z"/>
									</svg>
								</div>
							</div>
						</div>

						<input name="capture_reviews_token" type="hidden" value="<?= $form_token; ?>">
						<div class="text-center">
								<button type="submit" class="submit-button" id="submit-button-first-step" name="button"><div class="loader hidden"></div><b>SUBMIT REVIEW</b></button>
						</div>

						<span class="areviewer-form-error text-danger"></span>
						<span class="text-center text-muted col-md-12 footer-span">
							<p class="copyright">&copy; Copyright <?=date('Y')?>. All rights reserved.  <a href="https://www.smbreviewer.com" target="_blank">SMBreviewer</a>.</p>
						</span>
						<?php endif; ?>
						</form>
					</div>
				<div class="navigation-container">
					<nav class="navigation" id='stage-navigation'>
						<div onClick="GoToStage(0)" class="nav-item nav-active">business <div class='nav-circle'></div></div>
						<div onClick="GoToStage(1)" class="nav-item">reviewer <div class='nav-circle'></div></div>
						<div onClick="GoToStage(2)" class="nav-item">review <div class='nav-circle'></div></div>
						<?php if ($data['reward'] !== ''): ?> 
							<div onClick="GoToStage(3)" class="nav-item">reward <div class='nav-circle'></div></div>
						<?php endif; ?>
					</nav>
				</div>
             </div>
          <!-- ending step ! -->


             <!-- starting step 2 -->
             <div class=" review-container-step-2">

               <?php if (trim($data['logo'])!=''): ?>
				<img class="img-responsive center-block" src="/uploads/capture_reviews/<?=$data['logo']?>" alt="">
                 <?php else: ?>
				<h2 class='refokus-logo'><?=$data['name_of_business']?></h2>
               <?php endif; ?>

				<div class='leftside-title-container'> 
					<h2 class="primary-font-color primary-font-family title">Please Provide Your Feedback </h2>
					<p class="secondary-font-color secondary-font-family">
					<h3 class="secondary-font-color secondary-font-family desc">Thank you so much for your review, are there any specific comments or feedback that you would like to send to management regarding your experience, and how we could have improved your experience and your review of us?</h3></p>
				</div>
					<form class="form" id="feedback_form" method="post">
						<div class="form-group text-review-container">
							<label for="message" >
								<h3 class='field-title'>Enter Your Feedback Message</h3> </label>
							<textarea name="message" rows="8" class="text-area" data-toggle="popover" data-placement="bottom"></textarea>
						</div>
						<div class="form-group text-review-container review-text-feedback">
							<label for="message" class='field-title'>Your Review</label>
							<p class="text-area uneditable"></p>
						</div>
						<input name="capture_reviews_token" type="hidden" value="<?= $form_token; ?>">
						<div class="text-center">
							<button type="submit" class="submit-button" name="button">
								<div class="loader hidden"></div> <b>SUBMIT FEEDBACK</b></button>
						</div> <span class=" areviewer-form-error text-danger"></p>
               </form>
			   <span class="text-center text-muted col-md-12 footer-span">
				   <p class="copyright">&copy; Copyright <?=date('Y')?>. All rights reserved.  <a href="https://www.smbreviewer.com" target="_blank">SMBreviewer</a>.</p>
				   <!-- <?=$data['footer_text']?> -->
				</span>
             </div>

          <!-- ending step 2 -->


             <!-- starting step 3 -->

			 <div class=" review-container-step-3">
				<h2 class='refokus-logo'><?=$data['name_of_business']?></h2>
				<div class="leftside-title-container">
						<h1 class="title">Will You Do Us a Favor?</h1> 
						<h3 class="secondary-font-color secondary-font-family desc">
							<b><span style="text-decoration:underline;font-weight:bold;"></span>Your review has been copied to your clipboard</b>, 
                             if you would be graciously willing, could you share your experience with other people like yourself by pasting your review in the review directory listed below? It's a great way to help other people like yourself and we would greatly 
                             appreciate it!
						</h3>
					</div>
			<div class="col-12">
				<form class="form text-center" id="share_form" method="post">
					<div class="col-md-12 review-text-share <?= isset($reviewData['type']) && $reviewData['type'] == 'video' ? '':'hidden' ?>">
						<label class='field-title'>
							Share your testimonial
						</label>
						<div id="embed-container-step-3" style="margin-top: 20px"></div>
						<p class="text-area uneditable review-text"></p>
						<div>
							<h3 class="reward-notice">Let Your Friends, Family, and Network know about Good Quality People. Click Below to Share Your Testimonial with Your Network.</h3>
						</div>
					</div>
					<!-- <?= isset($reviewData['type']) && $reviewData['type'] == 'text' ? '':'hidden' ?> -->
					<div class="col-md-12 review-text-share h">
						<label class='field-title'>
						   Your Review
						</label>
                        <p class="text-area uneditable review-text"></p>
                        <div ><h3 class="reward-notice">Click on the review directory below of your choice to be directed to our page.</h3></div>
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
					<div class='list-container'>
						<ul class="list-inline text-center center-block link-box-list <?=isset($reviewData['type']) && $reviewData['type'] == 'text' ? '':'hidden' ?>">
							<!-- <?= isset($reviewData['type']) && $reviewData['type'] == 'text' ? '':'show' ?> -->
							<?php if ($data['enable_review_directories_google']==1 && trim($data['review_directories_google'])!=''): ?>
								<li>
									<a target="_blank" href="http://www.g.page/<?=trim($data['review_directories_google'])?>/review">
									<svg class='google-my-business-svg' xmlns="http://www.w3.org/2000/svg" width="52" height="53" viewBox="0 0 52 53">
										<path d="M51.83 17.44c0 3.54-2.894 6.46-6.46 6.46-3.564 0-6.458-2.92-6.458-6.46 0 3.54-2.894 6.46-6.46 6.46-3.565 0-6.458-2.92-6.458-6.46 0 3.54-2.894 6.46-6.46 6.46-3.565 0-6.459-2.92-6.459-6.46 0 3.54-2.893 6.46-6.459 6.46-3.565 0-6.46-2.92-6.46-6.46L3.749 3.438s.75-2.79 3.385-2.79h37.721c2.636 0 3.385 2.79 3.385 2.79l3.591 14.004zM49.246 27v20.153c0 2.842-2.325 5.167-5.167 5.167H7.908c-2.842 0-5.167-2.325-5.167-5.167V27a10.232 10.232 0 0010.334-1.498 10.342 10.342 0 006.46 2.274c2.454 0 4.702-.853 6.459-2.274a10.342 10.342 0 006.459 2.274c2.454 0 4.702-.853 6.459-2.274a10.287 10.287 0 006.459 2.274c1.37 0 2.687-.285 3.875-.776zM44.08 40.255c0-.517 0-1.06-.13-1.628l-.077-.413H36.2v3.022h4.676c-.155.569-.361 1.137-.8 1.602-.853.853-2.016 1.318-3.256 1.318-1.292 0-2.558-.543-3.488-1.447-1.783-1.834-1.783-4.806.052-6.666 1.782-1.808 4.728-1.808 6.588-.077l.362.336 2.17-2.197-.413-.361a7.876 7.876 0 00-5.374-2.093h-.026a7.84 7.84 0 00-5.53 2.248c-1.523 1.498-2.376 3.462-2.376 5.503 0 2.067.8 3.979 2.274 5.4a8.267 8.267 0 005.735 2.351h.052c2.067 0 3.901-.75 5.245-2.067 1.214-1.24 1.99-3.1 1.99-4.831z"/>
									</svg>
									Google My Business
									</a>
								</li>
								<?php endif; ?>
									<?php if ($data['enable_review_directories_facebook']==1 && trim($data['review_directories_facebook'])!=''): ?>
										<li>
											<a target="_blank" href="https://www.facebook.com/<?=trim($data['review_directories_facebook'])?>/reviews">
												<svg class='facebook-page-svg' xmlns="http://www.w3.org/2000/svg" width="52" height="53" viewBox="0 0 52 53">
													<g clip-path="url(#clip0)">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="5" d="M4.426 50.167a2.28 2.28 0 01-2.273-2.285V5.086A2.279 2.279 0 014.426 2.8h42.823a2.278 2.278 0 012.271 2.286v42.796a2.277 2.277 0 01-2.272 2.285H4.424h.002z"/>
														<path class='facebook-inner-svg-letter' fill-rule="evenodd" d="M41.62 19.903h-5.241a2.631 2.631 0 00-2.631 2.63v5.265h7.872l-1.152 7.893h-6.72v14.476H25.09V35.691H17.94V27.8h7.057l.091-7.491-.025-2.685a6.867 6.867 0 016.865-6.93h9.69v9.21z" clip-rule="evenodd"/>
													</g>
													<defs>
														<clipPath id="clip0">
														<path d="M0 0h51.673v51.673H0z" transform="translate(0 .647)"/>
														</clipPath>
													</defs>
												</svg>
												Facebook Page
											</a>
										</li>
										<?php endif; ?>
											<?php if ($data['enable_review_directories_yelp']==1 && trim($data['review_directories_yelp'])!=''): ?>
												<li>
													<a target="_blank" href="https://www.yelp.com/writeareview/biz/<?=trim($data['review_directories_yelp'])?>">
										
														<svg class='yelp-page-svg' xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="currentColor" viewBox="0 0 52 52">
															<path d="M23.92 31.447l-3.593 4.373a1.173 1.173 0 00.476 1.838l3.512 1.381a1.172 1.172 0 001.602-1.06l.153-5.728a1.215 1.215 0 00-2.153-.804h.003zm-.598-4.347l-5.33-1.914a1.173 1.173 0 00-1.568 1.066l-.12 3.773a1.173 1.173 0 001.537 1.152l5.447-1.781a1.214 1.214 0 00.034-2.296zm6.105.613l5.457-1.505a1.173 1.173 0 00.668-1.776L33.48 21.28a1.173 1.173 0 00-1.924-.055l-3.424 4.594a1.214 1.214 0 001.295 1.895zm-3.952-13.111a1.391 1.391 0 00-1.758-1.407L19.4 14.373a1.39 1.39 0 00-.77 2.147l4.953 8.586c.04.07.086.137.138.2a1.443 1.443 0 002.452-.468c.058-.182.08-.374.062-.564l-.762-9.672h.003zm9.446 17.035l-5.479-1.682a1.214 1.214 0 00-1.352 1.859l3.25 4.633c.46.65 1.42.668 1.898.031l2.275-3.01a1.171 1.171 0 00-.592-1.831z"/>
															<path class='yelp-inner-svg-inner' d="M10.4 5.2a5.2 5.2 0 00-5.2 5.2v31.2a5.2 5.2 0 005.2 5.2h31.2a5.2 5.2 0 005.2-5.2V10.4a5.2 5.2 0 00-5.2-5.2H10.4zm0-5.2h31.2A10.4 10.4 0 0152 10.4v31.2A10.4 10.4 0 0141.6 52H10.4A10.4 10.4 0 010 41.6V10.4A10.4 10.4 0 0110.4 0z"/>
														</svg>
														Yelp Review
												</a>
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
					</div>
					<input name="capture_reviews_token" type="hidden" value="<?= $form_token; ?>">
					<button type="submit" class="submit-button" name="button">
						<div class="loader hidden"></div><b>No Thanks</b></button>
				<span class="text-center text-muted col-md-12 footer-span">
					<p class="copyright">&copy; Copyright <?=date('Y')?>. All rights reserved.  <a href="https://www.smbreviewer.com" target="_blank">SMBreviewer</a>.</p>
					<!-- <?=$data['footer_text']?> -->
				</span>
				</form>
			</div>
			
		</div>
		<!-- ending step 3 -->
		<!-- starting step 4 -->
		<div class=" review-container-step-4">
			<h2 class="primary-font-color primary-font-family text-center title">Thank You for Your Review!</h2>
			<?php if (trim($data['redirect_url'])!=''): ?>
				<h4 class="secondary-font-color secondary-font-family text-center desc">Please wait while we redirect you.</h4>
			<?php endif; ?>
			<span class="text-center text-muted col-md-12 footer-span">
				<p class="copyright">&copy; Copyright <?=date('Y')?>. All rights reserved.  <a href="https://www.smbreviewer.com" target="_blank">SMBreviewer</a>.</p>
				<!-- <?=$data['footer_text']?> -->
			</span>
				
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
	function GoToStage(stageIndex) {
		const stages ={
			0: document.getElementById('stage-business'),
			1: document.getElementById('stage-reviewer'),
			2: document.getElementById('stage-review'),
			3: document.getElementById('stage-reward')
		}
		const navigation = document.getElementById('stage-navigation')

		// reset navigation
		for(let i = 0; i < navigation.children.length; i++){
			const navItem = navigation.children[i];
			const stage = stages[i];
			stage.classList.add('hidden')
			navItem.classList.remove('nav-active', 'nav-completed')
			console.log(navItem)
		}

		for (let i = 0; i <= stageIndex; i++) {
			const navItem = navigation.children[i];
			const stage = stages[i];
			if(i === stageIndex){
				stage.classList.remove('hidden')
				navItem.classList.add('nav-active')
				return
			}
			navItem.classList.add('nav-completed')
		}
	}

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
					$('#submit-button-first-step').popover({
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
				if(response.step_3) {
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
				} else if(response.step_2) {
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