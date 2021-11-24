<?php

//salesflare ali key
$salesFlare_api_key = 'ieQfu19Iduq24Vxxse_Qk1MMpWyUhj7GwR70QEpkyBJHv';



function apiCallSalesFlare($function,$method, $data)
{
        global $salesFlare_api_key;
         header('Content-Type: application/json'); // Specify the type of data
         $ch = curl_init('https://api.salesflare.com/'.$function); // Initialise cURL

         $authorization = "Authorization: Bearer ".$salesFlare_api_key; // Prepare the authorisation token
         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // Inject the token into the header
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POST, 1); // Specify the request method as POST

         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // This will follow any redirects

         if (strtolower($method) == "post") {
             $data = json_encode($data); // Encode the data array into a JSON string
             curl_setopt($ch, CURLOPT_POST, true);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Set the posted fields
         } elseif (strtolower($method)=="delete") {
             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
         } else {
             curl_setopt($ch, CURLOPT_HTTPGET, true);
         }

         $result = curl_exec($ch); // Execute the cURL statement
         curl_close($ch); // Close the cURL connection
         return json_decode($result); // Return the received data

}
// checking if form method post and exists required fields
if (isset($_POST)
  && isset($_POST['EMAIL'])
   && isset($_POST['FNAME'])
) {

  //adding form data to array, fields name from here https://api.salesflare.com/docs#operation/postAccounts
  $data = ["name" => $_POST['FNAME'],
          //"website" => $_POST['address'] ,
          // "description" => "reviewers account ",
          "email" => $_POST['EMAIL'],
          // "phone_number" => $_POST['payer_phone'],
          // "custom" => ['smbreviews_plan' => $plan['title'],
          //              'smbreviews_member' =>"yes",
          //              "premium_customer" => (((float)$plan['amount']>0) ? "yes" : "no" )
          //            ],
        ];

   apiCallSalesFlare('contacts','post',$data);
}
unset($_POST);

//here might be redirect to location or returning json or whatever ....

// header('Content-Type: text/html; charset=utf-8');
// die('form has been submitted');
 header("Location: ".$_SERVER['PHP_SELF'].'/../salesflareform.html');
exit;



 ?>
