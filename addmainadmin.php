<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_MAINADMIN"]) { exit; } // additional security

  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['save'])) { setVar($save,$_POST['save'],'save'); } else { unset($save); }
  if (isset($_POST['check'])) { setVar($check,$_POST['check'],'check'); } else { unset($check); }
  if (isset($_POST['mainuserid'])) { setVar($mainuserid,$_POST['mainuserid'],'userid'); } else { unset($mainuserid); }

  function checkuser(&$user) {
    return (!empty($user['id']) && isValidInput($user['id'],'userid'));
  }

	function mainAdminExistsInDB($database, $mainuserid) {
		$query = "SELECT count(id) FROM vtcal_adminuser WHERE id='".sqlescape($mainuserid)."'";
		$result = DBQuery($database, $query ); 
		$r = $result->fetchRow(0);
		if ($r[0]>0) { return true; }
		
		return false; // default rule
	}	

  if (isset($cancel)) {
    redirect2URL("managemainadmins.php");
    exit;
  };

  if (!empty($mainuserid)) { $user['id'] = $mainuserid; } else { $user['id'] = ""; }
  if (isset($save) && checkuser($user) && !mainAdminExistsInDB($database, $user['id']) && isValidUser($database, $user['id']) ) { // save user into DB
		$query = "INSERT INTO vtcal_adminuser (id) VALUES ('".sqlescape($user['id'])."')";
		$result = DBQuery($database, $query ); 

		// reroute to sponsormenu page
		redirect2URL("managemainadmins.php");
    exit;
  } // end: if (isset($save))

  // print page header
  if (!empty($chooseuser)) {
    if (empty($mainuserid)) { // no user was selected
      // reroute to sponsormenu page
      redirect2URL("update.php?fbid=userupdatefailed");
      exit;
    }
    else {
      pageheader(lang('edit_user'),
                 lang('edit_user'),
	             "Update","",$database);
      echo "<BR>\n";
      box_begin("inputbox",lang('edit_user'));
		}
  }
  else {
    pageheader(lang('add_new_main_admin'),
               lang('add_new_main_admin'),
               "Update","",$database);
    echo "<BR>\n";
    box_begin("inputbox",lang('add_new_main_admin'));
  }
  if (isset($user['id']) && (!isset($check) || $check != 1)) { // load user to update information if it's the first time the form is viewed
    $result = DBQuery($database, "SELECT * FROM vtcal_user WHERE id='".sqlescape($user['id'])."'" ); 
    $user = $result->fetchRow(DB_FETCHMODE_ASSOC);
  } // end if: "if (isset($mainuserid))"
?>
<FORM method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="mainform">
<TABLE border="0" cellpadding="2" cellspacing="0">
  <TR>
    <TD class="bodytext" valign="baseline">
      <b><?php echo lang('user_id'); ?>:</b>
    </TD>
    <TD class="bodytext" valign="baseline">
<?php
  	if (isset($check) && $check && (empty($mainuserid))) {
      feedback(lang('choose_user_id'),1);
    }
    elseif (isset($check) && $check && mainAdminExistsInDB($database,$mainuserid)) {
      feedback(lang('already_main_admin'),1);
    }
    elseif (isset($check) && $check && !isValidUser($database, $mainuserid)) {
      feedback(lang('user_not_exists'),1);
    }
?><INPUT type="text" size="20" name="mainuserid" maxlength="50" value="<?php
  if (!empty($mainuserid)) {
		if ($check) { $mainuserid=stripslashes($mainuserid); }
  	echo $mainuserid;
	}
?>"> <I><?php echo lang('user_id_example'); ?></I>
<BR>
    </TD>
  </TR>
  <tr>
    <td>&nbsp;</td>
    <td>
      <INPUT type="submit" name="save" value="<?php echo lang('ok_button_text'); ?>">
      <INPUT type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
    </td>
	</tr>
</TABLE>
<INPUT type="hidden" name="check" value="1">
<script language="JavaScript" type="text/javascript"><!--
document.mainform.userid.focus();
//--></script>
</FORM>
<?php
  box_end();
  echo "<br><br>";
  require("footer.inc.php");
?>