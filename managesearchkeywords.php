<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_ADMIN"]) { exit; } // additional security
 
	pageheader(lang('manage_search_keywords'),
					 lang('manage_search_keywords'),
					 "Update","",$database);
	echo "<BR>\n";
	box_begin("inputbox",lang('manage_search_keywords'));

  $result = DBQuery($database, "SELECT * FROM vtcal_searchkeyword WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' ORDER BY keyword" ); 
?>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>
<?php echo lang('manage_search_keywords_message'); ?><br>
<br>

<a href="addnewkeywordpair.php"><?php echo lang('add_new_keyword_pair'); ?></a>
<?php
  if ($result->numRows() > 0 ) {
?>
<?php echo lang('or_manage_existing_pairs'); ?><br>
<br>
<table border="0" cellspacing="0" cellpadding="4">
  <tr bgcolor="#CCCCCC">
    <td bgcolor="#CCCCCC"><b><?php echo lang('keyword'); ?></b></td>
    <td bgcolor="#CCCCCC"><b><?php echo lang('alternative_keyword'); ?></b></td>
    <td bgcolor="#CCCCCC">&nbsp;</td>
  </tr>
<?php
  $color = "#eeeeee";
  for ($i=0; $i<$result->numRows(); $i++) {
    $searchkeyword = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
		if ( $color == "#eeeeee" ) { $color = "#ffffff"; } else { $color = "#eeeeee"; }
?>	
  <tr bgcolor="<?php echo $color; ?>">
    <td bgcolor="<?php echo $color; ?>"><?php echo $searchkeyword['keyword']; ?></td>
    <td bgcolor="<?php echo $color; ?>"><?php echo $searchkeyword['alternative']; ?></td>
    <td bgcolor="<?php echo $color; ?>"><a href="deletekeywordpair.php?id=<?php echo $searchkeyword['id']; ?>"><?php echo lang('delete'); ?></a></td>
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