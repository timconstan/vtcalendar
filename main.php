<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');
  if (isset($_POST['userid'])) { setVar($userid,$_POST['userid'],'userid'); } else { unset($userid); }
  if (isset($_POST['password'])) { setVar($password,$_POST['password'],'password'); } else { unset($password); }
  if (isset($_GET['view'])) { setVar($view,$_GET['view'],'view'); } else { 
	  if (!empty($_SESSION['view'])) { $view = $_SESSION['view']; }
		else { unset($view); } 
	}
  if (isset($_GET['eventid'])) { setVar($eventid,$_GET['eventid'],'eventid'); } else { unset($eventid); }
  if (isset($_GET['timebegin'])) { setVar($timebegin,$_GET['timebegin'],'timebegin'); } else { unset($timebegin); }
  if (isset($_GET['timebegin_year'])) { setVar($timebegin_year,$_GET['timebegin_year'],'timebegin_year'); } else { unset($timebegin_year); }
  if (isset($_GET['timebegin_month'])) { setVar($timebegin_month,$_GET['timebegin_month'],'timebegin_month'); } else { unset($timebegin_month); }
  if (isset($_GET['timebegin_day'])) { setVar($timebegin_day,$_GET['timebegin_day'],'timebegin_day'); } else { unset($timebegin_day); }
  if (isset($_GET['timeend'])) { setVar($timeend,$_GET['timeend'],'timeend'); } else { unset($timeend); }
  if (isset($_GET['timeend_year'])) { setVar($timeend_year,$_GET['timeend_year'],'timeend_year'); } else { unset($timeend_year); }
  if (isset($_GET['timeend_month'])) { setVar($timeend_month,$_GET['timeend_month'],'timeend_month'); } else { unset($timeend_month); }
  if (isset($_GET['timeend_day'])) { setVar($timeend_day,$_GET['timeend_day'],'timeend_day'); } else { unset($timeend_day); }
  if (isset($_GET['categoryid'])) { setVar($categoryid,$_GET['categoryid'],'categoryid'); } else { unset($categoryid); }
  if (isset($_GET['sponsorid'])) { setVar($sponsorid,$_GET['sponsorid'],'sponsorid'); } else { unset($sponsorid); }
  if (isset($_GET['keyword'])) { setVar($keyword,$_GET['keyword'],'keyword'); } else { unset($keyword); }
  if (isset($_GET['filtercategories'])) { setVar($filtercategories,$_GET['filtercategories'],'filtercategories'); } else { unset($filtercategories); }
  if (isset($_COOKIE['CategoryFilter'])) { setVar($CategoryFilter,$_COOKIE['CategoryFilter'],'CategoryFilter'); } else { unset($CategoryFilter); }

  $database = DBopen();
  if (!viewauthorized($database)) { exit; }

	if (!isset($view)) { $view = "day"; }
  if ( $view == "month" && !$enableViewMonth ) { $view="week"; }
	if ( $view == "event" && !isset($eventid) ) { $view="week"; }
  $_SESSION['view'] = $view;
		
  // determine today's date
  $today = Decode_Date_US(date("m/d/Y"));
  $today['dow_text'] = Day_of_Week_Abbreviation(Day_of_Week($today['month'],$today['day'],$today['year']));

  // if the starting point not passed as a param then use defaults
  if (isset($timebegin_month) && isset($timebegin_year)) {
    $timebegin=datetime2timestamp($timebegin_year,$timebegin_month,1,$day_beg_h,0,"am");
  }
  elseif (!isset($timebegin) || $timebegin=="today") {
    // use today's date as default
    $timebegin=datetime2timestamp($today['year'],$today['month'],$today['day'],$day_beg_h,0,"am");
  }
  if (!isset($categoryid)) { $categoryid=0; }
  if (!isset($sponsorid)) { $sponsorid="all"; }
  if (!isset($keyword)) { $keyword=""; }

  // the week is specified by a single day, the whole week this day belongs to is displayed
  $showdate = timestamp2datetime($timebegin);
  $showdate['text'] = Encode_Date_US($showdate['month'],$showdate['day'],$showdate['year']);
  $showdate['timestamp_daybegin']=datetime2timestamp($showdate['year'],$showdate['month'],$showdate['day'],$day_beg_h,0,"am");
  $showdate['timestamp_dayend']  =datetime2timestamp($showdate['year'],$showdate['month'],$showdate['day'],$day_end_h,59,"pm");

  // determine the month
  $month1['year']  = $showdate['year'];
  $month1['month'] = $showdate['month'];
  $month1['day']   = 1;
  $month1['text']  = Month_to_Text($month1['month']);

  $minus_one_month['day']   = 1;
  $minus_one_month['month'] = $month1['month'] - 1;
  $minus_one_month['year']  = $month1['year'];
  if ($minus_one_month['month'] == 0) {
    $minus_one_month['month'] = 12;
    $minus_one_month['year']--;
  }

  $plus_one_month['day']   = 1;
  $plus_one_month['month'] = $month1['month'] + 1;
  $plus_one_month['year']  = $month1['year'];
  if ($plus_one_month['month'] == 13) {
    $plus_one_month['month'] = 1;
    $plus_one_month['year']++;
  }

  // date of first Sunday or Monday according to week beginning day in month1
  $month1['dow'] = Day_of_Week($month1['month'],1,$month1['year']);

  // $week_correction - variable to make one week correction according to week's starting weekday
  if($week_start == 1 && $month1['dow'] == 0){
    $week_correction=7;
  }else{
     $week_correction=0;
  }

  $month1start = Add_Delta_Days($month1['month'],1,$month1['year'],-$month1['dow']+$week_start-$week_correction);
  $month1start['timestamp'] = datetime2timestamp($month1start['year'],$month1start['month'],$month1start['day'],$day_beg_h,0,"am");
  $month1lastday = Add_Delta_Days($plus_one_month['month'],1,$plus_one_month['year'],-1);
  $month1lastday['dow'] = Day_of_Week($month1lastday['month'],$month1lastday['day'],$month1lastday['year']);
  $month1lastday['timestamp'] = datetime2timestamp($month1lastday['year'],$month1lastday['month'],$month1lastday['day'],$day_end_h,59,"pm");
	$month1end = Add_Delta_Days($month1lastday['month'],$month1lastday['day'],$month1lastday['year'],+6-$month1lastday['dow']+$week_start);
  $month1end['timestamp'] = datetime2timestamp($month1end['year'],$month1end['month'],$month1end['day'],$day_end_h,59,"pm");
  $month1['timestamp'] = datetime2timestamp($month1['year'],$month1['month'],$month1['day'],$day_beg_h,0,"am");

  // when does this particular week start and end?
  $dow = Day_of_Week($showdate['month'],$showdate['day'],$showdate['year']);
  $weekfrom = Add_Delta_Days($showdate['month'],$showdate['day'],$showdate['year'],-$dow+$week_start); //if $week_start is 1 we get Monday as week's start
  $weekto = Add_Delta_Days($showdate['month'],$showdate['day'],$showdate['year'],6-$dow+$week_start); //if $week_start is 1 we get Sunday week's end

  // determine the number of days since 4713 BC, needed for date arithmatic
  $weekfrom['jd'] = JulianToJD($weekfrom['month'],$weekfrom['day'],$weekfrom['year']);
  $weekto['jd']   = JulianToJD($weekto['month'],$weekto['day'],$weekto['year']);

  // construct timestamp for weekfrom & weekto
  $weekfrom['timestamp']=datetime2timestamp($weekfrom['year'],$weekfrom['month'],$weekfrom['day'],$day_beg_h,0,"am");
  $weekto['timestamp']  =datetime2timestamp($weekto['year'],$weekto['month'],$weekto['day'],$day_end_h,59,"pm");

  // determine the date of today minus/plus one week/month (important for navig. arrows)
  $minus_one_week = Add_Delta_Days($showdate['month'],$showdate['day'],$showdate['year'],-7);
  $plus_one_week  = Add_Delta_Days($showdate['month'],$showdate['day'],$showdate['year'],7);

