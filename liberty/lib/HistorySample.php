<?php
include "ApiAgent.php";

$auth = new Authentication("U1234567", "ApiName", 'SecurityWord');

$dateFrom = "2012-12-01 00:00:00";    
$dateTo = "2012-12-17 00:00:00";

$apiAgent = ApiAgentFactory::createApiAgent(ApiAgentFactory::JSON, $auth);
$history = $apiAgent->history($dateFrom, $dateTo);

echo "<table>
        <tr>
          <th>Batch</th>
          <th>Date</th>
          <th>Amount</th>
          <th>Fee</th>
          <th>Balance</th>
          <th>Currency</th>
          <th>Payer</th>
          <th>Payee</th>
          <th>Memo</th>
          <th>Private</th>
          <th>Reference</th>
          <th>Source</th>
        </tr>";
        
foreach ($history as $item) {
    echo "<tr>
          <td>$item->Batch</td>
          <td>$item->Date</td>  
          <td>$item->Amount</td>
          <td>$item->Fee</td>
          <td>$item->Balance</td>
          <td>$item->Currency</td>
          <td>$item->Payer($item->PayerName)</td>
          <td>$item->Payee($item->PayeeName)</td>
          <td>$item->Memo</td>
          <td>$item->Private</td>
          <td>$item->Reference</td>
          <td>$item->Source</td>
        </tr>";
}
echo "</table>";
?> 