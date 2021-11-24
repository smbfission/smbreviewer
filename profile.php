<?php
include_once("header.php");
include_once("sidebar.php");

//will be displayed if $current_user from header.php is active

function encode($str){
		$id=uniqid();
		$last=substr($id,strlen($id)-10);
		$start=rand(11,99);
		return $start.$str.$last;
	}

function post_fb($url,$method,$body=""){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    if($method == "post"){
        curl_setopt($ch, CURLOPT_POST, true );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    else{
        curl_setopt($ch, CURLOPT_HTTPGET, true );
    }
    return curl_exec($ch);
}

function getServerURL()
{

    $protocol = ( ((!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] !== 'off')) || ($_SERVER['SERVER_PORT'] == 443) ) ? "https://" : "http://";
	$serverName = $_SERVER['SERVER_NAME'];
	$filePath = $_SERVER['REQUEST_URI'];
	$withInstall = substr($filePath,0,strrpos($filePath,'/')+1);
	$serverPath = $protocol.$serverName.$withInstall;
	$applicationPath = $serverPath;

//	if(strpos($applicationPath,'http://www.')===false)
//	{
//		if(strpos($applicationPath,'www.')===false)
//			$applicationPath = 'www.'.$applicationPath;
//		if(strpos($applicationPath,'http://')===false)
//			$applicationPath = 'https://'.$applicationPath;
//	}

return $protocol.$serverName.'/';
	// return $applicationPath;
}


$last_plan=$db->getLastUserPlan($current_user['id']);

$settings = $db->getGeneralSettings();

$paypalUrl		 = 'https://www.paypal.com/cgi-bin/webscr';
$paypalEmail	 = $settings['business_email'];
$returnURL    = getServerUrl();
$returnURL    = substr($returnURL,0,strrpos($returnURL,'/'));




?>
<style media="screen">
.row.display-flex {
display: flex;
flex-wrap: wrap;
}
.thumbnail {
height: 100%;
}
.thumbnail li {
  text-align: left;
}

.thumbnail li span {

  text-align: right;
position: absolute;
right: 4rem;
}
</style>

