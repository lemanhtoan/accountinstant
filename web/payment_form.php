<html>
<head>
    <title>Secure Acceptance - Payment Form Example</title>
    <link rel="stylesheet" type="text/css" href="payment.css"/>
    <script type="text/javascript" src="jquery-1.7.min.js"></script>
</head>
<body>
<?php
	$orderid = uniqid();
	$ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
	$device_id=	'premiumaccountz'.$orderid;
?>
<form id="payment_form" action="payment_confirmation.php" method="post">
    <input type="hidden" name="access_key" value="8f572025b41d3a03a2893ea2cec9ba6c">
    <input type="hidden" name="profile_id" value="3AF6ED91-65D6-4A40-AE66-B0D20A591BDA">
    <input type="hidden" name="transaction_uuid" value="<?php echo uniqid() ?>">
    <input type="hidden" name="signed_field_names" value="access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type,reference_number,amount,currency,device_fingerprint_id,customer_ip_address">
    <input type="hidden" name="unsigned_field_names">
    <input type="hidden" name="signed_date_time" value="<?php echo gmdate("Y-m-d\TH:i:s\Z"); ?>">
    <input type="hidden" name="locale" value="en">
	<input type="hidden" name="transaction_type" size="25" value="sale" >
	<input type="hidden" name="reference_number" size="25" value="<?php echo $orderid; ?>">
	<input type="hidden" name="currency" size="25" value="USD">
	<input type="hidden" name="device_fingerprint_id" value="<?php echo $device_id; ?>">
	<input type="hidden" name="customer_ip_address" size="25" value="<?php echo $ipaddress; ?>">
	
	<select id="amount" name="amount">
		<option value="0.5">FilesMonter 30 days</option>
		<option value="1.5">FilesMonter 60 days</option>
		<option value="2.5">FilesMonter 90 days</option>
		<option value="3.5">FilesMonter 180 days</option>
		<option value="4.5">FilesMonter 365 days</option>
	</select>
    <input type="submit" id="submit" name="submit" value="Submit"/>
	<p style="background:url(https://h.online-metrix.net/fp/clear.png?org_id=premiumaccountz&amp;session_id=premiumaccountz<?php echo $orderid; ?>&amp;m=1)"></p>
    <img src="https://h.online-metrix.net/fp/clear.png?org_id=premiumaccountz&amp;session_id=premiumaccountz<?php echo $orderid; ?>&amp;m-2" alt="">
    <script type="text/javascript" src="payment_form.js"></script>
</form>
</body>
</html>
