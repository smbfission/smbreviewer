<?php

include_once("header.php");

include_once("sidebar.php");

?>

            <!-- ============================================================== -->

            <!-- Start right Content here -->

            <!-- ============================================================== -->

            <div class="content-page">

                <!-- Start content -->

                <div class="content">

                    <div class="container">

                        <div class="row">

							<div class="col-xs-12">

								<div class="page-title-box">

                                    <h4 class="page-title">Dashboard</h4>

                                    <ol class="breadcrumb p-0 m-0">

                                        <li class="active">

                                            Dashboard

                                        </li>

                                    </ol>

                                    <div class="clearfix"></div>

                                </div>

							</div>

						</div>
                        <!-- end row -->

                        <?php

                        $exe = mysqli_query($con,"select id from campaigns where user_id = '".$_SESSION['user_id']."'");

                        $no_of_campaigns = mysqli_num_rows($exe);



                        $exe = mysqli_query($con,"select id from messages where user_id = '".$_SESSION['user_id']."'");

                        $no_of_messages = mysqli_num_rows($exe);



                        $exe = mysqli_query($con,"select id from reports where user_id = '".$_SESSION['user_id']."'");

                        $no_of_logs = mysqli_num_rows($exe);



                        $exe = mysqli_query($con,"select id from reports where user_id = '".$_SESSION['user_id']."' and created_date like '%".date("Y-m-d")."%'");

                        $today_logs = mysqli_num_rows($exe);



                        $server =  $_SERVER['HTTP_HOST'];

                        $server = str_replace("www.","",$server);

                        if($server == "demo.ranksol.com"){

                            $temp_names = array("Ranksol","Promotion King","Elite Programmers");

                            $start_date = "2017-01-10";

                            $end_date = "2017-01-15";

                        }else{

                            $temp_names = array();

                            $start_date = date("Y-m-d",strtotime(date("Y-m-d")." - 5 days "));

                            $end_date = date("Y-m-d");

                        }





                        $donutData = array();

                        $sql2 = "SELECT count(*) AS totalComments, page_id FROM reports WHERE user_id = '".$_SESSION['user_id']."' GROUP BY page_id order by totalComments DESC limit 3";

                        $exe2 = mysqli_query($con,$sql2);

                        $i=0;

                        while($row2 = mysqli_fetch_assoc($exe2)){

                            $sql3 = "select title from campaigns where fb_page = '".$row2['page_id']."'";

                            $exe3 = mysqli_query($con,$sql3);

                            $row3 = mysqli_fetch_assoc($exe3);

                            if(@$row3['title']==""){ $row3['title'] = $temp_names[$i]; }

                            $donutData[] = array("label"=>$row3['title'], "value"=>$row2['totalComments']);

                            $i++;

                        }

                        $donutData = json_encode($donutData);





                        $barData = array();

                        $sql4 = "SELECT count(*) AS totalComments, date(created_date) as graphday FROM reports WHERE user_id = '".$_SESSION['user_id']."' GROUP BY date(created_date) having graphday between '".$start_date."' and '".$end_date."'";

                        $exe4 = mysqli_query($con,$sql4);

                        while($row4 = mysqli_fetch_assoc($exe4)){

                            $barData[] = array("y"=>date("m/d",strtotime($row4['graphday'])),"a"=>$row4['totalComments']);

                        }

                        $barData = json_encode($barData);

                        ?>





                        <div class="row">



                            <div class="col-lg-3 col-md-6">

                                <div class="card-box widget-box-two widget-two-info">

                                    <i class="fa fa-tachometer widget-two-icon"></i>

                                    <div class="wigdet-two-content text-white">

                                        <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Campaigns">Campaigns</p>

                                        <h2 class="text-white"><span data-plugin="counterup"><?php echo $no_of_campaigns; ?></span></h2>

                                        <p class="m-0"><a href="campaign.php" style="color:#FFF;">Manage <span style="font-size:13px;" class="fa fa-external-link"></span></a></p>

                                    </div>

                                </div>

                            </div><!-- end col -->



                            <div class="col-lg-3 col-md-6">

                                <div class="card-box widget-box-two widget-two-primary">

                                    <i class="fa fa-envelope widget-two-icon"></i>

                                    <div class="wigdet-two-content text-white">

                                        <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Message Templates">Messages</p>

                                        <h2 class="text-white"><span data-plugin="counterup"><?php echo $no_of_messages; ?> </span></h2>

                                        <p class="m-0"><a href="messages.php" style="color:#FFF;">Manage <span style="font-size:13px;" class="fa fa-external-link"></span></a></p>

                                    </div>

                                </div>

                            </div><!-- end col -->



                            <div class="col-lg-3 col-md-6">

                                <div class="card-box widget-box-two widget-two-danger">

                                    <i class="fa fa-bar-chart widget-two-icon"></i>

                                    <div class="wigdet-two-content text-white">

                                        <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Logs">Total Logs</p>

                                        <h2 class="text-white"><span data-plugin="counterup"><?php echo $no_of_logs; ?></span></h2>

                                        <p class="m-0"><a href="reports.php" style="color:#FFF;">Check <span style="font-size:13px;" class="fa fa-external-link"></span></a></p>

                                    </div>

                                </div>

                            </div><!-- end col -->



                            <div class="col-lg-3 col-md-6">

                                <div class="card-box widget-box-two widget-two-success">

                                    <i class="fa fa-bar-chart widget-two-icon"></i>

                                    <div class="wigdet-two-content text-white">

                                        <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Today's Logs">Today's Logs</p>

                                        <h2 class="text-white"><span data-plugin="counterup"><?php echo $today_logs; ?> </span></h2>

                                        <p class="m-0"><a href="reports.php" style="color:#FFF;">Check <span style="font-size:13px;" class="fa fa-external-link"></span></a></p>

                                    </div>

                                </div>

                            </div><!-- end col -->



                        </div>





                        <div class="row">

                            <div class="col-lg-4">

                        		<div class="card-box">



                        			<h4 class="header-title m-t-0 ">Top Campaigns</h4>



                                    <div class="widget-chart text-center">

                                        <div id="morris-donut-example"style="height: 245px; padding-top:15px;"></div>

                                        <ul class="list-inline chart-detail-list m-b-0" style="visibility: hidden;">

                                            <li>

                                                <h5 class=""><i class="fa fa-circle m-r-5"></i>Series A</h5>

                                            </li>

                                            <li>

                                                <h5 class=""><i class="fa fa-circle m-r-5"></i>Series B</h5>

                                            </li>

                                        </ul>

                                	</div>

                        		</div>

                            </div><!-- end col -->



                            <div class="col-lg-8">

                                <div class="card-box ">

                                    <h4 class="header-title m-t-0 ">Statistics</h4>

                                    <div id="morris-bar-example" style="height: 280px;"></div>

                                </div>

                            </div><!-- end col -->



                        </div>

                        <!-- end row -->



                        <div class="row">

                            <div class="col-md-12">

                                <div class="panel panel-color panel-info">

                                    <div class="panel-heading">

                                        <h3 class="panel-title">Recent Logs</h3>

                                    </div>

                                    <div class="panel-body">

                                        <div class="table-responsive">

                                            <table class="table table table-hover m-0">

                                                <thead>

                                                    <tr>

                                                        <th>Action</th>

                                                        <th>Responded Text</th>

                                                        <th>Attachment</th>

                                                        <th>User's Comment</th>

                                                        <th>Date</th>

                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <?php



                    									$sql = "SELECT * FROM `reports` where user_id = '".$_SESSION['user_id']."' order by id desc LIMIT 10";

                    									$result = $conn->query($sql);

                                                        $showingRec = $result->num_rows;

                    										if($showingRec > 0){

                    											while($row = $result->fetch_assoc()) {

                    											$post_id = explode("_",$row['post_id']);

                                                                $comment_id = explode("_",$row['comment_id']);

                                                                $reply_id = explode("_",$row['reply_id']);



                                                                if($row['action']=="comment_reply"){

                                                                    $label = "comment-o text-success"; $title = "Replied to Comment";

                                                                }

                                                                else

                                                                if($row['action']=="personal_msg"){

                                                                    $label = "envelope-o text-primary"; $title = "Sent Personal Messsage";

                                                                }

                                                                else{

                                                                    $label = "thumbs-o-up text-info"; $title = "Liked Comment";

                                                                }

                                                                ?>

                                                                <tr>

                                                                    <td>

                                                                        <span style="font-size: 20px;" class="fa fa-<?php echo $label; ?>" title="<?php echo $title; ?>"></span>

                                                                    </td>

                                                                	<td>

                                                                    <?php if($row['action']=="like_comment"){ echo "Liked Comment"; }

                                                                    else

                                                                    {

                                                                        echo $row['reply'];

                                                                        if($row['action']=="comment_reply"){

                                                                        ?>

                                                                        <a target="_blank" href="https://www.facebook.com/<?php echo $row['page_id']; ?>/posts/<?php echo $post_id[1]; ?>?comment_id=<?php echo $comment_id[1]; ?>&reply_comment_id=<?php echo $reply_id[1]; ?>">

                                                                             <span style="font-size:13px;" class="fa fa-external-link"></span>

                                                                        </a>

                                                                        <?php

                                                                        }

                                                                    }

                                                                    ?>

                                                                    </td>

                                                                    <td align="center">

                                                                    <?php

                                                                        if($row['action']=="personal_msg"){

                                                                            if($row['attachment']=="1" && $row['attachment_id']!="")

                                                                            {

                                                                                $media_action = json_decode($row['media_action'],true);

                                                                                $media_url = $media_action['media_url'];

                                                                                $media_type= $media_action['media_type'];

                                                                                if($media_type=="image")

                                                                                {

                                                                                    ?>

                                                                                    <br />

                                                                                    <img style="width:120px;" src="<?php echo $media_url; ?>" />

                                                                                    <?php

                                                                                }else

                                                                                if($media_type=="audio")

                                                                                {

                                                                                    ?>

                                                                                    <br />

                                                                                    <audio width="200" controls>

                                                                                        <source src="<?php echo $media_url; ?>" type="audio/mpeg">

                                                                                        Your browser does not support the audio element.

                                                                                    </audio>

                                                                                    <?php

                                                                                }else

                                                                                if($media_type=="video")

                                                                                {

                                                                                    ?><br />

                                                                                     <video width="200" height="150" controls>

                                                                                     <source src="<?php echo $media_url; ?>" type="video/mp4">

                                                                                     Your browser does not support the video tag.

                                                                                     </video>

                                                                                    <?php



                                                                                }

                                                                            }

                                                                            else{

                                                                                echo "N/A";

                                                                            }

                                                                        }

                                                                        else{

                                                                            echo "N/A";

                                                                        }

                                                                        ?>

                                                                    </td>

                                                                    <td>

                                                                        <?php echo $row['message']; ?>

                                                                        <a target="_blank" href="https://www.facebook.com/<?php echo $row['page_id']; ?>/posts/<?php echo $post_id[1]; ?>?comment_id=<?php echo $comment_id[1]; ?>">

                                                                            <span style="font-size:13px;" class="fa fa-external-link"></span>

                                                                        </a>

                                                                        <br />

                                                                        <span style="font-size: 10px;">

                                                                            <a target="_blank" href="https://www.facebook.com/<?php echo $row['sender_id']; ?>">

                                                                                (<?php echo $row['sender_name']; ?>)

                                                                            </a>

                                                                        </span>

                                                                    </td>

                                                                    <td><?php echo $row['created_date']; ?></td>

                                                                </tr>

                                                                <?php

                    											}

                    										} else {

                    											echo "0 results";

                    										}

                    						              ?>



                                                </tbody>

                                            </table>



                                        </div> <!-- table-responsive -->

                                    </div> <!-- end panel body -->

                                </div>

                                <!-- end panel -->

                            </div>

                            <!-- end col -->



                        </div>

                        <!-- end row -->







                    </div> <!-- container -->



                </div> <!-- content -->



                <?php include_once("footer.php"); ?>



            </div>



        </div>

        <!-- END wrapper -->





        <script>

            var resizefunc = [];

        </script>



        <!-- jQuery  -->

        <script src="assets/js/jquery.min.js"></script>

        <script src="assets/js/bootstrap.min.js"></script>

        <script src="assets/js/detect.js"></script>

        <script src="assets/js/fastclick.js"></script>

        <script src="assets/js/jquery.blockUI.js"></script>

        <script src="assets/js/waves.js"></script>

        <script src="assets/js/jquery.slimscroll.js"></script>

        <script src="assets/js/jquery.scrollTo.min.js"></script>

        <script src="../plugins/switchery/switchery.min.js"></script>



        <!-- Counter js  -->

        <script src="plugins/waypoints/jquery.waypoints.min.js"></script>

        <script src="plugins/counterup/jquery.counterup.min.js"></script>



        <!--Morris Chart-->

		<script src="plugins/morris/morris.min.js"></script>

		<script src="plugins/raphael/raphael-min.js"></script>



        <!-- Dashboard init -->

        <!-- <script src="assets/pages/jquery.dashboard.js"></script> -->



        <!-- App js -->

        <script src="assets/js/jquery.core.js"></script>

        <script src="assets/js/jquery.app.js"></script>





        <script>





