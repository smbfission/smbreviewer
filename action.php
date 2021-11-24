<?php

@session_start();
require_once('core/database.php');
require_once('core/tools.php');
error_log(print_r($_REQUEST, true));
if (isset($_REQUEST) && isset($_REQUEST['action'])) {
    $db = new Database();


    if ($_REQUEST['action'] == "plans") {
        checkUserSession();

        if ($_REQUEST['id'] != "" && $_REQUEST['id'] != "0") {
            if ($db->updatePlan($_REQUEST)) {
                $_SESSION['msg'] = 'Plan Updated Successfully';
                header('location:/plans/');
            }
        } else {
            $sql = "INSERT INTO `plans`(`title`,`amount`, `description`,`no_of_campaigns`) VALUES ('" . $_REQUEST['title'] . "','" . $_REQUEST['amount'] . "','" . $_REQUEST['description'] . "','" . $_REQUEST['no_of_campaigns'] . "')";
            if ($conn->query($sql)) {
                $_SESSION['msg'] = 'Plan Created Successfully';
                header('location:/plans/');
            }
        }
    } elseif ($_REQUEST['action'] == "custom_reviews") {
        checkUserSession();
        $photo = $_REQUEST['hidden_photo'];

        if (isset($_FILES["photo"]) && $_FILES["photo"]['size'] > 0) {
            $target_dir = 'uploads/';
            $rand = (int)$_SESSION['user_id'] . '_' . time() . '_';
            $target_file = $target_dir . $rand . substr(pathinfo($_FILES["photo"]["name"], PATHINFO_FILENAME), 50);
            $target_file .= '.png';

            $res = Tools::uploadImage($_FILES["photo"]['tmp_name'], $target_file, 150, 150, 2 * 1024 * 1024, (isset($_FILES["photo"]['error']) ? $_FILES["photo"]['error'] : 0));

            if ($res['success'] == false) {
                $_SESSION['msg'] = $res['message'];
            } else {
                @unlink($target_dir . $photo);
                $photo = basename($target_file);
            }
        }

        $icon = $_REQUEST['hidden_icon'];

        if (isset($_FILES["icon"]) && $_FILES["icon"]['size'] > 0) {
            $target_dir = 'uploads/custom_reviews/';
            $rand = (int)$_SESSION['user_id'] . '_' . $_REQUEST['id'] . '_' . time() . '_';
            $target_file = $target_dir . $rand . substr(pathinfo($_FILES["icon"]["name"], PATHINFO_FILENAME), 1, 50);
            $target_file .= '.png';

            $res = Tools::uploadImage($_FILES["icon"]['tmp_name'], $target_file, 30, 30, 2 * 1024 * 1024, (isset($_FILES["icon"]['error']) ? $_FILES["icon"]['error'] : 0));

            if ($res['success'] == false) {
                $_SESSION['msg'] = $res['message'];
            } else {
                @unlink($target_dir . $icon);
                $icon = basename($target_file);
            }
        }


        $_REQUEST['date'] = date("Y-m-d", strtotime($_REQUEST['date']));


        if (@$_REQUEST['id'] != "" && @$_REQUEST['id'] != "0") {


            $sql = "UPDATE `custom_reviews` set `name` = ? ,`rating` = ?, `review` = ?, `date` = ?, `photo` = ?, `user_id` = ?, `icon` = ? WHERE `id` = ?";

            $stmt = $db->connection->prepare($sql);
            $stmt->bind_param(
                'sssssssi',
                $_REQUEST['name'],
                $_REQUEST['rating'],
                $_REQUEST['review'],
                $_REQUEST['date'],
                $photo,
                $_SESSION['user_id'],
                $icon,
                $_REQUEST['id']
            );
            if ($stmt->execute()) {


                $sql = "DELETE FROM `custom_reviews_tags` WHERE `custom_reviews_id`=" . (int)$_REQUEST['id'];
                $db->connection->query($sql);

                if (isset($_REQUEST['tags']) && $tags = explode(",", $_REQUEST['tags'])) {

                    foreach ($tags as $tag) {
                        $tag = trim($tag);
                        if ($tag != '') {

                            $sql = "INSERT INTO `tags`(`user_id`,`name`) VALUES( ?,? ) ON DUPLICATE KEY UPDATE `id`=LAST_INSERT_ID(`id`)";
                            $stmt2 = $db->connection->prepare($sql);
                            $stmt2->bind_param(
                                'is',
                                $_SESSION['user_id'],
                                $tag
                            );
                            $stmt2->execute();
                            $tags_id = $db->connection->insert_id;


                            $sql = "INSERT INTO `custom_reviews_tags`(`tags_id`,`custom_reviews_id`) VALUES(?, ?)";
                            $stmt3 = $db->connection->prepare($sql);
                            $stmt3->bind_param(
                                'ii',
                                $tags_id,
                                $_REQUEST['id']
                            );
                            $stmt3->execute();
                        }
                    }
                }
                if (!isset($_SESSION['msg'])) {
                    @$_SESSION['msg'] = 'Review Updated Successfully';
                }
                header('location:/custom_reviews/');
            }
        } else {
            //            $sql = "INSERT INTO `custom_reviews`(`name`,`rating`, `review`,`date`, `photo`, `user_id`) VALUES ('".$_REQUEST['name']."','".$_REQUEST['rating']."','".$_REQUEST['review']."','".$_REQUEST['date']."','".$photo."','".$_SESSION['user_id']."')";
            $sql = "INSERT INTO `custom_reviews`(`name`,`rating`, `review`,`date`, `photo`, `user_id`) VALUES (?,?,?,?,?,?)";
            $stmt = $db->connection->prepare($sql);
            $stmt->bind_param(
                'sssssi',
                $_REQUEST['name'],
                $_REQUEST['rating'],
                $_REQUEST['review'],
                $_REQUEST['date'],
                $photo,
                $_SESSION['user_id']
            );
            // error_log($sql);

            if ($stmt->execute()) {
                $last_id = $db->connection->insert_id;

                $sql = "DELETE FROM `custom_reviews_tags` WHERE `custom_reviews_id`=" . (int)$last_id;
                $db->connection->query($sql);

                if (isset($_REQUEST['tags']) && $tags = explode(",", $_REQUEST['tags'])) {

                    foreach ($tags as $tag) {
                        $tag = trim($tag);
                        if ($tag != '') {

                            $sql = "INSERT INTO `tags`(`user_id`,`name`) VALUES( ?,? ) ON DUPLICATE KEY UPDATE `id`=LAST_INSERT_ID(`id`)";
                            $stmt2 = $db->connection->prepare($sql);
                            $stmt2->bind_param(
                                'is',
                                $_SESSION['user_id'],
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

                $_SESSION['msg'] = 'Review Created Successfully';
                header('location:/custom_reviews/');
            } else {
                $_SESSION['msg'] = $db->connection->error;
                header('location:/custom_reviews/');
            }
        }
    } elseif ($_REQUEST['action'] == "delete_plan") {
        checkUserSession();
        $sql = "delete from `plans` where id = '" . $_REQUEST['id'] . "'";
        if ($conn->query($sql)) {
            header('location:/plans/');
        }
    } elseif ($_REQUEST['action'] == "delete_review") {
        checkUserSession();
        $sql = "SELECT * FROM `custom_reviews` where `id` = '" . @$_REQUEST['id'] . "' and `user_id` = '" . $_SESSION['user_id'] . "'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        if ($result->num_rows > 0) {
            $target_dir = "uploads/";
            if ($photo = $row['photo']) {
                @unlink($target_dir . $photo);
            }
        }


        $sql = "delete from `custom_reviews` where `id` = '" . $_REQUEST['id'] . "' and `user_id` = '" . $_SESSION['user_id'] . "'";
        if ($conn->query($sql)) {
            header('location:/custom_reviews/');
        }
    } elseif ($_REQUEST['action'] == "profile") {
        checkUserSession();


        $sql_ck = "SELECT * from user where email = '" . $_REQUEST['email'] . "' and id != '" . $_REQUEST['id'] . "'";
        $exe_ck = mysqli_query($conn, $sql_ck);
        $row_ck = mysqli_num_rows($exe_ck);


        if ($row_ck == 0) {
            $current_user = $db->getUser($_REQUEST['id']);


            if ((int)$current_user['use_default_credentials'] == 1) {
                $advanced_settings = ((isset($_REQUEST['advanced_settings']) && $_REQUEST['advanced_settings'] = "on") ? 1 : 0);
            } else {
                $advanced_settings = 1;
            }


            if ($current_user['pwd'] != md5($_REQUEST['old_password']) && trim($_REQUEST['new_password']) != '') {
                $_SESSION['msg_type'] = 'danger';
                $_SESSION['msg'] = 'Old password is not valid';
                header('location:/profile/');
                exit();
            }

            if ($_REQUEST['new_password'] != $_REQUEST['new_password_confirm']) {
                $_SESSION['msg_type'] = 'danger';
                $_SESSION['msg'] = 'Password confirmation does not match';
                header('location:/profile/');
                exit();
            }


            if (trim($_REQUEST['new_password']) != '') {
                $sql = "UPDATE `user` set `name` = '" . $_REQUEST['name'] . "' ,`email` = '" . $_REQUEST['email'] . "', `pwd` = md5('" . $_REQUEST['new_password'] . "'),
                      `phone` = '" . $_REQUEST['phone'] . "', `advanced_settings` = " . $advanced_settings .
                    ", `address` = '" . $_REQUEST['address'] . "'
                       WHERE id = '" . $_REQUEST['id'] . "'";
            } else {
                $sql = "UPDATE `user` set `name` = '" . $_REQUEST['name'] . "' ,`email` = '" . $_REQUEST['email'] . "', phone = '" . $_REQUEST['phone'] . "', advanced_settings = " . $advanced_settings .
                    ", `address` = '" . $_REQUEST['address'] . "'
               WHERE id = '" . $_REQUEST['id'] . "'";
            }

            if ($conn->query($sql)) {
                $db->setActivePlan([
                    'plan_id' => $_REQUEST['plan'],
                    'user_id' => $_REQUEST['id']
                ]);
                $db->updateUserPlanParams([
                    'plan_id' => $_REQUEST['plan'],
                    'user_id' => $_REQUEST['id']
                ]);
                $_SESSION['msg_type'] = 'success';
                $_SESSION['msg'] = 'Profile Updated Successfully';
                $_SESSION['user_adv_settings'] = $advanced_settings;
                header('location:/profile/');
                exit();
            }
        } else {
            $_SESSION['msg_type'] = 'danger';
            $_SESSION['msg'] = 'User with this email already registred, please use other email';
            header('location:/profile/');
            exit();
        }
    } elseif ($_REQUEST['action'] == "member") {
        checkUserSession();
        $sql_plan = "select * from plans where id = '" . (int)@$_REQUEST['plan'] . "'";
        $exe_plan = mysqli_query($conn, $sql_plan);
        $row_plan = mysqli_fetch_assoc($exe_plan);


        if ($_REQUEST['id'] != "" && $_REQUEST['id'] != "0") {
            $sql_ck = "select * from user where email = '" . $_REQUEST['email'] . "' and id != '" . $_REQUEST['id'] . "'";
            $exe_ck = mysqli_query($conn, $sql_ck);
            $row_ck = mysqli_num_rows($exe_ck);
            if ($row_ck == 0) {
                if ($db->updateUser($_REQUEST)) {
                    if (array_key_exists('plan', $_REQUEST)) {
                        $db->updateUserPlan($_REQUEST);
                    }
                    if (array_key_exists('plan', $_REQUEST)) {
                        $db->updateUserType($_REQUEST['id'], $_REQUEST['type']);
                    }
                    $_SESSION['msg'] = 'Member Updated Successfully';
                    header('location:/members/');
                }
            } else {
                $_SESSION['msg'] = 'Email Already Exists';
                header('location:/members/');
            }
        } else {
            $sql_ck = "select * from user where email = '" . $_REQUEST['email'] . "'";
            $exe_ck = mysqli_query($conn, $sql_ck);
            $row_ck = mysqli_num_rows($exe_ck);
            if ($row_ck == 0) {
                // $sql = "INSERT INTO `user`(`name`,`email`, `password`,`address`,`phone`,`type`,`plan`,`no_of_campaigns`) VALUES ('".$_REQUEST['name']."','".$_REQUEST['email']."','".$_REQUEST['password']."','".@$_REQUEST['address']."','".$_REQUEST['phone']."','2','".$_REQUEST['plan']."','".$row_plan['no_of_campaigns']."')";
                $current_date = gmdate('Y-m-d H:i:s');
                $sql = "INSERT INTO `user`(`name`,`email`, `pwd`,`address`,`phone`,`type`,`plan`,`no_of_campaigns`,`fb_reviews_cnt`, `google_reviews_cnt`,`yelp_reviews_cnt`,`date_add`) VALUES ('" .
                    $_REQUEST['name'] . "','" .
                    $_REQUEST['email'] .
                    "',md5('" . $_REQUEST['password'] . "'),'" .
                    $_REQUEST['address'] . "','" .
                    $_REQUEST['phone'] . "'," . $_REQUEST['type'] . "," .
                    $_REQUEST['plan'] . "," .
                    $row_plan['no_of_campaigns'] . ", " .
                    $row_plan['fb_reviews_cnt'] . "," .
                    $row_plan['google_reviews_cnt'] . "," .
                    $row_plan['yelp_reviews_cnt'] . ",'" .
                    $current_date . "')";
                if ($conn->query($sql)) {
                    $_SESSION['msg'] = 'Member Created Successfully';
                    header('location:/members/');
                }
            } else {
                $_SESSION['msg'] = 'Email Already Exist';
                header('location:/members/');
            }
        }
    } elseif ($_REQUEST['action'] == "delete_member") {
        checkUserSession();
        $sql_ck = "select id,fb_page,page_token from campaigns where user_id = '" . $_REQUEST['id'] . "'";
        $exe_ck = mysqli_query($conn, $sql_ck);
        while ($row_ck = mysqli_fetch_assoc($exe_ck)) {
            $json_resp = removePageSubscription($row_ck['fb_page'], $row_ck['page_token']);
        }

        $del_campa = "delete from campaigns where user_id = '" . $_REQUEST['id'] . "'";
        mysqli_query($conn, $del_campa);


        $sql = "select id,app_id,app_secret from user where id = '" . $_REQUEST['id'] . "'";
        $exe = @mysqli_query($conn, $sql);
        $row = @mysqli_fetch_assoc($exe);

        removeAppSubscription($row['app_id'], $row['app_secret']);

        $sql = "delete from `user` where id = '" . $_REQUEST['id'] . "'";
        if ($conn->query($sql)) {
            $_SESSION['msg'] = "Member Successfully Deleted";
        } else {
            $_SESSION['msg'] = "Member Cannot be deleted";
        }
        header('location:/members/');
    } elseif ($_REQUEST['action'] == "delete_msg") {
        checkUserSession();
        $sql = "delete from `messages` where id = '" . $_REQUEST['id'] . "'";
        if ($conn->query($sql)) {
            header('location:/messages/');
        }
    } elseif ($_REQUEST['action'] == "delete_auto") {
        checkUserSession();
        $sql = "delete from `autoresponders` where id = '" . $_REQUEST['id'] . "'";
        if ($conn->query($sql)) {
            header('location:/autoresponders/');
        }
    } elseif ($_REQUEST['action'] == "save_campaigns") {
        checkUserSession();

        // База отдыха "Натали" в Затоке., бул. Золотой Берег, Затока, Одесская область, Украина
        //$current_user= $db->getUser($_SESSION['user_id']);
        // $fb_reviews_cnt = $current_user['fb_reviews_cnt'];
        // $google_reviews_cnt =  $current_user['google_reviews_cnt'];
        // $yelp_reviews_cnt =  $current_user['yelp_reviews_cnt'];
        $fb_reviews_cnt = 1;
        $google_reviews_cnt = 1;
        $yelp_reviews_cnt = 1;

        $sql = "INSERT INTO `campaigns` (`title`,`user_id`,`fb_reviews_cnt`,`google_reviews_cnt`,`yelp_reviews_cnt`) VALUES (?,?,?,?,?)";
        $stmt = $db->connection->prepare($sql);
        $stmt->bind_param(
            'siiii',
            $_REQUEST['title'],
            $_SESSION['user_id'],
            $fb_reviews_cnt,
            $google_reviews_cnt,
            $yelp_reviews_cnt

        );


        if ($stmt->execute()) {
            $insert_id = $db->connection->insert_id;
            header('location:/campaign_add?id=' . $insert_id);
        } else {
            echo $_SESSION['msg'] = $db->connection->error;
            header('location:/campaign/');
        }
    } elseif ($_REQUEST['action'] == "campaigns") {
        checkUserSession();

        // $title=mysqli_real_escape_string($conn, $_REQUEST['title']);
        $title = $_REQUEST['title'];

        if ($page = json_decode(base64_decode($_REQUEST['fb_page']), true)) {
            $fb_page[0] = $page['page_id'];
            $fb_page[1] = $page['page_name'];
            $fb_page[2] = $page['access_token'];
        } else {
            $fb_page = explode("|", $_REQUEST['fb_page']);
        }
        if (isset($_REQUEST['is_facebook'])) {
            $is_facebook = 1;
        } else {
            $is_facebook = 0;
        }

        if (isset($_REQUEST['is_google'])) {
            $is_google = 1;
        } else {
            $is_google = 0;
        }

        if (isset($_REQUEST['is_yelp'])) {
            $is_yelp = 1;
        } else {
            $is_yelp = 0;
        }

        if (isset($_REQUEST['is_custom'])) {
            $is_custom = 1;
        } else {
            $is_custom = 0;
        }

        if (isset($_REQUEST['is_social_share'])) {
            $is_social_share = 1;
        } else {
            $is_social_share = 0;
        }

        if (isset($_REQUEST['display_dp'])) {
            $display_dp = 1;
        } else {
            $display_dp = 0;
        }

        if (isset($_REQUEST['display_date'])) {
            $display_date = 1;
        } else {
            $display_date = 0;
        }

        if (isset($_REQUEST['display_rating'])) {
            $display_rating = 1;
        } else {
            $display_rating = 0;
        }

        if (isset($_REQUEST['display_review'])) {
            $display_review = 1;
        } else {
            $display_review = 0;
        }

        if (isset($_REQUEST['enable_short_io'])) {
            $enable_short_io = 1;
        } else {
            $enable_short_io = 0;
        }

        // $meta_description = mysqli_real_escape_string($con, $_REQUEST['meta_description']);
        $meta_description = $_REQUEST['meta_description'];

        if (@$_REQUEST['id'] != "" && @$_REQUEST['id'] != "0") {
            $sql = "SELECT u.`fb_reviews_cnt`, u.`google_reviews_cnt`, u.`yelp_reviews_cnt`  FROM `campaigns` c, `user` u where c.`id` = '" . @$_REQUEST['id'] . "' and c.`user_id` = u.`id` and c.`user_id` = '" . @$_SESSION['user_id'] . "'";
            $result = $conn->query($sql);
            $row_user = $result->fetch_assoc();
            if ($result->num_rows == 0) {
                die();
            }

            $custom_icon = null;
            if (isset($_FILES["custom_icon"]) && $_FILES["custom_icon"]['size'] > 0) {
                $target_dir = 'uploads/campaign/';
                $rand = (int)$_SESSION['user_id'] . '_' . $_REQUEST['id'] . '_' . time() . "_";
                $target_file = $target_dir . $rand . pathinfo($_FILES["custom_icon"]["name"], PATHINFO_FILENAME);
                $target_file .= '.png';

                $res = Tools::uploadImage($_FILES["custom_icon"]['tmp_name'], $target_file, 25, 25, 2 * 1024 * 1024);

                if ($res['success'] == false) {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['msg'] = $res['message'];
                } else {
                    @unlink($target_dir . $_REQUEST['hidden_custom_icon']);
                    $custom_icon = basename($target_file);
                }
            }
            $meta_picture = null;
            if (isset($_FILES["meta_picture"]) && $_FILES["meta_picture"]['size'] > 0) {
                $target_dir = 'uploads/';
                $rand = (int)$_SESSION['user_id'] . '_' . $_REQUEST['id'] . '_' . time() . "_";
                $target_file = $target_dir . $rand . pathinfo($_FILES["meta_picture"]["name"], PATHINFO_FILENAME);
                $target_file .= '.png';

                $res = Tools::uploadImage($_FILES["meta_picture"]['tmp_name'], $target_file, 600, 600, 2 * 1024 * 1024);

                if ($res['success'] == false) {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['msg'] = $res['message'];
                } else {
                    @unlink($target_dir . $_REQUEST['hidden_meta_picture']);
                    $meta_picture = basename($target_file);
                }
            }

            //
            // $sql = "UPDATE `campaigns` set `title` = '".$title."' ,`fb_page` = '".@$fb_page[0]."', `page_name` = '".@$fb_page[1]."', `page_token` = '".@$fb_page['2']."', `google_business` = '".
            // $_REQUEST['google_business']."', `yelp_business_id` = '".$_REQUEST['yelp_business_id']."', `place_id` = '".$_REQUEST['place_id']."', `is_social_share` = '".$is_social_share."', `meta_title` = '".$_REQUEST['meta_title']."',  `meta_description` = '".$meta_description."',
            // `is_facebook` = '".$is_facebook."', `is_google` = '".$is_google."', `is_yelp` = '".$is_yelp."', `is_custom` = '".$is_custom."',  `style` = '".$_REQUEST['style']."', `widget_bg_color` = '".$_REQUEST['widget_bg_color']."', widget_box_shadow = '".$_REQUEST['widget_box_shadow']."', `name_color` = '".$_REQUEST['name_color']."', `rating_color` = '".$_REQUEST['rating_color']."', date_color = '".
            // $_REQUEST['date_color']."', review_color = '".$_REQUEST['review_color']."', display_dp = '".$display_dp."', display_date = '".$display_date."', display_rating = '".$display_rating."', display_review = '".$display_review."', font_size = '".
            // $_REQUEST['font_size']."', font_family = '".$_REQUEST['font_family']."'".
            // (($custom_icon != null) ? " , `custom_icon`='".$custom_icon."' " : " ").
            // (($meta_picture != null) ? " , `meta_picture`='".$meta_picture."' " : " ").
            // " , `fb_reviews_cnt` = ". (((int)$row_user['fb_reviews_cnt'] > (int)$_REQUEST['fb_reviews_cnt']) ? (int)$_REQUEST['fb_reviews_cnt'] : (int)$row_user['fb_reviews_cnt']).
            // " , `minimum_rate` = ".  (int)$_REQUEST['minimum_rate'].
            // " , `recommendation_type` = '".  $_REQUEST['recommendation_type']."'".
            // " , `google_reviews_cnt` = ". (((int)$row_user['google_reviews_cnt'] > (int)$_REQUEST['google_reviews_cnt']) ? (int)$_REQUEST['google_reviews_cnt'] : (int)$row_user['google_reviews_cnt']).
            // " , `yelp_reviews_cnt` = ". (((int)$row_user['yelp_reviews_cnt'] > (int)$_REQUEST['yelp_reviews_cnt']) ? (int)$_REQUEST['yelp_reviews_cnt'] : (int)$row_user['yelp_reviews_cnt']).
            // " , `order_way` = ".  (int)$_REQUEST['order_way'].
            // " , `reviews_per_page` = ".  (int)$_REQUEST['reviews_per_page'].
            // " , `carousel_timeout` = ".  (int)$_REQUEST['carousel_timeout'].
            // " , `review_text_size` = ".  (int)$_REQUEST['review_text_size'].
            //
            // " WHERE `id` = '".$_REQUEST['id']."'";
            //
            //


            $sql = "UPDATE `campaigns` SET
            `title` = ?,
            `fb_page` = ?,
            `page_name` = ?,
            `page_token` = ?,
            `google_business` = ?,
            `yelp_business_id` = ?,
            `place_id` = ?,
            `is_social_share` = ?,
            `meta_title` = ?,
            `meta_description` = ?,
            `is_facebook` = ?,
            `is_google` = ?,
            `is_yelp` = ?,
            `is_custom` = ?,
            `style` = ?,
            `widget_bg_color` = ?,
            `widget_box_shadow`= ?,
            `name_color` = ?,
            `rating_color` = ?,
            `date_color` = ?,
            `review_color` = ?,
            `display_dp` = ?,
            `display_date` = ?,
            `display_rating` = ?,
            `display_review` =?,
            `font_size` = ?,
            `font_family` = ?,
            `custom_icon`=coalesce(?,`custom_icon`),
            `meta_picture`= coalesce(?,`meta_picture`),
            `minimum_rate` = ?,
            `recommendation_type` = ?,
            `fb_reviews_cnt` = ?,
            `google_reviews_cnt` = ?,
            `yelp_reviews_cnt` = ?,
            `order_way` = ?,
            `reviews_per_page` = ?,
            `carousel_timeout` = ?,
            `review_text_size` = ?,
            `enable_short_io` = ?,
            `short_io_domain` = ?
            WHERE `id` = ?";

            $fb_reviews_cnt = (((int)$row_user['fb_reviews_cnt'] > (int)$_REQUEST['fb_reviews_cnt']) ? (int)$_REQUEST['fb_reviews_cnt'] : (int)$row_user['fb_reviews_cnt']);

            $google_reviews_cnt = (((int)$row_user['google_reviews_cnt'] > (int)$_REQUEST['google_reviews_cnt']) ? (int)$_REQUEST['google_reviews_cnt'] : (int)$row_user['google_reviews_cnt']);
            $yelp_reviews_cnt = (((int)$row_user['yelp_reviews_cnt'] > (int)$_REQUEST['yelp_reviews_cnt']) ? (int)$_REQUEST['yelp_reviews_cnt'] : (int)$row_user['yelp_reviews_cnt']);
            $stmt = $db->connection->prepare($sql);

            $stmt->bind_param(
                'ssssssssssiiiisssssssssssssssssiiissssisi',
                $title,
                $fb_page[0],
                $fb_page[1],
                $fb_page[2],
                $_REQUEST['google_business'],
                $_REQUEST['yelp_business_id'],
                $_REQUEST['place_id'],
                $is_social_share,
                $_REQUEST['meta_title'],
                $meta_description,
                $is_facebook,
                $is_google,
                $is_yelp,
                $is_custom,
                $_REQUEST['style'],
                $_REQUEST['widget_bg_color'],
                $_REQUEST['widget_box_shadow'],
                $_REQUEST['name_color'],
                $_REQUEST['rating_color'],
                $_REQUEST['date_color'],
                $_REQUEST['review_color'],
                $display_dp,
                $display_date,
                $display_rating,
                $display_review,
                $_REQUEST['font_size'],
                $_REQUEST['font_family'],
                $custom_icon,
                $meta_picture,
                $_REQUEST['minimum_rate'],
                $_REQUEST['recommendation_type'],
                $fb_reviews_cnt,
                $google_reviews_cnt,
                $yelp_reviews_cnt,
                $_REQUEST['order_way'],
                $_REQUEST['reviews_per_page'],
                $_REQUEST['carousel_timeout'],
                $_REQUEST['review_text_size'],
                $enable_short_io,
                $_REQUEST['short_io_domain'],
                $_REQUEST['id']
            );


            // $sql = "UPDATE `campaigns` SET
            // `title` = '".$title.
            // "' ,`fb_page` = '".@$fb_page[0].
            // "', `page_name` = '".@$fb_page[1].
            // "', `page_token` = '".@$fb_page['2'].
            // "', `google_business` = '".$_REQUEST['google_business'].
            // "', `yelp_business_id` = '".$_REQUEST['yelp_business_id'].
            // "', `place_id` = '".$_REQUEST['place_id'].
            // "', `is_social_share` = '".$is_social_share.
            // "', `meta_title` = '".$_REQUEST['meta_title'].
            // "',  `meta_description` = '".$meta_description.
            // "', `is_facebook` = '".$is_facebook.
            // "', `is_google` = '".$is_google.
            // "', `is_yelp` = '".$is_yelp.
            // "', `is_custom` = '".$is_custom.
            // "',  `style` = '".$_REQUEST['style'].
            // "', `widget_bg_color` = '".$_REQUEST['widget_bg_color'].
            // "', widget_box_shadow = '".$_REQUEST['widget_box_shadow'].
            // "', `name_color` = '".$_REQUEST['name_color'].
            // "', `rating_color` = '".$_REQUEST['rating_color'].
            // "', date_color = '".$_REQUEST['date_color'].
            // "', review_color = '".$_REQUEST['review_color'].
            // "', display_dp = '".$display_dp.
            // "', display_date = '".$display_date.
            // "', display_rating = '".$display_rating.
            // "', display_review = '".$display_review.
            // "', font_size = '".$_REQUEST['font_size'].
            // "', font_family = '".$_REQUEST['font_family'].
            // "'".(($custom_icon != null) ? " , `custom_icon`='".$custom_icon."' " : " ").
            // (($meta_picture != null) ? " , `meta_picture`='".$meta_picture."' " : " ").
            // " , `fb_reviews_cnt` = ". (((int)$row_user['fb_reviews_cnt'] > (int)$_REQUEST['fb_reviews_cnt']) ? (int)$_REQUEST['fb_reviews_cnt'] : (int)$row_user['fb_reviews_cnt']).
            // " , `minimum_rate` = ".  (int)$_REQUEST['minimum_rate'].
            // " , `recommendation_type` = '".  $_REQUEST['recommendation_type']."'".
            // " , `google_reviews_cnt` = ". (((int)$row_user['google_reviews_cnt'] > (int)$_REQUEST['google_reviews_cnt']) ? (int)$_REQUEST['google_reviews_cnt'] : (int)$row_user['google_reviews_cnt']).
            // " , `yelp_reviews_cnt` = ". (((int)$row_user['yelp_reviews_cnt'] > (int)$_REQUEST['yelp_reviews_cnt']) ? (int)$_REQUEST['yelp_reviews_cnt'] : (int)$row_user['yelp_reviews_cnt']).
            // " , `order_way` = ".  (int)$_REQUEST['order_way'].
            // " , `reviews_per_page` = ".  (int)$_REQUEST['reviews_per_page'].
            // " , `carousel_timeout` = ".  (int)$_REQUEST['carousel_timeout'].
            // " , `review_text_size` = ".  (int)$_REQUEST['review_text_size'].
            // " WHERE `id` = '".$_REQUEST['id']."'";

            //            if ($conn->query($sql)) {
            if ($stmt->execute()) {

                $sql = "DELETE FROM `campaigns_tags` WHERE `campaigns_id`=" . (int)$_REQUEST['id'];
                $db->connection->query($sql);

                if ((isset($_REQUEST['tags'])) && ($tags = $_REQUEST['tags']) && (is_array($tags))) {

                    foreach ($tags as $tag) {

                        $sql = "INSERT INTO `campaigns_tags`(`tags_id`,`campaigns_id`) VALUES(?, ?)";
                        $stmt2 = $db->connection->prepare($sql);
                        $stmt2->bind_param(
                            'ii',
                            $tag,
                            $_REQUEST['id']
                        );
                        $stmt2->execute();
                    }
                }

                // if ($file_icon != null) {
                //     $sql = "UPDATE `campaigns` set `custom_icon` = ? WHERE `id` = '".$_REQUEST['id']."'";
                //     $stmt = $conn->prepare($sql);
                //     $null = null;
                //     $stmt->bind_param('b', $null);
                //     $stmt->send_long_data(0, $file_icon);
                //     $stmt->execute();
                // }

                header('X-LiteSpeed-Purge: /reviews?id=' . base64_encode($_REQUEST['id']) . '&src=embed');

                // $foo=purgeCacheUrl('/reviews.php?id='.base64_encode($_REQUEST['id']).'&src=embed');


                header('location:' . $_SERVER['HTTP_REFERER']);
            }
        } else {
            $sql = "INSERT INTO `campaigns`
            (`title`,`fb_page`,`page_name`,`page_token`,`google_business`, `yelp_business_id`, `place_id` , `is_facebook`, `is_google`, `is_yelp`, `style`, `name_color`, `rating_color`, `date_color`,`review_color`,`display_dp`,`display_date`,`display_rating`,`display_review`,`font_size`,`font_family`,`user_id`,`widget_bg_color`,`widget_box_shadow`,`is_social_share`,`meta_title`,`meta_description`,`meta_picture`, `is_custom`, `enable_short_io`, `short_io_domain`) VALUES
            ('" . $title . "','" . @$fb_page[0] . "','" . @$fb_page[1] . "','" . @$fb_page[2] . "','" . $_REQUEST['google_business'] . "','" . $_REQUEST['yelp_business_id'] . "','" . $_REQUEST['place_id'] . "','" . $is_facebook . "','" . $is_google . "','" . $is_yelp . "','" . $_REQUEST['style'] . "','" . $_REQUEST['name_color'] . "','" . $_REQUEST['rating_color'] . "', '" . $_REQUEST['date_color'] . "','" . $_REQUEST['review_color'] . "','" . $display_dp . "','" . $display_date . "','" . $display_rating . "','" . $display_review . "','" . $_REQUEST['font_size'] . "','" . $_REQUEST['font_family'] . "','" . $_SESSION['user_id'] . "','" . $_REQUEST['widget_bg_color'] . "','" . $_REQUEST['widget_box_shadow'] . "','" . $is_social_share . "','" . $_REQUEST['meta_title'] . "','" . $meta_description . "','" . $meta_picture . "','" . $_REQUEST['is_custom'] . "','" . $enable_short_io . "','" . $_REQUEST['short_io_domain'] . "')";
            if ($conn->query($sql)) {
                $_SESSION['msg'] = 'Campaign added successfully';
                header('location:/campaign/');
            } else {
                echo $_SESSION['msg'] = $conn->error;
                header('location:/campaign/');
            }
        }

        die('exit' . print_r($_REQUEST, true) . $sql . print_r($conn->error, true));
    } elseif ($_REQUEST['action'] == "delete_camp") {
        checkUserSession();
        $sql = "delete from `campaigns` where id = '" . $_REQUEST['id'] . "'";
        if ($conn->query($sql)) {
            header('location:/campaign/');
        }
    } elseif ($_REQUEST['action'] == "campaign_reviews") {

        checkUserSession();
        $current_user = $db->getUser($_SESSION['user_id']);
        $result = '{"data":""}';

        switch ($_REQUEST['review_type']) {
            case 'facebook':
                $page = json_decode(base64_decode($_REQUEST['page_id']), true);
                $url = "https://graph.facebook.com/" . $page['page_id'] . "/ratings?access_token=" . $page['access_token'] . "&limit=" . (int)$_REQUEST['limit'] . "&fields=created_time,has_rating,has_review,open_graph_story,rating,review_text,reviewer{name,id,picture},recommendation_type";

                $result = Tools::post_fb($url, "get");
                $result = Tools::makeReviewsArray(json_decode($result, true), $_REQUEST['review_type'], (int)$_REQUEST['limit']);

                $result = json_encode(['data' => (json_encode($result, JSON_FORCE_OBJECT))], JSON_FORCE_OBJECT);

                break;
            case 'google':

                $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=" . $_REQUEST['place_id'] . "&fields=name,rating,formatted_phone_number,reviews&key=" . $current_user['google_key'];
                $result = Tools::post_fb($url, "get");
                $result = Tools::makeReviewsArray(json_decode($result, true), $_REQUEST['review_type'], (int)$_REQUEST['limit']);
                $result = json_encode(['data' => (json_encode($result, JSON_FORCE_OBJECT))], JSON_FORCE_OBJECT);
                break;
            case 'yelp':

                $business_id = $_REQUEST['business_id'];
                $url = "https://api.yelp.com/v3/businesses/$business_id/reviews";
                $result = Tools::post_fb($url, "get", "", $current_user['yelp_api_key']);
                $result = Tools::makeReviewsArray(json_decode($result, true), $_REQUEST['review_type'], (int)$_REQUEST['limit']);

                $result = json_encode(['data' => (json_encode($result, JSON_FORCE_OBJECT))], JSON_FORCE_OBJECT);

                break;
            case 'custom':

                $url = Tools::iteURL() . "/review_api.php?uid=" . $_SESSION['user_id'] . "&campaigns_id=" . $_REQUEST['campaigns_id'];

                $result = Tools::post_fb($url, "get");
                $result = Tools::makeReviewsArray(json_decode($result, true), $_REQUEST['review_type'], (int)$_REQUEST['limit']);
                $result = json_encode(['data' => (json_encode($result, JSON_FORCE_OBJECT))], JSON_FORCE_OBJECT);

                break;
        }


        die($result);
    } elseif ($_REQUEST['action'] == "page_reviews") {
        $page_id = explode("|", $_REQUEST['page_id']);
        $url = "https://graph.facebook.com/$page_id[0]/ratings?access_token=$page_id[2]&limit=100&fields=created_time,has_rating,has_review,open_graph_story,rating,review_text,reviewer";

        $json = Tools::post_fb($url, "get");
        echo $json;
        die();
    } elseif ($_REQUEST['action'] == "place_reviews") {
        $sql = "select google_key from user where id = '" . $_SESSION['user_id'] . "'";
        $exe = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($exe);

        $place_id = $_REQUEST['place_id'];
        $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=$place_id&fields=name,rating,formatted_phone_number,reviews&key=$row[google_key]";
        $json = Tools::post_fb($url, "get");
        echo $json;
        die();
    } elseif ($_REQUEST['action'] == "yelp_reviews") {
        $sql = "select yelp_api_key from user where id = '" . $_SESSION['user_id'] . "'";
        $exe = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($exe);

        $business_id = $_REQUEST['business_id'];
        $url = "https://api.yelp.com/v3/businesses/$business_id/reviews";
        echo $yelp_json = Tools::post_fb($url, "get", "", $row['yelp_api_key']);
        die();
    } elseif ($_REQUEST['action'] == "update_user_plan_dates") {
        if (!isset($_SESSION['user_id'])) {
            $res = ['success' => false, 'message' => 'Access denied '];
            die(json_encode($res, true));
        }


        $id = str_replace("history", "", $_POST['id']);

        if ($id != 0) {
            if (strtotime($_POST['date_start']) > strtotime($_POST['date_stop'])) {
                $res = ['success' => false, 'message' => 'Start date must be greater than stop'];
                die(json_encode($res, true));
            }
            $data = [
                'id' => $id,
                'date_start' => $_POST['date_start'],
                'date_stop' => $_POST['date_stop'],

            ];

            if ($db->updateUserPlanPeriod($data)) {
                $res = ['success' => true, 'message' => 'Updated'];
                die(json_encode($res, true));
            }
        }
        $res = ['success' => false, 'message' => 'Access denied '];
        die(json_encode($res, true));
    } elseif ($_REQUEST['action'] == "validate_email") {
        $sql = "select `id` from `user` where md5(`email`)='" . substr($_REQUEST['key'], 32) . "' and `validate_email_request_token` is not null and `validate_email_request_token`='" . $_REQUEST['key'] . "'";
        $res = $db->connection->query($sql);
        if ($res->num_rows) {
            $row = $res->fetch_assoc();

            $sql = "UPDATE `user` SET `validate_email_request_token` = NULL, `status`=1  WHERE `id`=" . (int)$row['id'];
            $db->connection->query($sql);

            $_SESSION['msg'] = "Email validation completed, now you can login.";
            header('location:/');
            exit();
        }

        $_SESSION['msg'] = "Invalid validation link, try to login or reset password";
        header('location:/');
        exit();
    } elseif ($_REQUEST['action'] == "import_reviews") {


        $current_user = $db->getUser(@$_SESSION['user_id']);

        if ($current_user == null || (int)$current_user['status'] == 0) {
            die(json_encode(['success' => false, 'message' => 'User is not logged in']));
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {


            $phpFileUploadErrors = array(
                0 => 'There is no error, the file uploaded with success',
                1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                3 => 'The uploaded file was only partially uploaded',
                4 => 'No file was uploaded',
                6 => 'Missing a temporary folder',
                7 => 'Failed to write file to disk.',
                8 => 'A PHP extension stopped the file upload.',
            );

            if ((isset($_FILES['file'])) && (($_FILES['file']['error'] !== 0) || ($_FILES['file']['tmp_name'] == ''))) {

                die(json_encode(['success' => false, 'message' => $phpFileUploadErrors[$_FILES['file']['error']]]));
            } elseif (isset($_FILES['file']) && $_FILES['file']['tmp_name'] != '') {

                require_once(__DIR__ . '/core/import.php');
                $import = new Import();
                try {
                    $spreadsheets = $import->getWorkSheetNames($_FILES['file']['tmp_name']);
                    $tmp_file_name = __DIR__ . '/uploads/custom_reviews/tmp_' . time() . '_' . $_FILES['file']['name'];
                    move_uploaded_file($_FILES['file']['tmp_name'], $tmp_file_name);
                    $_SESSION['tmp_file_name'] = $tmp_file_name;
                } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                    die(json_encode(['success' => false, 'message' => $e->getMessage()]));
                }

                if ((isset($spreadsheets)) && (is_array($spreadsheets))) {
                    $data = null;
                    foreach ($spreadsheets as $value) {
                        $data[$value] = $import->readExcelFile($_SESSION['tmp_file_name'], 1, 1, null, $value);
                    }
                    $_SESSION['sheet_names'] = $data;
                    die(json_encode(['success' => true, 'data' => $spreadsheets]));
                }
            }


            if (isset($_POST['sheet_selected'])) {

                die(json_encode(['success' => true, 'data' => $_SESSION['sheet_names'][$_POST['sheet_selected']]]));


                die(json_encode(['success' => true, 'data' => $data]));
            }

            if (isset($_POST['sheet_selected_import']) && isset($_SESSION['tmp_file_name']) && file_exists($_SESSION['tmp_file_name'])) {


                $data = $_POST['sheet_selected_import'];


                $sheet_name = $data['sheet_name'];
                $options = array_column($data['options'], 'value', 'name');

                require_once(__DIR__ . '/core/import.php');
                $import = new Import();
                try {
                    $spreadsheet = $import->readExcelFile($_SESSION['tmp_file_name'], 1, 0, array_diff($options, ["0"]), $sheet_name);
                } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                    die(json_encode(['success' => false, 'message' => $e->getMessage()]));
                }

                $options = ($options);
                if ((isset($spreadsheet)) && (is_array($spreadsheet))) {
                    if ($data['skip_first_row'] == "true") {
                        array_shift($spreadsheet);
                    }

                    $r_cnt = count($spreadsheet);

                    $data = null;
                    $tags = [];
                    foreach ($spreadsheet as $value) {
                        $row = [];
                        foreach ($options as $k => $v) {
                            $row[$k] = $db->connection->real_escape_string($value[$v]);
                            // if ($k!="tags") {
                            //     $row[$k]=$db->connection->real_escape_string($value[$v]);
                            // } else {
                            //   $tags[]=$value[$v];
                            // }
                        }
                        $row = "('" . implode("','", $row) . "'," . $_SESSION['user_id'] . ")";

                        $data[] = $row;
                    }
                    $fields = '';
                    foreach ($options as $key => $value) {
                        // if ($key!="tags") {
                        //  $fields.='`'.$key.'`,';
                        //  }
                        $fields .= '`' . $key . '`,';
                    }
                    $fields .= '`user_id`';
                    $data = "INSERT INTO `custom_reviews`(" . $fields . ") VALUES " . implode(",", $data) . ";";
                    if ($db->connection->query($data) === TRUE) {
                        $message = 'Successfully imported ' . $db->connection->affected_rows . ' review(s).';
                    } else {
                        $message = 'Error: ' . $db->connection->error;
                    }

                    $db->updateCustomReviewsTempTags($_SESSION['user_id']);
                    $db->updateCustomReviewsImages($_SESSION['user_id']);
                }
                @unlink($_SESSION['tmp_file_name']);
                unset($_SESSION['tmp_file_name']);

                die(json_encode(['success' => true, 'message' => $message, 'data' => $data]));
            }
        }

        die(json_encode(['success' => false, 'message' => 'error 😕']));
    } elseif ($_REQUEST['action'] == "capture_reviews") {
        checkUserSession();

        $logo = $_REQUEST['hidden_logo'];
        if (isset($_FILES["logo"]) && $_FILES["logo"]['size'] > 0) {
            $target_dir = 'uploads/capture_reviews/';
            $rand = (int)$_SESSION['user_id'] . '_' . time() . '_';
            $target_file = $target_dir . $rand . substr(pathinfo($_FILES["logo"]["name"], PATHINFO_FILENAME), 50);
            $target_file .= '.png';

            $res = Tools::uploadImage($_FILES["logo"]['tmp_name'], $target_file, 300, 300, 2 * 1024 * 1024, (isset($_FILES["logo"]['error']) ? $_FILES["logo"]['error'] : 0));

            if ($res['success'] == false) {
                $_SESSION['msg'] = $res['message'];
            } else {
                @unlink($target_dir . $logo);
                $logo = basename($target_file);
            }
        }

        $custom_logo = $_REQUEST['hidden_custom_logo'];
        if (isset($_FILES["custom_logo"]) && $_FILES["custom_logo"]['size'] > 0) {
            $target_dir = 'uploads/capture_reviews/';
            $rand = (int)$_SESSION['user_id'] . '_' . time() . '_';
            $target_file = $target_dir . $rand . substr(pathinfo($_FILES["custom_logo"]["name"], PATHINFO_FILENAME), 50);
            $target_file .= '.png';

            $res = Tools::uploadImage($_FILES["custom_logo"]['tmp_name'], $target_file, 150, 150, 2 * 1024 * 1024, (isset($_FILES["custom_logo"]['error']) ? $_FILES["custom_logo"]['error'] : 0));

            if ($res['success'] == false) {
                $_SESSION['msg'] = $res['message'];
            } else {
                @unlink($target_dir . $custom_logo);
                $custom_logo = basename($target_file);
            }
        }


        if ((int)@$_REQUEST['id'] == 0) {
            $_REQUEST['id'] = (int)$db->addCaptureReviews([
                'user_id' => $_SESSION['user_id'],
            ]);
        }

        if (@$_REQUEST['id'] != "" && @$_REQUEST['id'] != "0") {
            $db->updateCaptureReviews([
                'id' => $_REQUEST['id'],
                'type' => $_REQUEST['review-type'],
                'title' => $_REQUEST['title'],
                'logo' => $logo,
                'name_of_business' => $_REQUEST['name_of_business'],
                'page_title' => $_REQUEST['page_title'],
                'description' => $_REQUEST['description'],
                'reward' => $_REQUEST['reward'],
                'reward_webhook' => trim($_REQUEST['reward_webhook']),
                'footer_text' => $_REQUEST['footer_text'],
                'min_rating' => $_REQUEST['min_rating'],
                'user_id' => $_SESSION['user_id'],
                'redirect_url' => $_REQUEST['redirect_url'],

                'enable_review_directories_google' => (int)@$_REQUEST['enable_review_directories_google'],
                'review_directories_google' => @$_REQUEST['review_directories_google'],

                'enable_review_directories_facebook' => (int)@$_REQUEST['enable_review_directories_facebook'],
                'review_directories_facebook' => @$_REQUEST['review_directories_facebook'],

                'enable_review_directories_yelp' => (int)@$_REQUEST['enable_review_directories_yelp'],
                'review_directories_yelp' => @$_REQUEST['review_directories_yelp'],


                'enable_review_directories_custom' => (int)@$_REQUEST['enable_review_directories_custom'],
                'review_directories_custom' => @$_REQUEST['review_directories_custom'],

                'custom_logo' => $custom_logo,
                'primary_font_family' => @$_REQUEST['primary_font_family'],
                'primary_font_color' => @$_REQUEST['primary_font_color'],
                'secondary_font_family' => @$_REQUEST['secondary_font_family'],
                'secondary_font_color' => @$_REQUEST['secondary_font_color'],

                'enable_email_for_receiving_negative_review' => @(int)$_REQUEST['enable_email_for_receiving_negative_review'],
                'email_for_receiving_negative_review' => @$_REQUEST['email_for_receiving_negative_review'],
                'enable_short_io' => @(int)$_REQUEST['enable_short_io'],
                'short_io_api_key' => @$_REQUEST['short_io_api_key'],
                'short_io_domain' => @$_REQUEST['short_io_domain'],
                'youtube' => @$_REQUEST['youtube'],
                'enable_google_sheets' => @(int)$_REQUEST['enable_google_sheets'],
                'share_linkedin' => (int) $_REQUEST['linkedin'],
                'share_facebook' => (int) $_REQUEST['facebook'],
                'share_twitter' => (int) $_REQUEST['twitter'],
                'review_template_id' => (int) $_REQUEST['review_template_id']
            ]);

            if ((int)$_REQUEST['enable_google_sheets'] == 0) {
                $data = [
                    'google_access_token' => "",
                    'google_refresh_token' => "",
                    'id' => $_REQUEST['id'],
                    'user_id' => $_SESSION['user_id'],
                ];

                $db->updateCaptureReviewsGoogleTokens($data);

                $data = [
                    'google_spread_sheet_id' => '',
                    'google_sheet_id' => '',
                    'id' => $_REQUEST['id'],
                    'user_id' => $_SESSION['user_id']
                ];
                $db->updateCaptureReviewsGoogleSheetID($data);
            } else {
                $data = [
                    'google_spread_sheet_id' => $_REQUEST['google_spread_sheet_id'],
                    'google_sheet_id' => $_REQUEST['google_sheet_id'],
                    'id' => $_REQUEST['id'],
                    'user_id' => $_SESSION['user_id']
                ];
                $db->updateCaptureReviewsGoogleSheetID($data);
            }


            if (isset($_REQUEST['tags']) && $tags = explode(",", $_REQUEST['tags'])) {
                $sql = "DELETE FROM `capture_reviews_tags` WHERE `capture_reviews_id`=" . (int)$_REQUEST['id'];
                $db->connection->query($sql);

                foreach ($tags as $tag) {
                    $tag = trim($tag);
                    if ($tag != '') {

                        $sql = "INSERT INTO `tags`(`user_id`,`name`) VALUES( ?,? ) ON DUPLICATE KEY UPDATE `id`=LAST_INSERT_ID(`id`)";
                        $stmt2 = $db->connection->prepare($sql);
                        $stmt2->bind_param(
                            'is',
                            $_SESSION['user_id'],
                            $tag
                        );
                        $stmt2->execute();
                        $tags_id = $db->connection->insert_id;


                        $sql = "INSERT INTO `capture_reviews_tags`(`tags_id`,`capture_reviews_id`) VALUES(?, ?)";
                        $stmt3 = $db->connection->prepare($sql);
                        $stmt3->bind_param(
                            'ii',
                            $tags_id,
                            $_REQUEST['id']
                        );
                        $stmt3->execute();
                    }
                }
            }


            if (!isset($_SESSION['msg'])) {
                @$_SESSION['msg'] = 'Review Updated Successfully';
            }
            header('location:/capture_reviews/');
        }
    } elseif ($_REQUEST['action'] == "delete_capture_reviews") {
        checkUserSession();
        $sql = "delete from `capture_reviews` where id = " . (int)$_REQUEST['id'] . " and user_id=" . (int)$_SESSION['user_id'];
        if ($db->connection->query($sql)) {
            header('location:/capture_reviews/');
        }
    } elseif ($_REQUEST['action'] == "clone_capture_reviews") {

        if ($db->cloneCaptureReviewCampaignById((int)$_REQUEST['id'])) {
            header('location:/capture_reviews/');
        }
    } elseif ($_REQUEST['action'] == "capture_reviews_get") {
        $result = $db->getCaptureReviewsByIdUserId($_REQUEST['id'], $_REQUEST['user_id']);

        $result = json_encode(['result' => $result], true);

        die($result);  // code...
    } elseif ($_REQUEST['action'] == "export_capture_reviews") {
        checkUserSession();
        $result = [];
        foreach ($db->getCustomReviewsByCaptureIdUserId($_REQUEST['id'], $_REQUEST['user_id']) as $key => $value) {
            if (trim($value['photo']) != '') {
                $value['phone'] = Tools::siteURL() . '/uploads/' . $value['photo'];
            }

            if (trim($value['icon']) != '') {
                $value['icon'] = Tools::siteURL() . '/uploads/' . $value['icon'];
            }
            $result[] = $value;
        }
        require_once(dirname(__FILE__) . '/core/export.php');
        $result = Export::exportToExcel($result);
        $result = json_encode($result, true);
        die($result);
    } elseif ($_REQUEST['action'] == "capture_reviews_enable_google_sheets" && strpos($_REQUEST['state'], "ggl_rsp_rcvd") === 0) {
        $gs = $db->getGeneralSettings();
        $client_id = $gs['default_google_client_id'];
        $client_secret = $gs['default_google_client_secret'];
        $redirect_uri = Tools::siteURL() . '/action.php?action=capture_reviews_enable_google_sheets';

        $url = "https://oauth2.googleapis.com/token";
        $data = [
            "code" => $_REQUEST['code'],
            "client_id" => $client_id,
            "client_secret" => $client_secret,
            "grant_type" => "authorization_code",
            "redirect_uri" => $redirect_uri,
        ];

        $result = Tools::post($url, "post", ['Authorization: Bearer ' . $_REQUEST['google_access_token'], 'Content-Type: application/x-www-form-urlencoded'], http_build_query($data));

        if (isset(json_decode($result, true)['access_token']) && isset(json_decode($result, true)['refresh_token'])) {
            $state = $_REQUEST['state'];
            parse_str(base64_decode(parse_url($state, PHP_URL_QUERY)), $state);

            $data = [
                'google_access_token' => json_decode($result, true)['access_token'],
                'google_refresh_token' => json_decode($result, true)['refresh_token'],
                'id' => $state['id'],
                'user_id' => $state['uid']
            ];

            $db->updateCaptureReviewsGoogleTokens($data);
            die('<script type="text/javascript">window.close();</script>');
        } else {
            $result = json_encode($result, true);
            die($result);
        }
    } elseif ($_REQUEST['action'] == "capture_reviews_enable_google_sheets") {
        $gs = $db->getGeneralSettings();

        $client_id = $gs['default_google_client_id'];
        $client_secret = $gs['default_google_client_secret'];

        $redirect_uri = Tools::siteURL() . '/action.php?action=capture_reviews_enable_google_sheets';

        $data = [
            "scope" => "https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/spreadsheets",
            "client_id" => $client_id,
            "access_type" => "offline",
            "prompt" => "consent",  // without this paramtere no refresh_token in response
            "state" => "ggl_rsp_rcvd?" . base64_encode("id=" . $_REQUEST['id'] . "&uid=" . (int)$_SESSION['user_id']),
            "include_granted_scopes" => "true",
            "response_type" => "code",
            "redirect_uri" => $redirect_uri,
        ];
        $login_url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query($data);


        $result = ['login_url' => $login_url];
        $result = json_encode(['result' => $result], true);

        die($result);
    } elseif ($_REQUEST['action'] == "capture_reviews_check_access_google_sheets") {

        $res = Tools::capture_reviews_check_access_google_sheets($_REQUEST['google_access_token'], $_REQUEST['google_refresh_token'], $_REQUEST['id'], $_REQUEST['user_id']);

        $result = $res;

        die($result);
    } elseif ($_REQUEST['action'] == "capture_reviews_set_google_sheets") {
        $data = [
            'google_spread_sheet_id' => $_REQUEST['google_spread_sheet_id'],
            'google_sheet_id' => $_REQUEST['google_sheet_id'],
            'id' => $_REQUEST['id'],
            'user_id' => $_SESSION['user_id']
        ];
        $db->updateCaptureReviewsGoogleSheetID($data);

        $data = Tools::capture_reviews_get_google_sheets($_REQUEST['google_spread_sheet_id'], $_REQUEST['id'], $_SESSION['user_id']);
        $data = json_decode($data, true);

        $data['google_sheet_id'] = $_REQUEST['google_sheet_id'];
        $result = json_encode(['result' => $data], true);
        die($result);
    } elseif ($_REQUEST['action'] == "capture_reviews_get_google_sheets") {
        $result = Tools::capture_reviews_get_google_sheets($_REQUEST['google_spread_sheet_id'], $_REQUEST['id'], $_REQUEST['user_id']);

        $result = json_decode($result, true);
        $result = json_encode(['result' => $result], true);

        die($result);
    } elseif ($_REQUEST['action'] == "capture_reviews_set_short_io") {
        $short_io_domain_link = $_REQUEST['original_url'];

        $link_result = json_decode(Tools::capture_reviews_get_short_io_by_origin_url($_REQUEST['short_io_api_key'], ['domain' => $_REQUEST['short_io_domain'], 'originalURL' => $short_io_domain_link]), true);
        if ($link_result['error']) {
            $link_result = Tools::capture_reviews_add_short_io($_REQUEST['short_io_api_key'], ['domain' => $_REQUEST['short_io_domain'], 'originalURL' => $short_io_domain_link]);
            $link_result = json_decode($link_result, true)['shortURL'];
        } else {
            $link_result = $link_result['shortURL'];
        }

        $result = json_encode([
            'short_io_domain' => $_REQUEST['short_io_domain'],
            'link_result' => $link_result,
            'or' => $or,
        ], true);

        die($result);
    } elseif ($_REQUEST['action'] == "capture_reviews_update_short_io") {
        $short_io_domain_link = $_REQUEST['original_url'];
        $link_result = json_decode(Tools::capture_reviews_get_short_io_by_origin_url($_REQUEST['short_io_api_key'], ['domain' => $_REQUEST['short_io_domain'], 'originalURL' => $short_io_domain_link]), true);
        if ($link_result['error']) {
            $link_result = Tools::capture_reviews_add_short_io($_REQUEST['short_io_api_key'], ['domain' => $_REQUEST['short_io_domain'], 'originalURL' => $short_io_domain_link]);
            $id = json_decode($link_result, true)['id'];
        } else {
            $id = $link_result['id'];
        }

        $update_result = json_decode(Tools::capture_reviews_update_short_io($_REQUEST['short_io_api_key'], $id, ['domain' => $_REQUEST['short_io_domain'], 'originalURL' => $short_io_domain_link, 'path' => $_REQUEST['short_io_custom_handle']]), true);

        $result = json_encode([
            'link_result' => $update_result['shortURL'],
            'error' => $update_result['error']
        ], true);
        die($result);
    } elseif ($_REQUEST['action'] == "review_template_add") {
        $templatesDir = './uploads/templates/' . $_REQUEST['template_name'] . '/';
        mkdir($templatesDir, 0777, true);

        if ($_FILES['resources']) {
            $total = count($_FILES['resources']['name']);
            for ($i = 0; $i < $total; $i++) {
                $tmpFilePath = $_FILES['resources']['tmp_name'][$i];
                if ($tmpFilePath != "") {
                    $newFilePath = $templatesDir . $_FILES['resources']['name'][$i];
                    move_uploaded_file($tmpFilePath, $newFilePath);
                }
            }
        }

        $template = '';
        if (isset($_REQUEST['template_code']) && $_REQUEST['template_code'] != "") {
            $rand = $_REQUEST['template_name'] . '_' . time();
            $target_file = $rand . substr(pathinfo($_FILES["template"]["name"], PATHINFO_FILENAME), 50);
            $target_file .= '.php';

            $template = $target_file;
            $response = '';
            $target_file = $templatesDir . $target_file;
            $templateFile = fopen($target_file, "w") or die("Unable to save template file!");
            fwrite($templateFile, $_REQUEST['template_code']);
        } else {
            $template = $currentTemplate['template'];
        }

        $preview_image = '';
        if (isset($_FILES["preview-image"]) && $_FILES["preview-image"]['size'] > 0) {
            $rand = $_REQUEST['template_name'] . '_' . time();
            $target_file = $rand . substr(pathinfo($_FILES["preview-image"]["name"], PATHINFO_FILENAME), 50);
            $target_file .= "." . end((explode(".", $_FILES["preview-image"]["name"])));

            $preview_image = $target_file;
            $target_file = $templatesDir . $target_file;
            if (move_uploaded_file($_FILES["preview-image"]["tmp_name"], $target_file)) {
                $response .= "The file " . htmlspecialchars(basename($_FILES["preview-image"]["name"])) . " has been uploaded.";
            } else {
                $response .= "Sorry, there was an error uploading your file.";
            }
        }
         $foreuser=$_REQUEST['template_forfreeuser']=="on"?true:false;
             $result = $db->insertReviewTemplate($_REQUEST['template_name'], $template, $preview_image, $_REQUEST['template_islive'], $foreuser);

        $result = json_encode([
            'response' => $result,
            'preview_image' => $preview_image,
            'template' => $template,
            'islive'=>$_REQUEST['template_islive'],
            'forfreeuser'=>$_REQUEST['template_forfreeuser']
        ], true);

        header("Location: /review_template_settings.php");

        die($result);
    } elseif ($_REQUEST['action'] == "delete_review_template") {
        $templateId = (int) $_REQUEST['id'];

        $template = $db->getReviewTemplate($templateId);
        $result = $db->deleteReviewTemplate($templateId);

        if ($result) {
            unlink('./uploads/templates/' . $template['name'] . '/');
        }

        $result = json_encode(['result' => $result], true);
        die($result);
    } elseif ($_REQUEST['action'] == "review_template_update") {
        $currentTemplate = $db->getReviewTemplate($_REQUEST['id']);
        $templatesDir = './uploads/templates/' . $currentTemplate['name'] . '/';

        if ($_FILES['resources']) {
            $total = count($_FILES['resources']['name']);
            for ($i = 0; $i < $total; $i++) {
                $tmpFilePath = $_FILES['resources']['tmp_name'][$i];
                if ($tmpFilePath != "") {
                    $newFilePath = $templatesDir . $_FILES['resources']['name'][$i];
                    move_uploaded_file($tmpFilePath, $newFilePath);
                }
            }
        }

        $template = '';
        if (isset($_REQUEST['template_code']) && $_REQUEST['template_code'] != "") {
            unlink($templatesDir  . $currentTemplate['template']);

            $rand = $_REQUEST['template_name'] . '_' . time();
            $target_file = $rand . substr(pathinfo($_FILES["template"]["name"], PATHINFO_FILENAME), 50);
            $target_file .= '.php';

            $template = $target_file;
            $response = '';
            $target_file = $templatesDir . $target_file;
            $templateFile = fopen($target_file, "w") or die("Unable to save template file!");
            fwrite($templateFile, $_REQUEST['template_code']);
        } else {
            $template = $currentTemplate['template'];
        }

        $preview_image = '';
        if (isset($_FILES["preview-image"]) && $_FILES["preview-image"]['size'] > 0) {
            unlink($templatesDir  . $currentTemplate['preview_image']);

            $rand = $_REQUEST['template_name'] . '_' . time();
            $target_file = $rand . substr(pathinfo($_FILES["preview-image"]["name"], PATHINFO_FILENAME), 50);
            $target_file .= "." . end((explode(".", $_FILES["preview-image"]["name"])));

            $preview_image = $target_file;
            $target_file = $templatesDir . $target_file;
            if (move_uploaded_file($_FILES["preview-image"]["tmp_name"], $target_file)) {
                $response .= "The file " . htmlspecialchars(basename($_FILES["preview-image"]["name"])) . " has been uploaded.";
            } else {
                $response .= "Sorry, there was an error uploading your file.";
            }
        } else {
            $preview_image = $currentTemplate['preview_image'];
        }

        /*$result = $db->updateReviewTemplate($_REQUEST['template_name'], $template, $preview_image, (int) $_REQUEST['id']);

        $result = json_encode([
            'response' => $result,
            'preview_image' => $preview_image,
            'template' => $template
        ], true);*/
        $foreuser=$_REQUEST['template_forfreeuser']=="on"?true:false;
    
           $result = $db->updateReviewTemplate($_REQUEST['template_name'], $template, $preview_image, $_REQUEST['template_islive'],$foreuser, (int) $_REQUEST['id']);
          
         
        $result = json_encode([
            'response' => $result,
            'preview_image' => $preview_image,
            'template' => $template
            ,
            'islive' => $_REQUEST['template_islive'],
            'forfreeuser' => $foreuser
        ], true);

        header("Location: /review_template_settings.php");

        die($result);
    /* Powered by www.Andrezzz.pt */
    } elseif ($_REQUEST['action'] == "review_template_save") {
        $contents = $_POST['contents'];
        $template = $_POST['template'];

        $templateFile = fopen('./uploads/templates/' . $template['name'] . '/' . $template['template'], "w") or json_encode(['result' => 'Unable to save template file.'], true);
        $result = fwrite($templateFile, $contents) ? true : 'Unable to save template file.';
        
        $result = json_encode(['result' => $result], true);
        die($result);
    }
}


function removePageSubscription($fb_page, $page_token)
{
    $url = "https://graph.facebook.com/$fb_page/subscribed_apps?access_token=$page_token";
    $un_subscribed_app = Tools::post_fb($url, "delete");
    return $un_subscribed_app;
}

function addPageSubscription($fb_page, $page_token)
{
    $url = "https://graph.facebook.com/$fb_page/subscribed_apps?access_token=$page_token";
    $subscribed_apps = Tools::post_fb($url, "post");
    return $subscribed_apps;
}

function removeAppSubscription($app_id, $app_secret)
{
    $app_access_token = $app_id . "|" . $app_secret;
    $url = "https://graph.facebook.com/$app_id/subscriptions?access_token=$app_access_token";
    $data = array();
    $data['object'] = "page";
    $un_subscribed_app = Tools::post_fb2($url, "post", $data);
    $posts = json_decode($un_subscribed_app, true);
    return $posts;
}

function checkUserSession()
{
    global $db;
    $current_user = $db->getUser(@$_SESSION['user_id']);
    if (@count($current_user) == 0 || (int)$current_user['status'] == 0) {
        unset($_SESSION['login_user']);
        $_SESSION['msg'] = 'Session was suspended. Please login.';
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: /");
        exit();
    }
    return true;
}