// if only today is shown highlight it
$bodycolor = $_SESSION['MAINCOLOR']; 
if ( $view == "day" ) { 
	if ( $showdate['day']==$today['day'] && $showdate['month']==$today['month'] && $showdate['year']==$today['year']) {
  		$bodycolor = $colortoday; 
	}
}
	
  // read all categories from the DB in two arrays
  $result = DBQuery($database, "SELECT * FROM vtcal_category WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' ORDER BY name" ); 
	$numcategories = $result->numRows();
	for ($c=0; $c<$numcategories; $c++) {
	  $categorydata = $result->fetchRow(DB_FETCHMODE_ASSOC, $c);
		$categories_id[$c]= $categorydata['id'];
		$categories_name[$c]= $categorydata['name'];
  }

	if ( isset($filtercategories) ) {
    if ( count($filtercategories)==$numcategories  ) {
		  unset($filtercategories); // if all categories are selected that means there is NO filter
      setcookie ("CategoryFilter", "", time()-3600); // delete filter cookie
		}
		else {		
			$categoryfilter = array_flip ( $filtercategories );
			// set a cookie
			$filtercookie = "";
			for($c=0; $c<count($filtercategories); $c++) {
				if ($c > 0) { $filtercookie .= ","; }		
				$filtercookie .= $filtercategories[$c];
			}
			setcookie ("CategoryFilter", $filtercookie, time()+3600*24*365);
		}
	}
	else {
	  // check for existence of a cookie & read it
		if ( isset($CategoryFilter) ) {
      $filtercategories = split ( ",", $CategoryFilter );
      $categoryfilter = array_flip ( $filtercategories );
		}
    if ( isset($filtercategories) && count($filtercategories)==$numcategories ) {
		  unset($filtercategories); // if all categories are selected that means there is NO filter
		  unset($categoryfilter);
      setcookie ("CategoryFilter", $filtercookie, time()-3600); // delete filter cookie
		}
	}
	
  if ( $view == "day" ) { 
  	pageheader(lang('day_page_header'),"","Day","",$database);
	}
	elseif ( $view == "week" ) { 
  	pageheader(lang('week_page_header'),"","Week","",$database);
	}
	elseif ( $view == "month" ) { 
  	pageheader(lang('month_page_header'),"","Month","",$database);
	}
	elseif ( $view == "event" ) { 
  	pageheader(lang('event_page_header'),"","","",$database);
	}
	elseif ( $view == "search" ) { 
  	pageheader(lang('search_page_header'),"","Search","",$database);
	}
	elseif ( $view == "searchresults" ) { 
  	pageheader(lang('searchresults_page_header'),"","Search","",$database);
	}
	elseif ( $view == "subscribe" ) { 
  	pageheader(lang('subscribe_page_header'),"","Subscribe","",$database);
	}
	elseif ( $view == "filter" ) { 
  	pageheader(lang('filter_page_header'),"","Filter","",$database);
	}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="1%" rowspan="5" bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>"><img src="images/spacer.gif" height="1" width="5" alt=""></td>
    <td width="5%" bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>"><img src="images/spacer.gif" height="1" width="5" alt=""></td>
    <td width="1%" rowspan="5" bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>"><img src="images/spacer.gif" height="1" width="3" alt=""></td>
    <td width="1%" rowspan="3" bgcolor="<?php echo $bodycolor; ?>"><img src="images/spacer.gif" height="1" width="3" alt=""><br>
    </td>
    <td width="90%" rowspan="2" valign="bottom" bgcolor="<?php echo $bodycolor; ?>">
