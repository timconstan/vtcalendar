<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files
?><table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#FFFFFF">
<tr valign="top">
	<td>
	  <br>
<?php echo lang('subscribe_message'); ?>
<br>
<br>
<table border="0" cellspacing="0" cellpadding="4">
<?php
  $color = '#ffffff';
  $iCalDirName = 'calendars/';
  if (is_dir($iCalDirName) && $iCalDir = opendir($iCalDirName)) {
    /* This is the correct way to loop over the directory. */
    while ( ($file = readdir($iCalDir)) != false) { 
      if (strlen($file)>4 && substr($file,strlen($file)-4,4)==".ics") {
  		  if ( $color == "#eeeeee" ) { $color = "#ffffff"; } else { $color = "#eeeeee"; }
?>	
  <tr bgcolor="<?php echo $color; ?>">
    <td bgcolor="<?php echo $color; ?>"><?php echo substr($file,0,strlen($file)-4); ?></td>
    <td bgcolor="<?php echo $color; ?>"><a href="webcal://<?php echo substr(BASEURL,7).$iCalDirName.$file; ?>"><?php echo lang('subscribe'); ?></a> &nbsp; 
	<a href="<?php echo BASEURL.$iCalDirName.$file; ?>"><?php echo lang('download'); ?></a></td>
  </tr>
<?php
      } // end: if (strlen($file)>4 && substr($file,strlen($file)-4,4)==".ics")
    } // end: while ( ($file = readdir($caldir)) != false) { 
?>	
<?php
    closedir($iCalDir);
  } // end: if ($caldir = opendir('calendars'))
	else { 
    $result = DBQuery($database, "SELECT * FROM vtcal_category WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' ORDER BY name" ); 
?>
<?php
    for ($i=0; $i<$result->numRows(); $i++) {
      $category = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
	  	if ( $color == "#eeeeee" ) { $color = "#ffffff"; } else { $color = "#eeeeee"; }
?>	
  <tr bgcolor="<?php echo $color; ?>">
    <td bgcolor="<?php echo $color; ?>"><?php echo $category['name']; ?></td>
    <td bgcolor="<?php echo $color; ?>"><a href="webcal://<?php echo substr(BASEURL,7); ?>export.php?calendar=<?php echo $_SESSION["CALENDARID"]; ?>&type=ical&sponsortype=all&timebegin=today&categoryid=<?php echo $category['id']; ?>"><?php echo lang('subscribe'); ?></a> &nbsp; 
	<a href="<?php echo BASEURL; ?>export.php?type=ical&sponsortype=all&timebegin=today&categoryid=<?php echo $category['id']; ?>"><?php echo lang('download'); ?></a></td>
  </tr>
<?php
    } // end: for ($i=0; $i<$result->numRows(); $i++)
?>	
<?php
  } // end: if ($caldir = opendir('calendars'))
?>
</table>
<br>
&nbsp;<?php echo $_SESSION["NAME"]; ?> (<?php echo lang('whole_calendar'); ?>)&nbsp; 
<a href="webcal://<?php echo substr(BASEURL,7); ?>export.php?calendar=<?php echo $_SESSION["CALENDARID"]; ?>&type=ical&sponsortype=all&timebegin=today"><?php echo lang('subscribe'); ?></a> &nbsp; 
<a href="<?php echo BASEURL; ?>export.php?type=ical&sponsortype=all&timebegin=today"><?php echo lang('download'); ?></a>
	<br>
	<br>
	</td>
</tr>
</table>
