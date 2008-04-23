<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_MAINADMIN"] ) { exit; } // additional security

  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['save'])) { setVar($save,$_POST['save'],'save'); } else { unset($save); }
  if (isset($_POST['cal']) && isset($_POST['cal']['id'])) { setVar($cal['id'],$_POST['cal']['id'],'calendarid'); }
	elseif (isset($_GET['cal']) && isset($_GET['cal']['id'])) { setVar($cal['id'],$_GET['cal']['id'],'calendarid'); } 
	else { unset($cal); }


  if (isset($cancel)) {
    redirect2URL("managecalendars.php");
    exit;
  }

  // make sure the calendar exists
	$result = DBQuery($database, "SELECT * FROM vtcal_calendar WHERE id='".sqlescape($cal['id'])."'" );
	if ( $result->numRows() != 1 ) {
		redirect2URL("managecalendars.php");
		exit;
	}
	else {
		$c = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
	}

  if (isset($save) ) {
	  $result = DBQuery($database, "DELETE FROM vtcal_event WHERE calendarid='".sqlescape($cal['id'])."'" );
	  $result = DBQuery($database, "DELETE FROM vtcal_event_repeat WHERE calendarid='".sqlescape($cal['id'])."'" );
	  $result = DBQuery($database, "DELETE FROM vtcal_event_public WHERE calendarid='".sqlescape($cal['id'])."'" );
	  $result = DBQuery($database, "DELETE FROM vtcal_calendarviewauth WHERE calendarid='".sqlescape($cal['id'])."'" );
	  $result = DBQuery($database, "DELETE FROM vtcal_auth WHERE calendarid='".sqlescape($cal['id'])."'" );
	  $result = DBQuery($database, "DELETE FROM vtcal_searchlog WHERE calendarid='".sqlescape($cal['id'])."'" );
	  $result = DBQuery($database, "DELETE FROM vtcal_searchkeyword WHERE calendarid='".sqlescape($cal['id'])."'" );
	  $result = DBQuery($database, "DELETE FROM vtcal_searchfeatured WHERE calendarid='".sqlescape($cal['id'])."'" );
	  $result = DBQuery($database, "DELETE FROM vtcal_category WHERE calendarid='".sqlescape($cal['id'])."'" );
	  $result = DBQuery($database, "DELETE FROM vtcal_template WHERE calendarid='".sqlescape($cal['id'])."'" );
	  $result = DBQuery($database, "DELETE FROM vtcal_sponsor WHERE calendarid='".sqlescape($cal['id'])."'" );
	  $result = DBQuery($database, "DELETE FROM vtcal_calendar WHERE id='".sqlescape($cal['id'])."'" );
    redirect2URL("managecalendars.php");
    exit;
  }

  pageheader(lang('delete_calendar'),
             lang('delete_calendar'),
             "Update","",$database);
  echo "<BR>";
  box_begin("inputbox",lang('delete_calendar'));
?>
<font color="#ff0000"><b><?php echo lang('warning_calendar_delete'); ?> &quot;<b><?php echo $c['name']; ?></b>&quot;</b></font>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php
  if ( isset ($cal['id']) ) { echo '<input type="hidden" name="cal[id]" value="'.$cal['id'].'">'; }
?>	
	<BR>
  <BR>
  <INPUT type="submit" name="save" value="<?php echo lang('ok_button_text'); ?>">
  <INPUT type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
</form>
<?php
  box_end();
  echo "<BR>";
  require("footer.inc.php");
?>