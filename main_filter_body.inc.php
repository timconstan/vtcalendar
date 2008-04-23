<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files
?><table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#FFFFFF">
<tr valign="top">
	<td>
<script language="JavaScript" type="text/javascript"><!--
function checkAll(myForm, id, state) {
  // determine if ALL of the checkboxes is checked
  b = new Boolean( true );
  for (var cnt=0; cnt < myForm.elements.length; cnt++) {
    var ckb = myForm.elements[cnt];
    if (ckb.type == "checkbox" && ckb.name.indexOf(id) == 0) {
      if (ckb.checked == false) { b = false; }
    }
  }

  for (var cnt=0; cnt < myForm.elements.length; cnt++) {
    var ckb = myForm.elements[cnt];
    if (ckb.type == "checkbox" && ckb.name.indexOf(id) == 0) {
      if ( b == true ) { ckb.checked = false; }
      else { ckb.checked = true; };
    }
  }
}

function validate ( myForm, id ) {
  b = new Boolean( false );
  for (var cnt=0; cnt < myForm.elements.length; cnt++) {
    var ckb = myForm.elements[cnt];
    if (ckb.type == "checkbox" && ckb.name.indexOf(id) == 0) {
      if (ckb.checked == true) { b = true; break; }
    }
  }
  if ( b == false ) {
    alert ( "Please select one or more categories before clicking the button." );
    return false;
  }
  return true;
}
//--></script>
	<table border="0" cellpadding="0" cellspacing="10" bgcolor="#FFFFFF">
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="categorylist">
			<tr align="left" valign="top">
			<td colspan="4" valign="top">
				<strong><?php echo lang('select_categories'); ?></strong>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="4" align="left" nowrap>
				<a href="javascript:checkAll(document.categorylist,'filtercategories',true);"><?php echo lang('select_unselect'); ?></a>
			</td>
			</tr>
			<tr valign="top">
				<td align="left" nowrap>
<?php
	$percolumn = ceil($numcategories / 3);
	for ($c=0; $c<$numcategories; $c++) {
		// determine if the current category has been selected previously
		if ( isset($categoryfilter) ) {
          $categoryselected = array_key_exists( $categories_id[$c], $categoryfilter );
		}
		else {
		  $categoryselected = true;
		}
		
		if ($c > 0 && $c % $percolumn == 0) {
		  echo "    </td>\n";
				echo "    <td align=\"left\" nowrap>\n";
			}
		echo "    <input type=\"checkbox\" name=\"filtercategories[]\" id=\"category",$c,"\" value=\"".$categories_id[$c]."\"";
			if ( $categoryselected || count($filtercategories)==0 ) {
			  echo " checked";
			}
			echo ">\n";
		echo "<label for=\"category",$c,"\">",$categories_name[$c],"</label><br>\n";
	} // end: for ($c=0; $c<$numcategories; $c++)
?>    
				</td>
			</tr>
			<tr valign="top">
				<td colspan="3" align="left" valign="top">
			  	<br>
					<input type="submit" name="ok" value="&nbsp;&nbsp;<?php echo lang('apply_filter'); ?>&nbsp;&nbsp;">&nbsp;
				</td>
			</tr>
			<input type="hidden" name="view" value="week">
		</form>
		</table>
  	<br>
	  <br>
	</td>
</tr>
</table>
