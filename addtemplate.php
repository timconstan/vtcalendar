<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');
  require("inputtdata.inc.php");

  $database = DBopen();
  if (!authorized($database)) { exit; }

  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['check'])) { setVar($check,$_POST['check'],'check'); } else { unset($check); }
  if (isset($_POST['template_name'])) { setVar($template_name,$_POST['template_name'],'template_name'); } else { unset($template_name); }
  if (isset($_POST['savetemplate'])) { setVar($savetemplate,$_POST['savetemplate'],'savetemplate'); } else { unset($savetemplate); }
  if (isset($_POST['event'])) {
		if (isset($_POST['event']['categoryid'])) { setVar($event['categoryid'],$_POST['event']['categoryid'],'categoryid'); } else { unset($event['categoryid']); }
		if (isset($_POST['event']['title'])) { setVar($event['title'],$_POST['event']['title'],'title'); } else { unset($event['title']); }
		if (isset($_POST['event']['location'])) { setVar($event['location'],$_POST['event']['location'],'location'); } else { unset($event['location']); }
		if (isset($_POST['event']['price'])) { setVar($event['price'],$_POST['event']['price'],'price'); } else { unset($event['price']); }
		if (isset($_POST['event']['description'])) { setVar($event['description'],$_POST['event']['description'],'description'); } else { unset($event['description']); }
		if (isset($_POST['event']['url'])) { setVar($event['url'],$_POST['event']['url'],'url'); } else { unset($event['url']); }
		if (isset($_POST['event']['displayedsponsor'])) { setVar($event['displayedsponsor'],$_POST['event']['displayedsponsor'],'displayedsponsor'); } else { unset($event['displayedsponsor']); }
		if (isset($_POST['event']['displayedsponsorurl'])) { setVar($event['displayedsponsorurl'],$_POST['event']['displayedsponsorurl'],'url'); } else { unset($event['displayedsponsorurl']); }
		if (isset($_POST['event']['showondefaultcal'])) { setVar($event['showondefaultcal'],$_POST['event']['showondefaultcal'],'showondefaultcal'); } else { unset($event['showondefaultcal']); }
		if (isset($_POST['event']['contact_name'])) { setVar($event['contact_name'],$_POST['event']['contact_name'],'contact_name'); } else { unset($event['contact_name']); }
		if (isset($_POST['event']['contact_phone'])) { setVar($event['contact_phone'],$_POST['event']['contact_phone'],'contact_phone'); } else { unset($event['contact_phone']); }
		if (isset($_POST['event']['contact_email'])) { setVar($event['contact_email'],$_POST['event']['contact_email'],'contact_email'); } else { unset($event['contact_email']); }
  } // end: if (isset($_POST['event'])) {

  if (isset($cancel)) {
    redirect2URL("managetemplates.php");
    exit;
  }

  // read sponsor name from DB
  $result = DBQuery($database, "SELECT name,url FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 
  $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC);

  $event['sponsorid']=$_SESSION["AUTH_SPONSORID"];
  if (isset($check)) { // check all the parameter passed for validity and save into DB
    if (!empty($template_name)) { // parameter is ok
      // save template into DB
      insertintotemplate($template_name,$event,$database);

      // reroute to sponsormenu page
      redirect2URL("managetemplates.php");
      exit;
    } // end: if (!empty($template_name))
  } // end: if (isset($check))
  else {
    $template_name = "";
    defaultevent($event,$_SESSION["AUTH_SPONSORID"],$database); // empty template
  } // end else: if (isset($check))

  pageheader(lang('add_new_template'),
             lang('add_new_template'),
             "Update","",$database);
  echo "<BR>";
  box_begin("inputbox",lang('add_new_template'));
?>
<BR>
<FORM method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php
  if (!isset($check)) { $check=0; }
  inputtemplatedata($event,$_SESSION["AUTH_SPONSORID"],$check,$template_name,$database);
?>
 <BR>
 <INPUT type="submit" name="savetemplate" value="<?php echo lang('ok_button_text'); ?>">
 <INPUT type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
</FORM>
<?php
  box_end();
  echo "<BR>";
  require("footer.inc.php");
?>