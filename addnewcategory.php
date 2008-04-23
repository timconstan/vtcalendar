<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_ADMIN"]) { exit; } // additional security

  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['save'])) { setVar($save,$_POST['save'],'save'); } else { unset($save); }
  if (isset($_POST['check'])) { setVar($check,$_POST['check'],'check'); } else { unset($check); }
  if (isset($_POST['category'])) { 
    if (isset($_POST['category']['name'])) { setVar($category['name'],$_POST['category']['name'],'category_name'); } 
	else { unset($category['name']); }
  }

  if (isset($cancel)) {
    redirect2URL("manageeventcategories.php");
    exit;
  }

  // check if name already exists
  $namealreadyexists = false;
  if (!empty($category['name'])) {
    $result = DBQuery($database, "SELECT * FROM vtcal_category WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND name='".sqlescape($category['name'])."'" );
    if ( $result->numRows() > 0 ) { $namealreadyexists = true; }
  }

  if (isset($save) && !$namealreadyexists && !empty($category['name']) ) {
    $result = DBQuery($database, "INSERT INTO vtcal_category (calendarid,name) VALUES ('".sqlescape($_SESSION["CALENDARID"])."','".sqlescape($category['name'])."')" );
    redirect2URL("manageeventcategories.php");
    exit;
  }

  pageheader(lang('add_new_event_category'),
             lang('add_new_event_category'),
             "Update","",$database);
  echo "<BR>";
  box_begin("inputbox",lang('add_new_event_category'));
?>
<br>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php
  if ( isset($check) ) {
		if ( empty($category['name']) ) {
			feedback(lang('category_name_cannot_be_empty'),1);
			echo "<br>";
		} // end: if ( $namealreadyexists )
		elseif ( $namealreadyexists ) {
			feedback(lang('category_name_already_exists'),1);
			echo "<br>";
		} // end: if ( $namealreadyexists )
  }
?>
  <b><?php echo lang('category_name'); ?>:&nbsp;</b>
	<input type="text" name="category[name]" maxlength="<?php echo constCategory_nameMaxLength; ?>" size="25" value="<?php 
	if (!empty($category['name'])) {
		echo HTMLSpecialChars($category['name']); 
	}
	?>">
	<input type="hidden" name="check" value="1">
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