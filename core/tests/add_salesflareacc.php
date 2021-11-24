<?php



require_once('../salesflare.php');

$data = ["name" => "foo",
        "website" => "foo.bar",
        "description" => "reviewers acc test ",
        "email" => "foo@bar.com",
        "phone_number" => "+1234567890"
      ];

$s = SalesFlare::apiCall('accounts','post',$data);

echo print_r($s,true);

 ?>
