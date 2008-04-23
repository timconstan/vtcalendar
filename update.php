<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

//  if (isset($_POST['userid'])) { setVar($userid,$_POST['userid'],'userid'); } else { unset($userid); }
//  if (isset($_POST['password'])) { setVar($password,$_POST['password'],'password'); } else { unset($password); }
  if (isset($_GET['authsponsorid'])) { setVar($authsponsorid,$_GET['authsponsorid'],'sponsorid'); } else { unset($authsponsorid); }

  // the next if statement is just to avoid that it redirects when using in testing mode 
	if ( $_SERVER['HTTP_HOST'] != "localhost" ) {
		$protocol = "http";
		if ( isset($_SERVER['HTTPS'])) { $protocol .= "s"; }
		if ( BASEURL != SECUREBASEURL && $protocol."://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"] != SECUREBASEURL."update.php" ) {
			redirect2URL(SECUREBASEURL."update.php?calendar=".$_SESSION["CALENDARID"]);
		}
  }

  $database = DBopen();
  if (!authorized($database)) { exit; }

	// read sponsor name from DB
	$result = DBQuery($database, "SELECT name FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 
	$sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);

	pageheader(lang('update_calendar'),
						 lang('update_calendar'),
						 "Update","",$database);

	echo "<BR>";
  echo '<table cellspacing="5" cellpadding="0" border="0"><tr><td valign="top">'."\n";
  echo "<fieldset>\n";
  echo "<legend><b>",lang('sponsors_options'),"&nbsp;</b></legend><br>\n";

  if ( isset($fbid) ) {
		if ($fbid=="eaddsuccess" && !$_SESSION["AUTH_ADMIN"]) {
			feedback(lang('new_event_submitted_notice')." ".stripslashes(urldecode("\"$fbparam\"")),0);
			echo "<BR>";
		}
		elseif ($fbid=="eupdatesuccess" && !$_SESSION["AUTH_ADMIN"] ) {
			feedback(lang('updated_event_submitted_notice')." ".stripslashes(urldecode("\"$fbparam\"")),0);
			echo "<BR>";
		}
		elseif ($fbid=="urlchangesuccess") {
			feedback(lang('hompage_changed_notice')." ".stripslashes(urldecode("\"$fbparam\"")),0);
			echo "<BR>";
		}
		elseif ($fbid=="emailchangesuccess") {
			feedback(lang('email_changed_notice')." ".stripslashes(urldecode("\"$fbparam\"")),0);
			echo "<BR>";
		}
  } // end: if ( isset($fbid) )
?>
<TABLE width="100%" border="0" cellspacing="1" cellpadding="2">
<TR>
  <TD class="inputbox">
    <A href="addevent.php"><?php echo lang('add_new_event'); ?></A>
  </TD class="inputbox">
  <TD class="inputbox">
    &nbsp;&nbsp;&nbsp;<A target="newWindow" onclick="new_window(this.href); return false" href="helpaddevent.php"><IMG src="images/nuvola/16x16/actions/help.png" width="16" height="16" alt="" border="0"></A>
  </TD>
</TR>
<TR>
  <TD class="inputbox">
    <A href="manageevents.php"><?php echo lang('manage_events'); ?></A>
  </TD>
  <TD class="inputbox">
    &nbsp;&nbsp;&nbsp;<A target="newWindow" onclick="new_window(this.href); return false" href="helpupdatecopydelete.php"><IMG src="images/nuvola/16x16/actions/help.png" width="16" height="16" alt="" border="0"></A>
  </TD>
</TR>
<TR>
  <TD><br></TD>
  <TD><br></TD>
</TR>
<TR>
  <TD class="inputbox">
    <A href="managetemplates.php"><?php echo lang('manage_templates'); ?></A>
  </TD>
  <TD class="inputbox">
    &nbsp;&nbsp;&nbsp;<A target="newWindow" onclick="new_window(this.href); return false" href="helptemplate.php"><IMG src="images/nuvola/16x16/actions/help.png" width="16" height="16" alt="" border="0"></A>
  </TD>
</TR>
<TR>
  <TD><br></TD>
  <TD><br></TD>
</TR>
<TR>
  <TD class="inputbox">
    <A href="export.php"><?php echo lang('export_events'); ?></A>
  </TD>
  <TD class="inputbox">
    &nbsp;&nbsp;&nbsp;<a target="newWindow" onclick="new_window(this.href); return false" href="helpexport.php"><img src="images/nuvola/16x16/actions/help.png" width="16" height="16" alt="" border="0"></A>
  </TD>
</TR>
<TR>
  <TD class="inputbox">
    <A href="import.php"><?php echo lang('import_events'); ?></A>
  </TD>
  <TD class="inputbox">
    &nbsp;&nbsp;&nbsp;<A target="newWindow" onclick="new_window(this.href); return false" href="helpimport.php"><IMG src="images/nuvola/16x16/actions/help.png" width="16" height="16" alt="" border="0"></A>
  </TD>
