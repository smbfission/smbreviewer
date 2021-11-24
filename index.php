<?php
@session_start();


if (isset($_SESSION['login_user']) && $_SESSION['login_user'] != ""){
header('Location:/campaign.php');
}

?>
<!doctype html>
<html class="fixed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<!-- App favicon -->
        <link rel="shortcut icon" href="/assets/images/favicon.ico">
        <!-- App title -->
        <title>Login &amp; Register | SMBreviewer Free Online Reputation and Free Tool</title>

    <meta name="keywords" content="facebook reviews, google my business reviews, smbreviewer, review management" />
		<meta name="description" content="Embed your reviews for free from Facebook Pages, Google My Business, Yelp and all other custom reviews. Build trust in your brand.">
		<meta name="author" content="SMBreviewer">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<!-- <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.css" /> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<!-- <link rel="stylesheet" href="/assets/vendor/font-awesome/css/font-awesome.css" /> -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
		<!-- <link rel="stylesheet" href="/assets/vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="/assets/vendor/bootstrap-datepicker/css/datepicker3.css" /> -->

		<!-- Theme CSS -->
		<link rel="stylesheet" href="/assets/stylesheets/theme.css" />
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous"> -->

		<!-- Skin CSS -->
		<link rel="stylesheet" href="/assets/stylesheets/skins/default.css" />

		<!-- Theme Custom CSS -->
		<!-- <link rel="stylesheet" href="/assets/stylesheets/theme-custom.css"> -->

		<!-- Head Libs -->
		<!-- <script src="/assets/vendor/modernizr/modernizr.js"></script> -->



	<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-122972834-8"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-122972834-8');
