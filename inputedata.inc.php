<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

function defaulteventtime(&$event) {
  $event['timebegin_year']=date("Y");
  $event['timebegin_month']=0;
  $event['timebegin_day']=0;
  $event['timebegin_hour']=0;
  $event['timebegin_min']=0;
  $event['timebegin_ampm']="pm";

  $event['timeend_hour']=0;
  $event['timeend_min']=0;
  $event['timeend_ampm']="pm";

  return 0;
}

function defaultevent(&$event,$sponsorid,$database) {
  defaulteventtime($event);

  // find sponsor name
  $result = DBQuery($database, "SELECT name,url FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($sponsorid)."'" ); 
  $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);

  $event['sponsorid']=$sponsorid;
  $event['title']="";
  $event['wholedayevent']=0;
  $event['categoryid']=0;
  $event['description']="";
  $event['location']="";
  $event['price']="";
  $event['contact_name']="";
  $event['contact_phone']="";
  $event['contact_email']="";
  $event['url']="http://";
  $event['displayedsponsor'] = "";
  $event['displayedsponsorurl'] = ""; //$sponsor['url'];

  return 1;
} /* function defaultevent */

/* checks the validity of the time 1am-12pm or 0:00-23:00 */
function checktime($hour,$min) {
   global $use_ampm;
   if ($use_ampm){
      return
         (($hour>0) && ($hour<=12)) &&
         (($min>=0) && ($min<=59));
   }else{
	   return
         (($hour>=0) && ($hour<23)) &&
         (($min>=0) && ($min<=59));
   }

}

function checkeventdate(&$event,&$repeat) {
  if ($repeat['mode']==0) { // it's a one-time event (no recurrences)
    return (checkdate($event['timebegin_month'],
                      $event['timebegin_day'],
		                  $event['timebegin_year']));
  }
  else { // it's a recurring event
    return (checkdate($event['timebegin_month'],
                      $event['timebegin_day'],
                      $event['timebegin_year'])
            &&
            !empty($event['timeend_month']) && !empty($event['timeend_day']) && !empty($event['timeend_year'])
						&&
						checkdate($event['timeend_month'],
                      $event['timeend_day'],
                      $event['timeend_year'])
	          &&
            checkstartenddate($event['timebegin_month'],
	                            $event['timebegin_day'],
                              $event['timebegin_year'],
                              $event['timeend_month'],
                              $event['timeend_day'],
                              $event['timeend_year']));
  }
}

function checkstartenddate($startdate_month,$startdate_day,$startdate_year,
                           $enddate_month,$enddate_day,$enddate_year) {
  if (strlen($startdate_month) == 1) { $startdate_month = "0".$startdate_month; }
  if (strlen($startdate_day) == 1) { $startdate_day = "0".$startdate_day; }
  if (strlen($enddate_month) == 1) { $enddate_month = "0".$enddate_month; }
  if (strlen($enddate_day) == 1) { $enddate_day = "0".$enddate_day; }

  $startdate = $startdate_year.$startdate_month.$startdate_day;
  $enddate = $enddate_year.$enddate_month.$enddate_day;

  return $startdate <= $enddate;
} // end: function checkstartenddate

function checkeventtime(&$event) {
  if ($event['wholedayevent']==1) {
    return 1;
  }
  else {
    /* create two temporary variables to compare times */
    $timebegin_hour = $event['timebegin_hour'];
    if (strlen($timebegin_hour) == 1) { $timebegin_hour = "0".$timebegin_hour; }
    elseif ($timebegin_hour == "12") { $timebegin_hour = "00"; }
    $timebegin_min = $event['timebegin_min'];
    if (strlen($timebegin_min) == 1) { $timebegin_min = "0".$timebegin_min; }
    $timebegin = $event['timebegin_ampm'].$timebegin_hour.$timebegin_min;

    $timeend_hour = $event['timeend_hour'];
    if (strlen($timeend_hour) == 1) { $timeend_hour = "0".$timeend_hour; }
    elseif ($timeend_hour == "12") { $timeend_hour = "00"; }
    $timeend_min = $event['timeend_min'];
    if (strlen($timeend_min) == 1) { $timeend_min = "0".$timeend_min; }
    $timeend = $event['timeend_ampm'].$timeend_hour.$timeend_min;

    return
      (
        checktime($event['timebegin_hour'],$event['timebegin_min'])
      );
  }
}

