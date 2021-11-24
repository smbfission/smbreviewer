<?php
ob_start();
include_once("header.php");
include_once("sidebar.php");
require_once('core/tools.php');

$fonts = ["Arial","Times","Courier","Verdana","Georgia","Palatino","Comic Sans MS","Trebuchet MS","Arial Black","Impact"];
$facebook_json = "";
$google_json = "";
$custom_json = "";

$campID = base64_encode($_REQUEST['id']);
$settings = $current_user;

if (trim(@$settings['app_id'])=="" || trim(@$settings['app_secret'])=="") {
    $access_token = "";
} else {
    $access_token = @$settings['access_token'];
}

$yelp_api_key = @$settings['yelp_api_key'];

$row = $db->getCampaignByUserIdCampaignId($_SESSION['user_id'], $_REQUEST['id']);
$user = $db->getUserProfile($_SESSION['user_id']);
$row['short_io_api_key'] = $user['short_io_api_key'];

if (count($row) == 0) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /campaign/");
    exit();
}

if (isset($row['short_io_api_key']) && trim($row['short_io_api_key']) != "") {
    $short_io_domains = Tools::capture_reviews_get_short_io_domains($row['short_io_api_key']);
    $short_io_domains = json_decode($short_io_domains, true);

    if(isset($row['short_io_domain']) && trim($row['short_io_domain']) != ""){
        $short_io_domain_link = Tools::siteURL() . '/reviews/'.$campID;
        $short_io_domain_link = json_decode(Tools::capture_reviews_get_short_io_by_origin_url($row['short_io_api_key'], ['domain' => $row['short_io_domain'], 'originalURL' => $short_io_domain_link]), true);

        if (!isset($short_io_domain_link['error'])) {
            $short_io_domain_link = $short_io_domain_link['shortURL'];
        } else if ($short_io_domain_link['error'] === "Link not found") {
            $short_io_domain_link = "";
        } else {
            $short_io_domain_link = $short_io_domain_link['error'];
        }
    }
}

$id= $_REQUEST['id'];
$refresh_time=0;

$gr=$db->getLastReviewsUpdateDatesByCampaingId($_REQUEST['id']);
$start_date = new DateTime();
$minimum_rate = (int)$row['minimum_rate'];

$reviews_array = [];
$errors_array = [];

if (@$row['is_facebook']==1) {
    $limit = (int)$row['fb_reviews_cnt'];
    $recommendation_type = $row['recommendation_type'];
    $url="https://graph.facebook.com/$row[fb_page]?access_token=$row[page_token]".""."&limit=".'100'."&fields=ratings{created_time,has_rating,has_review,open_graph_story,rating,review_text,reviewer{name,id,picture},recommendation_type},rating_count";
    $facebook_json = [];
    $since_start = $start_date->diff(new DateTime(@$gr['fb_last_update']));

    if ($since_start->i >= $refresh_time) {
        $g_json=post_fb($url, "get");
        $g=json_decode($g_json,true);

        if (!isset($g['ratings'])) {
            $errors_array['facebook_error']=$g['message'];
            $db->updateFacebookReviewCacheByCampaignId($_REQUEST['id'], '');
        } else {
            $db->updateFacebookReviewCacheByCampaignId($_REQUEST['id'], $g_json);
        }
    }

    $gr=null;
    $gr=$db->getLastReviewsByCampaingId($_REQUEST['id'], 'fb_reviews');
    if (count($gr) > 0) {
        $g_json=$gr['fb_reviews'];
        $g=json_decode($g_json,true);
        if ($g = $g['ratings']['data']) {
            foreach ($g as $v) {
                if ($limit>0) {
                    if ($recommendation_type == 'any'
                || $recommendation_type == $v['recommendation_type']
                ) {
                        if ((int)$v['has_rating'] ==1 && $v['rating'] >= $minimum_rate) {
                            $facebook_json[] = $v;
                            $limit--;
                        } elseif ((int)$v['has_rating'] ==0) {
                            $facebook_json[] = $v;
                            $limit--;
                        }
                    }
                }
            }
        }
    }
    foreach (json_decode(json_encode($facebook_json), true) as $value) {
        $reviews_array[] = [

     "action_url" => 'https://facebook.com/'.(isset($value['open_graph_story']) ? $value['open_graph_story']['id'] : '' ),
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


    $facebook_json = json_encode(['data'=>$facebook_json]);
}

if (@$row['is_google']==1) {
    $url="https://maps.googleapis.com/maps/api/place/details/json?placeid=$row[place_id]&fields=name,rating,formatted_phone_number,reviews&key=".@$settings['google_key'];

    $limit = (int)$row['google_reviews_cnt'];
    $google_json = null;
    $since_start = $start_date->diff(new DateTime(@$gr['google_last_update']));


    if ($since_start->i >= $refresh_time) {
        $g_json=post_fb($url, "get");
        $g=json_decode($g_json);
        $google_api_call_errors = '';
        if (property_exists($g, 'error_message')) {
            $google_api_call_errors = '<i class="fa fa-warning"></i> '.$g->error_message;
            $errors_array['google_error'] = $g->error_message;
            $db->updateGoogleReviewCacheByCampaignId($_REQUEST['id'], '');
        } else {
            $db->updateGoogleReviewCacheByCampaignId($_REQUEST['id'], $g_json);
        }
    }

    $gr=null;
    $gr=$db->getLastReviewsByCampaingId($_REQUEST['id'], 'google_reviews');
    if (count($gr) > 0) {
        $g_json=$gr['google_reviews'];
        $g=json_decode($g_json);
    }


    if (@count($g)>0) {
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
                    }
                }
                $v->reviews = $reviews; ///what is this !!&&!?
            }
        }
    }
    $google_json = json_encode($google_json);
}



