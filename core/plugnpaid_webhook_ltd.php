<?php

  header('Content-Type: application/json');

  $res = ['success'=>false,'message'=>'Something went wrong'];

if (isset($_GET['93NTqpXYueSkttw8TizYI0z9dKGwuQif'])
//&& isset($_GET['planid'])
) {

  $json = file_get_contents('php://input');

  // decode json
  $object = json_decode($json, true);
  error_log(print_r($object,true));

  include_once('database.php');
  $db= new Database();


  if (isset($object['data']['order']['products'][0]['id'])
        && $object['type']=="new_simple_sale"
        && $plan=$db->getPlanByPlugNPaidProductId($object['data']['order']['products'][0]['id'])
       ) {


        $email= $object['data']['order']['customer']['email'];
        $phone = $object['data']['order']['customer']['telephone'];

        if ($user  = $db->getUserByEmail($email)) {
            $res = ['success'=>true,'message'=>'Done',"debug"=>$user];

            $user_id = $user['id'];

            $user_plan = $db->getCurrentUserPlan($user['id']);

            if ($user_plan['plan_id']==$plan['id']) {
              $data= $db->prolongCurrentUserPlanPeriod($user_plan['user_id'],$user_plan['plan_id'],$object['data']['subscription']['cancellation_link']);
            } else {
              $data = [
                'id'=> $user_id,
                'plan' => $plan['id'],
                'cancellation_link' =>$object['data']['subscription']['cancellation_link'],
              ];
                    $db->updateUserPlan($data);
            }



                  //

            $res = ['success'=>true,'message'=>'Done'];





        } else {
            $password = '';
            $desired_length = rand(8, 12);
            for ($length = 0; $length < $desired_length; $length++) {
                $password .= chr(rand(32, 126));
            }

          $token = md5(uniqid($email, true)).md5($email);


            $sql = "INSERT into user
       (
                 name,
                 email,
                 pwd,
                 type,
                 status,
                 no_of_campaigns,
                 fb_reviews_cnt,
                 google_reviews_cnt,
                 yelp_reviews_cnt,
                 subscription_id,
                 phone,
                 address,
                 plan,
                 advanced_settings,
                 use_default_credentials,
                 validate_email_request_token

       )
     values
         (
            ?,
            ?,
            md5(?),
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
  ";




    $stmt = $db->connection->prepare($sql);

    $advanced_settings = (($plan['use_default_credentials'] == 0) ? 1 : 0);
    $type =2;
    $status =2;
    $address ='';

    $stmt->bind_param(
      'sssiisssssssssss',
      $object['data']['order']['customer']['name'],
      $email,
      $password,
      $type,
      $status,
      $plan['no_of_campaigns'],
      $plan['fb_reviews_cnt'],
      $plan['google_reviews_cnt'],
      $plan['yelp_reviews_cnt'],
      $object['data']['order']['customer']['id'],
      $phone,
      $address,
      $plan['id'],
      $advanced_settings,
      $plan['use_default_credentials'],
      $token
  );



        $stmt->execute();

        $user_id = $db->connection->insert_id;




            require_once('mailer.php');
            $mail = new  Mailer;
            $mail->SMTPDebug = 0;    // Enable verbose debug output

            $url = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/action.php?action=validate_email&key='.$token;

            $body = "<h4>Hey there!</h4>
        <p>Thanks for registering for SMBreviewer's Review Funnel Tool! As simple as this tool is, the team has worked hard to create this little tool for you to make it easy to captue reviews and video testimonials by using automated funnels as well as embed reviews and testimonials from Facebook, Yelp, Google My Business (and much more to come). You can also take reviews from other sources, and copy/paste them into the tool so that allows you to use reviews from basically anywhere. Pretty sweet, right?</p>
        <p>Before you move forward with using the review tool, you need to help us fight spam by clicking on the link below, or by copying & pasting it into your browser.</p>
        <p><a target=\"_blank\" href='".$url."''>Validation Link</a></p>
        <p>
        Your login details are:
        <br>
        Email: ".$email."
        <br>
        Password: ".$password."
        </p>
        <p>You can change your password anytime by clicking \"forgot password\" on the login screen, or in your profile. </p>
        <p>Thanks so much,</p>
        <p>Vik and the team at SMBrepute</p>

        ";


            $addTo = ['email'=>$email,'name'=>$object['data']['order']['customer']['name']];
            try {
                $mail->addAddress($addTo['email'], $addTo['name']);
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = "Validate Your SMBreviewer Account!";
                $mail->Body = $body;
                $mail->send();
                $_SESSION['msg']="We sent you an email with a validation link, please confirm your email address before logging in.";
            } catch (Exception $e) {
                error_log('mailsend error='.print_r($e, true));
            }







            $data = [
          'id'=> $user_id,
          'plan' => $plan['id'],
          'cancellation_link' =>$object['data']['subscription']['cancellation_link'],

      ];

            $db->updateUserPlan($data);

            $bp =[


            ];
            $res = ['success'=>true,'message'=>'Done',"debug"=>$bp];



        }



        $sql = "insert into client_payments
        (
            item_name,
            payment_gross,
            payment_status,
            payer_email,
            created_user

        )
        values
            (
            ?,
            ?,
            ?,
            ?,
            ?
            )
        ";


        $stmt = $db->connection->prepare($sql);
        $stmt->bind_param(
          'sssss',
          $object['data']['order']['products'][0]['title'],
          $object['data']['order']['amount_total'],
          $object['data']['order']['status'],
          $object['data']['order']['customer']['addresses']['billing'][0]['email'],
          $user_id

        );



        $stmt->execute();

        ///adding user to  crm
        // require_once('core/salesflare.php');
        //
        // $data = ["name" => $object['data']['order']['customer']['name']],
        //         "website" => $address,
        //         "description" => "reviewers account ",
        //         "email" => $email,
        //         "phone_number" => $phone,
        //         "custom" => ['smbreviews_plan' => $plan['title'], 'smbreviews_member' =>"yes", "premium_customer" => (((float)$plan['amount']>0) ? "yes" : "no" ) ],
        //       ];
        //
        // $s = SalesFlare::apiCall('accounts','post',$data);


//end of userplan exists
  die(json_encode($res, true));

    }

    if (isset($object['data']['products'][0]['id'])
          && $object['type']=="recurring_subscription_cancelled"
          && $plan=$db->getPlanByPlugNPaidProductId($object['data']['products'][0]['id'])
         ) {
           $email= $object['data']['customer']['email'];

           if ($user  = $db->getUserByEmail($email)) {
               $res = ['success'=>true,'message'=>'Done',"debug"=>$user];

               $user_id = $user['id'];

               $user_plan = $db->getCurrentUserPlan($user['id']);

               if ($user_plan['plan_id']==$plan['id']) {
                $db->removeCancellationLinkFromUsesPlans($user_plan['id']);
               }



                     //

               $res = ['success'=>true,'message'=>'Done'];





           }

        }


}


  die(json_encode($res, true));
