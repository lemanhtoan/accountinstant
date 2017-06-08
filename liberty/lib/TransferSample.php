<?
include "ApiAgent.php";

$auth = new Authentication("U1234567", "ApiName", 'SecurityWord');

$payee = "U2345678";
$currency = "Usd";
$amount = 1;
$memo = "Memo1";
$private = "false";
$purpose = "Salary";
$reference = "Reference1";

$apiAgent = ApiAgentFactory::createApiAgent(ApiAgentFactory::JSON, $auth); 
$transfer = $apiAgent->transfer($payee, $currency, $amount, $private, $purpose, $reference, $memo);

if(is_object($transfer)) {
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
              <td>$transfer->Batch</td>
              <td>$transfer->Date</td>
              <td>$transfer->Amount</td>
              <td>$transfer->Fee</td>
              <td>$transfer->Balance</td>
              <td>$transfer->Currency</td>
              <td>$transfer->Payer($transfer->PayerName)</td>
              <td>$transfer->Payee($transfer->PayeeName)</td>
              <td>$transfer->Memo</td>
              <td>$transfer->Private</td>
              <td>$transfer->Reference</td>
              <td>$transfer->Source</td> 
            </tr>";
    echo "</table>";
} else {
    echo "SMS confirmation code sent. Your RequestId: $transfer"; 
}
?> 