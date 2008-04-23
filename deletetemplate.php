<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  if (isset($_GET['templateid'])) { setVar($templateid,$_GET['templateid'],'templateid'); } else { unset($templateid); }

  $database = DBopen();
  if (!authorized($database)) { exit; }

  if (!empty($templateid)) {
	  $result = DBQuery($database, "DELETE FROM vtcal_template WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND sponsorid='".sqlescape($_SESSION["AUTH_SPONSORID"])."' AND id='".sqlescape($templateid)."'" );
  }

  redirect2URL("managetemplates.php");
  exit;
?>