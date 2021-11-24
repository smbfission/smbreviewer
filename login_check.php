<?php
@session_start();
require_once("core/database.php");


if (file_exists(dirname(__FILE__).'/astra/libraries/API_connect.php')) {
    require_once(dirname(__FILE__).'/astra/Astra.php');
    require_once(dirname(__FILE__).'/astra/libraries/API_connect.php');


    $client_api = new Api_connect();
}


if (isset($_POST['submit'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $_SESSION['msg'] = "Please fill-in the details below";
        header("Location: /");
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];
        // $sql = "SELECT id,type,email,name, password,status,advanced_settings FROM user WHERE email = '$email' AND password = '$password'";
        $sql = "SELECT id,type,email,name,status,advanced_settings FROM user WHERE email = '$email' AND pwd = md5('$password')";
        $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
        $counts     = mysqli_num_rows($result);
        if ($counts > 0) {
            $rows     = mysqli_fetch_assoc($result);
            if ($rows['status']==0) {
                $_SESSION['msg'] = 'The session has been suspended. Please login.';
                @mysqli_close($conn); // Closing Connection
                header('Location: /'); // Redirecting To Home Page
                exit;
            } elseif ($rows['status']==2) {
                $_SESSION['msg'] = 'Your email address is not validated, please check your email and follow the instructions.';
                @mysqli_close($conn); // Closing Connection
                  header('Location: /'); // Redirecting To Home Page
                  exit;
            } else {
                $_SESSION['user_id'] = $rows['id'];
                $_SESSION['type'] = $rows['type'];
                $_SESSION['login_user'] = $email;
                $_SESSION['user_name'] = $rows['name'];
                $_SESSION['user_adv_settings'] = $rows['advanced_settings'];
                $_SESSION['tzo'] = @$_POST['tzo'];
                unset($_SESSION['msg']);
                //die(print_r($_SESSION,true));
                header("Location: /dashboard/");
                exit;
            }
        } else {
            if (isset($client_api)) {
                $user = [
                  'user_email' => $_POST['email'],
                ];
                $ret = $client_api->send_request('has_loggedin', ['username' => $user, 'success' => 0,], 'magento');
                // error_log(print_r($ret,true));
            }
            $_SESSION['msg'] = 'Wrong username or password.';
            @mysqli_close($conn); // Closing Connection
            header('Location: /'); // Redirecting To Home Page
            exit;
        }
    }
} else {
    header('Location: /'); // Redirecting To Home Page
    exit;
}
