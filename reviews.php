<?php

@header('Access-Control-Allow-Origin: *');
include_once("core/database.php");
require_once('core/tools.php');

$id=@base64_decode(rtrim($_REQUEST['id'],'/'));

if (!isset($id) || base64_encode($id) != rtrim($_REQUEST['id'],'/')) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    die();
}


$db = new Database();

$row=$db->getCampaignByCampaignId($id);

if (@count($row)==0) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    die();
}



$settings = $db->getUser($row['user_id']);
$default_plan_id = $db->getGeneralSettings('default_plan_id')['default_plan_id'];
$current_plan =  $db->getLastUserPlan($row['user_id']);


$db->updateUserVisitsByUserId($row['user_id']);

if (trim(@$settings['app_id'])=="" || trim(@$settings['app_secret'])=="") {
    $access_token = "";
} else {
    $access_token = @$settings['access_token'];
}


$currentPageUrl = getServerURL().ltrim($_SERVER['REQUEST_URI'], '/');
$og_image = getServerURL()."uploads/".$row['meta_picture'];



  $refresh_time=60*24;  //minutes
  $gr=$db->getLastReviewsUpdateDatesByCampaingId($id);
  $start_date = new DateTime();
  $minimum_rate = (int)$row['minimum_rate'];
  $reviews_array =[];

  if (@$row['is_facebook']==1) {
      $limit = (int)$row['fb_reviews_cnt'];
      $recommendation_type = $row['recommendation_type'];

      //$url="https://graph.facebook.com/$row[fb_page]/ratings?access_token=$row[page_token]&limit=".'100'."&fields=created_time,has_rating,has_review,open_graph_story,rating,review_text,reviewer{name,id,picture},recommendation_type";

       //https://graph.facebook.com/271360416769510?access_token=EAAG68OhF3K0BANOcWXPa6K8SVaW9bCc3nf0ZChQ5HtoM2KmglEZAYtk0W0QS5E5CtAogycFcpIaweaMy5X4R6V4NtkqOHns9Wm3EUOeuETRPXAM6ZBjiAGRT4AJKdo9fO12jKCcGGBjFAhQ1N6OZA0iSckHwOTQ1z1930FxT9mpgNIgPyX8j&limit=100&fields=ratings{created_time,has_rating,has_review,open_graph_story,rating,review_text,reviewer{name,id,picture},recommendation_type},rating_count
      $url="https://graph.facebook.com/$row[fb_page]?access_token=$row[page_token]".""."&limit=".'100'."&fields=ratings{created_time,has_rating,has_review,open_graph_story,rating,review_text,reviewer{name,id,picture},recommendation_type},rating_count";
      $facebook_json = null;
      $since_start = $start_date->diff(new DateTime(@$gr['fb_last_update']));

      $minutes_passed =($since_start->d * 24 * 60) + ($since_start->h * 60) + $since_start->i;
      if ($minutes_passed >= $refresh_time) { ///check  minutes

          $g_json=post_fb($url, "get");
          $g=json_decode($g_json);

          if (@property_exists(@reset($g), 'message')) {
              // error_log(print_r((reset($g)),true));
          } else {
        //    error_log('updating fb '.$url);
              $db->updateFacebookReviewCacheByCampaignId($id, $g_json);
          }
      }

      $gr=null;
      $gr=$db->getLastReviewsByCampaingId($id, 'fb_reviews');


      if (count($gr) > 0) {
          $g_json=$gr['fb_reviews'];
          $g=json_decode($g_json,true);
      }



 $facebook_json=null;
    if ($g = $g['ratings']['data']) {
          foreach ($g as $v) {

          //    error_log(print_r($v,true));
              if ($limit>0) {
                  if ($recommendation_type == 'any'
              || $recommendation_type == $v['recommendation_type']
              ) {
                 if (isset($v['has_rating']) && (int)$v['has_rating'] ==1 && $v['rating'] >= $minimum_rate) {
                          $facebook_json[] = $v;
                          $limit--;
                      //    error_log(print_r('is rate',true));
                      } elseif (!isset($v['has_rating']) || (int)$v['has_rating'] ==0) {
                          $facebook_json[] = $v;
                          $limit--;
                      //    error_log(print_r('no is rate',true));
                      }
                  }
              }
          }



      }
      //
$facebook_array = $facebook_json;
// if ($id==202) {
//
//    header('Content-Type: application/json');
// //   die($facebook_json);
// die(json_encode($facebook_array,true));
// }

if (!empty($facebook_array)) {



      foreach ($facebook_array as $value) {


          $reviews_array[] = [

       "action_url" => 'https://facebook.com/'.$value['open_graph_story']['id'],
       "picture_url" => @$value['reviewer']['picture']['data']['url'],
       "author_name" => @$value['reviewer']['name'],
       "rating" => isset($value['rating']) ? $value['rating'] : 0,
       "date" => date("Y-m-d", strtotime($value['created_time'])),
       "review" => isset($value['review_text']) ? $value['review_text'] : "",
       "_type" => '<i class="icon-facebook"></i>',
       "src" => 'facebook',
       "_verified"=>false,
      ];
      }
    }

      $facebook_json = json_encode(['data'=>$facebook_json]);
      $facebook_data = json_decode($facebook_json, true);



  }

  if (@$row['is_google']==1) {
      $url="https://maps.googleapis.com/maps/api/place/details/json?placeid=$row[place_id]&fields=name,rating,formatted_phone_number,reviews&key=".@$settings['google_key'];



      $limit = (int)$row['google_reviews_cnt'];

      $google_json = null;
      $since_start = $start_date->diff(new DateTime(@$gr['google_last_update']));


      $minutes_passed =($since_start->d * 24 * 60) + ($since_start->h * 60) + $since_start->i;
      if ($minutes_passed >= $refresh_time) { ///check  minutes

          $g_json=post_fb($url, "get");
          $g=json_decode($g_json);
          $google_api_call_errors = '';
          if (property_exists($g, 'error_message')) {
              $google_api_call_errors = '<i class="fa fa-warning"></i> '.$g->error_message;
          } else {
              $db->updateGoogleReviewCacheByCampaignId($id, $g_json);
          }
      }


      $gr=null;
      $gr=$db->getLastReviewsByCampaingId($id, 'google_reviews');
      if (count($gr) > 0) {
          $g_json=$gr['google_reviews'];
          $g=json_decode($g_json);
      }

      if (count(get_object_vars($g))>0) {
          foreach ($g as $k => $v) {
              $google_json[$k] = $v;
              $reviews = [];
              if (is_object($v) && property_exists($v, 'reviews')) {
                  foreach ($v->reviews as $r) {
                      if ($limit>0) {
                          if ($r->rating >= $minimum_rate) {
                              $reviews[] = $r;

                              $reviews_array[] = [

                              "action_url" => $r->author_url,
                              "picture_url" => $r->profile_photo_url,
                              "author_name" => $r->author_name,
                              "rating" => $r->rating,
                              "date" => date("Y-m-d", $r->time),
                              "review" => $r->text,
                              "_type" => '<i class="icon-google"></i>',
                              "src" => 'google',
                              "_verified"=>false,
                            ];
                              $limit--;
                          }

                          // error_log(print_r($r,true));
                      }
                  }

                  $v->reviews = $reviews;
              }
          }
      }

      $google_json = json_encode($google_json);
      $google_data = json_decode($google_json, true);
  }

  if (@$row['is_yelp']==1) {
      $limit = (int)$row['yelp_reviews_cnt'];

      $yelp_json = [];
      $gr=$db->getLastReviewsUpdateDatesByCampaingId($id);

      $since_start = $start_date->diff(new DateTime(@$gr['yelp_last_update']));

      $minutes_passed =($since_start->d * 24 * 60) + ($since_start->h * 60) + $since_start->i;

      // error_log('here'.$minutes_passed. '>='. $refresh_time.'  ==='.$gr['yelp_last_update'].' now is '.$start_date->format('Y-m-d H:i:s'));
      if ($minutes_passed >= $refresh_time) { ///check  minutes

                if ($limit<=3) {

                   $url="https://api.yelp.com/v3/businesses/$row[yelp_business_id]/reviews";
                   $y_json=post_fb($url, "get", "", @$settings['yelp_api_key']);
                   $y= json_decode($y_json,true);

                } else {
                  require_once(dirname(__FILE__).'/core/scrapper.php');

                   $scrapper= new Scrapper();
                    //$foo= $scrapper->dlPage('https://api.ipify.org/?format=json',[],true);
                  // error_log('==== proxy '.$foo);

                   $scrapper->checkProxyDb();
                   $y = $scrapper->getYelpReviewsArray($row['yelp_business_id'],$limit);


    // error_log(print_r($scrapper->response_code,true));
                }

                if (isset($y['reviews'])) {


                $db->updateYelpReviewCacheByCampaignId($id, base64_encode(gzencode(json_encode($y,true),9)));

              }
      }


      $gr=null;

      $gr=$db->getLastReviewsByCampaingId($id, 'yelp_reviews');



          if (trim($gr['yelp_reviews'])!='') {
              if (!Tools::isJson($gr['yelp_reviews'])) {
            //    error_log(mb_strlen($gr['yelp_reviews']));
                $gr['yelp_reviews'] = gzdecode(base64_decode($gr['yelp_reviews']));
              }
            //  error_log(mb_strlen($gr['yelp_reviews']));
              $y=json_decode($gr['yelp_reviews'],true);


          }


          $y_res=[];
          if (isset($y['reviews'])) {
              foreach ($y['reviews'] as $k => $v) {
                  if ($limit>count($y_res)) {
                      if ($v['rating'] >= $minimum_rate) {
                          $y_res[] = $v;
                      }
                  }
              }
          }


          foreach ($y_res as $value) {

          $reviews_array[] = [

                        "action_url" => $value['url'],
                        "picture_url" => $value['user']['image_url'],
                        "author_name" => $value['user']['name'],
                        "rating" => $value['rating'],
                        "date" => date("Y-m-d", strtotime($value['time_created'])),
                        "review" => $value['text'],
                        "_type" => '<i class="icon-yelp"></i>',
                        "src" => 'yelp',
                        "_verified"=>false,
                      ];
      }
      $yelp_json = json_encode(['reviews'=>$yelp_json]);
      $yelp_data = json_decode($yelp_json, true);


  }

  if (@$row['is_custom']==1) {
      $url=getServerURL()."/review_api.php?uid=$row[user_id]"."&campaigns_id=".(int)$row['id'];
      $custom_json=post_fb($url, "get");
      $custom_data = json_decode($custom_json, true);


      foreach (json_decode($custom_json, true)['data'] as $value) {

        $custom_icon = ($value['icon']!="" && file_exists(dirname(__FILE__).'/uploads/custom_reviews/'.basename($value['icon']))) ? '<img class="icon-custom" src="'.
                                                (filter_var($value['icon'], FILTER_VALIDATE_URL) ? $value['icon'] : getServerURL()."uploads/custom_reviews/".$value['icon'] ).
                                                '">' : '<i class="icon-custom"></i>';



        if (trim($value['facebook_id'])!='') {
         $custom_icon='<i class="icon-facebook"></i>';
        }



if (!file_exists(dirname(__FILE__).'/uploads/'.basename($value['photo']))) {
  $value['photo'] =  getServerURL()."uploads/default_files/default_custom_review_user_picture.png";
}
      if ($value['rating'] >= $minimum_rate) {

          $reviews_array[] = [
                        "action_url" => "#",
                        "picture_url" => $value['photo'],
                        "author_name" => $value['name'],
                        "rating" => $value['rating'],
                        "date" => date("Y-m-d", strtotime($value['date'])),
                        "review" => $value['review'],
                        "_type" => $custom_icon,
                        "src" => 'custom',
                        "_verified"=>((trim($value['facebook_id'])!="") ? true : false),
                      ];
      }
    }
  }


  $sort_array = ['0'=>'date',
                 '1'=>'rating',
                 '2'=>'random',
                 '3'=>'text_length',
                ];


