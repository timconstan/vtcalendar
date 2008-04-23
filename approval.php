<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_ADMIN"]) { exit; } // additional security

  if (isset($_POST['approveallevents'])) { setVar($approveallevents,$_POST['approveallevents'],'approveallevents'); } else { unset($approveallevents); }
  if (isset($_POST['eventidlist'])) { setVar($eventidlist,$_POST['eventidlist'],'eventidlist'); } else { unset($eventidlist); }
  if (isset($_GET['approveall'])) { setVar($approveall,$_GET['approveall'],'approveall'); } else { unset($approveall); }
  if (isset($_GET['approvethis'])) { setVar($approvethis,$_GET['approvethis'],'approvethis'); } else { unset($approvethis); }
  if (isset($_GET['reject'])) { setVar($reject,$_GET['reject'],'reject'); } else { unset($reject); }
  if (isset($_POST['eventid'])) { setVar($eventid,$_POST['eventid'],'eventid'); } 
  else {
    if (isset($_GET['eventid'])) { setVar($eventid,$_GET['eventid'],'eventid'); } else { unset($eventid); }
  }
  if (isset($_POST['rejectreason'])) { setVar($rejectreason,$_POST['rejectreason'],'rejectreason'); } else { unset($rejectreason); }
  if (isset($_POST['rejectconfirmedall'])) { setVar($rejectconfirmedall,$_POST['rejectconfirmedall'],'rejectconfirmedall'); } else { unset($rejectconfirmedall); }
  if (isset($_POST['rejectconfirmedthis'])) { setVar($rejectconfirmedthis,$_POST['rejectconfirmedthis'],'rejectconfirmedthis'); } else { unset($rejectconfirmedthis); }


function sendrejectionemail($eventid,$database) {
  // determine sponsor id, name
  $query = "SELECT e.title AS event_title, e.rejectreason AS event_rejectreason, s.name AS sponsor_name, s.email AS sponsor_email, s.id AS sponsorid FROM vtcal_event e, vtcal_sponsor s WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.sponsorid=s.id AND e.id='".sqlescape($eventid)."'";
  $result = DBQuery($database, $query ); 
  $d = $result->fetchRow(DB_FETCHMODE_ASSOC);
  
  $subject = lang('email_submitted_event_rejected');
  $body = lang('email_admin_rejected_event')."\n";
  $body.= $d['event_title']."\n\n";
  $body.= lang('email_reason_for_rejection')."\n";
  $body.= $d['event_rejectreason']."\n\n";
  $body.= lang('email_login_edit_resubmit');

	/* taken out because it would need to be adapted to work for the calendar forwarding
	   feature which it currently does not. also, rejection is extremely rarely used.
		$body.= "You can update the information for this particular event by clicking here:\n";
		if ( isset($_SERVER["HTTPS"]) ) { $body .= "https"; } else { $body .= "http"; } 
		$body.= "://".$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'], "/"))."/";
		$body.= "changeeinfo.php?calendarid=".$_SESSION["CALENDARID"];
		$body.= "&authsponsorid=".$d['sponsorid'];
		$body.= "&eventid=$eventid&httpreferer=update.php";
	*/
	
  sendemail2sponsor($d['sponsor_name'],$d['sponsor_email'],$subject,$body);
} // end: function sendrejectionemail


  // if the "Approve" button was depressed, change the DB
  if (isset($approveallevents)) {
    $eventids=split(",",$eventidlist);
		for ($i=0; $i<count($eventids); $i++) {
			$eventid = $eventids[$i];
			if (!empty($eventid)) {
				$result = DBQuery($database, "SELECT * FROM vtcal_event WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'" );
				$event = $result->fetchRow(DB_FETCHMODE_ASSOC);
				if ($event["approved"]==0) {
//					eventaddslashes($event);
					if (!empty($event['repeatid'])) {
						repeatpublicizeevent($eventid,$event,$database);
					}
					else {
						publicizeevent($eventid,$event,$database);
					}
				}
		  }
		}
  }
  elseif (isset($eventid)) {
    // check if event is marked as "submitted" (to avoid multiple approvals/rejections)
    $query = "SELECT * FROM vtcal_event WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'";
		$result = DBQuery($database, $query ); 
    $event = $result->fetchRow(DB_FETCHMODE_ASSOC);

    if ($event["approved"]==0) {

      if (isset($approvethis)) {
//        eventaddslashes($event);
        publicizeevent($eventid,$event,$database);
      } // end: if (isset($approvethis))
      elseif (isset($approveall)) { // approve all events with the same repeatid
        // read update information, determine repeatid
        repeatpublicizeevent($eventid,$event,$database);
      } // end: elseif (isset($approveall))
      elseif (isset($rejectconfirmedthis)) {
        $result = DBQuery($database, "UPDATE vtcal_event SET approved=-1, rejectreason='".sqlescape($rejectreason)."' WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'" );
        sendrejectionemail($eventid,$database);
      }
      elseif (isset($rejectconfirmedall)) {
        // determine repeatid
        $result = DBQuery($database, "UPDATE vtcal_event SET approved=-1, rejectreason='".sqlescape($rejectreason)."' WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND approved=0 AND repeatid='".sqlescape($event['repeatid'])."'" ); 
        sendrejectionemail($eventid,$database);
      }
      elseif (isset($reject)) { // ask for confirmation, reason for rejection
        include("reject.inc.php");
        exit;
      } // end elseif (isset($reject))
    } // end: if ($event["approved"]==0)
  } // end: if (isset($eventid))

  pageheader(lang('approve_reject_event_updates'),
             lang('approve_reject_event_updates'),
	           "Update","",$database);
  echo "<BR>";
  box_begin("inputbox",lang('approve_reject_event_updates'));

  // print list with events
  $query = "SELECT e.id AS id,e.approved,e.timebegin,e.timeend,e.repeatid,e.sponsorid,e.displayedsponsor,e.displayedsponsorurl,e.title,e.wholedayevent,e.categoryid,e.description,e.location,e.price,e.contact_name,e.contact_phone,e.contact_email,e.url,c.id AS cid,c.name AS category_name,s.id AS sid,s.name AS sponsor_name,s.url AS sponsor_url,s.calendarid AS sponsor_calendarid FROM vtcal_event e, vtcal_category c, vtcal_sponsor s WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.categoryid = c.id AND e.sponsorid = s.id AND e.approved = 0";
  $query.= " ORDER BY e.timebegin asc, e.wholedayevent DESC";
  $result = DBQuery($database, $query ); 