</TR>
<TR>
  <TD><br></TD>
  <TD><br></TD>
</TR>
<TR>
  <TD class="inputbox">
    <A href="changehomepage.php"><?php echo lang('change_homepage'); ?></A>
  </TD>
</TR>
<TR>
  <TD class="inputbox">
    <A href="changeemail.php"><?php echo lang('change_email'); ?></A>
  </TD>
</TR>
<TR>
  <TD class="inputbox">
<?php
	if ( AUTH_DB && strlen($_SESSION["AUTH_USERID"]) > strlen(AUTH_DB_USER_PREFIX) && substr($_SESSION["AUTH_USERID"],0,strlen(AUTH_DB_USER_PREFIX)) == AUTH_DB_USER_PREFIX ) {
?>
    <A href="changeuserpassword.php"><?php echo lang('change_password_of_user'); ?> &quot;<?php echo $_SESSION["AUTH_USERID"]; ?>&quot;</A>
<?php
  } // end: if ( AUTH_DB ... )
?>&nbsp;
  </TD>
  <TD></TD>
</TR>
</TABLE>
<?php
  echo "</fieldset>\n";
?>
</td>
<?php
  if ($_SESSION["AUTH_ADMIN"]) {
		echo "<td valign=\"top\">\n";
	  echo "<fieldset>\n";
	  echo "<legend><b>",lang('administrators_options'),"&nbsp;</b></legend><br>\n";
?>
<TABLE width="100%" border="0" cellspacing="1" cellpadding="2">
<TR>
  <TD class="inputbox">
    <A href="approval.php"><?php echo lang('approve_reject_event_updates'); ?></A>
  </TD>
  <TD>&nbsp;</TD>
</TR>
<TR>
  <TD><br></TD>
  <TD><br></TD>
</TR>
<TR>
  <TD class="inputbox">
    <A href="managesponsors.php"><?php echo lang('manage_sponsors'); ?></A>
  </TD>
  <TD>&nbsp;</TD>
</TR>
<TR>
  <TD class="inputbox">
    <A href="deleteinactivesponsors.php"><?php echo lang('delete_inactive_sponsors'); ?></A>
  </TD>
  <TD>&nbsp;</TD>
</TR>
<TR>
  <TD colspan="2"><br></TD>
</TR>
<TR>
  <TD class="inputbox">
    <a href="changecalendarsettings.php"><?php echo lang('change_header_footer_colors_auth'); ?></a>
  </TD>
  <TD></TD>
</TR>
<TR>
  <TD class="inputbox">
    <a href="manageeventcategories.php"><?php echo lang('manage_event_categories'); ?></a>
  </TD>
  <TD></TD>
</TR>
<TR>
  <TD class="inputbox">
    <a href="managesearchkeywords.php"><?php echo lang('manage_search_keywords'); ?></a>
  </TD>
  <TD></TD>
</TR>
<TR>
  <TD class="inputbox">
    <a href="managefeaturedsearchkeywords.php"><?php echo lang('manage_featured_search_keywords'); ?></a>
  </TD>
  <TD></TD>
</TR>
<TR>
  <TD class="inputbox">
    <a href="viewsearchlog.php"><?php echo lang('view_search_log'); ?></a><br><br><br>
  </TD>
  <TD></TD>
</TR>
</table>
<?php
    	  echo "</fieldset>\n";
		echo "</td>\n";
  } // end: if ($_SESSION["AUTH_ADMIN"])
?>
<?php
  if ( $_SESSION["AUTH_MAINADMIN"] ) {
		echo "<td valign=\"top\">\n";
		  echo "<fieldset>\n";
		  echo "<legend><b>",lang('main_administrators_options'),"&nbsp;</b></legend><br>\n";
?>
<TABLE width="100%" border="0" cellspacing="1" cellpadding="3">
<?php
	if ( AUTH_DB ) {
?>
<TR>
  <TD class="inputbox">
    <A href="manageusers.php"><?php echo lang('manage_users'); ?></A> <?php echo AUTH_DB_NOTICE; ?>
  </TD>
  <TD>&nbsp;</TD>
</TR>
<?php
  } // end: if ( AUTH_DB )
?>
<TR valign="top">
  <TD class="inputbox">
    <a href="managecalendars.php"><?php echo lang('manage_calendars'); ?></a>
  </TD>
  <TD>&nbsp;</TD>
</TR>
<TR valign="top">
  <TD class="inputbox">
    <a href="managemainadmins.php"><?php echo lang('manage_main_admins'); ?></a>
  </TD>
  <TD>
	  &nbsp;<br><br><br><br><br><br><br><br><br><br><br><br>
	</TD>
</TR>
</table>
<?php
 	 echo "</fieldset>\n";
		echo "</td>\n";
  } // end: if ( $_SESSION["AUTH_MAINADMIN"] )
?>
</tr>
</table>
<BR>
<?php
  require("footer.inc.php");
?>