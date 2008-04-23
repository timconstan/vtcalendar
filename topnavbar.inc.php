<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files
?><table width="100%" border="0" cellspacing="0" cellPadding="0">
  <tr>
    <td width="1%" valign="bottom" bgcolor="#ffffff"><img src="images/spacer.gif" height="30" width="5" alt=""></td>
    <td width="1%" rowspan="2" valign="bottom" bgcolor="#ffffff"><a href="index.php"><img src="images/logo.gif" alt="" width="34" height="34" border="0"></a></td>
    <td width="97%" valign="bottom"  bgcolor="#ffffff">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="bottom" nowrap width="1%">&nbsp;<span class="calendartitle"><?php 
  if (isset($_SESSION["TITLE"])) { echo $_SESSION["TITLE"]; } else { echo lang('calendar'); } ?></span></td>
					<td valign="bottom" align="left" width="30%">
<?php
  if ($navbaractive=="Day") { $day_class = "tabactive"; } else { $day_class = "tabinactive"; }
  if ($navbaractive=="Week") { $week_class = "tabactive"; } else { $week_class = "tabinactive"; }
  if ($navbaractive=="Month") { $month_class = "tabactive"; } else { $month_class = "tabinactive"; }
  if ($navbaractive=="Search") { $search_class = "tabactive"; } else { $search_class = "tabinactive"; }
  if ($navbaractive=="Update") { $update_class = "tabactive"; } else { $update_class = "tabinactive"; }