$reviews_array = getReviewsSorted($reviews_array,$sort_array[(int)$row['order_way']]);



?>

<?php if (!isset($_REQUEST['src'])): ?>
<!DOCTYPE html>
<html>
  <head>
        <!--Meta Information-->
        <?php if (trim($row['meta_title'])!=''): ?>
          <title><?= $row['meta_title']; ?></title>
          <?php else: ?>
          <title>Reviews</title>
        <?php endif; ?>

        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="<?php echo $row['meta_description']; ?>" />
        <meta property="og:url" content="<?php echo $currentPageUrl; ?>" />
        <meta property="og:title" content="<?php echo $row['meta_title']; ?>" />
        <meta property="og:description" content="<?php echo $row['meta_description']; ?>" />
        <meta property="og:image" content="<?php echo $og_image; ?>" />
        <meta property="og:image:url" content="<?php echo $og_image; ?>" />
        <meta property="og:image:secure_url" content="<?php echo $og_image; ?>" />
        <meta property="og:type" content="website" />

        <meta name="twitter:title" content="<?php echo $row['meta_title']; ?>" />
        <meta name="twitter:card" content="photo" />
        <meta name="twitter:image" content="<?php echo $og_image; ?>" />
        <link rel="canonical" href="<?php echo $currentPageUrl; ?>" />
        <!--Meta Information-->

        <!-- App css -->
        <link rel="stylesheet" href="<?= getServerURL()?>assets/vendor/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="<?= getServerURL()?>assets/vendor/font-awesome/css/font-awesome.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">



  </head>


  <body >

