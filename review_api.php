<?php
include_once("core/database.php");
$db= new Database();
$reviews = array();
$data = array();
$result = $db->getCustomReviewsByUserIdCampaignId($_REQUEST['uid'],$_REQUEST['campaigns_id']);

if($result != null ){
    foreach ($result as $row) {



//            error_log(print_r($row,true));
             if (trim($row['photo'])=="") {
               $row['photo'] =getServerURL()."/uploads/default_files/default_custom_review_user_picture.png" ;
             }
               else {
            $row['photo'] = (filter_var($row['photo'], FILTER_VALIDATE_URL) ? $row['photo'] : getServerURL()."/uploads/".$row['photo']);
            }


        $data[] = $row;
    }
    $reviews['status'] = "success";
    $reviews['data'] = $data;
}else{
    $reviews['status'] = "error";
}
echo json_encode($reviews);


function getServerURL()
{
    $serverName = $_SERVER['SERVER_NAME'];
    $filePath = $_SERVER['REQUEST_URI'];
    $withInstall = substr($filePath,0,strrpos($filePath,'/')+1);
    $serverPath = $serverName.$withInstall;
    $applicationPath = $serverPath;

    if(strpos($applicationPath,'http://www.')===false)
    {
    if(strpos($applicationPath,'www.')===false)
    $applicationPath = 'www.'.$applicationPath;
    if(strpos($applicationPath,'http://')===false)
    $applicationPath = 'http://'.$applicationPath;
    }

    if(isset($_SERVER["HTTPS"])){
        $protocol = "https://";
    }else{
        $protocol = "http://";
    }

    $applicationPath = str_replace("www.","",$applicationPath);
    $applicationPath = str_replace("http://",$protocol,$applicationPath);
    $applicationPath = str_replace("https://",$protocol,$applicationPath);

    return $applicationPath;
}
