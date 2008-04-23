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
  if (isset($_POST['id'])) { setVar($id,$_POST['id'],'sponsorid'); } 
	else { 
	  if (isset($_GET['id'])) { setVar($id,$_GET['id'],'sponsorid'); } 
		else { unset($id); }
	}
  if (isset($_POST['sponsor'])) { 
    if (isset($_POST['sponsor']['name'])) { setVar($sponsor['name'],$_POST['sponsor']['name'],'sponsor_name'); } 
		else { unset($sponsor['name']); }
    if (isset($_POST['sponsor']['email'])) { setVar($sponsor['email'],$_POST['sponsor']['email'],'email'); } 
		else { unset($sponsor['email']); }
    if (isset($_POST['sponsor']['url'])) { setVar($sponsor['url'],$_POST['sponsor']['url'],'sponsor_url'); } 
		else { unset($sponsor['url']); }
    if (isset($_POST['sponsor']['admins'])) { setVar($sponsor['admins'],$_POST['sponsor']['admins'],'sponsor_admins'); } 
		else { unset($sponsor['admins']); }
  }

  if (isset($cancel)) {
    redirect2URL("managesponsors.php");
    exit;
  }

  function checksponsor(&$sponsor) {
    return (!empty($sponsor['name']) &&
       	    !empty($sponsor['email']) &&
						checkURL($sponsor['url']));
  }

	function emailsponsoraccountchanged(&$sponsor) {
		$subject = lang('email_account_updated_subject');
		$body = lang('email_account_updated_body');
		$body.= "   ".lang('sponsor_name')." ".stripslashes($sponsor['name'])."\n";
		$body.= "   ".lang('email')." ".stripslashes($sponsor['email'])."\n";
		$body.= "   ".lang('homepage')." ".stripslashes($sponsor['url'])."\n\n";

    	if ( isset($_SERVER["HTTPS"]) ) { $body .= "https"; } else { $body .= "http"; } 
        $body .= "://".$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'], "/"))."/update.php?calendarid=".$_SESSION["CALENDARID"]."\n\n";

		$body.= lang('email_add_event_instructions');
		sendemail2sponsor($sponsor['name'],$sponsor['email'],$subject,$body);
	} // end: emailsponsoraccountchanged

  $sponsorexists = false;
  $addPIDError="";
  if (isset($save) && checksponsor($sponsor) ) {
    $result = DBQuery($database, "SELECT * FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND name='".sqlescape($sponsor['name'])."'" );
		if ( $result->numRows()>0 ) {
      if ($result->numRows()>1) {
			  $sponsorexists = true;
			}
			else { // exactly one result
				if ( isset ($id) ) {
  				$s = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
	  			if ( $s['id'] != $id ) {
            $sponsorexists = true;
			  	}
				}
				else {
				  $sponsorexists = true;
				}			
			}
		}

		if (!$sponsorexists) {
			// check validity of sponsor-admins
			if ( !empty($sponsor['admins']) ) {
				// disassemble the admins string and check all PIDs against the DB
				$pidsInvalid = "";
				$pidsTokens = split ( "[ ,;\n\t]", $sponsor['admins'] );
				$pidsAddedCount = 0;
				for ($i=0; $i<count($pidsTokens); $i++) {
					$pidName = $pidsTokens[$i];
					$pidName = trim($pidName);
					if ( !empty($pidName) ) {
						if ( isvaliduser ( $database, $pidName ) ) {
							$pidsAdded[$pidsAddedCount] = $pidName;
							$pidsAddedCount++;
						} 
						else {
							if ( !empty($pidsInvalid) ) { $pidsInvalid .= ","; }
							$pidsInvalid .= $pidName;
						}
					} 
				} // end: while
		
				// save the changes
		
				// feedback message(s)
				if ( !empty($pidsInvalid) ) {
					if ( strpos($pidsInvalid, "," ) > 0 ) { // more than one user-ID
						$addPIDError = lang('user_ids_invalid')." &quot;".$pidsInvalid."&quot;";
					}
					else {
						$addPIDError = lang('user_id_invalid')." &quot;".$pidsInvalid."&quot;";
					}
				}
			} // end: else: if ( empty($sponsor[admins]) )

  		if (empty($addPIDError)) {    
				if ( isset ($id) ) { // edit, not new
					$result = DBQuery($database, "UPDATE vtcal_sponsor SET name='".sqlescape($sponsor['name'])."',email='".sqlescape($sponsor['email'])."',url='".sqlescape($sponsor['url'])."' WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id = '".sqlescape($id)."'" );
	
					// substitute existing auth info with the new one
					$result = DBQuery($database, "DELETE FROM vtcal_auth WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND sponsorid='".sqlescape($id)."'" );
					for ($i=0; $i<count($pidsAdded); $i++) {
						$result = DBQuery($database, "INSERT INTO vtcal_auth (calendarid,userid,sponsorid) VALUES ('".sqlescape($_SESSION["CALENDARID"])."','".sqlescape($pidsAdded[$i])."','".sqlescape($id)."')" );
					}
				}
				else {
					$query = "INSERT INTO vtcal_sponsor (calendarid,name,email,url) VALUES ('".sqlescape($_SESSION["CALENDARID"])."','".sqlescape($sponsor['name'])."','".sqlescape($sponsor['email'])."','".sqlescape($sponsor['url'])."')";
					$result = DBQuery($database, $query ); 
	
					// determine the automatically generated sponsor-id
					$result = DBQuery($database, "SELECT id FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND name='".sqlescape($sponsor['name'])."' AND email='".sqlescape($sponsor['email'])."' AND url='".sqlescape($sponsor['url'])."'" ); 
					$s = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
					$id = $s['id'];
					
					// substitute existing auth info with the new one
					$result = DBQuery($database, "DELETE FROM vtcal_auth WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND sponsorid='".sqlescape($id)."'" );
					for ($i=0; $i<count($pidsAdded); $i++) {
						$result = DBQuery($database, "INSERT INTO vtcal_auth (calendarid,userid,sponsorid) VALUES ('".sqlescape($_SESSION["CALENDARID"])."','".sqlescape($pidsAdded[$i])."','".sqlescape($id)."')" );
					}
				}
									
				emailsponsoraccountchanged($sponsor);
				redirect2URL("managesponsors.php");
				exit;
			} // end: if (empty($addPIDError))
    } // end: if (!$sponsorexists) 
	}

  if ( isset($id) ) {
    pageheader(lang('edit_sponsor'),
               lang('edit_sponsor'),
               "Update","",$database);
    echo "<BR>";
    box_begin("inputbox",lang('edit_sponsor'));
		if ( !isset($check) ) {
  		$result = DBQuery($database, "SELECT * FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($id)."'" );
      $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
		}
	}
	else {
    pageheader(lang('add_new_sponsor'),
               lang('add_new_sponsor'),
               "Update","",$database);
    echo "<BR>";
    box_begin("inputbox",lang('add_new_sponsor'));
	}
?>
<br>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<TABLE border="0" cellpadding="2" cellspacing="0">
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('sponsor_name'); ?></strong>
      <FONT color="#FF0000">*</FONT>
    </TD>
    <TD class="bodytext" valign="top">
<?php
		if ( isset($check) ) {
			if (empty($sponsor['name'])) {
				feedback(lang('choose_sponsor_name'),1);
			}
			elseif ($sponsorexists) {
				feedback(lang('sponsor_already_exists'),1);
			}
		}
?>
      <INPUT type="text" size="50" name="sponsor[name]" maxlength=<?php echo constSponsor_nameMaxLength; ?>  value="<?php
    if ( isset($check) ) { $sponsor['name']=stripslashes($sponsor['name']); }
    if ( isset($sponsor['name']) ) { echo HTMLSpecialChars($sponsor['name']); }
?>"> <I><?php echo lang('sponsor_name_example'); ?></I><BR>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('email'); ?></strong>
      <FONT color="#FF0000">*</FONT>
    </TD>
    <TD class="bodytext" valign="top">
<?php
  if (isset($check) && (empty($sponsor['email']))) {
    feedback(lang('choose_email'),1);
  }
?>
      <INPUT type="text" size="20" name="sponsor[email]" maxlength=<?php echo constEmailMaxLength; ?> value="<?php
  if ( isset($check) ) { $sponsor['email']=stripslashes($sponsor['email']); }
  if ( isset($sponsor['email'])) { echo HTMLSpecialChars($sponsor['email']); }
?>">
      <I><?php echo lang('email_example'); ?></I><BR>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('homepage'); ?></strong>
    </TD>
    <TD class="bodytext" valign="top">
<?php
  if ( isset($check) && !checkURL($sponsor['url']) ) {
    feedback(lang('url_invalid'),1);
  }
?>
      <INPUT type="text" size="50" name="sponsor[url]" maxlength=<?php echo constUrlMaxLength; ?> value="<?php
  if ( isset($check) ) { $sponsor['url']=stripslashes($sponsor['url']); }
  if ( isset($sponsor['url']) ) { echo HTMLSpecialChars($sponsor['url']); }
?>">
      <I><?php echo lang('url_example'); ?></I><BR>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('administrative_members'); ?></strong>
    </TD>
    <TD class="bodytext" valign="top">
<?php
  if (!empty($addPIDError)) {    
    feedback($addPIDError,1);
  }
?>
		<textarea name="sponsor[admins]" cols="40" rows="3" wrap="virtual"><?php
		if ( isset($sponsor['admins']) ) {
		  echo $sponsor['admins'];
		}
		elseif ( isset($id) ) {
		  $query = "SELECT * FROM vtcal_auth WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND sponsorid='".sqlescape($id)."' ORDER BY userid";
      $result = DBQuery($database, $query ); 
			$i = 0;
			while ($i < $result->numRows()) {
			  $authorization = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
				if ($i>0) { echo ","; }
				echo $authorization['userid'];
				$i++;
			}
		}
		?></textarea><br>
		<i><?php echo lang('administrative_members_example'); ?></i>
    </TD>
  </TR>
</TABLE>
	<input type="hidden" name="check" value="1">
<?php
  if ( isset ($id) ) { echo '<input type="hidden" name="id" value="',$id,'">'; }
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
