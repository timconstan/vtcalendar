<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');
  require_once("inputedata.inc.php");

  if (isset($_POST['choosetemplate'])) { setVar($choosetemplate,$_POST['choosetemplate'],'choosetemplate'); } else { unset($choosetemplate); }
  if (isset($_POST['preview'])) { setVar($preview,$_POST['preview'],'preview'); } else { unset($preview); }
  if (isset($_POST['savethis'])) { setVar($savethis,$_POST['savethis'],'savethis'); } else { unset($savethis); }
  if (isset($_POST['edit'])) { setVar($edit,$_POST['edit'],'edit'); } else { unset($edit); }
  if (isset($_POST['eventid'])) { setVar($eventid,$_POST['eventid'],'eventid'); } 
	else { 
	  if (isset($_GET['eventid'])) { setVar($eventid,$_GET['eventid'],'eventid'); } 
		else {
  		unset($eventid); 
		}
	}
  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['copy'])) { setVar($copy,$_POST['copy'],'copy'); } 
	else { 
	  if (isset($_GET['copy'])) { setVar($copy,$_GET['copy'],'copy'); } 
		else {
  	  unset($copy); 
		}
	}
  if (isset($_POST['check'])) { setVar($check,$_POST['check'],'check'); } else { unset($check); }
  if (isset($_POST['templateid'])) { setVar($templateid,$_POST['templateid'],'templateid'); } else { unset($templateid); }
  if (isset($_POST['httpreferer'])) { setVar($httpreferer,$_POST['httpreferer'],'httpreferer'); } else { unset($httpreferer); }
  if (isset($_GET['timebegin_year'])) { setVar($timebegin_year,$_GET['timebegin_year'],'timebegin_year'); } else { unset($timebegin_year); }
  if (isset($_GET['timebegin_month'])) { setVar($timebegin_month,$_GET['timebegin_month'],'timebegin_month'); } else { unset($timebegin_month); }
  if (isset($_GET['timebegin_day'])) { setVar($timebegin_day,$_GET['timebegin_day'],'timebegin_day'); } else { unset($timebegin_day); }
  if (isset($_POST['repeat'])) {
		if (isset($_POST['repeat']['mode'])) { setVar($repeat['mode'],$_POST['repeat']['mode'],'mode'); } else { unset($repeat['mode']); }
		if (isset($_POST['repeat']['interval1'])) { setVar($repeat['interval1'],$_POST['repeat']['interval1'],'interval1'); } else { unset($repeat['interval1']); }
		if (isset($_POST['repeat']['interval2'])) { setVar($repeat['interval2'],$_POST['repeat']['interval2'],'interval2'); } else { unset($repeat['interval2']); }
		if (isset($_POST['repeat']['frequency1'])) { setVar($repeat['frequency1'],$_POST['repeat']['frequency1'],'frequency1'); } else { unset($repeat['frequency1']); }
		if (isset($_POST['repeat']['frequency2modifier1'])) { setVar($repeat['frequency2modifier1'],$_POST['repeat']['frequency2modifier1'],'frequency2modifier1'); } else { unset($repeat['frequency2modifier1']); }
		if (isset($_POST['repeat']['frequency2modifier2'])) { setVar($repeat['frequency2modifier2'],$_POST['repeat']['frequency2modifier2'],'frequency2modifier2'); } else { unset($repeat['frequency2modifier2']); }
  }
	else {
	  unset($repeat);
	}
  if (isset($_POST['event'])) {
		if (isset($_POST['event']['timebegin_year'])) { setVar($event['timebegin_year'],$_POST['event']['timebegin_year'],'timebegin_year'); } else { unset($event['timebegin_year']); }
		if (isset($_POST['event']['timebegin_month'])) { setVar($event['timebegin_month'],$_POST['event']['timebegin_month'],'timebegin_month'); } else { unset($event['timebegin_month']); }
		if (isset($_POST['event']['timebegin_day'])) { setVar($event['timebegin_day'],$_POST['event']['timebegin_day'],'timebegin_day'); } else { unset($event['timebegin_day']); }
		if (isset($_POST['event']['timebegin_hour'])) { setVar($event['timebegin_hour'],$_POST['event']['timebegin_hour'],'timebegin_hour'); } else { unset($event['timebegin_hour']); }
		if (isset($_POST['event']['timebegin_min'])) { setVar($event['timebegin_min'],$_POST['event']['timebegin_min'],'timebegin_min'); } else { unset($event['timebegin_min']); }
		if (isset($_POST['event']['timebegin_ampm'])) { setVar($event['timebegin_ampm'],$_POST['event']['timebegin_ampm'],'timebegin_ampm'); } else { unset($event['timebegin_ampm']); }
		if (isset($_POST['event']['timeend_year'])) { setVar($event['timeend_year'],$_POST['event']['timeend_year'],'timeend_year'); } else { unset($event['timeend_year']); }
		if (isset($_POST['event']['timeend_month'])) { setVar($event['timeend_month'],$_POST['event']['timeend_month'],'timeend_month'); } else { unset($event['timeend_month']); }
		if (isset($_POST['event']['timeend_day'])) { setVar($event['timeend_day'],$_POST['event']['timeend_day'],'timeend_day'); } else { unset($event['timeend_day']); }
		if (isset($_POST['event']['timeend_hour'])) { setVar($event['timeend_hour'],$_POST['event']['timeend_hour'],'timeend_hour'); } else { unset($event['timeend_hour']); }
		if (isset($_POST['event']['timeend_min'])) { setVar($event['timeend_min'],$_POST['event']['timeend_min'],'timeend_min'); } else { unset($event['timeend_min']); }
		if (isset($_POST['event']['timeend_ampm'])) { setVar($event['timeend_ampm'],$_POST['event']['timeend_ampm'],'timeend_ampm'); } else { unset($event['timeend_ampm']); }
		if (isset($_POST['event']['wholedayevent'])) { setVar($event['wholedayevent'],$_POST['event']['wholedayevent'],'wholedayevent'); } else { unset($event['wholedayevent']); }
		if (isset($_POST['event']['categoryid'])) { setVar($event['categoryid'],$_POST['event']['categoryid'],'categoryid'); } else { unset($event['categoryid']); }
		if (isset($_POST['event']['title'])) { setVar($event['title'],$_POST['event']['title'],'title'); } else { unset($event['title']); }
		if (isset($_POST['event']['location'])) { setVar($event['location'],$_POST['event']['location'],'location'); } else { unset($event['location']); }
		if (isset($_POST['event']['price'])) { setVar($event['price'],$_POST['event']['price'],'price'); } else { unset($event['price']); }
		if (isset($_POST['event']['description'])) { setVar($event['description'],$_POST['event']['description'],'description'); } else { unset($event['description']); }
		if (isset($_POST['event']['url'])) { setVar($event['url'],$_POST['event']['url'],'url'); } else { unset($event['url']); }
		if (isset($_POST['event']['displayedsponsor'])) { setVar($event['displayedsponsor'],$_POST['event']['displayedsponsor'],'displayedsponsor'); } else { unset($event['displayedsponsor']); }
		if (isset($_POST['event']['displayedsponsorurl'])) { setVar($event['displayedsponsorurl'],$_POST['event']['displayedsponsorurl'],'url'); } else { unset($event['displayedsponsorurl']); }
		if (isset($_POST['event']['showincategory'])) { setVar($event['showincategory'],$_POST['event']['showincategory'],'categoryid'); } else { unset($event['showincategory']); }
		if (isset($_POST['event']['showondefaultcal'])) { setVar($event['showondefaultcal'],$_POST['event']['showondefaultcal'],'showondefaultcal'); } else { unset($event['showondefaultcal']); }
		if (isset($_POST['event']['contact_name'])) { setVar($event['contact_name'],$_POST['event']['contact_name'],'contact_name'); } else { unset($event['contact_name']); }
		if (isset($_POST['event']['contact_phone'])) { setVar($event['contact_phone'],$_POST['event']['contact_phone'],'contact_phone'); } else { unset($event['contact_phone']); }
		if (isset($_POST['event']['contact_email'])) { setVar($event['contact_email'],$_POST['event']['contact_email'],'contact_email'); } else { unset($event['contact_email']); }
		if (isset($_POST['event']['repeatid'])) { setVar($event['repeatid'],$_POST['event']['repeatid'],'repeatid'); } else { unset($event['repeatid']); }
  }
	else {
    unset($event);	
	}
	
  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!isset($httpreferer)) { $httpreferer = $_SERVER["HTTP_REFERER"]; }

  // check that none other than the even owner (or the calendar admin) calls for edit
  if (!empty($eventid)) {
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
  }