?>
      <FORM method="post" action="update.php">
        <INPUT type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
      </FORM>
<br>
<b><a href="<?php echo $_SERVER["PHP_SELF"]; ?>"><?php echo lang('refresh_display'); ?></a></b><br>
<?php
  if ($result->numRows() > 0 ) {
?>
<br>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<INPUT type="submit" name="approveallevents" value="<?php echo lang('approve_all_events'); ?>">
<input type="hidden" name="eventidlist" value="<?php
  // read first event if one exists
  $ievent = 0;
  while ($ievent < $result->numRows()) {
    if ( $ievent > 0 ) { echo ","; }
  	$event = $result->fetchRow(DB_FETCHMODE_ASSOC, $ievent);
	  echo $event["id"];
  	$ievent++;
  }
?>">
</form>
<table border="0" cellspacing="0" cellpadding="4">
  <tr bgcolor="#CCCCCC">
    <td bgcolor="#CCCCCC"><b><?php echo lang('date'),"/",lang('time'); ?></b></td>
    <td bgcolor="#CCCCCC"><b><?php echo lang('category'),": ",lang('title'),"/",lang('description'); ?></b></td>
    <td bgcolor="#CCCCCC"><b><?php echo lang('sponsor'); ?></b></td>
    <td bgcolor="#CCCCCC"><b><?php echo lang('location'); ?></b></td>
    <td bgcolor="#CCCCCC"><b><?php echo lang('price'); ?></b></td>
    <td bgcolor="#CCCCCC"><b><?php echo lang('contact'); ?></b></td>
    <td bgcolor="#CCCCCC">&nbsp;</td>
  </tr>
<?php
  $color = "#eeeeee";
  for ($i=0; $i<$result->numRows(); $i++) {
    $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
    disassemble_eventtime($event);
		
		// keep track of repeat id and print recurring events only once
  	if (!empty($event['repeatid']) ) { 
		  if ( isset($recurring_exists) && array_key_exists ($event['repeatid'],$recurring_exists) ) { continue; }
			else { 
			  // remember this recurring event
				$recurring_exists[$event['repeatid']] = $event['repeatid']; 
			}
		}

		if ( $color == "#eeeeee" ) { $color = "#ffffff"; } else { $color = "#eeeeee"; }
?>	
  <tr bgcolor="<?php echo $color; ?>">
    <td bgcolor="<?php echo $color; ?>" valign="top"><?php 
  // output date
  echo Day_of_Week_Abbreviation(Day_of_Week($event['timebegin_month'],$event['timebegin_day'],$event['timebegin_year']));
  echo ", ";
	echo substr(Month_to_Text($event['timebegin_month']),0,3)," ",$event['timebegin_day'],", ",$event['timebegin_year'];
  echo "<br>\n";
  if ($event['wholedayevent']==0) {
		echo timestring($event['timebegin_hour'],$event['timebegin_min'],$event['timebegin_ampm']),"-";
		if (endingtime_specified($event)) { // event has an explicit ending time
			echo timestring($event['timeend_hour'],$event['timeend_min'],$event['timeend_ampm']);
		}
  }
	else {
	  echo lang('all_day');
	}

	if (!empty($event['repeatid'])) {
		echo "<br>\n";
		echo '<font color="#00AA00">';
		readinrepeat($event['repeatid'],$event,$repeat,$database);
		$repeatdef = repeatinput2repeatdef($event,$repeat);
		printrecurrence($event['timebegin_year'],
										$event['timebegin_month'],
										$event['timebegin_day'],
										$repeatdef);
		echo '</font>';
	}
?></td>
    <td bgcolor="<?php echo $color; ?>" valign="top"><?php 
  echo $event['category_name'],": "; 
	echo "<b>",$event['title'],"</b>\n"; 
	echo "<br>",$event['description'];
?></td>
    <td bgcolor="<?php echo $color; ?>" valign="top"><?php 
  echo $event['displayedsponsor'];
	if ( $event['displayedsponsor'] != $event['sponsor_name'] ) {
	  echo ' (<font color="#ff0000"><b>',$event['sponsor_name'],'</b></font>)';
	}

  if ($_SESSION["CALENDARID"] != $event['sponsor_calendarid']) {
	  $q = "SELECT name FROM vtcal_calendar WHERE id='".sqlescape($event['sponsor_calendarid'])."'";
    $r = DBQuery($database, $q ); 
  	$c = $r->fetchRow(DB_FETCHMODE_ASSOC, 0);
		echo "<br>(<font color=\"#339933\"><b>".$c['name']."</b></font>)";
	}
?>
		</td>
    <td bgcolor="<?php echo $color; ?>" valign="top"><?php echo $event['location']; ?></td>
    <td bgcolor="<?php echo $color; ?>" valign="top"><?php echo $event['price']; ?></td>
    <td bgcolor="<?php echo $color; ?>" valign="top" nowrap><?php 
 if (!empty($event['contact_name']) ) { echo $event['contact_name'],"<br>"; } 
 if (!empty($event['contact_email']) ) { 
   echo '<img src="images/email.gif" width="20" height="20" alt="e-mail">';
   echo "<a href=\"mailto:",$event['contact_email'],"\">",$event['contact_email'],"</a><br>"; 
 } 
 if (!empty($event['contact_phone']) ) { 
  echo '<img src="images/phone.gif" width="20" height="20">';
  echo $event['contact_phone'],"<br>"; 
 }	 
?></td>
    <td bgcolor="<?php echo $color; ?>" valign="top" nowrap><a href="approval.php?<?php 
  if (!empty($event['repeatid'])) {		
	  echo "approveall=1";
	}
	else {
	  echo "approvethis=1";
	}
		
?>&eventid=<?php echo $event['id']; ?>"><?php echo lang('approve'); ?></a>&nbsp; 
<a href="approval.php?reject=1&eventid=<?php echo $event['id']; ?>"><?php echo lang('reject'); ?></a>&nbsp; 
<a href="changeeinfo.php?update=1&eventid=<?php echo $event['id']; ?>"><?php echo lang('edit'); ?></a></td>
  </tr>
<?php
  } // end: for ($i=0; $i<$result->numRows(); $i++)
?>	
  <tr bgcolor="#CCCCCC">
    <td colspan="7" bgcolor="#CCCCCC">&nbsp;</td>
  </tr>
</table>
<br>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>
<?php
  } // end: if ($result->numRows() > 0 )
  box_end();
  echo "<BR>";
  require("footer.inc.php");
?>