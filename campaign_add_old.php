<?php
ob_start();
include_once("header.php");

include_once("sidebar.php");

$fonts = ["Arial","Times","Courier","Verdana","Georgia","Palatino","Comic Sans MS","Trebuchet MS","Arial Black","Impact"];
$facebook_json = "";
$google_json = "";
$custom_json = "";

$settings = $current_user;

if (trim(@$settings['app_id'])=="" || trim(@$settings['app_secret'])=="") {
$access_token = "";
} else {
  $access_token = @$settings['access_token'];

}

$yelp_api_key = @$settings['yelp_api_key'];

$row = $db->getCampaignByUserIdCampaignId($_SESSION['user_id'],$_REQUEST['id']);
if (count($row) == 0) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: /campaign.php");
    exit();
}


$refresh_time=0;

$gr=$db->getLastReviewsUpdateDatesByCampaingId($_REQUEST['id']);
$start_date = new DateTime();
$minimum_rate = (int)$row['minimum_rate'];


if (@$row['is_facebook']==1) {
    $limit = (int)$row['fb_reviews_cnt'];
    $recommendation_type = $row['recommendation_type'];

    $url="https://graph.facebook.com/$row[fb_page]/ratings?access_token=$row[page_token]".""."&limit=".'100'."&fields=created_time,has_rating,has_review,open_graph_story,rating,review_text,reviewer{name,id,picture},recommendation_type";

;    $facebook_json = [];

    $since_start = $start_date->diff(new DateTime(@$gr['fb_last_update']));

     if ($since_start->i >= $refresh_time)
     {

          $g_json=post_fb($url, "get");
          $g=json_decode($g_json);


          if (@property_exists(@reset($g), 'message')) {
              // error_log(print_r((reset($g)),true));
            $db->updateFacebookReviewCacheByCampaignId($_REQUEST['id'],'');
            }
         else {
             $db->updateFacebookReviewCacheByCampaignId($_REQUEST['id'],$g_json);
         }
     }

     $gr=null;
     $gr=$db->getLastReviewsByCampaingId($_REQUEST['id'],'fb_reviews');
     if (count($gr) > 0) {
       $g_json=$gr['fb_reviews'];
       $g=json_decode($g_json);


if ($g  = @reset($g)) {


    foreach ($g as $v) {


        if ($limit>0) {

          if ($recommendation_type == 'any'
              || $recommendation_type == $v->recommendation_type
              )
          {

            if((int)$v->has_rating ==1 && $v->rating >= $minimum_rate ){
              $facebook_json[] = $v;
              $limit--;
            }
            elseif ((int)$v->has_rating ==0) {
              $facebook_json[] = $v;
              $limit--;
            }

          }

        }
    }
  }
  }
    $facebook_json = json_encode(['data'=>$facebook_json]);
}

