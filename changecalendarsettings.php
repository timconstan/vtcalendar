<?php //
session_start();
require_once('globalsettings.inc.php');
require_once('functions.inc.php');

$database = DBopen();
if (!authorized($database)) { exit; }
if (!$_SESSION["AUTH_ADMIN"]) { exit; } // additional security

if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
if (isset($_POST['save'])) { setVar($save,$_POST['save'],'save'); } else { unset($save); }
if (isset($_POST['users'])) { setVar($users,$_POST['users'],'users'); } else { unset($users); }
if (isset($_POST['title'])) { setVar($title,$_POST['title'],'calendarTitle'); } else { unset($title); }
if (isset($_POST['header'])) { setVar($header,$_POST['header'],'calendarHeader'); } else { unset($header); }
if (isset($_POST['footer'])) { setVar($footer,$_POST['footer'],'calendarFooter'); } else { unset($footer); }
if (isset($_POST['viewauthrequired'])) { setVar($viewauthrequired,$_POST['viewauthrequired'],'viewauthrequired'); } else { unset($viewauthrequired); }
if (isset($_POST['forwardeventdefault'])) { setVar($forwardeventdefault,$_POST['forwardeventdefault'],'forwardeventdefault'); } else { unset($forwardeventdefault); }
if (isset($_POST['bgcolor'])) { setVar($bgcolor,$_POST['bgcolor'],'color'); } else { unset($bgcolor); }
if (isset($_POST['maincolor'])) { setVar($maincolor,$_POST['maincolor'],'color'); } else { unset($maincolor); }
if (isset($_POST['todaycolor'])) { setVar($todaycolor,$_POST['todaycolor'],'color'); } else { unset($todaycolor); }
if (isset($_POST['pastcolor'])) { setVar($pastcolor,$_POST['pastcolor'],'color'); } else { unset($pastcolor); }
if (isset($_POST['futurecolor'])) { setVar($futurecolor,$_POST['futurecolor'],'color'); } else { unset($futurecolor); }
if (isset($_POST['textcolor'])) { setVar($textcolor,$_POST['textcolor'],'color'); } else { unset($textcolor); }
if (isset($_POST['linkcolor'])) { setVar($linkcolor,$_POST['linkcolor'],'color'); } else { unset($linkcolor); }
if (isset($_POST['gridcolor'])) { setVar($gridcolor,$_POST['gridcolor'],'color'); } else { unset($gridcolor); }
   
if (isset($cancel)) {
redirect2URL("update.php");
exit;
};

if (!(isset($title) && isset($header) && isset($footer) && 
	  isset($bgcolor) && isset($maincolor) && isset($todaycolor) && 
	  isset($pastcolor) && isset($futurecolor) && isset($textcolor) && isset($linkcolor) && isset($gridcolor) &&
      isset($viewauthrequired))) { //(re-)read from database
	$title = $_SESSION["TITLE"];	
	$header = $_SESSION["HEADER"];	
	$footer = $_SESSION["FOOTER"];	
	$viewauthrequired	= $_SESSION["VIEWAUTHREQUIRED"];
	$forwardeventdefault = $_SESSION["FORWARDEVENTDEFAULT"];

	$bgcolor = $_SESSION["BGCOLOR"];	
	$maincolor = $_SESSION["MAINCOLOR"];
	$todaycolor = $_SESSION["TODAYCOLOR"]; //color of the day's view border color, today's date highlight in week, month view and in little calendar 
	$pastcolor = $_SESSION["PASTCOLOR"];		
	$futurecolor = $_SESSION["FUTURECOLOR"];		
	$textcolor = $_SESSION["TEXTCOLOR"];		
	$linkcolor = $_SESSION["LINKCOLOR"];		
	$gridcolor = $_SESSION["GRIDCOLOR"];		
}

