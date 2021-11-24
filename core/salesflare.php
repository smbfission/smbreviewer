<?php




/**
 *
 */
class SalesFlare
{



  public static function apiCall($function,$method, $data)
  {

          require(dirname(__FILE__).'/config.php');


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

}


 ?>
