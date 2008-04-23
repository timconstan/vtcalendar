<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['save'])) { setVar($save,$_POST['save'],'save'); } else { unset($save); }
  if (isset($_POST['check'])) { setVar($check,$_POST['check'],'check'); } else { unset($check); }
  if (isset($_POST['categoryid'])) { setVar($categoryid,$_POST['categoryid'],'categoryid'); } 
	else { 
    if (isset($_GET['categoryid'])) { setVar($categoryid,$_GET['categoryid'],'categoryid'); } 
		else { unset($categoryid); }
  }		
  if (isset($_POST['category'])) { 
    if (isset($_POST['category']['name'])) { setVar($category['name'],$_POST['category']['name'],'category_name'); } else { unset($category['name']); }
	}

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_ADMIN"]) { exit; } // additional security

  if (isset($cancel)) {
    redirect2URL("manageeventcategories.php");
    exit;
  }

  // check if name already exists
	$namealreadyexists = false;
	if (!empty($category['name'])) {
    $result = DBQuery($database, "SELECT * FROM vtcal_category WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND name='".sqlescape($category['name'])."' AND id!='".sqlescape($categoryid)."'" );
    if ( $result->numRows() > 0 ) { $namealreadyexists = true; }
	}

  if (isset($save) && !$namealreadyexists && !empty($category['name']) ) {
    $result = DBQuery($database, "UPDATE vtcal_category SET name='".sqlescape($category['name'])."' WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($categoryid)."'" );
    redirect2URL("manageeventcategories.php");
    exit;
  }
  else { // read category from DB
		if ( !isset($check) ) {
			$result = DBQuery($database, "SELECT * FROM vtcal_category WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($categoryid)."'" );
			if ( $result->numRows() != 1 ) {
				redirect2URL("manageeventcategories.php");
				exit;
			}
			else {
				$category = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
			}
    }
  } // end else: if (isset($save))

  pageheader(lang('rename_event_category'),
             lang('rename_event_category'),
             "Update","",$database);
  echo "<BR>";
  box_begin("inputbox",lang('rename_event_category'));
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
	<input type="text" name="category[name]" maxlength="<?php echo constCategory_nameMaxLength; ?>" size="25" value="<?php echo HTMLSpecialChars($category['name']); ?>">
  <input type="hidden" name="categoryid" value="<?php echo $categoryid; ?>">
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