<!-- <link rel="stylesheet" href="assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.css" /> -->
          <section role="main" class="content-body">
					<header class="page-header">
						<h2>Profile</h2>

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
                                                Profile
                                            </h2>
            								<p class="text-muted font-13 m-b-15">
                                                <!-- Description Goes Here <br /> -->
                                                &nbsp;
                                            </p>
            							</div>
                  <?php if (isset($_SESSION['msg']) && $_SESSION['msg']!=""): ?>
                    <div class="col-sm-12">
                        <div class="alert alert-<?= (isset($_SESSION['msg_type']) ? $_SESSION['msg_type'] : 'info')  ?>"><?= $_SESSION['msg'] ?></div>
                    </div>
                  <?php endif; ?>
                  <?php unset($_SESSION['msg']); ?>
                  </div>

                  <form action="/action.php?action=profile" method="post" class="form-horizontal form-bordered">

                    <div class="form-group">
                     <label class="col-md-3 control-label" >Active plan</label>
                     <!-- <div class="col-md-6">
                        <span class="form-control"><?= $current_user['plan_name'] ?></span>
                     </div> -->

										 <div class="col-md-6">
										 	<select class="form-control" name="plan">

											 <?php foreach ($db->getProfilePlans($current_user['id']) as $plan): ?>

												 		<option <?= ($plan['id']==$last_plan['plan_id'] ? "selected" : "" ) ?>  value="<?=$plan['id']?>"><?=$plan['title']?></option>

											 <?php endforeach; ?>

											 </select>
										 </div>
                      <div class="clearfix"></div>

                     <label class="col-md-3 control-label">Expire at</label>
                     <div class="col-md-6">
                       <?php

                       $now = time();
                       $date_stop = strtotime($last_plan['date_stop']);
                       $diff = round(($date_stop-$now)/ (60 * 60 * 24));
                       ?>
                      <?= (($diff<=0) ? "<label class=\"control-label text-danger\"><i class=\"fa fa-warning  text-danger\"></i> " : "<label class=\"control-label\">") ?><?= date( 'Y-m-d', strtotime($last_plan['date_stop'])) ?>
                        <span class="<?=(($diff<=0) ? "text-danger" : "text-info") ?>"><?= $diff?> day(s) left</span>

                        <a class="" data-toggle="modal" data-target="#plans_history" href="#plans_history" role="button" >
                          Show history
                        </a>
												<?php if (trim($last_plan['plugnpaid_cancellation_link'])!=''): ?>
													<a class="" href="<?= $last_plan['plugnpaid_cancellation_link'] ?>" >
	                          (Cancel subscription)
	                        </a>
												<?php endif; ?>
                        </label>


                     </div>
                     <div class="clearfix"></div>
                     <?php

                     if ($diff<=0 || $db->getCurrentUserPlan($current_user['id'])['plan_id']==$db->getGeneralSettings('default_plan_id')['default_plan_id']): ?>


                     <label class="col-md-3 control-label"></label>
                     <div class="col-md-6">
                       <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#upgrade_plan">Upgrade plan</button>


                   </div>
                   <?php endif; ?>
                   <div class="clearfix">

                   </div>
                   <br>
                     <div class="form-group">
											<label class="col-md-3 control-label" for="name">Name</label>
											<div class="col-md-6">
                                                <input class="form-control" name="name" value="<?= $current_user['name'] ?>" type="text">
											</div>
										</div>

                      <div class="form-group">
											<label class="col-md-3 control-label" for="email">Email</label>
											<div class="col-md-6">
                                                <input class="form-control" name="email" value="<?= $current_user['email'] ?>" type="text">
											</div>
										</div>

                    <div class="form-group ">
											<label class="col-md-3 control-label" for="old_password">Old password</label>
											<div class="col-md-6">
                        <div class="input-group">

                          <input class="form-control" name="old_password" value="" type="password">
                          <div class="input-group-addon input-password-toggle"><i class="fa fa-eye-slash" aria-hidden="true"></i></div>
                        </div>

											</div>
	                    <div class="clearfix form-control-static"></div>

                      <label class="col-md-3 control-label" for="new_password">New password</label>
                      <div class="col-md-6">
                        <div class="input-group">

                          <input class="form-control" name="new_password" value="" type="password">
                          <div class="input-group-addon input-password-toggle"><i class="fa fa-eye-slash" aria-hidden="true"></i></div>
                        </div>
                      </div>
                      <div class="clearfix form-control-static"></div>
                      <label class="col-md-3 control-label" for="new_password_confirm">New password confirmation</label>
                      <div class="col-md-6">
                        <div class="input-group">
                          <input class="form-control" name="new_password_confirm" value="" type="password">
                          <div class="input-group-addon input-password-toggle"><i class="fa fa-eye-slash" aria-hidden="true"></i></div>
                        </div>
                      </div>
                      <div class="clearfix form-control-static"></div>
										</div>

                    <div class="form-group">
                      <label class="col-md-3 control-label" for="phone">Phone</label>
                      <div class="col-md-6">
                        <input class="form-control" name="phone" value="<?= $current_user['phone'] ?>" type="phone">
                      </div>
                    </div>
                    <div class="form-group">
											<label class="col-md-3 control-label" for="address">Website Domain (www.domain.com)</label>
											<div class="col-md-6">
                        <input class="form-control" name="address" value="<?= $current_user['address'] ?>" type="text">
											</div>
										</div>
                    <div class="form-group">
                      <label class="col-md-3 control-label" for="advanced_settings">Advanced settings</label>
                      <div class="col-md-6">
                        <input class="form-control-static "


                         <?= (int)$current_user['use_default_credentials'] == 1 ?'  name="advanced_settings" ':' onclick="return false;" ' ?>

                         <?= (int)$current_user['advanced_settings'] ==1 ?'checked':'' ?> type="checkbox">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-md-3 control-label" for="advanced_settings">API connection status</label>
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span class="text-nowrap"><a  href="/settings">Facebook:
                            <?php if (trim(@$current_user['access_token'])=="" || trim(@$current_user['app_id'])=="" || trim(@$current_user['app_secret'])==""): ?>
                              <i class="fa fa-warning  text-danger"></i>
                            <?php else: ?>
                                <i class="fa fa-refresh text-primary" aria-hidden="true"></i>
                            <?php endif; ?>
                            </a>
                          </span>
                        </div>
                        <div class="col-md-4">
                          <span class="text-nowrap"><a href="/google_settings">Google:
                            <?php if (trim(@$current_user['google_key'])==""): ?>
                              <i class="fa fa-warning  text-danger"></i>
                            <?php else: ?>
                                <i class="fa fa-refresh text-primary" aria-hidden="true"></i>
                            <?php endif; ?>
                            </a>
                        </div>
                        <div class="col-md-4">
                          <span class="text-nowrap"><a href="/yelp_settings">Yelp:
                            <?php if (trim(@$current_user['yelp_api_key'])==""): ?>
                                <i class="fa fa-warning  text-danger"></i>
                              <?php else: ?>
                                  <i class="fa fa-refresh text-primary" aria-hidden="true"></i>
                              <?php endif; ?>
                              </a>
                        </div>

                      </div>
                    </div>


                                        <div class="form-group">
											<label class="col-md-3 control-label" for="inputDefault"></label>
											<div class="col-md-6">
                                                <input type="hidden" name="id" value="<?= $current_user['id']; ?>">
                                                <button type="submit" name="submit" value="submit"  class="btn  btn-fill btn-success "><span class="ace-icon fa fa-save bigger-120"></span> Save</button>
											</div>
										</div>

								    </form>



                                 </div>
                             </section>
                        </div>
                    </div>
                </section>



        <!-- END wrapper -->

        <div class="modal fade" id="plans_history" tabindex="-1" role="dialog" aria-labelledby="plans_history_label">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="plans_history_label">Plans  history</h4>
              </div>
              <div class="modal-body">

                                         <table class="table">

                                           <tr>
                                                 <th scope="col">Creation date</th>
                                                 <th scope="col">Plan</th>
                                                 <th scope="col">Date start</th>
                                                 <th scope="col">Date end</th>

                                               </tr>
                                           <?php $p=0;
                                           $user_plans = $db->getUserPlans($current_user['id']);


                                            ?>
                                            <?php if (@count($user_plans)>0): ?>
                                             <?php foreach ($user_plans as $user_plan): ?>
                                               <?php $p++; ?>
                                               <tr class="<?=((round((strtotime($user_plan['date_stop'])-$now)/ (60 * 60 * 24))>0 && $p==1) ? "success" : "text-danger text-striked"); ?>">
                                                 <td >
                                                   <?= date( 'Y-m-d H:i:s', strtotime($user_plan['creation_date'].' + '.(int)$_SESSION['tzo'].' minutes')) ?>
                                                 </td>
                                                 <td>
                                                   <?=$user_plan['title']?>
                                                 </td>
                                                 <td>
                                                   <?= date( 'Y-m-d', strtotime($user_plan['date_start'])) ?>
                                                 </td>
                                                 <td>
                                                   <?= date( 'Y-m-d', strtotime($user_plan['date_stop'])) ?>
                                                 </td>
                                               </tr>
                                             <?php endforeach; ?>
                                           <?php endif; ?>

                                           </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

              </div>
            </div>
          </div>
        </div>


        <div class="modal fade" tabindex="-1" role="dialog" id="upgrade_plan">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

             <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title">Available plans</h4>
             </div>
             <div class="modal-body">
               <div class="row display-flex">
              <?php foreach ($db->getAllPlans(true) as $row): ?>
                <?php if ($row['amount']>0): ?>


                  <div class="col-sm-6 col-md-4">
                    <div class="thumbnail">
                      <div class="caption">
                        <h3 class="text-center"><?=strtoupper($row['title'])?></h3>
                        <div class="text-center bg-primary"><strong><span class="">$</span><span class="text-lg"><?=intval($row['amount']) ?>.</span><span class="text-lg"><?=sprintf("%02d", $row['amount']  * 100 % 100) ?></span></strong>
                          <div class=""><?= $row['pricing_frequency']?></div>
                        </div>
                        <ul class="">
                          <li><?php echo $row['description']; ?></l>
                          <?= $row['features_list']?>
                        </ul>
                        <br>
                        <form name="paypalForm" action="<?php echo $paypalUrl?>" method="post">
                         <input type="hidden" name="cmd" value="_xclick-subscriptions">
                         <input type="hidden" name="business" value="<?=$paypalEmail?>">
                         <!--<input type="hidden" name="business" value="business@nimblewebsolutions.com" />  -->
                         <input type="hidden" name="first_name" value="<?=$current_user['name']?>">
                         <!-- <input type="hidden" name="last_name" value=""> -->
                         <input type="hidden" name="item_name" value="<?=$row['title']?>">
                              <input type="hidden" name="no_of_campaigns" value="<?= $row['no_of_campaigns'] ?>">
                         <input type="hidden" name="item_number" value="<?=$row['id']?>">
                         <input type="hidden" name="invoice" value="<?=$current_user['id'].'_'.strtotime(date("Y-m-d H:i:s"));?>">
                         <input type="hidden" name="no_shipping" value="1">
                         <input type="hidden" name="no_note" value="1">
                         <input type="hidden" name="currency_code" value="USD">
                         <input type="hidden" name="lc" value="US">
                         <input type="hidden" name="custom" value="<?=$current_user['id'].'_user_id'?>">

                         <input type="hidden" name="a3" value="<?=$row['amount']?>">
                         <input type="hidden" name="p3" value="1">
                         <input type="hidden" name="t3" value="M">
                         <input type="hidden" name="src" value="1">
                         <input type="hidden" name="sra" value="1">
                         <input type="hidden" name="notify_url" value="<?=$returnURL?>/ipn.php">
                         <input type="hidden" name="return" value="<?php echo $returnURL; ?>">
                         <input type="hidden" name="cancel_return" value="<?=$returnURL?>">
												 <?php if (trim($row['plugnpaid_plug_link'])!=''): ?>
													 <p class="text-center">
														 <a class="upgrade btn btn-primary" href="<?=$row['plugnpaid_plug_link']?>">Upgrade</a>

													 </p>
								         <?php else: ?>
                         <p class="text-center"><button class="upgrade btn btn-primary" role="button" type="submit">Upgrade</button></p>
												   <?php endif; ?>
                       </form>

                      </div>
                    </div>
                  </div>

                <?php endif; ?>

             <?php endforeach; ?>
             </div>
           </div>
             <div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

