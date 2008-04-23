<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

	function pageheader($title, $headline, $navbaractive, $calendarnavbar, $database) {
	  global $enableViewMonth, $lang;
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title><?php echo $title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php
 echo lang('encoding'); ?>">
    <meta content="en-us" http-equiv=language>
    <link href="stylesheet.php" rel="stylesheet" type="text/css">
		<script language="JavaScript" type="text/javascript"><!--
		function isIE4()
		{ return( navigator.appName.indexOf("Microsoft") != -1 && (navigator.appVersion.charAt(0)=='4') ); }
		
		function new_window(freshurl) {
			SmallWin = window.open(freshurl, 'Calendar','scrollbars=yes,resizable=yes,toolbar=no,height=300,width=400');
			if (!isIE4())	{
				if (window.focus) { SmallWin.focus(); }
			}
			if (SmallWin.opener == null) SmallWin.opener = window;
			SmallWin.opener.name = "Main";
		}
		//-->
		</script>
		<!--[if gte IE 5.5000]>
        <script src="scripts/fix-ie6.js" type="text/javascript"></script>
        <![endif]-->
  </head>
  <body bgcolor="<?php echo $_SESSION["BGCOLOR"]; ?>" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<?php 
    echo $_SESSION["HEADER"];
    require("topnavbar.inc.php"); 
  } // function pageheader;
?>