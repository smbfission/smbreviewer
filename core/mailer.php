<?php

use PHPMailer\PHPMailer\PHPMailer as PHPMailer;
use PHPMailer\PHPMailer\Exception as Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once (dirname(__FILE__).'/vendor/autoload.php');

 class Mailer extends PHPMailer
 {

     public function __construct($exceptions = true)
     {


         //Don't forget to do this or other things may not be set correctly!
         parent::__construct($exceptions);

         require('config.php');

         $this->isSMTP();
         $this->SMTPAuth = $mail_SMTPAuth;
         $this->SMTPDebug = 4;
         $this->SMTPAutoTLS = $mail_SMTPAutoTLS;
         $this->SMTPSecure = $mail_SMTPSecure;
         $this->Port = $mail_Port;
         $this->Host = $mail_Host;
         $this->Username = $mail_Username;
         $this->Password = $mail_Password;
         $this->XMailer = ' ';

         $this->setFrom($mail_defaultEmailFrom, $mail_defaultNameFrom);

         $this->isHTML(true);

     }

     // //Extend the send function
     // public function send()
     // {
     //     $this->Subject = '[Yay for me!] ' . $this->Subject;
     //     $r = parent::send();
     //     echo 'I sent a message with subject '. $this->Subject;
     //
     //     return $r;
     // }
 }
 //
 // //Now creating and sending a message becomes simpler when you use this class in your app code
 // try {
 //     //Instantiate your new class, making use of the new `$body` parameter
 //     $mail = new myPHPMailer(true, '<strong>This is the message body</strong>');
 //     // Now you only need to set things that are different from the defaults you defined
 //     $mail->addAddress('jane@example.com', 'Jane User');
 //     $mail->Subject = 'Here is the subject';
 //     $mail->addAttachment(__FILE__, 'myPHPMailer.php');
 //     $mail->send(); //no need to check for errors - the exception handler will do it
 // } catch (Exception $e) {
 //     //Note that this is catching the PHPMailer Exception class, not the global \Exception type!
 //     echo 'Caught a '. get_class($e) .': '. $e->getMessage();
 // }

 ?>
