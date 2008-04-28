<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }

  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['save'])) { setVar($save,$_POST['save'],'save'); } else { unset($save); }
  if (isset($_POST['sponsor_email'])) { setVar($sponsor_email,$_POST['sponsor_email'],'sponsor_email'); } else { unset($sponsor_email); }

  if (isset($cancel)) {
    redirect2URL("update.php");
    exit;
  }

  // read sponsor name from DB
  $result = DBQuery($database, "SELECT name FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 
  $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);

  if (isset($save)) {
    $sponsor["email"]=$sponsor_email;
    if (checkemail($sponsor["email"])) { // email is valid
      // save email to DB
      $result = DBQuery($database, "UPDATE vtcal_sponsor SET email='".sqlescape($sponsor_email)."' WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 

      // reroute to sponsormenu page
      redirect2URL("update.php?fbid=emailchangesuccess&fbparam=".urlencode(stripslashes($sponsor_email)));
      exit;
    }
  }
  else
  { // read the sponsor's email from the DB
    $result = DBQuery($database, "SELECT * FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 
    $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
  } // end else: if (isset($save))

  pageheader(lang('change_email'),
             lang('change_email'),
             "Update","",$database);
  echo "<br />";
  box_begin("inputbox",lang('change_email'));
?>
<strong><?php echo lang('change_email_label'); ?></strong><br />
<form method="post" action="changeemail.php">
<?php
  if (!checkemail($sponsor["email"])) {
    feedback(lang('email_invalid'),1);
?>
  <br />
<?php
  } /* end: if ($checkemail($sponsor["email"])) */
?>
  <input type="text" name="sponsor_email" maxlength="100" size="60" value="<?php echo HTMLSpecialChars($sponsor["email"]); ?>" />
  <br />
  <br />
  <input type="submit" name="save" value="<?php echo lang('ok_button_text'); ?>" />
  <input type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>" />
</form>
<?php
  box_end();
  echo "<br />";

  require("footer.inc.php");
?>
