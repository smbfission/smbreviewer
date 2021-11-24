<?php

$access_token="EAAG68OhF3K0BANETTr03vaVV6KBLJNQGaxB48VkfCbLZBxEKQtDhAwBXgV0aAShsWSl21kuKKJ1zW8ZCdAyCTw7WTYH7tZCvbUXqNJGEb3eoWvGZCfKtM6Hw9XP1o45IC1C0RU5kanYkWNMx3sUs7CvAZABplt0OWZAlYByRqIkwZDZD";

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



$url_me="https://graph.facebook.com/me?access_token=".$access_token;


$me=post_fb($url_me,"get");




 ?>

 <!DOCTYPE html>
 <html lang="en" dir="ltr">
   <head>
     <meta charset="utf-8">
     <title></title>
   </head>
   <body>
      <h3>making get request with url =  <?= $url_me?></h3>

      <pre><?=$me?></pre>
   </body>
 </html>
