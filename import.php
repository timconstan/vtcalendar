<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');
  require_once("xmlparser.inc.php");

  if (isset($_GET['cancel'])) { setVar($cancel,$_GET['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_GET['importurl'])) { setVar($importurl,$_GET['importurl'],'importurl'); } else { unset($importurl); }
  if (isset($_GET['startimport'])) { setVar($startimport,$_GET['startimport'],'startimport'); } else { unset($startimport); }


  $database = DBopen();
  if (!authorized($database)) { exit; }
  
	if (isset($cancel)) {
    redirect2URL("update.php");
    exit;
  }
// check that the time adheres to the standard "2000-03-22 15:00:00" 
function eventtimestampvalid($timestamp) {
  return strlen($timestamp) == 19 &&
         ereg("^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$",$timestamp) &&
         checkdate(substr($timestamp,5,2),substr($timestamp,8,2),substr($timestamp,0,4)) &&
         substr($timestamp,11,2) >= 0 && substr($timestamp,11,2) <= 23 &&
         substr($timestamp,14,2) >= 0 && substr($timestamp,14,2) <= 59 &&
         substr($timestamp,17,2) >= 0 && substr($timestamp,17,2) <= 59;
} // end: function eventtimevalid

// check that the timestamp for the starting time is valid
function eventtimebeginvalid($timebeginstamp) {
  return eventtimestampvalid($timebeginstamp);
} // end: function eventtimebeginvalid

// check that the timestamp for the ending time is valid
function eventtimeendvalid($timeendstamp) {
  return eventtimestampvalid($timeendstamp);
} // end: function eventtimeendvalid

function saveevent() {
global $eventlist,$event,$eventnr,
       $date,$timebegin,$timeend,$error,$validcategory;

  // construct timestamps from the info $date, $timebegin, $timeend
  $event['sponsorid'] = $_SESSION['AUTH_SPONSORID'];
	if ($_SESSION["AUTH_ADMIN"]) { $event['approved'] = 1; }
	else { $event['approved'] = 0; }
  $event['rejectreason'] = "";
  $event['repeatid'] = "";
  $event['timebegin'] = $date." ".$timebegin.":00";
  if (empty($timeend)) { $timeend = "23:59"; }
  $event['timeend'] = $date." ".$timeend.":00";
  $event['wholedayevent'] = ($timebegin == "00:00") && ($timeend == "23:59");

  // make sure that the previous event got all the input fields
  if (!(strlen($event['displayedsponsor']) <= MAXLENGTH_SPONSOR)) { feedback(lang('import_error_displayedsponsor'),FEEDBACKNEG); $error = true; }
  if (!(strlen($event['displayedsponsorurl']) <= MAXLENGTH_URL && checkurl($event['displayedsponsorurl']))) { feedback(lang('import_error_displayedsponsorurl'),FEEDBACKNEG); $error = true; }
  if (!(eventtimebeginvalid($event['timebegin']))) { feedback(lang('import_error_timebegin'),FEEDBACKNEG); $error = true; }
  if (!(eventtimeendvalid($event['timeend']))) { feedback(lang('import_error_timeend'),FEEDBACKNEG); $error = true; }
  if (!(array_key_exists($event['categoryid'],$validcategory))) { feedback(lang('import_error_categoryid'),FEEDBACKNEG); $error = true; }
  if (!(!empty($event['title']) && strlen($event['title']) <= MAXLENGTH_TITLE)) { feedback(lang('import_error_title'),FEEDBACKNEG); $error = true; }
  if (!(strlen($event['description']) <= MAXLENGTH_DESCRIPTION)) { feedback(lang('import_error_description'),FEEDBACKNEG); $error = true; }
  if (!(strlen($event['location']) <= MAXLENGTH_LOCATION)) { feedback(lang('import_error_location'),FEEDBACKNEG); $error = true; }
  if (!(strlen($event['price']) <= MAXLENGTH_PRICE)) { feedback(lang('import_error_price'),FEEDBACKNEG); $error = true; }
  if (!(strlen($event['contact_name']) <= MAXLENGTH_CONTACT_NAME)) { feedback(lang('import_error_contact_name'),FEEDBACKNEG); $error = true; }
  if (!(strlen($event['contact_phone']) <= MAXLENGTH_CONTACT_PHONE)) { feedback(lang('import_error_contact_phone'),FEEDBACKNEG); $error = true; }
  if (!(strlen($event['contact_email']) <= MAXLENGTH_CONTACT_EMAIL)) { feedback(lang('import_error_contact_email'),FEEDBACKNEG); $error = true; }
  if (!(strlen($event['url']) <= MAXLENGTH_URL && checkurl($event['url']))) { feedback(lang('import_error_contact_url'),FEEDBACKNEG); $error = true; }

  // save all the data of the previous event in the array
	if (!$error) {
	  $eventnr++;
    $eventlist[$eventnr] = $event;
  }
} // end: function saveevent

// XML parser element handler for start element
function xmlstartelement_importevent($parser, $element, $attrs) {
  global $xmlcurrentelement,$xmlelementattrs,
         $firstelement,$event,$eventnr,
         $date,$timebegin,$timeend,$error;

  $xmlcurrentelement = $element;
  $xmlelementattrs = $attrs;

  if (strtolower($xmlcurrentelement)=="events") {
    if (!$firstelement) { feedback(lang('import_error_events'),FEEDBACKNEG); } // <events> must always be the first element
  }
  elseif (strtolower($xmlcurrentelement)=="event") {
    // start new element
    $date = "";
    $timebegin = "";
    $timeend = "";
		$event['displayedsponsor']="";
		$event['displayedsponsorurl']="";
		$event['categoryid']="";
		$event['title']="";
		$event['description']="";
		$event['location']="";
		$event['price']="";
		$event['contact_name']="";
		$event['contact_phone']="";
		$event['contact_email']="";
		$event['url']="";
  }
  
  $firstelement = 0;
}

// XML parser element handler for end element
function xmlendelement_importevent($parser, $element) {
  global $xmlcurrentelement,$xmlelementattrs,$event,$error;

  $xmlcurrentelement = "";
  $xmlelementattrs = "";

  if (strtolower($element)=="event") { saveevent(); }
}

function xmlcharacterdata_importevent($parser, $data) {
  global $xmlcurrentelement,$xmlelementattrs,
         $firstelement,$eventlist,$event,$eventnr,
         $date,$timebegin,$timeend,$error;
  
  if (strtolower($xmlcurrentelement)=="displayedsponsor") {
    $event['displayedsponsor'] .= $data;
  }
  elseif (strtolower($xmlcurrentelement)=="displayedsponsorurl") {
    $event['displayedsponsorurl'] .= $data;
  }
  elseif (strtolower($xmlcurrentelement)=="date") {
    $date = $data;
  }
  elseif (strtolower($xmlcurrentelement)=="timebegin") {
    $timebegin = $data;
  }
  elseif (strtolower($xmlcurrentelement)=="timeend") {
    $timeend = $data;
  }
  elseif (strtolower($xmlcurrentelement)=="categoryid") {
    $event['categoryid'] = $data;
  }
  elseif (strtolower($xmlcurrentelement)=="title") {
    $event['title'] .= $data;
  }
  elseif (strtolower($xmlcurrentelement)=="description") {
    $event['description'] .= $data;
  }
  elseif (strtolower($xmlcurrentelement)=="location") {
    $event['location'] .= $data;
  }
  elseif (strtolower($xmlcurrentelement)=="price") {
    $event['price'] .= $data;
  }
  elseif (strtolower($xmlcurrentelement)=="contact_name") {
    $event['contact_name'] .= $data;
  }
  elseif (strtolower($xmlcurrentelement)=="contact_phone") {
    $event['contact_phone'] .= $data;
  }
  elseif (strtolower($xmlcurrentelement)=="contact_email") {
    $event['contact_email'] .= $data;
  }
  elseif (strtolower($xmlcurrentelement)=="url") {
    $event['url'] .= $data;
  }
} // end: function characterdata_importevents

// default error handler
function xmlerror_importevent($xml_parser) {
  echo "<br>\n";
  feedback("XML error: ".xml_error_string(xml_get_error_code($xml_parser))." at line ".xml_get_current_line_number($xml_parser),FEEDBACKNEG);
} // end: function xmlerror

  pageheader(lang('import_events'),
             lang('import_events'),
             "Update","",$database);
  echo "<BR>";
  box_begin("inputbox",lang('import_events'));
  
  $showinputbox = 1;
  if (isset($importurl)) {
    if (checkurl($importurl)) {
      // get list of valid category-IDs
			$result = DBQuery($database, "SELECT * FROM vtcal_category WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."'" ); 
			for($i=0; $i<$result->numRows(); $i++) {
  			$category = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
			  $validcategory[$category['id']] = true;
			}
			
      // open remote file and parse it
      $firstelement = 1;
      $eventnr = 0;
			$error = false;
      $parsexmlerror = parsexml("$importurl", "xmlstartelement_importevent", 
                                 "xmlendelement_importevent", 
                                 "xmlcharacterdata_importevent",
                                 "xmlerror_importevent");
      if ($parsexmlerror == FILEOPENERROR) {
        feedback(lang('import_error_open_url')."<br>",FEEDBACKNEG);
      }
      if ($error) {
        feedback("<br>".lang('no_events_imported')."<br>",FEEDBACKNEG);
      }
      if (!$parsexmlerror) {
			  if (!$error) {
					if ($eventnr > 0) {
						// determine sponsor name & URL
						$result = DBQuery($database, "SELECT * FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($event['sponsorid'])."'" ); 
						$sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
					
						$id = getNewEventId();
						$id1 = substr($id,0,10);
						for ($i=1; $i<=$eventnr; $i++) {
							$event = $eventlist[$i];
							if (empty($event['displayedsponsor'])) { $event['displayedsponsor']=$sponsor['name']; }
							if (empty($event['displayedsponsorurl'])) { $event['displayedsponsorurl']=$sponsor['url'];	}
							$id1++;
							$eventid = $id1."000";
							$event['id'] = $eventid;
							insertintoevent($eventid,$event,$database);
							if ($_SESSION["AUTH_ADMIN"]) {
								publicizeevent($eventid,$event,$database);
							}
						}
						$showinputbox = 0;
						echo "<br>\n";
						feedback($eventnr." ".lang('events_successfully_imported'),FEEDBACKPOS);
						echo "<br>\n";
						echo "<form method=\"post\" action=\"update.php\">\n";
						echo '  <input type="submit" name="back" value="',lang('back_to_menu'),'">',"\n";
						echo "</form>\n";
					}
					else {
						feedback(lang('import_file_contains_no_events'),FEEDBACKNEG);
					}
        } // end: if (!$error) 
			} // end: if (!$parsexmlerror
    } // end: if (checkurl($importurl))
  }
  if ($showinputbox) {
?>
<a target="main" href="helpimport.php"><img src="images/nuvola/16x16/actions/help.png" width="16" height="16" alt="" border="0"> 
<?php echo lang('how_to_import'); ?></a>
<br>
<br>
<form method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
<b><?php echo lang('enter_import_url_message'); ?></b><br>
<br>
<input type="text" name="importurl" value="<?php 
if (isset($importurl)) { echo $importurl; } ?>" size="60" maxlength="<?php echo constImporturlMaxLength; ?>"><br>
<?php echo lang('enter_import_url_example'); ?><br>
<br>
<input type="submit" name="startimport" value="<?php echo lang('ok_button_text'); ?>">
<INPUT type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
</form>
<?php
  } // end: if ($showinputbox)
  box_end();
  echo "<BR>";

  require("footer.inc.php");
?>