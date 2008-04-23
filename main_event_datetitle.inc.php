<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

  // read event from the DB
  $query = "SELECT e.id AS eventid,e.timebegin,e.timeend,e.sponsorid,e.title,e.location,e.description,e.contact_name,e.contact_email,e.contact_phone,e.price,e.url,e.displayedsponsor,e.displayedsponsorurl,e.wholedayevent,e.categoryid,c.id,c.name AS category_name FROM vtcal_event_public e, vtcal_category c WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.categoryid=c.id AND e.id='".sqlescape($eventid)."'";
  $result = DBQuery($database, $query );
  if ( $result->numRows() > 0 ) {
    $event = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
 	  disassemble_eventtime($event);	
    $event_timebegin  = timestamp2datetime($event['timebegin']);
    $event_timeend    = timestamp2datetime($event['timeend']);


echo day_view_date_format($event_timebegin['day'], Day_of_Week_to_Text(Day_of_Week($event_timebegin['month'],$event_timebegin['day'],$event_timebegin['year'])),Month_to_Text($event_timebegin['month']),$event_timebegin['year']);
}
?>