if (@$row['is_google']==1) {
    $url="https://maps.googleapis.com/maps/api/place/details/json?placeid=$row[place_id]&fields=name,rating,formatted_phone_number,reviews&key=".@$settings['google_key'];



    $limit = (int)$row['google_reviews_cnt'];
error_log($url);
    $google_json = null;
    $since_start = $start_date->diff(new DateTime(@$gr['google_last_update']));


     if ($since_start->i >= $refresh_time)
    {

        $g_json=post_fb($url, "get");
        $g=json_decode($g_json);
        $google_api_call_errors = '';
        if (property_exists($g, 'error_message')) {
          $google_api_call_errors = '<i class="fa fa-warning"></i> '.$g->error_message;
           $db->updateGoogleReviewCacheByCampaignId($_REQUEST['id'],'');
        } else {

            $db->updateGoogleReviewCacheByCampaignId($_REQUEST['id'],$g_json);

        }
    }




        $gr=null;
        $gr=$db->getLastReviewsByCampaingId($_REQUEST['id'],'google_reviews');
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

                        if($r->rating >= $minimum_rate ){
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

}

if (@$row['is_yelp']==1) {
    $limit = (int)$row['yelp_reviews_cnt'];
    $url="https://api.yelp.com/v3/businesses/$row[yelp_business_id]/reviews";



    $yelp_json = [];

    $since_start = $start_date->diff(new DateTime(@$gr['yelp_last_update']));

     if ($since_start->i >= $refresh_time)
    {

         $y_json=post_fb($url, "get", "", @$settings['yelp_api_key']);


         $y= json_decode($y_json);

         //error_log(print_r($y,true));

         if (@property_exists($y,'reviews'))
         {

           $db->updateYelpReviewCacheByCampaignId($_REQUEST['id'],$y_json);
         } else {

           $db->updateYelpReviewCacheByCampaignId($_REQUEST['id'],'');

         }



    }


    $gr=null;
    $gr=$db->getLastReviewsByCampaingId($_REQUEST['id'],'yelp_reviews');
    if (count($gr) > 0) {
      $y_json=$gr['yelp_reviews'];
      $y=json_decode($y_json);
    }


    if (@property_exists($y,'reviews'))
    {

      foreach (@reset($y) as $k => $v) {
          if ($limit>0) {
            if($v->rating >= $minimum_rate ){
              $yelp_json[$k] = $v;
              $limit--;
            }
          }
      }
    }


    $yelp_json = json_encode(['reviews'=>$yelp_json]);
}

if (@$row['is_custom']==1) {
    $url=getServerURL()."review_api.php?uid=".$_SESSION['user_id'];
    $custom_json=post_fb($url, "get");
}




?>
<script>
function getReviews(obj){
    $( "#reviews" ).prepend("<center class='m-t-50'><img src='assets/img/ajax-loader-black-bar.gif' width='75px'/></center>");
    var str = $(obj).val()
    var qry = "action=page_reviews&page_id="+str;
    $.post( "action.php",qry, function( data ) {

      $("#json_container_facebook").text(data);
      changeStyle($("input[name='style']:checked").val());
    });
}

function getYelpReviews(){
    $( "#reviews" ).prepend("<center class='m-t-50'><img src='assets/img/ajax-loader-black-bar.gif' width='75px'/></center>");
    var qry = "action=yelp_reviews&business_id="+$("#yelp_business_id").val();
    $.post( "action.php",qry, function( data ) {

      $("#json_container_yelp").text(data);
      changeStyle($("input[name='style']:checked").val());
    });
}

function getcustomReviews(){
    $( "#reviews_loader" ).html("<center class='m-t-50'><img src='assets/img/ajax-loader-black-bar.gif' width='75px'/></center>");
    var qry = "uid=<?php echo $_SESSION['user_id']; ?>";
    $.post( "review_api.php",qry, function( data ) {
      $( "#reviews_loader" ).html("");
      $("#json_container_custom").text(data);
      changeStyle($("input[name='style']:checked").val());
    });
}

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

</script>

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

.thumbnail{
    padding: 10px 10px 0px 10px;
}

.unselectable {
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.carousel-control span {
    position: absolute;
    top: 50%;
    z-index: 5;
    display: inline-block;
    font-size: 30px;
}

.img-responsive{
    display: block;
    max-width: 100%;
    height: auto;
}
.embed-code {
  display: flex;
  flex-direction: column;
}
.tag-head, .tag-body {
display: block;


}

#reviews .media-list .thumbnail {

  min-height: 100px;
}

#reviews  img._picture_url_{

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


<?php if (trim(@$row['custom_icon'])!='' && file_exists('uploads/campaign/'.$row['custom_icon'])): ?>

.icon-custom::before{
content: url("<?= 'uploads/campaign/'.$row['custom_icon'] ?>");
display: inline-block;
width: 25px;
height:25px;

}
<?php endif; ?>



#campaign_form .input-group .input-group-addon i {
font-weight: 100;

}
#campaign_form .input-group .form-control{

min-width:3rem;
padding: 0;
text-align: center;


}




span.yelp-rating {
  display: block;
  padding: .4rem 0;
}

