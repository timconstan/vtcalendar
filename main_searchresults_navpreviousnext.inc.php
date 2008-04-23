<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

  $minus_one_day = Add_Delta_Days($showdate['month'],$showdate['day'],$showdate['year'],-1);
  $plus_one_day = Add_Delta_Days($showdate['month'],$showdate['day'],$showdate['year'],1);
  $previous_href = "main.php?view=day&timebegin=".urlencode(datetime2timestamp($minus_one_day['year'],$minus_one_day['month'],$minus_one_day['day'],12,0,"am"))."&sponsorid=".urlencode($sponsorid)."&categoryid=".urlencode($categoryid)."&keyword=".urlencode($keyword); 
  $next_href = "main.php?view=day&timebegin=".urlencode(datetime2timestamp($plus_one_day['year'],$plus_one_day['month'],$plus_one_day['day'],12,0,"am"))."&sponsorid=".urlencode($sponsorid)."&categoryid=".urlencode($categoryid)."&keyword=".urlencode($keyword);
?>