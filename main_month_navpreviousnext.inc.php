<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

  $previous_href = "main.php?view=month&timebegin=".urlencode(datetime2timestamp($minus_one_month['year'],$minus_one_month['month'],$minus_one_month['day'],12,0,"am"))."&sponsorid=".urlencode($sponsorid)."&categoryid=".urlencode($categoryid)."&keyword=".urlencode($keyword); 
  $next_href = "main.php?view=month&timebegin=".urlencode(datetime2timestamp($plus_one_month['year'],$plus_one_month['month'],$plus_one_month['day'],12,0,"am"))."&sponsorid=".urlencode($sponsorid)."&categoryid=".urlencode($categoryid)."&keyword=".urlencode($keyword);
?>
            <table border="0" cellspacing="0" cellpadding="1">
              <tr>
                <td align="right" valign="middle"><a href="<?php echo $previous_href; ?>"><strong><img src="images/littlearrowleft.gif" width="15" height="13" border="0" align="absmiddle"></strong></a></td>
                <td align="left" valign="middle"><a href="<?php echo $previous_href; ?>" ><strong><?php echo lang('previous_month'); ?>
                      </strong></a></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td align="right" valign="middle"><a href="<?php echo $next_href; ?>" ><strong><?php echo lang('next_month'); ?>
                      </strong></a></td>
                <td align="right" valign="middle"><a href="<?php echo $next_href; ?>"><strong><img src="images/littlearrowright.gif" width="15" height="13" border="0" align="absmiddle"></strong></a></td>
              </tr>
            </table>