if (@$row['is_yelp']==1) {
    $limit = (int)$row['yelp_reviews_cnt'];



    $yelp_json = [];

    $since_start = $start_date->diff(new DateTime(@$gr['yelp_last_update']));

    $gr=null;
    $gr=$db->getLastReviewsByCampaingId($_REQUEST['id'], 'yelp_reviews');


    if (($since_start->i >= ($limit>3?  24*60 :$refresh_time)) || (trim($gr['yelp_reviews'])=='')) {


        if ($limit<=3) {

           $url="https://api.yelp.com/v3/businesses/$row[yelp_business_id]/reviews";
           $y_json=post_fb($url, "get", "", @$settings['yelp_api_key']);
           $y= json_decode($y_json,true);

        } else {
          require_once(dirname(__FILE__).'/core/scrapper.php');

           $scrapper= new Scrapper();
           $scrapper->checkProxyDb();
           $y = $scrapper->getYelpReviewsArray($row['yelp_business_id'],$limit);

        }

        if (isset($y['reviews'])) {

            $db->updateYelpReviewCacheByCampaignId($_REQUEST['id'], base64_encode(gzencode(json_encode($y,true),9)));
            //$db->updateYelpReviewCacheByCampaignId($_REQUEST['id'], json_encode($y,true));
            $gr=$db->getLastReviewsByCampaingId($_REQUEST['id'], 'yelp_reviews');

        } else {
            $errors_array['yelp_error']=$y;
        }
    }

    if (trim($gr['yelp_reviews'])!='') {
        if (!Tools::isJson($gr['yelp_reviews'])) {
          $gr['yelp_reviews'] = gzdecode(base64_decode($gr['yelp_reviews']));
        }
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
} else {
    $db->updateYelpReviewCacheByCampaignId($_REQUEST['id'], '');
}




if (@$row['is_custom']==1) {
    $url=getServerURL()."review_api.php?uid=".$_SESSION['user_id']."&campaigns_id=".$row['id'];
    $custom_json=post_fb($url, "get");

    if (($c = json_decode($custom_json, true)) && (isset($c['data']))) {


    foreach ($c['data'] as $value) {

      $custom_icon = ($value['icon']!="") ? '<img class="icon-custom" src="'.
                                              (filter_var($value['icon'], FILTER_VALIDATE_URL) ? $value['icon'] : getServerURL()."uploads/custom_reviews/".$value['icon'] ).
                                              '">' : '<i class="icon-custom"></i>';

      if (trim($value['facebook_id'])!='') {
       $custom_icon='<i class="icon-facebook"></i>';
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
}

$sort_array = ['0'=>'date',
               '1'=>'rating',
               '2'=>'random',
               '3'=>'text_length',
              ];
$reviews_array = getReviewsSorted($reviews_array,$sort_array[(int)$row['order_way']]);


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
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    if ($apiKey!="") {
        $headr[] = 'Authorization: Bearer '.$apiKey;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
    }
    if ($method == "post") {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    } else {
        //curl_setopt($ch, CURLOPT_HTTPGET, true );
    }
    $res = curl_exec($ch);
    //echo curl_error($ch);
    return $res;
}


function post_fb2()
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://reviews.socialspider.net/review_api.php?uid=1",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }
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
//    return $applicationPath;
}

function displayReview($type, $view_type, $i=0, $action_url ="", $picture_url="", $author_name="", $rating="", $date="", $review="", $_type="", $_verified ="")
{

  //$_verified = false;
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



$appURL = getServerURL();

?>



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
color:<?= $row['name_color'] ?>;
cursor: pointer;
width: 20px;
box-sizing: unset;


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

  width: 50px;
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
    color:<?php echo $row['name_color']; ?>;
}
<?='#smb_reviews'.$id?> .r_rating{
    color:<?php echo $row['rating_color']; ?>;
    <?= ($row['display_rating']=="0" ? "display: none;" : "")?>

}
<?='#smb_reviews'.$id?> .r_date{
    color:<?php echo $row['date_color']; ?>;
    <?= ($row['display_date']=="0" ? "display: none;" : "")?>


}
<?='#smb_reviews'.$id?> .r_review{
    color:<?php echo $row['review_color']; ?>;
    font-size:<?php echo $row['font_size']; ?>;
    <?= (trim($row['font_family'])!="" ? "font-family:".$row['font_family'].";" : "")?>
    <?= ($row['display_review']=="0" ? "display: none;" : "")?>
    text-align: justify;

}

<?='#smb_reviews'.$id?>  .media-slide .r_review, <?='#smb_reviews'.$id?>  .media-grid .r_review{
  text-align: center;
}

<?='#smb_reviews'.$id?> .thumbnail{
    background:<?php echo $row['widget_bg_color']; ?>;
    box-shadow:0 2px 5px <?php echo $row['widget_box_shadow']; ?>, 0 2px 10px rgba(0, 0, 0, 0);
}
<?='#smb_reviews'.$id?> .carousel-inner .item{
    background:<?php echo $row['widget_bg_color']; ?>;
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
        background: <?= $row['widget_bg_color']; ?>;
        color: <?= $row['review_color']; ?>;
        font-weight: 600;
        padding: .5rem;
        margin: .5rem;
        border-radius: 3px;
        cursor: default;
}



/*
    <?='#smb_reviews'.$id?> .img-container { position: relative; }
    <?='#smb_reviews'.$id?> .img-container img { display: block; }
    <?='#smb_reviews'.$id?> .img-container .fa { position: absolute; top:0; right:-8px; color:green; }
 */

    <?='#smb_reviews'.$id?> .r_type .fa { position: absolute; top:4px; margin-left: -4px; color:green; }

</style>
<style>
#settings_nav  ul li  {
    padding-right:  .5rem;
}

#settings_nav  ul li a {
    padding: 1rem .5rem;

}
.text-danger {
    font-size: 11px;
}
.switch{
    width:90%;
}
.code {
    padding: 6px 5%;
    color: #c7254e;
    background-color: #f9f2f4;
}
.select2-container .select2-choice{
    height: 35px !important;
    line-height: 21px !important;

}

#custom_reviews_area .select2-search-choice {
background: #5bc0de;
}


.jq-ry-container{
    padding:0 !important;
}

.code {
    padding: 6px 1rem;
    color: #c7254e;
    background-color: #f9f2f4;
    font-size: 1.1rem;
    height: auto;
    overflow: scroll;
    border: none;
    white-space:pre;
}

.code {
    -ms-overflow-style: none;  /* Internet Explorer 10+ */
    scrollbar-width: none;  /* Firefox */
}
.code::-webkit-scrollbar, .code::-webkit-scrollbar-corner {
   /* Safari and Chrome */
   background-color: transparent;
}

.unselectable {
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.embed-code {
  display: flex;
  flex-direction: column;
}
.tag-head, .tag-body {
    display: block;
}

.flex-center {
    display: flex;
    justify-content: center;
}

.flex-center button, .alert {
       margin-top: 10px;
}

#campaign_form .input-group .input-group-addon i {
    font-weight: 100;
}

#campaign_form .input-group .form-control{
    min-width:3rem;
    padding: 0;
    text-align: center;
}

#short-io-container .form-control {
    text-align: left !important;
}

#short-io-container .form-control {
    padding: 6px 12px !important;
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
<link rel="stylesheet" href="/assets/vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.css" />

