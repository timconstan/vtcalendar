<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }

  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['save'])) { setVar($save,$_POST['save'],'save'); } else { unset($save); }
  if (isset($_POST['user_oldpassword'])) { setVar($user_oldpassword,$_POST['user_oldpassword'],'password'); } else { unset($user_oldpassword); }
  if (isset($_POST['user_newpassword1'])) { setVar($user_newpassword1,$_POST['user_newpassword1'],'password'); } else { unset($user_newpassword1); }
  if (isset($_POST['user_newpassword2'])) { setVar($user_newpassword2,$_POST['user_newpassword2'],'password'); } else { unset($user_newpassword2); }

  if (isset($cancel)) {
    redirect2URL("update.php");
    exit;
  }

  if (isset($save)) {
    $user['oldpassword']=$user_oldpassword;
    $user['newpassword1']=$user_newpassword1;
    $user['newpassword2']=$user_newpassword2;

    $oldpw_error = checkoldpassword($user,$_SESSION["AUTH_USERID"],$database);
    $newpw_error = checknewpassword($user,$database);
    if ($oldpw_error==0) {
      if ($newpw_error==0) { // new password is valid
        // save password to DB
        $result = DBQuery($database, "UPDATE vtcal_user SET password='".sqlescape(crypt($user['newpassword1']))."' WHERE id='".sqlescape($_SESSION["AUTH_USERID"])."'" ); 

        // reroute to sponsormenu page
        redirect2URL("update.php?fbid=passwordchangesuccess");
        exit;
      }
    }
  }

  pageheader(lang('change_password'),
             lang('change_password'),
	           "Update","",$database);

  echo "<br />";
  box_begin("inputbox",lang('change_password'));
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <table border="0" cellpadding="2" cellspacing="0">
    <TR>
      <TD class="bodytext" valign="top">
        <strong><?php echo lang('old_password'); ?></strong>
      </TD>
      <TD class="bodytext" valign="top">
<?php
    if (isset($save) && $oldpw_error) {
      feedback(lang('old_password_wrong'),1);
    }
?>
        <input type="password" name="user_oldpassword" maxlength="20" size="20" value="" />
        <em>&nbsp;<?php echo lang('case_sensitive'); ?></em>
      </TD>
    </TR>
    <TR>
      <TD class="bodytext" valign="top">
        <strong><?php echo lang('new_password'); ?></strong>
      </TD>
      <TD class="bodytext" valign="top">
<?php
  if (isset($save)) {
    if ($newpw_error == 1) {
      feedback(lang('two_passwords_dont_match'),1);
    }
    elseif ($newpw_error == 2) {
      feedback(lang('new_password_invalid'),1);
    } // end: if ($newpw_error == 2)
  } // end: if (isset($save))
?>
        <input type="password" name="user_newpassword1" maxlength="20" size="20" value="" />
        <em>&nbsp;<?php echo lang('case_sensitive'); ?></em>
      </TD>
    </TR>
    <TR>
      <TD class="bodytext" valign="top">
        <strong><?php echo lang('new_password'); ?></strong><br /><?php echo lang('password_repeated'); ?>
      </TD>
      <TD class="bodytext" valign="top">
        <input type="password" name="user_newpassword2" maxlength="20" size="20" value="" />
        <em>&nbsp;<?php echo lang('case_sensitive'); ?></em>
      </TD>
    </TR>
  </table>
  <br />
   <input type="submit" name="save" value="<?php echo lang('ok_button_text'); ?>" />
  <input type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>" />
</form>
<?php
  box_end();
  echo "<br />";
  require("footer.inc.php");
?>