<?php
@session_start();
include_once("core/database.php");
    if ($_REQUEST['txn_type']=='subscr_payment') {
        $user_id = 0;
        $db = new Database();
        $webUserID = $_REQUEST['custom'];

        if (strpos($webUserID, '_user_id') !== false) {

            $user_id = @$db->getUserProfile(str_replace('_user_id','',$webUserID))['id'];
            $row['pkg_id'] = $_REQUEST['item_number'];


        }
         else
        {
            $sql = "select * from web_user_info where id='".$webUserID."'";
            $res = mysqli_query($con, $sql);
            if (mysqli_num_rows($res)) {
                $row = mysqli_fetch_assoc($res);

                if (@count($db->getUserByEmail($row['email']))==0) {
                    $password	 = $row['password'];
                    // $sql = "select * from plans where `id`='".$row['pkg_id']."'";
                    // $result = $conn->query($sql);
                    $plan = $db->getPlanById($row['pkg_id']);

                    $token = md5(uniqid($row['email'], true)).md5($row['email']);
                    $current_date=gmdate('Y-m-d H:i:s');

                    $ins = "INSERT into user
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
                                   paypal_subscriber_id,
																	 plan,
																	 advanced_settings,
																	 use_default_credentials,
                                   date_add,
                                   validate_email_request_token


                         )
                       values
                           (
                                   '".$row['name']."',
                                   '".$row['email']."',
                                   md5('".$row['password']."'),
                                   '2',
                                   '2',
                                  '".$plan['no_of_campaigns']."',
                                  '".$plan['fb_reviews_cnt']."',
                                  '".$plan['google_reviews_cnt']."',
                                  '".$plan['yelp_reviews_cnt']."',

                                   '".$_REQUEST['subscr_id']."',
                                   '".$_REQUEST['payer_phone']."',
                                   '".$_REQUEST['address']."',

																	 'avshdgvhvhav',
                                   '".$row['pkg_id']."',
                                   '".(($plan['use_default_credentials'] == 0) ? 1 : 0)."',
																	 '".$plan['use_default_credentials']."',
																	 '".$current_date."',
																	 '".$token."'
                          )";

                    $exe = mysqli_query($con, $ins) or die(mysqli_error($con));

                    $user_id = mysqli_insert_id($con);

                    $query2 =mysqli_query($con, "delete from web_user_info where id=".$row['id']);

                    require_once('core/mailer.php');
                    $mail = new  Mailer;
                    $mail->SMTPDebug = 0;    // Enable verbose debug output

                    $url = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].pathinfo($_SERVER['PHP_SELF'])['dirname'].'/action.php?action=validate_email&key='.$token;

                  $body = "<h4>Hey there!</h4>
                          <p>Thanks for registering for SMBreviewer's Free Review Funnel and Display Tool! 
                          Did you know that BrightLocal found in their 2019 study that 74% of people under the age of 55 in the U.S. use reviews on a weekly or daily basis to find what their looking for? And now, the same study found that 89% of people aged 35-54 trust onine reviews as much as personal recommendations.</p>
                          <p>As seemingly simple as this tool is, the team has worked hard to create this little tool for you to make it easy to embed reviews and testimonials from Facebook, Yelp, Google My Business, as well as written and video reviews captured by the tool. You can also input and upload reviews from other sources, so that allows you to use reviews from basically anywhere. Pretty sweet, right?</p>
                          <p>In early 2021 we added a &quot;Smart Review Funnel&quot; feature which allows you to use funnels to capture reviews, and in Q2 of 2021 we added the ability to capture video testimonials from a mobile phone or desktop computer.</p>
                          <p>Right now we're working on a Wordpress plugin and an open API.</p>
                          <p>Before you move forward with using the review funnel tool, you need to help us fight spam by clicking on the link below, or by copying & pasting it into your browser.</p>
                          <p><a target=\"_blank\" href='".$url."''>Validation Link</a></p>
                          <p>Or you can copy and paste the link in your address bar: ".$url."
                          <p>
                             Your Login Details Are:
                          <br>
                          Email: ".$row['email']."
                          <br>
                          Password: ".$row['password']."
                          </p>
                          <p>You can change your password anytime by clicking \"forgot password\" on the login screen, or in your profile. </p>
                          <p>Thanks so much for using SMBreviewer to build trust in your brand or business! We look forward to being there for you.</p>
                          <p>Vik, founder of SMBrepute and SMBreviewer</p>
                          
                          
                          
                          ";


                  $addTo = ['email'=>$row['email'],'name'=>$row['name']];
                  try {
                      $mail->addAddress($addTo['email'], $addTo['name']);
                      $mail->isHTML(true);                                  // Set email format to HTML
                      $mail->Subject = "Take a Second to Validate Your SMBreviewer Account";
                      $mail->Body = $body;
                      $mail->send();
                      $_SESSION['msg']="We sent you an email with a validation link, please check your email and confirm your email address before logging in.";

                        } catch ( Exception $e) {

                          error_log('mailsend error='.print_r($e,true));

                        }


                ///adding user to  crm
                require_once('core/salesflare.php');

                $data = ["name" => $row['name'],
                        "website" => $_REQUEST['address'] ,
                        "description" => "reviewers account ",
                        "email" => $row['email'],
                        "phone_number" => $_REQUEST['payer_phone'],
                        "custom" => ['smbreviews_plan' => $plan['title'], 'smbreviews_member' =>"yes", "premium_customer" => (((float)$plan['amount']>0) ? "yes" : "no" ) ],
                      ];

                $s = SalesFlare::apiCall('accounts','post',$data);


                }
            }
        }

        $sql3 = "insert into client_payments
                            (
                                item_name,
                                payment_gross,
                                payment_status,
                                payer_email,
																created_user,
                                txn_id
                            )
                            values
                                (
                                    '" . $_REQUEST['item_name'] . "',
                                    '" . @$_REQUEST['payment_gross'] . "',
                                    '" . $_REQUEST['payment_status'] . "',
                                    '" . $_REQUEST['payer_email'] . "',
                                    '" . $user_id . "',
                                    '" . $_REQUEST['txn_id'] . "'
                                )
                            ";
        $query = mysqli_query($con, $sql3);


        $data = [
                            'id'=> $user_id,
                            'plan' => $row['pkg_id']

                        ];

        $db->updateUserPlan($data);

        header("location: index.php");
    }
