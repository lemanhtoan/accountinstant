<?php

    if ($_POST) {
        
		$destinationUrl="https://pay.vtc.vn/cong-thanh-toan/checkout.html";
		//new version
		$plaintext = $_POST["txtWebsiteID"] . "-" . $_POST["txtCurency"] . "-" . $_POST["txtOrderID"] . "-" . $_POST["txtTotalAmount"] . "-" . $_POST["txtReceiveAccount"] . "-" . $_POST["txtParamExt"] . "-" . $_POST["txtSecret"]. "-" . $_POST["txtUrlReturn"];
		$sign = strtoupper(hash('sha256', $plaintext));
		$data = "?website_id=" . $_POST["txtWebsiteID"] . "&payment_method=" . $_POST["txtCurency"] . "&order_code=" . $_POST["txtOrderID"] . "&amount=" . $_POST["txtTotalAmount"] . "&receiver_acc=" .  $_POST["txtReceiveAccount"]. "&urlreturn=" .  $_POST["txtUrlReturn"];
		$customer_first_name = htmlentities($_POST["txtCustomerFirstName"]);
		$customer_last_name = htmlentities($_POST["txtCustomerLastName"]);
		$bill_to_address_line1 = htmlentities($_POST["txtBillAddress1"]);
		$bill_to_address_line2 = htmlentities($_POST["txtBillAddress2"]);
		$city_name = htmlentities($_POST["txtCity"]);
		$address_country = htmlentities($_POST["txtCountry"]);
		$customer_email = htmlentities($_POST["txtCustomerEmail"]);
		$order_des = htmlentities($_POST["txtDescription"]);
		$data = $data . "&l=en&customer_first_name=" . $customer_first_name. "&customer_last_name=" . $customer_last_name. "&customer_mobile=" . $_POST["txtCustomerMobile"]. "&bill_to_address_line1=" . $bill_to_address_line1. "&bill_to_address_line2=" . $bill_to_address_line2. "&city_name=" . $city_name. "&address_country=" . $address_country. "&customer_email=" . $customer_email . "&order_des=" . $order_des . "&param_extend=" . $_POST["txtParamExt"] . "&sign=" . $sign;

		$destinationUrl = $destinationUrl . $data;
		
$html =  <<<XML
<div align='center'><h2><font color='red'>Loading Data.Please Wait</font></h2></div>
<script>window.location = '{$destinationUrl}';</script>
XML;
		
		echo $html;        
    }
    else
    {
        echo "No data";
    }
?>