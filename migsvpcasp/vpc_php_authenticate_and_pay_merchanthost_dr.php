<?php

define('__ROOT__', dirname(dirname(__FILE__)));
require_once(__ROOT__.'/app/Mage.php'); 
umask(0);
Mage::app();
/* -----------------------------------------------------------------------------

 Version 2.0

------------------ Disclaimer --------------------------------------------------

Copyright 2004 Dialect Holdings.  All rights reserved.

This document is provided by Dialect Holdings on the basis that you will treat
it as confidential.

No part of this document may be reproduced or copied in any form by any means
without the written permission of Dialect Holdings.  Unless otherwise expressly
agreed in writing, the information contained in this document is subject to
change without notice and Dialect Holdings assumes no responsibility for any
alteration to, or any error or other deficiency, in this document.

All intellectual property rights in the Document and in all extracts and things
derived from any part of the Document are owned by Dialect and will be assigned
to Dialect on their creation. You will protect all the intellectual property
rights relating to the Document in a manner that is equal to the protection
you provide your own intellectual property.  You will notify Dialect
immediately, and in writing where you become aware of a breach of Dialect's
intellectual property rights in relation to the Document.

The names "Dialect", "QSI Payments" and all similar words are trademarks of
Dialect Holdings and you must not use that name or any similar name.

Dialect may at its sole discretion terminate the rights granted in this
document with immediate effect by notifying you in writing and you will
thereupon return (or destroy and certify that destruction to Dialect) all
copies and extracts of the Document in its possession or control.

Dialect does not warrant the accuracy or completeness of the Document or its
content or its usefulness to you or your merchant customers.   To the extent
permitted by law, all conditions and warranties implied by law (whether as to
fitness for any particular purpose or otherwise) are excluded.  Where the
exclusion is not effective, Dialect limits its liability to $100 or the
resupply of the Document (at Dialect's option).

Data used in examples and sample data files are intended to be fictional and
any resemblance to real persons or companies is entirely coincidental.

Dialect does not indemnify you or any third party in relation to the content or
any use of the content as contemplated in these terms and conditions.

Mention of any product not owned by Dialect does not constitute an endorsement
of that product.

This document is governed by the laws of New South Wales, Australia and is
intended to be legally binding.

-------------------------------------------------------------------------------

Following is a copy of the disclaimer / license agreement provided by RSA:

Copyright (C) 1991-2, RSA Data Security, Inc. Created 1991. All rights reserved.

License to copy and use this software is granted provided that it is identified
as the "RSA Data Security, Inc. MD5 Message-Digest Algorithm" in all material 
mentioning or referencing this software or this function.

License is also granted to make and use derivative works provided that such 
works are identified as "derived from the RSA Data Security, Inc. MD5 
Message-Digest Algorithm" in all material mentioning or referencing the 
derived work.

RSA Data Security, Inc. makes no representations concerning either the 
merchantability of this software or the suitability of this software for any 
particular purpose. It is provided "as is" without express or implied warranty 
of any kind.

These notices must be retained in any copies of any part of this documentation 
and/or software.

-------------------------------------------------------------------------------- 
 
This example assumes that a form has been sent to this example with the
required fields. The example then processes the command and displays the
receipt or error to a HTML page in the users web browser.

NOTE:
=====
  You may have installed the libeay32.dll and ssleay32.dll libraries 
  into your x:\WINNT\system32 directory to run this example.

--------------------------------------------------------------------------------

 @author Dialect Payment Solutions Pty Ltd Group 

------------------------------------------------------------------------------*/

// *********************
// START OF MAIN PROGRAM
// *********************

// Define Constants
// ----------------
// This is secret for encoding the MD5 hash
// This secret will vary from merchant to merchant
// To not create a secure hash, let SECURE_SECRET be an empty string - ""
// $SECURE_SECRET = "secure-hash-secret";
$SECURE_SECRET = "96302DBC17C5F1381263A6BD5D8F4112";

// If there has been a merchant secret set then sort and loop through all the
// data in the Virtual Payment Client response. While we have the data, we can
// append all the fields that contain values (except the secure hash) so that
// we can create a hash and validate it against the secure hash in the Virtual
// Payment Client response.

// NOTE: If the vpc_TxnResponseCode in not a single character then
// there was a Virtual Payment Client error and we cannot accurately validate
// the incoming data from the secure hash. */

// get and remove the vpc_TxnResponseCode code from the response fields as we
// do not want to include this field in the hash calculation
$vpc_Txn_Secure_Hash = $_GET["vpc_SecureHash"];
unset($_GET["vpc_SecureHash"]); 

// set a flag to indicate if hash has been validated
$errorExists = false;

if (strlen($SECURE_SECRET) > 0 && $_GET["vpc_TxnResponseCode"] != "7" && $_GET["vpc_TxnResponseCode"] != "No Value Returned") {

    $md5HashData = $SECURE_SECRET;

    // sort all the incoming vpc response fields and leave out any with no value
    foreach($_GET as $key => $value) {
        if ($key != "vpc_SecureHash" or strlen($value) > 0) {
            $md5HashData .= $value;
        }
    }
    
    // Validate the Secure Hash (remember MD5 hashes are not case sensitive)
	// This is just one way of displaying the result of checking the hash.
	// In production, you would work out your own way of presenting the result.
	// The hash check is all about detecting if the data has changed in transit.
    if (strtoupper($vpc_Txn_Secure_Hash) == strtoupper(md5($md5HashData))) {
        // Secure Hash validation succeeded, add a data field to be displayed
        // later.
		if($_GET["vpc_TxnResponseCode"] == "0")
		{
			try {
			$amount          = $_GET["vpc_Amount"];
			$orderInfo       = $_GET["vpc_OrderInfo"];
			$body = $orderInfo ."  ". $amount ;
			$mail = Mage::getModel('core/email');
			$mail->setToName('KeyInstant Reseller');
			$mail->setToEmail('keyinstant@gmail.com');
			$mail->setBody($body);
			$mail->setSubject('UOB Order');
			$mail->setFromEmail('keyinstant@gmail.com');
			$mail->setFromName("KeyInstant Reseller");
			$mail->setType('text');// You can use 'html' or 'text'
			$mail->send();
			
			$_order = new Mage_Sales_Model_Order();
			$pieces = explode(" ", $orderInfo);
			$_order->loadByIncrementId($pieces[5]);
			$_order->getSendConfirmation(null);
            $_order->sendNewOrderEmail();
			
			}
			catch (Exception $e) {
   
			}
			header( 'Location: http://keyinstant.com/success/' ) ;
			die();
		}
        else
		{
			header( 'Location: http://keyinstant.com/cancel/' ) ;
			die();
		}
		
    } else {
        // Secure Hash validation failed, add a data field to be displayed
        // later.
        //$hashValidated = "<FONT color='#FF0066'><strong>INVALID HASH</strong></FONT>";
        $errorExists = true;
    }
} else {
    // Secure Hash was not validated, add a data field to be displayed later.
    //$hashValidated = "<FONT color='orange'><strong>Not Calculated - No 'SECURE_SECRET' present.</strong></FONT>";
}

//echo $hashValidated;
    
?>