<?php
  if ( $view == "day" || $view == "week" || $view == "month" || 
       $view == "search" || $view == "searchresults"
  	 ) { 
	  if (isset($filtercategories)) { 
?>
<img src="images/spacer.gif" width="1" height="5" alt=""><br>
<table width="100%"  border="0" cellspacing="0" cellpadding="2">
	<tr>
		<td bgcolor="#ff0000" style="color:#FFFFFF; font-size:0.7em">
		  &nbsp;<b><?php echo lang('showing_filtered_events'); ?></b> (<?php 
	
	$activecategories = "";
	for ($c=0; $c<$numcategories; $c++) {
		// determine if the current category has been selected previously
    $categoryselected = array_key_exists( $categories_id[$c], $categoryfilter );
		if ( $categoryselected || count($filtercategories)==0 ) {
		  if (!empty($activecategories)) { $activecategories.=", "; }
			$activecategories .= $categories_name[$c];
    }
  }		
	if (strlen($activecategories) > 70) { $activecategories = substr($activecategories,0,70)."..."; }
	echo $activecategories;	
?>)</td>
    <td nowrap align="right" bgcolor="#ff0000" style="font-size:0.7em"><b><!--a style="color:#0000ff; text-decoration:none" href="main.php?view=filter">Filter events</a-->&nbsp;</b>
		</td>
	</tr>
	</table>
<?php
    } // end:  if (isset($filtercategories)) 
  } // end: if ( substr($_SERVER['PHP_SELF'],-7) == "day.php" || ...
