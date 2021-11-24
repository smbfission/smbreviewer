<?php
@session_start();


//die(print_r($_SESSION,true));
if (@$_SESSION['login_user'] == "" || !isset($_SESSION['user_id'])){
    $_SESSION['msg'] = "Please first login to access this page.";

    header('Location: /');
}


$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);

$components = explode('/', $path);

$first_part = @$components[count($components)-1];
require_once("core/database.php");

$db = new Database();


$current_user = $db->getUser(@$_SESSION['user_id']);


if (@count($current_user) == 0 || (int)$current_user['status']==0) {

  unset($_SESSION['login_user']);
  $_SESSION['msg']='Session was suspended. Please login.';
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: /");
  exit();
}



$plan_expired =  $db->checkPlanExpired(@@$_SESSION['user_id']);

?>
<!doctype html>
<html class="fixed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8" >

        <!-- App favicon -->
        <link rel="shortcut icon" href="/assets/images/favicon.ico">
        <!-- App title -->
        <title> <?php echo ucwords(str_replace(array(".php","_")," ",$first_part)); ?> App | SMBreviewer</title>

		<meta name="keywords" content="SMBreviewer" />
		<meta name="description" content="Build Your Online Reputation for Your Business for Free">
		<meta name="author" content="SMBreviewer">



		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

		<!-- Vendor CSS -->
		<link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="/assets/vendor/font-awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="/assets/vendor/magnific-popup/magnific-popup.css" />
		<link rel="stylesheet" href="/assets/vendor/bootstrap-datepicker/css/datepicker3.css" />
		<link rel="stylesheet" href="/assets/vendor/bootstrap-tagsinput/bootstrap-tagsinput.css" />
		<link rel="stylesheet" href="/assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css" />

		<!-- Specific Page Vendor CSS -->
    <link rel="stylesheet" href="/assets/vendor/select2/select2.css" />
		<link rel="stylesheet" href="/assets/vendor/select2/select2-bootstrap.css" />

		<!-- <link rel="stylesheet" href="assets/vendor/jquery-datatables-bs3/assets/css/datatables.css" /> -->
		<link rel="stylesheet" href="/assets/vendor/jquery-datatables/extras/Responsive/css/dataTables.responsive.css" />



		<!-- Theme CSS -->
		<link rel="stylesheet" href="/assets/stylesheets/theme.css" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="/assets/stylesheets/skins/default.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="/assets/stylesheets/theme-custom.css">


		<!-- Head Libs -->
		<script src="/assets/vendor/modernizr/modernizr.js"></script>

		<!-- Dashboard CSS -->
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/bootstrapclasses.css">

		<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-122972834-8"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-122972834-8');
</script>
	<!-- end Global site tag (gtag.js) - Google Analytics -->



<!-- ProductFlare Code Script added to footer to prevent loading delays. Stylesheet added here. -->


<link rel="stylesheet" href="https://eu-us.productflare.com/changelogstylefilerr">

<!-- End of Announcefly Code -->




	<!-- userguilding -->
	<script>
  (function (u, s, e, r, g) {
      u[r] = u[r] || [];
      u[r].push({
        'ug.start': new Date().getTime(), event: 'embed.js',
      });
      var f = s.getElementsByTagName(e)[0],
          j = s.createElement(e);
      j.async = true;
      j.src = 'https://static.userguiding.com/media/user-guiding-'
       + g + '-embedded.js';
      f.parentNode.insertBefore(j, f);
  })(window, document, 'script', 'userGuidingLayer', '41400919ID');
</script>

	<!-- end userguilding -->

<!-- start Gist JS code-->
<!-- <script>
    (function(d,h,w){var gist=w.gist=w.gist||[];gist.methods=['trackPageView','identify','track','setAppId'];gist.factory=function(t){return function(){var e=Array.prototype.slice.call(arguments);e.unshift(t);gist.push(e);return gist;}};for(var i=0;i<gist.methods.length;i++){var c=gist.methods[i];gist[c]=gist.factory(c)}s=d.createElement('script'),s.src="https://widget.getgist.com",s.async=!0,e=d.getElementsByTagName(h)[0],e.appendChild(s),s.addEventListener('load',function(e){},!1),gist.setAppId("omu34soq"),gist.trackPageView()})(document,'head',window);
</script> -->
<!-- end Gist JS code-->


<!-- start aiva CODE -->
<script async src="https://aivalabs.com/cta/?identity=PhCGCXQx1dRqnufJAO751OCDa5.Pw5EvcMh4TDoAtYztMoqS0kFXC"></script>
<!-- end aiva code -->

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



<style media="screen">
  .text-striked {

    text-decoration: line-through;
  }
  .datepicker{z-index:1151 !important;}
  div.row.datatables-footer div {
    z-index:999 !important;
  }

  #birdseed-widget-container  {
    z-index:998 !important;
  }

.header {
  display: flex;
  justify-content: space-between;
}
.logo-container {
display: inline-flex;
align-items: center;
flex:1;
}
  .changelog-icon {
  color: #ef4f03; font-size: 1.9rem;
  margin-left: auto;
  }

@media (max-width: 767px) {

   .changelog-icon {
    margin: 19px 70px auto auto;
     z-index: 99;
  }
}



</style>

	</head>
	<body>
		<section class="body">


            <?php

            ?>



			<!-- start: header -->
			<header class="header">
				<div class="logo-container">
					<a href="../" class="logo" style="font-size:25px;">
						<!--<img src="/assets/images/logo.png" height="35" alt="Porto Admin" />-->
                        <img src="/assets/images/logo-smbreviewer2.png" height="35" width="80" alt="SMBreviewer" style="border:0px;" />
					</a>

          <div class="changelog-icon" >
            <i id="changelog" class="fa fa-bell"></i>
          </div>
          <div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
            <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
          </div>


				</div>


				<!-- start: search & user box -->
				<div class="header-right">

					<span class="separator"></span>

					<div id="userbox" class="userbox">
						<a href="#" data-toggle="dropdown">
							<div class="profile-info" data-lock-name="John Doe" data-lock-email="johndoe@okler.com">
								<span class="name"><?php echo @$_SESSION['user_name']; ?> <?= (($plan_expired) ? "<span class=\"text-danger\">(expired)</span>" : "")?></span>
								<span class="role" style="display: none;">administrator</span>
							</div>

							<i class="fa custom-caret"></i>
						</a>

						<div class="dropdown-menu">
							<ul class="list-unstyled">
								<li class="divider"></li>
								<li>
									<a role="menuitem" tabindex="-1" href="/profile/"><i class="fa fa-user"></i> My Profile</a>
								</li>
								<li>
									<a role="menuitem" tabindex="-1" href="/logout/"><i class="fa fa-power-off"></i> Logout</a>
								</li>
							</ul>
						</div>
					</div>
				</div>



				<!-- end: search & user box -->
			</header>
			<!-- end: header -->

			<div class="inner-wrapper">
