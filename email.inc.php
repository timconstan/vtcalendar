<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

	// code adapted from php.net, "mail" help page, author: tstrum@salter.com
	function emailaddressok($email) {
		if (eregi("^[_\.0-9a-z-]+@([0-9a-z][-0-9a-z\.]+)\.([a-z]{2,3}$)", $email, $check)) {
	/*	
		if (getmxrr($check[1].".".$check[2],$temp)) { // doesn't work on Windows
				return 1; // "Valid - ".$check[1].".".$check[2]
			}
			return 0; // No MX for $check[1].".".$check[2]
	*/
			return 1;
		}
		else {
			return 0; // Badly formed address
		}
	} // end: function emailaddressok
	
	// sends an email
	function sendemail($toName,$toAddress,$fromName,$fromAddress,$subject,$body) {
		if (emailaddressok($toAddress)) {
			mail($toName." <".$toAddress.">", 
					 trim($subject), 
					 trim($body), 
					 "From: ".$fromName." <".$fromAddress.">\nContent-type: text/plain; charset=us-ascii");
			return 1;
		}
		else {
			return 0;
		}
	} // end: Function sendemail
?>