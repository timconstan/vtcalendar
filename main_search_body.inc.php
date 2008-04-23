<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files
?><table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#FFFFFF">
        <tr valign="top">
				<td>
<?php
  // check if some input params are set, and if not set them to default
  if (!isset($timebegin_year))  { $timebegin_year = $today['year']; }
  if (!isset($timebegin_month)) { $timebegin_month = $today['month']; }
  if (!isset($timebegin_day))   { $timebegin_day = $today['day']; }

  if (!isset($timeend_year)) { $timeend_year = $timebegin_year; }
  if (!isset($timeend_month)) {
    $timeend_month = $timebegin_month+6;
    if ($timeend_month >= 13) {
      $timeend_month = $timeend_month-12;
      $timeend_year++;
    }
  }
  if (!isset($timeend_day)) {
    $timeend_day = $timebegin_day;
    while (!checkdate($timeend_month,$timeend_day,$timeend_year)) { $timeend_day--; };
  }
?>
<br>
<form method="get" action="main.php" name="searchform">
  <input type="hidden" name="view" value="searchresults">
  <TABLE border="0" cellpadding="3" cellspacing="2">
    <TR>
      <TD class="bodytext" valign="baseline">
        <strong><?php echo lang('keyword'); ?>:&nbsp;&nbsp;&nbsp;</strong>
      </TD>
      <TD class="bodytext" valign="baseline">
        <INPUT type="text" size="40" name="keyword" value="<?php echo $keyword; ?>" maxlength="<?php echo constKeywordMaxLength; ?>"><br>
        <?php echo lang('case_insensit'); ?><br>
        <br>
      </TD>
    </TR>
    <tr>
		  <TD class="bodytext" valign="baseline">
        <strong><?php echo lang('starting_from'); ?></strong>
      </TD>
      <TD class="bodytext" valign="baseline">

<?php
inputdate($timebegin_month,"timebegin_month",
            $timebegin_day,"timebegin_day",
            $timebegin_year,"timebegin_year");
?>

					</td></tr>
						<tr>
						  <td>&nbsp;</td>
						  <td><br><INPUT type="submit" name="search" value="&nbsp;&nbsp;&nbsp;<?php echo lang('search'); ?>&nbsp;&nbsp;&nbsp;"></td>
						</tr>
					
        </TABLE>
  <BR>
</FORM>				
				</td>
        </tr>
      </table>
<script language="JavaScript1.2"><!--
  document.searchform.keyword.focus();
//--></script>