<?php endif; ?>



  <!-- reviews styles begin -->
  <style>


  <?='#smb_reviews'.$id?> {

  padding-left: 15px;
  padding-right: 15px;
  overflow-x: auto;


  }

  <?='#smb_reviews'.$id?> .img-circle {
    border-radius: 50%;
  }
  <?='#smb_reviews'.$id?> a{

  text-decoration: none;
  }


  <?='#smb_reviews'.$id?> ul.media-list {

  list-style: none;
  display: flex;
  flex-wrap: wrap;
  padding: 0;

  }
  <?='#smb_reviews'.$id?> ul.media-list li {

  width: 100%;
  display: flex;
  padding: 0;
  }

  <?='#smb_reviews'.$id?> ul.media-list li>.pull-left {

  float: left;
  padding: 10px;
  align-self: flex-start;

  }
  <?='#smb_reviews'.$id?> ul.media-list li>div.media-body {

  float: left;
  padding: 10px;
  align-self: flex-start;
  overflow: hidden;
  width: 100%;
  }

  <?='#smb_reviews'.$id?> ul.media-list li>div.media-body>*
  {

    margin: 0px 0 3px 0;

  }
  <?='#smb_reviews'.$id?> ul.media-list li>div.media-body .user-rating {
  margin: 3px 0 0px 0;
  }

  <?='#smb_reviews'.$id?> ul.media-grid {

  list-style: none;
  display: flex;
  flex-wrap: wrap;
  padding: 0;
  justify-content: center;
  }



  <?='#smb_reviews'.$id?> ul.media-grid>li {
  display: flex;
   padding: 0;

  width: 100%;
  flex-wrap: wrap;
  align-content: flex-start;
  align-self: baseline;
  flex-direction: column;
  }


  <?='#smb_reviews'.$id?> ul.media-grid li>*
  {

    margin: 10px auto;


  }

  <?='#smb_reviews'.$id?> ul.media-grid li>div.media-body>*
  {

    margin: 0px 0 3px 0;

  }

  <?='#smb_reviews'.$id?> ul.media-grid li>div.media-body {
  overflow: hidden;
    width: calc(100% - 20px);
  padding-left: 10px;
  padding-right: 10px;
  text-align: center;
  box-sizing: unset;
  }


  @media (min-width: 576px) {

  <?='#smb_reviews'.$id?> ul.media-grid>li {
    margin: 10px;
    width: calc(100% / 2 - 20px);
  }


  }


  @media (min-width: 768px) {

  <?='#smb_reviews'.$id?> ul.media-grid>li {

     width: calc(100% / 3 - 30px);
  }

  }


  <?='#smb_reviews'.$id?> ul.media-list {

  list-style: none;
  display: flex;
  flex-wrap: wrap;
  padding: 0;

  }
  <?='#smb_reviews'.$id?> ul.media-list li {

  width: 100%;
  display: flex;
  padding: 0;
  }

  <?='#smb_reviews'.$id?> .smb_reviews_slide   {

  display: flex;

  padding: 10px;
  margin: 0 -15px 0 -10px;
    overflow: hidden;

  }



  <?='#smb_reviews'.$id?> .smb_reviews_slide  .nav-prev, <?='#smb_reviews'.$id?> .smb_reviews_slide  .nav-next   {
  display: flex;
  text-align: center;
  z-index: 9;
  cursor: pointer;
  width: 20px;
  box-sizing: unset;
  <?php if (trim($row['name_color'])!=''): ?>
    color:<?= $row['name_color'] ?>;
  <?php endif; ?>


  }

  <?='#smb_reviews'.$id?> .smb_reviews_slide  .nav-prev span, <?='#smb_reviews'.$id?> .smb_reviews_slide  .nav-next span
  {

  text-transform: none;
  text-decoration: none;
  font-size: 12px;
  transform: scale(1, 3.5);
  -webkit-transform: scale(1, 3.5); /* Safari and Chrome */
  -moz-transform: scale(1, 3.5); /* Firefox */
  -ms-transform: scale(1, 3.5); /* IE 9+ */
  -o-transform: scale(1, 3.5); /* Opera */

  }

  <?='#smb_reviews'.$id?> .smb_reviews_slide  .nav-prev    {
    margin-right: -20px;
  }


  <?='#smb_reviews'.$id?> .smb_reviews_slide  .nav-next    {
    margin-left: -20px;
  }

  <?='#smb_reviews'.$id?> .smb_reviews_slide  .nav-prev span   {
  margin: auto;
  padding: 0px 0px 0px 10px;
  }
  <?='#smb_reviews'.$id?> .smb_reviews_slide  .nav-next span  {
  margin: auto;
  padding: 0px 10px 0px 0px;
  }

  <?='#smb_reviews'.$id?> .smb_reviews_slide  ul   {
  list-style: none;
  display: flex;
  width: 100%;
  padding: 0;
  margin: 0;
  }

  <?='#smb_reviews'.$id?> .smb_reviews_slide  ul li {
    width: 100%;
    display: none;
    padding: 20px;
    margin: 0;
  }

  <?='#smb_reviews'.$id?> .smb_reviews_slide  ul li.active {
  display: flex;
  justify-content: center;
      align-items: flex-start;
      text-align: center;
      flex-wrap: wrap;
      overflow: hidden;
  }
  <?='#smb_reviews'.$id?> .smb_reviews_slide  ul li.active a {
  display: flex;
    justify-content: center;
    align-items: center;

    flex-direction: column;
    width: 100%;
  }
  <?='#smb_reviews'.$id?> .smb_reviews_slide  ul li .media-body {
  overflow: hidden;
  width: 100%;
  padding-left: 10px;
  padding-right: 10px;
  text-align: center;
  }


  <?='#smb_reviews'.$id?> .r_review {
  overflow: hidden;

  }

  <?='#smb_reviews'.$id?> .thumbnail{
      padding: 10px 10px 0px 10px;
      padding: 4px;
      margin-bottom: 20px;
      border-radius: 4px;
      -webkit-transition: border .2s ease-in-out;
      -o-transition: border .2s ease-in-out;
      transition: border .2s ease-in-out;
  }

  <?='#smb_reviews'.$id?> .media-list .thumbnail {

    min-height: 100px;
  }

  <?='#smb_reviews'.$id?> img._picture_url_ {

    width: auto;
    height: 50px;
  }
  <?='#smb_reviews'.$id?> .media-slide .r_rating, <?='#smb_reviews'.$id?> .media-grid .r_rating {


    margin-left: auto;
    margin-right: auto;

  }

  <?='#smb_reviews'.$id?> .r_type {

    display: block;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
    padding-top: 10px;
    position: relative;
  }

  <?='#smb_reviews'.$id?> .icon-google::before {
  content: url("data:image/svg+xml,%3Csvg height='25' width='25' version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 512 512' style='enable-background:new 0 0 512 512;' xml:space='preserve'%3E%3Cpath style='fill:%23FBBB00;' d='M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256 c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456 C103.821,274.792,107.225,292.797,113.47,309.408z'/%3E%3Cpath style='fill:%23518EF8;' d='M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451 c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535 c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176L507.527,208.176z'/%3E%3Cpath style='fill:%2328B446;' d='M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512 c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771 c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z'/%3E%3Cpath style='fill:%23F14336;' d='M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012 c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0 C318.115,0,375.068,22.126,419.404,58.936z'/%3E%3C/svg%3E%0A");
  display: inline-block;
  width: 25px;
  height:25px;

  }

  <?='#smb_reviews'.$id?> .icon-facebook::before{
  content: url("data:image/svg+xml,%3Csvg height='25' width='25' version='1.1' id='Capa_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'%0AviewBox='0 0 112.196 112.196' style='enable-background:new 0 0 112.196 112.196;' xml:space='preserve'%3E%3Ccircle style='fill:%233B5998;' cx='56.098' cy='56.098' r='56.098'/%3E%3Cpath style='fill:%23FFFFFF;' d='M70.201,58.294h-10.01v36.672H45.025V58.294h-7.213V45.406h7.213v-8.34%0Ac0-5.964,2.833-15.303,15.301-15.303L71.56,21.81v12.51h-8.151c-1.337,0-3.217,0.668-3.217,3.513v7.585h11.334L70.201,58.294z'/%3E%3C/svg%3E");
  display: inline-block;
  width: 25px;
  height:25px;

  }

  <?='#smb_reviews'.$id?> .icon-yelp::before{
  content: url("data:image/svg+xml,%3Csvg enable-background='new 0 0 24 24' viewBox='0 0 24 24' height='25' width='25' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23f44336'%3E%3Cpath d='m12.062 17.662c.038-.934-1.266-1.395-1.829-.671-1.214 1.466-3.493 4.129-3.624 4.457-.347 1 1.28 1.638 2.312 2.024 1.121.42 1.919.591 2.392.51.342-.071.562-.248.67-.533.089-.245.08-5.568.079-5.787z'/%3E%3Cpath d='m11.522.642c-.08-.31-.295-.51-.647-.6-1.037-.272-4.966.838-5.698 1.624-.234.238-.318.515-.248.828l4.985 8c1.018 1.628 2.298 1.139 2.214-.681h-.001c-.066-1.199-.544-8.775-.605-9.171z'/%3E%3Cpath d='m9.413 15.237c.942-.29.872-1.671.07-1.995-2.139-.881-5.06-2.114-5.285-2.114-.876-.052-1.045 1.201-1.134 2.096-.08.81-.084 1.552-.014 2.229.066.714.221 1.443.933 1.485.309-.001 5.383-1.686 5.43-1.701z'/%3E%3Cpath d='m20.514 12.052c.403-.281.342-.7.347-.838-.108-1.024-1.83-3.61-2.692-4.029-.328-.152-.614-.143-.858.029-.323.219-3.24 4.444-3.413 4.619-.567.767.244 1.871 1.092 1.648l-.014.029c.341-.115 5.274-1.282 5.538-1.458z'/%3E%3Cpath d='m15.321 15.586c-.881-.315-1.712.81-1.2 1.581.145.247 2.809 4.705 3.043 4.871.225.191.507.219.83.095.905-.362 2.865-2.876 2.992-3.857.051-.348-.042-.619-.286-.814-.197-.176-5.379-1.876-5.379-1.876z'/%3E%3C/g%3E%3C/svg%3E");
  display: inline-block;
  width: 25px;
  height:25px;

  }

  <?='#smb_reviews'.$id?> img.icon-custom{
  display: inline-block;
  width: 25px;
  height:25px;
  }

  <?php if (trim($row['custom_icon'])!='' && file_exists(dirname(__FILE__).'/uploads/campaign/'.@$row['custom_icon'])): ?>


  <?='#smb_reviews'.$id?> i.icon-custom::before{
  content: url("<?= getServerURL().'uploads/campaign/'.$row['custom_icon'] ?>");
  display: inline-block;
  width: 25px;
  height:25px;

  }
  <?php endif; ?>

  <?='#smb_reviews'.$id?> .src-type-yelp .user-rating {
    display: block;
    padding: .4rem 0;
    font-size: 0;
  }

  <?='#smb_reviews'.$id?> .src-type-yelp .user-rating.rating_v1::before {
    content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_1.png');
    display:inline-block;
  }

  <?='#smb_reviews'.$id?> .src-type-yelp .user-rating.rating_v1_5::before {
    content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_1_half.png');
    display:inline-block;
  }
  <?='#smb_reviews'.$id?> .src-type-yelp .user-rating.rating_v2::before {
    content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_2.png');
    display:inline-block;
  }

  <?='#smb_reviews'.$id?> .src-type-yelp .user-rating.rating_v2_5::before {
    content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_2_half.png');
    display:inline-block;
  }

  <?='#smb_reviews'.$id?> .src-type-yelp .user-rating.rating_v3::before {
    content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_3.png');
    display:inline-block;
  }

  <?='#smb_reviews'.$id?> .src-type-yelp .user-rating.rating_v3_5::before {
    content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_3_half.png');
    display:inline-block;
  }


  <?='#smb_reviews'.$id?> .src-type-yelp .user-rating.rating_v4::before {
    content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_4.png');
    display:inline-block;
  }

  <?='#smb_reviews'.$id?> .src-type-yelp .user-rating.rating_v4_5::before {
    content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_4_half.png');
    display:inline-block;
  }
  <?='#smb_reviews'.$id?> .src-type-yelp .user-rating.rating_v5 {
    font-size: 0;
  }
  <?='#smb_reviews'.$id?> .src-type-yelp .user-rating.rating_v5::before {
    content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_5.png');
    display:inline-block;
  }

  <?='#smb_reviews'.$id?> .r_dp{
    <?= ($row['display_dp']=="0" ? "display: none;" : "display:block;")?>
  }
  <?='#smb_reviews'.$id?> .r_name{
    <?php if (trim($row['name_color'])!=''): ?>
      color:<?= $row['name_color'] ?>;
    <?php endif; ?>

  }
  <?='#smb_reviews'.$id?> .r_rating{

    <?php if (trim($row['rating_color'])!=''): ?>
      color:<?= $row['rating_color'] ?>;
    <?php endif; ?>




      <?= ($row['display_rating']=="0" ? "display: none;" : "")?>

  }
  <?='#smb_reviews'.$id?> .r_date{

      <?php if (trim($row['date_color'])!=''): ?>
        color:<?= $row['date_color'] ?>;
      <?php endif; ?>

      <?= ($row['display_date']=="0" ? "display: none;" : "")?>


  }
  <?='#smb_reviews'.$id?> .r_review{

      <?php if (trim($row['review_color'])!=''): ?>
        color:<?= $row['review_color'] ?>;
      <?php endif; ?>

      <?php if (trim($row['font_size'])!=''): ?>
        font-size:<?= $row['font_size'] ?>;
      <?php endif; ?>



      <?= (trim($row['font_family'])!="" ? "font-family:".$row['font_family'].";" : "")?>
      <?= ($row['display_review']=="0" ? "display: none;" : "")?>
      text-align: justify;

  }

  <?='#smb_reviews'.$id?>  .media-slide .r_review, <?='#smb_reviews'.$id?>  .media-grid .r_review{
    text-align: center;
  }
  <?='#smb_reviews'.$id?> .thumbnail{
    <?php if (trim($row['widget_bg_color'])!=''): ?>
      background:<?= $row['widget_bg_color'] ?>;
    <?php endif; ?>

      box-shadow:0 2px 5px <?php echo $row['widget_box_shadow']; ?>, 0 2px 10px rgba(0, 0, 0, 0);
  }
  <?='#smb_reviews'.$id?> .carousel-inner .item{

    <?php if (trim($row['widget_bg_color'])!=''): ?>
      background:<?= $row['widget_bg_color'] ?>;
    <?php endif; ?>

  }
  <?='#smb_reviews'.$id?> .pagination{
      display: flex;
      justify-content: center;
  }
  <?='#smb_reviews'.$id?> .pagination .page_prev, <?='#smb_reviews'.$id?> .pagination .page_next{
          border:1px solid <?= $row['widget_bg_color']; ?>;
          padding: .5rem;
          margin: .5rem;
          border-radius: 3px;
          cursor: pointer;
  }

  <?='#smb_reviews'.$id?> .pagination .page_number{
      border:1px solid <?= $row['widget_bg_color']; ?>;
      padding: .5rem;
      margin: .5rem;
      border-radius: 3px;
      cursor: pointer;
  }
  <?='#smb_reviews'.$id?> .pagination .page_number.active{
          border:1px solid <?= $row['widget_bg_color']; ?>;

          <?php if (trim($row['widget_bg_color'])!=''): ?>
            background:<?= $row['widget_bg_color'] ?>;
          <?php endif; ?>



          <?php if (trim($row['review_color'])!=''): ?>
            color:<?= $row['review_color'] ?>;
          <?php endif; ?>
          font-weight: 600;
          padding: .5rem;
          margin: .5rem;
          border-radius: 3px;
          cursor: default;
  }

