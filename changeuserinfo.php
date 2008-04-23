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
  if (isset($_POST['userid'])) { setVar($userid,$_POST['userid'],'userid'); } 
	else { 
	  if (isset($_GET['userid'])) { setVar($userid,$_GET['userid'],'userid'); } 
		else { unset($userid); }
	}
  if (isset($_POST['chooseuser'])) { setVar($chooseuser,$_POST['chooseuser'],'chooseuser'); } 
	else { 
    if (isset($_GET['chooseuser'])) { setVar($chooseuser,$_GET['chooseuser'],'chooseuser'); } 
		else { unset($chooseuser); }
	}
  if (isset($_POST['user'])) { 
    if (isset($_POST['user']['password'])) { setVar($user['password'],$_POST['user']['password'],'password'); } 
		else { unset($user['password']); }
    if (isset($_POST['user']['email'])) { setVar($user['email'],$_POST['user']['email'],'email'); } 
		else { unset($user['email']); }
		
  }

  // prepend the prefix when creating new users
  if (isset($userid) && !isset($chooseuser)) {
		if (substr($userid,0,strlen(AUTH_DB_USER_PREFIX))!=AUTH_DB_USER_PREFIX) { 
			$userid=AUTH_DB_USER_PREFIX.$userid; 
		}
	}

	function checkuser(&$user) {
    return (!empty($user['id']) && isValidInput($user['id'],'userid') &&
		        !empty($user['password']));
  }

	function emailuseraccountchanged(&$user) {
		$subject = lang('email_account_updated_subject');
		$body = lang('email_account_updated_body');
		$body.= "   ".lang('user_id').": ".stripslashes($user['id'])."\n";
		if ( $user['password'] != "#nochange$" ) {
		  $body.= "   ".lang('password')." ".stripslashes($user['password'])."\n";
		}
		$body.= "   ".lang('email')." ".stripslashes($user['email'])."\n";
		
		sendemail2user($user['email'],$subject,$body);
	} // end: emailuseraccountchanged

  
  if (isset($cancel)) {
    redirect2URL("manageusers.php");
    exit;
  };

  if (isset($userid)) {
  	$user['id'] = $userid;
	}

  if (isset($save) && checkuser($user) && ($chooseuser || !userExistsInDB($database, $user['id'])) ) { // save user into DB
    if (!empty($chooseuser)) { // update an existing user
      if ( $user['password'] == "#nochange$" ) { // update only the e-mail address
  			$result = DBQuery($database, "UPDATE vtcal_user SET email='".sqlescape($user['email'])."' WHERE id='".sqlescape($user['id'])."'" );
		  }	
			else { // update password and email address
	  		$result = DBQuery($database, "UPDATE vtcal_user SET password='".sqlescape(crypt($user['password']))."',email='".sqlescape($user['email'])."' WHERE id='".sqlescape($user['id'])."'" );
      }
			
      emailuseraccountchanged($user);

      redirect2URL("manageusers.php");
    }
    else { // insert as a new user
      $query = "INSERT INTO vtcal_user (id,password,email) VALUES ('".sqlescape($user['id'])."','".sqlescape(crypt($user['password']))."','".sqlescape($user['email'])."')";
      $result = DBQuery($database, $query ); 

      emailuseraccountchanged($user);

      // reroute to sponsormenu page
      redirect2URL("manageusers.php");
    }
    exit;
  } // end: if (isset($save))

  // print page header
  if (!empty($chooseuser)) {
    if (empty($userid)) { // no user was selected
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
    pageheader(lang('add_new_user'),
               lang('add_new_user'),
               "Update","",$database);
    echo "<BR>\n";
    box_begin("inputbox",lang('add_new_user'));
  }

  if (isset($user['id']) && (!isset($check) || $check != 1)) { // load user to update information if it's the first time the form is viewed
    $result = DBQuery($database, "SELECT * FROM vtcal_user WHERE id='".sqlescape($user['id'])."'" ); 
    $user = $result->fetchRow(DB_FETCHMODE_ASSOC);
  } // end if: "if (isset($userid))"
?>
<FORM method="post" action="changeuserinfo.php">
<?php
  // set the allowed length for the input fields
  $maxlength_id = 8;
?>
<TABLE border="0" cellpadding="2" cellspacing="0">
  <TR>
    <TD class="bodytext" valign="baseline">
      <b><?php echo lang('user_id'); ?>:<FONT color="#FF0000">*</FONT></b>
    </TD>
    <TD class="bodytext" valign="baseline">
<?php
  if (!empty($chooseuser)) {
	  echo "<b>".$userid."</b>";
	}
	else {
  	if (isset($check) && $check && (empty($userid))) {
      feedback(lang('choose_user_id'),1);
    }
    if (isset($check) && $check && userExistsInDB($database,$userid)) {
      feedback(lang('user_id_already_exists'),1);
    }
		
	 echo AUTH_DB_USER_PREFIX;	
?><INPUT type="text" size="10" name="userid" maxlength=<?php echo $maxlength_id; ?>  value="<?php
  if (isset($check) && $check) { $userid=stripslashes($userid); }
  if (isset($userid)) { echo HTMLSpecialChars(substr($userid,strlen(AUTH_DB_USER_PREFIX))); }
?>"> <I>(e.g. <?php echo AUTH_DB_USER_PREFIX; ?>jsmith)</I>
<?php
  } // end: else: if (!empty($userid))
?>
<BR>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="baseline">
      <b><?php echo lang('password'); ?><FONT color="#FF0000">*</FONT></b>
    </TD>
    <TD class="bodytext" valign="baseline">
<?php
  if (isset($check) && $check && (empty($user['password']))) {
    feedback(lang('choose_password'),1);
  }
?>
      <INPUT type="password" size="14" name="user[password]" maxlength=<?php echo constPasswordMaxLength; ?>  value="<?php if (!empty($chooseuser)) { echo "#nochange$"; } ?>"> <BR>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="baseline">
      <b><?php echo lang('email'); ?></b>
    </TD>
    <TD class="bodytext" valign="baseline">
      <INPUT type="text" size="20" name="user[email]" maxlength=<?php echo constEmailMaxLength; ?> value="<?php
  if (isset($user) && isset($user['email'])) {
	  if (isset($check) && $check) { 
		  $user['email']=stripslashes($user['email']); 
		}
  	echo HTMLSpecialChars($user['email']);
	}
?>">
      <I><?php echo lang('email_example'); ?></I><BR>
    </TD>
  </TR>
</TABLE>
<INPUT type="hidden" name="check" value="1">
<BR>
<?php
 if (!empty($chooseuser)) { 
   echo "<INPUT type=\"hidden\" name=\"chooseuser\" value=\"1\">\n"; 
   if (!empty($userid)) { echo "<INPUT type=\"hidden\" name=\"userid\" value=\"$userid\">\n"; }
 }
?>
<INPUT type="submit" name="save" value="<?php echo lang('ok_button_text'); ?>">
<INPUT type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
</FORM>
<?php
  box_end();
  echo "<br><br>";
  require("footer.inc.php");
?>