<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['save'])) { setVar($save,$_POST['save'],'save'); } else { unset($save); }

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_ADMIN"]) { exit; } // additional security

  if (isset($cancel)) {
    redirect2URL("viewsearchlog.php");
    exit;
  }

  if (isset($save) ) {
	  $result = DBQuery($database, "DELETE FROM vtcal_searchlog WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."'" );
    redirect2URL("viewsearchlog.php");
    exit;
  }

  pageheader(lang('clear_search_log'),
             lang('clear_search_log'),
             "Update","",$database);
  echo "<BR>";
  box_begin("inputbox",lang('clear_search_log'));
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <?php echo lang('clear_search_log_confirm'); ?><br>
	<BR>
  <INPUT type="submit" name="save" value="<?php echo lang('ok_button_text'); ?>">
  <INPUT type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
</form>
<?php
  box_end();
  echo "<BR>";
  require("footer.inc.php");
?>