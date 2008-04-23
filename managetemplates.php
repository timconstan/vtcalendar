<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
 
	pageheader(lang('manage_templates'),
					 lang('manage_templates'),
					 "Update","",$database);
	echo "<BR>\n";
	box_begin("inputbox",lang('manage_templates'));

  $result = DBQuery($database, "SELECT * FROM vtcal_template WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND sponsorid='".sqlescape($_SESSION["AUTH_SPONSORID"])."' ORDER BY name" ); 
?>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>
<a href="addtemplate.php"><?php echo lang('add_new_template'); ?></a>
<?php
  if ($result->numRows() > 0 ) {
?>
<?php echo lang('or_modify_existing_template'); ?><br>
<br>
<table border="0" cellspacing="0" cellpadding="4">
  <tr bgcolor="#CCCCCC">
    <td bgcolor="#CCCCCC"><b><?php echo lang('template_name'); ?></b></td>
    <td bgcolor="#CCCCCC">&nbsp;</td>
  </tr>
<?php
  $color = "#eeeeee";
  for ($i=0; $i<$result->numRows(); $i++) {
    $template = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
		if ( $color == "#eeeeee" ) { $color = "#ffffff"; } else { $color = "#eeeeee"; }
?>	
  <tr bgcolor="<?php echo $color; ?>">
    <td bgcolor="<?php echo $color; ?>"><?php echo $template['name']; ?></td>
    <td bgcolor="<?php echo $color; ?>"><a href="updatetinfo.php?templateid=<?php echo $template['id']; ?>"><?php echo lang('edit'); ?></a> 
	&nbsp;<a href="deletetemplate.php?templateid=<?php echo $template['id']; ?>"><?php echo lang('delete'); ?></a></td>
  </tr>
<?php
  } // end: for ($i=0; $i<$result->numRows(); $i++)
?>	
</table>
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