?>
      <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="<?php echo $bodycolor; ?>">
        <tr>
          <td valign="bottom">
					<img alt="" src="images/spacer.gif" width="1" height="3"><br>
					<img alt="" src="images/spacer.gif" width="3" height="1"><span class="datetitle"><?php 
require ( "main_".$view."_datetitle.inc.php" );		  
		  ?></span></td>
          <td align="right" valign="bottom"><?php 
require ( "main_".$view."_navpreviousnext.inc.php" );		  
		  ?></td>
        </tr>
				<tr><td colspan="3"><img alt="" src="images/spacer.gif" height="2" width="1"><br></td></tr>
      </table>
    </td>
    <td width="1%" rowspan="3" bgcolor="<?php echo $bodycolor; ?>"><img src="images/spacer.gif" height="1" width="3" alt=""></td>
    <td width="1%" rowspan="5" bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>"><img src="images/spacer.gif" height="1" width="3" alt=""></td>
  </tr>
  <tr>
    <td valign="bottom" bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>">
		<table width="100%" border="0" cellpadding="0" cellspacing="2" bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>">
        <tr>
          <td align="left" valign="middle"><a href="<?php echo $_SERVER['PHP_SELF']."?timebegin=".urlencode(datetime2timestamp($minus_one_month['year'],$minus_one_month['month'],$minus_one_month['day'],12,0,"am"))."&sponsorid=".urlencode($sponsorid)."&categoryid=".urlencode($categoryid)."&keyword=".urlencode($keyword); ?>"><img src="images/littlearrowleft.gif" width="15" height="13" border="0"></a></td>
          <td align="center" nowrap valign="middle"><?php
  if ( $view == "month" || !$enableViewMonth ) { 
    echo "<span style=\"color:#000000; font-weight:bold; text-decoration:none\">".above_lit_cal_date_format (Month_to_Text($month1['month']), $month1['year'])."</span>";
  }
	else {
    echo "<a style=\"font-weight:bold\" href=\"main.php?view=month&amp;timebegin=".urlencode(datetime2timestamp($month1['year'],$month1['month'],$month1['day'],12,0,"am"))."&sponsorid=".urlencode($sponsorid)."&categoryid=".urlencode($categoryid)."&keyword=".urlencode($keyword)."\" >";
    echo above_lit_cal_date_format (Month_to_Text($month1['month']), $month1['year']);
    echo "</a>";
	}					
?></td>
          <td align="right" valign="middle"><a href="<?php echo $_SERVER['PHP_SELF']."?timebegin=".urlencode(datetime2timestamp($plus_one_month['year'],$plus_one_month['month'],$plus_one_month['day'],12,0,"am"))."&sponsorid=".urlencode($sponsorid)."&categoryid=".urlencode($categoryid)."&keyword=".urlencode($keyword); ?>"><img src="images/littlearrowright.gif" width="15" height="13" border="0"></a></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td rowspan="2" valign="top" bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>">
		<table width="100%" border="0" cellpadding="3" cellspacing="0" >
        <tr align="center">
          <td class="littlecalendarheader" width="16%">&nbsp;</td>
          <?php if($week_start == 0){?>
            <td class="littlecalendarheader" width="12%"><?php echo lang('lit_cal_sun'); ?></td>
	 <?php } ?>
          <td class="littlecalendarheader" width="12%"><?php echo lang('lit_cal_mon');?></td>
          <td class="littlecalendarheader" width="12%"><?php echo lang('lit_cal_tue');?></td>
          <td class="littlecalendarheader" width="12%"><?php echo lang('lit_cal_wed');?></td>
          <td class="littlecalendarheader" width="12%"><?php echo lang('lit_cal_thu');?></td>
          <td class="littlecalendarheader" width="12%"><?php echo lang('lit_cal_fri');?></td>
          <td class="littlecalendarheader" width="12%"><?php echo lang('lit_cal_sat');?></td>
	 <?php if($week_start == 1){?>
            <td class="littlecalendarheader" width="12%"><?php echo lang('lit_cal_sun');?></td>
	<?php } ?>
        </tr>
        <tr align="center">
          <td colspan="8" bgcolor="#ffffff">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td bgcolor="#999999"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
              </tr>
            </table>
          </td>
        </tr>

