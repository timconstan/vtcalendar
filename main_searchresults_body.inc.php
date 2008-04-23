<?php
if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files
?><table cellspacing="5" cellpadding="0" width="100%" bgcolor="#ffffff" border="0">
      <FORM method="post" action="main.php?view=search">
			<tr><td colspan="3">
			<br>
        <INPUT type="submit" name="back" value="&laquo; <?php echo lang('back_to_prev_page'); ?>">
				<br><br>
			</td></tr>
<?php
if (isset($timebegin_year)) { // details was called from the searchform
	$timebegin = datetime2timestamp($timebegin_year,$timebegin_month,$timebegin_day,12,0,"am");
}
else { // details is called without any time limits, use "today" as default
	$timebegin = datetime2timestamp($today['year'],$today['month'],$today['day'],12,0,"am");
}
if (!isset($timeend) || $timeend=="today") {
	if (isset($timeend_year)) {
		$timeend = datetime2timestamp($timeend_year,$timeend_month,$timeend_day,11,59,"pm");
	}
	if (isset($timeend) && $timeend=="today") {
		$timeend = datetime2timestamp($today['year'],$today['month'],$today['day'],11,59,"pm");
	}
}

$ievent = 0;
// read all events for this week from the DB
$query = "SELECT e.id AS eventid,e.timebegin,e.timeend,e.sponsorid,e.title,e.location,e.description,e.wholedayevent,e.categoryid,c.id,c.name AS category_name FROM vtcal_event_public e, vtcal_category c ";
$query.= "WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.categoryid = c.id";
if (!empty($timebegin)) { $query.= " AND e.timebegin >= '".sqlescape($timebegin)."'"; }
if (!empty($timeend)) { $query.= " AND e.timeend <= '".sqlescape($timeend)."'"; }

if ( isset($filtercategories) && count($filtercategories) > 0 ) {
  $query.= " AND (";
	for($c=0; $c < count($filtercategories); $c++) {
	  if ($c > 0) { $query.=" OR "; }
	  $query.= "(e.categoryid='".sqlescape($filtercategories[$c])."')";
    }
	$query.= ")";
}
else {
   if (isset($categoryid) && $categoryid != 0) { $query.= " AND (e.categoryid='".sqlescape($categoryid)."')"; }
}