<!-- campaign section -->
    <section role="main" class="content-body">
    	<header class="page-header">
    		<h2>Campaigns</h2>

    		<div class="right-wrapper pull-right" style="display:none;">
    			<ol class="breadcrumbs">
    				<li>
    					<a href="index.html">
    						<i class="fa fa-home"></i>
    					</a>
    				</li>
    				<li><span>Tables</span></li>
    				<li><span>Advanced</span></li>
    			</ol>

    			<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    		</div>
    	</header>

      <div class="row">
    		<div class="col-lg-12">
    			<section class="panel">
    				<div class="panel-body">
              <div class="row">
                <div class="col-sm-12">
      						<h2 class="h2 mt-none mb-sm text-dark text-bold">
                                                      Campaign Setup
                  </h2>
      						<p class="text-muted font-13 m-b-15">
                                                      <!-- Description Goes Here <br /> -->
                                                      &nbsp;
                  </p>
      					</div>

                <?php
                if (!isset($_SESSION['msg_type'])) {
                    $_SESSION['msg_type']='info';
                }
                if (isset($_SESSION['msg']) && $_SESSION['msg']!="") {
                    ?>
                    <div class="col-sm-12">
                        <div class="alert alert-<?=$_SESSION['msg_type']?>"><?= $_SESSION['msg'] ?></div>
                    </div>
                    <?php
                    unset($_SESSION['msg']);
                    unset($_SESSION['msg_type']);
                }
                ?>

              </div>

              <div id="map" style="display: none;"></div>
              <form action="/action.php?action=campaigns" id="campaign_form" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered">
                <div class="form-group">
    							<label class="col-md-3 control-label" for="inputDefault">Campaign Name:</label>
    							<div class="col-md-6">
                    <input class="form-control" name="title" value="<?php echo @$row['title'] ?>" type="text">
                    <input type="hidden" name="social_type" id="social_type" value="1">
    							</div>
    						</div>

                <div class="form-group">
    							<label class="col-md-3 control-label" for="inputDefault">Facebook:</label>
    							<div class="col-md-6">
                    <input id="is_facebook" value="1" name="is_facebook" type="checkbox" onclick="showHidePlatform(this,'facebook')" <?= (@$row['is_facebook']=="1") ? "checked" : "" ?> />
                      <label for="is_facebook"> Connent your Facebook Pages / Business</label>
                      <?php if (trim(@$settings['access_token'])=="" || trim(@$settings['app_id'])=="" || trim(@$settings['app_secret'])==""): ?>
                        <br /><span class="text-danger"><i class="fa fa-warning"></i> Facebook App credentials are missing! Please add <a href="/settings/">here</a> to get Facebook page reviews</span>
                      <?php endif; ?>
    							</div>
    						</div>

                <div class="form-group" id="f_area" <?= ((@$row['is_facebook']=="0" || @$row['is_facebook']=="") ? "style=\"display:none\"" : "") ?> >
    						  <div class="col-md-3"> </div>
    							<div class="col-md-6 col-xs-9">

                    <?php

                    if (isset($_REQUEST['log']) && isset($_REQUEST['limit']) && $_REQUEST['limit']!="") {
                        $limit = $_REQUEST['limit'];
                    }

                    $url="https://graph.facebook.com/me/accounts?access_token=".$access_token."&fields=access_token,name,id,rating_count";


                    $json=post_fb($url, "get");

                    $pages=json_decode($json, true);

                    $l=3;
                    while (isset($pages['error']) &&  $l>0) {

                        $url_e = $url."&limit=".$l;
                        // error_log('error='.$url_e);
                        $l--;

                        $json=post_fb($url_e, "get");

                        $pages=json_decode($json, true);
                    }


                    if (isset($_REQUEST['log'])) {
                        echo "<pre>";
                        print_r($pages);
                        echo "</pre>";
                    }
                    ?>

    								<select class="form-control" id="fb_page" name="fb_page" onchange="getReviews(this)">
                      <option value=""> Choose Facebook  Page/Business </option>
                      <?php if (isset($pages['data']) && count($pages['data'])>0): ?>
                        <?php foreach ($pages['data'] as $page): ?>
                          <option <?= ((@$row['fb_page'] == $page['id']) ? "selected=\"selected\"" : "" ) ?> value="<?= base64_encode(json_encode(['page_id'=>$page['id'],'access_token'=>$page['access_token'],'page_name'=>$page['name']]))?>">
                            <?=$page['name']?>(total reviews <?=$page['rating_count']?>)
                           </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
    								</select>
    							</div>

                  <div class="col-md-2 col-sm-2 col-xs-3">
                    <div class="input-group">
                      <div class="input-group-addon bg-success"><i class="fa fa-minus" aria-hidden="true"></i></div>
                      <input class="form-control" max="<?= $current_user['fb_reviews_cnt'] ?>" value="<?= $row['fb_reviews_cnt'] ?>" name="fb_reviews_cnt" type="text"/>
                      <div class="input-group-addon bg-danger"><i class="fa fa-plus" aria-hidden="true"></i></div>
                    </div>
                  </div>
                  <div class="clearfix"></div>
                  <div class="col-md-3"></div>
                  <div class="col-md-9">
                    <span style="font-size:12px;font-weight: normal !important;color: #797979;">
                          ( List of your Facebook pages )
                    </span>
                  </div>
                  <div class="clearfix"></div>
                  <div class="col-md-3"></div>
                  <div class="col-md-2">
                    <select class="form-control" name="recommendation_type" >
                      <option value="any" <?= ($row['recommendation_type']==""||$row['recommendation_type']=="any" ? "selected":"")?> >any</option>
                      <option value="positive" <?= ($row['recommendation_type']=="positive" ? "selected":"")?>> positive</option>
                      <option value="negative" <?= ($row['recommendation_type']=="negative" ? "selected":"")?>> negative</option>
                    </select>
                  </div>
                  <div class="clearfix"></div>
                  <div class="col-md-3"></div>
                  <div class="col-md-9">
                    <span style="font-size:12px;font-weight: normal !important;color: #797979;">
                              ( Recommendation type )
                    </span>
                  </div>

                  <div class="clearfix"></div>
                </div>


                <div class="form-group">
    							<label class="col-md-3 control-label" for="inputDefault">Google:</label>
    							<div class="col-md-6">
                    <input id="is_google" value="1" name="is_google" type="checkbox" onclick="showHidePlatform(this,'google')" <?php if (@$row['is_google']=="1") {
                          echo "checked";
                      } ?> />
                    <label for="is_google"> Connent your Google Places / My Business Reviews</label>
                    <?php
                    if (trim(@$settings['google_key'])=="") {
                        ?>
                    <br /><span class="text-danger"><i class="fa fa-warning"></i> Google project key missing! Please add <a href="/google_settings/">here</a> to get Google place reviews</span>
                    <?php
                    }
                    ?>
    							</div>
    						</div>

                <div class="form-group" foo="<?=$row['is_google']?>" id="g_area" <?= ((@$row['is_google']=="0" || @$row['is_google']=="") ? "style=\"display:none\"" : "") ?> >
                  <div class="col-md-3"> </div>
                  <div class="col-md-6 col-xs-9">
                    <input class="form-control" name="google_business" autocomplete="off" id="google_business" value="<?php echo @$row['google_business'] ?>" type="text" placeholder="Enter Business / Place" />

                    <span>You can find your place id via <a target="_blank" href="https://developers-dot-devsite-v2-prod.appspot.com/maps/documentation/javascript/examples/places-placeid-finder"> Google Places ID Finder</a></span>
                    <input class="form-control" type="text" name="place_id" placeholder="Your place id"  id="place_id" value="<?php echo @$row['place_id'] ?>" />
                    <span class="text-danger"><?= @$google_api_call_errors?></span>
                  </div>
                  <div class="col-md-2 col-sm-2 col-xs-3">
                    <div class="input-group">
                      <div class="input-group-addon bg-success"><i class="fa fa-minus"   aria-hidden="true"></i></div>
                      <input class="form-control" value="<?= $row['google_reviews_cnt'] ?>" max="<?= $current_user['google_reviews_cnt'] ?>" name="google_reviews_cnt" type="text"/>
                      <div class="input-group-addon bg-danger"><i class="fa fa-plus" aria-hidden="true"></i></div>
                    </div>
                  </div>
                  <div class="clearfix"></div>
                </div>

                <div class="form-group">
    							<label class="col-md-3 control-label" for="inputDefault">Yelp:</label>
    							<div class="col-md-6">
                                          <input id="is_yelp" value="1" name="is_yelp" type="checkbox" onclick="showHidePlatform(this,'yelp')" <?php if (@$row['is_yelp']=="1") {
                        echo "checked";
                    } ?> />
                                          <label for="is_yelp"> Connent your Yelp Business</label>

                                          <?php
                                          if (trim(@$settings['yelp_api_key'])=="") {
                                              ?>
                                          <br /><span class="text-danger"><i class="fa fa-warning"></i> Yelp key missing! Please add api key <a href="/yelp_settings/">here</a> to get Yelp reviews</span>
                                          <?php
                                          }
                                          ?>
    							</div>
    						</div>

                <div class="form-group" id="y_area" style="display: <?php if (@$row['is_yelp']=="0" || @$row['is_yelp']=="") {
                                              echo "none";
                                          } ?>;">
    							<div class="col-md-3"></div>
                  <div class="col-md-5 col-sm-8 col-xs-7">
                                            <input class="form-control" name="yelp_business_id" id="yelp_business_id" value="<?php echo @$row['yelp_business_id'] ?>" type="text" placeholder="Enter Yelp Business ID" />



                                            <span id="yelp_error_area" class="text-danger"></span>
                  </div>
                  <div class="col-md-1 col-sm-1 col-xs-2" style="padding-left:0px;">
                                        <button class="btn btn-default" type="button" id="yelp_reviews" onclick="getReviews(this);"><i class="fa fa-repeat"></i></button>
                  </div>

                  <div class="col-md-1 col-sm-2 col-xs-3">
                    <div class="input-group">
                      <div class="input-group-addon bg-success"><i class="fa fa-minus"   aria-hidden="true"></i></div>
                      <input class="form-control" value="<?= $row['yelp_reviews_cnt'] ?>" max="<?= $current_user['yelp_reviews_cnt'] ?>" name="yelp_reviews_cnt" type="text"/>
                      <div class="input-group-addon bg-danger"><i class="fa fa-plus" aria-hidden="true"></i></div>
                    </div>
                  </div>
                        <div class="col-md-3"></div>
                  <div class="col-md-6 ">

                                                              <?php if ($row['yelp_reviews_cnt']<=3): ?>
                                                                <span>The Yelp basic API only allows for 3 of your latest reviews to show, want to pull all of your reviews? Upgrade for $4.99</span>
                                                              <?php endif; ?>
                  </div>
                  <div class="clearfix"></div>
                </div>

                <div class="form-group">
    							<label class="col-md-3 control-label" for="inputDefault">Custom:</label>
    							<div class="col-md-4 ">
                                        <input class=" " id="is_custom" value="1" name="is_custom" type="checkbox" onclick="showHidePlatform(this,'custom');" <?php if (@$row['is_custom']=="1") {
                                              echo "checked";
                                          } ?> />
                                        <label class=" " for="is_custom"> Your Custom Reviews</label>
                  </div>
                </div>

                <div class="form-group" id="custom_reviews_area">
                  	<div class="col-md-3"></div>
                  <div class="col-md-6 ">
                    <?php if (trim($row['custom_icon'])!=''): ?>
                      <img src="/uploads/campaign/<?= @$row['custom_icon'] ?>" alt="">
                    <?php endif; ?>

                      <label class="" for="custom_icon"> Your Default Custom Icon</label>
                      <input type="file" class="" name="custom_icon" id="custom_icon">
                      <input type="hidden" name="hidden_custom_icon"  value="<?= @$row['custom_icon'] ?>">

    							</div>
                  <div class="clearfix"></div>
                  <br>
                  <div class="col-md-3"></div>
                  <div class=" col-md-6">

                  <label class="" for="tags">Tags</label>
                  <select  class="select2-tags" multiple="multiple" name="tags[]">

                    <?php if (($tags = $db->getTagsByUserId($_SESSION['user_id'])) && $tags!= null ){ foreach ($tags as $value): ?>
                      <option <?= ((in_array($value['name'],explode(',', $row['tags']))) ? "selected" :"") ?> value="<?= $value['id']?>" data-badge=""><?=$value['name']?></option>
                    <?php endforeach; } ?>

                  </select>
                  <span style="font-size:12px;font-weight: normal !important;color: #797979;">
                            ( A tag can only be selected if it's already assigned for a 'custom review' )
                  </span>
                  </div>

                </div>



                <div class="form-group">
                  <label class="col-md-3 control-label" >Minimum Rating:</label>
                  <div class="col-md-1 col-sm-2 col-xs-3">
                    <div class="input-group">
                      <div class="input-group-addon bg-success"><i class="fa fa-minus"   aria-hidden="true"></i></div>
                      <input class="form-control" value="<?= $row['minimum_rate'] ?>" max="5" name="minimum_rate" type="text"/>
                      <div class="input-group-addon bg-danger"><i class="fa fa-plus" aria-hidden="true"></i></div>
                    </div>
                  </div>

                </div>
                <div class="form-group">
    				<label class="col-md-3 control-label" for="inputDefault">Your Campaign URL:</label>
    				<div class="col-md-6"> You may find your campaign URL below:
    					<br>
                        <a target="_blank" href="<?= $appURL.'reviews/'.$campID ?>"><?= $appURL.'reviews/'.$campID ?></a><br>
                        <!--<br>Want to personalize and use your own domain for your public page? Copy the campaign URL and paste it into your
                        <a href="https://go.smbreviewer.com/short-io" target="_blank" >free Short.io account</a> to make a free customized link.
                        <a href="http://tutorials.smbreviewer.com/l/jrjghj0m1i-2qe3o1wt7n" target="_blank">View the instructions</a> (at 4:45) for more details.-->
    				</div>
    				<!--<?= var_dump($_REQUEST['id']); var_dump(base64_encode($_REQUEST['id']));?>-->
    			</div>
    			<div class="form-group">
    			    <label class="col-md-3 control-label" style="text-align:left">Short.io Brandable URL Integration
    			        <br> Would you like to customize and brand your URL with your *free*
    			        <a href="https://go.smbreviewer.com/short-io" target="_blank">Short.io</a> account? Enter your *Secret* <a href="https://short.io/features/api" target="_blank">API key</a>
                        and select a domain.
                    </label>
                    <div class="col-md-4">
                        <!-- Short.io -->
                        <?php if (isset($row['short_io_api_key']) && $row['short_io_api_key'] != ''): ?>
                            <div class="input-group" id="short-io-group">
                                <div class="input-group-addon" style="width: 38px; height: 98px" id="input-group-addon-shortio">
                                    <input type="checkbox" class="" name="enable_short_io" id="enable_short_io"
                                           value="<?= isset($row['enable_short_io']) ? $row['enable_short_io'] : "" ?>" <?= (isset($row['enable_short_io']) && (int)$row['enable_short_io'] != 0 ? "checked" : "") ?>>
                                </div>

                                <div id="short-io-container">
                                    <?php if (isset($row['short_io_api_key']) && $row['short_io_api_key'] != ""): ?>
                                        <select class="form-control" name="short_io_domain" data-toggle="tooltip" data-placement="top" title="Short.io Domain">
                                            <?php foreach ($short_io_domains as $domain): ?>
                                                <option value="<?= $domain['hostname'] ?>" <?= ($domain['hostname'] == $row['short_io_domain'] ? "selected" : "") ?> ><?= $domain['hostname'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>

                                    <input type="text" class="form-control" name="custom_link" id="custom_link" placeholder="Enter custom url handle (optional)">
                                    <p class="form-control <?= ($short_io_domain_link != "" ? "hidden":"")?>" id="short_io_message">Short.io URL is not set</p>
                                    <a target="_blank" class="form-control <?= ($short_io_domain_link == "" ? "hidden":"")?>" id="short_io_domain_link"
                                        href="<?= $short_io_domain_link ?>" data-toggle="tooltip"
                                        data-placement="top" title="Short.io link"><?= $short_io_domain_link ?></a>
                                </div>
                            </div>
                        <?php else: ?>
                            Please connect your account to short.io in settings to enable Short.io.
                        <?php endif; ?>

                        <div class="alert alert-danger" role="alert" id="short-io-error"></div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-3"></div>
                    <div class="col-md-4 flex-center">
                        <button type="button" id="set_short_io"
                                class="btn  btn-fill btn-primary">
                            <span class="ace-icon fa fa-check-square-o bigger-120"></span> Set
                        </button>
                    </div>
    			</div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="inputDefault">Social Share:</label>
                  <div class="col-md-6">
                                          <input id="is_social_share" value="1" name="is_social_share" type="checkbox" onclick="showHidePlatform(this,'social_share')" <?php if (@$row['is_social_share']=="1") {
                                              echo "checked";
                                          } ?> />
                                          <label for="is_social_share"> </label>
                  </div>
                </div>


                <div class="form-group" id="social_share_area" style="display: <?php if (@$row['is_social_share']=="0" || @$row['is_social_share']=="") {
                                              echo "none";
                                          } ?>;">

                    <div class="form-group">
    									<label class="col-md-3 control-label">Meta Title</label>
    									<div class="col-md-6">
                                              <input class="form-control" name="meta_title" id="meta_title" value="<?php echo @$row['meta_title'] ?>" type="text" placeholder="Enter Meta Title" />
    									</div>
    								</div>

                    <div class="form-group">
    									<label class="col-md-3 control-label">Meta Description</label>
    									<div class="col-md-6">
                                              <input class="form-control" name="meta_description" id="meta_description" value="<?php echo @$row['meta_description'] ?>" type="text"  placeholder="Enter Meta Description"/>
    									</div>
    								</div>

                    <div class="form-group">
    									<label class="col-md-3 control-label" for="inputDefault"></label>
    									<div class="col-md-6">
                                              <input type="file" name="meta_picture" class="filestyle" data-buttontext="Choose File" data-buttonname="btn-default" data-placeholder="Enter Meta Picture" style="padding-left: 0;">
                                              <input type="hidden" name="hidden_meta_picture" id="hidden_meta_picture" value="<?php echo @$row['meta_picture'] ?>" />

                                              <?php
                                              if (@$row['meta_picture']!="") {
                                                  ?>
                                                  <img style=" width:100px;" src="/uploads/<?php echo $row['meta_picture']; ?>" />
                                                  <?php
                                              }
                                              ?>
    									</div>

                      <?php if ((int)@$row['is_social_share']=="1"): ?>
                        <div class="clearfix"></div>
                        <br>
                        <div id="social_share_btns">
                            <label class="col-md-3 control-label" >Share on</label>
                            <div class="button-list col-md-9">
                                <button type="button" class="btn btn-facebook waves-effect waves-light" onclick="socialShare('facebook')">
                                   <i class="fa fa-facebook m-r-5"></i> Facebook
                                </button>

                                <!-- <button type="button" class="btn btn-googleplus waves-effect waves-light" onclick="socialShare('gplus')">
                                   <i class="fa fa-google-plus m-r-5"></i> Google+
                                </button> -->

                                <button type="button" class="btn btn-pinterest waves-effect waves-light" style="background-color:red; color:white;" onclick="socialShare('pinterest')">
                                   <i class="fa fa-pinterest m-r-5"></i> Pinterest
                                </button>

                                <button type="button" class="btn btn-twitter waves-effect waves-light" onclick="socialShare('twitter')">
                                   <i class="fa fa-twitter m-r-5"></i> Twitter
                                </button>

                                <button type="button" class="btn btn-linkedin waves-effect waves-light" onclick="socialShare('linkedin')">
                                   <i class="fa fa-linkedin m-r-5"></i> Linkedin
                                </button>

                            </div>

                        </div>

                      <?php endif; ?>
    								</div>

                </div>


                  <div class="form-group">
    							<label class="col-md-3 control-label" for="inputDefault">Save Campaign</label>
    							<div class="col-md-6">
                   Be sure that you click on the green 'Save Campaign' button below after every change to ensure that you save any changes or enhancements.
    							</div>
    						</div>



                <div class="form-group">
                  <div class="col-md-1"></div>
                  <div class="col-md-11" style="position: absolute;left: 50%;">
                      <input name="id" type="hidden" value="<?php echo @$_REQUEST['id'] ?>" />
                      <button class="btn btn-success" type="button" onclick="submitForm()"> Save Campaign</button>
                  </div>
                </div>

                <div class="row">
                                      <div class="col-md-12">
                                          <hr />
                                      </div>
                </div>



                <div class="">
                  <div class="col-md-4" style="padding-left:0px;">
                    <div id="settings_nav" style="display:<?php if (@$row['is_facebook']=="1" || @$row['is_google']=="1" || @$row['is_yelp']=="1" || @$row['is_custom']=="1") {
                                                  echo "";
                                              } else {
                                                  echo "none";
                                              } ?>;">

                      <ul class="nav nav-tabs tabs-bordered">
                          <li  role="presentation" class="active">
                              <a href="#settings_tab" data-toggle="tab" aria-expanded="true">
                                  <span class="visible-xs"><i class="fa fa-cogs"></i></span>
                                  <span class="hidden-xs">Settings</span>
                              </a>
                          </li>
                          <li  role="presentation" class="">
                              <a href="#embed_tab" data-toggle="tab" aria-expanded="false">
                                  <span class="visible-xs"><i class="fa fa-code"></i></span>
                                  <span class="hidden-xs">Embed Code</span>
                              </a>
                          </li>
                          <li  role="presentation" class="">
                              <a href="#embed_if_tab" data-toggle="tab" aria-expanded="false">
                                  <span class="visible-xs"><i class="fa fa-file-code-o"></i></span>
                                  <span class="hidden-xs">Embed iFrame</span>
                              </a>
                          </li>
                      </ul>

                      <div class="tab-content">
                        <div class="tab-pane active" id="settings_tab">
                          <div class="form-group">
                            <label class="col-md-12">Widget Style</label>
                            <div class="col-md-4">
                              <div class="radio-custom radio-success">
    										        <input type="radio" name="style" id="list" value="list" onclick="changeStyle('list')" <?php if (@$row['style']=="list" ||  trim(@$row['style'])=="") {
                                                  echo "checked";
                                              } ?> />
    										        <label for="list">List</label>
    									        </div>
                            </div>
                            <div class="col-md-4">
                              <div class="radio-custom radio-primary">
    														<input type="radio" name="style" id="grid" value="grid" onclick="changeStyle('grid')" <?php if (@$row['style']=="grid") {
                                                  echo "checked";
                                              } ?> />
    														<label for="grid">Grid</label>
    									        </div>
                            </div>
                            <div class="col-md-4">
    									        <div class="radio-custom">
    										        <input type="radio" name="style" id="slide" value="slide" onclick="changeStyle('slide')" <?php if (@$row['style']=="slide") {
                                                  echo "checked";
                                              } ?>/>
    										        <label for="slide">Slide</label>
    									        </div>
                            </div>
                          </div>

                          <div class="form-group">
    									      <label class="col-md-12">Author Font Color</label>
    									      <div class="col-md-12">
    										      <input autocomplete="off" name="name_color" id="name_color" value="<?php echo @$row['name_color'] ?>" type="text" data-plugin-colorpicker class="colorpicker-default form-control"/>
    									      </div>
    								      </div>

                          <div class="form-group">
    												<label class="col-md-12">Rating Color</label>
    												<div class="col-md-12">
    													<input autocomplete="off" name="rating_color" id="rating_color" value="<?php echo @$row['rating_color'] ?>" type="text" data-plugin-colorpicker class="colorpicker-default form-control"/>
    												</div>
    											</div>

                          <div class="form-group">
    												<label class="col-md-12">Date Font Color</label>
    												<div class="col-md-12">
    													<input autocomplete="off" name="date_color" id="date_color" value="<?php echo @$row['date_color'] ?>" type="text" data-plugin-colorpicker class="colorpicker-default form-control"/>
    												</div>
    											</div>

                          <div class="form-group">
    												<label class="col-md-12">Review Font Color</label>
    												<div class="col-md-12">
    													<input autocomplete="off" name="review_color" id="review_color" value="<?php echo @$row['review_color'] ?>" type="text" data-plugin-colorpicker class="colorpicker-default form-control"/>
    												</div>
    											</div>

                          <div class="form-group">
    												<label class="col-md-12">Widget Background Color</label>
    												<div class="col-md-12">
    													<input autocomplete="off" name="widget_bg_color" id="widget_bg_color" value="<?php echo @$row['widget_bg_color'] ?>" type="text" data-plugin-colorpicker class="colorpicker-default form-control"/>
    												</div>
    											</div>

                          <div class="form-group">
    												<label class="col-md-12">Widget Box Shadow</label>
    												<div class="col-md-12">
    													<input autocomplete="off" name="widget_box_shadow" id="widget_box_shadow" value="<?php echo @$row['widget_box_shadow'] ?>" type="text" data-plugin-colorpicker class="colorpicker-default form-control"/>
    												</div>
    											</div>

                          <div class="form-group">
                            <label class="col-md-9">Display Picture</label>
                            <div class="col-md-3">
                              <div class="checkbox-custom checkbox-default">
                                <input type="checkbox" id="display_dp" name="display_dp" onclick="showHideThings(this,'dp')" <?php if (@$row['display_dp']=="1") {
                                                  echo "checked='checked'";
                                              } ?> />
    	                          <label for=""></label>
    							            </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-md-9">Display Date</label>
                            <div class="col-md-3">
                              <div class="checkbox-custom checkbox-default">
                                <input type="checkbox" id="display_date" name="display_date" onclick="showHideThings(this,'date')" <?php if (@$row['display_date']=="1") {
                                                  echo "checked='checked'";
                                              } ?> />
    	                          <label for=""></label>
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-md-9">Display Rating</label>
                            <div class="col-md-3">
                              <div class="checkbox-custom checkbox-default">
                                <input type="checkbox" id="display_rating" name="display_rating" onclick="showHideThings(this,'rating')" <?php if (@$row['display_rating']=="1") {
                                                  echo "checked='checked'";
                                              } ?> />
    	                          <label for=""></label>
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-md-9">Display Review</label>
                            <div class="col-md-3">
                              <div class="checkbox-custom checkbox-default">
                                <input type="checkbox" id="display_review" name="display_review" onclick="showHideThings(this,'review')" <?php if (@$row['display_review']=="1") {
                                                  echo "checked='checked'";
                                              } ?> />
    										        <label for=""></label>
    							            </div>
                            </div>
                          </div>


                          <div class="form-group">
      										  <label class="col-md-12" for="inputDefault">Font Size:</label>
      										  <div class="col-md-12">
                              <select name="font_size" id="font_size" data-plugin-selectTwo class="form-control populate" onchange="applyFont(this)">
                                  <option value="14px">Default</option>
                                  <?php
                                  for ($i=8;$i<=26;$i++) {
                                      ?>
                                      <option value="<?php echo $i; ?>px" <?php if (@$row['font_size']==$i."px") {
                                          echo "selected";
                                      } ?>><?php echo $i; ?>px</option>
                                      <?php
                                  }
                                  ?>
                              </select>
      										  </div>
      									  </div>

                          <div class="form-group">
      										  <label class="col-md-12" for="inputDefault">Font Family:</label>
      										  <div class="col-md-12">
                              <select name="font_family" id="font_family" data-plugin-selectTwo class="form-control populate" onchange="applyFontFamily(this)">
                                  <option value="">Default</option>
                                  <?php
                                  foreach ($fonts as $font) {
                                      ?>
                                  <option value="<?php echo $font; ?>" <?php if (@$row['font_family']==$font) {
                                          echo "selected";
                                      } ?>><?php echo $font; ?></option>
                                  <?php
                                  }
                                  ?>
                              </select>
      										  </div>
      									  </div>
                          <div class="form-group">
      										  <label class="col-md-12" for="inputDefault">Reviews Ordered By:</label>
      										  <div class="col-md-12">
                              <select name="order_way" id="order_way" data-plugin-selectTwo class="form-control populate" onchange="">
                                  <option <?=   (((int)@$row['order_way']==0) ? "selected" : "" )?> value="0">Newest first</option>
                                  <option <?=   (((int)@$row['order_way']==1) ? "selected" : "") ?> value="1">Maximum rating first</option>
                                  <option <?=   (((int)@$row['order_way']==2) ? "selected" : "") ?> value="2">Random</option>
                                  <option <?=   (((int)@$row['order_way']==3) ? "selected" : "") ?> value="3">Text length, longest first</option>


                              </select>
      										  </div>
      									  </div>

                          <div class="form-group">
                            <label class="col-md-12" for="inputDefault"># of Reviews Per Page:</label>

                            <div class="col-md-12">
                              <div class="input-group">
                                <div class="input-group-addon bg-success"><i class="fa fa-minus"   aria-hidden="true"></i></div>
                                <input class="form-control" value="<?= $row['reviews_per_page'] ?>" max="999"  id ="reviews_per_page" name="reviews_per_page" type="text"/>
                                <div class="input-group-addon bg-danger"><i class="fa fa-plus" aria-hidden="true"></i></div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-md-12" >Carousel Change (in milliseconds):</label>
                            <div class="col-md-12">
                                <input class="form-control" value="<?= (($row['carousel_timeout']<=0)? 2000 :$row['carousel_timeout']) ?>" min="0"  name="carousel_timeout" type="number"/>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-md-12" >Max Character Count Per Review</label>
                            <div class="col-md-12">
                                <input class="form-control" value="<?= $row['review_text_size'] ?>" min="0"  name="review_text_size" type="number"/>
                            </div>
                          </div>

                           <div class="form-group">
                            <label class="col-md-12" ><b>NOTE:</b> <u>Save Your Campaign after Every Change</u></label>
                            <div class="col-md-12">
                               <input name="id" type="hidden" value="<?php echo @$_REQUEST['id'] ?>" />
                      <button class="btn btn-success" type="button" onclick="submitForm()"> Save Campaign</button>
                            </div>
                          </div>

                        </div>
                        <div class="tab-pane" id="embed_tab">
                          <div class="form-group embed-code">
                              <label>Embed Code</label>
                              <label class="tag-head unselectable" style="order: 1;">Place this to <?= htmlentities('<head>') ?> tag</label>
                              <label class="tag-body unselectable" style="order: 3;">Place this to <?= htmlentities('<body>') ?> tag</label>


                              <?php

                              $script= '<script type="text/javascript">'.PHP_EOL.
                                        '  var review_token = \''.$campID.'\';'.PHP_EOL.
                                        '  var review_target = \'nm-review-container\';'.PHP_EOL.
                                        '  var application_url = \''.$appURL.'\';'.PHP_EOL.
                                        '</script>'.PHP_EOL.
                                        '<script src="'.$appURL.'embed.js?v=6" type="text/javascript"></script>';
                              // new version of script

                              $script= '<script type="text/javascript">'.PHP_EOL.
                                        '  var reviewData = [];'.PHP_EOL.
                                        '  var application_url = \''.$appURL.'\';'.PHP_EOL.
                                        '  reviewData.push({'.PHP_EOL.
                                        '     \'container\': \'nm-review-container\','.PHP_EOL.
                                        '     \'token\': \''.$campID.'\''.PHP_EOL.
                                        '  });'.PHP_EOL.
                                        '</script>'.PHP_EOL.
                                        '<script src="'.$appURL.'embed.js?v=8" type="text/javascript"></script>'.PHP_EOL;







                              echo '<pre class="form-control code" style="order: 2;"  onclick="selectElement(this);">'.htmlentities($script).'</pre>';
                               ?>



                              <?php
                              $script= '<div id="nm-review-container"></div>';
                              echo '<pre class="form-control code" style="order: 4;"  onclick="selectElement(this);">'.htmlentities($script).'</pre>';
                               ?>

                              </div>

                            </div>

                            <div class="tab-pane" id="embed_if_tab">
                                  <div class="form-group">
                                    <label>iFrame Code</label>
                                  <?php
                                  $script= '<iframe src="'.$appURL.'reviews/'.$campID.'" width="960" style="border:none;" height="800"></iframe>';
                                  echo '<pre class="form-control code" onclick="selectElement(this);">'.htmlentities($script).'</pre>';
                                  ?>



                                  </div>




                            </div>
                      </div>
                      <!-- end of tab content -->
                    </div>
                      <!-- end of settings_nav -->
                  </div>


                  <div class="col-md-8">
                    <h3>Reviews Preview</h3>
                    <div class="border m-b-20"></div>
    		            <div id="reviews_loader"></div>
                      <div class="form-group smb-reviews-container" id="<?='smb_reviews'.$id?>" rating_color="<?=$row['rating_color']?>" reviews_per_page="<?=$row['reviews_per_page']?>" review_text_size="<?=$row['review_text_size']?>" >

                      </div>
                  </div>
                </div>
              </form>


              <!-- temlates section  -->

              <div id="list_structure<?=$id?>" style="display:none;">

                  <?= displayReview('', 'list', 0).
                  htmlentities(displayReview('__tp_name__', 'list', 1, "__action_url_", "__picture_url_", "__author_name_", "__rating_", "__date_", "__review_", "__type_","__verified_")).
                  displayReview('', 'list', -1) ?>

              </div>
              <div id="grid_structure<?=$id?>" style="display:none;">

                  <?= displayReview('', 'grid', 0).
                  htmlentities(displayReview('__tp_name__', 'grid', 1, "__action_url_", "__picture_url_", "__author_name_", "__rating_", "__date_", "__review_", "__type_","__verified_")).
                  displayReview('', 'grid', -1) ?>

              </div>

              <div id="slide_structure<?=$id?>" style="display:none;">

                  <?= displayReview('', 'slide', 0).
                      htmlentities(displayReview('__tp_name__', 'slide', 1, "__action_url_", "__picture_url_", "__author_name_", "__rating_", "__date_", "__review_", "__type_","__verified_")).
                      displayReview('', 'slide', -1) ?>

              </div>

              <div id="json_container_reviews<?=$id?>" style="display:none;"><?= htmlentities(json_encode($reviews_array)) ?></div>

              <!-- end temlates section  -->




          </section>
          <?php if ($row['id']==-1): ?>
            <div class="row">
              <pre>
               </pre>
            </div>

          <?php endif; ?>

        </div>
      </div>
    </section>