function passeventvalues(&$event,$sponsorid,&$repeat) {
  // pass the values
//  echo '<INPUT type="hidden" name="event[rejectreason]" value="',HTMLSpecialChars($event['rejectreason']),"\">\n";
  echo '<INPUT type="hidden" name="event[timebegin_year]" value="',HTMLSpecialChars($event['timebegin_year']),"\">\n";
  echo '<INPUT type="hidden" name="event[timebegin_month]" value="',HTMLSpecialChars($event['timebegin_month']),"\">\n";
  echo '<INPUT type="hidden" name="event[timebegin_day]" value="',HTMLSpecialChars($event['timebegin_day']),"\">\n";
  echo '<INPUT type="hidden" name="event[timebegin_hour]" value="',HTMLSpecialChars($event['timebegin_hour']),"\">\n";
  echo '<INPUT type="hidden" name="event[timebegin_min]" value="',HTMLSpecialChars($event['timebegin_min']),"\">\n";
  echo '<INPUT type="hidden" name="event[timebegin_ampm]" value="',HTMLSpecialChars($event['timebegin_ampm']),"\">\n";
  echo '<INPUT type="hidden" name="event[timeend_year]" value="',HTMLSpecialChars($event['timeend_year']),"\">\n";
  echo '<INPUT type="hidden" name="event[timeend_month]" value="',HTMLSpecialChars($event['timeend_month']),"\">\n";
  echo '<INPUT type="hidden" name="event[timeend_day]" value="',HTMLSpecialChars($event['timeend_day']),"\">\n";
  echo '<INPUT type="hidden" name="event[timeend_hour]" value="',HTMLSpecialChars($event['timeend_hour']),"\">\n";
  echo '<INPUT type="hidden" name="event[timeend_min]" value="',HTMLSpecialChars($event['timeend_min']),"\">\n";
  echo '<INPUT type="hidden" name="event[timeend_ampm]" value="',HTMLSpecialChars($event['timeend_ampm']),"\">\n";
  if (!empty($event['repeatid'])) {
		echo '<INPUT type="hidden" name="event[repeatid]" value="',$event['repeatid'],"\">\n";
	}
  echo '<INPUT type="hidden" name="event[sponsorid]" value="',HTMLSpecialChars($event['sponsorid']),"\">\n";
  echo '<INPUT type="hidden" name="event[title]" value="',HTMLSpecialChars(stripslashes($event['title'])),"\">\n";
  echo '<INPUT type="hidden" name="event[wholedayevent]" value="',HTMLSpecialChars($event['wholedayevent']),"\">\n";
  echo '<INPUT type="hidden" name="event[categoryid]" value="',HTMLSpecialChars($event['categoryid']),"\">\n";
  echo '<INPUT type="hidden" name="event[description]" value="',HTMLSpecialChars(stripslashes($event['description'])),"\">\n";
  echo '<INPUT type="hidden" name="event[location]" value="',HTMLSpecialChars(stripslashes($event['location'])),"\">\n";
  echo '<INPUT type="hidden" name="event[price]" value="',HTMLSpecialChars(stripslashes($event['price'])),"\">\n";
  echo '<INPUT type="hidden" name="event[contact_name]" value="',HTMLSpecialChars(stripslashes($event['contact_name'])),"\">\n";
  echo '<INPUT type="hidden" name="event[contact_phone]" value="',HTMLSpecialChars(stripslashes($event['contact_phone'])),"\">\n";
  echo '<INPUT type="hidden" name="event[contact_email]" value="',HTMLSpecialChars(stripslashes($event['contact_email'])),"\">\n";
  echo '<INPUT type="hidden" name="event[url]" value="',HTMLSpecialChars(stripslashes($event['url'])),"\">\n";
  echo '<INPUT type="hidden" name="event[displayedsponsor]" value="',HTMLSpecialChars(stripslashes($event['displayedsponsor'])),"\">\n";
  echo '<INPUT type="hidden" name="event[displayedsponsorurl]" value="',HTMLSpecialChars(stripslashes($event['displayedsponsorurl'])),"\">\n";
  
	if ($_SESSION["CALENDARID"]=='default') {
    $event['showondefaultcal'] = 0;	
    $event['showincategory'] = 0;	
	}
	echo '<INPUT type="hidden" name="event[showondefaultcal]" value="',HTMLSpecialChars($event['showondefaultcal']),"\">\n";
  echo '<INPUT type="hidden" name="event[showincategory]" value="',HTMLSpecialChars($event['showincategory']),"\">\n";
  
	echo '<INPUT type="hidden" name="repeat[mode]" value="',HTMLSpecialChars($repeat['mode']),'">';
 	if (!empty($repeat['frequency1'])) { echo '<INPUT type="hidden" name="repeat[frequency1]" value="',$repeat['frequency1'],'">',"\n"; }
  if (!empty($repeat['interval1'])) { echo '<INPUT type="hidden" name="repeat[interval1]" value="',$repeat['interval1'],'">',"\n"; }
  if (!empty($repeat['interval2'])) { echo '<INPUT type="hidden" name="repeat[interval2]" value="',$repeat['interval2'],'">',"\n"; }
  if (!empty($repeat['frequency2modifier1'])) { echo '<INPUT type="hidden" name="repeat[frequency2modifier1]" value="',$repeat['frequency2modifier1'],'">',"\n"; }
  if (!empty($repeat['frequency2modifier2'])) { echo '<INPUT type="hidden" name="repeat[frequency2modifier2]" value="',$repeat['frequency2modifier2'],'">',"\n"; }
} // end: function passeventvalues