span.yelp-rating.yelp1::before {
  content: url('/assets/vendor/yelp_stars/web_and_ios/small/small_1.png');
  display:inline-block;
}

span.yelp-rating.yelp1_5::before {
  content: url('/assets/vendor/yelp_stars/web_and_ios/small/small_1_half.png');
  display:inline-block;
}
span.yelp-rating.yelp2::before {
  content: url('/assets/vendor/yelp_stars/web_and_ios/small/small_2.png');
  display:inline-block;
}

span.yelp-rating.yelp2_5::before {
  content: url('/assets/vendor/yelp_stars/web_and_ios/small/small_2_half.png');
  display:inline-block;
}

span.yelp-rating.yelp3::before {
  content: url('/assets/vendor/yelp_stars/web_and_ios/small/small_3.png');
  display:inline-block;
}

span.yelp-rating.yelp3_5::before {
  content: url('/assets/vendor/yelp_stars/web_and_ios/small/small_3_half.png');
  display:inline-block;
}
span.yelp-rating.yelp4::before {
  content: url('/assets/vendor/yelp_stars/web_and_ios/small/small_4.png');
  display:inline-block;
}

span.yelp-rating.yelp4_5::before {
  content: url('/assets/vendor/yelp_stars/web_and_ios/small/small_4_half.png');
  display:inline-block;
}
span.yelp-rating.yelp5::before {
  content: url('/assets/vendor/yelp_stars/web_and_ios/small/small_5.png');
  display:inline-block;
}



</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
<link rel="stylesheet" href="assets/vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.css" />
<link rel="stylesheet" href="assets/vendor/select2/select2.css" />

<section role="main" class="content-body">
	<header class="page-header">
		<h2>Campaign</h2>

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
                                                  Campaign
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
          <form action="action.php?action=campaigns" id="campaign_form" method="post" enctype="multipart/form-data" class="form-horizontal form-bordered">
                  <div class="form-group">
										<label class="col-md-3 control-label" for="inputDefault">Campain Name:</label>
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
                          <br /><span class="text-danger"><i class="fa fa-warning"></i> Facebook App credentials are missing! Please add <a href="settings.php">here</a> to get Facebook page reviews</span>
                        <?php endif; ?>
										</div>
									</div>

                  <div class="form-group" id="f_area" style="display: <?php if (@$row['is_facebook']=="0" || @$row['is_facebook']=="") {
                echo "none";
            } ?>;">
									  <div class="col-md-3"> </div>
										<div class="col-md-6 col-xs-9">

                                            <?php

                                            if (isset($_REQUEST['log']) && isset($_REQUEST['limit']) && $_REQUEST['limit']!="") {
                                                $limit = $_REQUEST['limit'];
                                            }

                                            $url="https://graph.facebook.com/me/accounts?access_token=$access_token";

