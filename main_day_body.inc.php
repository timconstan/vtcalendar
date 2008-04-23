<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files
?><table cellspacing="5" cellpadding="0" width="100%" bgcolor="#ffffff" border="0">
<?php
  $ievent = 0;
  // read all events for this week from the DB
  $query = "SELECT e.id AS eventid,e.timebegin,e.timeend,e.sponsorid,e.title,e.location,e.description,e.wholedayevent,e.categoryid,c.id,c.name AS category_name FROM vtcal_event_public e, vtcal_category c ";
	$query.= "WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.categoryid = c.id AND e.timebegin >= '".sqlescape($showdate['timestamp_daybegin'])."' AND e.timeend <= '".sqlescape($showdate['timestamp_dayend'])."'";
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
	else {
?>
        <tr valign="top">
          <td colspan="3"><br><span class="announcement">&nbsp;&nbsp;<?php echo lang('no_events');?> </span></td>
        </tr>
<?php	
	} // end: else: if ($ievent < $result->numRows())

  $previousWholeDay = false;
	// print all events of one day
	while ($ievent < $result->numRows()) {
		if ( $previousWholeDay && $event['wholedayevent']==0 ) {  
			echo '        <tr valign="top" bgcolor="#999999">',"\n";
	  	echo '          <td colspan="3"><img src="images/spacer.gif" alt="" width="1" height="1"></td>',"\n";

  		echo '        </tr>',"\n";
		}

		// print event
 	  disassemble_eventtime($event);	
		$datediff = Delta_Days($event['timebegin_month'],$event['timebegin_day'],$event['timebegin_year'],date("m"),date("d"),date("Y"));
		echo '        <tr valign="top">',"\n";
		echo '          <td width="1%" align="right" valign="top" nowrap"';
    if ( $datediff > 0 ) {
		  echo " class=\"past\" style=\"background-color : #ffffff\"";
		}
		echo '>',"\n";
		echo '          	';
    if ($event['wholedayevent']==0) {
			echo timestring($event['timebegin_hour'],$event['timebegin_min'],$event['timebegin_ampm']);
    }
		else {
		  if (!$previousWholeDay ) { echo lang('all_day'); }
      $previousWholeDay = true;
		}
		echo '</td>',"\n";
		echo '          <td width="1%" bgcolor="',$_SESSION['MAINCOLOR'],'"><img src="images/spacer.gif" width="5" height=1" alt=""></td>',"\n";
		echo "          <td width=\"98%\"";
    if ( $datediff > 0 ) {
		  echo " class=\"past\" style=\"background-color : #ffffff\"";
		}
		echo ">";
		echo "<a "; 
                if ( $datediff > 0 ) {
		  echo " class=\"past\" style=\"background-color : #ffffff\"";
		}
                echo "href=\"main.php?view=event&eventid=",$event['eventid'],"\"><b>",$event['title'],"</b></a> -\n";
		echo "            ",$event['category_name']," ";
		if ( !empty($event['location']) ) { echo "(".$event['location'].")"; }

    if ((isset($_SESSION["AUTH_SPONSORID"]) && $_SESSION["AUTH_SPONSORID"]==$event['sponsorid']) || !empty($_SESSION["AUTH_ADMIN"])) {
      echo " &nbsp;&nbsp;<a href=\"changeeinfo.php?eventid=",$event['eventid'],"\" title=\"",lang('update_event'),"\">";
      echo "<img src=\"images/nuvola/16x16/actions/color_line.png\" height=\"16\" width=\"16\" alt=\"",lang('update_event'),"\" border=\"0\"></a>";

      echo " <a href=\"changeeinfo.php?copy=1&eventid=",$event['eventid'],"\" title=\"",lang('copy_event'),"\">";
      echo "<img src=\"images/nuvola/16x16/actions/editcopy.png\" height=\"16\" width=\"16\" alt=\"",lang('copy_event'),"\" border=\"0\"></a>";

      echo " <a href=\"deleteevent.php?eventid=",$event['eventid'],"&check=1\" title=\"",lang('delete_event'),"\">";
      echo "<img src=\"images/nuvola/16x16/actions/button_cancel.png\" height=\"16\" width=\"16\" alt=\"",lang('delete_event'),"\" border=\"0\"></a>";
    }

		echo "<br>\n";
		if (!empty($event['description'])) {
  		echo "            ";
			if (strlen($event['description']) < 140 ) {
			  echo $event['description'];
			} 
			else {
			  echo substr($event['description'],0,140);
		    echo "... \n";
    		echo "            <a href=\"main.php?view=event&eventid=",$event['eventid'],"\">more</a>";
      }
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
?>
        <tr valign="top">
          <td colspan="3"><br><br><br><br><br><br><br><br><br><br></td>
        </tr>
      </table>