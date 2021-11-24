<?php




/**
 * scrapper class
 */
class Scrapper
{
public $html;

private $yelp_tag;
private  $db;

public $response_code;
   function __construct()
   {
     if (class_exists('Database')) {

           $this->db =  Database::getInstance();
     }
     else {
     require_once(dirname(__FILE__).'/databse.php');
           $this->db =  Database::getInstance();
     }


   }



    public  function setProxyDb($settings=[])
    {
      return $this->db->updateGeneralSettings(['proxy_settings'=>json_encode($settings,true)]);
    }

    public  function getProxyDb()
    {


      // for ( as $key=>$value)
      // {
      // //  if ($value instanceof class_A)
      //     return $value;
      // }

      return json_decode($this->db->getGeneralSettings()['proxy_settings'],true);
// return get_declared_classes ( );




    }



    public  function checkProxyDb()
    {
      $proxy = $this->getProxyDb();



      // $foo= $this->dlPage('https://api.ipify.org/?format=json',[],true);
      // error_log('is ok with proxy '.$foo);
      if($fp = @fsockopen($proxy['ip'],$proxy['port'],$errCode,$errStr,3)){
        // error_log('is ok with proxy ');
        return true;
      }
      else {
          error_log('start with new proxy cuz of error '.$errStr);
        //
        // $proxy_type = [
        //   'http'=> 'CURLPROXY_HTTP',
        //   'socks4'=> 'CURLPROXY_SOCKS4',
        //   'socks4a'=> 'CURLPROXY_SOCKS4A',
        //   'socks5'=> 'CURLPROXY_SOCKS5',
        //   'socks5h'=> 'CURLPROXY_SOCKS5_HOSTNAME',
        //
        // ];

        $proxy_url = 'https://gimmeproxy.com/api/getProxy?protocol=http';
        $proxy = $this->dlPage($proxy_url,[],false);
        error_log('newe proxy is='.print_r($proxy,true));
        $proxy = json_decode($proxy,true);
        if (isset($proxy['ip'])) {
          $this->setProxyDb(['ip'=>strip_tags($proxy['ip']),'port'=>strip_tags($proxy['port']),'type'=>strip_tags($proxy['type'])]);
        }
        else {


           $proxy_url = 'https://api.getproxylist.com/proxy?port[]=80';
           $proxy = $this->dlPage($proxy_url,[],false);
           error_log('new2 proxy is='.print_r($proxy,true));
           $proxy = json_decode($proxy,true);
           if (isset($proxy['ip'])) {
             $this->setProxyDb(['ip'=>strip_tags($proxy['ip']),'port'=>strip_tags($proxy['port']),'type'=>strip_tags($proxy['protocol'])]);

           }



          // $proxy = file_get_contents('https://api.getproxylist.com/proxy');



        }


        return $errStr;

      }
      fclose($fp);

    }

    public function getYelpReviewsArray($bussines_name,$limit =0,$next =0,$sort_by="date_desc")
    {
      $url="https://www.yelp.com/biz/".$bussines_name."/review_feed?rl=en&sort_by=".$sort_by."&q=&start=".(int)$next;


      $reviews = $this->dlPage($url,[],true);
      // error_log($reviews);
      if ($r = json_decode($reviews,true)) {
        $result=[];
        if ($r['reviews']) {

          $result['total']=$r['pagination']['totalResults'];
          foreach ($r['reviews'] as $value) {

            $result['reviews'][] =
                      [
                        'id' => $value['id'],
                        'url' => 'https://www.yelp.com/biz/'.$bussines_name.'?hrid='.$value['id'],
                        'text' => $value['comment']['text'],
                        'rating' => $value['rating'],
                        'time_created' => $value['localizedDate'],
                        'user' => [
                                  'id' => $value['userId'],
                                  'profile_url' => 'https://www.yelp.com/user_details?userid='.$value['userId'],
                                  'image_url' => $value['user']['src'],
                                  'name' => $value['user']['markupDisplayName'],
                                    ]
                      ];


          }


        }

      if ($limit>0 && count($result['reviews'])<$limit && $result['total']>=$limit
      ) {

        foreach ( $this->getYelpReviewsArray($bussines_name,$limit-count($result['reviews']),$next+10,$sort_by)['reviews'] as $v) {
          $result['reviews'][] = $v;
        }

      }

        return $result;
      } else {
        return [];
      }


    }



  public function getYelpReviewsHtml($bussines_name,$next =0,$limit =0)
  {

    include_once 'vendor/autoload.php';
    $this->html = new simplehtmldom\HtmlDocument();
    $this->yelp_tag= '#wrap .main-content-wrap script';
 $url = ('https://www.yelp.com/biz/'.$bussines_name.'?sort_by=date_desc'.(($next !=0) ? "&start=".$next : ""));
//  $this->html->load_file($url);

  $this->html->load($this->dlPage($url),[],false);

  //return $this->html->save();
  // foreach($html->find('#wrap div[itemprop="review"]') as $element)


   if ($elements = $this->html->find($this->yelp_tag)) {

  //  return ['error' =>$elements];

  foreach($elements as $element){

    $data = preg_replace('/^<!--|-->$/', '', $element->innertext);


    if ($r = json_decode($data,true) ) {

      if (isset($r['bizDetailsPageProps'])) {

        if (isset($r['bizDetailsPageProps']['reviewFeedQueryProps'])) {
            $result = $r['bizDetailsPageProps']['reviewFeedQueryProps'];



        }
        else {
          return ['error'=>'no property '.'bizDetailsPageProps'];
        }

      } else {
        return ['error'=>'no property '.'bizDetailsPageProps'];
      }



    } else {
      return ['error'=>'error on decoding json data'];

    }


    return $result;
  }
}
     return ['error'=>'general parsing error on tag '.$this->yelp_tag];
  }

