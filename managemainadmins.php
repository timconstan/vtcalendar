<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_MAINADMIN"]) { exit; } // additional security

	pageheader(lang('manage_main_admins'),
					 lang('manage_main_admins'),
					 "Update","",$database);
	echo "<BR>\n";
	box_begin("inputbox",lang('manage_main_admins'));
?>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>
<form method="post" name="mainform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<a href="addmainadmin.php"><?php echo lang('add_new_main_admin'); ?></a>
<?php echo lang('or_delete_existing'); ?><br>
<br>
<table border="0" cellspacing="0" cellpadding="4">
  <tr bgcolor="#CCCCCC">
    <td bgcolor="#CCCCCC"><b><?php echo lang('user_id'); ?></b></td>
    <td bgcolor="#CCCCCC">&nbsp;</td>
  </tr>
<?php
  $result = DBQuery($database, "SELECT * FROM vtcal_adminuser ORDER BY id" ); 

  $color = "#eeeeee";
  for ($i=0; $i<$result->numRows(); $i++) {
    $user = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
		if ( $color == "#eeeeee" ) { $color = "#ffffff"; } else { $color = "#eeeeee"; }
?>	
  <tr bgcolor="<?php echo $color; ?>">
    <td bgcolor="<?php echo $color; ?>"><?php echo $user['id']; ?></td>
    <td bgcolor="<?php echo $color; ?>"><a href="deletemainadmin.php?mainuserid=<?php echo $user['id']; ?>"><?php echo lang('delete'); ?></a></td>
  </tr>
<?php
  } // end: for ($i=0; $i<$result->numRows(); $i++)
?>	
</table>
<br>
<b><?php echo $result->numRows(); ?> <?php echo lang('main_admins_total'); ?></b>
</form>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>
<?php
  box_end();
  echo "<br><br>\n";
  require("footer.inc.php");
?>