//error_log($url);
                                            $json=post_fb($url, "get");

                                            $pages=json_decode($json, true);

                                            $l=3;
                                            while (isset($pages['error']) &&  $l>0) {
                                              error_log('error='.$url);
                                              $url="https://graph.facebook.com/me/accounts?access_token=".$access_token."&limit=".$l;


                                              $l--;

                                              $json=post_fb($url, "get");

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
                          <?php
                          if (isset($pages['data']) && count($pages['data'])>0) {
                              foreach ($pages['data'] as $page) {
                                  //  if(!in_array($page['id'],$fb_pages))
                                  {
                                  ?>
                                  <option <?php if (@$row['fb_page'] == $page['id']) {
                                      echo "selected='selected'";
                                  }?> value="<?php echo $page['id'];?>|<?php echo $page['name'];?>|<?php echo $page['access_token'];?>"><?php echo $page['name'];?> (<?php echo $page['id'];?>)</option>
                                  <?php
                                  }
                              }
                          }
                          ?>

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
                      <label for="is_google"> Connent your Google Places / Business </label>
                      <?php
                      if (trim(@$settings['google_key'])=="") {
                          ?>
                      <br /><span class="text-danger"><i class="fa fa-warning"></i> Google project key missing! Please add <a href="google_settings.php">here</a> to get Google place reviews</span>
                      <?php
                      }
                      ?>
										</div>


									</div>

                  <div class="form-group" id="g_area" style="display: <?php if (@$row['is_google']=="0" || @$row['is_google']=="") {
                          echo "none";
                      } ?>;">
                      <div class="col-md-3"> </div>
                      <div class="col-md-6 col-xs-9">

                          <input class="form-control" name="google_business" autocomplete="off" id="google_business" value="<?php echo @$row['google_business'] ?>" type="text" placeholder="Enter Business / Place" />

                          <span>You can find your place id via <a target="_blank" href="https://developers-dot-devsite-v2-prod.appspot.com/maps/documentation/javascript/examples/places-placeid-finder"> google place id fider</a></span>
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
                                            <br /><span class="text-danger"><i class="fa fa-warning"></i> Yelp key missing! Please add api key <a href="yelp_settings.php">here</a> to get Yelp reviews</span>
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
                                          <button class="btn btn-default" type="button" onclick="getYelpReviews()"><i class="fa fa-repeat"></i></button>
                    </div>

                    <div class="col-md-1 col-sm-2 col-xs-3">
                      <div class="input-group">
                        <div class="input-group-addon bg-success"><i class="fa fa-minus"   aria-hidden="true"></i></div>
                        <input class="form-control" value="<?= $row['yelp_reviews_cnt'] ?>" max="<?= $current_user['yelp_reviews_cnt'] ?>" name="yelp_reviews_cnt" type="text"/>
                        <div class="input-group-addon bg-danger"><i class="fa fa-plus" aria-hidden="true"></i></div>
                      </div>
                    </div>
                    <div class="clearfix"></div>
									</div>

                  <div class="form-group ">
										<label class="col-md-3 control-label" for="inputDefault">Custom:</label>
										<div class="col-md-4 ">
                                          <input class=" " id="is_custom" value="1" name="is_custom" type="checkbox" onclick="showHidePlatform(this,'custom')" <?php if (@$row['is_custom']=="1") {
                                                echo "checked";
                                            } ?> />
                                          <label class=" " for="is_custom"> Your Custom Reviews</label>

										</div>

                    <div class="col-md-4 ">
                      <?php if (trim($row['custom_icon'])!=''): ?>
                        <img src="uploads/campaign/<?= @$row['custom_icon'] ?>" alt="">
                      <?php endif; ?>

                        <label class="" for="custom_icon"> Your Custom icon</label>
                        <input type="file" class="" name="custom_icon" id="custom_icon">
                        <input type="hidden" name="hidden_custom_icon"  value="<?= @$row['custom_icon'] ?>">

										</div>
									</div>



                  <div class="form-group">
                    <label class="col-md-3 control-label" >Miminum rate:</label>
                    <div class="col-md-1 col-sm-2 col-xs-3">
                      <div class="input-group">
                        <div class="input-group-addon bg-success"><i class="fa fa-minus"   aria-hidden="true"></i></div>
                        <input class="form-control" value="<?= $row['minimum_rate'] ?>" max="5" name="minimum_rate" type="text"/>
                        <div class="input-group-addon bg-danger"><i class="fa fa-plus" aria-hidden="true"></i></div>
                      </div>
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
                                                    <img style=" width:100px;" src="uploads/<?php echo $row['meta_picture']; ?>" />
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
                    <div class="col-md-1"></div>
                    <div class="col-md-11">
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
                        <div id="settings_nav" style="display:<?php if (@$row['is_facebook']=="1" || @$row['is_google']=="1") {
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
                                                <span class="hidden-xs">Embed IFrame</span>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                      <div class="tab-pane active" id="settings_tab">
                                        <div class="form-group">
                                          <label class="col-md-12">Widget Style</label>
                                          <div class="col-md-4">
                                            <div class="radio-custom radio-success">
        															        <input type="radio" name="style" id="list" value="list" onclick="changeStyle('list')" <?php if (@$row['style']=="list" ||  trim(@$row['style'])=="" ) {
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



                                      </div>
                                      <div class="tab-pane" id="embed_tab">
                                        <div class="form-group embed-code">
                                            <label>Embed Code</label>
                                            <label class="tag-head unselectable" style="order: 1;">Place this to <?= htmlentities('<head>') ?> tag</label>
                                            <label class="tag-body unselectable" style="order: 3;">Place this to <?= htmlentities('<body>') ?> tag</label>


                                            <?php
                                            $appURL = getServerURL();
                                            $campID = base64_encode($_REQUEST['id']);

                                            $script= '<script type="text/javascript">'.PHP_EOL.
                                                      '  var review_token = \''.$campID.'\';'.PHP_EOL.
                                                      '  var review_target = \'nm-review-container\';'.PHP_EOL.
                                                      '  var application_url = \''.$appURL.'\';'.PHP_EOL.
                                                      '</script>'.PHP_EOL.
                                                      '<script src="'.$appURL.'embed.js?v=5" type="text/javascript"></script>';
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
                                                  <label>IFrame Code</label>
                                                <?php
                                                $script= '<iframe src="'.$appURL.'reviews.php?id='.$campID.'" width="960" style="border:none;" height="800"></iframe>';
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
                        <div class="form-group" id="reviews"></div>
					            </div>
									    </div>
                    </form>


                                <ul class="media-list" id="list_structure" style="display: none;">
                                    <li class="media thumbnail">
                                        <a class="pull-left" href="_action_url_" target="_blank">
                                            <img class="media-object img-circle  r_dp _picture_url_"  src="" alt=""/>
                                                   <span class="r_type">_type_</span>
                                        </a>

                                        <div class="media-body">
                                            <h5 class="media-heading"><a href="_action_url_" target="_blank" class="r_name">_author_name_</a>_rating_</h5>
                                            <h6 class="r_date">_date_</h6>

                                            <p class="r_review" align="justify">_review_</p>
                                        </div>
                                    </li>
                                </ul>

                                <div class="row" id="grid_structure" style="display: none;">
                                    <div class="col-md-6">
                                        <div class="thumbnail">
                                            <img src="" class="img-responsive img-circle r_dp _picture_url_" alt=""/>

                                            <div class="caption" align="center">
                                              <span class="r_type">_type_</span>
                                                <h3><a href="_action_url_" target="_blank" class="r_name">_author_name_</a> _rating_</h3>
                                                <h6 class="r_date">_date_</h6>
                                                <p class="r_review" align="justify">_review_</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="slide_structure" style="display: none;">
                									<div class="item _active_" align="center">
                										<div class="row">
                                        <div class="col-md-12">
                                            <img src="" class="img-responsive r_dp img-circle _picture_url_" alt=""/>
                                            <span class="r_type">_type_</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                      <div class="col-md-12">
        										             <div class="carousel-caption" align="center">
        											              <h3 class="font-600 r_name" align="center"><a href="_action_url_" target="_blank" class="r_name">_author_name_</a>_rating_</h3>
        											              <h6 class="r_date">_date_</h6>
                                            <p class="r_review" align="center">_review_</p>
        										              </div>
                                      </div>
                                    </div>
									                </div>
                                </div>

                                <div id="json_container_facebook" style="display:none;"><?php echo $facebook_json;?></div>
                                <div id="json_container_google" style="display:none;"><?php echo $google_json; ?></div>
                                <div id="json_container_yelp" style="display:none;"><?= @$yelp_json;?></div>
                                <div id="json_container_custom" style="display:none;"><?php echo $custom_json;?></div>

                                </div>
                            </section>
                        </div>
                    </div>
                </section>

              </div>
        </section>

		<!-- Vendor -->
		<script src="assets/vendor/jquery/jquery.js"></script>
		<script src="assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="assets/vendor/bootstrap/js/bootstrap.js"></script>
		<script src="assets/vendor/nanoscroller/nanoscroller.js"></script>
		<script src="assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="assets/vendor/magnific-popup/magnific-popup.js"></script>
		<script src="assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>

		<!-- Specific Page Vendor -->
        <script src="assets/vendor/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
		<script src="assets/vendor/jquery-autosize/jquery.autosize.js"></script>
		<script src="assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>

        <script src="assets/vendor/ios7-switch/ios7-switch.js"></script>
		<script src="assets/vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
        <script src="assets/vendor/select2/select2.js"></script>
        <script src="assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js"></script>

		<!-- Theme Base, Components and Settings -->
		<script src="assets/javascripts/theme.js"></script>

		<!-- Theme Custom -->
		<script src="assets/javascripts/theme.custom.js"></script>

		<!-- Theme Initialization Files -->
		<script src="assets/javascripts/theme.init.js"></script>



<script>

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
            getcustomReviews();
        }else{
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
    $("#reviews").html("");
    $("#settings_nav").slideUp("slow");
    $("#fb_page").val("");
    $("#google_business").val("");
}