  public  function dlPage($url, $headers = [], $use_proxy=true) {

      // $headers[] = 'referer: https://www.ilcats.ru/';
      $headers[] = 'upgrade-insecure-requests: 1';
      // $headers[] = ':authority: www.ilcats.ru';
      // $headers[] = ':method: GET';
      // $headers[] = ':path: /abarth/';
      // $headers[] = ':scheme: https';
       $headers[] = 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
       $headers[] = 'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7';
      $headers[] = 'cache-control: no-cache';
      $headers[] = 'pragma: no-cache';


      // $proxy = file_get_contents('https://api.getproxylist.com/proxy?port[]=80');


      // $proxy = file_get_contents('https://api.getproxylist.com/proxy');
      //
      // $proxy = json_decode($proxy,true);




      $proxy_type = [
        'http'=> 'CURLPROXY_HTTP',
        'https'=> 'CURLPROXY_HTTPS',
        'socks4'=> 'CURLPROXY_SOCKS4',
        'socks4a'=> 'CURLPROXY_SOCKS4A',
        'socks5'=> 'CURLPROXY_SOCKS5',
        'socks5h'=> 'CURLPROXY_SOCKS5_HOSTNAME',

      ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
    //    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
if ($use_proxy === true ) {



/////#1
  //  $proxy_url = 'http://pubproxy.com/api/proxy';
  //  $proxy = $this->dlPage($proxy_url,[],false);
  //
  // $proxy1 = $proxy;
  //  $proxy = json_decode($proxy,true)['data'][0];
  //  curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy_type[$proxy['type']]);
  //  curl_setopt($ch, CURLOPT_PROXY, $proxy['ip'].":".$proxy['port']);

//////#2


   // $proxy = file_get_contents('https://api.getproxylist.com/proxy');
//    $proxy = $this->dlPage('https://api.getproxylist.com/proxy',[],false);
// $proxy1 = $proxy;
//   $proxy = json_decode($proxy,true);
//    curl_setopt($ch, CURLOPT_PROXY, $proxy['ip'].":".$proxy['port']);
//   curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy_type[$proxy['protocol']]);


/////###3

$this->checkProxyDb();


$proxy=$this->getProxyDb();

//
// $proxy_url = 'https://gimmeproxy.com/api/getProxy?protocol=http';
// $proxy = $this->dlPage($proxy_url,[],false);
//
// $proxy1 = $proxy;
// $proxy = json_decode($proxy,true);
//



curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy_type[$proxy['type']]);
curl_setopt($ch, CURLOPT_PROXY, $proxy['ip'].":".$proxy['port']);





}


        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds





        $htmltext = curl_exec($ch);


$this->response_code=curl_error($ch);
        if (trim($htmltext)=='') {


      //    $htmltext = json_encode(["error"=>curl_error($ch),'proxy'=>$proxy1],true);
          $htmltext = curl_error($ch);
        }

        curl_close($ch);

      //  error_log(print_r($htmltext,true));
        return $htmltext;

    }



}

//
//
//
// // end of class declatation
// //
// // $url="https://www.yelp.com/biz/the-artisan-kitchen-and-cafe-richmond-3?sort_by=date_desc";
// // $html = file_get_contents( $url );
// // $dom  = new DOMDocument();
// // libxml_use_internal_errors( 1 );
// // $dom->loadHTML( $html );
// // $xpath = new DOMXpath( $dom );
// // $jsonScripts = $xpath->query( '//script[@type="application/ld+json"]' );
// // $json = trim( $jsonScripts->item(0)->nodeValue );
// //
// // $data = json_decode( $json,true );
// //
//  $url="https://www.yelp.com/biz/3bqC5zevRa5o3jaH-3VNkw/review_feed?rl=en&sort_by=date_desc&q=&start=0";
// //  $html = file_get_contents( $url );
// // $data = json_decode( $html,true );
//
//
//
//
//
//  // echo count($data['review']);
//  //
//  // die();
//
// $s = new Scrapper();
//  //$data = $s->getYelpReviews('the-artisan-kitchen-and-cafe-richmond-2', 0);
//
//
// //die($s->test());
// $data=$s->dlPage($url,[],true);
// $data = json_decode( $data,true );
// header('Content-Type: application/json');die( json_encode($data));
// $a=100;
// while (isset($data['error']) and $a>0) {
//   sleep(1);
//   $a--;
//   $data=$s->dlPage($url,[],true);
//   $data = json_decode( $data,true );
// }
//
// header('Content-Type: application/json');die( json_encode($data));
//


 ?>
