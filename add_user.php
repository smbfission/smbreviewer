<?php
header('Access-Control-Allow-Origin: *');
@session_start();
include_once("core/database.php");
function decode($str){
	return substr($str,2,strlen($str)-12);
}
$id  = decode($_REQUEST['pid']);
$uid = $_REQUEST['uid'];

$db=new Database();
// $sql = "select * from plans where id='".$id."'";
// $res = $db->connection->query($sql);
// if($res->num_rows){
// 	$row = $res->fetch_assoc();
// }

$row=$db->getPlanById($id);
$settings = $db->getGeneralSettings();

?>
<!doctype html>
<html class="fixed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<!-- App favicon -->
    <link rel="shortcut icon" href="images/favi.png">

        <!-- App title -->
        <title>Login &amp; Create an Account | SMBreviewer </title>

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
	<style>
.footer{
	left:0px !important;
	text-align:center !important
}
input[type="checkbox"]{
	opacity:1 !important;
}
.alert-success {
    background-color: #dff0d8;
    border-color: #d0e9c6;
    color: #3c763d;
}
.alert-danger {
    background-color: #f2dede;
    border-color: #ebcccc;
    color: #a94442;
}
.alert {
    border: 1px solid transparent;
    border-radius: 0.25rem;
    padding: 0.75rem 1.25rem;
    margin-bottom: 0;
    margin-top: 25px;
}
</style>
	<body>
		<!-- start: page -->
		<section class="body-sign">
			<div class="center-sign">
				<a class="logo pull-left" style="font-size:35px;">
                    <img src="/assets/images/logo-smbreviewer2.png" height="35" width="81" alt="SMBreviewer" style="border:0px;" />
				</a>

				<div id="singInPanel" class="panel panel-sign">
					<div class="panel-title-sign text-right">
						<h2 class="title text-uppercase text-bold m-none"><i class="fa fa-user mr-xs"></i> Register</h2>
					</div>
					<div class="col-lg-12">
								<h4 class="m-t-0 header-title"><b>Please Provide the Information Below:</b></h4>
	<p class="text-muted font-13">
		You're just one step away.
		 <?php
          

        if(isset($_SESSION['message']) && $_SESSION['message']!=""){
            echo $_SESSION['message'];
        }
        unset($_SESSION['message']);


              ?>
	</p></div>
					<div class="panel-body">
					
						    
						   <form method="post"  class="form-horizontal-" enctype="multipart/form-data" action="/billing.php">
			
						    
						    

             
              <div class="p-20">
		<h4>You're signing up for the <span style="color:#FF8700"><?php echo $row['title']?></span> for <span style="color:red"><?php echo '$'.$row['amount']?> per month.</span></h4></div>

							<div class="form-group mb-lg">
								<label>Full Name</label>
								<div class="input-group input-group-icon">
									<input name="name" class="form-control input-lg" type="text" placeholder="John Doe"   >
									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-user"></i>
										</span>
									</span>
									
								</div>
							</div>

	
								
								
							
				<div class="form-group mb-lg">
								<label>Email Address (to Receive a Confirmation)</label>
								<div class="input-group input-group-icon">
									<input name="email" class="form-control input-lg" type="email" placeholder="Email Address" required>
									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-envelope-o"></i>
										</span>
									</span>
									
								</div>
							</div>
							
							
									<div class="form-group mb-lg">
								<label>Phone Number for SMS Notifications (U.S. Only)<br/>(Optional)</label>
								<div class="input-group input-group-icon">
									<input name="phone" class="form-control input-lg" type="phone" placeholder="###-###-####">
									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-mobile-phone"></i>
										</span>
									</span>
									
								</div>
							</div>
       
       
       		<div class="form-group mb-lg">
								<label>Your Website (Https://www.mysite.com)<br/>(Optional)</label>
								<div class="input-group input-group-icon">
									<input name="address" class="form-control input-lg" type="url" placeholder="https://www.mysite.com">
									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-desktop"></i>
										</span>
									</span>
									
								</div>
							</div>
       
       
			 
			
			
				<div class="form-group mb-lg">
								<div class="clearfix">
									<label class="pull-left">Password</label>
								</div>
								<div class="input-group input-group-icon">
									<input name="password" class="form-control input-lg" type="password"  placeholder="Password" required>
                                  									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-lock"></i>
										</span>
									</span>
								</div>
								
										<div class="form-group mb-lg">
								<div class="clearfix">
									<label class="pull-left">Retype Your Password</label>
								</div>
								<div class="input-group input-group-icon">
									<input name="retype_password" class="form-control input-lg" type="password"  placeholder="Password" required>
                                  									<span class="input-group-addon">
										<span class="icon icon-lg">
											<i class="fa fa-lock"></i>
										</span>
									</span>
								</div>
			


            <div class="form-group">
				<label class="checkbox"><input type="checkbox" name="privacy_policy" value="1" required style="margin-left:0px !important; margin-right:5px !important; position:relative !important">T&C/Privacy Policy <a href="<?php echo $settings['privacy_policy_url']; ?>">Read here</a></label>
			</div>
			<div class="form-group text-right m-b-0">
                <!-- package fields -->
				<input type="hidden" name="pkg_id" value="<?php echo $id?>">
				<input type="hidden" name="pkg_price" value="<?php echo $row['amount']?>">
				<input type="hidden" name="pkg_title" value="<?php echo $row['title']?>">
				<button class="btn btn-primary waves-effect waves-light" type="submit"> Finish Registration </button>
				<button type="reset" class="btn btn-default waves-effect waves-light m-l-5" onclick="window.location = 'javascript:history.go(-1)'"> Cancel </button>
			</div>
		</form> 
								
									
            
							</div>
							</div>

							<span class="mt-lg mb-lg line-thru text-center text-uppercase">
								<span>or</span>
							</span>

							<p class="text-center">Have an Account? <a href="/">Login Here</a>

						</form>

					</div>
				</div>

      


				<p class="text-center text-muted mt-md mb-md" >&copy; Copyright <?=date('Y')?>. All rights reserved.  <a href="https://www.smbrepute.com" target="_blank">SMBrepute</a>.</p>
			</div>
		</section>
		<!-- end: page -->

    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://app.syncspider.com/api/v1/htmlforms/source/1962/form/snippet/smartForm"></script>

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


<!-- <script src="https://dash.getastra.com/sdk.js?site=3YKkjSqTZ"></script> -->
	</body>
</html>
