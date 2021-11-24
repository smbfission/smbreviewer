<?php
@session_start();
include_once("database.php");
$uid = @$_REQUEST['uid'];
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Pricing Plans</title>
<!-- App css -->
<link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/core.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/components.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/icons.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/pages.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/menu.css" rel="stylesheet" type="text/css" />
<link href="/assets/css/responsive.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="plugins/switchery/switchery.min.css" />



</head>
<body>

<!-- header row -->
    <div class="row">
                            <div class="col-lg-9 center-page">
                                <div class="text-center">
                                    <h3 class="m-b-30 m-t-20">Sign up</h3>
                                    <p>
                                        Register your self today!
                                    </p>
                                </div>




<!-- pricing plans -->



                                <div class="row m-t-50">



		<?php
        if(isset($_SESSION['message']) && trim($_SESSION['message'])!=''){
			echo $_SESSION['message'];
		}
		unset($_SESSION['message']);
        ?>

			<?php

                $sql = "select * from plans";
                $res = mysqli_query($con,$sql);
				$totalRecords = mysqli_num_rows($res);
				if($totalRecords>0){
					while($row = mysqli_fetch_assoc($res)){
			?>
                <article class="pricing-column col-lg-4 col-md-4">
                    <div class="inner-box card-box">
                        <div class="plan-header text-center">
                            <h3 class="plan-title"><?php echo strtoupper($row['title'])?></h3>
                            <h2 class="plan-price">$ <?php echo $row['amount'];?></h2>
                            <div class="plan-duration">Per Month</div>
                        </div>
                        <ul class="plan-stats list-unstyled text-center">
                            <li><?php echo $row['description']; ?></li>
                            <li><?php echo $row['no_of_campaigns']?> Number of campaigns</li>
                            <li>Self Service</li>
                        </ul>

                        <div class="text-center">
                            <a href="add_user.php?pid=<?php echo encode($row['id'])?>&uid=<?php echo $uid?>" class="btn btn-danger btn-bordred btn-rounded waves-effect waves-light">Signup Now</a>
                        </div>
                    </div>
                </article>
            <?php
            ?>
			<?php
					}
				}else{
					echo '<h1 style="color:red">No plans created by admin.</h1>';
				}
			?>
            </div>
        </div>
   </div>


	<!-- footer div -->

<div class="footer">
	<div class="wrap">
		<p>&copy; 2020  All rights  Reserved | Powered by &nbsp;<a href="https://wwww.smbreviewer.com" target="_blank">SMBreviewer</a></p>
	</div>
</div>






</body>
</html>

<?php
function encode($str){
		$id=uniqid();
		$last=substr($id,strlen($id)-10);
		$start=rand(11,99);
		return $start.$str.$last;
	}
?>
