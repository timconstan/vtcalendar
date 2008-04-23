<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_ADMIN"]) { exit; } // additional security

  if (isset($_POST['edit'])) { setVar($edit,$_POST['edit'],'edit'); } else { unset($edit); }
  if (isset($_POST['delete'])) { setVar($delete,$_POST['delete'],'delete'); } else { unset($delete); }
  if (isset($_POST['id'])) { setVar($id,$_POST['id'],'sponsorid'); } else { unset($id); }

  if ( isset($edit) ) {
	  redirect2URL("editsponsor.php?id=".$id); exit;
	}
  elseif ( isset($delete) ) {
    $result = DBQuery($database, "SELECT * FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($id)."'" ); 
    $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
    
		if ( $sponsor['admin'] == 0 ) {
	    redirect2URL("deletesponsor.php?id=".$id);
		}
	}
 
	pageheader(lang('manage_sponsors'),
					 lang('manage_sponsors'),
					 "Update","",$database);
	echo "<BR>\n";
	box_begin("inputbox",lang('manage_sponsors'));
?>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>
<form method="post" name="mainform" action="managesponsors.php">
<a href="editsponsor.php"><?php echo lang('add_new_sponsor'); ?></a>
<?php echo lang('or_modify_existing_sponsor'); ?><br>
<br>
<?php
  $numLines = 15;
?>
<select name="id" size="<?php echo $numLines; ?>" style="width:250px">
<?php
  $result = DBQuery($database, "SELECT * FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' ORDER BY name" ); 

  for ($i=0; $i<$result->numRows(); $i++) {
    $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
?>	
  <option value="<?php echo $sponsor['id']; ?>"><?php echo $sponsor['name']; ?></option>
<?php
  } // end: for ($i=0; $i<$result->numRows(); $i++)
?>	
</select><br>
<input type="submit" name="edit" value="<?php echo lang('button_edit'); ?>">
<input type="submit" name="delete" value="<?php echo lang('button_delete'); ?>"><br>
<br>
<b><?php echo $result->numRows(); ?> <?php echo lang('sponsors_total'); ?></b>
</form>
<script language="JavaScript" type="text/javascript"><!--
document.mainform.id.focus();
//--></script>
<form method="post" action="update.php">
	<input type="submit" name="back" value="<?php echo lang('back_to_menu'); ?>">
</form>

<?php
  box_end();
  echo "<br><br>\n";
  require("footer.inc.php");
?>