<?php 
function getCountryInfoByIp(){

    $client  = @$_SERVER['HTTP_CLIENT_IP'];

    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];

    $remote  = @$_SERVER['REMOTE_ADDR'];


    if(filter_var($client, FILTER_VALIDATE_IP)){

        $ip = $client;

    }elseif(filter_var($forward, FILTER_VALIDATE_IP)){

        $ip = $forward;

    }else{

        $ip = $remote;

    }

    $country = '';

    //$ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));    

	/*$ip_data = json_decode( file_get_contents( 'http://api.ip2location.com/?' . 'ip=42.112.234.249&key=demo' . '&package=WS10&format=json', true ));

    if($ip_data && $ip_data->geoplugin_countryName != null){

        $country = $ip_data->geoplugin_countryCode;

    }*/

$ip_data = json_decode(file_get_contents("http://freegeoip.net/json/".$ip));    

	    if($ip_data && $ip_data->country_code != null){

		$country = $ip_data->country_code;

	    }

    return $country;

}

var_dump(getCountryInfoByIp()); echo "<hr>"; 
$ipaddress ='42.112.234.249';

echo "<pre>"; var_dump(file_get_contents("http://freegeoip.net/json/".$ipaddress));

die("!");



$license_key = 'HJdvZknqxHeW';

$query = "https://minfraud.maxmind.com/app/ipauth_http?l=" . $license_key . "&i=" . $ipaddress;
$score = explode("proxyScore=", file_get_contents($query));

var_dump($score[1]);


?>