</script>
	<!-- end Global site tag (gtag.js) - Google Analytics -->
	<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '298704808017390');
  fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=298704808017390&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->

	</head>
	<body>
		<!-- start: page -->
		<section class="body-sign">
			<div class="center-sign">
				<a class="logo pull-left" style="font-size:35px;">
                    <img src="/assets/images/logo-smbreviewer2.png" height="35" width="81" alt="SMBreviewer" style="border:0px;" />
				</a>

				<div id="singInPanel" class="panel panel-sign">
					<div class="panel-title-sign text-right">
						<h2 class="title text-uppercase text-bold m-none"><i class="fa fa-user mr-xs"></i> Sign In</h2>
					</div>
					<div class="panel-body">
						<form class="form-horizontal-" action="/login_check.php" method="post">

              <?php
              if (!isset($_SESSION['msg_type'])) {
                  $_SESSION['msg_type']='info';
              }
              if (isset($_SESSION['msg']) && $_SESSION['msg']!="") {
                  ?>

                      <div class="alert alert-<?=$_SESSION['msg_type']?>"><?= $_SESSION['msg'] ?></div>

                  <?php
                  unset($_SESSION['msg']);
                  unset($_SESSION['msg_type']);
              }
              ?>

							<div class="form-group mb-lg">
								<label>Email</label>
								<div class="input-group input-group-icon">
									<input name="email" class="form-control input-lg" type="text" required="" placeholder="Email Address">
									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-user"></i>
										</span>
									</span>
								</div>
							</div>

							<div class="form-group mb-lg">
								<div class="clearfix">
									<label class="pull-left">Password</label>
								</div>
								<div class="input-group input-group-icon">
									<input name="password" class="form-control input-lg" type="password" required="" placeholder="Password">
                                    <input name="action" value="verify_login" type="hidden">

									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-lock"></i>
										</span>
									</span>
								</div>
                <small id="forgotPassword" class="form-text text-muted"><a href="javascript:">Forgot your password?</a></small>
							</div>

							<div class="row">
								<div class="col-sm-8">
									<div class="checkbox-custom checkbox-default">
										<input id="RememberMe" name="rememberme" type="checkbox"/>
										<label for="RememberMe">Remember Me</label>
									</div>
								</div>
								<div class="col-sm-4 text-right">
									<button type="submit" name="submit" class="btn btn-primary hidden-xs">Sign In</button>
									<button type="submit" name="submit" class="btn btn-primary btn-block btn-lg visible-xs mt-lg">Sign In</button>
								</div>
							</div>

							<span class="mt-lg mb-lg line-thru text-center text-uppercase">
								<span>or</span>
							</span>

							<p class="text-center">Don't have an account yet? <a href="/pricing_plans/">Sign Up!</a>

						</form>

					</div>
				</div>

        <div id="forgotPassowrdPanel" class="panel panel-sign" style="display:none;">
          <div class="panel-title-sign text-right">
            <h2 class="title text-uppercase text-bold m-none"><i class="fa fa-refresh mr-xs"></i> Reset passoword</h2>
          </div>
          <div class="panel-body">
        <form action="/core/password_reset.php" class="form-horizontal-" method="post" >

          <div class="alert" style="display:none;" role="alert"></div>

          <div class="form-group mb-lg">
            <label>Enter your email address and we'll send you instructions on how to reset your password.</label>
            <div class="input-group input-group-icon">
              <input name="email" class="form-control input-lg" type="text" required="" placeholder="Email Address">
              <span class="input-group-addon">
                <span class="icon icon-lg">
                  <i class="fa fa-user"></i>
                </span>
              </span>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-5 text-left">
              <button type="button" name="back_to_login" class="btn btn-warning hidden-xs"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to login</button>
              <button type="button" name="back_to_login" class="btn btn-warning btn-block btn-lg visible-xs mt-lg"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back login</button>
            </div>
            <div class="col-sm-7 text-right">
              <button type="submit" name="submit" class="btn btn-primary hidden-xs">Reset</button>
              <button type="submit" name="submit" class="btn btn-primary btn-block btn-lg visible-xs mt-lg">Reset</button>
            </div>
          </div>
        </form>
        <script type="text/javascript">

          tzo = (- new Date().getTimezoneOffset());


        window.onload = function () {

          $('form').submit(function() {
            $(this).append('<input type="hidden" name="tzo" value="'+tzo+'" /> ');
            return true;
          });


            $("#forgotPassword").click(function() {
            $("#singInPanel").hide();
            $("#forgotPassowrdPanel").show();
          });

          $('#forgotPassowrdPanel button[name="back_to_login"').click(function() {
            $("#singInPanel").show();
            $("#forgotPassowrdPanel").hide();
          });

          $("#forgotPassowrdPanel form").submit(function(e) {
              e.preventDefault();
              var form = $(this),
               alert = form.find('.alert'),
               url = form.attr('action');
              alert.hide();
              alert.removeClass('alert-danger');
              alert.removeClass('alert-success');
              alert.html('');

              $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(),
                success: function(data)
                {
                  if (data.success) {
                    if (data.message && data.message!='') {

                      alert.addClass('alert-success');
                      alert.html(data.message);
                      alert.show();

                    }
                  } else{

                    if (data.message && data.message!='') {

                      alert.addClass('alert-danger');
                      alert.html(data.message);
                      alert.show();

                    }
                  }

                }
              });


});


        };
        </script>
      </div>
      </div>


				<p class="text-center text-muted mt-md mb-md" >&copy; Copyright <?=date('Y')?>. All rights reserved.  <a href="https://www.smbrepute.com" target="_blank">SMBrepute</a>.</p>
			</div>
		</section>
		<!-- end: page -->

    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

		<!-- Vendor -->
		<!-- <script src="/assets/vendor/jquery/jquery.js"></script>
		<script src="/assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="/assets/vendor/bootstrap/js/bootstrap.js"></script>
		<script src="/assets/vendor/nanoscroller/nanoscroller.js"></script>
		<script src="/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="/assets/vendor/magnific-popup/magnific-popup.js"></script>
		<script src="/assets/vendor/jquery-placeholder/jquery.placeholder.js"></script> -->

		<!-- Theme Base, Components and Settings -->
		<!-- <script src="/assets/javascripts/theme.js"></script> -->

		<!-- Theme Custom -->
		<!-- <script src="/assets/javascripts/theme.custom.js"></script> -->

		<!-- Theme Initialization Files -->
		 <!-- <script src="/assets/javascripts/theme.init.js"></script> -->
<script src="//code-eu1.jivosite.com/widget/z0l0X67K3W" async></script>

<!-- <script src="https://dash.getastra.com/sdk.js?site=3YKkjSqTZ"></script> -->
	</body>
</html>
