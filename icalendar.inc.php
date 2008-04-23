<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

	define("CRLF","\r\n");
	
	function getICalHeader() {
		$ical = "BEGIN:VCALENDAR".CRLF;
		$ical .= "VERSION:2.0".CRLF;
		$ical .= "METHOD:PUBLISH".CRLF;
		$ical .= "PRODID:-//Virginia Tech//VTCalendar//EN".CRLF;
		return $ical;
	}
	
	function getICalFooter() {
		$ical = "END:VCALENDAR".CRLF;
		return $ical;
	}
	
	function iCalPrintMultipleLines($text) {
		$ical = "";
		$nl_at_nextspace = 0;
		for ($i=0; $i < strlen($text); $i++) {
			$c = substr($text, $i, 1);
			if ($i>0 && $i/45==floor($i/45)) { $nl_at_nextspace = 1; }
			if ($c==" " && $nl_at_nextspace) { $ical .= " ".CRLF." "; $nl_at_nextspace = 0; }
			elseif ($c==chr(13)) { $ical .= "\\n".CRLF." "; $i++; }
			else { $ical .= $c; }
		}
		
		return $ical;
	} // end: iCalPrintMultipleLines
	
	function getICalFormat(&$event) {
		disassemble_eventtime($event);
	
		$begintime = Timezone2UTC(TIMEZONE_OFFSET, $event['timebegin_year'], $event['timebegin_month'], $event['timebegin_day'], 
												 $event['timebegin_hour'], $event['timebegin_min'], $event['timebegin_ampm']);
		$endtime = Timezone2UTC(TIMEZONE_OFFSET,$event['timeend_year'], $event['timeend_month'], $event['timeend_day'], 
											 $event['timeend_hour'], $event['timeend_min'], $event['timeend_ampm']);
		$dtstart = datetime2ISO8601datetime($begintime['year'], $begintime['month'], $begintime['day'],
																				$begintime['hour'], $begintime['min'],$begintime['ampm']);
		$dtend   = datetime2ISO8601datetime($endtime['year'], $endtime['month'], $endtime['day'],
																				$endtime['hour'], $endtime['min'], $endtime['ampm']);

		$ical = "BEGIN:VEVENT".CRLF;
		$ical.= "DTSTAMP:".$dtstart."Z".CRLF;
		$ical.= "UID:".$event['id']."@".$_SERVER["HTTP_HOST"].CRLF;
		$ical.= "CATEGORIES:".$event['category_name'].CRLF;
		if ($event['wholedayevent']==1) {
			$ical.= "DTSTART;VALUE=DATE:".substr($dtstart,0,8).CRLF;
			$ical.= "DTEND;VALUE=DATE:".substr($dtend,0,8).CRLF;
		}
		else {
			$ical.= "DTSTART:".$dtstart."Z".CRLF;
			$ical.= "DTEND:".$dtend."Z".CRLF;
		}
		$ical.= "SUMMARY:".$event['title'].CRLF;
	
		$ical.= "DESCRIPTION:".CRLF." ";
		if (!empty($event['description'])) {
			$ical.= iCalPrintMultipleLines($event['description']);
			$ical.= "\\n\\n".CRLF;
		}
		if (!empty($event['price'])) {
			$ical.= " ".lang('price').": ";
			$ical.= iCalPrintMultipleLines($event['price']);
			$ical.= "\\n".CRLF;
		}
		if (!empty($event['sponsor_name'])) {
			$ical.= " ".lang('sponsor').": ";
			$ical.= iCalPrintMultipleLines($event['sponsor_name']);
			$ical.= "\\n".CRLF;
		}
		if (!(empty($event['sponsor_url']) || $event['sponsor_url']=="http://")) {
			$ical.= " ".lang('homepage')." ";
			$ical.= iCalPrintMultipleLines($event['sponsor_url']);
			$ical.= "\\n".CRLF;
		}
		if (!empty($event['contact_name'])) {
			$ical.= " ".lang('contact').": ";
			$ical.= iCalPrintMultipleLines($event['contact_name']);
			$ical.= "\\n".CRLF;
		}
		if (!empty($event['contact_phone'])) {
			$ical.= " ".lang('phone').": ";
			$ical.= iCalPrintMultipleLines($event['contact_phone']);
			$ical.= "\\n".CRLF;
		}
		if (!empty($event['contact_email'])) {
			$ical.= " ".lang('email').": ";
			$ical.= iCalPrintMultipleLines($event['contact_email']);
			$ical.= "\\n".CRLF;
		}
		if (!(empty($event['url']) || $event['url']=="http://")) {
			$ical.= " ".lang('for_more_info_visit').":\\n ".CRLF." ";
			$ical.= iCalPrintMultipleLines($event['url']);
			$ical.= "\\n".CRLF;
		}
	
		if (!empty($event['location'])) {
			$ical.= "LOCATION:".$event['location'].CRLF;
		}
		$ical.= "END:VEVENT".CRLF;
		
		return $ical;
	} // end: function getICalFormat
?>