<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
 
	pageheader(lang('manage_events'),
					 lang('manage_events'),
					 "Update","",$database);
	echo "<BR>\n";
	box_begin("inputbox",lang('manage_events'));

  $ievent = 0;
  $today = Decode_Date_US(date("m/d/Y"));
  $today['timestamp_daybegin']=datetime2timestamp($today['year'],$today['month'],$today['day'],12,0,"am");

  // print list with events
  $query = "SELECT e.id AS id,e.approved,e.rejectreason,e.timebegin,e.timeend,e.repeatid,e.sponsorid,e.displayedsponsor,e.displayedsponsorurl,e.title,e.wholedayevent,e.categoryid,e.description,e.location,e.price,e.contact_name,e.contact_phone,e.contact_email,e.url,c.id AS cid,c.name AS category_name,s.id AS sid,s.name AS sponsor_name,s.url AS sponsor_url FROM vtcal_event e, vtcal_category c, vtcal_sponsor s WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND s.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.categoryid = c.id AND e.sponsorid = s.id AND e.sponsorid='".sqlescape($_SESSION["AUTH_SPONSORID"])."'";
//  $query .= " AND e.timebegin >= '".$today['timestamp_daybegin']."'";
  $query.= " ORDER BY e.timebegin ASC,e.wholedayevent DESC";

  $result = DBQuery($database, $query ); 
?>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>

<a href="addevent.php"><?php echo lang('add_new_event'); ?></a>
<?php
  if ($result->numRows() > 0 ) {
?>
<?php echo lang('or_manage_existing_events'); ?><br>
<br>
<?php
/*
Below you see a list of all <i>future</i> events. <span style="color:#FF0000; font-weight:bold">Past events are NOT shown here.</span><br>
However, you can use the <a href="main.php?view=day">day</a>/<a href="main.php?view=week">week</a>/<a href="main.php?view=month">month</a> view to find, edit and delete past events.<br>
<br>
*/
?>
<table border="0" cellspacing="0" cellpadding="4">
  <tr bgcolor="#CCCCCC">
    <td bgcolor="#CCCCCC"><b><?php echo lang('date'); ?>/<?php echo lang('time'); ?></b></td>
    <td bgcolor="#CCCCCC"><b><?php echo lang('title'); ?></b></td>
    <td bgcolor="#CCCCCC"><b><?php echo lang('status'); ?></b></td>
    <td bgcolor="#CCCCCC">&nbsp;</td>
  </tr>
<?php
  $color = "#eeeeee";
  for ($i=0; $i<$result->numRows(); $i++) {
    $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
    disassemble_eventtime($event);

		// keep track of repeat id and print recurring events only once
  	if (!empty($event['repeatid'])) { 
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
    <td bgcolor="<?php echo $color; ?>" valign="top"><b><?php echo $event['title']; ?></b></td>
    <td bgcolor="<?php echo $color; ?>" valign="top">
<?php
    if ($event['approved'] == -1) {
      echo '<FONT color="red"><B>rejected</B></FONT>';
      if (!empty($event['rejectreason'])) { echo "<BR><B>Reason:</B> ",$event['rejectreason']; }
    }
    elseif ($event['approved'] == 0) {
      echo '<FONT color="blue">',lang('submitted_for_approval'),'</FONT><br>';
    }
    elseif ($event['approved'] == 1) {
      echo '<FONT color="green">',lang('approved'),'</FONT><br>';
    }
?>
    </td>
    <td bgcolor="<?php echo $color; ?>" valign="top"><a href="changeeinfo.php?eventid=<?php echo $event['id']; ?>&update=1"><?php echo lang('edit'); ?></a>&nbsp; 
	<a href="changeeinfo.php?eventid=<?php echo $event['id']; ?>&amp;copy=1"><?php echo lang('copy'); ?></a>&nbsp; 
	<a href="deleteevent.php?eventid=<?php echo $event['id']; ?>&check=1"><?php echo lang('delete'); ?></a></td>
  </tr>
<?php
  } // end: for ($i=0; $i<$result->numRows(); $i++)
?>	
  <tr bgcolor="#CCCCCC">
    <td colspan="4" bgcolor="#CCCCCC">&nbsp;</td>
  </tr>
</table>
<br>
<b><?php echo lang('status_info_message'); ?></b><br>
<table border="0" cellspacing="0" cellpadding="3">
<tr>
  <td><FONT color="red"><B><?php echo lang('rejected'); ?></B></FONT></td>
  <td><?php echo lang('rejected_explanation'); ?></td>
<tr>
  <td><FONT color="blue"><?php echo lang('submitted_for_approval'); ?></FONT></td>
  <td><?php echo lang('submitted_for_approval_explanation'); ?></td>
<tr>
  <td><FONT color="green"><?php echo lang('approved'); ?></FONT></td>
  <td><?php echo lang('approved_explanation'); ?></td>
</tr></table>
<br>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>

<?php
  } // end: if ($result->numRows() > 0 )
  box_end();
  echo "<br><br>\n";
  require("footer.inc.php");
?>