<!-- end of campaign section -->
  </div>
</section>

		<!-- Vendor -->
		<script src="/assets/vendor/jquery/jquery.js"></script>
		<script src="/assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="/assets/vendor/bootstrap/js/bootstrap.js"></script>
		<script src="/assets/vendor/nanoscroller/nanoscroller.js"></script>
		<script src="/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="/assets/vendor/magnific-popup/magnific-popup.js"></script>
		<script src="/assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>

		<!-- Specific Page Vendor -->
    <script src="/assets/vendor/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
		<script src="/assets/vendor/jquery-autosize/jquery.autosize.js"></script>
		<script src="/assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>

    <script src="/assets/vendor/ios7-switch/ios7-switch.js"></script>
		<script src="/assets/vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
    <script src="/assets/vendor/select2/select2.min.js"></script>
    <script src="/assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>

		<!-- Theme Base, Components and Settings -->
		<script src="/assets/javascripts/theme.js"></script>

		<!-- Theme Custom -->
		<script src="/assets/javascripts/theme.custom.js"></script>

		<!-- Theme Initialization Files -->
		<script src="/assets/javascripts/theme.init.js"></script>


<?php

if (@$settings['google_key']!="" && @$row['place_id']=="") {
    ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo @$settings['google_key']; ?>&libraries=places&callback=initMap"
async defer></script>
<?php
}
?>
        <!-- Rating js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>

