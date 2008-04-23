<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_ADMIN"]) { exit; } // additional security
 
	pageheader(lang('manage_event_categories'),
					 lang('manage_event_categories'),
					 "Update","",$database);
	echo "<BR>\n";
	box_begin("inputbox",lang('manage_event_categories'));

  $result = DBQuery($database, "SELECT * FROM vtcal_category WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' ORDER BY name" ); 
?>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>
<a href="addnewcategory.php"><?php echo lang('add_new_event_category'); ?></a>
<?php
  if ($result->numRows() > 0 ) {
?>
<?php echo lang('or_modify_existing_category'); ?><br>
<br>
<table border="0" cellspacing="0" cellpadding="4">
  <tr bgcolor="#CCCCCC">
    <td bgcolor="#CCCCCC"><b><?php echo lang('category_name'); ?></b></td>
    <td bgcolor="#CCCCCC">&nbsp;</td>
  </tr>
<?php
  $color = "#eeeeee";
  for ($i=0; $i<$result->numRows(); $i++) {
    $category = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
		if ( $color == "#eeeeee" ) { $color = "#ffffff"; } else { $color = "#eeeeee"; }
?>	
  <tr bgcolor="<?php echo $color; ?>">
    <td bgcolor="<?php echo $color; ?>"><?php echo $category['name']; ?></td>
    <td bgcolor="<?php echo $color; ?>"><a href="renamecategory.php?categoryid=<?php echo $category['id']; ?>"><?php echo lang('rename'); ?></a> 
	&nbsp;<a href="deletecategory.php?categoryid=<?php echo $category['id']; ?>"><?php echo lang('delete'); ?></a></td>
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