<?php 
  // print 6 lines for the weeks
  for ($iweek=1; $iweek<=6; $iweek++) {
    // first day of the week
    $weekstart = Add_Delta_Days($month1start['month'],$month1start['day'],$month1start['year'],($iweek-1)*7);
    $weekstart['timestamp'] = datetime2timestamp($weekstart['year'],$weekstart['month'],$weekstart['day'],12,0,"am");

    // print the 5th and the 6th week only if the days are still in this month
    if (($iweek < 5) || ($weekstart['month'] == $month1['month'])) {
      echo "<tr>\n";

      // output the link to the week
      echo "<td class=\"littlecalendarweek\" ";
			
			echo "valign=\"top\" align=\"left\">\n";
      echo "<a style=\"text-decoration:none\" href=\"main.php?view=week&amp;timebegin=".urlencode($weekstart['timestamp'])."\">".lang('lit_cal_week'),date("W",mktime(0,0,0,$weekstart['month'],$weekstart['day'],$weekstart['year'])),"</a></td>\n";

      // output event info for every day
      for ($weekday = 0; $weekday <= 6; $weekday++) {
        // calculate the appropriate day for the cell of the calendar
        $iday = Add_Delta_Days($month1start['month'],$month1start['day'],$month1start['year'],($iweek-1)*7+$weekday);
        $iday['timebegin'] = datetime2timestamp($iday['year'],$iday['month'],$iday['day'],0,0,"am");
        $iday['timeend']   = datetime2timestamp($iday['year'],$iday['month'],$iday['day'],11,59,"pm");

        $iday['css'] = datetoclass($iday['month'],$iday['day'],$iday['year']);
        $iday['color'] = datetocolor($iday['month'],$iday['day'],$iday['year'],$colorpast,$colortoday,$colorfuture);
        echo "<td class=\"littlecalendarday\" ";
				if ( $iday['day']==$today['day'] && $iday['month']==$today['month'] && $iday['year']==$today['year']) {
  				echo "style=\"background-color:".$_SESSION["TODAYCOLOR"]."\" ";
				} 
				else { // highlight the days that are currently displayed on the right
          if ( $view == "day" ) { 
    				if ( $iday['day']==$showdate['day'] && $iday['month']==$showdate['month'] && $iday['year']==$showdate['year']) {
  						echo "style=\"background-color:".$_SESSION["GRIDCOLOR"]."\" ";
						}
					} 
					else if ( $view == "week" ) { 
            $datediff1 = Delta_Days($weekfrom['month'],$weekfrom['day'],$weekfrom['year'],$iday['month'],$iday['day'],$iday['year']);				
            $datediff2 = Delta_Days($iday['month'],$iday['day'],$iday['year'],$weekto['month'],$weekto['day'],$weekto['year']);
						if ( $datediff1 >= 0 && $datediff2 >= 0 ) {
  						echo "style=\"background-color:".$_SESSION["GRIDCOLOR"]."\" ";
						}
					}
					else if ( $view == "month" ) { 
					  if ($iday['month']==$month1['month']) {
  						echo "style=\"background-color:".$_SESSION["GRIDCOLOR"]."\" ";
						}
					}
				}
				echo "valign=\"top\" align=\"center\">\n";

        if ( $view == "day" &&
  			     $iday['day']==$showdate['day'] && $iday['month']==$showdate['month'] && $iday['year']==$showdate['year']) {
				  echo $iday['day'];		 
				}
				else {
					echo "<a ";
					if ( $iday['month']!=$month1['month'] ) {
						echo "style=\"color:#999999; text-decoration:none\" ";
					}
					else {
						echo "style=\"text-decoration:none\" ";
					}
					echo "href=\"main.php?view=day&amp;timebegin=",urlencode(datetime2timestamp($iday['year'],$iday['month'],$iday['day'],12,0,"am")),"&timeend=",urlencode(datetime2timestamp($iday['year'],$iday['month'],$iday['day'],11,59,"pm")),"&sponsorid=",urlencode($sponsorid),"&categoryid=",urlencode($categoryid),"&keyword=",urlencode($keyword),"\">";
					echo $iday['day'];
					echo "</a>\n";
        }
				echo "</td>\n";
      } // end: for ($weekday = 0; $weekday <= 6; $weekday++)
      echo "</tr>\n";
    } // end: if (($iweek < 5) || ($weekstart[month] == $month1[month])
  } // end: for ($iweek=1; $iweek<=6; $iweek++)