<script async defer src="//assets.pinterest.com/js/pinit.js"></script>

<script>

function getReviews(obj){



$( "<?='#smb_reviews'.$id?>" ).prepend("<center class='m-t-50'><img src='assets/img/ajax-loader-black-bar.gif' width='75px'/></center>");
var r= $("#json_container_reviews<?=$id?>").text();

switch ($(obj).attr('id')) {
  case 'fb_page':
    var qry = "action=campaign_reviews&page_id="+$(obj).val()+"&limit="+$("#f_area input[name='fb_reviews_cnt']").val()+'&review_type=facebook';
    try {
        r= $.parseJSON(r);
        $.each(r,function(i,v){
          if (v.src == 'facebook') {
            delete r[i];
          }
        });
      }
      catch(e) {
        r={};
      }

    $("#json_container_reviews<?=$id?>").text('');
    $.post( "/action.php",qry, function( result ) {
      try {
        var data = $.parseJSON(result).data;
        data = $.parseJSON(data);
        r= $.extend({},r,data);
        $("#json_container_reviews<?=$id?>").text(JSON.stringify(r));
        $("#settings_nav").show();
      } catch(err){
        // console.log(err);
      }
      changeStyle($("input[name='style']:checked").val());

    });
  break;
  case 'g_area':
    var qry = "action=campaign_reviews&place_id="+$(obj).find('#place_id').val()+"&limit="+$(obj).find("input[name='google_reviews_cnt']").val()+'&review_type=google';

    try {
        r= $.parseJSON(r);
        $.each(r,function(i,v){
          if (v.src == 'google') {
            delete r[i];
          }
        });
      }
      catch(e) {
        r={};
      }

    $("#json_container_reviews<?=$id?>").text('');

    $.post( "/action.php",qry, function( result ) {

      try {
        var data = $.parseJSON(result).data;
        data = $.parseJSON(data);
        r= $.extend({},r,data);
        $("#json_container_reviews<?=$id?>").text(JSON.stringify(r));
        $("#settings_nav").show();
      } catch(err){
        // console.log(err);
      }
      changeStyle($("input[name='style']:checked").val());



    });

    break;
  case 'yelp_reviews':

    var qry = "action=campaign_reviews&business_id="+$("#yelp_business_id").val()+"&limit="+$("#y_area input[name='yelp_reviews_cnt']").val()+'&review_type=yelp';


    try {
        r= $.parseJSON(r);
        $.each(r,function(i,v){
          if (v.src == 'yelp') {
            delete r[i];
          }
        });
      }
      catch(e) {
        r={};
      }

    $("#json_container_reviews<?=$id?>").text('');

    $.post("/action.php",qry, function( result ) {


      try {
        var data = $.parseJSON(result).data;
        data = $.parseJSON(data);
        r= $.extend({},r,data);
      } catch(err){
        // console.log(err);
  }
      $("#settings_nav").show();
      $("#json_container_reviews<?=$id?>").text(JSON.stringify(r));
      changeStyle($("input[name='style']:checked").val());
    });
    break;
    case 'is_custom':
      var qry = "action=campaign_reviews&limit="+"99999"+'&review_type=custom&campaigns_id=<?=$id?>';

      try {
          r= $.parseJSON(r);
          $.each(r,function(i,v){


            if (v.src == 'custom') {
              delete r[i];
            }
          });
        }
        catch(e) {
          r={};
        }

      $("#json_container_reviews<?=$id?>").text('');

      $.post( "/action.php",qry, function( result ) {

        try {
          var data = $.parseJSON(result).data;
          data = $.parseJSON(data);
          r= $.extend({},r,data);

        } catch(err){
          // console.log(err);

        }
        $("#settings_nav").show();
        $("#json_container_reviews<?=$id?>").text(JSON.stringify(r));
        changeStyle($("input[name='style']:checked").val());

      });

    break;

}


}


