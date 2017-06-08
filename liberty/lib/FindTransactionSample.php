<?php
include "ApiAgent.php";

$auth = new Authentication("U1234567", "ApiName", 'SecurityWord');
$receiptId = "12345678";

$apiAgent = ApiAgentFactory::createApiAgent(ApiAgentFactory::JSON, $auth);
$findTransfer = $apiAgent->findTransaction($receiptId);

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
echo "<tr>
          <td>$findTransfer->Batch</td>
          <td>$findTransfer->Date</td>
          <td>$findTransfer->Amount</td>
          <td>$findTransfer->Fee</td>
          <td>$findTransfer->Balance</td>
          <td>$findTransfer->Currency</td>
          <td>$findTransfer->Payer($findTransfer->PayerName)</td>
          <td>$findTransfer->Payee($findTransfer->PayeeName)</td>
          <td>$findTransfer->Memo</td>
          <td>$findTransfer->Private</td>
          <td>$findTransfer->Reference</td>
          <td>$findTransfer->Source</td>      
        </tr>";
echo "</table>";
?> 