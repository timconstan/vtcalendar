<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }
  if (!$_SESSION["AUTH_MAINADMIN"] ) { exit; } // additional security

  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['save'])) { setVar($save,$_POST['save'],'save'); } else { unset($save); }
  if (isset($_POST['check'])) { setVar($check,$_POST['check'],'check'); } else { unset($check); }
  if (isset($_POST['new'])) { setVar($new,$_POST['new'],'check'); } else { 
	  if (isset($_GET['new'])) { setVar($new,$_GET['new'],'check'); } else { unset($new); }
	}
  if (isset($_GET['cal']) && isset($_GET['cal']['id'])) { setVar($cal['id'],$_GET['cal']['id'],'calendarid'); } else { unset($cal); }
  if (isset($_POST['cal'])) { 
    if (isset($_POST['cal']['id'])) { setVar($cal['id'],$_POST['cal']['id'],'calendarid'); } else { unset($cal['id']); }
    if (isset($_POST['cal']['name'])) { setVar($cal['name'],$_POST['cal']['name'],'calendarname'); } else { unset($cal['name']); }
    if (isset($_POST['cal']['admins'])) { setVar($cal['admins'],$_POST['cal']['admins'],'users'); } else { unset($cal['admins']); }
    if (isset($_POST['cal']['forwardeventdefault'])) { setVar($cal['forwardeventdefault'],$_POST['cal']['forwardeventdefault'],'forwardeventdefault'); } else { unset($cal['forwardeventdefault']); }
	}

  if (isset($cancel)) {
    redirect2URL("managecalendars.php");
    exit;
  }

  function checkcalendar(&$cal) {
    return (!empty($cal['id']) && !empty($cal['name']));
  }

  $calendarexists = false;
  $addPIDError="";
  if (isset($save) && checkcalendar($cal) ) {
    $result = DBQuery($database, "SELECT * FROM vtcal_calendar WHERE id='".$cal['id']."'" );
		if ( $cal['forwardeventdefault']!="1" ) { $cal['forwardeventdefault'] = "0"; }
		if ( isset($new) ) {
			if ( $result->numRows()>0 ) {
				$calendarexists = true;
			}
			else {
				// create new calendar
			  			  $query = "INSERT INTO vtcal_calendar (id, name,                       title,             header, footer, bgcolor, maincolor,  todaycolor, pastcolor, futurecolor, textcolor, linkcolor, gridcolor, viewauthrequired, forwardeventdefault) VALUES 
                                    ('".sqlescape($cal['id'])."','".sqlescape($cal['name'])."', '".lang('calendar')."', '',    '', '#ffffff','#ff9900', '#ffcc66', '#eeeeee',  '#ffffff',   '#000000', '#3333cc', '#cccccc', 0, '".sqlescape($cal['forwardeventdefault'])."')";


        $result = DBQuery($database, $query );

				$query = "INSERT INTO vtcal_sponsor (calendarid,name,email,url,admin) VALUES ('".sqlescape($cal['id'])."','".lang('administration')."','','".sqlescape(BASEURL.$cal['id'])."/"."','1')";
				$result = DBQuery($database, $query ); 
				
				// create three categories to have a starting point
        $result = DBQuery($database, "INSERT INTO vtcal_category (calendarid,name) VALUES ('".sqlescape($cal['id'])."','".lang('category1')."')" );
        $result = DBQuery($database, "INSERT INTO vtcal_category (calendarid,name) VALUES ('".sqlescape($cal['id'])."','".lang('category2')."')" );
        $result = DBQuery($database, "INSERT INTO vtcal_category (calendarid,name) VALUES ('".sqlescape($cal['id'])."','".lang('category3')."')" );
			}
		} // end: if ( isset($new) )
		else { 
      // update existing calendar
		  $query = "UPDATE vtcal_calendar SET name='".sqlescape($cal['name'])."',forwardeventdefault='".sqlescape($cal['forwardeventdefault'])."' WHERE id='".sqlescape($cal['id'])."'";
      $result = DBQuery($database, $query );
		} // end: else: if ( isset($new) )
		
		if (!$calendarexists) {
			// check validity of cal-admins
			if (!empty($cal['admins']) ) {
				// disassemble the admins string and check all PIDs against the DB
				$pidsInvalid = "";
				$pidsTokens = split ( "[ ,;\n\t]", $cal['admins'] );
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
				} // end: for ($i=0; $i<count($pidsTokens); $i++)
		
				// feedback message(s)
				if ( !empty($pidsInvalid) ) {
					if ( strpos($pidsInvalid, "," ) > 0 ) { // more than one user-ID
						$addPIDError = lang('user_ids_invalid')." &quot;".$pidsInvalid."&quot;";
					}
					else {
						$addPIDError = lang('user_id_invalid')." &quot;".$pidsInvalid."&quot;";
					}
				}
			} // end: else: if ( empty($cal[admins]) )

  		if (empty($addPIDError)) {    
				// determine the id of sponsor "Administration"
				$result = DBQuery($database, "SELECT id FROM vtcal_sponsor WHERE calendarid='".sqlescape($cal['id'])."' AND admin='1'" );
				$s = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
				$administrationId = $s['id'];
				
				// substitute existing auth info with the new one
				$result = DBQuery($database, "DELETE FROM vtcal_auth WHERE calendarid='".sqlescape($cal['id'])."' AND sponsorid='".sqlescape($administrationId)."'" );
				for ($i=0; $i<count($pidsAdded); $i++) {
					$result = DBQuery($database, "INSERT INTO vtcal_auth (calendarid,userid,sponsorid) VALUES ('".$cal['id']."','".$pidsAdded[$i]."','".$administrationId."')" );
				}
									
				redirect2URL("managecalendars.php");
				exit;
			} // end: if (empty($addPIDError))
    } // end: if (!$calendarexists) 
	} // end: if (isset($save) && checkcalendar($cal) )

  if ( isset($cal['id']) ) {
    pageheader(lang('edit_calendar'),
               lang('edit_calendar'),
               "Update","",$database);
    echo "<BR>";
    box_begin("inputbox",lang('edit_calendar'));
		if ( !isset($check) ) {
  		$result = DBQuery($database, "SELECT * FROM vtcal_calendar WHERE id='".sqlescape($cal['id'])."'" );
      $cal = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
		}
	}
	else {
    pageheader(lang('add_new_calendar'),
               lang('add_new_calendar'),
               "Update","",$database);
    echo "<BR>";
    box_begin("inputbox",lang('add_new_calendar'));
	}