// test if the recurrence info was changed, return true if it was
// not used because changing only one instance of a recurring event was too error-prone
/*
function recurrenceschanged($repeatid,&$repeat,&$event,$database) {
  $repeat['startdate'] = datetime2timestamp($event['timebegin_year'],$event['timebegin_month'],$event['timebegin_day'],0,0,"am");
  $repeat['enddate'] = datetime2timestamp($event['timeend_year'],$event['timeend_month'],$event['timeend_day'],0,0,"am");

  $query = "SELECT * FROM vtcal_event_repeat WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($repeatid)."'";
  $result = DBQuery($database, $query ); 
  $r = $result->fetchRow(DB_FETCHMODE_ASSOC,0);

  return ($r['repeatdef']!=$repeat['repeatdef']) ||
         (substr($r['startdate'],0,10)!=substr($repeat['startdate'],0,10)) ||
         (substr($r['enddate'],0,10)!=substr($repeat['enddate'],0,10));
} // end: function recurrenceschanged
*/

function insertrecurrences($repeatid,&$event,&$repeatlist,$database) {
  $i = 0;
  while ($dateJD = each($repeatlist)) {
    $i++;
    $date = Decode_Date_US(JDToJulian($dateJD['value']));
    $event['timebegin_month'] = $date['month'];
    $event['timebegin_day']   = $date['day'];
    $event['timebegin_year']  = $date['year'];
    $event['timeend_month'] = $date['month'];
    $event['timeend_day']   = $date['day'];
    $event['timeend_year']  = $date['year'];
    assemble_eventtime($event);

    // insert event
    $eventidext = "";
	  if ($i<1000) {
      if ($i<100) {
	      if ($i<10) { 
		      $eventidext .= "0"; 
		    }
        $eventidext .= "0";
	    }
	    $eventidext .= "0";
	  }
    $eventidext .= $i;

    insertintoevent($repeatid."-".$eventidext,$event,$database);
  } // end: while ($dateJD = each($repeatlist))
} // end: function insertrecurrences

