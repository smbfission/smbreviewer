<?php
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['email'])) {
  header('Content-Type: application/json');
  $res = ['success'=>false,'message'=>'Something went wrong'];

  require_once "database.php";
  //
  $sel = "select `id`,`email`, `last_password_change`, CURRENT_TIMESTAMP as ct from `user` where email='".$_POST['email']."'";
  $qry_res = mysqli_query($con,$sel);
  if (mysqli_num_rows($qry_res)==0) {
    $res = ['success'=>false,'message'=>'User with this email doesn\'t exist'];
  } else {
  //
     $row = mysqli_fetch_assoc($qry_res);
  //
     $diff = strtotime($row['ct']) - strtotime($row['last_password_change']);
  //
   if ((int)$diff/60 < 1) {
        $res = ['success'=>false,
        'message'=>sprintf('Password reset request already sent, please try in %d minute(s).',(int)$diff/60+1)
      ];
     } else {
      $token = md5(uniqid($row['email'], true)).md5($row['email']);


        require_once('mailer.php');
        $mail = new  Mailer;
        $mail->SMTPDebug = 0;    // Enable verbose debug output


       $ThisEmail = $mail_defaultEmailFrom;
    //  $ThisEmail = 'contactus@smbreviewer.com';
      $subject = "SMBreviewer Password Reset Confirmation";

      $headers = "From: ".$ThisEmail."\r\n";
      //$to = 'streletsky@gmail.com';
      $to = $_POST['email'];

      $url = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].pathinfo($_SERVER['PHP_SELF'])['dirname'].'/password_reset.php?key='.$token;

      $body = '<h4> Hi there,</h4>
        <p>If you asked for a password reset for your SMBreviewer account, please click on this link to confirm. </p>';
      $body .= '<a href="'.$url.'">'.$url.'</a>';
      $body .='<p>Contact contactus@smbreviewer.com if you need help.</p>';
      $addFrom = ['email'=>$ThisEmail,'name'=>'SMBrepute Team'];
      $addTo = ['email'=>$to,'name'=>''];
      try {
          $mail->addAddress($addTo['email'], $addTo['name']);     // Add a recipient
          $mail->setFrom($addFrom['email'], $addFrom['name']);
                // Content
          $mail->isHTML(true);                                  // Set email format to HTML
          $mail->Subject = $subject;
          $mail->Body = $body;
          $mail->send();
          $sql = "UPDATE `user` SET  `change_mail_request_token`='".$token."', `last_password_change`=CURRENT_TIMESTAMP  where `id` = ".$row['id'];
          mysqli_query($con,$sql);
          $res = ['success'=>true,'message'=>'An email with password reset instructions has been sent to your email. If the email is not received, try checking your Spam folder or try again in 10 minutes.'];
            } catch ( Exception $e) {

              $res = ['success'=>false,
              'message'=> $mail->ErrorInfo
            ];


            }


     }

   }
die(json_encode($res,true));
}

else if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['key']) && isset($_POST['password'])) {
  header('Content-Type: application/json');
  $res = ['success'=>false,'message'=>'Something went wrong'];


  if (strlen($_POST['password'])<6) {
    $res = ['success'=>false,'message'=>'Password length is too short'];
      die(json_encode($res,true));
  }


   if ($_POST['password']!=$_POST['password_confirm']) {
     $res = ['success'=>false,'message'=>'Passwords do not match'];
       die(json_encode($res,true));
   }


   require_once "database.php";
   //
   $sel = "select `id` from `user` where md5(`email`)='".substr($_POST['key'],32)."' and `change_mail_request_token` is not null and `change_mail_request_token`='".$_POST['key']."'";
   $qry_res = mysqli_query($con,$sel);
   if (mysqli_num_rows($qry_res)==0) {
       $res = ['success'=>false,'message'=>'Reset URL is not valid, please request password reset again.'.$_POST['key']];
       die(json_encode($res,true));
   } else {
     $row = mysqli_fetch_assoc($qry_res);
     $sql = "UPDATE `user` SET  `change_mail_request_token`= null , `last_password_change`=CURRENT_TIMESTAMP, `pwd`=md5(?), `validate_email_request_token` = NULL, `status`=1   where `id` =?";
     $stmt = mysqli_prepare($con, $sql);
     mysqli_stmt_bind_param($stmt, 'ss', $_POST['password'], $row['id']);
     mysqli_stmt_execute($stmt);
     mysqli_stmt_close($stmt);

    $res = ['success'=>true,'message'=>'Password has been successfully reset. Now you can login with your credentials.'];
    }



  die(json_encode($res,true));



}

