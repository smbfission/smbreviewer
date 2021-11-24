<?php


/**
 * Tools class
 */
class Tools
{



  public static function purgeCacheUrl($url)
  {
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, siteURL());
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
      curl_setopt($ch, CURLOPT_TIMEOUT, 100);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $headr[] = 'X-LiteSpeed-Purge: '.$url;
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
      curl_setopt($ch, CURLOPT_HTTPGET, true);
      return curl_exec($ch);
  }



  public static function uploadImage($src_file, $target_file, $dst_width, $dst_height, $size_limit = 2*1024*1024, $error = 0)
  {
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

      $src_size= filesize($src_file);
      if ($src_size >0) {
          if ($i = getimagesize($src_file)) {
              $src_width = $i[0];
              $src_height = $i[1];

              if ($src_size < $size_limit) {
                  if ($src_image = @imagecreatefromstring(file_get_contents($src_file))) {
                      $width = $dst_width;
                      $height = $dst_height;
                      //

                      if ($src_width > $src_height) {
                          $dst_width = $width;
                          $dst_height = ($dst_width * $src_height) / $src_width ;

                          if ($dst_height > $height) {
                              $dst_height = $height;
                              $dst_width = ($dst_height * $src_width) / $src_height;
                          }
                      } else {
                          $dst_height = $height;
                          $dst_width = ($dst_height * $src_width) / $src_height;

                          if ($dst_width > $width) {
                              $dst_width = $width;
                              $dst_height = ($dst_width * $src_height) / $src_width;
                          }
                      }

                      $shift_x = abs($width - $dst_width) / 2;
                      $shift_y = abs($height - $dst_height) / 2;



                      $dst_image = imagecreatetruecolor($width, $height);
                      imagealphablending($dst_image, false);
                      imagesavealpha($dst_image, true);
                      $transparent = imagecolorallocatealpha($dst_image, 255, 255, 255, 127);
                      imagefilledrectangle($dst_image, 0, 0, $width, $height, $transparent);
                      imagecopyresampled($dst_image, $src_image, $shift_x, $shift_y, 0, 0, $dst_width, $dst_height, $src_width, $src_height);


                      imagepng($dst_image, $target_file);
                      //save memory
                      imagedestroy($dst_image);
                      return ['success'=>true];
                  } else {
                      return ['success'=>false,
                            'message'=> 'Image is not updated, only png and jpeg image files can be uploaded. '
                            ];
                  }
              } else {
                  return ['success'=>false,
                        'message'=> 'Image size is too big maximum 2MB. '
                       ];
              }
          } else {
              return ['success'=>false,
                    'message'=> 'Image is not updated, only png and jpeg image files can be uploaded. '
                   ];
          }
      } elseif ((int)$error > 0
             && (int)$error != 4
            ) {
          return ['success'=>false,
                'message'=> 'Image is not updated, only png and jpeg image files can be uploaded, maximum size 2MB. '
              ];
      }

      return ['success'=>false,
            'message'=> 'Image is not updated, only png and jpeg image files can be uploaded, maximum size 2MB. '
          ];
  }


  public static function makeReviewsArray($data, $type, $limit=1)
  {

    $reviews_array=[];

      switch ($type) {
        case 'facebook':
          if (isset($data['data'])) {
            foreach (@reset($data) as $value) {
              if ($limit>0 ) {
                $reviews_array[] = [
                                   "action_url" => 'https://facebook.com/'.$value['open_graph_story']['id'],
                                   "picture_url" => @$value['reviewer']['picture']['data']['url'],
                                   "author_name" => @$value['reviewer']['name'],
                                   "rating" => isset($value['rating']) ? $value['rating'] : 0,
                                   "date" => date("Y-m-d", strtotime($value['created_time'])),
                                   "review" => isset($value['review_text']) ? $value['review_text'] : "",
                                   "_type" => '<i class="icon-facebook"></i>',
                                   "src" => 'facebook',
                                 ];
              }
              $limit--;
            }
          }
          break;
        case 'google':

          if (isset($data['result'])) {

            foreach (@$data['result']['reviews'] as $value) {
              if ($limit>0 ) {
                $reviews_array[] = [
                "action_url" => $value['author_url'],
                "picture_url" => $value['profile_photo_url'],
                "author_name" => $value['author_name'],
                "rating" => $value['rating'],
                "date" => date("Y-m-d", $value['time']),
                "review" => $value['text'],
                "_type" => '<i class="icon-google"></i>',
                "src" => 'google',
              ];
             }
             $limit--;
            }
          }
          break;
        case 'yelp':

        if (isset($data['reviews'])) {

          foreach (@$data['reviews'] as $value) {
            if ($limit>0 ) {
              $reviews_array[] = [

                            "action_url" => $value['url'],
                            "picture_url" => $value['user']['image_url'],
                            "author_name" => $value['user']['name'],
                            "rating" => $value['rating'],
                            "date" => date("Y-m-d", strtotime($value['time_created'])),
                            "review" => $value['text'],
                            "_type" => '<i class="icon-yelp"></i>',
                            "src" => 'yelp',
                          ];
           }
           $limit--;
          }
        }

        break;
        case 'custom':

        foreach (@$data['data'] as $value) {
          if ($limit>0 ) {
            $reviews_array[] = [
                          "action_url" => "#",
                          "picture_url" => $value['photo'],
                          "author_name" => $value['name'],
                          "rating" => $value['rating'],
                          "date" => date("Y-m-d", strtotime($value['date'])),
                          "review" => $value['review'],
                          "_type" => '<i class="icon-custom"></i>',
                          "src" => 'custom',
                        ];
          }
          $limit--;
        }
        break;

    }

    return $reviews_array;
  }



  public static function siteURL()
  {
      $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ||
      $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
      $domainName = $_SERVER['HTTP_HOST'];
      return $protocol.$domainName;
  }


  public static function getServerURL()
  {
      $serverName = $_SERVER['SERVER_NAME'];
      $filePath = $_SERVER['REQUEST_URI'];
      $withInstall = substr($filePath, 0, strrpos($filePath, '/')+1);
      $serverPath = $serverName.$withInstall;
      $applicationPath = $serverPath;

      if (strpos($applicationPath, 'http://www.')===false) {
          if (strpos($applicationPath, 'www.')===false) {
              $applicationPath = 'www.'.$applicationPath;
          }
          if (strpos($applicationPath, 'http://')===false) {
              $applicationPath = 'http://'.$applicationPath;
          }
      }
      $applicationPath = str_replace("www.", "", $applicationPath);
      return $applicationPath;
  }


  public static function post($url, $method="get", $headers = array('Content-Type: application/x-www-form-urlencoded'), $body="" )
  {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
      curl_setopt($ch, CURLOPT_TIMEOUT, 100);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      // curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      if ($method == "post" ) {
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
          curl_setopt($ch, CURLOPT_POSTFIELDS, $body);



      }
     elseif ($method=="put") {
       curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
       curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
     }
       elseif ($method=="delete") {
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      } else {
          curl_setopt($ch, CURLOPT_HTTPGET, true);

      }

      $result  = curl_exec($ch);

      curl_close($ch);
      return $result;
  }


  public static function post_fb($url, $method, $body="", $apiKey="")
  {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
      curl_setopt($ch, CURLOPT_TIMEOUT, 100);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

      if ($apiKey!="") {
          $headr[] = 'Authorization: Bearer '.$apiKey;
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
      }

      if ($method == "post") {
          curl_setopt($ch, CURLOPT_POST, true);
      //curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
      } elseif ($method=="delete") {
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      } else {
          curl_setopt($ch, CURLOPT_HTTPGET, true);
      }
      return curl_exec($ch);
  }

  public static function post_fb2($url, $method, $body="")
  {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
      curl_setopt($ch, CURLOPT_TIMEOUT, 100);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      if ($method == "post") {
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      } else {
          curl_setopt($ch, CURLOPT_HTTPGET, true);
      }
      return curl_exec($ch);
  }

  public static function parse_size($size)
  {
      $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
  }

  public static function encrypt_link($link)
  {
    require_once(dirname(__FILE__).'/config.php');
    if (!isset($link_password)) {
      global $link_password;
    }



    $link= openssl_encrypt ($link, "aes-128-cbc",$link_password,true,'iviviviviviviviv');
    $link=bin2hex($link);
    //        $id_=hex2bin($id_);
    //      $id_= openssl_decrypt($id_, "aes-128-cbc",$link_password,true);
    return $link;
  }

  public static function decrypt_link($link)
  {
    require_once(dirname(__FILE__).'/config.php');
    if (!isset($link_password)) {
      global $link_password;
    }
    $link=hex2bin($link);
    $link= openssl_decrypt($link, "aes-128-cbc",$link_password,true,'iviviviviviviviv');
    return $link;
  }


  public static function capture_reviews_check_access_google_sheets($google_access_token,$google_refresh_token, $id,$user_id)
 {

   global $db;

              $url ="https://www.googleapis.com/userinfo/v2/me";

                 $res = self::post($url,"get",['Authorization: Bearer '.$google_access_token]);


               if (!isset(json_decode($res,true)['id'])) {

                $google_access_token = '';

                $url ="https://oauth2.googleapis.com/token";
                $gs=$db->getGeneralSettings();

                $client_id=$gs['default_google_client_id'];
                $client_secret= $gs['default_google_client_secret'];

                $data = [
                         "client_id"=>$client_id,
                         "client_secret"=>$client_secret,
                         "grant_type"=>"refresh_token",
                         "refresh_token" => $google_refresh_token,
                       ];
                       $data = json_encode($data,true);
                       $headers = [
                         'Authorization: Bearer '.$google_access_token,
                         'Accept: application/json',
                         'Content-Type: application/json'
                       ];
               $res_refresh = self::post($url,"post",$headers,$data);


               if (isset(json_decode($res_refresh,true)['access_token'])) {
                    $google_access_token = json_decode($res_refresh,true)['access_token'];

                    $url ="https://www.googleapis.com/userinfo/v2/me";

                   $res = self::post($url,"get",['Authorization: Bearer '.$google_access_token]);

                  }
                  else {
               //     $google_refresh_token = '';

                  }

                   $data= [
                    'google_access_token'=>$google_access_token,
                    'google_refresh_token'=>$google_refresh_token,
                    'id'=>$id,
                    'user_id'=>$user_id
                  ];

                   $db->updateCaptureReviewsGoogleTokens($data);




              }

               return $res;

  }


  public static function capture_reviews_get_google_sheets($google_spread_sheet_id,$id,$user_id)

  {
    global $db ;



    $url ="https://sheets.googleapis.com/v4/spreadsheets/".$google_spread_sheet_id;

    $row = $db->getCaptureReviewsByIdUserId($id,$user_id);
    self::capture_reviews_check_access_google_sheets($row['google_access_token'],$row['google_refresh_token'], $id,$user_id);
    $row = $db->getCaptureReviewsByIdUserId($id,$user_id);


     $result = self::post($url,"get",['Authorization: Bearer '.$row['google_access_token']]);

    return $result ;

  }

  public static function capture_reviews_add_google_sheets_row($google_access_token,$google_refresh_token, $id, $user_id, $review)
  {


    global $db;

    self::capture_reviews_check_access_google_sheets($google_access_token,$google_refresh_token, $id,$user_id);
    $row = $db->getCaptureReviewsByIdUserId($id,$user_id);

     $url ="https://sheets.googleapis.com/v4/spreadsheets/".$row['google_spread_sheet_id']."/values/".$row['google_sheet_id'].'!A1:A1:append?valueInputOption=RAW&insertDataOption=INSERT_ROWS';
     // error_log('url'.print_r($url,true));

     // $headers = [
     //   'Authorization: Bearer '.$row['google_access_token'],
     //   'Accept: application/json',
     //   'Content-Type: application/json'
     // ];

     $headers = [
       'Authorization: Bearer '.$row['google_access_token'],
       'Content-Type: application/gzip',
       'Content-Encoding: gzip'
     ];


     //
     // $data =['values'=>[[
     //   $_REQUEST['data']['date'],
     //   $_REQUEST['data']['capture_reviews_title'],
     //   $_REQUEST['data']['name'],
     //   $_REQUEST['data']['rating'],
     //   $_REQUEST['data']['review'],
     //   $_REQUEST['data']['feedback_text'],
     //
     //   ]]];

      $data =[
        "range"=>$row['google_sheet_id'].'!A1:A1',
        "majorDimension"=>"ROWS",

        "values"=>[$review]
      ];



     $data = json_encode($data,true);

     $data =gzencode($data, 9);

      $result = Tools::post($url,"post",$headers,$data);

      // error_log('result'.print_r($result,true));

   return $result;
  }


  public static function capture_reviews_update_google_sheets_cell($google_access_token,$google_refresh_token, $id, $user_id, $range,$text)
  {
    global $db;

    self::capture_reviews_check_access_google_sheets($google_access_token,$google_refresh_token, $id,$user_id);
    $row = $db->getCaptureReviewsByIdUserId($id,$user_id);

    //'https://sheets.googleapis.com/v4/spreadsheets/1SwO0RJu23bGiSVGJwkpJFanQq69pqlALHw5bl2-YU_U/values/reviews_list!f5?valueInputOption=RAW&key=[YOUR_API_KEY]' \


     $url ="https://sheets.googleapis.com/v4/spreadsheets/".$row['google_spread_sheet_id']."/values/".$row['google_sheet_id'].'!'.$range.'?valueInputOption=RAW';

     $headers = [
       'Authorization: Bearer '.$row['google_access_token'],
       'Content-Type: application/gzip',
       'Content-Encoding: gzip'
     ];


      $data =['values'=>[[$text]]];

     $data = json_encode($data,true);
     $data =gzencode($data, 9);

      $result = Tools::post($url,"put",$headers,$data);


   return $result;
  }

  public static function isJson($string) {
   json_decode($string);
   return (json_last_error() == JSON_ERROR_NONE);
  }


  public static function capture_reviews_get_short_io_domains($short_io_api_key)
  {
    $url="https://api.short.io/api/domains";
    $result = self::post($url,"get",['Accept : application/json','Authorization:'. $short_io_api_key]);
    return $result ;
  }


  public static function capture_reviews_get_short_io_by_origin_url($short_io_api_key,$searched=[])
  {
    $url = "https://api.short.io/links/by-original-url?".http_build_query($searched);
    $result = self::post($url,"get",['Accept : application/json','Authorization:'. $short_io_api_key]);
    return $result ;
  }

  public static function capture_reviews_add_short_io($short_io_api_key,$searched=[])
  {
    $url = "https://api.short.io/links";
    $searched = json_encode($searched,true);
    $result = self::post($url,"post",['Accept: application/json','Content-Type: application/json','Authorization:'. $short_io_api_key],$searched);
    return $result ;
  }

  public static function capture_reviews_update_short_io($short_io_api_key,$updateId,$searched=[]) {
    $url = "https://api.short.io/links/".$updateId;
    $searched = json_encode($searched,true);
    $searched['allowDuplicates'] = false;
    $result = self::post($url,"post",['Accept: application/json','Content-Type: application/json','Authorization:'. $short_io_api_key],$searched);
    return $result ;
  }

  public static function console_log($data, $do_script=true)
  {
    $data = json_encode($data,true);
    $data ="console.log(".$data.");";
    if ($do_script) {
      $data ="<script>".$data."</script>";
    }


    return $data;

  }


}
 ?>