function addRating(){
     $(".smb-reviews-container .src-type-google .user-rating, .smb-reviews-container .src-type-facebook .user-rating, .smb-reviews-container .src-type-custom .user-rating").each(function(){
        var score = $(this).html();
        var color = $("#rating_color").val();
        $(this).html("");
        $(this).rateYo({
            rating : score,
            starWidth: "15px",
            ratedFill: color,
            readOnly: true,
        })
    })
}

function applyFont(obj){
    $(".r_review").css("font-size",$(obj).val());
}

function applyFontFamily(obj){
    $(".r_review").css("font-family",$(obj).val());
}

function selectElement(element) {
    if (window.getSelection) {
        var sel = window.getSelection();
        sel.removeAllRanges();
        var range = document.createRange();
        range.selectNodeContents(element);
        sel.addRange(range);
    } else if (document.selection) {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(element);
        textRange.select();
    }
}

function socialShare(social)
{
    var share_url = '<?php echo urlencode($appURL."reviews.php?id=".$campID); ?>';
    if(social=="facebook"){
        var social_url = "https://www.facebook.com/sharer/sharer.php?u="+share_url;
    }else if(social=="twitter"){
        var social_url = "https://twitter.com/intent/tweet?url="+share_url;
    }else if(social=="linkedin"){
        var social_url = "http://www.linkedin.com/shareArticle?url="+share_url;
    }else if(social=="gplus"){
        var social_url = "https://plus.google.com/share?url="+share_url;
    }else if (social="pinterest") {
      PinUtils.pinOne({
      'url': '<?=$appURL."reviews.php?id=".$campID?>',
      'media': '<?= $appURL.'uploads/'.$row['meta_picture'] ?>',
      'description': '<?= $row['meta_description'] ?>'
      });
      return false;

    }
    window.fb_share_box = window.open(social_url,"share-box",'width=572,height=567');
    return false;
}
function submitForm(){
    document.forms['campaign_form'].submit();
}