?>
<br>
<form method="post" action="editcalendar.php">
<TABLE border="0" cellpadding="2" cellspacing="0">
  <TR>
    <TD class="bodytext" valign="top">
      <?php echo lang('calendar_id'); ?>:
      <FONT color="#FF0000">*</FONT>
    </TD>
    <TD class="bodytext" valign="top">
<?php
	if ( isset($check) ) {
		if (empty($cal['id']) || !isValidInput($cal['id'],'calendarid')) {
			feedback(lang('choose_valid_calendar_id')." ".constCalendaridVALIDMESSAGE,1);
		}
		elseif ($calendarexists) {
			feedback(lang('calendar_already_exists'),1);
		}
	}
?>
<?php
  if ( isset ($new) ) { 
?>
  <INPUT type="text" size="20" name="cal[id]" maxlength=<?php echo constCalendaridMAXLENGTH; ?> value="<?php
  if ( isset($check) ) { $cal['id']=stripslashes($cal['id']); }
  if ( isset($cal['id']) ) { echo HTMLSpecialChars($cal['id']); }
?>"> <I><?php echo lang('calendar_id_example'); ?></I>
<?php
  } // end: else: if ( isset ($cal['id']) )
	else {
	  echo '<input type="hidden" name="cal[id]" value="',$cal['id'],'">';
		echo "<b>".$cal['id']."</b>\n"; 
	}
?>
<BR>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <?php echo lang('calendar_name'); ?>:
      <FONT color="#FF0000">*</FONT>
    </TD>
    <TD class="bodytext" valign="top">
<?php
	if ( isset($check) ) {
		if (empty($cal['name']) || !isValidInput($cal['name'],'calendarname')) {
			feedback(lang('choose_valid_calendar_name')." ".constCalendarnameVALIDMESSAGE,1);
		}
	}
?>
      <INPUT type="text" size="50" name="cal[name]" maxlength=<?php echo constCalendarnameMAXLENGTH; ?>  value="<?php
  if ( isset($check) ) { $cal['name']=stripslashes($cal['name']); }
  if ( isset($cal['name']) ) { echo HTMLSpecialChars($cal['name']); }
?>"> <I><?php echo lang('calendar_name_example'); ?></I><BR>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <?php echo lang('administrators'); ?><br>
    </TD>
    <TD class="bodytext" valign="top">
<?php
  if (!empty($addPIDError)) {    
    feedback($addPIDError,1);
  }
?>
		<textarea name="cal[admins]" cols="40" rows="3" wrap="virtual"><?php
		if ( isset($cal['admins']) ) {
		  echo $cal['admins'];
		}
		elseif ( isset($cal['id']) ) {
			// determine the automatically generated sponsor-id
			$result = DBQuery($database, "SELECT id FROM vtcal_sponsor WHERE calendarid='".sqlescape($cal['id'])."' AND admin='1'" );
			$s = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
			$administrationId = $s['id'];


		  $query = "SELECT * FROM vtcal_auth WHERE calendarid='".sqlescape($cal['id'])."' AND sponsorid='".sqlescape($administrationId)."' ORDER BY userid";
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
		<i><?php echo lang('administrators_example'); ?></i>
    </TD>
  </TR>
<?php
  if ( !isset($cal['id']) || $cal['id'] != "default" ) {
?>
  <TR>
    <TD class="bodytext" valign="top">&nbsp;</TD>
    <TD class="bodytext" valign="top">
<?php
  $result = DBQuery($database, "SELECT * FROM vtcal_calendar WHERE id='default'" ); 
  $c = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
  $defaultcalendarname = $c['name'];
?>
      <br>
      <table border="0">
        <tr align="left" valign="top">
          <td><input type="checkbox" name="cal[forwardeventdefault]" id="forwardeventdefault" value="1"<?php 
					if (isset($cal['forwardeventdefault']) && $cal['forwardeventdefault']=="1") { echo " checked"; } 
					?>></td>
          <td>
             <label for="forwardeventdefault"><?php echo lang('also_display_on_calendar_message'); ?> <?php echo $defaultcalendarname ?></label><br>
      <?php echo lang('also_display_on_calendar_notice'); ?></td>
        </tr>
      </table>
    </TD>
  </TR>
<?php
  } // end: if ( $cal['id'] != "default" ) {
?>	
	<tr>
	  <td>&nbsp;</td>
		<td>
		<input type="hidden" name="check" value="1">
<?php
  if ( isset($new) ) {
		echo '<input type="hidden" name="new" value="1">';
  }
?>		
    <BR>
    <INPUT type="submit" name="save" value="<?php echo lang('ok_button_text'); ?>">
    <INPUT type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
  	</td>
	</tr>
</TABLE>
</form>
<?php
  box_end();
  echo "<BR>";
  require("footer.inc.php");
?>