if (!empty($keyword)) {
	$keywords = split ( " ", $keyword );
		
	// read alternative keywords from database
	$r = DBQuery($database, "SELECT * FROM vtcal_searchkeyword WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."'" );
    for ($i=0; $i < $r->numRows(); $i++) {
  		$searchkeyword = $r->fetchRow(DB_FETCHMODE_ASSOC,$i);
		$search_keyword[$i]=$searchkeyword['keyword'];
		$search_alternative[$i]=$searchkeyword['alternative'];
	}

	// read featured keywords from database
	$featuredresult = DBQuery($database, "SELECT * FROM vtcal_searchfeatured WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."'" );
    for ($i=0; $i < $featuredresult->numRows(); $i++) {
  		$feature = $featuredresult->fetchRow(DB_FETCHMODE_ASSOC,$i);
		$search_featured[$feature['keyword']]=$feature['featuretext'];
	}
		 		
	for ($i=0; $i<count($keywords); $i++) {
		$kw = strtolower($keywords[$i]);
		if ( !empty($kw) ) {
		  // print featured text if exists
			if ( isset($search_featured) && array_key_exists ($kw, $search_featured) ) {
			  	echo "<tr valign=\"top\">\n  <td colspan=\"3\">\n";
				echo '<table border="0" cellspacing="0" cellpadding="0" width="100%"><tr><td >';
				echo '<table border="0" cellspacing="2" cellpadding="5" width="100%"><tr><td bgcolor="#ffffff">';
				echo $search_featured[$kw];
				echo '</td></tr></table>';
				echo '</td></tr></table>';
			  	echo "  <br><br></td>\n</tr>\n";
			}
			
			$query.= " and (";
			$query.="(e.location LIKE '%".sqlescape($kw)."%') or (e.title LIKE '%".sqlescape($kw)."%') or (e.description LIKE '%".sqlescape($kw)."%') or (e.displayedsponsor LIKE '%".sqlescape($kw)."%') or (c.name LIKE '%".sqlescape($kw)."%')";
		
			// check if there is a matching keyword in the database
			if (!empty($search_keyword)) {
				for ($j=0; $j<count($search_keyword); $j++) {
					if ($kw==$search_keyword[$j]) {
						$kwalt = $search_alternative[$j];
						$query.=" or (e.location LIKE '%".sqlescape($kw)."%') or (e.title LIKE '%".sqlescape($kwalt)."%') or (e.description LIKE '%".sqlescape($kwalt)."%') or (e.displayedsponsor LIKE '%".sqlescape($kwalt)."%') or (c.name LIKE '%".sqlescape($kwalt)."%')";
					}
				} // end: for ($j=0; $j<count($search_keyword); $j++) {
		    }
			$query.=")"; 
		} // if ( !empty($kw) ) 
	}	// for ($i=0; $i<count($keywords); $i++)
} // end: if (!empty($keyword)) {
	
$query.= " ORDER BY e.timebegin ASC, e.wholedayevent DESC";
$result = DBQuery($database, $query );
// read first event if one exists
if ($ievent < $result->numRows()) {
	$event = $result->fetchRow(DB_FETCHMODE_ASSOC,$ievent);
	$event_timebegin  = timestamp2datetime($event['timebegin']);
	$event_timeend    = timestamp2datetime($event['timeend']);
}
else {
?>
        <tr valign="top">
          <td colspan="3"><br><span class="announcement">&nbsp;&nbsp;<?php echo lang('no_events_found'); ?>.<br><br><br><br><br><br></span></td>
        </tr>
<?php	
} // end: else: if ($ievent < $result->numRows())

// print all events of one day
while ($ievent < $result->numRows()) {
	// print event
	echo '        <tr valign="top">',"\n";
	echo '          <td width="1%" align="right" valign="top" nowrap>',"\n";
	echo '          	';
    echo searchresult_date_format($event_timebegin['day'],Day_of_Week_to_Text(Day_of_Week($event_timebegin['month'],$event_timebegin['day'],$event_timebegin['year'])),Month_to_Text($event_timebegin['month']),$event_timebegin['year']);

    //echo Day_of_Week_Abbreviation(Day_of_Week($event_timebegin['month'],$event_timebegin['day'],$event_timebegin['year'])),", ";
    //echo Month_to_Text_Abbreviation($event_timebegin['month'])," ",$event_timebegin['day'],", ",$event_timebegin['year'];
    echo "<br>";
    if ($event['wholedayevent']==0) {
  	  disassemble_eventtime($event);	
			echo timestring($event['timebegin_hour'],$event['timebegin_min'],$event['timebegin_ampm']);
    }
    else {
        if (isset($previousWholeDay) && !$previousWholeDay ) { echo 'All day'; }
        $previousWholeDay = true;
	}
	echo '</td>',"\n";
	echo '          <td width="1%" bgcolor="',$_SESSION["MAINCOLOR"],'"><img src="images/spacer.gif" width="5" height=1" alt=""></td>',"\n";
	echo "          <td width=\"98%\"><a href=\"main.php?view=event&eventid=",$event['eventid'],"\"><b>",highlight_keyword($keyword,$event['title']),"</b></a> -\n";
	echo "            ",highlight_keyword($keyword,$event['category_name'])," ";
	if (!empty($event['location'])) { echo "(".highlight_keyword($keyword,$event['location']).")"; }

    if ((isset($_SESSION["AUTH_SPONSORID"]) && $_SESSION["AUTH_SPONSORID"] == $event['sponsorid']) || !empty($_SESSION["AUTH_ADMIN"])) {
		echo " &nbsp;&nbsp;<a href=\"changeeinfo.php?eventid=",$event['eventid'],"\" title=\"",lang('update_event'),"\">";
		echo "<img src=\"images/nuvola/16x16/actions/color_line.png\" height=\"16\" width=\"16\" alt=\"",lang('update_event'),"\" border=\"0\"></a>";
		
		echo " <a href=\"changeeinfo.php?copy=1&eventid=",$event['eventid'],"\" title=\"",lang('copy_event'),"\">";
		echo "<img src=\"images/nuvola/16x16/actions/editcopy.png\" height=\"16\" width=\"16\" alt=\"",lang('copy_event'),"\" border=\"0\"></a>";
		
		echo " <a href=\"deleteevent.php?eventid=",$event['eventid'],"&check=1\" title=\"",lang('delete_event'),"\">";
		echo "<img src=\"images/nuvola/16x16/actions/button_cancel.png\" height=\"16\" width=\"16\" alt=\"",lang('delete_event'),"\" border=\"0\"></a>";
    }

	echo "<br>\n";
	if (!empty($event['description'])) {
		echo highlight_keyword($keyword,$event['description']);
		echo " \n";
	}
	else {
		echo "<br>\n";
	}
	echo "</td>\n";
	echo '        </tr>',"\n";

	// read next event if one exists
	$ievent++;
	if ($ievent < $result->numRows()) {
		$event = $result->fetchRow(DB_FETCHMODE_ASSOC,$ievent);
		$event_timebegin  = timestamp2datetime($event['timebegin']);
		$event_timeend    = timestamp2datetime($event['timeend']);
	}
} // end: while (...)
	
// keep search log
$searchlogresult = DBQuery($database, "INSERT INTO vtcal_searchlog (calendarid,time,ip,numresults,keyword) VALUES ('".sqlescape($_SESSION["CALENDARID"])."','".sqlescape(date("Y-m-d H:i:s", time()))."','".sqlescape($_SERVER['REMOTE_ADDR'])."','".sqlescape($result->numRows())."','".sqlescape($keyword)."')" );
?>
        <tr valign="top">
          <td colspan="3"><br><br><br></td>
        </tr>
      </FORM>
      </table>