function checkevent(&$event,&$repeat) {
  return
    (!empty($event['title'])) &&
    checkeventdate($event,$repeat) &&
    checkeventtime($event) &&
    ($event['categoryid']>=1) &&
    checkURL(urldecode($event['url'])) &&
    checkURL(urldecode($event['displayedsponsorurl'])) &&
		(!isset($event['showondefaultcal']) || $event['showondefaultcal']==0 || $event['showincategory']!=0);
}

// shows the inputfields for the recurrence information
function inputrecurrences(&$event,&$repeat,$check) {
?>
        <INPUT type="radio" name="repeat[mode]" id="repeatmode1" value="1"<?php if ($repeat['mode']==1) { echo " checked"; } ?>><label for="repeatmode1"> 
		<?php echo lang('repeat'); ?></label>
        <SELECT name="repeat[interval1]" size="1">
          <OPTION value="every"<?php if (isset($repeat['interval1']) && $repeat['interval1']=="every") { echo " selected"; } ?>><?php echo lang('every'); ?></OPTION>
          <OPTION value="everyother"<?php if (isset($repeat['interval1']) && $repeat['interval1']=="everyother") { echo " selected"; } ?>><?php echo lang('every_other'); ?></OPTION>
          <OPTION value="everythird"<?php if (isset($repeat['interval1']) && $repeat['interval1']=="everythird") { echo " selected"; } ?>><?php echo lang('every_third'); ?></OPTION>
          <OPTION value="everyfourth"<?php if (isset($repeat['interval1']) && $repeat['interval1']=="everyfourth") { echo " selected"; } ?>><?php echo lang('every_fourth'); ?></OPTION>
        </SELECT>
        <SELECT name="repeat[frequency1]" size="1">
          <OPTION value="day"<?php if (isset($repeat['frequency1']) && $repeat['frequency1']=="day") { echo " selected"; } ?>><?php echo lang('day'); ?></OPTION>
          <OPTION value="week"<?php if (isset($repeat['frequency1']) && $repeat['frequency1']=="week") { echo " selected"; } ?>><?php echo lang('week'); ?></OPTION>
          <OPTION value="month">Month<?php if (isset($repeat['frequency1']) && $repeat['frequency1']=="month") { echo " selected"; } ?></OPTION>
          <OPTION value="year"<?php if (isset($repeat['frequency1']) && $repeat['frequency1']=="year") { echo " selected"; } ?>><?php echo lang('year'); ?></OPTION>
          <OPTION value="monwedfri"<?php if (isset($repeat['frequency1']) && $repeat['frequency1']=="monwedfri") { echo " selected"; } ?>><?php echo lang('mon'); ?>, <?php echo lang('wed'); ?>, <?php echo lang('fri'); ?></OPTION>
          <OPTION value="tuethu"<?php if (isset($repeat['frequency1']) && $repeat['frequency1']=="tuethu") { echo " selected"; } ?>><?php echo lang('tue'); ?> &amp; <?php echo lang('thu'); ?></OPTION>
          <OPTION value="montuewedthufri"<?php if (isset($repeat['frequency1']) && $repeat['frequency1']=="montuewedthufri") { echo " selected"; } ?>><?php echo lang('mon'); ?> - <?php echo lang('fri'); ?></OPTION>
          <OPTION value="satsun"<?php if (isset($repeat['frequency1']) && $repeat['frequency1']=="satsun") { echo " selected"; } ?>><?php echo lang('sat'); ?> &amp; <?php echo lang('sun'); ?></OPTION>
        </SELECT>
        <BR>
        <INPUT type="radio" name="repeat[mode]" id="repeatmode2" value="2"<?php if ($repeat['mode']==2) { echo " checked"; } ?>> <label for="repeatmode2"><?php echo lang('repeat_on_the'); ?></label>
        <SELECT name="repeat[frequency2modifier1]" size="1">
          <OPTION value="first"<?php if (isset($repeat['frequency2modifier1']) && $repeat['frequency2modifier1']=="first") { echo " selected"; } ?>><?php echo lang('first'); ?></OPTION>
          <OPTION value="second"<?php if (isset($repeat['frequency2modifier1']) && $repeat['frequency2modifier1']=="second") { echo " selected"; } ?>><?php echo lang('second'); ?></OPTION>
          <OPTION value="third"<?php if (isset($repeat['frequency2modifier1']) && $repeat['frequency2modifier1']=="third") { echo " selected"; } ?>><?php echo lang('third'); ?></OPTION>
          <OPTION value="fourth"<?php if (isset($repeat['frequency2modifier1']) && $repeat['frequency2modifier1']=="fourth") { echo " selected"; } ?>><?php echo lang('fourth'); ?></OPTION>
          <OPTION value="last"<?php if (isset($repeat['frequency2modifier1']) && $repeat['frequency2modifier1']=="last") { echo " selected"; } ?>><?php echo lang('last'); ?></OPTION>
        </SELECT>
        <SELECT name="repeat[frequency2modifier2]" size="1">
          <OPTION value="sun"<?php if (isset($repeat['frequency2modifier2']) && $repeat['frequency2modifier2']=="sun") { echo " selected"; } ?>><?php echo lang('sun'); ?></OPTION>
          <OPTION value="mon"<?php if (isset($repeat['frequency2modifier2']) && $repeat['frequency2modifier2']=="mon") { echo " selected"; } ?>><?php echo lang('mon'); ?></OPTION>
          <OPTION value="tue"<?php if (isset($repeat['frequency2modifier2']) && $repeat['frequency2modifier2']=="tue") { echo " selected"; } ?>><?php echo lang('tue'); ?></OPTION>
          <OPTION value="wed"<?php if (isset($repeat['frequency2modifier2']) && $repeat['frequency2modifier2']=="wed") { echo " selected"; } ?>><?php echo lang('wed'); ?></OPTION>
          <OPTION value="thu"<?php if (isset($repeat['frequency2modifier2']) && $repeat['frequency2modifier2']=="thu") { echo " selected"; } ?>><?php echo lang('thu'); ?></OPTION>
          <OPTION value="fri"<?php if (isset($repeat['frequency2modifier2']) && $repeat['frequency2modifier2']=="fri") { echo " selected"; } ?>><?php echo lang('fri'); ?></OPTION>
          <OPTION value="sat"<?php if (isset($repeat['frequency2modifier2']) && $repeat['frequency2modifier2']=="sat") { echo " selected"; } ?>><?php echo lang('sat'); ?></OPTION>
        </SELECT>
        of the month every
        <SELECT name="repeat[interval2]" size="1">
          <OPTION value="month"<?php if (isset($repeat['interval2']) && $repeat['interval2']=="month") { echo " selected"; } ?>><?php echo lang('month'); ?></OPTION>
          <OPTION value="2months"<?php if (isset($repeat['interval2']) && $repeat['interval2']=="2months") { echo " selected"; } ?>><?php echo lang('other_month'); ?></OPTION>
          <OPTION value="3months"<?php if (isset($repeat['interval2']) && $repeat['interval2']=="3months") { echo " selected"; } ?>>3 <?php echo lang('months'); ?></OPTION>
          <OPTION value="4months"<?php if (isset($repeat['interval2']) && $repeat['interval2']=="4months") { echo " selected"; } ?>>4 <?php echo lang('months'); ?></OPTION>
          <OPTION value="6months"<?php if (isset($repeat['interval2']) && $repeat['interval2']=="6months") { echo " selected"; } ?>>6 <?php echo lang('months'); ?></OPTION>
          <OPTION value="year"<?php if (isset($repeat['interval2']) && $repeat['interval2']=="year") { echo " selected"; } ?>><?php echo lang('year'); ?></OPTION>
        </SELECT>
	<BR>
        <BR>
<?php
  if (isset($check) && $repeat['mode'] > 0) {

    if (!isset($event['timeend_month']) || !isset($event['timeend_day']) || !isset($event['timeend_year'])) {
      feedback(lang('specify_valid_ending_date'),1);
		}
    elseif (
		    !checkdate($event['timebegin_month'],$event['timebegin_day'],$event['timebegin_year']) &&
        !checkdate($event['timeend_month'],$event['timeend_day'],$event['timeend_year'])
				) {
      feedback(lang('specify_valid_dates'),1);
    }
    elseif (!checkdate($event['timebegin_month'],$event['timebegin_day'],$event['timebegin_year'])) {
      feedback(lang('specify_valid_starting_date'),1);
    }
    elseif (!checkdate($event['timeend_month'],$event['timeend_day'],$event['timeend_year'])) {
      feedback(lang('specify_valid_ending_date'),1);
    }
    elseif (!checkstartenddate($event['timebegin_month'],
                               $event['timebegin_day'],
			                         $event['timebegin_year'],
                               $event['timeend_month'],
			                         $event['timeend_day'],
			                         $event['timeend_year'])) {
      feedback(lang('ending_date_after_starting_date'),1);
    }
  } // end: if (isset($check) && repeat[mode] > 0)
?>
	      from
<?php
    inputdate($event['timebegin_month'],"event[timebegin_month]",
            $event['timebegin_day'],"event[timebegin_day]",
            $event['timebegin_year'],"event[timebegin_year]");
    echo " ",lang('to')," ";
		if (!isset($event['timeend_month']) || !isset($event['timeend_day']) || !isset($event['timeend_year'])) {
			inputdate(0,"event[timeend_month]",
							0,"event[timeend_day]",
							$event['timebegin_year'],"event[timeend_year]");
    }
		else {
			inputdate($event['timeend_month'],"event[timeend_month]",
							$event['timeend_day'],"event[timeend_day]",
							$event['timeend_year'],"event[timeend_year]");
		}
?>
        <BR>
<?php
} // end: function inputrecurrences