if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['key'])) {


require_once "database.php";
//
$sel = "select `id` from `user` where md5(`email`)='".substr($_GET['key'],32)."' and `change_mail_request_token`='".$_GET['key']."'";
$qry_res = mysqli_query($con,$sel);
if (mysqli_num_rows($qry_res)==0) {
  $valid=false;
} else {
   $valid=true;
   $row = mysqli_fetch_assoc($qry_res);
 }

$key = $_GET['key'];

 @session_start();


}

 ?>
 <!doctype html>
 <html class="fixed">
 	<head>
 		<!-- Basic -->
 		<meta charset="UTF-8">

 		<!-- App favicon -->
     <link rel="shortcut icon" href="../assets/images/favicon.ico">
         <!-- App title -->
    <title>Password reset | SMBreviewer Free Review Tool</title>

    <meta name="keywords" content="facebook reviews, google my business reviews, smbreviewer, review management" />
 		<meta name="description" content="Embed your reviews for free from Facebook Pages, Google My Business, Yelp and all other custom reviews. Build trust in your brand.">
 		<meta name="author" content="SMBreviewer">

 		<!-- Mobile Metas -->
 		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

 		<!-- Web Fonts  -->
 		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

 		<!-- Vendor CSS -->
 		<link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.css" />
 		<link rel="stylesheet" href="../assets/vendor/font-awesome/css/font-awesome.css" />
 		<link rel="stylesheet" href="../assets/vendor/magnific-popup/magnific-popup.css" />
 		<link rel="stylesheet" href="../assets/vendor/bootstrap-datepicker/css/datepicker3.css" />

 		<!-- Theme CSS -->
 		<link rel="stylesheet" href="../assets/stylesheets/theme.css" />

 		<!-- Skin CSS -->
 		<link rel="stylesheet" href="../assets/stylesheets/skins/default.css" />

 		<!-- Theme Custom CSS -->
 		<link rel="stylesheet" href="../assets/stylesheets/theme-custom.css">

 		<!-- Head Libs -->
 		<script src="../assets/vendor/modernizr/modernizr.js"></script>

 	</head>
 	<body>
 		<!-- start: page -->
 		<section class="body-sign">
 			<div class="center-sign">
 				<a class="logo pull-left" style="font-size:35px;">
                     <img src="../assets/images/logo-smbreviewer.png" height="35" width="81" alt="SMBreviewer" style="border:0px;" />
 				</a>

 				<div id="resetPanel" class="panel panel-sign">
 					<div class="panel-title-sign text-right">
 						<h2 class="title text-uppercase text-bold m-none"><i class="fa fa-lock mr-xs"></i> Password reset</h2>
 					</div>
 					<div class="panel-body">
            <?php if ($valid): ?>
 						<form class="form-horizontal-" method="post" action="">
		            <input name="key"  type="hidden" value="<?= $key ?>">
              <div class="alert" style="display:none;" role="alert"></div>
 							<div class="form-group mb-lg">
 								<div class="clearfix">
 									<label class="pull-left">Password</label>
 								</div>
 								<div class="input-group input-group-icon">
 									<input name="password" class="form-control input-lg" type="password" required="" placeholder="Password">

 									<span class="input-group-addon">
 										<span class="icon icon-lg">
 											<i class="fa fa-lock"></i>
 										</span>
 									</span>
 								</div>
                <div class="clearfix">
                  <label class="pull-left">Confirm password</label>
                </div>
                <div class="input-group input-group-icon">
 									<input name="password_confirm" class="form-control input-lg" type="password" required="" placeholder="Confirm password">


 									<span class="input-group-addon">
 										<span class="icon icon-lg">
 											<i class="fa fa-lock"></i>
 										</span>
 									</span>
 								</div>
 							</div>

 							<div class="row">
                <div class="col-sm-5 text-left back-to-login">
                  <a href="/" class="btn btn-warning hidden-xs" role="button" aria-disabled="true">Back to login</a>
                  <a href="/" class="btn btn-warning btn-block btn-lg visible-xs mt-lg " role="button" aria-disabled="true">Back to login</a>

                </div>
 								<div class="col-sm-7 text-right save">
 									<button type="submit" name="submit" class="btn btn-primary hidden-xs">Save</button>
 									<button type="submit" name="submit" class="btn btn-primary btn-block btn-lg visible-xs mt-lg">Save</button>
 								</div>
 							</div>

 						</form>

         <script type="text/javascript">
         window.onload = function () {

           $("#resetPanel form").submit(function(e) {
               e.preventDefault();
               var form = $(this),
                alert = form.find('.alert');

               alert.hide();
               alert.removeClass('alert-danger');
               alert.removeClass('alert-success');
               alert.html('');

               $.ajax({
                 type: "POST",
                 data: form.serialize(),
                 success: function(data)
                 {
                   // console.log(data);
                   if (data.success) {
                     if (data.message && data.message!='') {
                       console.log(data.message);
                       alert.addClass('alert-success');
                       form.find('.form-group, div.save').hide();

                       form.find('div.back-to-login').removeClass('text-left col-sm-5');
                       form.find('div.back-to-login').addClass('text-center col-sm-12');
                       alert.html(data.message);
                       alert.show();

                     }
                   } else{

                     if (data.message && data.message!='') {
                       console.log(data.message);
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
              <?php else: ?>


             <div class="alert alert-danger text-center" role="alert">Link is not valid, please try again.</div>
             <div class="row col-sm-12 text-center">
               <a href="/" class="btn btn-warning hidden-xs " role="button" aria-disabled="true">Back to login</a>
               <a href="/" class="btn btn-warning btn-block btn-lg visible-xs mt-lg " role="button" aria-disabled="true">Back to login</a>

             </div>

       <?php endif; ?>
     </div>
     </div>

 				<p class="text-center text-muted mt-md mb-md" style="display: none;">&copy; Copyright 2019. All rights reserved. Template by <a href="https://www.smbfission.com">SMBfission</a>.</p>
 			</div>
 		</section>
 		<!-- end: page -->

 		<!-- Vendor -->
 		<script src="../assets/vendor/jquery/jquery.js"></script>
 		<script src="../assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
 		<script src="../assets/vendor/bootstrap/js/bootstrap.js"></script>
 		<script src="../assets/vendor/nanoscroller/nanoscroller.js"></script>
 		<script src="../assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
 		<script src="../assets/vendor/magnific-popup/magnific-popup.js"></script>
 		<script src="../assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>

 		<!-- Theme Base, Components and Settings -->
 		<script src="../assets/javascripts/theme.js"></script>

 		<!-- Theme Custom -->
 		<script src="../assets/javascripts/theme.custom.js"></script>

 		<!-- Theme Initialization Files -->
 		<script src="../assets/javascripts/theme.init.js"></script>

 	</body>
 </html>