.footer-label , .footer-label a {
text-decoration:none;color:#737778;
text-align: center;

}


<?='#smb_reviews'.$id?> .img-container { position: relative; }
<?='#smb_reviews'.$id?> .img-container img { display: block; }
<?='#smb_reviews'.$id?> .img-container .fa { position: absolute; top:0; right:-8px; color:green; }

<?='#smb_reviews'.$id?> .r_type .fa { position: absolute; top:4px; margin-left: -4px; color:green; }

  </style>






	<div id="<?='smb_reviews'.$id?>" class="smb-reviews-container" render_type="<?=$row['style']?>" rating_color="<?=$row['rating_color']?>" reviews_per_page="<?=$row['reviews_per_page']?>" review_text_size="<?=$row['review_text_size']?>"  style="display:none;">
           <?php

                echo displayReview('', @$row['style'], 0);


                $i=0;
                foreach ($reviews_array as $key => $value) {
                  $i++;
                  if ($value['_verified']==true) {
                    $value['_verified'] = '<i class="fa fa-check-circle"></i>';
                  } else {
                    $value['_verified'] = '';
                  }
                   echo displayReview($value['src'], @$row['style'], $i, $value['action_url'], $value['picture_url'], $value['author_name'], $value['rating'], $value['date'], $value['review'], $value['_type'],$value['_verified']) ;

                }
                echo displayReview('', @$row['style'], -1);


             // if (false and (@$row['is_google']==1 || @$row['is_facebook']==1 || @$row['is_yelp']==1 || @$row['is_custom']==1)) {
             //     $i=0;
             //     $i++;
             //
             //     if ((@$row['is_google']==1) && isset($google_data['result']['reviews'])) {
             //         foreach ($google_data['result']['reviews'] as $reviews) {
             //             $action_url = $reviews['author_url'];
             //             $picture_url = $reviews['profile_photo_url'];
             //             $author_name = $reviews['author_name'];
             //             $rating = $reviews['rating'];
             //             $date = $reviews['relative_time_description'];
             //             $date = date("Y-m-d",$reviews['time']);
             //             $review = $reviews['text'];
             //             $_type = '<i class="icon-google"></i>';
             //
             //             echo displayReview('google', @$row['style'], $i, $action_url, $picture_url, $author_name, $rating, $date, $review, $_type) ;
             //             $i++;
             //         }
             //     }
             //
             //     if (@$row['is_facebook']==1 && isset($facebook_data['data'])) {
             //         $_type = '<i class="icon-facebook"></i>';
             //
             //         foreach ($facebook_data['data'] as $reviews) {
             //             $action_url = 'https://facebook.com/'.$reviews['open_graph_story']['id'];
             //             $picture_url = @$reviews['reviewer']['picture']['data']['url'];
             //             $author_name = @$reviews['reviewer']['name'];
             //             $rating = isset($reviews['rating']) ? $reviews['rating'] : 0;
             //             $date = $reviews['created_time'];
             //             $date = date("Y-m-d",strtotime($reviews['created_time']));
             //             $review = isset($reviews['review_text']) ? $reviews['review_text'] : "";
             //             echo displayReview('facebook', @$row['style'], $i, $action_url, $picture_url, $author_name, $rating, $date, $review, $_type) ;
             //             $i++;
             //         }
             //     }
             //
             //     if (@$row['is_custom']==1 && isset($custom_data['data'])) {
             //         $_type = '<i class="icon-custom"></i>';
             //         foreach ($custom_data['data'] as $reviews) {
             //             $action_url = "#";
             //             $picture_url = $reviews['photo'];
             //             $author_name = $reviews['name'];
             //             $rating = $reviews['rating'];
             //             $date = $reviews['date'];
             //             $date = date("Y-m-d",strtotime($reviews['date']));
             //             $review = $reviews['review'];
             //             echo displayReview('custom', @$row['style'], $i, $action_url, $picture_url, $author_name, $rating, $date, $review, $_type) ;
             //             $i++;
             //         }
             //     }
             //
             //     if (@$row['is_yelp']==1 && isset($yelp_data['reviews'])) {
             //         $_type = '<i class="icon-yelp"></i>';
             //         foreach ($yelp_data['reviews'] as $reviews) {
             //             $action_url = $reviews['url'];
             //             $picture_url = $reviews['user']['image_url'];
             //             $author_name = $reviews['user']['name'];
             //             $rating = $reviews['rating'];
             //             $date = $reviews['time_created'];
             //             $date = date("Y-m-d",strtotime($reviews['time_created']));
             //             $review = $reviews['text'];
             //             echo displayReview('yelp', @$row['style'], $i, $action_url, $picture_url, $author_name, $rating, $date, $review, $_type) ;
             //             $i++;
             //         }
             //     }
             //
             //
             //     echo displayReview('google', @$row['style'], -1) ;
             // }


               ?>


  </div>