$addPIDError="";
if ( isset($save) ) {
	if (!preg_match(REGEXVALIDCOLOR, $bgcolor)) { $bgcolor = "#ffffff"; }
	if (!preg_match(REGEXVALIDCOLOR, $maincolor)) { $maincolor = "#ff9900"; }
	if (!preg_match(REGEXVALIDCOLOR, $todaycolor)) { $todaycolor = "#ffcc66"; }
	if (!preg_match(REGEXVALIDCOLOR, $pastcolor)) { $pastcolor = "#eeeeee"; }
	if (!preg_match(REGEXVALIDCOLOR, $futurecolor)) { $futurecolor = "#ffffff"; }
	if (!preg_match(REGEXVALIDCOLOR, $textcolor)) { $textcolor = "#000000"; }
	if (!preg_match(REGEXVALIDCOLOR, $linkcolor)) { $linkcolor = "#3333cc"; }
	if (!preg_match(REGEXVALIDCOLOR, $gridcolor)) { $gridcolor = "#cccccc"; }

	// check validity of users
	if ( !empty($users) ) {
		// disassemble the users string and check all PIDs against the DB
		$pidsInvalid = "";
		$pidsTokens = split ( "[ ,;\n\t]", $users );
		$pidsAddedCount = 0;
		for ($i=0; $i<count($pidsTokens); $i++) {
			$pidName = $pidsTokens[$i];
			$pidName = trim($pidName);
			if ( !empty($pidName) ) {
				if ( isValidUser ( $database, $pidName ) ) {
					$pidsAdded[$pidsAddedCount] = $pidName;
					$pidsAddedCount++;
				} 
				else {
					if ( !empty($pidsInvalid) ) { $pidsInvalid .= ","; }
					$pidsInvalid .= $pidName;
				}
			} 
		} // end: while

		// feedback message(s)
		if ( !empty($pidsInvalid) ) {
			if ( strpos($pidsInvalid, "," ) > 0 ) { // more than one user-ID
				$addPIDError = lang('user_ids_invalid')." &quot;".$pidsInvalid."&quot;";
			}
			else {
				$addPIDError = lange('user_id_invalid')." &quot;".$pidsInvalid."&quot;";
			}
		}
	} // end: else: if ( empty($users) )
	
	if (empty($addPIDError)) { 
		// save the settings to database
		if ( $viewauthrequired != 0 ) { $viewauthrequired = 1; }
		if ( $forwardeventdefault!="1" ) { $forwardeventdefault = "0"; }
		$result = DBQuery($database, 
		"UPDATE vtcal_calendar SET title='".sqlescape($title)."',header='".sqlescape($header)."',footer='".sqlescape($footer)."',
bgcolor='".sqlescape($bgcolor)."',maincolor='".sqlescape($maincolor)."',todaycolor='".sqlescape($todaycolor)."',
pastcolor='".sqlescape($pastcolor)."',futurecolor='".sqlescape($futurecolor)."',textcolor='".sqlescape($textcolor)."',
linkcolor='".sqlescape($linkcolor)."',gridcolor='".sqlescape($gridcolor)."',
viewauthrequired='".sqlescape($viewauthrequired)."',forwardeventdefault='".sqlescape($forwardeventdefault)."' 
WHERE id='".sqlescape($_SESSION["CALENDARID"])."'" ); 
		
		// substitute existing auth info with the new one
		$result = DBQuery($database, "DELETE FROM vtcal_calendarviewauth WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."'" );
		for ($i=0; $i<count($pidsAdded); $i++) {
			$result = DBQuery($database, "INSERT INTO vtcal_calendarviewauth (calendarid,userid) VALUES ('".sqlescape($_SESSION["CALENDARID"])."','".sqlescape($pidsAdded[$i])."')" );
		}
		
		setCalendarPreferences();
		
		redirect2URL("update.php");
		exit;
	} // end: if (empty($addPIDError))
}

// read sponsor name from DB
$result = DBQuery($database, "SELECT name FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($_SESSION["AUTH_SPONSORID"])."'" ); 
$sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);

pageheader(lang('change_header_footer_colors_auth'),
           lang('change_header_footer_colors_auth'),
           "Update","",$database);
echo "<br>\n";
box_begin("inputbox", lang('change_header_footer_colors_auth'));
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="globalSettings">
<script language="javascript">
function UpdateNewColor( colorVarName, colorImgName, color ) {
  document[colorImgName].src = "images/webcolors/" + color + ".gif";
  document.globalSettings[colorVarName].value = '#' + color;
}

function WebColorTable( colorVarName, colorImgName, initColor )
{
  var cur_color = initColor.substr(1,6).toLowerCase();
  var cur_image = "images/webcolors/" + cur_color + ".gif";

  // have to nest table in a shell table so netscape correctly fills in background color
  document.writeln('<table border="0" cellpadding="0" cellspacing="0"><tr><td bgcolor="#000000">');
  document.writeln('<table border="0" cellpadding="0" cellspacing="1" bgcolor="#000000">');

  var color;

  for (hgreen=0; hgreen<=153; hgreen+=153) {
    for (red=0; red<=255; red+=51) {
      document.writeln('<tr>');
      for (green=hgreen; green<=hgreen+102; green+=51) {
        for (blue=0; blue<=255; blue+=51) {
          // determine the color from the rgb value
          color = '';

          if (red<=16) color += '0' + red.toString(16);
          else color += red.toString(16);

          if (green<=16) color += '0' + green.toString(16);
          else color += green.toString(16);

          if (blue<=16) color += '0' + blue.toString(16);
          else color += blue.toString(16);

          document.write('<td align="center" bgcolor="#' + color + '">');
          document.write('<a href="javascript:UpdateNewColor(\'' + colorVarName + '\', \'' + colorImgName + '\', \'' + color + '\');">');

          if(color == cur_color) {
            if((red/51 > 3) || (green/51 > 3) || (blue/51 > 3))
              document.write('<img src="images/frame_dark.gif" width="13" height="13" border="0" alt=""></a></td>');
            else
              document.write('<img src="images/frame_lite.gif" width="13" height="13" border="0" alt=""></a></td>');
          } else {
            document.write('<img src="images/spacer.gif" width="13" height="13" border="0" alt=""></a></td>');
          }
        }
      }
      document.writeln('</tr>');
    }
  }

  document.writeln('</table></td></tr>');
  document.writeln('<tr><td align="right" valign="top">');
  document.writeln('</td></tr></table>');
}
</script>
 <br>

  <b><?php echo lang('calendar_title'); ?>:</b> <font color="#999999"><?php echo lang('empty_or_any_text'); ?></font><br>
  <input type="text" name="title" maxlength="<?php echo $constCalendarTitleMAXLENGTH; ?>" size="30" value="<?php 
	echo htmlentities($title);
	?>"><br>
  <br>

  <b><?php echo lang('header_html'); ?>:</b> <font color="#999999"><?php echo lang('empty_or_any_html'); ?></font><br>
  <textarea name="header" wrap="physical" cols="70" rows="10"><?php 
	echo htmlentities($header);
	?></textarea><br>
  <br>

  <b><?php echo lang('footer_html'); ?>:</b> <font color="#999999"><?php echo lang('empty_or_any_html'); ?></font><br>
  <textarea name="footer" wrap="physical" cols="70" rows="10"><?php
	echo htmlentities($footer);
  ?></textarea><br>
  <br>

<?php echo lang('colorscheme'); ?>
<br>
<br>
<table border="0" cellspacing="0" cellpadding="0" >
<tr>
<td colspan="2"><strong><?php echo lang('backgroundcolor'); ?>:</strong></td>
</tr>
<tr>
<td>
<table cellspacing="0" cellpadding="0"><tr><td bgcolor="#000000" ><table cellspacing="1" cellpadding="0"><tr><td>
<img
name="imgBgColor" src="images/webcolors/<?php echo substr($bgcolor,1); ?>.gif" height="15" width="15"><br></td>
</tr></table></td></tr></table>
</td>
<td>&nbsp;<input type="text" name="bgcolor" size="8" maxlength="7" value="<?php echo $bgcolor; ?>"></td>
</tr>
<tr><td colspan="2">
<script language="JavaScript">
  WebColorTable( 'bgcolor', 'imgBgColor', '<?php echo substr($bgcolor,1); ?>' );
</script>
</td>
</tr>
</table>

<br>

<table border="0" cellspacing="0" cellpadding="0" ><tr>
<td colspan="2"><strong><?php echo lang('maincolor'); ?>:</strong></td>
</tr>
<tr>
<td>
<table   cellspacing="0" cellpadding="0"><tr><td bgcolor="#000000" ><table cellspacing="1" cellpadding="0"><tr><td>
<img
name="imgMaincolor" src="images/webcolors/<?php echo substr($maincolor,1); ?>.gif" height="15" width="15"><br></td>
</tr></table></td></tr></table>
</td>
<td>&nbsp;<input type="text" name="maincolor" size="8" maxlength="7" value="<?php echo $maincolor; ?>"></td>
</tr>
<tr><td colspan="2">
<script language="JavaScript">
  WebColorTable( 'maincolor', 'imgMaincolor', '<?php echo substr($maincolor,1); ?>' );
</script>
</td>
</tr>
</table>

<br>

<table border="0" cellspacing="0" cellpadding="0" ><tr>
<td colspan="2"><strong><?php echo lang('textcolor'); ?>:</strong></td>
</tr>
<tr>
<td>
<table   cellspacing="0" cellpadding="0"><tr><td bgcolor="#000000" ><table cellspacing="1" cellpadding="0"><tr><td>
<img
name="imgTextcolor" src="images/webcolors/<?php echo substr($textcolor,1); ?>.gif" height="15" width="15"><br></td>
</tr></table></td></tr></table>
</td>
<td>&nbsp;<input type="text" name="textcolor" size="8" maxlength="7" value="<?php echo $textcolor; ?>"></td>
</tr>
<tr><td colspan="2">
<script language="JavaScript">
  WebColorTable( 'textcolor', 'imgTextcolor', '<?php echo substr($textcolor,1); ?>' );
</script>
</td>
</tr>
</table>

<br>

<table border="0" cellspacing="0" cellpadding="0" ><tr>
<td colspan="2"><strong><?php echo lang('linkcolor'); ?>:</strong></td>
</tr>
<tr>
<td>
<table   cellspacing="0" cellpadding="0"><tr><td bgcolor="#000000" ><table cellspacing="1" cellpadding="0"><tr><td>
<img
name="imgLinkcolor" src="images/webcolors/<?php echo substr($linkcolor,1); ?>.gif" height="15" width="15"><br></td>
</tr></table></td></tr></table>
</td>
<td>&nbsp;<input type="text" name="linkcolor" size="8" maxlength="7" value="<?php echo $linkcolor; ?>"></td>
</tr>
<tr><td colspan="2">
<script language="JavaScript">
  WebColorTable( 'linkcolor', 'imgLinkcolor', '<?php echo substr($linkcolor,1); ?>' );
</script>
</td>
</tr>
</table>

<br>

<table border="0" cellspacing="0" cellpadding="0" ><tr>
<td colspan="2"><strong><?php echo lang('gridcolor'); ?>:</strong> <span class="example"><?php echo lang('gridcolor_explanation'); ?></span></td>
</tr>
<tr>
<td>
<table   cellspacing="0" cellpadding="0"><tr><td bgcolor="#000000" ><table cellspacing="1" cellpadding="0"><tr><td>
<img
name="imgGridcolor" src="images/webcolors/<?php echo substr($gridcolor,1); ?>.gif" height="15" width="15"><br></td>
</tr></table></td></tr></table>
</td>
<td>&nbsp;<input type="text" name="gridcolor" size="8" maxlength="7" value="<?php echo $gridcolor; ?>"></td>
</tr>
<tr><td colspan="2">
<script language="JavaScript">
  WebColorTable( 'gridcolor', 'imgGridcolor', '<?php echo substr($gridcolor,1); ?>' );
</script>
</td>
</tr>
</table>

<br>

<table border="0" cellspacing="0" cellpadding="0" ><tr>
<td colspan="2"><strong><?php echo lang('pastcolor'); ?>:</strong></td>
</tr>
<tr>
<td>
<table   cellspacing="0" cellpadding="0"><tr><td bgcolor="#000000" ><table cellspacing="1" cellpadding="0"><tr><td>
<img
name="imgPastcolor" src="images/webcolors/<?php echo substr($pastcolor,1); ?>.gif" height="15" width="15"><br></td>
</tr></table></td></tr></table>
</td>
<td>&nbsp;<input type="text" name="pastcolor" size="8" maxlength="7" value="<?php echo $pastcolor; ?>"></td>
</tr>
<tr><td colspan="2">
<script language="JavaScript">
  WebColorTable( 'pastcolor', 'imgPastcolor', '<?php echo substr($pastcolor,1); ?>' );
</script>
</td>
</tr>
</table>

<br>

<table border="0" cellspacing="0" cellpadding="0" ><tr>
<td colspan="2"><strong><?php echo lang('todaycolor'); ?>:</strong></td>
</tr>
<tr>
<td>
<table   cellspacing="0" cellpadding="0"><tr><td bgcolor="#000000" ><table cellspacing="1" cellpadding="0"><tr><td>
<img
name="imgTodayColor" src="images/webcolors/<?php echo substr($todaycolor,1); ?>.gif" height="15" width="15"><br></td>
</tr></table></td></tr></table>
</td>
<td>&nbsp;<input type="text" name="todaycolor" size="8" maxlength="7" value="<?php echo $todaycolor; ?>"></td>
</tr>
<tr><td colspan="2">
<script language="JavaScript">
  WebColorTable( 'todaycolor', 'imgTodayColor', '<?php echo substr($todaycolor,1); ?>' );
</script>
</td>
</tr>
</table>

<br>

<table border="0" cellspacing="0" cellpadding="0" ><tr>
<td colspan="2"><strong><?php echo lang('futurecolor'); ?>:</strong></td>
</tr>
<tr>
<td>
<table   cellspacing="0" cellpadding="0"><tr><td bgcolor="#000000" ><table cellspacing="1" cellpadding="0"><tr><td>
<img
name="imgFuturecolor" src="images/webcolors/<?php echo substr($futurecolor,1); ?>.gif" height="15" width="15"><br></td>
</tr></table></td></tr></table>
</td>
<td>&nbsp;<input type="text" name="futurecolor" size="8" maxlength="7" value="<?php echo $futurecolor; ?>"></td>
</tr>
<tr><td colspan="2">
<script language="JavaScript">
  WebColorTable( 'futurecolor', 'imgFuturecolor', '<?php echo substr($futurecolor,1); ?>' );
</script>
</td>
</tr>
</table>

<br>

<?php
  if ( $_SESSION["CALENDARID"] != "default" ) {
?>
<?php
  $result = DBQuery($database, "SELECT * FROM vtcal_calendar WHERE id='default'" ); 
  $c = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
  $defaultcalendarname = $c['name'];
?>
<br>
  <table border="0">
    <tr align="left" valign="top">
      <td><input type="checkbox" name="forwardeventdefault" id="forwardeventdefault" value="1"<?php if ($forwardeventdefault=="1") { echo " checked"; } ?>></td>
      <td><strong><label for="forwardeventdefault">By default also display events on the <?php echo $defaultcalendarname ?></label></strong> <br>
        (Sponsors can still disable this on a per-event basis)</td>
    </tr>
  </table>
<?php
  } // end: if ( $_SESSION["CALENDARID"] != "default" ) {
?>
 <br>
<br>

    <b><?php echo lang('login_required_for_viewing'); ?></b>
</p>
<table border="0" cellpadding="3" cellspacing="3">
<tr>
  <td align="right"><input type="radio" name="viewauthrequired" value="0"<?php 
	if ( $viewauthrequired == 0 ) { echo " checked"; }
	?>></td>
  <td align="left"><?php echo lang('no_login_required'); ?><br></td>
</tr>
<tr>
  <td align="right" valign="top"><input type="radio" name="viewauthrequired" value="1"<?php 
	if ( $viewauthrequired != 0 ) { echo " checked"; }
	?>></td>
  <td align="left"><?php echo lang('login_required_user_ids'); ?>:<br>
<?php
  if (!empty($addPIDError)) {    
    feedback($addPIDError,1);
  }
?>
		<textarea name="users" cols="40" rows="6" wrap="virtual"><?php
		if ( isset($users) ) {
		  echo $users;
		}
		else {
		  $query = "SELECT * FROM vtcal_calendarviewauth WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' ORDER BY userid";
      $result = DBQuery($database, $query ); 
			$i = 0;
			while ($i < $result->numRows()) {
			  $viewauth = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
				if ($i>0) { echo ","; }
				echo $viewauth['userid'];
				$i++;
			}
		}
		?></textarea><br>
		<i><?php echo lang('separate_user_ids_with_comma'); ?></i>
	</td>
</tr>
</table>
  <br>  
  <br>
  <input type="submit" name="save" value="<?php echo lang('ok_button_text'); ?>" class="button">&nbsp;&nbsp;<input type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>" class="button">
</form>
<?php 
  box_end();
  echo "<br>";
  require("footer.inc.php");
?>