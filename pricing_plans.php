<?php
@session_start();
include_once("core/database.php");
$uid = @$_REQUEST['uid'];

$db = new Database();
$res = $db->getAllPlans(true);

?>
<!DOCTYPE HTML>
<html>
<head>
<title>SMBreviewer Pricing Plans</title>
<!-- App css -->
<meta charset="UTF-8">
<link href="/assets/css/pricingstyles.css?v=8" rel='stylesheet' type='text/css'>

<script src="https://plu.ug/n/2dq6iyhz"></script>


</head>
<body style="text-align:center;vertical-align:top">

  <div class="s06d-pricing" style="text-align:center;vertical-align:top">


    <div class="logo" style="">

    <img src="/assets/images/logo_smbreviewer_new_20210202_800x400px-trans.png" style="border:0px;vertical-align:top; display: block; margin-left: auto; margin-right: auto; width: 40%;" alt="SMBreviwer">

 	  </div>
    <div class="clearfix"></div>



<?php
    if(isset($_SESSION['message']) && trim($_SESSION['message'])!=''){
			echo $_SESSION['message'];
		}
		unset($_SESSION['message']);
?>



<!-- Pricing Packages -->

<?php if (@count($res) > 0): ?>
  <?php foreach ($res as $row): ?>

    <div class="s06d-pricing-column <?= $row['pricing_column_classes']?>">
      <?php if (trim($row['pricing_ribbon'])!=''): ?>
          <div class="s06d-pricing-ribbon"><span><?= $row['pricing_ribbon']?></span></div>
      <?php endif; ?>
      <div class="s06d-pricing-header">
        <h1><?php echo strtoupper($row['title'])?></h1>
      </div>
      <div class="s06d-pricing-amount"><strong><span class="s06d-pricing-currency">$</span><span><?=intval($row['amount']) ?></span><code><?=sprintf("%02d", $row['amount']  * 100 % 100) ?></code></strong>
        <div class="s06d-pricing-frequency"><?= $row['pricing_frequency']?></div>
      </div>
      <ul class="s06d-pricing-feature">
  	<li><?php echo $row['description']; ?></li>
        <?= $row['features_list']?>
      </ul>
      <div class="s06d-pricing-footer">
        <?php if (trim($row['plugnpaid_plug_link'])!=''): ?>
          <a class="s06d-pricing-button" href="<?=$row['plugnpaid_plug_link']?>"><?= $row['pricing_button']?></a>
        <?php else: ?>
          <a class="s06d-pricing-button" href="/add_user?pid=<?php echo encode($row['id'])?>&uid=<?php echo $uid?>"><?= $row['pricing_button']?></a>
        <?php endif; ?>

      </div>
    </div>


  <?php endforeach; ?>




  <div class="s06d-pricing-column recommended">

    <div class="s06d-pricing-ribbon"><span>Best Value!</span></div>
    <div class="s06d-pricing-header">
    <h1>FULL SERVICE PARTNERSHIP</h1>
    </div>
    <div class="s06d-pricing-amount"><strong><span class="s06d-pricing-currency">$</span><span>25</span><code>/review</code></code></strong>
    <div class="s06d-pricing-frequency">Monthly</div>
    </div>
    <ul class="s06d-pricing-feature">
    <li></li>
    <li>Everything in the first 2 plans<span title="Everything in the first 2 plans">Plus...</span></li>
    <li>Dedicated Local Marketing Consultant<span title="Dedicated Local Marketing Consultant">Yes</span></li>
    <li>Monthly Email Credits<span title="Monthly Email Credits">10,000</span></li>
    <li>Monthly SMS Credits<span title="Monthly SMS Credits">1,000</span></li>
    <li>Incentivized Reviews<span title="Incentivized Reviews">Yes</span></li>
    <li>Premium Review Displays<span title="Premium Review Displays">Yes</span></li>
    <li>Gated Landing Pages<span title="Gated Landing Pages">Yes</span></li>
    <li>Campaign Analytics Dashboard<span title="Campaign Analytics Dashboard">Yes</span></li>
    <li>Customized Social Proof<span title="Customized Social Proof">Yes</span></li>
    <li>Lead Generation Tools<span title="Lead Generation Tools">Yes</span></li>
    <li>Review Tracking<span title="Review Tracking">Yes</span></li>
    <li>Number of Platforms Tracked<span title="Number of Platforms Tracked">25+</span></li>
    <li>Premium Support<span title="Premium Support">Yes</span></li>
    <li>0-Risk Policy<span title="Plan Commitment">Yes</span></li>
    <li>Refund for < 8 Reviews<span title="Up-front Payment">Yes</span></li> </ul>
    <div class="s06d-pricing-footer" >
    <a class="s06d-pricing-button" onclick="pnp_open_cart2dq6iyhz()" >Partner With Us</a>

    </div>
  </div>

<?php else: ?>
    <h1 style="color:red">No plans currently exist.</h1>
<?php endif; ?>


<!-- footer div -->

    <div class="footer">
    	<div class="wrap">
    		<p class="s06d-pricing-header" >&copy; <?= date("Y"); ?>  All rights  Reserved | Powered by &nbsp;<a href="https://www.smbreviewer.com" target="_blank" style="color:#ff6630">SMBreviewer</a></p>
    	</div>
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
