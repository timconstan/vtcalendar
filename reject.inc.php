<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

  $database = DBopen();
  pageheader(lang('reject_event_update'),
             lang('reject_event_update'),
   	         "Update","",$database);
  echo "<BR>";
  box_begin("inputbox",lang('reject_event_update'));

  $query = "SELECT e.id AS id,e.timebegin,e.timeend,e.repeatid,e.sponsorid,e.displayedsponsor,e.displayedsponsorurl,e.title,e.wholedayevent,e.categoryid,e.description,e.location,e.price,e.contact_name,e.contact_phone,e.contact_email,e.url,c.id AS cid,c.name AS category_name,s.id AS sid,s.name AS sponsor_name,s.url AS sponsor_url FROM vtcal_event e, vtcal_category c, vtcal_sponsor s WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND s.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.categoryid=c.id AND e.sponsorid=s.id AND e.id='".sqlescape($eventid)."'";
  $result = DBQuery($database, $query ); 
  $event = $result->fetchRow(DB_FETCHMODE_ASSOC,0);

  disassemble_eventtime($event);
  $event['css'] = datetoclass($event['timebegin_month'],$event['timebegin_day'],$event['timebegin_year']);
  $event['color'] = datetocolor($event['timebegin_month'],$event['timebegin_day'],$event['timebegin_year'],$colorpast,$colortoday,$colorfuture);

  echo '<SPAN class="bodytext">';
  if (!empty($event['repeatid'])) {
    readinrepeat($event['repeatid'],$event,$repeat,$database);
  	echo lang('recurring_event'),": ";
    $repeatdef = repeatinput2repeatdef($event,$repeat);
    printrecurrence($event['timebegin_year'],
                    $event['timebegin_month'],
                    $event['timebegin_day'],
                    $repeatdef);
    echo "<BR>";
  }
  echo '</SPAN>';
?>
<BR>
<FORM method="post" action="approval.php">
  <?php echo lang('reason_for_rejection'); ?>
  <BR>
  <TEXTAREA name="rejectreason" rows="2" cols="50" wrap=virtual></TEXTAREA>
  <INPUT type="hidden" name="eventid" value="<?php echo $eventid; ?>">
  <BR>
<?php
  if (!empty($event['repeatid']) && num_unapprovedevents($event['repeatid'],$database) > 1) {
    echo '<INPUT type="submit" name="rejectconfirmedall" value="&nbsp;&nbsp;&nbsp;&nbsp;OK&nbsp;&nbsp;&nbsp;&nbsp;">';
  } 
	else {
    echo '<INPUT type="submit" name="rejectconfirmedthis" value="&nbsp;&nbsp;&nbsp;&nbsp;OK&nbsp;&nbsp;&nbsp;&nbsp;">';
  } 
?>
  <INPUT type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
</FORM>
<BR>
<?php
  box_end();
  echo "<BR>";
	require("footer.inc.php");
?>