</div>
        </div>




      </div>

    </div>


<?php
// function post_fb($url, $method, $body="")
//                                 {
//                                     $ch = curl_init();
//                                     curl_setopt($ch, CURLOPT_URL, $url);
//                                     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 100);
//                                     curl_setopt($ch, CURLOPT_TIMEOUT, 100);
//                                     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//                                     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//                                     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//                                     if ($method == "post") {
//                                         curl_setopt($ch, CURLOPT_POST, true);
//                                         curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
//                                     } else {
//                                         curl_setopt($ch, CURLOPT_HTTPGET, true);
//                                     }
//                                     return curl_exec($ch);
//                                 }
?>
<!--
        <script>
            var resizefunc = [];
        </script> -->

        <!-- jQuery  -->
        <!-- <script src="assets/js/detect.js"></script>
        <script src="assets/js/fastclick.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/waves.js"></script>
        <script src="assets/js/jquery.slimscroll.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>
        <script src="../plugins/switchery/switchery.min.js"></script>

        <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="../plugins/datatables/dataTables.bootstrap.js"></script>

        <script src="../plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="../plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="../plugins/datatables/jszip.min.js"></script>
        <script src="../plugins/datatables/pdfmake.min.js"></script>
        <script src="../plugins/datatables/vfs_fonts.js"></script>
        <script src="../plugins/datatables/buttons.html5.min.js"></script>
        <script src="../plugins/datatables/buttons.print.min.js"></script>
        <script src="../plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="../plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="../plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="../plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="../plugins/datatables/dataTables.scroller.min.js"></script>
        <script src="../plugins/datatables/dataTables.colVis.js"></script>
        <script src="../plugins/datatables/dataTables.fixedColumns.min.js"></script> -->

        <!-- init -->
        <!-- <script src="assets/pages/jquery.datatables.init.js"></script> -->

        <!-- App js -->
        <!-- <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>

        <script type="text/javascript">
            $(document).ready(function () {
                $('#datatable-responsive').DataTable();
            });
            TableManageButtons.init();

        </script> -->



<?php include_once('footer_default.php'); ?>

<script type="text/javascript">

  $(document).ready(function () {
    $(".input-password-toggle").on('click', function(event){
      $(this).find('i').toggleClass('fa-eye-slash');
      $(this).find('i').toggleClass('fa-eye');
      if ($(this).siblings('input').attr('type')=='password') {
        $(this).siblings('input').attr('type','text');
      } else {
        $(this).siblings('input').attr('type','password');
      }

    });


    // $(document).on('submit','form[name="paypalForm"]',function(e){
    //
    //
    // });

  })
</script>

<script src="https://app.syncspider.com/api/v1/htmlforms/source/3022/form/snippet/smartForm"></script>

    </body>
</html>