<?php if ($default_plan_id==$current_plan['plan_id']): ?>
<p  class="text-center footer-label" >Powered by <a href="https://www.smbreviewer.com" target="_blank">SMBreviewer</a></p>
<?php endif; ?>


<?php if (!isset($_REQUEST['src'])): ?>
  <script src="<?= getServerURL()?>assets/vendor/jquery/jquery.js"></script>
  <script src="<?= getServerURL()?>assets/vendor/bootstrap/js/bootstrap.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
  <script>
  $(document).ready(function() {
      $(".smb-reviews-container .src-type-google .user-rating,.smb-reviews-container .src-type-facebook .user-rating,.smb-reviews-container .src-type-custom .user-rating").each(function(){
          var score = parseFloat($(this).html());
          if (isNaN(score)) {
            score=0;
          }
          var color = $(".smb-reviews-container").attr('rating_color');


          $(this).html("");
          $(this).rateYo({
              rating : score,
              starWidth: "15px",
              ratedFill: color,
              readOnly: true,
          })
      });
    var slideShow;

    switch ($("#smb_reviews<?=$id?>").attr("render_type")) {
      case 'list':
            renderPagination('list');
          break;
      case 'grid':
          renderPagination('grid');
          break;
      case 'slide':
        var carousel_timeout = $("#smb_reviews<?=$id?> .smb_reviews_slide").attr('carousel_timeout');
        slideShowFunction(carousel_timeout);
          break;
    }

    $("#smb_reviews<?=$id?>").show();

    addReadmore();

      $("<?='#smb_reviews'.$id?> .smb_reviews_slide .nav-prev,<?='#smb_reviews'.$id?> .smb_reviews_slide .nav-next").on("click slide", function(e) {
        var carousel_timeout = $("#smb_reviews<?=$id?> .smb_reviews_slide").attr('carousel_timeout');

        clearTimeout(slideShow);
        if (e.type == 'slide') {
        slideShowFunction(carousel_timeout);
        } else {
        slideShowFunction(carousel_timeout);
        }


        var a_s = $(this).siblings('.media-slide').find('li.active'),
          prev, next;
        a_s.removeClass('active',2000);

        if ($(this).hasClass('nav-prev')) {

          if ((prev = a_s.prev()) && (prev.length > 0)) {
            prev.css('margin-left', '-300%').addClass('active');

          } else {
            a_s.closest('.media-slide').find('li').last().css('margin-left', '-300%').addClass('active');

          }

        } else {
          if ((next = a_s.next()) && (next.length > 0)) {
            next.css('margin-left', '300%').addClass('active');
          } else {
            a_s.closest('.media-slide').find('li').first().css('margin-left', '-300%').addClass('active');
          }
        }

        $(this).siblings('.media-slide').find('li.active').animate({
          "margin-left": "0"
        }, 200, function() {
          $(this).removeAttr('style');
        });

      });

      $(document).on("click","<?='#smb_reviews'.$id?> .pagination span:not(.active)", function() {
        changeActivePage($(this));
      });

      function slideShowFunction(t) {
        slideShow = setTimeout(function(){ $("<?='#smb_reviews'.$id?> .smb_reviews_slide .nav-next").trigger('slide'); }, t);

      }


      function renderPagination(style) {

        if ((style=='list')
            || (style=='grid')
          ){

            var reviews_per_page  =  $('#smb_reviews<?=$id?>').attr('reviews_per_page');
            var total_reviews= $('#smb_reviews<?=$id?> ul li').length;
            var total_pages = (reviews_per_page!=0 ? Math.ceil(total_reviews/reviews_per_page) : 1) ;
            // $('#smb_reviews<?=$id?> ul li:nth-child(n+'+reviews_per_page+')').hide();
            var pagination = '';

              for (var i = 0; i < total_pages; i++) {
                pagination=pagination+'<span class="page_number'+(i==0 ? ' active' : '')+'">'+(i+1)+'</span>';
              }

              if (total_pages >1) {
                  pagination='<span class="page_prev"><</span>'+pagination+'<span class="page_next">></span>';
              }

              $('#smb_reviews<?=$id?>').append('<div class="pagination" reviews_per_page="'+reviews_per_page+'" active_page="1" total_pages="'+total_pages+'">'+pagination+'</div>');

              $('#smb_reviews<?=$id?> ul li:nth-child(n+'+0+')').hide();
              $('#smb_reviews<?=$id?> ul li:nth-child(n+1):nth-child(-n+'+reviews_per_page+')').show();

        }
      }

      function changeActivePage(obj) {

      var target_page =$(obj).text();
      var pagination = $(obj).closest('.pagination');
      var current_page = pagination.attr('active_page');
      var total_pages = pagination.attr('total_pages');
      var reviews_per_page = pagination.attr('reviews_per_page');
      pagination.children('span.active').removeClass('active');
      if (target_page=='>') {

        target_page = parseInt(current_page)+1;
      }
      else if (target_page=='<') {

        target_page = parseInt(current_page)-1;
      }

      if (parseInt(target_page)<1) {
      target_page=1;
      }

      if (parseInt(target_page)>parseInt(total_pages)) {
      target_page=total_pages;
      }

      var range_stop=parseInt(target_page)*parseInt(reviews_per_page);
      var range_start=range_stop-parseInt(reviews_per_page)+1;
      $('#smb_reviews<?=$id?> ul li:nth-child(n+'+0+')').hide();
      $('#smb_reviews<?=$id?> ul li:nth-child(n+'+range_start+'):nth-child(-n+'+range_stop+')').show();
      pagination.attr('active_page',target_page);
      pagination.children('span.page_number:nth-child('+(parseInt(target_page)+1)+')').addClass('active');
      $('html, body').animate({
                          scrollTop: $("#smb_reviews<?=$id?>").offset().top-200
                      }, 100);
      }


      function addReadmore() {

        if((review_text_size =   $('#smb_reviews<?=$id?>').attr('review_text_size')) && (review_text_size >0)){
          $('#smb_reviews<?=$id?> .r_review').each(function(i){
             var text = $(this).text();
             text = '<span class="read-more">'+text.substr(0,review_text_size)+'...[read more]</span>'+
             '<span style="display:none;">'+text+'</span>';
             $(this).html(text);
          });
        }
      }


    $(document).on("click","<?='#smb_reviews'.$id?> .r_review .read-more", function(e) {
      $(this).next().show(200);
      $(this).remove();

      });

      $('.r_date').each(function(i){
        if ( $(this).text().trim()!='') {
          var dt =(new Date(Date.parse($(this).text().trim()))).toLocaleDateString(navigator.language,{year: 'numeric', month: 'long', day: 'numeric' })
          $(this).text(dt);
        }
       });



    });



  </script>
  </body>
