<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }

  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['httpreferer'])) { setVar($httpreferer,$_POST['httpreferer'],'httpreferer'); } else { unset($httpreferer); }
  if (isset($_GET['timebegin_year'])) { setVar($timebegin_year,$_GET['timebegin_year'],'timebegin_year'); } else { unset($timebegin_year); }
  if (isset($_GET['timebegin_month'])) { setVar($timebegin_month,$_GET['timebegin_month'],'timebegin_month'); } else { unset($timebegin_month); }
  if (isset($_GET['timebegin_day'])) { setVar($timebegin_day,$_GET['timebegin_day'],'timebegin_day'); } else { unset($timebegin_day); }

  if (isset($_POST['cancel'])) {
    redirect2URL("update.php");
    exit;
  };

  if (!isset($httpreferer)) { $httpreferer = $_SERVER["HTTP_REFERER"]; }
	
  // read sponsor name from DB
  $result = DBQuery($database, "SELECT name,url FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 
  $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);

  // test if any template exists already
  $result = DBQuery($database, "SELECT * FROM vtcal_template WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND sponsorid='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 

  if ($result->numRows() == 0) { // before: if ($result->numRows() == '0')
    // reroute to input page
    $url = "changeeinfo.php";

    // if addevent was called by clicking on the icons in week or month view provide the date info
    if (isset($timebegin_year)) {
      $url.="?templateid=0&timebegin_year=".$timebegin_year."&timebegin_month=".$timebegin_month."&timebegin_day=".$timebegin_day;
    }
    redirect2URL($url);
    exit;
  }

  // print page header
  pageheader(lang('choose_template'),
             lang('choose_template'),
             "","",$database);
  echo "<BR>";
  box_begin("inputbox",lang('choose_template'));
?>
<BR>
<FORM method="post" action="changeeinfo.php">
<?php
  echo '<INPUT type="hidden" name="httpreferer" value="',$httpreferer,'">',"\n";
?>
  <SELECT name="templateid" size="6">
    <OPTION selected value="0">----- <?php echo lang('blank'); ?> -----</OPTION>
<?php
  $result = DBQuery($database, "SELECT * FROM vtcal_template WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND sponsorid='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 
  for ($i=0; $i<$result->numRows(); $i++) {
    $template = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
    echo "<OPTION value=\"",$template['id'],"\">",$template['name'],"</OPTION>\n";
  }
?>
  </SELECT>
  <BR>
  <BR>
  <INPUT type="submit" name="choosetemplate" value="<?php echo lang('ok_button_text'); ?>">
  <INPUT type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
<?php
  // forward date info, if the page was called with date info appended
  // can later be done with PHP session management
  if (isset($timebegin_year)) { echo "<INPUT type=\"hidden\" name=\"timebegin_year\" value=\"",$timebegin_year,"\">"; }
  if (isset($timebegin_month)) { echo "<INPUT type=\"hidden\" name=\"timebegin_month\" value=\"",$timebegin_month,"\">"; }
  if (isset($timebegin_day)) { echo "<INPUT type=\"hidden\" name=\"timebegin_day\" value=\"",$timebegin_day,"\">"; }
?>
</FORM>
<?php
  box_end();
  echo "<BR>";
  require("footer.inc.php");
?>
