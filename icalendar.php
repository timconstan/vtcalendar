<?php
  session_start ();
  // session_start introduces a cache header which results in problems with download unless it's changed
	header("Cache-control: private");

  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');
	require_once("icalendar.inc.php");

  if (isset($_GET['eventid'])) { setVar($eventid,$_GET['eventid'],'eventid'); } else { unset($eventid); }
  if (isset($_GET['timebegin'])) { setVar($timebegin,$_GET['timebegin'],'timebegin'); } else { unset($timebegin); }
  if (isset($_GET['timeend'])) { setVar($timeend,$_GET['timeend'],'timeend'); } else { unset($timeend); }
  if (isset($_GET['categoryid'])) { setVar($categoryid,$_GET['categoryid'],'categoryid'); } else { unset($categoryid); }
  if (isset($_GET['sponsorid'])) { setVar($sponsorid,$_GET['sponsorid'],'sponsorid'); } else { unset($sponsorid); }
  if (isset($_GET['keyword'])) { setVar($keyword,$_GET['keyword'],'keyword'); } else { unset($keyword); }

  $database = DBopen();
  if (!viewauthorized($database)) { exit; }

  Header("Content-Type: text/calendar; charset=\"utf-8\"; name=\"icalendar.ics\"");
  Header("Content-disposition: attachment; filename=icalendar.ics");
  echo getICalHeader();
	
  /* if the starting point not passed as a param then use defaults */
  if (!isset($timebegin)) { $timebegin = ""; }
  if (!isset($timeend)) { $timeend = ""; }
  if (!isset($categoryid)) { $categoryid=0; }
  if (!isset($sponsorid)) { $sponsorid="all"; }
  if (!isset($keyword)) { $keyword=""; }

  /* print list with events */
  $query = "SELECT e.id,e.timebegin,e.timeend,e.sponsorid,e.title,e.wholedayevent,e.categoryid,e.description,e.location,e.price,e.contact_name,e.contact_phone,e.contact_email,e.url,c.id AS category_id,c.name AS category_name,e.displayedsponsor AS sponsor_name, e.displayedsponsorurl AS sponsor_url FROM vtcal_event_public e, vtcal_category c WHERE e.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND c.calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND e.categoryid = c.id";
  if (!empty($eventid))  { $query.= " AND e.id='".sqlescape($eventid)."'"; }
  if (!empty($timebegin)) { $query.= " AND e.timebegin >= '".sqlescape($timebegin)."'"; }
  if (!empty($timeend)) { $query.= " AND e.timeend <= '".sqlescape($timeend)."'"; }
  if ($sponsorid != "all")  { $query.= " AND e.sponsorid='".sqlescape($sponsorid)."'"; }
  if ($categoryid != 0) { $query.= " AND e.categoryid='".sqlescape($categoryid)."'"; }
  if (!empty($keyword)) { $query.= " AND ((e.title LIKE '%".sqlescape($keyword)."%') OR (e.description LIKE '%".sqlescape($keyword)."%'))"; }

  $query.= " ORDER BY e.timebegin asc, e.wholedayevent desc";
  $result = DBQuery($database, $query ); 

  /* read first event if one exists */
  $ievent = 0;
  while ( $ievent < $result->numRows() ) {
    $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$ievent);

    $ievent++;

    echo getICalFormat($event);
  } /* end: while (...) */
  
	echo getICalFooter();
?>