function showHidePlatform(obj,platform){
    if(platform=="facebook"){
        if($(obj).is(":checked")){
            $("#f_area").slideDown();
        }else{
            $("#f_area").slideUp();
            $("#json_container_facebook").text("");
            $("#fb_page").val("");
        }
    }else if(platform=="google"){
        if($(obj).is(":checked")){
            $("#g_area").slideDown();
        }else{
            $("#g_area").slideUp();
            $("#json_container_google").text("");
            $("#google_business").val("");
        }
    }else if(platform=="yelp"){
        if($(obj).is(":checked")){
            $("#y_area").slideDown();
        }else{
            $("#y_area").slideUp();
            $("#json_container_yelp").text("");
            $("#yelp_business_id").val("");
        }
    }else if(platform=="custom"){
        if($(obj).is(":checked")){
            $("#custom_reviews_area").slideDown();
            getReviews(obj);
        }else{
            $("#custom_reviews_area").slideUp();
            $("#json_container_custom").text("");
        }
    }else if(platform=="social_share"){
        if($(obj).is(":checked")){
            $("#social_share_area").slideDown();
            $("#social_share_btns").slideDown();
        }else{
            $("#social_share_area").slideUp();
            $("#social_share_btns").slideUp();
        }
    }
    changeStyle($("input[name='style']:checked").val());
}

function showHideThings(obj,thing){
    if($(obj).is(":checked")){
        $(".r_"+thing).css("display","");
    }else{
        $(".r_"+thing).css("display","none");
    }

}

function changeMenu(bit){
    if(bit=="facebook"){
        $("#f_area").show();
        $("#g_area").hide();
        $("#social_type").val(1);
        $("#f_btn").removeClass("btn-default");
        $("#f_btn").addClass("btn-inverse");
        $("#g_btn").removeClass("btn-inverse");
        $("#g_btn").addClass("btn-default");

    }else{
        $("#f_area").hide();
        $("#g_area").show();
        $("#social_type").val(2);
        $("#g_btn").removeClass("btn-default");
        $("#g_btn").addClass("btn-inverse");
        $("#f_btn").removeClass("btn-inverse");
        $("#f_btn").addClass("btn-default");
    }
    //resetSettings();

}

function resetSettings(){
    $("<?='#smb_reviews'.$id?>").html("");
    $("#settings_nav").slideUp("slow");
    $("#fb_page").val("");
    $("#google_business").val("");
}


function changeStyle(style="list"){

  var template, reviews_div;
  //console.log('changeStyle='+style);

  $( "<?='#smb_reviews'.$id?>" ).html("");

  switch (style) {
    case 'list':
    reviews_div = $('#list_structure<?=$id?>').html();
    template = $(reviews_div).text();
    reviews_div = $(reviews_div).html("");
    break;
    case 'grid':
    reviews_div =$('#grid_structure<?=$id?>').html();
    template = $(reviews_div).text();
    reviews_div = $(reviews_div).html("");
    break;
    case 'slide':
    reviews_div =$('#slide_structure<?=$id?>').html();
    template = $(reviews_div).find('ul').text();
    reviews_div = $(reviews_div);
    reviews_div.find('ul').html('');
    break;
  }
//


$( "<?='#smb_reviews'.$id?>" ).html(reviews_div);
// console.log(template);

 var i=0;
 if ($("#json_container_reviews<?=$id?>").text().trim() !=='') {


 $.each($.parseJSON($("#json_container_reviews<?=$id?>").text()), function( key, value ) {

    // console.log(key);
    // console.log(value);

    if((($("#is_facebook").is(":checked")) && (value.src=='facebook'))
      || (($("#is_google").is(":checked")) && (value.src=='google'))
      || (($("#is_yelp").is(":checked")) && (value.src=='yelp'))
      || (($("#is_custom").is(":checked")) && (value.src=='custom'))
      )
    {
// console.log(value);
      var content;
      content=template;
      content = content.replace(/__tp_name__/g, value.src)
                       .replace(/__action_url_/g, value.action_url)
                       .replace(/__picture_url_/g, value.picture_url)
                       .replace(/__author_name_/g, value.author_name)
                       .replace(/__rating_/g, value.rating)
                       .replace(/__date_/g, value.date)
                       .replace(/__review_/g, value.review)
                       .replace(/__type_/g, value._type)
                      .replace(/__verified_/g, (value._verified ==true ? '<i class="fa fa-check-circle"></i>':''));


      if (i > 0) {
        content = content.replace(/class="active /g, 'class="');
      }

      $( "<?='#smb_reviews'.$id?>" ).find('ul').append(content);

      i++;
    }


  });
  addRating();
  slideShowFunction(3000);
  renderPagination(style);
  addReadmore();

}

updateDateFormat();
  return;

}




function initMap() {

    var map = new google.maps.Map(document.getElementById('map'), {
      center: {lat: -33.8688, lng: 151.2195},
      zoom: 13
    });

    var input = document.getElementById('google_business');
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);

    autocomplete.addListener('place_changed', function() {

      var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }

      $("#place_id").val(place.place_id);

      getReviews($('#g_area'));


    });
}

