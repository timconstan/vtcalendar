<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  if (isset($_POST['httpreferer'])) { setVar($httpreferer,$_POST['httpreferer'],'httpreferer'); } else { unset($httpreferer); }
  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['check'])) { setVar($check,$_POST['check'],'check'); } 
	else { 
    if (isset($_GET['check'])) { setVar($check,$_GET['check'],'check'); } 
		else {
  	  unset($check); 
		}
	}
  if (isset($_POST['deleteconfirmed'])) { setVar($deleteconfirmed,$_POST['deleteconfirmed'],'deleteconfirmed'); } else { unset($deleteconfirmed); }
  if (isset($_POST['deletethis'])) { setVar($deletethis,$_POST['deletethis'],'deletethis'); } else { unset($deletethis); }
  if (isset($_POST['deleteall'])) { setVar($deleteall,$_POST['deleteall'],'deleteall'); } else { unset($deleteall); }
  if (isset($_POST['eventid'])) { setVar($eventid,$_POST['eventid'],'eventid'); } else { 
	  if (isset($_GET['eventid'])) { setVar($eventid,$_GET['eventid'],'eventid'); } 
		else { 
		  unset($eventid); 
		}
	}
  if (isset($_POST['detailscaller'])) { setVar($detailscaller,$_POST['detailscaller'],'detailscaller'); } else { unset($detailscaller); }

  $database = DBopen();
  if (!authorized($database)) { exit; }

  if (!isset($httpreferer)) { $httpreferer = $_SERVER["HTTP_REFERER"]; }

  // check that none other than the even owner (or the calendar admin) calls for deletion
  $query = "SELECT sponsorid FROM vtcal_event_public WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'";
  $result = DBQuery($database, $query );
  if ($result->numRows() > 0) { 
  	  $e = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
  	  if (!(
  	        (isset($_SESSION["AUTH_SPONSORID"]) && $_SESSION["AUTH_SPONSORID"] == $e['sponsorid']) || 
  	        !empty($_SESSION["AUTH_ADMIN"])
  	       )) {
           redirect2URL($httpreferer);
           exit;
      }
  }
  else {
    redirect2URL($httpreferer);
    exit;
  }  


  if (isset($cancel)) {
    $target = $httpreferer;
    if (isset($detailscaller)) { $target .= "&detailscaller=$detailscaller"; }
    redirect2URL($target);
    exit;
  }
  
  if (isset($deleteconfirmed)) {
    // get the event title from the database
    $result = DBQuery($database, "SELECT * FROM vtcal_event WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'" ); 
    if ($result->numRows() > 0) { $event  = $result->fetchRow(DB_FETCHMODE_ASSOC,0); }
    else { $event['title']=""; }

    deletefromevent($eventid,$database);
    deletefromevent_public($eventid,$database);
		
		// also delete the copies of an event that have been forwarded to the default calendar
		if ( $_SESSION["CALENDARID"] != "default" ) {
			$query = "DELETE FROM vtcal_event_public WHERE calendarid='default' AND id='".sqlescape($eventid)."'";
			$result = DBQuery($database, $query ); 
		} // end: if ( $_SESSION["CALENDARID"] != "default" )
		
    if (isset($deleteall)) {
      repeatdeletefromevent($event['repeatid'],$database);
      repeatdeletefromevent_public($event['repeatid'],$database);
      deletefromrepeat($event['repeatid'],$database);

  		// also delete the copies of an event that have been forwarded to the default calendar
			if ( $_SESSION["CALENDARID"] != "default" ) {
			  if (!empty($event['repeatid'])) {
					$query = "DELETE FROM vtcal_event_public WHERE calendarid='default' AND repeatid='".sqlescape($event['repeatid'])."'";
					$result = DBQuery($database, $query ); 
				}
			} // end: if ( $_SESSION["CALENDARID"] != "default" )
    } // end: elseif (isset($deleteall))

    // reroute
    if (strpos($httpreferer,"update.php")) {
      redirect2URL("update.php?fbid=edeletesuccess&fbparam=".urlencode(stripslashes($event['title'])));
    }
    else {
      $target = $httpreferer;
      if (isset($detailscaller)) { $target .= "&detailscaller=$detailscaller"; }
      redirect2URL($target);
    }
    exit;
  }

  // read sponsor name from DB
  $result = DBQuery($database, "SELECT name,url FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 
  $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);

  pageheader(lang('delete_event'),
             lang('delete_event'),
             "Update","",$database);
  echo "<BR>";
  box_begin("inputbox",lang('delete_event'));
?>
<FORM method="post" action="deleteevent.php">
<?php
    echo '<INPUT type="hidden" name="httpreferer" value="',$httpreferer,'">',"\n";
    if (isset($detailscaller)) { echo "<INPUT type=\"hidden\" name=\"detailscaller\" value=\"$detailscaller\">\n"; }

    if (isset($check)) { // ask for delete confirmation
      $query = "SELECT e.id AS eventid,e.timebegin,e.timeend,e.sponsorid,e.title,e.location,e.description,e.contact_name,e.contact_email,e.contact_phone,e.price,e.url,e.displayedsponsor,e.displayedsponsorurl,e.wholedayevent,e.repeatid,e.categoryid,c.id,c.name AS category_name FROM vtcal_event_public e, vtcal_category c WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.categoryid = c.id AND e.id='".sqlescape($eventid)."'";
      $result = DBQuery($database, $query );

      if ($result->numRows() > 0) { // display the preview only if there is a corresponding entry in "event"
        $event = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
  
        if (!empty($event['repeatid'])) {
          readinrepeat($event['repeatid'],$event,$repeat,$database);
        }
        else { $repeat['mode'] = 0; }
        disassemble_eventtime($event);

        print_event($event);  
?>
    <BR>
<?php
		if (!empty($event['repeatid'])) {
			echo '<font color="#00AA00">';
			readinrepeat($event['repeatid'],$event,$repeat,$database);
			$repeatdef = repeatinput2repeatdef($event,$repeat);
			printrecurrence($event['timebegin_year'],
											$event['timebegin_month'],
											$event['timebegin_day'],
											$repeatdef);
			echo '</font>';
		}
  } // end: if (numRows() > 0)
  else {
    $repeat['mode'] = 0;
  }
?>
  <BR>
  <BR>
  <B><?php echo lang('delete_event_confirm'); ?></B>
  <BR>
  <BR>
  <INPUT type="hidden" name="eventid" value="<?php echo $eventid; ?>">
  <INPUT type="hidden" name="deleteconfirmed" value="1">
  <INPUT type="submit" name="deletethis" value="<?php echo lang('button_delete_this_event'); ?>">
<?php
  if ($repeat['mode'] > 0) {
    echo '&nbsp;<INPUT type="submit" name="deleteall" value="',lang('button_delete_all_recurrences'),'">';
  }

    } // end: if (isset($check))
?>
  &nbsp;
  <INPUT type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
  <BR>
</FORM>
<?php
  box_end();
  echo "<BR>";
  require("footer.inc.php");
?>