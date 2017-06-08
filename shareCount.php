<?php 
var_dump(get_pinterest());die('11');
function get_pinterest() {
$return_data = file_get_contents_curl('https://www.pinterest.com/oauth/?client_id=1439615&redirect_uri=https://keyissuer.com/shareCount.php&scope=read');
var_dump($return_data);die('11');
$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $return_data);
$json = json_decode($json_string, true);
return isset($json['count'])?intval($json['count']):0;
}
function file_get_contents_curl($url){
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_FAILONERROR, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$cont = curl_exec($ch);
if(curl_error($ch))
{
die(curl_error($ch));
}
return $cont;
}

?>