</html>

<?php endif; ?>


<?php

function displayReview($type, $view_type, $i=0, $action_url ="", $picture_url="", $author_name="", $rating="", $date="", $review="", $_type="", $_verified="")
{
    switch ($view_type) {
  case 'list':


$return =  '
  <li class="media thumbnail src-type-'.$type.'">
    <a class="pull-left" href="'.$action_url .'" target="_blank">

      <img class="media-object img-circle _picture_url_ r_dp" src="'.$picture_url.'" alt=""/>
      <span class="r_type">'.$_type.$_verified.'</span>
    </a>
    <div class="media-body">
      <h5 class="media-heading">
        <a href="'.$action_url.'" target="_blank" class="r_name">'.$author_name.'</a>
        <span class="user-rating r_rating rating_v'.str_replace('.', '_', $rating).'">'.$rating.'</span>
      </h5>
      <h6 class="r_date">'.$date.'</h6>
      <p class="r_review" align="justify">'.$review.'</p>
    </div>
  </li>';

  if ($i==0) {
      $return = '<ul class="media-list">';
  } elseif ($i==-1) {
      $return = '</ul>';
  }

    break;

  case 'grid':

  $return =  '

      <li class="thumbnail src-type-'.$type.'">

          <img src="'.$picture_url.'" class="img-responsive img-circle _picture_url_ r_dp" alt=""/>


          <div class="media-body">
            <span class="r_type">'.$_type.$_verified.'</span>
              <h5>
                <a href="'.$action_url .'" target="_blank" class="r_name">'.$author_name.'</a>
                <span class="user-rating r_rating rating_v'.str_replace('.', '_', $rating).'">'.$rating.'</span>
              </h5>
              <h6 class="r_date">'.$date.'</h6>
              <p class="r_review">'.$review.'</p>
          </div>
      </li>
  ';

  if ($i==0) {
      $return ='<ul class="media-grid">';
  } elseif ($i==-1) {
      $return = '</ul>';
  }

  break;
  default:



  $return =  '
  <li class="'.($i==1 ? 'active ':'').'media thumbnail src-type-'.$type.'">
    <a href="'.$action_url .'" target="_blank">

      <img class="media-object img-circle _picture_url_ r_dp" src="'.$picture_url.'" alt=""/>

      <span class="r_type">'.$_type.$_verified.'</span>
    </a>
    <div class="media-body">
      <h5 class="media-heading">
        <a href="'.$action_url.'" target="_blank" class="r_name">'.$author_name.'</a>
        <span class="user-rating r_rating rating_v'.str_replace('.', '_', $rating).'">'.$rating.'</span>
      </h5>
      <h6 class="r_date">'.$date.'</h6>
      <p class="r_review" align="justify">'.$review.'</p>
    </div>
  </li>';

  if ($i==0) {
    global $row;
      $return =
    '<div class="smb_reviews_slide" carousel_timeout="'.(($row['carousel_timeout']<=0)? 2000 :$row['carousel_timeout']).'"><div class="nav-prev"><span><</span></div><ul class="media-slide">';

  } elseif ($i==-1) {
      $return ='</ul><div class="nav-next"><span >></span></div></div>';
  }

    break;
}


    return $return;
}

