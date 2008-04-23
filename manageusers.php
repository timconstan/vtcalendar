<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_MAINADMIN"] ) { exit; } // additional security

  if (isset($_POST['edit'])) { setVar($edit,$_POST['edit'],'edit'); } else { unset($edit); }
  if (isset($_POST['delete'])) { setVar($delete,$_POST['delete'],'delete'); } else { unset($delete); }
  if (isset($_POST['userid'])) { setVar($userid,$_POST['userid'],'userid'); } else { unset($userid); }


  if ( isset($edit) ) {
	  redirect2URL("changeuserinfo.php?chooseuser=1&userid=".$userid); exit;
	}
  elseif ( isset($delete) ) {
    redirect2URL("deleteuser.php?userid=".$userid); exit;
	}
 
	pageheader(lang('manage_users'),
					 lang('manage_users'),
					 "Update","",$database);
	echo "<BR>\n";
	box_begin("inputbox",lang('manage_users'));
?>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>
<form method="post" name="mainform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<a href="changeuserinfo.php"><?php echo lang('add_new_user'); ?></a>
<?php echo lang('or_modify_existing_user'); ?><br>
<br>
<?php
  $numLines = 15;
?>
<select name="userid" size="<?php echo $numLines; ?>" style="width:200px">
<?php
  $result = DBQuery($database, "SELECT * FROM vtcal_user ORDER BY id" ); 

  for ($i=0; $i<$result->numRows(); $i++) {
    $user = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
?>	
  <option value="<?php echo $user['id']; ?>"><?php echo $user['id']; ?></option>
<?php
  } // end: for ($i=0; $i<$result->numRows(); $i++)
?>	
</select><br>
<input type="submit" name="edit" value="<?php echo lang('button_edit'); ?>">
<input type="submit" name="delete" value="<?php echo lang('button_delete'); ?>"><br>
<br>
<b><?php echo $result->numRows(); ?> Users total</b>
</form>
<script language="JavaScript" type="text/javascript"><!--
document.mainform.userid.focus();
//--></script>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>

<?php
  box_end();
  echo "<br><br>\n";
  require("footer.inc.php");
?>