<?php
	if (!file_exists("config.inc.php")) {
	  Header("Location: install/index.php");
		exit;
	}
	
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

//  $serverpath = $GLOBALS["SCRIPT_URI"];
//  if ($serverpath[strlen($serverpath)]!="/") { 
//    $serverpath = substr($serverpath,0,strrpos($serverpath,"/")+1); 
//  }
//  Header("Location: ".$serverpath."main.php");
  Header("Location: main.php");
  exit;
?>