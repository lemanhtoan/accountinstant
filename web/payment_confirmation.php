<?php include 'security.php' ?>

<html>
<head>
    <title>Your payment is processing</title>
    <link rel="stylesheet" type="text/css" href="payment.css"/>
</head>
<body>
<form id="payment_confirmation" action="https://secureacceptance.cybersource.com/pay" method="post"/>
<?php
    foreach($_REQUEST as $name => $value) {
        $params[$name] = $value;
    }
?>
    <?php
        foreach($params as $name => $value) {
            echo "<input type=\"hidden\" id=\"" . $name . "\" name=\"" . $name . "\" value=\"" . $value . "\"/>\n";
        }
        echo "<input type=\"hidden\" id=\"signature\" name=\"signature\" value=\"" . sign($params) . "\"/>\n";
    ?>
	<input type="submit" value="Pay" name="Pay" border="0">
</form>
<script type="text/javascript">
		document.onreadystatechange=function(){
			document.form.submit();
		}
	</script>
</body>
</html>