?>						  
						<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td rowspan="3" valign="top"><img src="images/spacer.gif" height="1" width="10" alt="1"></td>
								<td rowspan="3" class="<?php echo $day_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="2" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $day_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="1" width="1" alt="1"></td>
								<td class="<?php echo $day_class; ?>"><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $day_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="1" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $day_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="2" width="1" alt="1"></td>
								<td><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $week_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="2" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $week_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="1" width="1" alt="1"></td>
								<td class="<?php echo $week_class; ?>"><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $week_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="1" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $week_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="2" width="1" alt="1"></td>
								<td><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $month_class; ?>" valign="top"><?php if ($enableViewMonth) { ?><img src="images/webcolors/ffffff.gif" height="2" width="1" alt="1"><?php } ?></td>
								<td rowspan="3" class="<?php echo $month_class; ?>" valign="top"><?php if ($enableViewMonth) { ?><img src="images/webcolors/ffffff.gif" height="1" width="1" alt="1"><?php } ?></td>
								<td class="<?php echo $month_class; ?>"><?php if ($enableViewMonth) { ?><img src="images/spacer.gif" height="3" width="1" alt="1"><?php }  ?></td>
								<td rowspan="3" class="<?php echo $month_class; ?>" valign="top"><?php if ($enableViewMonth) { ?><img src="images/webcolors/ffffff.gif" height="1" width="1" alt="1"><?php } ?></td>
								<td rowspan="3" class="<?php echo $month_class; ?>" valign="top"><?php if ($enableViewMonth) { ?><img src="images/webcolors/ffffff.gif" height="2" width="1" alt="1"><?php } ?></td>
								<td rowspan="3" valign="top"><img src="images/spacer.gif" height="1" width="10" alt="1"></td>
							</tr>
							<tr>
								<td class="<?php echo $day_class; ?>"><strong>&nbsp;&nbsp;&nbsp;<?php 
								if ($navbaractive=="Day") echo lang('day'); 
						 else { echo '<a href="main.php?view=day" >',lang('day'),'</a>'; } ?>&nbsp;&nbsp;&nbsp;</strong></td>
								<td><img src="images/spacer.gif" width="3" height="1" alt=""></td>
								<td class="<?php echo $week_class; ?>"><strong>&nbsp;&nbsp;<?php 
								if ($navbaractive=="Week") echo lang('week');
 else { echo '<a href="main.php?view=week" >',lang('week'),'</a>'; } ?>&nbsp;&nbsp;</strong></td>
								<td><img src="images/spacer.gif" width="3" height="1" alt=""></td>
								<td class="<?php echo $month_class; ?>"><?php 
								  if ($enableViewMonth) { 
								?><strong>&nbsp;&nbsp;<?php 
	  							if ($navbaractive=="Month") echo lang('month');
else { echo '<a href="main.php?view=month">',lang('month'),'</a>'; } ?>&nbsp;&nbsp;</strong><?php
								} // end: if ($enableViewMonth) 
								?></td>
							</tr>
							<tr>
								<td class="<?php echo $day_class; ?>"><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td class="<?php echo $week_class; ?>"><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td class="<?php echo $month_class; ?>"><?php if ($enableViewMonth) { ?><img src="images/spacer.gif" height="3" width="1" alt="1"><?php } ?></td>
							</tr>
						</table>
					</td>
          <td valign="bottom" align="right" width="50%"><?php 
  if (!empty($_SESSION["AUTH_USERID"])) {
?>
    <table cellpadding="3" cellspacing="0" border="0"><tr><td>
		<b><?php echo $_SESSION["AUTH_USERID"]; ?></b> 
<?php		
  if (!empty($_SESSION["AUTH_SPONSORNAME"])) {
		echo "(<b>",$_SESSION["AUTH_SPONSORNAME"],"</b>)"; 
	}
?>		
		<?php echo lang('is_logged_on'); ?> 
		<a href="logout.php">Logout</a>
		</td></tr></table>
<?php		
  } // end: if (!empty($_SESSION["AUTH_USERID"]))
	else { echo "&nbsp;"; }
					?></td>
          <td valign="bottom" align="right" width="19%">
						<table border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td rowspan="3" valign="top"><img src="images/spacer.gif" height="1" width="10" alt="1"></td>
								<td rowspan="3" class="<?php echo $search_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="2" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $search_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="1" width="1" alt="1"></td>
								<td class="<?php echo $search_class; ?>"><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $search_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="1" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $search_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="2" width="1" alt="1"></td>
								<td><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $update_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="2" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $update_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="1" width="1" alt="1"></td>
								<td class="<?php echo $update_class; ?>"><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $update_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="1" width="1" alt="1"></td>
								<td rowspan="3" class="<?php echo $update_class; ?>" valign="top"><img src="images/webcolors/ffffff.gif" height="2" width="1" alt="1"></td>
							</tr>
							<tr>
								<td class="<?php echo $search_class; ?>"><strong>&nbsp;&nbsp;&nbsp;<?php 
								if ($navbaractive=="Search") echo lang('search');
 else { echo '<a href="main.php?view=search">',lang('search'),'</a>'; } ?>&nbsp;&nbsp;&nbsp;</strong></td>
								<td><img src="images/spacer.gif" width="3" height="1" alt=""></td>
								<td class="<?php echo $update_class; ?>"><strong>&nbsp;&nbsp;<?php 
								if ($navbaractive=="Update") echo lang('update');
 else { echo '<a href="update.php">',lang('update'),'</a>'; } ?>&nbsp;&nbsp;</strong></td>
							</tr>
							<tr>
								<td class="<?php echo $search_class; ?>"><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
								<td class="<?php echo $update_class; ?>"><img src="images/spacer.gif" height="3" width="1" alt="1"></td>
							</tr>
						</table>
					</td>            
				</tr>
      </table>
    </td>
    <td width="1%" bgcolor="#ffffff"><img src="images/spacer.gif" height="1" width="5" alt=""></td>
  </tr>
  <tr>
    <td bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>"><img src="images/spacer.gif" height="1" width="5" alt=""></td>
    <td bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>"><img src="images/spacer.gif" height="8" width="1" alt=""></td>
    <td bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>"><img src="images/spacer.gif" height="1" width="5" alt=""></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>"><img src="images/spacer.gif" height="5" width="1" alt=""></td>
  </tr>
</table>
