<?php
  session_start ();
  header("Cache-control: private");

  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');
  require_once("icalendar.inc.php");

  if (isset($_GET['cancel'])) { setVar($cancel,$_GET['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_GET['type'])) { setVar($type,$_GET['type'],'type'); } else { unset($type); }
  if (isset($_GET['sponsortype'])) { setVar($sponsortype,$_GET['sponsortype'],'sponsortype'); } else { unset($sponsortype); }
  if (isset($_GET['eventid'])) { setVar($eventid,$_GET['eventid'],'eventid'); } else { unset($eventid); }
  if (isset($_GET['timebegin'])) { setVar($timebegin,$_GET['timebegin'],'timebegin'); } else { unset($timebegin); }
  if (isset($_GET['timebegin_year'])) { setVar($timebegin_year,$_GET['timebegin_year'],'timebegin_year'); } else { unset($timebegin_year); }
  if (isset($_GET['timebegin_month'])) { setVar($timebegin_month,$_GET['timebegin_month'],'timebegin_month'); } else { unset($timebegin_month); }
  if (isset($_GET['timebegin_day'])) { setVar($timebegin_day,$_GET['timebegin_day'],'timebegin_day'); } else { unset($timebegin_day); }
  if (isset($_GET['timeend'])) { setVar($timeend,$_GET['timeend'],'timeend'); } else { unset($timeend); }
  if (isset($_GET['timeend_year'])) { setVar($timeend_year,$_GET['timeend_year'],'timeend_year'); } else { unset($timeend_year); }
  if (isset($_GET['timeend_month'])) { setVar($timeend_month,$_GET['timeend_month'],'timeend_month'); } else { unset($timeend_month); }
  if (isset($_GET['timeend_day'])) { setVar($timeend_day,$_GET['timeend_day'],'timeend_day'); } else { unset($timeend_day); }
  if (isset($_GET['rangedays'])) { setVar($rangedays,$_GET['rangedays'],'rangedays'); } else { unset($rangedays); }
  if (isset($_GET['categoryid'])) { setVar($categoryid,$_GET['categoryid'],'categoryid'); } else { unset($categoryid); }

// TODO: input validation for "categoryidlist" parameter of the format e.g. "3,34,17"
$categoryidlist = $_GET['categoryidlist'];

  if (isset($_GET['keyword'])) { setVar($keyword,$_GET['keyword'],'keyword'); } else { unset($keyword); }
  if (isset($_GET['specificsponsor'])) { setVar($specificsponsor,$_GET['specificsponsor'],'specificsponsor'); } else { unset($specificsponsor); }
			
  $database = DBopen();
  if (!viewauthorized($database)) { exit; }

  if (isset($cancel)) {
    redirect2URL("update.php");
    exit;
  }
  
  if ( isset($_SERVER["HTTPS"]) ) { $calendarurl = "https"; } else { $calendarurl = "http"; } 
	$calendarurl .= "://".$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'], "/"))."/";

  // translates text into XML text, writing entity names like "&amp;" instead of "&"
  function text2xmltext($text) {
    $text = htmlentities($text);
    $text = ereg_replace("\'","&apos;",$text);
    return $text;
  } // end: function txt2xmltxt

	if (isset($type) && ($type == "xml" || $type == "rss" || $type == "ical" || $type == "rss1_0" || $type == "vxml") ) { // outputs everything depending in the params in XML format
    // determine which sponsors to show
    if ($sponsortype=="self" && !empty($_SESSION["AUTH_SPONSORID"])) { 
      // read sponsor name from DB
      $result = DBQuery($database, "SELECT name FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 
      $s = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
      $displayedsponsor = $s['name']; 
    }
    elseif ($sponsortype == "specific") {
      $displayedsponsor = $specificsponsor; 
    }
    else { // elseif ($sponsortype == "all") {
      $displayedsponsor = ""; 
    }

    // if the starting point not passed as a param then use defaults
    if (isset($eventid)) {
      $timebegin = "";
      $timeend = "";
    }
    else {
      // determine today's date
	  $today = Decode_Date_US(date("m/d/Y"));
      if ($timebegin == "now") {
	  	$timebegin = date("Y-m-d H:i:s");
	  }
	  elseif (!isset($timebegin) || $timebegin=="today") {
        if (isset($timebegin_year)) { // details was called from the searchform
          $timebegin = datetime2timestamp($timebegin_year,$timebegin_month,$timebegin_day,12,0,"am");
        }
        else { // details is called without any time limits, use "today" as default
          $timebegin = datetime2timestamp($today['year'],$today['month'],$today['day'],12,0,"am");
        }
      }
	  
      if (!isset($timeend) || $timeend=="today") {
        if (isset($timeend_year)) {
          $timeend = datetime2timestamp($timeend_year,$timeend_month,$timeend_day,11,59,"pm");
        }
        if (isset($timeend) && $timeend=="today") {
          $timeend = datetime2timestamp($today[year],$today[month],$today[day],11,59,"pm");
        }
      }
			if (isset($rangedays)) {
			  $timebeginrange = timestamp2datetime($timebegin);
			  $timeendrange = Add_Delta_Days($timebeginrange['month'],$timebeginrange['day'],$timebeginrange['year'],$rangedays);
			  $timeend = datetime2timestamp($timeendrange['year'],$timeendrange['month'],$timeendrange['day'],11,59,"pm");
			}
    } // end: if (isset($eventid))

    if (!isset($categoryid)) { $categoryid=0; }
    if (!isset($keyword)) { $keyword=""; }

    $query = "SELECT e.recordchangedtime,e.recordchangeduser,e.repeatid,e.id AS id,e.timebegin,e.timeend,e.sponsorid,e.displayedsponsor,e.displayedsponsorurl,e.title,e.wholedayevent,e.categoryid,e.description,e.location,e.price,e.contact_name,e.contact_phone,e.contact_email,e.url,c.id AS cid,c.name AS category_name,s.id AS sid,s.name AS sponsor_name,s.url AS sponsor_url FROM vtcal_event_public e, vtcal_category c, vtcal_sponsor s WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.categoryid = c.id AND e.sponsorid = s.id";

    if (!empty($eventid))  { $query.= " AND e.id='".sqlescape($eventid)."'"; }
    if (!empty($timebegin)) { 
        $date = substr($timebegin,0,10);
	    $query.= " AND (";
		// also get "all day" events
//		$query.= "(e.timebegin = '".sqlescape($date)." 00:00:00' AND e.timeend = '".sqlescape($date)." 23:59:00')"; 
		$query.= "(e.timebegin = '".sqlescape($date)." 00:00:00' AND e.wholedayevent = '1')"; 
		$query.= " OR e.timebegin >= '".sqlescape($timebegin)."')"; 
	}
    if (!empty($timeend)) { $query.= " AND e.timeend <= '".sqlescape($timeend)."'"; }
    if (!empty($displayedsponsor))  { $query.= " AND e.displayedsponsor LIKE '%".sqlescape($displayedsponsor)."%'"; }

	if (!empty($categoryidlist)) {
		  $query.= " AND (";
		  $aCategoryIdList = explode(",",$categoryidlist);
		  $i=0;
		  foreach($aCategoryIdList as $sCategoryId) {
		  	  $i++;
			  if ($i > 1) {
				  $query.= " OR "; 
			  }
			  $query.= "e.categoryid='".sqlescape($sCategoryId)."'"; 
		  }
		  $query.= ")";
	}
	else {
    	if (isset($categoryid) && $categoryid!=0) { 
		  $query.= " AND e.categoryid='".sqlescape($categoryid)."'"; 
		}
	}

    if (!empty($keyword)) { $query.= " AND ((e.title LIKE '%".sqlescape($keyword)."%') or (e.description LIKE '%".sqlescape($keyword)."%'))"; }
    $query.= " ORDER BY e.timebegin ASC, e.wholedayevent DESC";
    
		$result = DBQuery($database, $query ); 

    if ($type == "rss") {
      echo '<?xml version="1.0"?>',"\n";
      echo '<rss version="0.91">',"\n";
      echo "<channel>\n";
      echo "    <title>".$_SESSION["TITLE"]."</title>\n";
      if (substr($timebegin,8,1) == "0") { $day = substr($timebegin,9,1); } 
      else { $day = substr($timebegin,8,2); }
      if (substr($timebegin,5,1) == "0") { $month = substr($timebegin,6,1); } 
      else { $month = substr($timebegin,5,2); }
      $date = $month."/".$day."/".substr($timebegin,0,4);
      echo "    <description>".$date."</description>\n";

      echo "    <link>".$calendarurl."?calendarid=".$_SESSION["CALENDARID"]."</link>\n\n";
      for ($i=0; $i < $result->numRows(); $i++) {
        $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
        disassemble_eventtime($event);
        echo "    <item>\n";
        echo "      <title>",text2xmltext($event['title']),"</title>\n";
        echo "      <link>".$calendarurl."main.php?view=event&amp;calendarid=".$_SESSION["CALENDARID"]."&amp;eventid=".$event['id']."</link>\n";
        echo "      <description>";
        if ($event['wholedayevent']==0) {
          echo timestring($event['timebegin_hour'],$event['timebegin_min'],$event['timebegin_ampm']), ": ";
        }
        else {
          echo "All day: ";
        }
        echo text2xmltext($event['category_name']),"</description>\n";
        echo "    </item>\n";
      } // end: for ($i=0; $i < $result->numRows(); $i++)
      
      echo "  </channel>\n";
      echo "</rss>\n";
    } // end: if ($type == "rss")
    if ($type == "rss1_0") { 
      echo '<?xml version="1.0"?>',"\n";
?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:rss091="http://purl.org/rss/1.0/modules/rss091/"
         xmlns:syn="http://purl.org/rss/1.0/modules/syndication/"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns="http://purl.org/rss/1.0/">

<channel rdf:about="<?php echo $calendarurl; ?>?calendarid=<?php echo $_SESSION["CALENDARID"]; ?>">
  <link><?php echo $calendarurl; ?>?calendarid=<?php echo $_SESSION["CALENDARID"]; ?></link>
<?php
  if (substr($timebegin,8,1) == "0") { $day = substr($timebegin,9,1); } 
  else { $day = substr($timebegin,8,2); }
  if (substr($timebegin,5,1) == "0") { $month = substr($timebegin,6,1); } 
  else { $month = substr($timebegin,5,2); }
  $date = $month."/".$day."/".substr($timebegin,0,4);
?>
  <description><?php echo $date; ?></description>
  <title><?php echo $_SESSION["TITLE"]; ?></title>
  <items>
    <rdf:Seq>
<?php
  for ($i=0; $i < $result->numRows(); $i++) {
    $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
    echo "      <rdf:li resource=\"".$calendarurl."main.php?view=event&amp;calendarid=".$_SESSION["CALENDARID"]."&amp;eventid=".$event['id']."\"/>\n";
  }
?>
    </rdf:Seq>
  </items>
</channel>
<?php    
      for ($i=0; $i < $result->numRows(); $i++) {
        $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
        disassemble_eventtime($event);
        echo "    <item rdf:about=\"".$calendarurl."main.php?view=event&amp;calendarid=".$_SESSION["CALENDARID"]."&amp;eventid=".$event['id']."\">\n";
        echo "      <link>".$calendarurl."main.php?view=event&amp;calendarid=".$_SESSION["CALENDARID"]."&amp;eventid=".$event['id']."</link>\n";
        echo "      <title>",text2xmltext($event['title']),"</title>\n";
        echo "      <description>";
        if ($event['wholedayevent']==0) {
          echo timestring($event['timebegin_hour'],$event['timebegin_min'],$event['timebegin_ampm']), ": ";
        }
        else {
          echo "All day: ";
        }
        echo text2xmltext($event['category_name']),"</description>\n";
        echo "    </item>\n";
      } // end: for ($i=0; $i < $result->numRows(); $i++)

      echo "</rdf:RDF>\n";
    } // end: if ($type == "rss1_0")
    elseif ($type == "xml") {
      echo '<?xml version="1.0"?>',"\n";
      echo "<events>\n";
      for ($i=0; $i < $result->numRows(); $i++) {
        $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);

        unset($repeat);
        // read in repeatid if necessary
        if (!empty($event['repeatid'])) {
//          $queryRepeat = "SELECT * FROM vtcal_event_repeat WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($event['repeatid'])."'";
          $queryRepeat = "SELECT * FROM vtcal_event_repeat WHERE id='".sqlescape($event['repeatid'])."'";
					$repeatresult = DBQuery($database, $queryRepeat ); 
          if ( $repeatresult->numRows () > 0 ) {
            $repeat = $repeatresult->fetchRow(DB_FETCHMODE_ASSOC,0);
          }
        }

        // convert some data fields
        $date = substr($event['timebegin'],0,10);
        $timebegin = substr($event['timebegin'],11,5);
        $timeend = substr($event['timeend'],11,5);
        
        // output XML code
        echo "  <event>\n";
        echo "    <eventid>",$event['id'],"</eventid>\n";
        echo "    <sponsorid>",$event['sponsorid'],"</sponsorid>\n";
        echo "    <inputsponsor>",text2xmltext($event['sponsor_name']),"</inputsponsor>\n";
        echo "    <displayedsponsor>",text2xmltext($event['displayedsponsor']),"</displayedsponsor>\n";
        echo "    <displayedsponsorurl>",text2xmltext($event['displayedsponsorurl']),"</displayedsponsorurl>\n";
        echo "    <date>",$date,"</date>\n";
        echo "    <timebegin>",$timebegin,"</timebegin>\n";
        echo "    <timeend>",$timeend,"</timeend>\n";
        echo "    <repeat_vcaldef>";
        if (!empty($repeat['repeatdef'])) { echo $repeat['repeatdef']; }
        echo "</repeat_vcaldef>\n";
        echo "    <repeat_startdate>";
        if (!empty($repeat['startdate'])) { echo substr($repeat['startdate'],0,10); }
        echo "</repeat_startdate>\n";
        echo "    <repeat_enddate>";
        if (!empty($repeat['enddate'])) { echo substr($repeat['enddate'],0,10); }
        echo "</repeat_enddate>\n";
        echo "    <categoryid>",$event['categoryid'],"</categoryid>\n";
        echo "    <category>",text2xmltext($event['category_name']),"</category>\n";
        echo "    <title>",text2xmltext($event['title']),"</title>\n";
        echo "    <description>",text2xmltext($event['description']),"</description>\n";
        echo "    <location>",text2xmltext($event['location']),"</location>\n";
        echo "    <price>",text2xmltext($event['price']),"</price>\n";
        echo "    <contact_name>",text2xmltext($event['contact_name']),"</contact_name>\n";
        echo "    <contact_phone>",text2xmltext($event['contact_phone']),"</contact_phone>\n";
        echo "    <contact_email>",text2xmltext($event['contact_email']),"</contact_email>\n";
        echo "    <url>",text2xmltext($event['url']),"</url>\n";
        echo "    <recordchangedtime>",substr($event['recordchangedtime'],0,19),"</recordchangedtime>\n";
        echo "    <recordchangeduser>",$event['recordchangeduser'],"</recordchangeduser>\n";
        echo "  </event>\n";
      } // end: for ($i=0; $i < $result->numRows(); $i++) {
      echo "</events>\n";
    } // end: elseif ($type == "xml")
		elseif ($type == "ical") {
			$icalname = "calendar";
			if ($categoryid != 0) {
				if ($result->numRows() > 0) {
					$event = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
					$icalname = str_replace(array(" ","-","/"),"_",$event['category_name']);
				}
			}
			else {
			  $icalname = str_replace(array(" ","-","/"),"_",$_SESSION["NAME"]);
			}
      Header("Content-Type: text/calendar; charset=\"utf-8\"; name=\"".$icalname.".ics\"");
      Header("Content-disposition: attachment; filename=".$icalname.".ics");
			echo getICalHeader();

			// this is for Apple iCal since it does not take the calendar name from the .ics file name
			if ($result->numRows() > 0) {
	  		echo "X-WR-CALNAME;VALUE=TEXT:".$icalname.CRLF;	
			}

			for ($i=0; $i < $result->numRows(); $i++) {
				$event = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
				echo getICalFormat($event);
			} // end: for ($i=0; $i < $result->numRows(); $i++) {
			echo getICalFooter();	
	} // end: elseif ($type == "ical")
    elseif ($type == "vxml") {
      echo '<?xml version="1.0"?>',"\n";
      echo '<vxml version="2.0">
  <form>
    <block>
      <prompt>
        ';
	  echo lang('vxml_welcome')." ";
	  echo '<break size="medium"/>',"\n";
	  $iNumEvents = $result->numRows();
	  if ($iNumEvents > 0) {
		  echo lang('vxml_there_are'),' ',$iNumEvents,' ',lang('vxml_events_for_today'),' ',date("F j");
	  }
	  else {
	  	  echo lang('vxml_no_more_events'),' ',date("F j");
	  }
	  
	  if (date("j") == "1") { echo "st"; }
	  elseif (date("j") == "2") { echo "nd"; }
	  elseif (date("j") == "3") { echo "rd"; }
	  else { echo "th"; }
	  echo ".\n";
	  
	  echo '<break size="medium"/>',"\n";

      for ($i=0; $i < $iNumEvents; $i++) {
        $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
		
		if ($event['wholedayevent'] == '1') {
			echo lang('all_day');
		}
		else {
			$aTimeBegin = timestamp2datetime($event['timebegin']);
			echo $aTimeBegin['hour'];
			if ($aTimeBegin['min'] != "00") {
				echo " ",$aTimeBegin['min'];
			}
			echo strtoupper($aTimeBegin['ampm']),"\n";
        }
		echo '<break size="small"/>',"\n";
        
        echo $event['title'],"\n";
        
        echo '<break size="large"/>',"\n";
      } // end: for ($i=0; $i < $result->numRows(); $i++) {

      echo '<break size="large"/>',"\n";
	  echo lang('vxml_goodbye'),"\n";

	  echo '
      </prompt>
	</block>
  </form>
</vxml>
';
    } // end: elseif ($type == "vxml")
  
  } // end: elseif ($type == "xml" || $type == "rss")
  else { // display form
    // determine today's date
    $today = Decode_Date_US(date("m/d/Y"));
  
    // check if some input params are set, and if not set them to default
    if (!isset($timebegin_year))  { $timebegin_year = $today['year']; }
    if (!isset($timebegin_month)) { $timebegin_month = $today['month']; }
    if (!isset($timebegin_day))   { $timebegin_day = $today['day']; }
  
    if (!isset($timeend_year))    { $timeend_year = $timebegin_year; }
    if (!isset($timeend_month)) {
      $timeend_month = $timebegin_month+6;
      if ($timeend_month >= 13) {
        $timeend_month = $timeend_month-12;
        $timeend_year++;
      }
    }
    if (!isset($timeend_day)) {
      $timeend_day = $timebegin_day;
      while (!checkdate($timeend_month,$timeend_day,$timeend_year)) { $timeend_day--; };
    }

    pageheader(lang('export_events'),
               lang('export_events'),
               "","",$database);
    echo "<BR>";
    box_begin("inputbox",lang('export_events'));
?>
<a target="newWindow" onclick="new_window(this.href); return false" 
   href="helpexport.php"><img src="images/nuvola/16x16/actions/help.png" width="16" height="16" alt="" border="0"> 
   <?php echo lang('how_to_export_events'); ?></a>
<br>
<br>
<form method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="bodytext" valign="top">
      <strong><?php echo lang('output_format'); ?></strong>&nbsp;
    </td>
    <td>
      <select name="type">
        <option value="xml">XML/VT Calendar</option>
        <option value="ical">iCalendar</option>
        <option value="rss">XML/RSS 0.91</option>
        <option value="rss1_0">XML/RSS 1.0</option>
        <option value="vxml">VoiceXML 2.0</option>
      </select> <br>
      <br>
    </td>
  </tr>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('category'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
      <SELECT name="categoryid" size="1">
<?php
$result = DBQuery($database, "SELECT * FROM vtcal_category WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."'" ); 

// print list with categories from the DB
echo "<OPTION ";
if (empty($categoryid) || $categoryid==0) {
  echo "selected ";
}
echo "value=\"0\">all</OPTION>\n";

for ($i=0; $i<$result->numRows(); $i++) {
  $category = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
  echo "<OPTION ";
  if (!empty($categoryid) && $categoryid==$category['id']) { echo "selected "; }
  echo "value=\"",$category['id'],"\">",$category['name'],"</OPTION>\n";
}
?>
      </SELECT><br>
      <br>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('sponsor'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
      <input type="radio" name="sponsortype" value="all" checked> <?php echo lang('all'); ?><br>
<?php
  if (!empty($_SESSION["AUTH_SPONSORID"])) {
    // read sponsor name from DB
    $result = DBQuery($database, "SELECT name FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 
    $s = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
    echo '<input type="radio" name="sponsortype" value="self"> ',$s['name'],"<br>\n";
  }
?>
        <input type="radio" name="sponsortype" value="specific"> <?php echo lang('specific_sponsor'); ?>
        <INPUT type="text" size="28" maxlength="<?php echo constSpecificsponsorMaxLength; ?>" name="specificsponsor" value="">
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  <i><?php echo lang('specific_sponsor_example'); ?></i><BR>
      <br>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('date'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
      <TABLE border="0">
        <TR>
          <TD class="bodytext" valign="top"><?php echo lang('from'); ?>:</TD>
          <TD class="bodytext" valign="top">
            <SELECT name="timebegin_month" size="1">
<?php
// print list with months
for ($i=1; $i<=12; $i++) {
  print '<OPTION ';
  if ($timebegin_month==$i) { echo "selected "; }
  echo "value=\"$i\">",Month_to_Text($i),"</OPTION>\n";
}
?>
          </SELECT>
        </TD>
        <TD class="bodytext" valign="top">
          <SELECT name="timebegin_day" size="1">
<?php
// print list with days
for ($i=1; $i<=31; $i++) {
  echo "<OPTION ";
  if ($timebegin_day==$i) { echo "selected "; }
  echo "value=\"$i\">$i</OPTION>\n";
}
?>
        </SELECT>
      </TD>
      <TD class="bodytext" valign="top">
        <SELECT name="timebegin_year" size="1">
<?php
// print list with years
for ($i=date("Y")-1; $i<=date("Y")+3; $i++) {
  echo "<OPTION ";
  if ($timebegin_year==$i) { echo "selected "; }
  echo "value=\"$i\">$i</OPTION>\n";
}
?>
        </SELECT>
      </TD>
    </TR>
    <TR>
      <TD class="bodytext" valign="top"><?php echo lang('to'); ?>:</TD>
      <TD class="bodytext" valign="top">
        <SELECT name="timeend_month" size="1">
<?php
// print list with months
for ($i=1; $i<=12; $i++) {
  print '<OPTION ';
  if ($timeend_month==$i) { echo "selected "; }
  echo "value=\"$i\">",Month_to_Text($i),"</OPTION>\n";
}
?>
        </SELECT>
      </TD>
      <TD>
        <SELECT name="timeend_day" size="1">
<?php
// print list with days
for ($i=1; $i<=31; $i++) {
  echo "<OPTION ";
  if ($timeend_day==$i) { echo "selected "; }
  echo "value=\"$i\">$i</OPTION>\n";
}
?>
        </SELECT>
      </TD>
      <TD class="bodytext" valign="top">
        <SELECT name="timeend_year" size="1">
<?php
// print list with years
for ($i=date("Y")-1; $i<=date("Y")+3; $i++) {
  echo "<OPTION ";
  if ($timeend_year==$i) { echo "selected "; }
  echo "value=\"$i\">$i</OPTION>\n";
}
?>
        </SELECT>
      </TD>
    </TR>
  </TABLE>
</table>
<br>

<?php echo lang('export_message'); ?><br>
<br>
<input type="submit" name="startexport" value="<?php echo lang('ok_button_text'); ?>">
<input type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
</form>
<?php    
    box_end();
    echo "<BR>";
    require("footer.inc.php");
  }
?>