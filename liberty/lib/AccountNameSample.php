<?php
include "ApiAgent.php";

$auth = new Authentication("U1234567", "ApiName", 'SecurityWord');

$accountToRetrieve = "U2345678";

$apiAgent = ApiAgentFactory::createApiAgent(ApiAgentFactory::JSON, $auth); 
$accountName = $apiAgent->accountName($accountToRetrieve);    

echo $accountName;                                             
?> 