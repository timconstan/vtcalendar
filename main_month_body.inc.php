<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files
?><table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="<?php echo $_SESSION["GRIDCOLOR"]; ?>">
        <tr align="center">
        <?php if($week_start == 0){?>
          <td width="14%"><br><strong><?php echo lang('sunday');?></strong></td>
         <?php } ?>
          <td width="14%"><br><strong><?php echo lang('monday');?></strong></td>
          <td width="14%"><br><strong><?php echo lang('tuesday');?></strong></td>
          <td width="14%"><br><strong><?php echo lang('wednesday');?></strong></td>
          <td width="14%"><br><strong><?php echo lang('thursday');?></strong></td>
          <td width="14%"><br><strong><?php echo lang('friday');?></strong></td>
          <td width="14%"><br><strong><?php echo lang('saturday');?></strong></td>
        <?php if($week_start == 1){?>
          <td width="14%"><br><strong><?php echo lang('sunday');?></strong></td>
         <?php } ?>
        </tr>
<?php 
  $ievent = 0;
  // read all events for this week from the DB
  $query = "SELECT e.id AS eventid,e.timebegin,e.timeend,e.sponsorid,e.title,e.wholedayevent,e.categoryid,c.id,c.name AS category_name FROM vtcal_event_public e, vtcal_category c ";
	$query .= "WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.categoryid = c.id AND e.timebegin >= '".sqlescape($month1start['timestamp'])."' AND e.timeend <= '".sqlescape($month1end['timestamp'])."'";
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
	   if ($categoryid != 0) { $query.= " AND (e.categoryid='".sqlescape($categoryid)."')"; }
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

  // print 6 lines for the weeks
  for ($iweek=1; $iweek<=6; $iweek++) {
    // first day of the week
    $weekstart = Add_Delta_Days($month1start['month'],$month1start['day'],$month1start['year'],($iweek-1)*7);
    $weekstart['timestamp'] = datetime2timestamp($weekstart['year'],$weekstart['month'],$weekstart['day'],12,0,"am");

    // print the 5th and the 6th week only if the days are still in this month
    if (($iweek < 5) || ($weekstart['month'] == $month1['month'])) {
      echo "<tr>\n";

      // output event info for every day
      for ($weekday = 0; $weekday <= 6; $weekday++) {
        // calculate the appropriate day for the cell of the calendar
        $iday = Add_Delta_Days($month1start['month'],$month1start['day'],$month1start['year'],($iweek-1)*7+$weekday);
        $iday['timebegin'] = datetime2timestamp($iday['year'],$iday['month'],$iday['day'],0,0,"am");
        $iday['timeend']   = datetime2timestamp($iday['year'],$iday['month'],$iday['day'],11,59,"pm");

        $iday['css'] = datetoclass($iday['month'],$iday['day'],$iday['year']);
        $iday['color'] = datetocolor($iday['month'],$iday['day'],$iday['year'],$colorpast,$colortoday,$colorfuture);
        $datediff = Delta_Days($iday['month'],$iday['day'],$iday['year'],date("m"),date("d"),date("Y"));
        
				echo "<td bgcolor=\"",$iday['color'],"\" ";
				echo "valign=\"top\" align=\"center\">\n";

        echo '<table cellspacing="0" cellpadding="4" border="0" width="100%">',"\n";
				echo '<tr>',"\n";
				echo '  <td align="left" bgcolor="'.$iday['color'].'">',"\n";

				echo "<a ";
			
				if ($datediff > 0) { echo 'class="past" '; }
				
				echo "href=\"main.php?view=day&timebegin=",urlencode(datetime2timestamp($iday['year'],$iday['month'],$iday['day'],12,0,"am")),"&timeend=",urlencode(datetime2timestamp($iday['year'],$iday['month'],$iday['day'],11,59,"pm")),"&sponsorid=",urlencode($sponsorid),"&categoryid=",urlencode($categoryid),"&keyword=",urlencode($keyword),"\">";
				echo "<b>",$iday['day'],"</b>";
				echo "</a>\n";
				echo '  </td>',"\n";
        if (!empty($_SESSION["AUTH_SPONSORID"])) { // display "add event" icon
  				echo '  <td align="right" bgcolor="'.$iday['color'].'">',"\n";
          echo "<a href=\"addevent.php?timebegin_year=".$iday['year']."&timebegin_month=".$iday['month']."&timebegin_day=".$iday['day']."\" title=\"",lang('add_new_event'),"\">";
          echo '<img src="images/nuvola/16x16/actions/filenew.png" height="16" width="16" alt="',lang('add_new_event'),'" border="0"></a>';
  				echo '  </td>',"\n";
        }
				echo '</tr>',"\n";
				echo '<tr>',"\n";
				echo '  <td colspan="2">',"\n";

      	echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"$iday[color]\">\n";
        // print all events of one day
        while (($ievent < $result->numRows())
                &&
               ($event_timebegin['year']==$iday['year']) &&
               ($event_timebegin['month']==$iday['month']) &&
               ($event_timebegin['day']==$iday['day'])) {

          echo "<tr><td class=\"".$iday['css']."\" bgcolor=\"".$iday['color']."\" align=\"left\" valign=\"top\"";
					if ( $datediff > 0 ) {
						// echo " style=\"color:#999999\"";
					}
					echo ">&#8226;</td>\n";
          echo "<td class=\"".$iday['css']."\" bgcolor=\"".$iday['color']."\" align=\"left\" valign=\"top\">\n";

      	  // print event
					echo "<a ";
					if ( $datediff > 0 ) {
						// echo "style=\"color:#999999\" ";
					}
					echo "href=\"main.php?view=event&eventid=",$event['eventid'],"\">";
					echo HTMLSpecialChars($event['title']);
					// add little update, delete icons
					if ((isset($_SESSION["AUTH_SPONSORID"]) && $_SESSION["AUTH_SPONSORID"] == $event['sponsorid']) || !empty($_SESSION["AUTH_ADMIN"])) {
						echo " <br><a href=\"changeeinfo.php?eventid=",$event['eventid'],"\" title=\"",lang('update_event'),"\">";
						echo "<img src=\"images/nuvola/16x16/actions/color_line.png\" height=\"16\" width=\"16\" alt=\"",lang('update_event'),"\" border=\"0\"></a>";
			
						echo " <a href=\"changeeinfo.php?copy=1&eventid=",$event['eventid'],"\" title=\"",lang('copy_event'),"\">";
						echo "<img src=\"images/nuvola/16x16/actions/editcopy.png\" height=\"16\" width=\"16\" alt=\"",lang('copy_event'),"\" border=\"0\"></a>";
			
						echo " <a href=\"deleteevent.php?eventid=",$event['eventid'],"&check=1\" title=\"",lang('delete_event'),"\">";
						echo "<img src=\"images/nuvola/16x16/actions/button_cancel.png\" height=\"16\" width=\"16\" alt=\"",lang('delete_event'),"\" border=\"0\"></a>";
					}
					echo "</a>";
					echo "<br>\n";

          echo "</td></tr>\n";
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
        echo "</table>\n";

				echo '  <br></td>',"\n";
				echo '</tr>',"\n";
				echo '</table>',"\n";

				echo "</td>\n";
      } // end: for ($weekday = 0; $weekday <= 6; $weekday++)
      echo "</tr>\n";
    } // end: if (($iweek < 5) || ($weekstart[month] == $month1[month])
  } // end: for ($iweek=1; $iweek<=6; $iweek++)

?>				
      </table>