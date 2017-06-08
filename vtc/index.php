<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
<head><title>
Sample Merchant SA PHP
<link href="https://365.vtc.vn/trading/Content/css/365.css" rel="stylesheet">
<link href="https://365.vtc.vn/trading/Content/css/style.css" rel="stylesheet">
</title
</head>
<body>
    <form name="form1" method="post" action="checkout.php" id="form1">
    <table>
     <tr><td>OrderID</td><td>
         <input name="txtOrderID" type="text" value="<?php echo date("YmdHis")?>" id="txtOrderID" /></td></tr>
    <tr>
    <td>Customer First Name</td><td>
        <input name="txtCustomerFirstName" type="text" value="" id="txtCustomerFirstName" /></td></tr>
		<td>Customer Last Name</td><td>
        <input name="txtCustomerLastName" type="text" value="" id="txtCustomerLastName" /></td></tr>
		<td>Bill to address line 1</td><td>
        <input name="txtBillAddress1" type="text" value="" id="txtBillAddress1" /></td></tr>
		<td>Bill to address line 2</td><td>
        <input name="txtBillAddress2" type="text" value="" id="txtBillAddress2" /></td></tr>
		<td>City</td><td>
        <input name="txtCity" type="text" value="" id="txtCity" /></td></tr>
		<td>Country</td><td>
        <input name="txtCountry" type="text" value="" id="txtCountry" /></td></tr>
		<td>Customer Email</td><td>
        <input name="txtCustomerEmail" type="text" value="" id="txtCustomerEmail" /></td></tr>
		<td>Customer Mobile</td><td>
        <input name="txtCustomerMobile" type="text" value="84919948888" id="txtCustomerMobile" /></td></tr>
		<td>Param Exten</td><td>
        <input name="txtParamExt" type="text" value="PaymentType:Visa;Direct:Visa" id="txtParamExt" /></td></tr>
		<td>URL return</td><td>
        <input name="txtUrlReturn" type="text" value="http://keyinstant.com/success" id="txtUrlReturn" /></td></tr>
		<td>Secret Key</td><td>
        <input name="txtSecret" type="text" value="2bacb86c6da2Sda2N7dE660dPassword2008#" id="txtSecret" /></td></tr>
    <tr><td>Price: </td><td>
        <input name="txtTotalAmount" type="text" value="0.5" id="txtTotalAmount" /></td></tr>
    
     <tr><td class="style1">Unit: </td><td class="style1">
        <input name="txtCurency" type="text" value="2" id="txtCurency" />
	</td></tr>
		
        <tr><td>Website ID</td><td><input name="txtWebsiteID" type="text" value="1238" id="txtWebsiteID" /></td></tr>
    <tr><td></td><td>
        <tr><td>Recieve Account</td><td><input name="txtReceiveAccount" type="text" value="0919948887" id="txtReceiveAccount" /></td></tr>		
<tr><td>Description</td><td>
             <input name="txtDescription" type="text" value="Premium key" id="txtDescription" /></td></tr>					 			
    <tr><td></td><td>	
        <input type="submit" name="Button1" value="Pay with Credit Card" id="Button1" style="width:188px;" />
        </td></tr>
    </table>
    </div>
    </form>
</body>
</html>