function savechangesbuttons(&$event,&$repeat,$database) {
  echo '<INPUT type="submit" name="savethis" value="',lang('save_changes'),'"> ';
/*
  if ($repeat['mode'] > 0 && !empty($event['repeatid'])) {
    if (!recurrenceschanged($event['repeatid'],$repeat,$event,$database)) {
      echo '<INPUT type="submit" name="saveall" value="Save changes for ALL recurrences"><BR><BR>';
    }
  }
*/
} // end: function savechangesbuttons

function inputeventbuttons(&$event,&$repeat,$database) {
//  savechangesbuttons($event,$repeat,$database);
  echo '<INPUT type="submit" name="preview" value="',lang('preview_event'),'"> ';
  echo '<INPUT type="submit" name="cancel" value="',lang('cancel_button_text'),'">';
} // end: inputeventbuttons


  if (isset($cancel)) {
    $target = $httpreferer;
    // forward the page that details.php was called from as a parameter
    if (isset($detailscaller)) { $target .= "&detailscaller=$detailscaller"; }
    redirect2URL($target);
    exit;
  };

  // pass on the event id in the update mode
  if (isset($eventid)) { $event['id'] = $eventid; }

  $eventvalid = checkevent($event,$repeat);

  if (empty($event['sponsorid'])) {
    $event['sponsorid']=$_SESSION["AUTH_SPONSORID"];
  }
  
  if ($repeat['mode']=="0") { settimeenddate2timebegindate($event); } 
	if ($repeat['mode'] > 0 && !empty($repeat['interval1'])) {
		$repeat['repeatdef'] = repeatinput2repeatdef($event,$repeat);
	}

  // check if user chose to reset the sponsorname/url to the default values
  $result = DBQuery($database, "SELECT * FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($event['sponsorid'])."'" ); 
  $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
  if (isset($defaultdisplayedsponsor) || isset($defaultallsponsor)) {
    $event['displayedsponsor']=$sponsor['name'];
  }
  if (isset($defaultdisplayedsponsorurl) || isset($defaultallsponsor)) {
    $event['displayedsponsorurl']=$sponsor['url'];
  }

  if ((isset($savethis) || isset($saveall)) && $eventvalid) { // save event into DB

    // if event is a "one-time" event then the ending date equals the starting date
    if ($repeat['mode'] == 0) {
      $event['timeend_year']=$event['timebegin_year'];
      $event['timeend_month']=$event['timebegin_month'];
      $event['timeend_day']=$event['timebegin_day'];
    }

    // if event is a "whole day event" than set time to 12am
    if ($event['wholedayevent']==1) {
      $event['timebegin_hour']=12;
      $event['timebegin_min']=0;
      $event['timebegin_ampm']="am";
      $event['timeend_hour']=11;
      $event['timeend_min']=59;
      $event['timeend_ampm']="pm";
    }

    assemble_eventtime($event);

    // make a list containing all the resulting dates (in case there are any)
    unset($repeatlist);
    if ($repeat['mode']>=1 && $repeat['mode']<=2) { $repeatlist = producerepeatlist($event,$repeat); }

    if (isset($eventid) && !isset($copy)) { // update an existing event; "copy" is like adding a new event

      if (empty($event['repeatid'])) { // before it was a single event
        if (sizeof($repeatlist) > 0) { // the event has recurrences
          // delete the old single event
          deletefromevent($eventid,$database);

      	  // insert recurrences
	        $event['repeatid'] = $eventid; // = getNewEventId();
	        insertintorepeat($event['repeatid'],$event,$repeat,$database);
          insertrecurrences($event['repeatid'],$event,$repeatlist,$database);

        } // end: if (sizeof($repeatlist) > 0)
        else { // the event is a single event
          if ($repeat['mode']>=1 && $repeat['mode']<=2) { // it is a recurring event but has no real recurrences
            // delete the event completely
            deletefromevent($eventid,$database);
      	  }
          else { // it's a single event
            $event['repeatid']="";
  	        updateevent($eventid,$event,$database);
	        }
        } // end else: if (sizeof($repeatlist) > 0)
      } // end: if ($event[repeatid] == 0)
      else { // it had recurrences before
        if (!empty($repeatlist)) { // the event has recurrences
          if (isset($saveall) ||
             (isset($savethis) /* && recurrenceschanged($event['repeatid'],$repeat,$event,$database) */)
       	     ) { // apply the changes to all recurrences
         	  // delete the old events
            repeatdeletefromevent($event['repeatid'],$database);

            // insert recurrences
            updaterepeat($event['repeatid'],$event,$repeat,$database);
            insertrecurrences($event['repeatid'],$event,$repeatlist,$database);
      	  } // end: if (isset($saveall) || ...)
          elseif (isset($savethis)) { // apply the changes only to one recurrence (recurrence pattern hasn't changed)
            updateevent($eventid,$event,$database);
	        } // end elseif: (isset($savethis))
        } // end: if (sizeof($repeatlist) > 0)
        else { // the event had recurrences before but now is a single event
          if ($repeat['mode']>=1 && $repeat['mode']<=2) { // it is a recurring event but has no real recurrences
            // delete the event completely
            repeatdeletefromevent($event['repeatid'],$database);
            deletefromrepeat($event['repeatid'],$database);
       	  } // end: if ($repeat['mode']>=1 && $repeat['mode']<=2)
          else { // it's a single event
						deletefromrepeat($event['repeatid'],$database);
       	    $oldrepeatid=$event['repeatid'];
						$eventid=$event['repeatid']; // added to avoid "...-0001" in eventid if it's not repeating
	          $event['repeatid']="";
            insertintoevent($eventid,$event,$database);
      	    
						// delete all old recurring events but one
            repeatdeletefromevent($oldrepeatid,$database);
            repeatdeletefromevent_public($oldrepeatid,$database);
	        } // end else: if ($repeat[mode]>=1 && $repeat[mode]<=2)
        }  // end else: if (sizeof($repeatlist) > 0)
      } // end else: if ($event[repeatid] == 0)

      // whatever the "admin" edits gets approved right away
      if ($_SESSION["AUTH_ADMIN"]) {
	      if (!empty($event['repeatid'])) { repeatpublicizeevent($eventid,$event,$database); }
        else { publicizeevent($eventid,$event,$database); }
      }

      // reroute
      if (strpos($httpreferer,"update.php")) {
        redirect2URL("update.php?fbid=eupdatesuccess&fbparam=".urlencode(stripslashes($event['title'])));
        exit;    
			}
      else {
        $target = $httpreferer;
        if (isset($detailscaller)) { $target .= "&detailscaller=".$detailscaller; }
        redirect2URL($target);
				exit;
      }
    } // end: if (isset($eventid) && !isset($copy))
    else { // insert as new event(s) or copy an event
      if (sizeof($repeatlist) > 0) { // save multiple events
        $event['repeatid'] = getNewEventId();
	      insertintorepeat($event['repeatid'],$event,$repeat,$database);
	      insertrecurrences($event['repeatid'],$event,$repeatlist,$database);
        $eventid = "";
      }
      else { // save just one event
        $event['repeatid'] = ""; // no recurrences
		    $eventid = getNewEventId();
        insertintoevent($eventid,$event,$database);
      }
      $event['id'] = $eventid;

      // whatever the "admin" edits gets approved right away
      if ($_SESSION["AUTH_ADMIN"]) {
      	if (!empty($event['repeatid'])) { repeatpublicizeevent($eventid,$event,$database); }
        else { publicizeevent($eventid,$event,$database); }
      }

      // reroute
      if (strpos($httpreferer,"update.php")) {
        redirect2URL("update.php?fbid=eaddsuccess&fbparam=".urlencode(stripslashes($event['title'])));
        exit;
			}
      else {
        $target = $httpreferer;
        if (isset($detailscaller)) { $target .= "&detailscaller=".$detailscaller; }
        redirect2URL($target);
				exit;
      }
    }
  } // end: if ((isset($saveall) || ...)

  // read sponsor name from DB
  $result = DBQuery($database, "SELECT name,url FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 
  $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);

  if (isset($check) && $eventvalid && isset($preview)) { // display preview
		pageheader(lang('preview_event'),
							 lang('preview_event'),
							 "Update","",$database);
	
		// determine the text representation in the form "MM/DD/YYYY" and the day of the week
		$day['text'] = Encode_Date_US($event['timebegin_month'],$event['timebegin_day'],$event['timebegin_year']);
		$day['dow_text'] = Day_of_Week_Abbreviation(Day_of_Week($event['timebegin_month'],$event['timebegin_day'],$event['timebegin_year']));
		assemble_eventtime($event);
		$event['css'] = datetoclass($event['timebegin_month'],$event['timebegin_day'],$event['timebegin_year']);
		$event['color'] = datetocolor($event['timebegin_month'],$event['timebegin_day'],$event['timebegin_year'],$colorpast,$colortoday,$colorfuture);
		removeslashes($event);
	
		// determine the name of the category
		$result = DBQuery($database, "SELECT c.id,c.name AS category_name FROM vtcal_category c WHERE c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.id='".sqlescape($event['categoryid'])."'" ); 
	
		if ($result->numRows() > 0) { // error checking, actually there should be always a category
			$e = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
			$event['category_name']=$e['category_name'];
		}
		else {
			$event['category_name']="???";
		}
	
		echo "<BR>";
		box_begin("inputbox",lang('preview_event'));
		echo '<form method="post" action="changeeinfo.php">',"\n";
    echo '<input type="submit" name="savethis" value="',lang('save_changes'),'">',"\n";

/*
		if ($repeat['mode'] > 0 && !empty($event['repeatid'])) {
			if (!recurrenceschanged($event['repeatid'],$repeat,$event,$database)) {
				echo '<input type="submit" name="saveall" value="Save changes for ALL recurrences"><BR><BR>';
			}
		}
*/
?>
<input type="submit" name="edit" value="<?php echo lang('go_back_to_make_changes'); ?>"> &nbsp;&nbsp;&nbsp;
<input type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>"><BR>
<br>
<table width="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="<?php echo "#cccccc"; ?>">
  <tr>
    <td valign="bottom"><span class="datetitle">
<?php
    echo Day_of_Week_to_Text(Day_of_Week($event['timebegin_month'],$event['timebegin_day'],$event['timebegin_year'])),", ";
    echo Month_to_Text($event['timebegin_month'])," ",$event['timebegin_day'],", ",$event['timebegin_year'];
?></span></td>
</tr>
<tr>
<td align="right" valign="middle">&nbsp;
<?php
    print_event($event);
?>
</td>
</tr>
</table>
<BR>
<?php
		if (!checkeventtime($event)) {
			echo "<BR>";
			feedback(lang('warning_ending_time_before_starting_time'),1);
		}
		if ($event['timeend_hour']==0) {
			echo "<BR>";
			feedback(lang('warning_no_ending_time'),1);
		}
	
		echo '<span class="bodytext">';
		if ($repeat['mode'] > 0) {
			echo lang('recurring_event'),": ";
			$repeatdef = repeatinput2repeatdef($event,$repeat);
			printrecurrence($event['timebegin_year'],
											$event['timebegin_month'],
											$event['timebegin_day'],
											$repeatdef);
			echo "<BR>";
			$repeatlist = producerepeatlist($event,$repeat);
			printrecurrencedetails($repeatlist);
		}
		else {
			echo lang('no_recurrences_defined');
		}
		echo "<BR><BR>\n";
	
		if (isset($detailscaller)) { echo "<INPUT type=\"hidden\" name=\"detailscaller\" value=\"",$detailscaller,"\">\n"; }
		passeventvalues($event,$event['sponsorid'],$repeat); // add the common input fields
?>
<INPUT type="hidden" name="check" value="1">
<?php
		echo '<INPUT type="hidden" name="httpreferer" value="',$httpreferer,'">',"\n";
		if (isset($eventid)) { echo "<INPUT type=\"hidden\" name=\"eventid\" value=\"",$event['id'],"\">\n"; }
		if (isset($copy)) { echo "<INPUT type=\"hidden\" name=\"copy\" value=\"",$copy,"\">\n"; }
		savechangesbuttons($event,$repeat,$database);
?>
<input type="submit" name="edit" value="<?php echo lang('go_back_to_make_changes'); ?>"> &nbsp;&nbsp;&nbsp;
<input type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
<br>
</span>
</form>
<?php
		box_end();
  } // end: if (isset($check) && $eventvalid && isset($preview))
  else { // display input form
		if (isset($eventid)) {
			if (isset($copy)) {
				pageheader(lang('copy_event'),
									 lang('copy_event'),
									 "Update","",$database);
				echo "<INPUT type=\"hidden\" name=\"copy\" value=\"",$copy,"\">\n";
			} else {
				pageheader(lang('update_event'),
									 lang('update_event'),
									 "Update","",$database);
			}
		}
		else {
			pageheader(lang('add_new_event'),
								 lang('add_new_event'),
								 "Update","",$database);
		}
		
		if (!isset($check)) { // end: ) && !isset($repeatsave)
			defaultevent($event,$_SESSION["AUTH_SPONSORID"],$database); // preset event with defaults
		}
		
		if (isset($templateid)) { // load template?
			if ($templateid > 0) {
				$result = DBQuery($database, "SELECT * FROM vtcal_template WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($templateid)."'" ); 
				$event = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
			}
		} // end: if (isset($templateid))
		
		// "add new event" was started from week,month or detail view
		if (isset($timebegin_year)) { $event['timebegin_year']=$timebegin_year; }
		if (isset($timebegin_month)) { $event['timebegin_month']=$timebegin_month; }
		if (isset($timebegin_day)) { $event['timebegin_day']=$timebegin_day; }
		
		if (isset($eventid) && (!isset($check) || $check != 1)) { // load event to update information if it's the first time the form is viewed
			$result = DBQuery($database, "SELECT * FROM vtcal_event WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'" ); 
		
			if ($result->numRows() > 0) { // event exists
				$event = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
			}
			else { // event doesn't exist in table event
				// check if event exists in table "event_public"
				$result = DBQuery($database, "SELECT * FROM vtcal_event_public WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'" ); 
		
				if ($result->numRows() > 0) { // event exists in "event_public"
					// copy event from table event_public into table event
					$event = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
//					eventaddslashes($event);
					insertintoevent($event['id'],$event,$database);
				}
			}
		
			disassemble_eventtime($event);
			if (!empty($event['repeatid'])) {
				readinrepeat($event['repeatid'],$event,$repeat,$database);
			}
			else { $repeat['mode'] = 0; }
		//  $sponsorid = $event[sponsorid];
		} // end if: "if (isset($eventid))"
    echo "<br>\n";
		box_begin("inputbox",lang('input_event_information'));
		echo "<form name=\"inputevent\" method=\"post\" action=\"changeeinfo.php\">\n";
		inputeventbuttons($event,$repeat,$database);
		echo "<br>\n<br>\n";
		if (isset($detailscaller)) { 
			echo "<INPUT type=\"hidden\" name=\"detailscaller\" value=\"",$detailscaller,"\">\n"; 
		}
		if (!isset($check)) { $check = 0; }
 		inputeventdata($event,$event['sponsorid'],1,$check,1,$repeat,$database);
		echo "<br>\n";
		echo '<INPUT type="hidden" name="httpreferer" value="',$httpreferer,'">',"\n";
		if (isset($eventid)) { echo "<INPUT type=\"hidden\" name=\"eventid\" value=\"",$event['id'],"\">\n"; }
		echo '<INPUT type="hidden" name="event[repeatid]" value="',HTMLSpecialChars($event['repeatid']),"\">\n";
		if (!$_SESSION["AUTH_ADMIN"]) { echo "<INPUT type=\"hidden\" name=\"event[sponsorid]\" value=\"",$event['sponsorid'],"\">\n"; }
		if (isset($copy)) { echo "<INPUT type=\"hidden\" name=\"copy\" value=\"",$copy,"\">\n"; }
		
		inputeventbuttons($event,$repeat,$database);
		
		echo "</form>\n";
		box_end();
  } // end: else: if (isset($check) && $eventvalid && isset($preview)) { // display preview

	echo "<br>";
	require("footer.inc.php");
?>