<?php

$myFile = "tmp/log.txt";
$fh = fopen($myFile, 'a+') or die("can't open file");

if ($fh){
    $postdata = file_get_contents("php://input");
	fwrite($fh, print_r("$postdata \n", true));
	fclose($fh);
}

echo "ok";

?>