function changeStyle(style="list"){


    $( "#reviews" ).html("");

    if(style=="slide"){
        var content = '<div id="carousel-example-captions" data-ride="carousel" class="carousel slide media-slide"><div role="listbox" class="carousel-inner"></div><a href="#carousel-example-captions" role="button" data-slide="prev" class="left carousel-control"> <span aria-hidden="true" class="fa fa-angle-left"></span> <span class="sr-only">Previous</span> </a><a href="#carousel-example-captions" role="button" data-slide="next" class="right carousel-control"> <span aria-hidden="true" class="fa fa-angle-right"></span> <span class="sr-only">Next</span></a></div>';
    }
    else if(style=="grid"){
      var content = '<ul class="media-grid"></ul>';
    }
    else{
        var content = '<ul class="media-list"></ul>';
    }

    $( "#reviews" ).html( content );

    if($("#is_google").is(":checked")){
        var json = $("#json_container_google").text();
        if(json.trim()!=""){
            showReviews(json,"google",style);
        }
    }

    if($("#is_facebook").is(":checked")){
        var json = $("#json_container_facebook").text();
        if(json.trim()!=""){
            showReviews(json,"facebook",style);
        }
    }

    if($("#is_yelp").is(":checked")){
        var json = $("#json_container_yelp").text();
        if(json.trim()!=""){
            showReviews(json,"yelp",style);
        }
    }

    if($("#is_custom").is(":checked")){
        var json = $("#json_container_custom").text();
        if(json.trim()!=""){
            showReviews(json,"custom",style);
        }
    }

    addRating();

    if(style=="slide"){
        $("#reviews .item:first-child").addClass("active");
        $("#reviews span.user-rating").addClass("m-auto");
    }
}

