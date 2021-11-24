<?php

@header('Access-Control-Allow-Origin: *');

include_once("database.php");

$id=@base64_decode($_REQUEST['id']);

if (!isset($id) || base64_encode($id) != $_REQUEST['id']) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    die();
}

$db = new Database();

$row=$db->getCampaignByCampaignId($id);


if (count($row)==0) {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    die();
}

$settings = $db->getUser($row['user_id']);

$db->updateUserVisitsByUserId($row['user_id']);

if (trim(@$settings['app_id'])=="" || trim(@$settings['app_secret'])=="") {
    $access_token = "";
} else {
    $access_token = @$settings['access_token'];
}


if (isset($_SERVER["HTTPS"])) {
    $protocol = "https://";
} else {
    $protocol = "http://";
}
$currentPageUrl = $protocol.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

$og_image = getServerURL()."uploads/".$row['meta_picture'];


if (!isset($_REQUEST['src'])) {
    ?>
<html>

    <head>

        <!--Meta Information-->

        <title><?php echo $row['meta_title']; ?></title>

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
        <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.css" />
        <!-- jQuery  -->
        <script src="assets/vendor/jquery/jquery.js"></script>
        <script src="assets/vendor/bootstrap/js/bootstrap.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">


        <script>

        function addRating(){

            $("#reviews .user-rating").each(function(){
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

        </script>


    </head>


    <body >



<?php
}
?>


<style>
body{
    background:none;
}
.card-box{
    margin:0;
}
.jq-ry-container{
    padding:0 !important;
}
.carousel-caption{
    padding:30px 55px;
    position: unset;
    text-shadow:none;
    color:inherit;
}
.carousel-inner > .item{
    padding-top:10px;
}
.card-box{
    box-shadow:none;
}
.thumbnail{
    padding: 10px 10px 0px 10px;
}
.checkbox {
    padding: 0;
}
div.card-box.table-responsive {
  border: none;
}

.m-auto {
   margin:auto
}
#reviews .media-list .thumbnail {

  min-height: 100px;
}

#reviews img._picture_url_ {

  width: 50px;
  height: 50px;
}

.r_type {

  display: block;
  margin-left: auto;
  margin-right: auto;
  text-align: center;
  padding-top: 1rem;
}

.icon-google::before {
content: url("data:image/svg+xml,%3Csvg height='25' width='25' version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 512 512' style='enable-background:new 0 0 512 512;' xml:space='preserve'%3E%3Cpath style='fill:%23FBBB00;' d='M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256 c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456 C103.821,274.792,107.225,292.797,113.47,309.408z'/%3E%3Cpath style='fill:%23518EF8;' d='M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451 c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535 c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176L507.527,208.176z'/%3E%3Cpath style='fill:%2328B446;' d='M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512 c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771 c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z'/%3E%3Cpath style='fill:%23F14336;' d='M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012 c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0 C318.115,0,375.068,22.126,419.404,58.936z'/%3E%3C/svg%3E%0A");
display: inline-block;
width: 25px;
height:25px;

}

.icon-facebook::before{
content: url("data:image/svg+xml,%3Csvg height='25' width='25' version='1.1' id='Capa_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'%0AviewBox='0 0 112.196 112.196' style='enable-background:new 0 0 112.196 112.196;' xml:space='preserve'%3E%3Ccircle style='fill:%233B5998;' cx='56.098' cy='56.098' r='56.098'/%3E%3Cpath style='fill:%23FFFFFF;' d='M70.201,58.294h-10.01v36.672H45.025V58.294h-7.213V45.406h7.213v-8.34%0Ac0-5.964,2.833-15.303,15.301-15.303L71.56,21.81v12.51h-8.151c-1.337,0-3.217,0.668-3.217,3.513v7.585h11.334L70.201,58.294z'/%3E%3C/svg%3E");
display: inline-block;
width: 25px;
height:25px;

}

.icon-yelp::before{
content: url("data:image/svg+xml,%3Csvg enable-background='new 0 0 24 24' viewBox='0 0 24 24' height='25' width='25' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23f44336'%3E%3Cpath d='m12.062 17.662c.038-.934-1.266-1.395-1.829-.671-1.214 1.466-3.493 4.129-3.624 4.457-.347 1 1.28 1.638 2.312 2.024 1.121.42 1.919.591 2.392.51.342-.071.562-.248.67-.533.089-.245.08-5.568.079-5.787z'/%3E%3Cpath d='m11.522.642c-.08-.31-.295-.51-.647-.6-1.037-.272-4.966.838-5.698 1.624-.234.238-.318.515-.248.828l4.985 8c1.018 1.628 2.298 1.139 2.214-.681h-.001c-.066-1.199-.544-8.775-.605-9.171z'/%3E%3Cpath d='m9.413 15.237c.942-.29.872-1.671.07-1.995-2.139-.881-5.06-2.114-5.285-2.114-.876-.052-1.045 1.201-1.134 2.096-.08.81-.084 1.552-.014 2.229.066.714.221 1.443.933 1.485.309-.001 5.383-1.686 5.43-1.701z'/%3E%3Cpath d='m20.514 12.052c.403-.281.342-.7.347-.838-.108-1.024-1.83-3.61-2.692-4.029-.328-.152-.614-.143-.858.029-.323.219-3.24 4.444-3.413 4.619-.567.767.244 1.871 1.092 1.648l-.014.029c.341-.115 5.274-1.282 5.538-1.458z'/%3E%3Cpath d='m15.321 15.586c-.881-.315-1.712.81-1.2 1.581.145.247 2.809 4.705 3.043 4.871.225.191.507.219.83.095.905-.362 2.865-2.876 2.992-3.857.051-.348-.042-.619-.286-.814-.197-.176-5.379-1.876-5.379-1.876z'/%3E%3C/g%3E%3C/svg%3E");
display: inline-block;
width: 25px;
height:25px;

}

<?php if (@file_exists('uploads/campaign/'.$row['custom_icon'])): ?>

.icon-custom::before{
content: url("<?= getServerURL().'/uploads/campaign/'.$row['custom_icon'] ?>");
display: inline-block;
width: 25px;
height:25px;

}
<?php endif; ?>



span.yelp-rating {
  display: block;
  padding: .4rem 0;
}

span.yelp-rating.yelp1::before {
  content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_1.png');
  display:inline-block;
}

span.yelp-rating.yelp1_5::before {
  content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_1_half.png');
  display:inline-block;
}
span.yelp-rating.yelp2::before {
  content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_2.png');
  display:inline-block;
}

span.yelp-rating.yelp2_5::before {
  content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_2_half.png');
  display:inline-block;
}

span.yelp-rating.yelp3::before {
  content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_3.png');
  display:inline-block;
}

span.yelp-rating.yelp3_5::before {
  content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_3_half.png');
  display:inline-block;
}
span.yelp-rating.yelp4::before {
  content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_4.png');
  display:inline-block;
}

span.yelp-rating.yelp4_5::before {
  content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_4_half.png');
  display:inline-block;
}
span.yelp-rating.yelp5::before {
  content: url('<?=getServerURL()?>/assets/vendor/yelp_stars/web_and_ios/small/small_5.png');
  display:inline-block;
}

.r_dp{
    display:<?php if ($row['display_dp']=="0") {
    echo "none";
} else {
    echo "block";
} ?>;
}
.r_name{
    color:<?php echo $row['name_color']; ?>;
}
.r_rating{
    color:<?php echo $row['rating_color']; ?>;
    display:<?php if ($row['display_rating']=="0") {
    echo "none";
} ?>;
}
.r_date{
    color:<?php echo $row['date_color']; ?>;
    display:<?php if ($row['display_date']=="0") {
    echo "none";
} ?>;
}
.r_review{
    color:<?php echo $row['review_color']; ?>;
    font-size:<?php echo $row['font_size']; ?>;
    font-family:<?php echo $row['font_family']; ?>;
    display:<?php if ($row['display_review']=="0") {
    echo "none";
} ?>;
}
.thumbnail{
    background:<?php echo $row['widget_bg_color']; ?>;
    box-shadow:0 2px 5px <?php echo $row['widget_box_shadow']; ?>, 0 2px 10px rgba(0, 0, 0, 0);
}
.carousel-inner .item{
    background:<?php echo $row['widget_bg_color']; ?>;
}



</style>
                                <div class="card-box table-responsive">

                                    <?php

                                    $refresh_time=5;

                                    $gr=$db->getLastReviewsUpdateDatesByCampaingId($id);
                                    $start_date = new DateTime();

                                    $minimum_rate = (int)$row['minimum_rate'];

                                    if (@$row['is_facebook']==1) {
                                        $limit = (int)$row['fb_reviews_cnt'];
                                        $recommendation_type = $row['recommendation_type'];

                                        $url="https://graph.facebook.com/$row[fb_page]/ratings?access_token=$row[page_token]&limit=".'100'."&fields=created_time,has_rating,has_review,open_graph_story,rating,review_text,reviewer{name,id,picture},recommendation_type";

                                        $facebook_json = [];
                                        $since_start = $start_date->diff(new DateTime(@$gr['fb_last_update']));

                                        if ($since_start->i >= $refresh_time) {
                                            $g_json=post_fb($url, "get");
                                            $g=json_decode($g_json);

                                            if (@property_exists(@reset($g), 'message')) {
                                                // error_log(print_r((reset($g)),true));
                                            } else {
                                                $db->updateFacebookReviewCacheByCampaignId($id, $g_json);
                                            }
                                        }

                                        $gr=null;
                                        $gr=$db->getLastReviewsByCampaingId($id, 'fb_reviews');
                                        if (count($gr) > 0) {
                                            $g_json=$gr['fb_reviews'];
                                            $g=json_decode($g_json);
                                        }

                                        // foreach (@reset($g) as $v) {
                                        //     $limit--;
                                        //     if ($limit>=0) {
                                        //         $facebook_json[] = $v;
                                        //     }
                                        // }

                                        if ($g  = @reset($g)) {
                                            foreach ($g as $v) {
                                                if ($limit>0) {
                                                    if ($recommendation_type == 'any'
                                                || $recommendation_type == $v->recommendation_type
                                                ) {
                                                        if ((int)$v->has_rating ==1 && $v->rating >= $minimum_rate) {
                                                            $facebook_json[] = $v;
                                                            $limit--;
                                                        } elseif ((int)$v->has_rating ==0) {
                                                            $facebook_json[] = $v;
                                                            $limit--;
                                                        }
                                                    }
                                                }
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


                                        if ($since_start->i >= $refresh_time) {
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
                                        $url="https://api.yelp.com/v3/businesses/$row[yelp_business_id]/reviews";


                                        $yelp_json = [];

                                        $since_start = $start_date->diff(new DateTime(@$gr['yelp_last_update']));

                                        if ($since_start->i >= $refresh_time) {
                                            $y_json=post_fb($url, "get", "", @$settings['yelp_api_key']);
                                            $y= json_decode($y_json);

                                            if (@property_exists($y, 'reviews')) {
                                                $db->updateYelpReviewCacheByCampaignId($id, $y_json);
                                            }
                                        }


                                        $gr=null;
                                        $gr=$db->getLastReviewsByCampaingId($id, 'yelp_reviews');
                                        if (count($gr) > 0) {
                                            $y_json=$gr['yelp_reviews'];
                                            $y=json_decode($y_json);
                                        }



                                        if (@property_exists($y, 'reviews')) {
                                            foreach (@reset($y) as $k => $v) {
                                                if ($limit>0) {
                                                    if ($v->rating >= $minimum_rate) {
                                                        $yelp_json[$k] = $v;
                                                        $limit--;
                                                    }
                                                }
                                            }
                                        }

                                        $yelp_json = json_encode(['reviews'=>$yelp_json]);
                                        $yelp_data = json_decode($yelp_json, true);
                                    }

                                    if (@$row['is_custom']==1) {
                                        $url=getServerURL()."/review_api.php?uid=$row[user_id]";//.$_SESSION['user_id'];
                                        $custom_json=post_fb($url, "get");
                                        $custom_data = json_decode($custom_json, true);
                                    }
                                    ?>



                                    <input id="rating_color" type="hidden" value="<?php echo $row['rating_color']; ?>" />



                                    <div class="">
										<div class="col-md-12">
											<div class="form-group" id="reviews">
                                                <?php
                                                if (@$row['is_google']==1 || @$row['is_facebook']==1 || @$row['is_yelp']==1 || @$row['is_custom']==1) {
                                                    if (@$row['style']=="slide") {
                                                        ?>
                                                        <div id="carousel-example-captions" data-ride="carousel" class="carousel slide media-slide">
                                                            <div role="listbox" class="carousel-inner">
                                                              <?php
                                                    } elseif (@$row['style']=="grid") {
                                                        ?>
                                                            <ul class="media-grid">

                                                    <?php
                                                    } else {
                                                        ?>
                                                        <ul class="media-list">
                                                        <?php
                                                    }
                                                }

                                                $i=1;
                                                if (@$row['is_google']==1) {
                                                    if (isset($google_data['result']['reviews'])) {
                                                        $_type = '<i class="icon-google"></i>';

                                                        //$i=1;
                                                        foreach ($google_data['result']['reviews'] as $reviews) {
                                                            $action_url = $reviews['author_url'];
                                                            $picture_url = $reviews['profile_photo_url'];
                                                            $author_name = $reviews['author_name'];
                                                            $rating = $reviews['rating'];
                                                            $date = $reviews['relative_time_description'];
                                                            $review = $reviews['text'];

                                                            if (@$row['style']=="list") {
                                                                ?>
                                                                <li class="media thumbnail">
                                                                    <a class="pull-left" href="<?php echo $action_url; ?>" target="_blank">
                                                                        <img class="media-object img-circle _picture_url_ r_dp"
                                                                             src="<?php echo $picture_url; ?>" alt=""/>
                                                                                      <span class="r_type"><?=$_type?></span>
                                                                    </a>
                                                                    <div class="media-body">
                                                                        <h5 class="media-heading"><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a> <span class="user-rating r_rating"><?php echo $rating; ?></span></h5>
                                                                        <h6 class="r_date"><?php echo $date; ?></h6>
                                                                        <p class="r_review" align="justify"><?php echo $review; ?></p>
                                                                    </div>
                                                                </li>
                                                                <?php
                                                            } elseif (@$row['style']=="grid") {
                                                                ?>
                                                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 ">
                                                                    <div class="thumbnail">
                                                                        <img src="<?php echo $picture_url; ?>" class="img-responsive img-circle _picture_url_ r_dp" alt=""/>
                                                                        <div class="caption" align="center">
                                                                            <span class="r_type"><?=$_type?></span>
                                                                            <h3><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a> <span class="user-rating r_rating"><?php echo $rating; ?></span></h3>
                                                                            <h6 class="r_date"><?php echo $date; ?></h6>
                                                                            <p class="r_review" align="justify"><?php echo $review; ?></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            } elseif (@$row['style']=="slide") {
                                                                ?>
                                                                <div class="item <?php if ($i==1) {
                                                                    echo "active";
                                                                } ?>" align="center">
                            										<div class="row">
                                                                        <div class="col-md-12">
                                                                            <img src="<?php echo $picture_url; ?>" class="img-responsive img-circle _picture_url_ r_dp" alt=""/>
                                                                                     <span class="r_type"><?=$_type?></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                    										<div class="carousel-caption" align="center">
                                    											<h3 class="font-600 r_name" align="center"><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a> <span class="user-rating r_rating m-auto"><?php echo $rating; ?></span></h3>
                                    											<h6 class="r_date"><?php echo $date; ?></h6>
                                                                                <p class="r_review" align="center"><?php echo $review; ?></p>
                                    										</div>
                                                                        </div>
                                                                    </div>
                            									</div>
                                                                <?php
                                                            }
                                                            $i++;
                                                        }
                                                    }
                                                }

                                                if (@$row['is_facebook']==1) {
                                                    if (isset($facebook_data['data'])) {
                                                        //$i=1;

                                                        $_type = '<i class="icon-facebook"></i>';

                                                        foreach ($facebook_data['data'] as $reviews) {
                                                            $action_url = 'https://facebook.com/'.$reviews['open_graph_story']['id'];
                                                            // $picture_url = "https://graph.facebook.com/".$reviews['profile_photo_url']."/picture?type=large";

                                                            $picture_url = @$reviews['reviewer']['picture']['data']['url'];

                                                            $author_name = @$reviews['reviewer']['name'];
                                                            $rating = isset($reviews['rating']) ? $reviews['rating'] : 0;
                                                            $date = $reviews['created_time'];
                                                            $review = isset($reviews['review_text']) ? $reviews['review_text'] : "";

                                                            if (@$row['style']=="list") {
                                                                ?>
                                                                <li class="media thumbnail">
                                                                    <a class="pull-left" href="<?php echo $action_url; ?>" target="_blank">
                                                                        <img class="media-object img-circle _picture_url_ r_dp"
                                                                             src="<?php echo $picture_url; ?>" alt=""/>
                                                                        <span class="r_type"><?=$_type?></span>
                                                                    </a>
                                                                    <div class="media-body">
                                                                        <h5 class="media-heading"><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a> <span class="user-rating r_rating"><?php echo $rating; ?></span></h5>
                                                                        <h6 class="r_date"><?php echo $date; ?></h6>
                                                                        <p class="r_review" align="justify"><?php echo $review; ?></p>
                                                                    </div>
                                                                </li>
                                                                <?php
                                                            } elseif (@$row['style']=="grid") {
                                                                ?>
                                                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 ">
                                                                    <div class="thumbnail">
                                                                        <img src="<?php echo $picture_url; ?>" class="img-responsive img-circle _picture_url_ r_dp" alt=""/>
                                                                        <div class="caption" align="center">
                                                                            <span class="r_type"><?=$_type?></span>
                                                                            <h3><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a> <span class="user-rating r_rating"><?php echo $rating; ?></span></h3>
                                                                            <h6 class="r_date"><?php echo $date; ?></h6>
                                                                            <p class="r_review" align="justify"><?php echo $review; ?></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            } elseif (@$row['style']=="slide") {
                                                                ?>
                                                                <div class="item <?php if ($i==1) {
                                                                    echo "active";
                                                                } ?>" align="center">
                            										<div class="row">
                                                                        <div class="col-md-12">
                                                                            <img src="<?php echo $picture_url; ?>" class="img-responsive img-circle _picture_url_ r_dp" alt=""/>
                                                                            <span class="r_type"><?=$_type?></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                    										<div class="carousel-caption" align="center">
                                    											<h3 class="font-600 r_name" align="center"><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a> <span class="user-rating m-auto r_rating"><?php echo $rating; ?></span></h3>
                                    											<h6 class="r_date"><?php echo $date; ?></h6>
                                                                                <p class="r_review" align="center"><?php echo $review; ?></p>
                                    										</div>
                                                                        </div>
                                                                    </div>
                            									</div>
                                                                <?php
                                                            }
                                                            $i++;
                                                        }
                                                    }
                                                }

                                                if (@$row['is_custom']==1) {
                                                    if (isset($custom_data['data'])) {
                                                        //$i=1;
                                                        $_type = '<i class="icon-custom"></i>';
                                                        foreach ($custom_data['data'] as $reviews) {
                                                            $action_url = "#";
                                                            $picture_url = $reviews['photo'];
                                                            $author_name = $reviews['name'];
                                                            $rating = $reviews['rating'];
                                                            $date = $reviews['date'];
                                                            $review = $reviews['review'];

                                                            if (@$row['style']=="list") {
                                                                ?>
                                                                <li class="media thumbnail">
                                                                    <a class="pull-left" href="<?php echo $action_url; ?>" target="_blank">
                                                                        <img class="media-object img-circle _picture_url_ r_dp"
                                                                             src="<?php echo $picture_url; ?>" alt=""/>
                                                                        <span class="r_type"><?=$_type?></span>
                                                                    </a>
                                                                    <div class="media-body">
                                                                        <h5 class="media-heading"><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a> <span class="user-rating r_rating"><?php echo $rating; ?></span></h5>
                                                                        <h6 class="r_date"><?php echo $date; ?></h6>
                                                                        <p class="r_review" align="justify"><?php echo $review; ?></p>
                                                                    </div>
                                                                </li>
                                                                <?php
                                                            } elseif (@$row['style']=="grid") {
                                                                ?>
                                                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 ">
                                                                    <div class="thumbnail">
                                                                        <img src="<?php echo $picture_url; ?>" class="img-circle _picture_url_ r_dp" alt=""/>
                                                                        <div class="caption" align="center">
                                                                            <span class="r_type"><?=$_type?></span>
                                                                            <h3><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a> <span class="user-rating r_rating"><?php echo $rating; ?></span></h3>
                                                                            <h6 class="r_date"><?php echo $date; ?></h6>
                                                                            <p class="r_review" align="justify"><?php echo $review; ?></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            } elseif (@$row['style']=="slide") {
                                                                ?>
                                                                <div class="item <?php if ($i==1) {
                                                                    echo "active";
                                                                } ?>" align="center">
                            										<div class="row">
                                                                        <div class="col-md-12">
                                                                            <img src="<?php echo $picture_url; ?>" class="img-circle _picture_url_ r_dp" alt=""/>
                                                                            <span class="r_type"><?=$_type?></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                    										<div class="carousel-caption" align="center">
                                    											<h3 class="font-600 r_name" align="center"><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a> <span class="user-rating m-auto r_rating"><?php echo $rating; ?></span></h3>
                                    											<h6 class="r_date"><?php echo $date; ?></h6>
                                                                                <p class="r_review" align="center"><?php echo $review; ?></p>
                                    										</div>
                                                                        </div>
                                                                    </div>
                            									</div>
                                                                <?php
                                                            }
                                                            $i++;
                                                        }
                                                    }
                                                }

                                                if (@$row['is_yelp']==1) {
                                                    $_type = '<i class="icon-yelp"></i>';
                                                    if (isset($yelp_data['reviews'])) {
                                                        foreach ($yelp_data['reviews'] as $reviews) {
                                                            $action_url = $reviews['url'];
                                                            $picture_url = $reviews['user']['image_url'];
                                                            $author_name = $reviews['user']['name'];
                                                            $rating = $reviews['rating'];
                                                            $date = $reviews['time_created'];
                                                            $review = $reviews['text'];

                                                            if (@$row['style']=="list") {
                                                                ?>
                                                                <li class="media thumbnail">
                                                                    <a class="pull-left" href="<?php echo $action_url; ?>" target="_blank">
                                                                        <img class="media-object img-circle  r_dp" style="width:50px; height:50px;"
                                                                             src="<?php echo $picture_url; ?>" alt=""/>
                                                                        <span class="r_type"><?=$_type?></span>
                                                                    </a>
                                                                    <div class="media-body">
                                                                        <h5 class="media-heading"><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a><span class="yelp-rating yelp<?= str_replace('.', '_', $rating) ?>"></span></h5>
                                                                        <h6 class="r_date"><?php echo $date; ?></h6>
                                                                        <p class="r_review" align="justify"><?php echo $review; ?></p>
                                                                    </div>
                                                                </li>
                                                                <?php
                                                            } elseif (@$row['style']=="grid") {
                                                                ?>
                                                                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 ">
                                                                    <div class="thumbnail">
                                                                        <img src="<?php echo $picture_url; ?>" class="img-circle _picture_url_ r_dp" alt=""/>
                                                                        <div class="caption" align="center">
                                                                            <span class="r_type"><?=$_type?></span>
                                                                            <h3><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a><span class="yelp-rating yelp<?= str_replace('.', '_', $rating) ?>"></span></h3>
                                                                            <h6 class="r_date"><?php echo $date; ?></h6>
                                                                            <p class="r_review" align="justify"><?php echo $review; ?></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            } elseif (@$row['style']=="slide") {
                                                                ?>
                                                                <div class="item <?php if ($i==1) {
                                                                    echo "active";
                                                                } ?>" align="center">
                            										<div class="row">
                                                                        <div class="col-md-12">
                                                                            <img src="<?php echo $picture_url; ?>" class="img-circle _picture_url_ r_dp" alt=""/>
                                                                            <span class="r_type"><?=$_type?></span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                    										<div class="carousel-caption" align="center">
                                    											<h3 class="font-600 r_name" align="center"><a href="<?php echo $action_url; ?>" target="_blank" class="r_name"><?php echo $author_name; ?></a><span class="yelp-rating yelp<?= str_replace('.', '_', $rating) ?>"></span></h3>
                                    											<h6 class="r_date"><?php echo $date; ?></h6>
                                                                                <p class="r_review" align="center"><?php echo $review; ?></p>
                                    										</div>
                                                                        </div>
                                                                    </div>
                            									</div>
                                                                <?php
                                                            }
                                                            $i++;
                                                        }
                                                    }
                                                }



                                                if (@$row['is_google']==1 || @$row['is_facebook']==1 || @$row['is_yelp']==1 || @$row['is_custom']==1) {
                                                    if (@$row['style']=="slide") {
                                                        ?>
                                                            </div>
                                                            <a href="#carousel-example-captions" role="button" data-slide="prev" class="left carousel-control">
                                                                <span aria-hidden="true" class="fa fa-angle-left"></span>
                                                                <span class="sr-only">Previous</span>
                                                            </a>
                                                            <a href="#carousel-example-captions" role="button" data-slide="next" class="right carousel-control">
                                                                <span aria-hidden="true" class="fa fa-angle-right"></span>
                                                                <span class="sr-only">Next</span>
                                                            </a>
                                                        </div>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        </ul>
                                                        <?php
                                                    }

                                                    if (!isset($_REQUEST['src'])) {
                                                        ?>
                                                        <script>addRating()</script>
                                                    <?php
                                                    }
                                                }



                                                ?>


                                            </div>
										</div>
									</div>

                                </div>
<?php
if (!isset($_REQUEST['src'])) {
                                                    ?>
        </body>
    </html>
<?php
                                                }
?>

<?php
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

    return $applicationPath;
}
?>
