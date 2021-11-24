<?php



require_once('../mailer.php');
$mail = new  Mailer;
$mail->SMTPDebug = 1;    // Enable verbose debug output


$ThisEmail = 'noreply@smbreviewer.com';
$subject = "Password reset request confirmation";

$headers = "From: ".$ThisEmail."\r\n";
//$to = 'streletsky@gmail.com';
$to = "mactest13@ukr.net";

//$url = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].pathinfo($_SERVER['PHP_SELF'])['dirname'].'/password_reset.php?key='.$token;

$body = '<h4> Hi there,</h4>
<p>If you asked for passoword reset for Reviewer account, please follow this link to confirm that. </p>';
//$body .= '<a href="'.$url.'">'.$url.'</a>';
$body .='<p>Good luck!</p>';
$addFrom = ['email'=>$ThisEmail,'name'=>'No-Reply passoword reseter'];
$addTo = ['email'=>$to,'name'=>''];
try {
  $mail->addAddress($addTo['email'], $addTo['name']);     // Add a recipient
  $mail->setFrom($addFrom['email'], $addFrom['name']);
        // Content
  $mail->isHTML(true);                                  // Set email format to HTML
  $mail->Subject = $subject;
  $mail->Body = $body;
  $mail->send();

  $res = ['success'=>true,'message'=>'Email with password reset instructions has been sent to your email. If the email is not received try to check spam or try again in 10 minutes.'];
    } catch ( Exception $e) {

      $res = ['success'=>false,
      'message'=> $mail->ErrorInfo
    ];


    }

die(json_encode($res));

 ?>
