<?php include 'security.php' ?>

<html>
<head>
    <title>Secure Acceptance - Cyber Source</title>
  
</head>

<body>

<?php
    foreach($_REQUEST as $name => $value) {
        $params[$name] = $value;
		if(strcasecmp($name,"currency")==0 ){
				$params[$name] ="USD";
				}
    }
?>



<form action="https://secureacceptance.cybersource.com/pay" method="post" id="bluepaid_payment_form"/>
    <?php
        foreach($params as $name => $value) {
			
			echo "<input type=\"hidden\" id=\"" . $name . "\" name=\"" . $name . "\" value=\"" . $value . "\"/>\n";
        }

        echo "<input type=\"hidden\" id=\"signature\" name=\"signature\" value=\"" . sign($params) . "\"/>\n";
    ?>

<script type="text/javascript">
document.getElementById("bluepaid_payment_form").submit();
</script>

</form>

</body>
</html>