function showReviews(json,platform,style="list"){

    var response = jQuery.parseJSON(json);

    if(platform=="google"){


        if(response != null && typeof(response.error_message) !== "undefined" && response.error_message !== null) {
            var alertmsg = '<div class="alert alert-danger" style="margin: 10px -7px;"><b>Google API ERROR</b><br /> '+response.status+' : '+response.error_message+'</div>';
            $(".content-page .container").prepend(alertmsg);
        }
        else
        if(response != null && typeof(response.result.reviews) != "undefined" && response.result.reviews !== null) {


            $.each( response.result.reviews, function( key, review ) {
              // console.log(review);
                var action_url = review.author_url;
                var picture_url = review.profile_photo_url;
                var author_name = review.author_name;
                var rating = review.rating;

                rating = '<span class="user-rating r_rating">'+rating+'</span>';
                var date = review.relative_time_description;
                var date = new Date(review.time * 1000);

                var date = date.getFullYear()+'-'+(date.getMonth()+1).toString().padStart(2,0)+'-'+date.getDay().toString().padStart(2,0);
                var review = review.text;

                if(style=="list"){
                    var structure = $("#list_structure");
                }else if(style=="grid"){
                    var structure = $("#grid_structure");
                }else{
                    var structure = $("#slide_structure");
                }
                structure.find('img._picture_url_').attr('src',picture_url);
                structure = structure.html();

                structure = structure.replace(/_action_url_/g, action_url);

                structure = structure.replace(/_author_name_/g, author_name);
                structure = structure.replace(/_rating_/g, rating);
                structure = structure.replace(/_date_/g, date);


                structure = structure.replace(/_type_/g, '<i class="icon-google"></i>');
                structure = structure.replace(/_review_/g, review);

                if(style=="slide"){
                    $("#reviews .carousel-inner").append(structure);
                }else{
                    $("#reviews ul").append(structure);
                }

            });
            //addRating();
            $("#settings_nav").slideDown("slow");
        }
        else{
            $("#reviews").append("No Google Reviews Found");
        }
    }
    else
    if(platform=="facebook"){


        if(typeof(response.data) != "undefined" && response.data !== null) {




            $.each( response.data, function( key, review ) {

                  if(typeof(review.open_graph_story) != "undefined" && review.open_graph_story !== null) {



                var action_url = review.open_graph_story.id;
                if (review.reviewer) {

                  //var picture_url = "https://graph.facebook.com/"+review.reviewer.id+"/picture?type=large";
                  // console.log(review.reviewer);
                  if (review.reviewer.picture) {

                  var picture_url = review.reviewer.picture.data.url;
                }
                  var author_name = review.reviewer.name;
                } else {
                  var picture_url = "";
                  var author_name = "-";
                }

                if (review.rating) {
                  var rating = review.rating;
                } else {
                  var rating = 0;
                }
                rating = '<span class="user-rating r_rating">'+rating+'</span>';
                var date = review.created_time;

                var msec = Date.parse(review.created_time);
                var date = new Date(msec);
                var date = date.getFullYear()+'-'+(date.getMonth()+1).toString().padStart(2,0)+'-'+date.getDay().toString().padStart(2,0);

                if (review.review_text) {
                var review = review.review_text;
              } else {
                var review = "";
              }


                if(style=="list"){
                    var structure = $("#list_structure");
                }else if(style=="grid"){
                    var structure = $("#grid_structure");
                }else{
                    var structure = $("#slide_structure");
                }

                structure.find('img._picture_url_').attr('src',picture_url);
                structure = structure.html();
                structure = structure.replace(/_action_url_/g, 'https://facebook.com/'+action_url);
                structure = structure.replace(/_author_name_/g, author_name);
                structure = structure.replace(/_rating_/g, rating);
                structure = structure.replace(/_date_/g, date);
                structure = structure.replace(/_type_/g, '<i class="icon-facebook"></i>');
                structure = structure.replace(/_review_/g, review);


                if(style=="slide"){
                    $("#reviews .carousel-inner").append(structure);
                }else{
                    $("#reviews ul").append(structure);
                }
              }  else{

                  $("#reviews").append(review);
                    return false;
              }
            });

            //addRating();
            $("#settings_nav").slideDown("slow");
        }
        else{
            $("#reviews").append("No Facebook Reviews Found");
        }
    }
    else
    if(platform=="yelp"){
        if(typeof(response.error) != "undefined" && response.error !== null) {
          //  console.log("ererer");
            $("#yelp_error_area").html(response.error.description);
        }
        else
        if(typeof(response.reviews) != "undefined" && response.reviews !== null) {
            $("#yelp_error_area").html("");

            if (response.reviews.code != "VALIDATION_ERROR") {


            $.each( response.reviews, function( key, review ) {

              // console.log(review);
                var action_url = review.url;
                var picture_url = review.user.image_url;
                var author_name = review.user.name;
                var rating = review.rating;
                rating = rating.toString().replace(/\./g,'_');
                rating = '<span class="yelp-rating yelp'+rating+'"></span>';

                var date = review.time_created;
                var msec = Date.parse(review.time_created);
                var date = new Date(msec);
                var date = date.getFullYear()+'-'+(date.getMonth()+1).toString().padStart(2,0)+'-'+date.getDay().toString().padStart(2,0);

                var review = review.text;

                if(style=="list"){
                    var structure = $("#list_structure");
                }else if(style=="grid"){
                    var structure = $("#grid_structure");
                }else{
                    var structure = $("#slide_structure");
                }

                structure.find('img._picture_url_').attr('src',picture_url);
                structure = structure.html();

                structure = structure.replace(/_action_url_/g, action_url);

                structure = structure.replace(/_author_name_/g, author_name);
                structure = structure.replace(/_rating_/g, rating);
                structure = structure.replace(/_date_/g, date);

                structure = structure.replace(/_type_/g, '<i class="icon-yelp"></i>');
                structure = structure.replace(/_review_/g, review);

                if(style=="slide"){
                    $("#reviews .carousel-inner").append(structure);
                }else{
                    $("#reviews ul").append(structure);
                }
            });
            }
            //addRating();
            $("#settings_nav").slideDown("slow");
        }
        else{
            $("#reviews").append("No Facebook Reviews Found");
        }
    }
    else
    if(platform=="custom"){
        if(typeof(response.data) != "undefined" && response.status == "success") {

            $.each( response.data, function( key, review ) {


                var action_url = "#";
                var picture_url = review.photo;
                var author_name = review.name;
                var rating = review.rating;
                rating = '<span class="user-rating r_rating">'+rating+'</span>';
                var date = review.date;

                var msec = Date.parse(review.date);
                var date = new Date(msec);
                var date = date.getFullYear()+'-'+(date.getMonth()+1).toString().padStart(2,0)+'-'+date.getDay().toString().padStart(2,0);

                var review = review.review;

                if(style=="list"){
                    var structure = $("#list_structure");
                }else if(style=="grid"){
                    var structure = $("#grid_structure");
                }else{
                    var structure = $("#slide_structure");
                }

                structure.find('img._picture_url_').attr('src',picture_url);
                structure = structure.html();
                structure = structure.replace(/_action_url_/g, action_url);
                structure = structure.replace(/_author_name_/g, author_name);
                structure = structure.replace(/_rating_/g, rating);
                structure = structure.replace(/_date_/g, date);
                structure = structure.replace(/_type_/g, '<i class="icon-custom"></i>');
                structure = structure.replace(/_review_/g, review);

                if(style=="slide"){
                    $("#reviews .carousel-inner").append(structure);
                }else{
                    $("#reviews ul").append(structure);
                }
            });
            //addRating();
            $("#settings_nav").slideDown("slow");
        }
        else{
            $("#reviews").append("No Custom Reviews Found");
        }
    }








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
      $( "#reviews" ).prepend("<center class='m-t-50'><img src='assets/img/ajax-loader-black-bar.gif'/></center>");
      var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }

      $("#place_id").val(place.place_id);
      var qry = "action=place_reviews&place_id="+place.place_id;
      $.post( "action.php",qry, function( data ) {

          $("#json_container_google").text(data);
          changeStyle($("input[name='style']:checked").val());

      });


    });
}
</script>
<?php


if (@$settings['google_key']!="" && @$row['place_id']=="") {
    ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo @$settings['google_key']; ?>&libraries=places&callback=initMap"
async defer></script>
<?php
}
?>

<?php
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



?>



        <!-- Rating js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>


        <script>



            $( document ).ready(function() {
                 <?php
                 if ($row['is_google']==1 || $row['is_facebook']==1 || $row['is_yelp']==1 || $row['is_custom']==1) {
                     ?>
                    changeStyle($("input[name='style']:checked").val());
                    ApplyStyle();
                    <?php
                 }
                 ?>
            });


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
        </script>

        <script>

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
                $(".user-rating").rateYo("option", "ratedFill", ev.color.toHex());
            });


            $(document).ready(function () {
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

            })
        </script>


        <?php
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

<script async defer src="//assets.pinterest.com/js/pinit.js"></script>

	</body>
</html>