/* print out the event input form and use the provided parameter as preset */
function inputeventdata(&$event,$sponsorid,$inputrequired,$check,$displaydatetime,&$repeat,$database) {
  /* now printing the HTML code for the input form */
  global $use_ampm;
  $unknownvalue = "???"; /* this is printed when the value of input field is unspecified */

  // the value of the radio box when user chooses recurring event
  $recurring = 10;

  // read sponsor name from DB
  $result = DBQuery($database, "SELECT name,url FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($sponsorid)."'" ); 
  $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
?>
<TABLE border="0" cellpadding="2" cellspacing="0">
<?php
  // switch from "recurring event" to "repeat ..."
  if ($repeat['mode']==$recurring) { $repeat['mode'] = 1; }
  if ($displaydatetime) {
?>
  <TR>
    <TD class="bodytext" valign="top"><strong><?php echo lang('date'); ?>:</strong>
<?php
  if ($inputrequired) {
?>
<FONT color="#FF0000">*</FONT>
<?php
  }
?>
    </TD>
    <TD class="bodytext" valign="top">
<?php
  if ($inputrequired && $check && $repeat['mode'] == 0 && !checkeventdate($event,$repeat)) {
    feedback(lang('date_invalid'),1);
  }

  echo '<INPUT type="radio" name="repeat[mode]" value="0" id="onetime"';
  if (!isset($repeat['mode']) || $repeat['mode']==0) { echo " checked"; }
  echo ' onClick="this.form.submit()">';
  echo "\n<label for=\"onetime\">",lang('one_time_event'),"</label> ";

  if ($repeat['mode']==0) {
		if (!isset($event['timebegin_month'])) { $event['timebegin_month'] = 0; }
		if (!isset($event['timebegin_day'])) { $event['timebegin_day'] = 0; }
		if (!isset($event['timebegin_year'])) { $event['timebegin_year'] = 0; }

    inputdate($event['timebegin_month'],"event[timebegin_month]",
            $event['timebegin_day'],"event[timebegin_day]",
            $event['timebegin_year'],"event[timebegin_year]");
  }

  if ($repeat['mode'] == 0 || $repeat['mode'] == $recurring) {
    echo "<BR>\n";
    echo "<INPUT type=\"radio\" name=\"repeat[mode]\" id=\"recurringevent\" value=\"$recurring\"";
    if ($repeat['mode']>=1) { echo " checked"; }
    echo ' onClick="this.form.submit()"><label for="recurringevent"> ',lang('recurring_event'),'</label>';
    echo "<BR>\n";
  }
  elseif ($repeat['mode']>=1 && $repeat['mode']<=2) {
    echo "<BR>\n";
    inputrecurrences($event,$repeat,$check);
  }
  echo "<BR>\n";
?>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
    <strong><?php echo lang('time'); ?>:</strong>
<?php
  if ($inputrequired) {
?>
<FONT color="#FF0000">*</FONT>
<?php
  }
?>
    </TD>
    <TD class="bodytext" valign="top">
<?php
  if ($inputrequired && $check && $event['wholedayevent']==0 && $event['timebegin_hour']==0) {
    feedback(lang('specify_all_day_or_starting_time'),1);
  }
?>
      <INPUT type="radio" name="event[wholedayevent]" id="alldayevent" value="1"<?php if ($event['wholedayevent']==1) { echo " checked "; } ?>>
      <label for="alldayevent"><?php echo lang('all_day_event'); ?></label>
      <BR>
   <INPUT type="radio" name="event[wholedayevent]" id="timedevent" value="0"<?php if ($event['wholedayevent']==0) { echo " checked "; } ?>>
   <label for="timedevent"><?php echo lang('timed_event'); ?>:
    <?php echo lang('from'); ?>
    <SELECT name="event[timebegin_hour]" size="1">
<?php
  if ($event['timebegin_hour']==0) {
    echo "<OPTION selected value=\"0\">",$unknownvalue,"</OPTION>\n";
  }
  // print list with hours and select the one read from the DB
   if($use_ampm){
      $start_hour=1;
      $end_hour=12;
   }else{
      $start_hour=0;
      $end_hour=23;
   }
  for ($i=$start_hour; $i<=$end_hour; $i++) {
    echo "<OPTION ";
    if (isset($event['timebegin_hour']) && $event['timebegin_hour']==$i) { echo "selected "; }
    echo "value=\"$i\">$i</OPTION>\n";
  }
?>
      </SELECT>
      <B>:</B>
      <SELECT name="event[timebegin_min]" size="1">
<?php
  // print list with minutes and select the one read from the DB
  for ($i=0; $i<=55; $i+=5) {
    echo "<OPTION ";
    if (isset($event['timebegin_min']) && $event['timebegin_min']==$i) { echo "selected "; }
    if ($i < 10) { $j="0"; } else { $j=""; } // "0","5" to "00", "05"
    echo "value=\"$i\">$j$i</OPTION>\n";
  }
?>
      </SELECT>
<?php 

  if($use_ampm){
 echo '
<SELECT name="event[timebegin_ampm]" size="1">
        <OPTION value="am"';

  if (isset($event['timebegin_ampm']) && $event['timebegin_ampm']=="am") {echo "selected"; }

  echo '    >am</OPTION>
        <OPTION value="pm"';
  if (isset($event['timebegin_ampm']) && $event['timebegin_ampm']=="pm") {echo "selected "; }

      echo ' >pm</OPTION>
      </SELECT>';
}
?>
      <?php echo lang('to'); ?>
      <SELECT name="event[timeend_hour]" size="1">
<?php
  if (!endingtime_specified($event)) {
    $event['timeend_hour']=0;
  }

  echo "<OPTION ";
  if (isset($event['timeend_hour']) && $event['timeend_hour']==0) { echo "selected "; }
  echo "value=\"0\">$unknownvalue</OPTION>\n";
  // print list with hours and select the one read from the DB
   if($use_ampm){
      $start_hour=1;
      $end_hour=12;
   }else{
      $start_hour=0;
      $end_hour=23;
   }
  for ($i=$start_hour; $i<=$end_hour; $i++) {
    echo "<OPTION ";
    if (isset($event['timeend_hour']) && $event['timeend_hour']==$i) { echo "selected "; }
    echo "value=\"$i\">$i</OPTION>\n";
  }
?>
      </SELECT>
      <B>:</B>
      <SELECT name="event[timeend_min]" size="1">
<?php
  // print list with minutes and select the one read from the DB
  for ($i=0; $i<=55; $i+=5) {
    echo "<OPTION ";
    if (isset($event['timeend_min']) && $event['timeend_min']==$i) { echo "selected "; }
    if ($i < 10) { $j="0"; } else { $j=""; } // "0","5" to "00", "05"
    echo "value=\"$i\">$j$i</OPTION>\n";
  }
?>
      </SELECT>
<?php 
if($use_ampm){
   echo'
      <SELECT name="event[timeend_ampm]" size="1">
        <OPTION value="am"';

  if (isset ($event['timeend_ampm']) && $event['timeend_ampm']=="am") {echo "selected "; }

     echo'   >am</OPTION>
        <OPTION value="pm"';
  if (isset($event['timeend_ampm']) && $event['timeend_ampm']=="pm") {echo "selected "; }

echo'  >pm</OPTION>
      </SELECT>';
}
?>
			</label>
      &nbsp;<I><?php echo lang('ending_time_not_required'); ?></I>
      <BR>
    </TD>
  </TR>
<?php
  } /* end: if ($displaydatetime) */
?>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('category'); ?>:</strong>
<?php
  if ($inputrequired) {
?>
<FONT color="#FF0000">*</FONT>
<?php
  }
?>
    </TD>
    <TD class="bodytext" valign="top">
<?php
  if ($inputrequired && $check && ($event['categoryid']==0)) {
    feedback(lang('choose_category'),1);
  }
?>
      <SELECT name="event[categoryid]" size="1">
<?php
  // read event categories from DB
  $result = DBQuery($database, "SELECT * FROM vtcal_category WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' ORDER BY name ASC" ); 

  // print list with categories and select the one read from the DB

  if ($event['categoryid']==0) {
    echo "<OPTION selected value=\"0\">$unknownvalue</OPTION>\n";
  }
  for ($i=0;$i<$result->numRows();$i++) {
    $category = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);

    echo "<OPTION ";
    if (isset($event['categoryid']) && $event['categoryid']==$category['id']) { echo "selected "; }
    echo "value=\"".$category['id']."\">".$category['name']."</OPTION>\n";
  }
?>
      </SELECT>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('title'); ?>:</strong>
<?php
  if ($inputrequired) {
?>
<FONT color="#FF0000">*</FONT>
<?php
  }
?>
    </TD>
    <TD class="bodytext" valign="top">
<?php
  if ($inputrequired && $check && (empty($event['title']))) {
    feedback(lang('choose_title'),1);
  }
?>
      <INPUT type="text" size="24" name="event[title]" maxlength=<?php echo constTitleMaxLength; ?> value="<?php
  if (isset($event['title'])) {
	  if ($check) { $event['title']=stripslashes($event['title']); }
    echo HTMLSpecialChars($event['title']);
	}
?>">
      <I><?php echo lang('title_example'); ?></I><BR>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('description'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
      <TEXTAREA name="event[description]" rows="10" cols="60" wrap=virtual><?php
  if (isset($event['description'])) {
	  if ($check) { $event['description']=stripslashes($event['description']); }
    echo HTMLSpecialChars($event['description']);
	}
?></TEXTAREA>
      <BR>
      <I><?php echo lang('description_example'); ?></I><BR>
      <BR>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('location'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
      <INPUT type="text" size="24" name="event[location]" maxlength=<?php echo constLocationMaxLength; ?> value="<?php
  if (isset($event['location'])) {
    if ($check) { $event['location']=stripslashes($event['location']); }
    echo HTMLSpecialChars($event['location']);
	}
?>"> <I><?php echo lang('location_example'); ?></I><BR>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('price'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
      <INPUT type="text" size="24" name="event[price]" maxlength=<?php echo constPriceMaxLength; ?>  value="<?php
  if (isset($event['price'])) {
    if ($check) { $event['price']=stripslashes($event['price']); }
    echo HTMLSpecialChars($event['price']);
	}
?>"> <I><?php echo lang('price_example'); ?></I><BR>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('contact_name'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
      <INPUT type="text" size="24" name="event[contact_name]" maxlength=<?php echo constContact_nameMaxLength; ?> value="<?php
  if (isset($event['contact_name'])) {
    if ($check) { $event['contact_name']=stripslashes($event['contact_name']); }
    echo HTMLSpecialChars($event['contact_name']);
	}
?>"> <I><?php echo lang('contact_name_example'); ?></I>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('contact_phone'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
      <INPUT type="text" size="24" name="event[contact_phone]" maxlength=<?php echo constContact_phoneMaxLength; ?> value="<?php
  if (isset($event['contact_phone'])) {
    if ($check) { $event['contact_phone']=stripslashes($event['contact_phone']); }
    echo HTMLSpecialChars($event['contact_phone']);
	}
?>"> <I><?php echo lang('contact_phone_example'); ?></I>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
       <strong><?php echo lang('contact_email'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
      <INPUT type="text" size="24" name="event[contact_email]" maxlength=<?php echo constEmailMaxLength; ?> value="<?php
  if (isset($event['contact_email'])) {
    if ($check) { $event['contact_email']=stripslashes($event['contact_email']); }
    echo HTMLSpecialChars(urldecode($event['contact_email']));
	}
?>"> <I><?php echo lang('contact_email_example'); ?></I>
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('event_page_web_address'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
<?php
  if ($check && isset($event['url']) && !checkURL($event['url'])) {
    feedback(lang('url_invalid'),1);
  }
?>
      <INPUT type="text" size="50" name="event[url]" maxlength=<?php echo constUrlMaxLength; ?> value="<?php
  if (isset($event['url'])) {
    if ($check) { $event['url']=stripslashes($event['url']); }
    echo HTMLSpecialChars($event['url']);
	}
?>">
      <BR>
      <I><?php echo lang('event_page_url_example'); ?></I><BR>
    </TD>
  </TR>
  <TR>
    <TD>&nbsp;</TD>
    <TD>&nbsp;</TD>
  </TR>
<?php
  if ($_SESSION["AUTH_ADMIN"]) {
?>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('sponsor'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
      <SELECT name="event[sponsorid]" size="1">
<?php
  // read sponsors from DB
  $result = DBQuery($database, "SELECT * FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' ORDER BY name ASC" ); 

  // print list with sponsors and select the one read from the DB

  for ($i=0;$i<$result->numRows();$i++) {
    $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);

    echo "<OPTION ";
    if ($event['sponsorid']==$sponsor['id']) { echo "selected "; }
    echo "value=\"".$sponsor['id']."\">".$sponsor['name']."</OPTION>\n";
  }
?>
      </SELECT>
      <INPUT type="submit" name="defaultallsponsor" value="<?php echo lang('button_restore_all_sponsor_defaults'); ?>">
    </TD>
  </TR>
<?php
  } // end: if ($_SESSION["AUTH_ADMIN"])
?>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('displayed_sponsor_name'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
      <INPUT type="text" size="50" name="event[displayedsponsor]" maxlength=<?php echo constDisplayedsponsorMaxLength; ?> value="<?php
  if (isset($event['displayedsponsor'])) {
    if ($check) { $event['displayedsponsor']=stripslashes($event['displayedsponsor']); }
    echo HTMLSpecialChars($event['displayedsponsor']);
	}
?>">
      <INPUT type="submit" name="defaultdisplayedsponsor" value="Restore default">
    </TD>
  </TR>
  <TR>
    <TD class="bodytext" valign="top">
      <strong><?php echo lang('sponsor_page_web_address'); ?>:</strong>
    </TD>
    <TD class="bodytext" valign="top">
<?php
  if ($check && isset($event['displayedsponsorurl']) && !checkURL($event['displayedsponsorurl'])) {
    feedback(lang('url_invalid'),1);
  }
?>
      <INPUT type="text" size="50" name="event[displayedsponsorurl]" maxlength=<?php echo constDisplayedsponsorurlMaxLength; ?> value="<?php
  if (isset($event['displayedsponsorurl'])) {
    if ($check) { $event['displayedsponsorurl']=stripslashes($event['displayedsponsorurl']); }
    echo HTMLSpecialChars($event['displayedsponsorurl']);
	}
?>">
      <INPUT type="submit" name="defaultdisplayedsponsorurl" value="<?php echo lang('button_restore_default'); ?>">
      <BR>
    </TD>
  </TR>
<?php
  if ( $_SESSION["CALENDARID"] != "default" ) {
?>
  <TR>
    <TD class="bodytext" valign="top">&nbsp;</TD>
    <TD class="bodytext" valign="top">
		<br>
<?php
  if ($check && !empty($event['showondefaultcal']) && $event['showondefaultcal']==1 && !empty($event['showincategory']) && $event['showincategory']==0) {
    feedback(lang('choose_category'),1);
  }
?>
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
  		<td valign="top">
<?php
  $result = DBQuery($database, "SELECT * FROM vtcal_calendar WHERE id='default'" ); 
  $c = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
  $defaultcalendarname = $c['name'];
?>	
  		  <input type="checkbox" name="event[showondefaultcal]" value="1"<?php 
	  		if ( (isset($event['showondefaultcal']) && $event['showondefaultcal']=="1") ||
				     (!isset($event['showondefaultcal']) && $_SESSION["FORWARDEVENTDEFAULT"]=="1")
				) { echo " checked"; }
        ?>>
			</td>
  		<td valign="top">&nbsp;</td>
  		<td valign="top">
			<?php echo lang('also_display_on'); ?> <?php echo $defaultcalendarname ?><br>
			<?php echo lang('assign_to_category'); ?>: 
      <SELECT name="event[showincategory]" size="1">
<?php
  // read event categories from DB
  $result = DBQuery($database, "SELECT * FROM vtcal_category WHERE calendarid='default' ORDER BY name ASC" );

  // print list with categories and select the one read from the DB
  if ($event['showincategory']==0) {
    echo "<OPTION selected value=\"0\">$unknownvalue</OPTION>\n";
  }
  for ($i=0;$i<$result->numRows();$i++) {
    $category = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);

    echo "<OPTION ";
    if (!empty($event['showincategory']) && $event['showincategory']==$category['id']) { echo "selected "; }
    echo "value=\"".$category['id']."\">".$category['name']."</OPTION>\n";
  }
?>
      </SELECT>
			</td>
		</tr>
		</table>
		</TD>
	</TR>
<?php
  } // end: if ( $_SESSION["CALENDARID"] != "default" )
?>
</TABLE>
<INPUT type="hidden" name="check" value="1">
<?php
  return 1;
} // end of function: "inputeventdata"
?>