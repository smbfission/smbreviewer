<?php
 use PHPMailer\PHPMailer\PHPMailer as PHPMailer;
 use PHPMailer\PHPMailer\Exception as Exception;


 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);

      require_once('../../../core/phpmailer/Exception.php');
      require_once('../../../core/phpmailer/PHPMailer.php');
      require_once('../../../core/phpmailer/SMTP.php');


       $mail = new PHPMailer(true);

      $mail->SMTPDebug = 4;    // Enable verbose debug output
      $mail->isSMTP();     // Set mailer to use SMTP
      $mail->SMTPAuth   = true;

      // $mail->SMTPAutoTLS = true;                                   // Enable SMTP authentication
      // $mail->SMTPSecure = 'tsl';
      // $mail->Port = 587;
      // $mail->Host = 'smtp1.s.ipzmarketing.com';
      // $mail->Username = 'xjbklazwdmde';
      // $mail->Password   = 'LiSvsfev5NguN4Fn';                               // SMTP password
      $mail->SMTPAutoTLS = true;                                   // Enable SMTP authentication
      $mail->SMTPSecure = 'ssl';
      $mail->Port = 465;
      $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
      $mail->Username = 'AKIA55BQ33L7GCRN6PMT';
      $mail->Password   = 'BHwGvBi7ZpMPjFv2lM3qdcntxwiS3hOHyGpY8Bo322tU';                               // SMTP password




$ThisEmail = 'contactus@smbreviewer.com';

$subject = "My subject";
$txt = "Hello world!";
$headers = "From: ".$ThisEmail."\r\n";
//$to = 'streletsky@gmail.com';
$to = 'mactest13@ukr.net';

$body = 'lalalala';

$addFrom = ['email'=>$ThisEmail,'name'=>'user name'];
$addTo = ['email'=>$to,'name'=>'KS'];



$subject = 'Re:';
try {
          //Server settings


          //Recipients
    $mail->addAddress($addTo['email'], $addTo['name']);     // Add a recipient

    $mail->setFrom($addFrom['email'], $addFrom['name']);



          // Content
     $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $subject;
          $mail->Body = $body;


          $mail->send();
             echo 'message sent';

      } catch (Exception $e) {



        echo $mail->ErrorInfo;
      }


$i=0;
?>
