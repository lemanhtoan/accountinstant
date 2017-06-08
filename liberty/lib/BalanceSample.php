<?php
include "ApiAgent.php";

$auth = new Authentication("U1234567", "ApiName", 'SecurityWord');

$currency = "Usd";

$apiAgent = ApiAgentFactory::createApiAgent(ApiAgentFactory::JSON, $auth); 
$balance = $apiAgent->balance($currency);

echo $balance;
?>