function getReviewsSorted($reviews, $type="date", $way="desc")
{


  $reviews_result =[];
  $i=0;

    switch ($type) {
      case 'rating':
        foreach ($reviews as $review) {
          $i++;
          $reviews_result[$review['rating'].'_'.$review['date'].'_'.$i] = $review;
        }
        break;
      case 'date':
        foreach ($reviews as $review) {
          $i++;
          $reviews_result[$review['date'].'_'.$i] = $review;
        }
        break;
      case 'random':
        foreach ($reviews as $review) {
          $i++;
          $reviews_result[mt_rand().'_'.$i] = $review;
        }
      case 'text_length':
        foreach ($reviews as $review) {
          $i++;
          $reviews_result[str_pad(mb_strlen($review['review']),20,'0',STR_PAD_LEFT).'_'.sprintf('%05d',$i)] = $review;
        }
        break;
      default:
        $reviews_result=$reviews;
        break;
    }


switch ($way) {
  case 'asc':
    ksort($reviews_result);
    break;
  default:
    krsort($reviews_result);
    break;
}

  return $reviews_result;
}


function post_fb($url, $method, $body="", $apiKey="")
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    } else {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    }
    return curl_exec($ch);
}

function getServerURL()
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

    if (isset($_SERVER["HTTPS"])) {
        $protocol = "https://";
    } else {
        $protocol = "http://";
    }

    $applicationPath = str_replace("www.", "", $applicationPath);
    $applicationPath = str_replace("http://", $protocol, $applicationPath);
    $applicationPath = str_replace("https://", $protocol, $applicationPath);

return $protocol.$serverName.'/';

    //return $applicationPath;
}
?>