!function($) {

    "use strict";



    var Dashboard1 = function() {

    	this.$realData = []

    };



    //creates Bar chart

    Dashboard1.prototype.createBarChart  = function(element, data, xkey, ykeys, labels, lineColors) {

        Morris.Bar({

            element: element,

            data: data,

            xkey: xkey,

            ykeys: ykeys,

            labels: labels,

            hideHover: 'auto',

            resize: true, //defaulted to true

            gridLineColor: '#eef0f2',

            barSizeRatio: 0.3,

            barColors: lineColors,

            postUnits: ''

        });

    },





    //creates Donut chart

    Dashboard1.prototype.createDonutChart = function(element, data, colors) {

        Morris.Donut({

            element: element,

            data: data,

            resize: true, //defaulted to true

            colors: colors

        });

    },





    Dashboard1.prototype.init = function() {



        //creating bar chart

        /*var $barData  = [

            { y: '01/16', a: 42 },

            { y: '02/16', a: 75 },

            { y: '03/16', a: 38 },

            { y: '04/16', a: 19 },

            { y: '05/16', a: 93 }

        ];

        */

        var $barData = <?php echo $barData; ?>;

        this.createBarChart('morris-bar-example', $barData, 'y', ['a'], ['Statistics'], ['#3bafda']);





        //creating donut chart

        /*var $donutData = [

                {label: "Download Sales", value: 12},

                {label: "In-Store Sales", value: 30},

                {label: "Mail-Order Sales", value: 20}

            ];

            */

        var $donutData = <?php echo $donutData; ?>;

        this.createDonutChart('morris-donut-example', $donutData, ['#3ac9d6', '#f5707a', "#4bd396"]);

    },

    //init

    $.Dashboard1 = new Dashboard1, $.Dashboard1.Constructor = Dashboard1

}(window.jQuery),



//initializing

function($) {

    "use strict";

    $.Dashboard1.init();

}(window.jQuery);





        </script>



        <script src="https://dash.getastra.com/sdk.js?site=3YKkjSqTZ"></script>

<!-- footer check -->

    </body>

</html>
