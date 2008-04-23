<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files
?><table cellspacing="1" cellpadding="7" width="100%" class="weekheader" border="0">
        <tr>
<?php
  // print the days of the week in the header of the table
  for ($weekday=0; $weekday <= 6; $weekday++) {
    
		$iday = Add_Delta_Days($weekfrom['month'],$weekfrom['day'],$weekfrom['year'],$weekday);
    $datediff = Delta_Days($iday['month'],$iday['day'],$iday['year'],date("m"),date("d"),date("Y"));

	echo '<td valign="top" width="14%" align="center">';
    echo "<b>\n";
    echo Day_of_Week_to_Text(($weekday+$week_start)%7); // use modulus 7 as week can begin with Sunday or Monday
    echo "<br>\n";
    echo "<a href=\"main.php?view=day&timebegin=",urlencode(datetime2timestamp($iday['year'],$iday['month'],$iday['day'],12,0,"am")),"&timeend=",urlencode(datetime2timestamp($iday['year'],$iday['month'],$iday['day'],11,59,"pm")),"\">".week_header_date_format($iday['day'],Month_to_Text($iday['month']),0,3)."</a>\n";

    if (!empty($_SESSION["AUTH_SPONSORID"])) { // display "add event" icon
			echo "<br><a href=\"addevent.php?timebegin_year=".$iday['year']."&timebegin_month=".$iday['month']."&timebegin_day=".$iday['day']."\" title=\"",lang('add_new_event'),"\">";
      echo '<img src="images/nuvola/16x16/actions/filenew.png" height="16" width="16" alt="',lang('add_new_event'),'" border="0"></a>';
    }

    echo "</b>\n</td>\n";
  }
?>		
        </tr>
        <tr>
<?php
  $ievent = 0;
  // read all events for this week from the DB
  $query = "SELECT e.id AS eventid,e.timebegin,e.timeend,e.sponsorid,e.title,e.wholedayevent,e.categoryid,c.id,c.name AS category_name FROM vtcal_event_public e, vtcal_category c WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.categoryid = c.id AND e.timebegin >= '".sqlescape($weekfrom['timestamp'])."' AND e.timeend <= '".sqlescape($weekto['timestamp'])."'";
  if ($sponsorid != "all")  { $query.= " AND (e.sponsorid='".sqlescape($sponsorid)."')"; }
  
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
	
  if (!empty($keyword)) { $query.= " AND ((e.title LIKE '%".sqlescape($keyword)."%') OR (e.description LIKE '%".sqlescape($keyword)."%'))"; }
  $query.= " ORDER BY e.timebegin ASC, e.wholedayevent DESC";
	$result = DBQuery($database, $query );

  // read first event if one exists
  if ($ievent < $result->numRows()) {
    $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$ievent);
    $event_timebegin  = timestamp2datetime($event['timebegin']);
    $event_timeend    = timestamp2datetime($event['timeend']);
  }

  // output event info for every day
  for ($weekday = 0; $weekday <= 6; $weekday++) {
	  $events_per_day = 0;
    $iday = Add_Delta_Days($weekfrom['month'],$weekfrom['day'],$weekfrom['year'],$weekday);
    $iday['timebegin'] = datetime2timestamp($iday['year'],$iday['month'],$iday['day'],0,0,"am");
    $iday['timeend']   = datetime2timestamp($iday['year'],$iday['month'],$iday['day'],11,59,"pm");

    $iday['css'] = datetoclass($iday['month'],$iday['day'],$iday['year']);
    $iday['color'] = datetocolor($iday['month'],$iday['day'],$iday['year'],$colorpast,$colortoday,$colorfuture);
    
		echo "<td class=\"",$iday['css'],"\" bgcolor=\"",$iday['color'],"\" valign=\"top\" width=\"14%\">";
    $event['css'] = $iday['css'];
    $event['color'] = $iday['color'];

    // print all events of one day
    while (($ievent < $result->numRows())
           &&
           ($event_timebegin['year']==$iday['year']) &&
           ($event_timebegin['month']==$iday['month']) &&
           ($event_timebegin['day']==$iday['day'])) {
      $events_per_day++;
      // print event
      print_week_event($event,$nopreview);

      // read next event if one exists
      $ievent++;
      if ($ievent < $result->numRows()) {
        $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$ievent);
        $event_timebegin  = timestamp2datetime($event['timebegin']);
        $event_timeend    = timestamp2datetime($event['timeend']);
        $event['css'] = $iday['css'];
        $event['color'] = $iday['color'];
      }
    } // end: while (...)
		
		// making sure the spacing looks ok, even if there are no or few (<3) events per day
		if ( $events_per_day < 3 ) {
		  for ($i=$events_per_day; $i < 3; $i++) {
			  echo "<br><br><br>";
			}
		}
    echo "&nbsp;\n</td>\n";
  } // end: for ($weekday = 0; $weekday <= 6; $weekday++)
?>
        </tr>
      </table>