function ApplyStyle(){
    $(".r_name").css("color",$('#name_color').val());
    $(".r_date").css("color",$('#date_color').val());
    $(".r_review").css("color",$('#review_color').val());
    $(".user-rating").css("color",$('#rating_color').val());

    $(".thumbnail").css("background",$('#widget_bg_color').val());
    $(".carousel-inner .item").css("background",$('#widget_bg_color').val());
    $("#slide_structure .item").css("background",$('#widget_bg_color').val());

    $(".thumbnail").css("box-shadow","0 2px 5px "+$("#widget_box_shadow").val()+", 0 2px 10px rgba(0, 0, 0, 0)");

    $(".r_review").css("font-family",$("#font_family").val());
    $(".r_review").css("font-size",$("#font_size").val());

    if($("#display_dp").is(":checked")){
        $(".r_dp").css("display","");
    }else{
        $(".r_dp").css("display","none");
    }

    if($("#display_rating").is(":checked")){
        $(".r_rating").css("display","");
    }else{
        $(".r_rating").css("display","none");
    }

    if($("#display_date").is(":checked")){
        $(".r_date").css("display","");
    }else{
        $(".r_date").css("display","none");
    }

    if($("#display_review").is(":checked")){
        $(".r_review").css("display","");
    }else{
        $(".r_review").css("display","none");
    }


}

var slideShow ;

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

function setCustomShortioUrl() {
    console.log({
            'action': 'capture_reviews_update_short_io',
            'short_io_api_key': "<?=$row['short_io_api_key']?>",
            'short_io_domain': $('select[name="short_io_domain"] option:selected').val(),
            'short_io_custom_handle': $('#custom_link').val(),
            'original_url': '<?= Tools::siteURL() . '/reviews/'.$campID ?>',
            'id': '<?=$_REQUEST['id']?>'
        });
    $.ajax({
        type: 'post',
        url: "/action.php",
        data: {
            'action': 'capture_reviews_update_short_io',
            'short_io_api_key': "<?=$row['short_io_api_key']?>",
            'short_io_domain': $('select[name="short_io_domain"] option:selected').val(),
            'original_url': '<?= Tools::siteURL() . '/reviews/'.$campID ?>',
            'short_io_custom_handle': $('#custom_link').val(),
            'id': '<?=$_REQUEST['id']?>'
        },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            if(response.link_result) {
                $('#short_io_message').addClass("hidden");
                $('#short_io_domain_link').removeClass("hidden");
                $('#short_io_domain_link').attr("href", response.link_result);
                $('#short_io_domain_link').html(response.link_result);
                updateShortUrlInput();
            } else if (response.error) {
                $('#short-io-error').show();
                $('#short-io-error').html(response.error);
            }
        }
    });
    }

function setDefaultShortioUrl(shouldCallSetCustom) {
    $('#short-io-error').hide();
    console.log({
            'action': 'capture_reviews_set_short_io',
            'short_io_api_key': "<?=$row['short_io_api_key']?>",
            'short_io_domain': $('select[name="short_io_domain"] option:selected').val(),
            'original_url': '<?= Tools::siteURL() . '/reviews/'.$campID ?>',
            'id': '<?=$_REQUEST['id']?>'
        })
    $.ajax({
        type: 'post',
        url: "/action.php",
        data: {
            'action': 'capture_reviews_set_short_io',
            'short_io_api_key': "<?=$row['short_io_api_key']?>",
            'short_io_domain': $('select[name="short_io_domain"] option:selected').val(),
            'original_url': '<?= Tools::siteURL() . '/reviews/'.$campID ?>',
            'id': '<?=$_REQUEST['id']?>'
        },
        dataType: 'json',
        success: function (response) {
            console.log(response);
            if(response.link_result) {
                $('#short_io_message').addClass("hidden");
                $('#short_io_domain_link').removeClass("hidden");
                $('#short_io_domain_link').attr("href", response.link_result);
                $('#short_io_domain_link').html(response.link_result);
                if(shouldCallSetCustom) {
                    setCustomShortioUrl();
                } else {
                    updateShortUrlInput();
                }
            }
        }
    });
}

function updateShortUrlInput() {
    let handle = $('#short_io_domain_link').attr("href");



    if((typeof handle !== "undefined") && (handle != '')) {
    
        handle = handle.split('/');
        handle = handle[handle.length - 1];
        $('#custom_link').val(handle);
    }

}

$( document ).ready(function() {
    $('#short-io-error').hide();
    if($('#enable_short_io').is(':checked')) {
        $('#short-io-container').show();
        $('#input-group-addon-shortio').css('border-right', '0');
        $('#set_short_io').show();
    }else{
        $('#short-io-container').hide();
        $('#input-group-addon-shortio').css('border-right', '1px solid #ccc');
        $('#set_short_io').hide();
    }

    $('#enable_short_io').click(function (){
        if($('#enable_short_io').is(':checked')) {
            $('#short-io-container').show();
            $('#input-group-addon-shortio').css('border-right', '0');
            $('#set_short_io').show();
        }else{
            $('#short-io-container').hide();
            $('#input-group-addon-shortio').css('border-right', '1px solid #ccc');
            $('#set_short_io').hide();
        }
    });

    $('#set_short_io').on("click", function () {
        if($('#custom_link').val().trim() == '') {
            console.log("Generating handle");
            setDefaultShortioUrl(false);
        } else {
            console.log("Setting a custom handle");
            setDefaultShortioUrl(true);
        }
    });

    updateShortUrlInput();

    $(".select2-tags").select2({
              closeOnSelect : false,
              placeholder : "all custom reviews by default",
              allowHtml: false,
              allowClear: true,
               width: '100%' ,
               theme: "bootstrap"

          });


     <?php
     if ($row['is_google']==1 || $row['is_facebook']==1 || $row['is_yelp']==1 || $row['is_custom']==1) {
         ?>
        changeStyle($("input[name='style']:checked").val());
        ApplyStyle();
        <?php
     }
     ?>

     $("#campaign_form .input-group .input-group-addon").on('click', function(){

     var max_val = parseInt($(this).closest('.input-group').find('input').attr('max'));
     if ($(this).find('i').hasClass('fa-minus')) {



       $(this).closest('.input-group').find('input').val(function(i, oldval) {

         return ((oldval<1 ? oldval : (oldval>=max_val ? --max_val : --oldval)));
       })
     } else {


       if (isNaN(max_val)) {
         max_val=0;
       }


       $(this).closest('.input-group').find('input').val(function(i, oldval) {return ((oldval>=max_val ? max_val : ++oldval));})
     }


     });

    $(document).on("click","<?='#smb_reviews'.$id?> .pagination span:not(.active)", function() {
      changeActivePage($(this));
    });




   $(document).on("click slide","<?='#smb_reviews'.$id?> .smb_reviews_slide .nav-prev,<?='#smb_reviews'.$id?> .smb_reviews_slide .nav-next", function(e) {

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

    $(document).on("click","<?='#smb_reviews'.$id?> .r_review .read-more", function(e) {
      $(this).next().show(200);
      $(this).remove();
      });

      $('#widget_bg_color').colorpicker({format: 'hex'}).on('changeColor', function(ev){
         $(".thumbnail").css("background",ev.color.toHex());
         $(".carousel-inner .item").css("background",ev.color.toHex());
         $("#slide_structure .item").css("background",ev.color.toHex());
      });

      $('#widget_box_shadow').colorpicker({format: 'hex'}).on('changeColor', function(ev){
         $(".thumbnail").css("box-shadow","0 2px 5px "+ev.color.toHex()+", 0 2px 10px rgba(0, 0, 0, 0)");
         $(".carousel slide").css("box-shadow","0 2px 5px "+ev.color.toHex()+", 0 2px 10px rgba(0, 0, 0, 0)");

      });

      $('#name_color').colorpicker({format: 'hex'}).on('changeColor', function(ev){
         $(".r_name").css("color",ev.color.toHex());
      });

      $('#date_color').colorpicker({format: 'hex'}).on('changeColor', function(ev){
         $(".r_date").css("color",ev.color.toHex());
      });

      $('#review_color').colorpicker({format: 'hex'}).on('changeColor', function(ev){
         $(".r_review").css("color",ev.color.toHex());
      });

      $('#rating_color').colorpicker({format: 'hex'}).on('changeColor', function(ev){
               $(".smb-reviews-container .src-type-google .user-rating, .smb-reviews-container .src-type-facebook .user-rating, .smb-reviews-container .src-type-custom .user-rating").rateYo("option", "ratedFill", ev.color.toHex());
      });

  });


 function updateDateFormat () {
   $('.r_date').each(function(i){
     if ( $(this).text().trim()!='') {
       var dt =(new Date(Date.parse($(this).text().trim()))).toLocaleDateString(navigator.language,{year: 'numeric', month: 'long', day: 'numeric' })
       $(this).text(dt);
     }
    });
 }


</script>

</body>
</html>