?>				
      </table>
      <img src="images/spacer.gif" width="1" height="3" alt=""><br>
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
			<form name="form1" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <tr>
				  <td>
				<select name="timebegin" onChange="document.forms.form1.submit()" style="width:100%">
					<option selected><?php echo lang('jump_to'); ?></option>
<?php
  $m = date("n");
	$y = date("Y");
	for ($i=1; $i<=24; $i++) {
		$m++;
		if ($m==13) { 
		  $m=1; 
			$y++; 
   		echo '					<option value="',date("Y"),'-',date("m"),'-01 00:00:00">----------------</option>',"\n";
		}
		echo '					<option value="',$y,'-',str_pad($m, 2, "0", STR_PAD_LEFT),'-01 00:00:00">',Month_to_Text($m),' ',$y,'</option>',"\n";
  }
?>					
				</select>
				<input type="hidden" name="view" value="month">
			  	</td>
				</tr>
			</form>
			</table>
      <img src="images/spacer.gif" width="1" height="7" alt=""><br>
			
      <table width="100%" border="0" cellpadding="2" cellspacing="0" bgcolor="<?php echo $_SESSION["TODAYCOLOR"]; ?>">
        <tr>
          <td class="todayis"><?php echo lang('today_is'); ?><br>
<?php
  $showtodaylink = 0;
  if ( !($view=="day" && 
	       $showdate['year']==$today['year'] &&
	       $showdate['month']==$today['month'] &&
	       $showdate['day']==$today['day'] 
				 ) ) {
	  $showtodaylink = 1;
	}
	if ($showtodaylink) {
    echo "<a href=\"main.php?view=day&amp;timebegin=today\" >";	
	}
  echo "<b>";

echo today_is_date_format($today['day'], Day_of_Week_to_Text(Day_of_Week($today['month'],$today['day'],$today['year'])),Month_to_Text($today['month']),$today['year']);
  echo "</b>";
	if ($showtodaylink) {
    echo "</a>";
	}
?>
           </td>
        </tr>
      </table>
      <img src="images/spacer.gif" width="1" height="7" alt=""><br>

			<br>
<?php
  if ($view!='subscribe') {
		echo'<a style="font-weight:bold"  href="main.php?view=subscribe"><b>',lang('subscribe_download'),'</b></a>';
	}
	else {
	  echo '<b>',lang('subscribe_download'),'</b>';
	}
?>
			<br>
      <img src="images/spacer.gif" width="1" height="6" alt=""><br>
<?php
  if ($view!='filter') {
		echo'    <a style="font-weight:bold" href="main.php?view=filter"><b>',lang('filter_events'),'</b></a>';
	}
	else {
	  echo '<b>',lang('filter_events'),'</b>';
	}
?>
<br>
<br>
    </td>
    <td align="left" valign="top" bgcolor="<?php echo $bodycolor; ?>">
<?php 
require ( "main_".$view."_body.inc.php" );		  
?>
      </span>
    </td>
  </tr>
  <tr>
    <td colspan="3" bgcolor="<?php echo $bodycolor; ?>"><img src="images/spacer.gif" height="5" width="1" alt=""></td>
  </tr>
  <tr>
    <td bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>"><img src="images/spacer.gif" width="1" height="10" alt=""></td>
    <td bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
    <td bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>" align="right" valign="top">
	  <img src="images/spacer.gif" width="1" height="20" alt="" align="absmiddle"><span style="font-size:smaller">powered by <a style="text-decoration:none" href="http://vtcalendar.sourceforge.net/">VTCalendar</a> 
	 <?php if (file_exists("VERSION.txt")) { include('VERSION.txt'); } ?></span>
	
	</td>
    <td bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>"><img src="images/spacer.gif" width="1" height="1" alt=""></td>
  </tr>
</table>
<?php
  require("footer.inc.php");
?>
