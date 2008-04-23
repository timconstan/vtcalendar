<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

  // read first event if one exists
  if ($result->numRows()>0) {
    $event['calendarid'] = $_SESSION["CALENDARID"];
    $event['id'] = $eventid;
		print_event($event);    
	}
?>