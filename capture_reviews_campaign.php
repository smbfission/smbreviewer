<?php
session_name("capture_reviews");
session_start();

include_once("core/database.php");
require_once('core/tools.php');

if (!isset($_REQUEST['id'])) {
  header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
  die();
}

$form_token = uniqid();

$ids = Tools::decrypt_link($_REQUEST['id']);
$db = new Database();
$capture_reviews_id = (int)$ids;
$data = $db->getCaptureReviewsById($capture_reviews_id);
if (@count($data) == 0) {
  header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", true, 404);
  die();
}

$loomUrl = "";

if (isset($_POST['iframe']) && $_POST['iframe'] == '1') {
  $_SESSION['capture_reviews_token'] = $_POST['capture_reviews_token'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['capture_reviews_token'] == $_SESSION['capture_reviews_token']) {
  $_SESSION['capture_reviews_token'] = '';

  header('Content-Type: application/json');

  if (!isset($_POST['step'])) {
    $photo = '';
    if (isset($_FILES["file"]) && $_FILES["file"]['size'] > 0) {
      $target_dir = dirname(__FILE__) . '/uploads/';
      $rand = (int)$_REQUEST['id'] . '_capture_reviews_' . time() . '_';
      $target_file = $target_dir . $rand . substr(pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME), 50);
      $target_file .= '.png';

      $res = Tools::uploadImage($_FILES["file"]['tmp_name'], $target_file, 150, 150, 2 * 1024 * 1024, (isset($_FILES["file"]['error']) ? $_FILES["file"]['error'] : 0));

      if ($res['success'] == false) {
        $_SESSION['msg'] = $res['message'];
      } else {
        @unlink($target_dir . $photo);
        $photo = basename($target_file);
      }
    }

    $sql = "INSERT INTO `custom_reviews`(`name`,`rating`, `review`,`date`, `photo`, `user_id`,`capture_reviews_id`,`email`,`facebook_id`,`loom_url`) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $stmt = $db->connection->prepare($sql);
    $rating = sprintf("%01.1f", $_POST['star']);
    $date = gmdate('Y-m-d H:i:s');

    $name = @$_POST['name'];
    $loomUrl = @$_POST['loomUrl'];
    $message = @$_POST['message'];
    $email = @$_POST['email'];
    $facebook_id = @$_POST['facebook_id'];

    $stmt->bind_param(
      'sssssiisss',
      $name,
      $rating,
      $message,
      $date,
      $photo,
      $data['user_id'],
      $capture_reviews_id,
      $email,
      $facebook_id,
      $loomUrl
    );

    if ($stmt->execute()) {
      if (trim($data['reward']) != '' && trim($data['reward_webhook']) != '') {
        $reward  = [
          'review' => [
            'name' => $name,
            'email' => $email,
            'review_text' => $message,
            'review_rating' => $rating,
            'date' => $date
          ]
        ];
        $reward = json_encode($reward, true);
        $res =   Tools::post(trim($data['reward_webhook']), "post", ['Content-Type: application/json'],     $reward);
      }

      $last_id = $db->connection->insert_id;
      if ($_POST['star'] <= $data['min_rating']) {

        $response =  ['step_2' => true, 'insert_id' => $last_id];
        $data['tags'] .= ',feedback';

        if (trim($data['email_for_receiving_negative_review']) != '') {
          require_once(dirname(__FILE__) . '/core/mailer.php');
          $mail = new  Mailer;
          $mail->SMTPDebug = 0;
          $mail->CharSet = 'UTF-8';

          $subject = "User Left a Negative Review";

          $to = $data['email_for_receiving_negative_review'];

          $body = '<h4> Hi there,</h4>';
          $body .= '<p>User ' . $name . ' ' . @$_POST['email'] . ' has left the following review:</p>';
          $body .= '<p>' . $message . '</p>';

          $addTo = ['email' => $to, 'name' => ''];
          try {
            $mail->addAddress($addTo['email'], $addTo['name']);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
          } catch (Exception $e) {
            error_log($mail->ErrorInf);
          }
        }
      } else {
        if ( $data['type'] == 'text' && ((int)$data['enable_review_directories_google'] == 1
        || (int)$data['enable_review_directories_facebook'] == 1
        || (int)$data['enable_review_directories_yelp'] == 1
        || (int)$data['enable_review_directories_custom'] == 1) ) {
          $response =  ['step_3' => true, 'insert_id' => $last_id];
        } elseif( $data['type'] == 'video' && ((int)$data['share_linkedin'] == 1
        || (int)$data['share_facebook'] == 1
        || (int)$data['share_twitter'] == 1) ) {
          $response =  ['step_3' => true, 'insert_id' => $last_id];
        } else {
          $response =  ['step_4' => true, 'insert_id' => $last_id, 'display_share_buttons' =>  true];

          if (trim($data['redirect_url']) != '') {
            $response['redirect_url'] = $data['redirect_url'];
          } else {
            $response['redirect_url'] = $_SERVER['REQUEST_URI'];
          }
        }
      }

      if ($tags = explode(",", $data['tags'])) {
        foreach ($tags as $tag) {
          $tag = trim($tag);
          if ($tag != '') {

            $sql = "INSERT INTO `tags`(`user_id`,`name`) VALUES( ?,? ) ON DUPLICATE KEY UPDATE `id`=LAST_INSERT_ID(`id`)";
            $stmt2 = $db->connection->prepare($sql);
            $stmt2->bind_param(
              'is',
              $data['user_id'],
              $tag
            );
            $stmt2->execute();
            $tags_id = $db->connection->insert_id;

            $sql = "INSERT INTO `custom_reviews_tags`(`tags_id`,`custom_reviews_id`) VALUES(?, ?)";
            $stmt3 = $db->connection->prepare($sql);
            $stmt3->bind_param(
              'ii',
              $tags_id,
              $last_id
            );
            $stmt3->execute();
          }
        }
      }

      if (trim($data['google_spread_sheet_id']) != '') {
        $res = $db->getCustomReviewByIdUserId($last_id, $data['user_id']);
        $res = Tools::capture_reviews_add_google_sheets_row(
          $data['google_access_token'],
          $data['google_refresh_token'],
          $data['id'],
          $data['user_id'],
          [
            $res['date'] . ' GMT',
            $res['capture_reviews_title'],
            $res['name'],
            $res['rating'],
            $res['tags'],
            $res['review'],
            $res['feedback_text'],
            $res['email']
          ]
        );
        $res = json_decode($res, true);
        if (isset($res['updates'])) {
          $response['google_updates'] = $res['updates'];
        }
      }
      $response['capture_reviews_token'] = $form_token;
      $_SESSION['capture_reviews_token'] = $form_token;
      die(json_encode($response, true));
    }
  }

  if (isset($_POST['step']) && (int)$_POST['step'] == 2) {
    $sql = "UPDATE `custom_reviews` SET `feedback_text` = ? WHERE `id` = ?";
    $stmt = $db->connection->prepare($sql);

    $message = @$_POST['message'];

    $stmt->bind_param(
      'si',
      $message,
      $_POST['insert_id']
    );

    $stmt->execute();

    if (trim($data['redirect_url']) != '') {
      $response =  ['redirect_url' => $data['redirect_url']];
    } else {
      $response = ['redirect_url' => $_SERVER['REQUEST_URI']];
    }

    if (isset($_POST['google_updates'])) {
      $range = $_POST['google_updates'];
      $range = mb_substr($range, mb_strrpos($range, ':') + 1);
      $response['range_0'] = $range;
      $range_word = preg_replace('/[0-9]+/', '', $range);
      $range_word++;
      $range = $range_word . preg_replace('/[^0-9]+/', '', $range);

      $res = Tools::capture_reviews_update_google_sheets_cell($data['google_access_token'], $data['google_refresh_token'], $data['id'], $data['user_id'], $range, $message);
    }
    $response['capture_reviews_token'] = $form_token;
    $_SESSION['capture_reviews_token'] = $form_token;
    die(json_encode($response, true));
  }

  if (isset($_POST['step']) && (int)$_POST['step'] == 3) {
    if (trim($data['redirect_url']) != '') {
      $response =  ['redirect_url' => $data['redirect_url']];
    } else {
      $response = ['redirect_url' => $_SERVER['REQUEST_URI']];
    }

    $response['capture_reviews_token'] = $form_token;
    $_SESSION['capture_reviews_token'] = $form_token;
    die(json_encode($response, true));
  }

  unset($_POST);
  header("Location: " . $_SERVER['REQUEST_URI']);
  exit;
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' &&  $_POST['capture_reviews_token'] != $_SESSION['capture_reviews_token']) {
  die();
}

$_SESSION['capture_reviews_token'] = $form_token;

unset($_POST);
$template_id = 1;
$default_fb_verify_app_id = $db->getGeneralSettings('default_fb_verify_app_id')['default_fb_verify_app_id'];
$template_footer = "
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId      : '" . $default_fb_verify_app_id . "',
      cookie     : true,
      xfbml      : true,
      version    : 'v10.0'
    });
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = \"https://connect.facebook.net/en_US/sdk.js\";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>
";

if (isset($_REQUEST['preview']) && $_REQUEST['preview'] == true) {
  $template = $db->getReviewTemplate($_REQUEST['preview_id']);
} else {
  $template = $db->getReviewTemplate($data['review_template_id']);
}

/*
All templates should use this variable before inlcuding any css, images or js
Example usage:
<link rel="stylesheet" href="<?= $basePath ?>/test.css">
*/
$basePath = '/uploads/templates/' . $template['name'];

include_once(dirname(__FILE__) . $basePath . '/' . $template['template']);
