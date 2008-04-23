<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

echo day_view_date_format($showdate['day'], Day_of_Week_to_Text(Day_of_Week($showdate['month'],$showdate['day'],$showdate['year'])),Month_to_Text($showdate['month']),$showdate['year']);
	 
  if (!empty($_SESSION["AUTH_SPONSORID"])) { // display "add event" icon
    echo " <a href=\"addevent.php?timebegin_year=".$showdate['year']."&timebegin_month=".$showdate['month']."&timebegin_day=".$showdate['day']."\" title=\"",lang('add_new_event'),"\">";
    echo '<img src="images/nuvola/16x16/actions/filenew.png" height="16" width="16" alt="',lang('add_new_event'),'" border="0"></a>';
  }
?>