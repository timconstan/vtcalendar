<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');
  logout();
	
  // reroute to calendar home page
  redirect2URL("index.php");
  exit;
?>