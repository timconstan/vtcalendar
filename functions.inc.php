<?php
  $colorpast = $_SESSION["PASTCOLOR"];
  $colortoday  = $_SESSION["TODAYCOLOR"];
  $colorfuture = $_SESSION["FUTURECOLOR"];
  $colorhelpbox = "#FFFFCC";
  $colorinputbox = "#FFFFFF";

  require_once("datecalc.inc.php");
  require_once("header.inc.php");
  require_once("email.inc.php");

  define("REGEXVALIDCOLOR","/^#[ABCDEFabcdef0-9]{6,6}$/");	
  define("BGCOLORNAVBARACTIVE","#993333");
  define("BGCOLORWEEKMONTHNAVBAR","#993333");
  define("BGCOLORDETAILSHEADER","#993333");
  define("MAXLENGTH_URL","100");
  define("MAXLENGTH_TITLE","40");
  define("MAXLENGTH_DESCRIPTION","5000");
  define("MAXLENGTH_LOCATION","100");
  define("MAXLENGTH_PRICE","100");
  define("MAXLENGTH_CONTACT_NAME","100");
  define("MAXLENGTH_CONTACT_PHONE","100");
  define("MAXLENGTH_CONTACT_EMAIL","100");
  define("MAXLENGTH_SPONSOR","50");
  define("FEEDBACKPOS","0");
  define("FEEDBACKNEG","1");

  $nopreview    = 0;
  $showpreview  = 1;

function getNewEventId() {
  $random = rand(0,999);
  $id = time();
  if ($random<100) {
    if ($random<10) {
      $id .= "0";
    }
    $id .= "0";
  }
  return $id.$random;
}

function feedback($msg,$type) {
  echo '<span class="';
	if ($type==0) { echo "feedbackpos"; } // positive feedback
  if ($type==1) { echo "feedbackneg"; } // error message
  echo '">';
	echo $msg;
  echo '</span><br>';
}

function redirect2URL($url) {
  Header("Location: $url");
  return TRUE;
} // end: Function redirect2URL($url)

// display login screen and errormsg (if exists)
function displaylogin($errormsg,$database) {
 
  global $colorinputbox, $lang;

  pageheader(lang('update_page_header'),
             "Login",
            "Update","",$database);
  echo "<BR>\n";
  box_begin("inputbox",lang('login'));

  if (!empty($errormsg)) {
    echo "<BR>\n";
    feedback($errormsg,1);
  }
?>
    <BR>
    <DIV align="center">
    <FORM method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" name="loginform">
<?php
	if (isset($GLOBALS["eventid"])) { echo "<input type=\"hidden\" name=\"eventid\" value=\"",$GLOBALS["eventid"],"\">\n"; }
  if (isset($GLOBALS["httpreferer"])) {  echo "<input type=\"hidden\" name=\"httpreferer\" value=\"",$GLOBALS["httpreferer"],"\">\n"; }
	if (isset($GLOBALS["authsponsorid"])) { echo "<input type=\"hidden\" name=\"authsponsorid\" value=\"",$GLOBALS["authsponsorid"],"\">\n"; }
?>
      <TABLE width="50%" border="0" cellspacing="1" cellpadding="3" align="center">
        <TR>
          <TD class="inputbox" align="right" nowrap><b><?php echo lang('user_id'); ?>:</b></TD>
          <TD align="left"><INPUT type="text" name="login_userid" value=""></TD>
        </TR>
        <TR>
          <TD class="inputbox" align="right"><b><?php echo lang('password'); ?></b></TD>
          <TD align="left"><INPUT type="password" name="login_password" value="" maxlength="<?php echo constPasswordMaxLength; ?>"></TD>
        </TR>
        <TR>
          <TD class="inputbox">&nbsp;</TD>
          <TD align="left"><INPUT type="submit" name="login" value="&nbsp;&nbsp;&nbsp;<?php echo lang('login'); ?>&nbsp;&nbsp;&nbsp;"><br>
    <br>
		<a href="helpsignup.php" target="newWindow"
		onclick="new_window(this.href); return false"><b><?php echo lang('new_user'); ?></b></a>
    <BR>
					
					</TD>
        </TR>
      </TABLE>
      <BR>
		
      
    </FORM>
<script language="JavaScript1.2"><!--
  document.loginform.login_userid.focus();
//--></script>
    </DIV>
    <BR>
<?php
  box_end();
  echo "<BR>\n";

  require("footer.inc.php");
} // end: Function displaylogin

// display login screen
function displaymultiplelogin($database) {
  global $colorinputbox;

  pageheader(lang('login'),
             lang('login'),
            "Update","",$database);
  echo "<BR>\n";
  box_begin("inputbox",lang('choose_sponsor_role'));
?>
<table cellpadding="2" cellspacing="2" border="0">
<?php
	$result = DBQuery($database, "SELECT * FROM vtcal_auth WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND userid='".sqlescape($_SESSION["AUTH_USERID"])."'");
	if ($result->numRows() > 0) {
    for ($i=0;$i<=$result->numRows();$i++) {
      $authorization = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
  
	    // read sponsor name from DB
	    $r = DBQuery($database, "SELECT name FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($authorization['sponsorid'])."'");

      $sponsor = $r->fetchRow(DB_FETCHMODE_ASSOC,0);			
			
			echo "<tr><td>&nbsp;&nbsp;&nbsp;\n";
			echo "<a href=\"".$_SERVER["PHP_SELF"]."?authsponsorid=".$authorization['sponsorid'];
    	if (isset($GLOBALS["eventid"])) { 
			  echo "&eventid=",$GLOBALS["eventid"];
			}
      if (isset($GLOBALS["httpreferer"])) { 
			  echo "&httpreferer=",$GLOBALS["httpreferer"]; 
			}
			echo "\">";
			echo $sponsor['name'];
			echo "</a>";
			echo "</td></tr>\n";
		}
	}
?>
</table>
    <BR>
<?php
    box_end();
  echo "<BR>\n";

  require("footer.inc.php");
} // end: function displaymultiplelogin

function displaynotauthorized($database) {
  global $colorinputbox;

  pageheader(lang('login'),
             lang('login'),
            "Update","",$database);
  echo "<BR>\n";
  box_begin("inputbox",lang('error_not_authorized'));
?>
<?php echo lang('error_not_authorized_message'); ?><br>
<br>
    <a href="helpsignup.php" target="newWindow"	onclick="new_window(this.href); return false"><?php echo lang('help_signup_link'); ?></a><br>
<BR>
<?php
    box_end();
  echo "<BR>\n";

  require("footer.inc.php");
} // end: Function displaynotauthorized


function userauthenticated($database,$userid,$password) {
	if ( AUTH_DB ) {
		$result = DBQuery( $database, "SELECT * FROM vtcal_user WHERE id='".sqlescape($userid)."'" ); 
    if ($result->numRows() > 0) {
			$u = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
			if ( crypt($password,$u['password'])==$u['password'] ) {
			  return true;
			}
		}
	} 
	if ( AUTH_LDAP ) {
    $host = LDAP_HOST;
		$baseDn = LDAP_BASE_DN;
		$userfield = LDAP_USERFIELD;
		$pid = $userid;
		$credential = $password;
    
		/*ldap will bind anonymously, make sure we have some credentials*/
		if (isset($pid) && $pid != '' && isset($credential)) {
			$ldap = ldap_connect($host);
			if (isset($ldap) && $ldap != '') {
				/* search for pid dn */
				$result = @ldap_search($ldap, $baseDn, $userfield.'='.$pid, array('dn'));
				if ($result != 0) {
					$entries = ldap_get_entries($ldap, $result);
					$principal = $entries[0]['dn'];
					if (isset($principal)) {
						/* bind as this user */
						if (@ldap_bind($ldap, $principal, $credential)) {
//							print('Authenticate success');
							return true;
						} 
						else {
//							print('Authenticate failure');
							return false;
						}
					} // end: if (isset($principal))
					else {
//						print('User not found in LDAP');
						return false;
					} // end: else: if (isset($principal))
					ldap_free_result($result);
				} // end: if ($result != 0)
				else {
					print('Error occured searching the LDAP');
					return false;
				}
				ldap_close($ldap);
			} 
			else {
				print('Could not connect to LDAP at '.$host);
				return false;
			}
    } // end: if (isset($pid) && $pid != '' && isset($credential))
		else {
		  return false;
		}
	}
	else {
	  return false;
	}
	
	return false; // default rule
}

function authorized($database) {
  global $authsponsorid;
  if (isset($_POST['login_userid']) && isset($_POST['login_password'])) {
	  $userid = $_POST['login_userid'];
	  $password = $_POST['login_password'];
	  $userid=strtolower($userid);
  }
	$message_loginerror = lang('login_failed'); 

	if ( isset($userid) && preg_match(REGEXVALIDUSERID, $userid) && isset($password) ) {
    // checking authentication of PID/password
		if ( userauthenticated($database,$userid,$password) ) {
			$_SESSION["AUTH_USERID"] = $userid;
		} // end: if ( userauthenticated($userid,$password) )
    else {
		  displaylogin($message_loginerror, $database);
			return false;
    } // end: if (!session_is_registered("AUTH_SPONSORID"))
  } // end: if (isset($userid) && isset($password))

  if ( isset($_SESSION["AUTH_USERID"]) && 
	     isset($authsponsorid) ) {
    // checking authentication of authsponsorid/pid combination
  	$result = DBQuery( $database, "SELECT * FROM vtcal_auth WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND userid='".sqlescape($_SESSION["AUTH_USERID"])."' AND sponsorid='".sqlescape($authsponsorid)."'" );
		if ($result->numRows() > 0) {
			$_SESSION["AUTH_SPONSORID"]= $authsponsorid;
 			$_SESSION["AUTH_SPONSORNAME"] = getSponsorName($database, $authsponsorid);
			
			// determine if the sponsor is administrator for the calendar
		  $_SESSION["AUTH_ADMIN"] = false;
      $result = DBQuery($database, "SELECT admin FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($authsponsorid)."'" );
  		if ($result->numRows() > 0) {
			  $s = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
			  if ( $s["admin"]==1 ) { 
  			  $_SESSION["AUTH_ADMIN"] = true;
	      }			
			}

			// determine if the user is one of the main administrators
		  $_SESSION["AUTH_MAINADMIN"] = false;
      $result = DBQuery($database, "SELECT * FROM vtcal_adminuser WHERE id='".sqlescape($_SESSION["AUTH_USERID"])."'" );
  		if ($result->numRows() > 0) {
			  $a = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
			  if ( $a["id"]==$_SESSION["AUTH_USERID"] ) { 
  			  $_SESSION["AUTH_MAINADMIN"] = true;
	      }			
			}

			return TRUE;
	  }
		else {
			displaymultiplelogin($database);
			return FALSE;
		}
	} // end: if ( isset($_SESSION["AUTH_USERID"]) && isset($authsponsorid) )

  if ( isset($_SESSION["AUTH_USERID"]) && !isset($_SESSION["AUTH_SPONSORID"]) ) {
    // checking authentication of authsponsorid/pid combination
  	$result = DBQuery($database, "SELECT * FROM vtcal_auth WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND userid='".sqlescape($_SESSION["AUTH_USERID"])."'" );
		if ($result->numRows() == 0) { // the user has only access to one sponsor
		  displaynotauthorized($database);
			return false;
		}
		elseif ($result->numRows() == 1) { // the user has only access to one sponsor
		  $authorization = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
			$_SESSION["AUTH_SPONSORID"]= $authorization['sponsorid'];
 			$_SESSION["AUTH_SPONSORNAME"] = getSponsorName($database, $authorization['sponsorid']);

			// determine if the sponsor is administrator for the calendar
		  $_SESSION["AUTH_ADMIN"] = false;
      $result = DBQuery($database, "SELECT admin FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($authorization['sponsorid'])."'" );
  		if ($result->numRows() > 0) {
			  $s = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
			  if ( $s["admin"]==1 ) { 
  			  $_SESSION["AUTH_ADMIN"] = true;
	      }			
			}

			// determine if the user is one of the main administrators
		  $_SESSION["AUTH_MAINADMIN"] = false;
      $result = DBQuery($database, "SELECT * FROM vtcal_adminuser WHERE id='".sqlescape($_SESSION["AUTH_USERID"])."'" );
  		if ($result->numRows() > 0) {
			  $a = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
			  if ( $a["id"]==$_SESSION["AUTH_USERID"] ) { 
  			  $_SESSION["AUTH_MAINADMIN"] = true;
	      }			
			}
			
			return true;
		}
		else {
  		displaymultiplelogin($database);
	  	return false;	
		}
	}

  if ( isset($_SESSION["AUTH_USERID"]) && 
	     isset($_SESSION["AUTH_SPONSORID"]) && 
			 $_SESSION["AUTH_SPONSORNAME"] ) {
    return true;
	}
	else {
	  displaylogin("",$database);
		return false;
	}
} // end: Function authorized()

function viewauthorized($database) {
  $authok = 0;
  if (isset($_POST['login_userid']) && isset($_POST['login_password'])) {
	  $userid = $_POST['login_userid'];
	  $password = $_POST['login_password'];
	  $userid=strtolower($userid);
  }
	if ( $_SESSION["VIEWAUTHREQUIRED"] == 0 ) {
	  $authok = 1;
	}
	elseif (isset($userid) && isset($password)) { // verify userid/password
    $userid=strtolower($userid);

    // checking authentication
		if ( userauthenticated($database,$userid,$password) ) {
			// checking authorization
			$result = DBQuery($database, "SELECT * FROM vtcal_calendarviewauth WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND userid='".sqlescape($userid)."'" );
			if ($result->numRows() > 0) {
  			$_SESSION["AUTH_USERID"] = $userid;
				$_SESSION["CALENDAR_LOGIN"] = $_SESSION["CALENDARID"];
				$authok = 1;
			}
		} // end: if ( userauthenticated($userid,$password) )
    
    if (!$authok) { // display error message
      displaylogin("Error! Your login failed. Please try again.",$database);
    }

  } // end: if (isset($userid) && isset($password))
  elseif ( isset($_SESSION["AUTH_USERID"]) && !empty($_SESSION["AUTH_USERID"]) ) {
		$authok = 1;
	}
	else {
		$protocol = "http";
		$path = substr($_SERVER["PHP_SELF"],0,strrpos($_SERVER["PHP_SELF"],"/")+1);
		$page = substr($_SERVER["PHP_SELF"],strrpos($_SERVER["PHP_SELF"],"/")+1);
		if ( isset($_SERVER['HTTPS'])) { $protocol .= "s"; }
		if ( BASEURL != SECUREBASEURL && 
		    $protocol."://".$_SERVER["HTTP_HOST"].$path != SECUREBASEURL ) {
			redirect2URL(SECUREBASEURL.$page."?calendar=".$_SESSION["CALENDARID"]);
		}
    displaylogin("",$database);
  }
  
  return $authok;
} // end: function viewauthorized()

function getSponsorName ($database, $sponsorid) {
	$result = DBQuery($database, "SELECT name FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($sponsorid)."'" );
  if ($result->numRows() > 0) {
    $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
    return $sponsor['name'];
	}
	else {
	  return "";
	}
}

// returns true if a particular userid exists in the database
function userExistsInDB($database, $userid) {
  if ( AUTH_DB ) {
  	$query = "SELECT count(id) FROM vtcal_user WHERE id='".sqlescape($userid)."'";
    $result = DBQuery($database, $query ); 
    $r = $result->fetchRow(0);
    if ($r[0]>0) { return true; }
	}
	
  return false; // default rule
}

// returns true if the user-id is valid
function isValidUser($database, $userid) {
  if ( AUTH_DB ) {
  	$query = "SELECT count(id) FROM vtcal_user WHERE id='".sqlescape($userid)."'";
    $result = DBQuery($database, $query ); 
    $r = $result->fetchRow(0);
    if ($r[0]>0) { return true; }
	}
	
	if ( AUTH_LDAP ) {
	  // in the future have some code that checks against LDAP
	  return preg_match(REGEXVALIDUSERID, $userid);
	}

  return false; // default rule
}

// determines a background color according to the day
function datetocolor($month,$day,$year,$colorpast,$colortoday,$colorfuture) {
  $datediff = Delta_Days($month,$day,$year,date("m"),date("d"),date("Y"));

  if ($datediff > 0) {
    $color=$colorpast;
  }
  elseif ($datediff < 0) {
    $color=$colorfuture;
  }
  else {
    $color=$colortoday;
  }

  return $color;
}

// determines the CSS class (past, today, future) according to the day
function datetoclass($month,$day,$year) {
  $datediff = Delta_Days($month,$day,$year,date("m"),date("d"),date("Y"));

  if ($datediff > 0) {
    $class="past";
  }
  elseif ($datediff < 0) {
    $class="future";
  }
  else {
    $class="today";
  }

  return $class;
}

function checkURL($url) {
  return
    (empty($url) || 
		 strtolower(substr($url,0,7))=="http://" ||
		 strtolower(substr($url,0,8))=="https://"
		 );
}

function checkemail($email) {
  return
    ((!empty($email)) && (eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$",$email)));
}

function checknewpassword(&$user) {
  /* include more sophisticated constraints here */
  if ($user['newpassword1']!=$user['newpassword2']) { return 1; }
  elseif ((empty($user['newpassword1'])) || (strlen($user['newpassword1']) < 5)) { return 2; }
  else { return 0; }
}

function checkoldpassword(&$user,$userid,$database) {
  $result = DBQuery($database, "SELECT * FROM vtcal_user WHERE id='".sqlescape($userid)."'" ); 
  $data = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
	return
    ($data['password']!=crypt($user['oldpassword'],$data['password']));
}

function printeventdate(&$event) {
  $event_timebegin_month = $event['timebegin_month'];
  if (strlen($event_timebegin_month) == 1) { $event_timebegin_month = "0".$event_timebegin_month; }
  $event_timebegin_day   = $event['timebegin_day'];
  if (strlen($event_timebegin_day) == 1) { $event_timebegin_day = "0".$event_timebegin_day; }

  return $event_timebegin_month.'/'.$event_timebegin_day.'/'.$event['timebegin_year'];
}

function printeventtime(&$event) {
  $event_timebegin_hour = $event['timebegin_hour'];
  if (strlen($event_timebegin_hour) == 1) { $event_timebegin_hour = "0".$event_timebegin_hour; }
  $event_timebegin_min = $event['timebegin_min'];
  if (strlen($event_timebegin_min) == 1) { $event_timebegin_min = "0".$event_timebegin_min; }
  $event_timeend_hour = $event['timeend_hour'];
  if (strlen($event_timeend_hour) == 1) { $event_timeend_hour = "0".$event_timeend_hour; }
  $event_timeend_min = $event['timeend_min'];
  if (strlen($event_timeend_min) == 1) { $event_timeend_min = "0".$event_timeend_min; }

  return $event_timebegin_hour.':'.$event_timebegin_min.$event['timebegin_ampm'].'-'.$event_timeend_hour.':'.$event_timeend_min.$event['timeend_ampm'];
}

/* converts a year/month-pair to a timestamp in the format "1999-09" */
function yearmonth2timestamp($year,$month) {
  $timestamp="$year-";
  if (strlen($month)==1) { $timestamp.="0"; }
  $timestamp.="$month";

  return $timestamp;
}

// converts a year/month/day-pair to a timestamp in the format "1999-09-17"
function yearmonthday2timestamp($year,$month,$day) {
  $timestamp="$year-";
  if (strlen($month)==1) { $timestamp.="0"; }
  $timestamp.="$month";
  if (strlen($day)==1) { $timestamp.="0"; }
  $timestamp.="$day";

  return $timestamp;
}

/* converts a date/time to a timestamp in the format "1999-09-16 18:57:00" */
function datetime2timestamp($year,$month,$day,$hour,$min,$ampm) {
  global $use_ampm;
  $timestamp="$year-";
  if (strlen($month)==1) { $timestamp.="0$month-"; } else { $timestamp.="$month-"; }
  if (strlen($day)==1) { $timestamp.="0$day "; } else { $timestamp.="$day "; }
  if($use_ampm){  // if am, pm format is used
	 if (($ampm=="pm") && ($hour!=12)) { $hour+=12; }; /* 12pm is noon */
     if (($ampm=="am") && ($hour==12)) { $hour=0; }; /* 12am is midnight */
  }
  if (strlen($hour)==1) { $timestamp.="0$hour:"; } else { $timestamp.="$hour:"; }
  if (strlen($min)==1) { $timestamp.="0$min:00"; } else { $timestamp.="$min:00"; }

  return $timestamp;
}

/* converts a timestamp "1999-09-16 18:57:00" to a date/time format */
function timestamp2datetime($timestamp) {
   global $use_ampm;
  /* split the date/time field-info into its parts */
  /* format returned by postgres is "1999-09-10 07:30:00" */
  $datetime['year']  = substr($timestamp,0,4);
  $datetime['month'] = substr($timestamp,5,2);
  if (substr($datetime['month'],0,1)=="0") { /* remove leading "0" */
    $datetime['month'] = substr($datetime['month'],1,1);
  }
  $datetime['day']   = substr($timestamp,8,2);
  if (substr($datetime['day'],0,1)=="0") { /* remove leading "0" */
    $datetime['day'] = substr($datetime['day'],1,1);
  }

  $datetime['hour']  = substr($timestamp,11,2);

  /* convert 24 hour into 1-12am/pm  if am, pm in data format is used*/
  if($use_ampm){  
     $datetime['ampm'] = "pm";
     if ($datetime['hour'] < 12) {
       if ($datetime['hour'] == 0) { $datetime['hour'] = 12; }
       $datetime['ampm'] = "am";
     } else {
       if ($datetime['hour'] > 12) { $datetime['hour'] -= 12; }
     }
  }
  
  if (substr($datetime['hour'],0,1)=="0") { /* remove leading "0" */
    $datetime['hour'] = substr($datetime['hour'],1,1);
  }
  $datetime['min']=substr($timestamp,14,2);

  return $datetime;
}
// returns the date&time in the ISO8601format: 20000211T235900 (used by vCalendar)
function datetime2ISO8601datetime($year,$month,$day,$hour,$min,$ampm) {
  $datetime = strtr(datetime2timestamp($year,$month,$day,$hour,$min,$ampm)," ","T");
  $datetime = str_replace("-","",$datetime);
  $datetime = str_replace(":","",$datetime);

  return $datetime;
}

// converts a vCalendar timestamp "20000211T235900" to a date/time format
function ISO8601datetime2datetime($ISO8601datetime) {
  $datetime['year']  = substr($ISO8601datetime,0,4);
  $datetime['month'] = substr($ISO8601datetime,4,2);
  if (substr($datetime['month'],0,1)=="0") { // remove leading "0"
    $datetime['month'] = substr($datetime['month'],1,1);
  }
  $datetime['day']   = substr($ISO8601datetime,6,2);
  if (substr($datetime['day'],0,1)=="0") { // remove leading "0"
    $datetime['day'] = substr($datetime['day'],1,1);
  }

  $datetime['hour']  = substr($ISO8601datetime,9,2);

  // convert 24 hour into 1-12am/pm
  $datetime['ampm'] = "pm";
  if ($datetime['hour'] < 12) {
    if ($datetime['hour'] == 0) { $datetime['hour'] = 12; }
    $datetime['ampm'] = "am";
  } else {
    if ($datetime['hour'] > 12) { $datetime['hour'] -= 12; }
  }
  if (substr($datetime['hour'],0,1)=="0") { // remove leading "0"
    $datetime['hour'] = substr($datetime['hour'],1,1);
  }
  $datetime['min']=substr($ISO8601datetime,11,2);

  return $datetime;
}

/* construct event_timbegin(timeend)_month/day/year/hour/min/ampm from timestamp */
function disassemble_eventtime(&$event) {
  $timebegin = timestamp2datetime($event['timebegin']);
  $event['timebegin_year']  = $timebegin['year'];
  $event['timebegin_month'] = $timebegin['month'];
  $event['timebegin_day']   = $timebegin['day'];
  $event['timebegin_hour']  = $timebegin['hour'];
  $event['timebegin_min']   = $timebegin['min'];
  $event['timebegin_ampm']  = $timebegin['ampm'];

  $timeend = timestamp2datetime($event['timeend']);
  $event['timeend_year']  = $timeend['year'];
  $event['timeend_month'] = $timeend['month'];
  $event['timeend_day']   = $timeend['day'];
  $event['timeend_hour']  = $timeend['hour'];
  $event['timeend_min']   = $timeend['min'];
  $event['timeend_ampm']  = $timeend['ampm'];

  return 0;
}

// for non-recurring events the ending time equals the starting time
function settimeenddate2timebegindate(&$event) {
  $event['timeend_year'] = $event['timebegin_year'];
  $event['timeend_month'] = $event['timebegin_month'];
  $event['timeend_day'] = $event['timebegin_day'];
}

// construct event timestamps "timebegin&timeend" from month/day/year/hour/min/ampm
function assemble_eventtime(&$event) {
  global $day_beg_h, $day_end_h, $use_ampm;
  $event['timebegin'] = datetime2timestamp(
                        $event['timebegin_year'],
                        $event['timebegin_month'],
                        $event['timebegin_day'],
                        $event['timebegin_hour'],
                        $event['timebegin_min'],
                        $event['timebegin_ampm']);

  // if event doesn't have an ending time, set it to the end of the day
  if ($event['timeend_hour']==0) {
    $event['timeend_hour']=$day_end_h;
    $event['timeend_min']=59;
    if($use_ampm)
       $event['timeend_ampm']="pm";
  }

  $event['timeend'] =   datetime2timestamp(
                        $event['timeend_year'],
                        $event['timeend_month'],
                        $event['timeend_day'],
                        $event['timeend_hour'],
                        $event['timeend_min'],
                        $event['timeend_ampm']);
  return 0;
}

// prints out the HTML code a box begins with, use box_end to finish the box
function box_begin($class, $headertext) {
?>
<TABLE border="0" cellPadding="7" cellSpacing="0">
  <TR>
    <TD bgcolor="<?php echo $_SESSION["BGCOLOR"]; ?>">&nbsp;</td>
    <TD bgcolor="#eeeeee">
<?php

  if (!empty($headertext)) {
    echo "<h3>".$headertext."</h3>";
	}
	
} // end: function box_begin()

// prints out the HTML code a box ends with, use box_begin to begin the box
function box_end() {
?>
    </TD>
  </TR>
</TABLE>
<?php
} // end: function box_end()

// prints out the HTML code a helpbox begins with, use helpbox_end to finish the helpbox
function helpbox_begin() {
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title><?php echo lang('help'); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
    <meta content="en-us" http-equiv=language>
    <link href="stylesheet.php" rel="stylesheet" type="text/css">
  </head>
  <body bgcolor="<?php echo $_SESSION["BGCOLOR"]; ?>" leftMargin="0" topMargin="0" marginheight="0" marginwidth="0" onLoad="this.window.focus()">
		<br>
		<table border="0" cellPadding="5" cellSpacing="0">
			<tr>
				<td bgcolor="<?php echo $_SESSION["BGCOLOR"]; ?>">&nbsp;</td>
				<td bgcolor="#eeeeee">  
<?php
} // end: function helpbox_begin

// prints out the HTML code a helpbox ends with, use helpbox_begin to begin the helpbox
function helpbox_end() {
?>
				<td bgcolor="<?php echo $_SESSION["BGCOLOR"]; ?>">&nbsp;</td>
			</tr>
		</table>
		<br>
  </body>
</html>
<?php
} // end: function helpbox_end()

// returns a string like "5:00pm" from the input "5", "0", "pm"
function timestring($hour,$min,$ampm) {
  if (strlen($min)==1) { $min = "0".$min; }

  return $hour.":".$min.$ampm;
} // function timestring($hour,$min,$ampm)

// returns true if the ending time is not 11:59pm (meaning: not specified)
function endingtime_specified(&$event) {
  return !($event['timeend_hour']==11 &&
          $event['timeend_min']==59 &&
          $event['timeend_ampm']=="pm");
}

// prints one event in the format of the week view
function print_week_event(&$event,$preview) {
  disassemble_eventtime($event);

  if ($event['wholedayevent']==0) {
    echo '<span class="eventtime">';
		echo timestring($event['timebegin_hour'],$event['timebegin_min'],$event['timebegin_ampm']);
    echo '</span>';
		echo "<br>";
  }

  if ($preview!=1) {
    echo "<a href=\"main.php?view=event&eventid=",$event['eventid'],"\">";
  }
  echo "<b>",$event['title'],"</b><br>";
  if ($preview!=1) {
    echo "</a>";
  echo '<span class="eventcategory">'.$event['category_name'].'</span>';

    // add little update, delete icons
    if ((isset($_SESSION["AUTH_SPONSORID"]) && $_SESSION["AUTH_SPONSORID"] == $event['sponsorid']) || 
         !empty($_SESSION["AUTH_ADMIN"]) ) {
      echo "<br><a href=\"changeeinfo.php?eventid=",$event['eventid'],"\" title=\"",lang('update_event'),"\">";
      echo "<img src=\"images/nuvola/16x16/actions/color_line.png\" height=\"16\" width=\"16\" alt=\"",lang('update_event'),"\" border=\"0\"></a>";

      echo " <a href=\"changeeinfo.php?copy=1&eventid=",$event['eventid'],"\" title=\"",lang('copy_event'),"\">";
      echo "<img src=\"images/nuvola/16x16/actions/editcopy.png\" height=\"16\" width=\"16\" alt=\"",lang('copy_event'),"\" border=\"0\"></a>";

      echo " <a href=\"deleteevent.php?eventid=",$event['eventid'],"&check=1\" title=\"",lang('delete_event'),"\">";
      echo "<img src=\"images/nuvola/16x16/actions/button_cancel.png\" height=\"16\" width=\"16\" alt=\"",lang('delete_event'),"\" border=\"0\"></a>";
    }
  };
  echo "<BR><BR>\n";
} // end: function print_week_event(&$event)

// remove slashes from event fields
function removeslashes(&$event) {
  $event['title']=stripslashes($event['title']);
  $event['description']=stripslashes($event['description']);
  $event['location']=stripslashes($event['location']);
  $event['price']=stripslashes($event['price']);
  $event['contact_name']=stripslashes($event['contact_name']);
  $event['contact_phone']=stripslashes($event['contact_phone']);
  $event['contact_email']=stripslashes($event['contact_email']);
  $event['url']=stripslashes($event['url']);
  $event['displayedsponsor']=stripslashes($event['displayedsponsor']);
  $event['displayedsponsorurl']=stripslashes($event['displayedsponsorurl']);
} // end: function removeslashes

// display input fields for a date (month, day, year)
function inputdate($month,$monthvar,$day,$dayvar,$year,$yearvar) {
  $unknownvalue = "???"; // this is printed when the value of input field is unspecified
  echo "<SELECT name=\"",$monthvar,"\" id=\"",$monthvar,"\">\n";

  //if ($month==0) {
  //  echo "<OPTION selected value=\"0\">",$unknownvalue,"</OPTION>\n";
  //}
  /* print list with months and select the one read from the DB */
  for ($i=1; $i<=12; $i++) {
    print '<OPTION ';
    if ($month==$i) { echo "selected "; }
    if (date("n")==$i && $month==0) { echo "selected "; }
    echo "value=\"$i\">",Month_to_Text($i),"</OPTION>\n";
  }
  echo "</SELECT>\n";
  echo "<SELECT name=\"",$dayvar,"\" id=\"",$dayvar,"\">\n";

  // if ($day==0) {
  // echo "<OPTION selected value=\"0\">",$unknownvalue,"</OPTION>\n";
  //}

  // print list with days and select the one read from the DB
  for ($i=1;$i<=31;$i++) {
    echo "<OPTION ";
    if ($day==$i) { echo "selected "; }
    if (date("j")==$i && $day==0) { echo "selected "; }
    echo "value=\"",$i,"\">",$i,"</OPTION>\n";
  }
  echo "</SELECT>\n";
  echo "<SELECT name=\"",$yearvar,"\" id=\"",$yearvar,"\">\n";

  // print list with years and select the one read from the DB
  if (!empty($year) && $year < date("Y")) { echo "<OPTION selected value=\"",$year,"\">",$year,"</OPTION>\n"; }
  for ($i=date("Y");$i<=date("Y")+3;$i++) {
    echo "<OPTION ";
    if ($year==$i) { echo "selected "; }
    echo "value=\"",$i,"\">",$i,"</OPTION>\n";
  }
  echo "</SELECT>\n";
  
  if (!isset($GLOBALS['popupCalendarJavascriptIsLoaded'])) {
     $calendarLanguageFile = 'scripts/jscalendar/lang/calendar-'.LANGUAGE.'.js';
	 if (!file_exists($calendarLanguageFile)) {
	     $calendarLanguageFile = 'scripts/jscalendar/lang/calendar-en.js';
	 }
	 echo '
  <link rel="stylesheet" type="text/css" media="all" href="scripts/jscalendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />
  <script type="text/javascript" src="scripts/jscalendar/calendar.js"></script>
  <script type="text/javascript" src="',$calendarLanguageFile,'"></script>
  <script type="text/javascript" src="scripts/jscalendar/calendar-setup.js"></script>
';
	  $GLOBALS['popupCalendarJavascriptIsLoaded'] = TRUE;
  }
  
  $uniqueid = strtr($monthvar, '[]','__');
  
  $firstDay = WEEK_STARTING_DAY;
  echo <<<END
<input type="hidden" name="popupCalendarDate" id="popupCalendarDate_$uniqueid" value="$month/$day/$year">
<img src="images/nuvola/16x16/apps/date.png" width="16" height="16" id="showPopupCalendarImage_$uniqueid" 
title="Date selector" border="0" align="baseline" style="cursor: pointer;" hspace="3">
<script type="text/javascript"><!--
function onSelectDate(cal) {
  var p = cal.params;
  if (cal.dateClicked) {
	  cal.callCloseHandler();
	  var month = document.getElementById("$monthvar");
	  monthPerhapsWithLeadingZero = cal.date.print("%m");
	  if (monthPerhapsWithLeadingZero.charAt(0) == "0") {
	  	month.value = monthPerhapsWithLeadingZero.substr(1);
	  }
	  else {
	    month.value = monthPerhapsWithLeadingZero;
	  }
	  var date = document.getElementById("$dayvar");
	  date.value = cal.date.print("%e");
	  var year = document.getElementById("$yearvar");
	  year.value = cal.date.print("%Y");
	  
	  document.getElementById("popupCalendarDate_$uniqueid").value = cal.date.print("%m/%e/%Y");
  }
};

Calendar.setup({
	inputField     :    "popupCalendarDate_$uniqueid",     // id of the input field
	ifFormat       :    "%m/%e/%Y",      // format of the input field
	button         :    "showPopupCalendarImage_$uniqueid",  // trigger for the calendar (button ID)
	align          :    "br",           // alignment (defaults to "Bl")
	weekNumbers    :    false,
	firstDay       :    $firstDay,
	onSelect       :    onSelectDate
});
//--></script>

END;
} // end: function inputdate

function readinrepeat($repeatid,&$event,&$repeat,$database) {
  $query = "SELECT * FROM vtcal_event_repeat WHERE id = '".sqlescape($repeatid)."'";
	$result = DBQuery($database, $query ); 
  $r = $result->fetchRow(DB_FETCHMODE_ASSOC,0);

  repeatdef2repeatinput($r['repeatdef'],$event,$repeat);

  $startdate = timestamp2datetime($r['startdate']);
  $event['timebegin_year']  = $startdate['year'];
  $event['timebegin_month'] = $startdate['month'];
  $event['timebegin_day']   = $startdate['day'];
}

// takes the values from the inputfields on the form and constructs a
// repeat-definition string in vCalendar format, e.g. "MP2 3+ TH 20000211T235900"
function repeatinput2repeatdef(&$event,&$repeat) {
  if ($repeat['mode'] == 1) {
     if ($repeat['interval1']=="every") { $interval = "1"; }
     if ($repeat['interval1']=="everyother") { $interval = "2"; }
     if ($repeat['interval1']=="everythird") { $interval = "3"; }
     if ($repeat['interval1']=="everyfourth") { $interval = "4"; }

     if ($repeat['frequency1']=="day") { $frequency = "D"; }
     if ($repeat['frequency1']=="week") { $frequency = "W"; }
     if ($repeat['frequency1']=="month") { $frequency = "M"; }
     if ($repeat['frequency1']=="year") { $frequency = "YD"; }
     if ($repeat['frequency1']=="monwedfri") { $frequency = "W"; $frequencymodifier="MO WE FR"; }
     if ($repeat['frequency1']=="tuethu") { $frequency = "W"; $frequencymodifier="TU TH"; }
     if ($repeat['frequency1']=="montuewedthufri") { $frequency = "W"; $frequencymodifier="MO TU WE TH FR"; }
     if ($repeat['frequency1']=="satsun") { $frequency = "W"; $frequencymodifier="SA SU"; }
  }
  elseif ($repeat['mode'] == 2) {
     $frequency = "MP";

     if ($repeat['frequency2modifier1']=="first") { $frequencymodifier = "1+"; }
     if ($repeat['frequency2modifier1']=="second") { $frequencymodifier = "2+"; }
     if ($repeat['frequency2modifier1']=="third") { $frequencymodifier = "3+"; }
     if ($repeat['frequency2modifier1']=="fourth") { $frequencymodifier = "4+"; }
     if ($repeat['frequency2modifier1']=="last") { $frequencymodifier = "1-"; }

     if ($repeat['frequency2modifier2']=="sun") { $frequencymodifier.=" SU"; }
     if ($repeat['frequency2modifier2']=="mon") { $frequencymodifier.=" MO"; }
     if ($repeat['frequency2modifier2']=="tue") { $frequencymodifier.=" TU"; }
     if ($repeat['frequency2modifier2']=="wed") { $frequencymodifier.=" WE"; }
     if ($repeat['frequency2modifier2']=="thu") { $frequencymodifier.=" TH"; }
     if ($repeat['frequency2modifier2']=="fri") { $frequencymodifier.=" FR"; }
     if ($repeat['frequency2modifier2']=="sat") { $frequencymodifier.=" SA"; }

     if ($repeat['interval2']=="month") { $interval="1"; }
     if ($repeat['interval2']=="2months") { $interval="2"; }
     if ($repeat['interval2']=="3months") { $interval="3"; }
     if ($repeat['interval2']=="4months") { $interval="4"; }
     if ($repeat['interval2']=="6months") { $interval="6"; }
     if ($repeat['interval2']=="year") { $interval="12"; }
  } // end: elseif ($repeat[mode] == 2)

  // construct a repeat definition using the vCalendar standard
  $repeatdef = $frequency.$interval." ";
  if (!empty($frequencymodifier)) { $repeatdef.=$frequencymodifier." "; }
  $repeatdef .= datetime2ISO8601datetime($event['timeend_year'],
                                         $event['timeend_month'],
                                         $event['timeend_day'],
                                         11,59,"pm");
  return $repeatdef;
} // end: function repeatinput2repeatdef(&$event,&$repeat)

// separate the string at the first space
function getfirstslice($s) {
  $spacepos = strpos($s," ");
  if ($spacepos==0) {
    $part1 = $s;
    $part2 = "";
  }
  else {
    $part1 = substr($s,0,$spacepos);
    $part2 = substr($s,$spacepos+1,strlen($s)-$spacepos-1);
  }

  return array($part1, $part2);
}

// splits a vcalendar-style repeatdef string like "MP2 3+ TH 20000211T235900" into
// its parts "frequency","interval","frequencymodifier" and enddatetime (year,month,day,hour,min,ampm)
// Attention!: it does not implement the whole vCalendar recurrence grammar, but rather
//             the subset used by the VTEC interface
function repeatdefdisassemble($repeatdef,
                              &$frequency,&$interval,&$frequencymodifier,
                              &$endyear,&$endmonth,&$endday) {
  $frequencymodifier = "";
  list($frequencyinterval,$remainder) = getfirstslice($repeatdef);

  if (substr($frequencyinterval,0,2)=="MP") {  // it's of the format: "MP2 3+ TH 19991224T135000"
    $frequency = "MP";
    $interval = substr($frequencyinterval,2,strlen($frequencyinterval)-2);
    list($frequencymodifier1,$frequencymodifier2,$enddatetimeISO8601) = explode(" ",$remainder);

    $frequencymodifier = $frequencymodifier1." ".$frequencymodifier2;
    $enddatetime = ISO8601datetime2datetime($enddatetimeISO8601);
  }
  elseif (substr($frequencyinterval,0,2)=="YD") {
    $frequency = "YD";
    $interval = $frequencyinterval[2];
    $enddatetime = ISO8601datetime2datetime($remainder);
  }
  elseif ($frequencyinterval[0]=="D" || $frequencyinterval[0]=="M") {
    $frequency = $frequencyinterval[0];
    $interval = $frequencyinterval[1];
    $enddatetime = ISO8601datetime2datetime($remainder);
  }
  elseif ($frequencyinterval[0]=="W") {
    $frequency = $frequencyinterval[0];
    $interval = $frequencyinterval[1];

    // parse the string and add all but the last component (which is the date) to the "modifier"
    do {
      list($part,$newremainder) = getfirstslice($remainder);

      if (!empty($newremainder)) {
       if (!empty($frequencymodifier)) { $frequencymodifier.=" "; }
       $frequencymodifier.=$part;
       $remainder = $newremainder;
      }
      else {
       $enddatetime = ISO8601datetime2datetime($part);
      }
    } while (!empty($newremainder));
  }

  $endyear = $enddatetime['year'];
  $endmonth = $enddatetime['month'];
  $endday = $enddatetime['day'];

  return 1;
} // end: Function repeatdefdisassemble

// prints the definition for a recurring event
function printrecurrence($startyear,$startmonth,$startday,
                         $repeatdef) {
  if (!empty($repeatdef)) {
    repeatdefdisassemble($repeatdef,
                         $frequency,$interval,$frequencymodifier,
                         $endyear,$endmonth,$endday);
    echo lang('recurring')," ";
    if ($frequency=="MP") {
      list($frequencymodifiernumber,$frequencymodifierday) = getfirstslice($frequencymodifier);
      echo lang('on_the'),' ';

      if ($frequencymodifiernumber[1]=="-") { echo lang('last'); }
      else {
	if ($frequencymodifiernumber=="1+") { echo lang('first'); }
        elseif ($frequencymodifiernumber=="2+") { lang('second'); }
        elseif ($frequencymodifiernumber=="3+") { lang('third'); }
        elseif ($frequencymodifiernumber=="4+") { lang('fourth'); }
      }
      echo " ";
      if ($frequencymodifierday=="SU") { echo lang('sunday'); }
      elseif ($frequencymodifierday=="MO") { echo lang('monday'); }
      elseif ($frequencymodifierday=="TU") { echo lang('tuesday'); }
      elseif ($frequencymodifierday=="WE") { echo lang('wednesday'); }
      elseif ($frequencymodifierday=="TH") { echo lang('thursday'); }
      elseif ($frequencymodifierday=="FR") { echo lang('friday'); }
      elseif ($frequencymodifierday=="SA") { echo lang('saturday'); }

      echo ' ',lang('of_the_month_every'),' ';

      if ($interval==1) { echo lang("month"); }
      elseif ($interval==2) { echo lang("other_month"); }
      elseif ($interval>=3 && $interval<=6) { echo $interval,' ',lang('months'); }
      elseif ($interval==12) { echo lang("year"); }

    } // end: if ($frequency=="MP")
    else {
      if ($interval==1) { echo lang("every"); }
      elseif ($interval==2) { echo lang("every_other"); }
      elseif ($interval==3) { echo lang("every_third"); }
      elseif ($interval==4) { echo lang("every_fourth"); }
      echo ' ';

      if ($frequency=="D") { echo lang("day"); }
      elseif ($frequency=="M") { echo lang("month"); }
      elseif ($frequency=="Y") { echo lang("year"); }
      elseif ($frequency=="W") {
        echo " ";
        if (empty($frequencymodifier)) { echo lang("week"); }
        else {
          $frequencymodifier = " ".$frequencymodifier;

	  $comma = 0;
	  if (strpos($frequencymodifier,"SU")!=0) {
	    if ($comma) { echo ", "; }
	    echo lang("sunday");
            $comma=1;
	  }
	  if (strpos($frequencymodifier,"MO")!=0) {
	    if ($comma) { echo ", "; }
	    echo lang("monday");
            $comma=1;
	  }
	  if (strpos($frequencymodifier,"TU")!=0) {
	    if ($comma) { echo ", "; }
	    echo lang("tuesday");
            $comma=1;
	  }
	  if (strpos($frequencymodifier,"WE")!=0) {
	    if ($comma) { echo ", "; }
	    echo lang("wednesday");
            $comma=1;
	  }
	  if (strpos($frequencymodifier,"TH")!=0) {
	    if ($comma) { echo ", "; }
	    echo lang("thursday");
            $comma=1;
	  }
	  if (strpos($frequencymodifier,"FR")!=0) {
	    if ($comma) { echo ", "; }
	    echo lang("friday");
            $comma=1;
	  }
	  if (strpos($frequencymodifier,"SA")!=0) {
	    if ($comma) { echo ", "; }
	    echo lang("saturday");
            $comma=1;
	  }
	} // end: else: if (empty($frequencymodifier))
      } // end: elseif ($frequency=="W")
    } // end: else: if ($frequency=="MP")

    echo '; ',lang('starting'),' ',Encode_Date_US($startmonth,$startday,$startyear);
    echo '; ',lang('ending'),' ',Encode_Date_US($endmonth,$endday,$endyear);

  } // end: if (!empty($repeatdef))
  else {
    echo lang('no_recurrences_defined');
  }
} // end: function printrecurrence

// transform a startdate and a repeat-definition in the vCalendar format,
// e.g. "MP2 3+ TH 20000211T235900" into an array of single dates
function repeatdefdisassembled2repeatlist($startyear,$startmonth,$startday,
                                          $frequency,
                                          $interval,
                                          $frequencymodifier,
                                          $endyear, $endmonth, $endday) {

  $repeatlist = array();
  $startdateJD = JulianToJD($startmonth,$startday,$startyear);
  $enddateJD = JulianToJD($endmonth,$endday,$endyear);
  $ecount = 0;

  if ($frequency=="D") { // recurring daily
    $dateJD = $startdateJD + $ecount * $interval;
    while ($dateJD <= $enddateJD) {
      $repeatlist[$ecount]=$dateJD; // store this date in the list (array)
      $ecount++;
      $dateJD = $startdateJD + $ecount * $interval;
    }
  }
  elseif ($frequency=="M") { // recurring same date monthly
    $enddate = yearmonthday2timestamp($endyear,$endmonth,$endday);
    $year = $startyear;
    $month = $startmonth;
    $date=yearmonthday2timestamp($year,$month,$startday);
    while ($date <= $enddate) {
      // check if it is a valid date and not for example Feb, 30th,...
      if (checkdate($month,$startday,$year)) {
        $dateJD = JulianToJD($month,$startday,$year);
        $repeatlist[$ecount]=$dateJD; // store this date in the list (array)
        $ecount++;
      }
      $month+=$interval;
      if ($month>12) { $month -= 12; $year++; }
      $date=yearmonthday2timestamp($year,$month,$startday);
    }
  }
  elseif ($frequency=="YD") { // recurring same date yearly
    $enddate = yearmonthday2timestamp($endyear,$endmonth,$endday);
    $year = $startyear;
    $date=yearmonthday2timestamp($year,$startmonth,$startday);
    while ($date <= $enddate) {
      // check if it is a valid date
      if (checkdate($startmonth,$startday,$year)) {
        $dateJD = JulianToJD($startmonth,$startday,$year);
        $repeatlist[$ecount]=$dateJD; // store this date in the list (array)
        $ecount++;
      }
      $year+=$interval;
      $date=yearmonthday2timestamp($year,$startmonth,$startday);
    }
  }
  elseif ($frequency=="W") { // recurring in weekly cycles
    if (empty($frequencymodifier)) {
      $dateJD = $startdateJD + $ecount * $interval*7;
      while ($dateJD <= $enddateJD) {
        $repeatlist[$ecount]=$dateJD; // store this date in the list (array)
        $ecount++;
        $dateJD = $startdateJD + $ecount * $interval*7;
      }
    }
    else {
      // determine the Sunday of the week
      $dow = Day_of_Week($startmonth,$startday,$startyear);
      $weekfrom = Add_Delta_Days($startmonth,$startday,$startyear,-$dow);

      $weekfromJD = JulianToJD($weekfrom[month],$weekfrom[day],$weekfrom[year]);

      // prepend a space to allow searching the string by testing "strpos(..) != 0"
      $frequencymodifier = " ".$frequencymodifier;

      $i = 0;
      $dateJD = $weekfromJD + $i * $interval*7;

      while ($dateJD <= $enddateJD) {
	      if (strpos($frequencymodifier,"MO")!=0) {
          if ($dateJD+1 >= $startdateJD && $dateJD+1 <= $enddateJD) { $repeatlist[$ecount]=$dateJD+1; $ecount++; }
        }
	     if (strpos($frequencymodifier,"TU")!=0) {
          if ($dateJD+2 >= $startdateJD && $dateJD+2 <= $enddateJD) { $repeatlist[$ecount]=$dateJD+2; $ecount++; }
        }
	if (strpos($frequencymodifier,"WE")!=0) {
          if ($dateJD+3 >= $startdateJD && $dateJD+3 <= $enddateJD) { $repeatlist[$ecount]=$dateJD+3; $ecount++; }
        }
	if (strpos($frequencymodifier,"TH")!=0) {
          if ($dateJD+4 >= $startdateJD && $dateJD+4 <= $enddateJD) { $repeatlist[$ecount]=$dateJD+4; $ecount++; }
        }
	if (strpos($frequencymodifier,"FR")!=0) {
          if ($dateJD+5 >= $startdateJD && $dateJD+5 <= $enddateJD) { $repeatlist[$ecount]=$dateJD+5; $ecount++; }
        }
	if (strpos($frequencymodifier,"SA")!=0) {
          if ($dateJD+6 >= $startdateJD && $dateJD+6 <= $enddateJD) { $repeatlist[$ecount]=$dateJD+6; $ecount++; }
        }
        if (strpos($frequencymodifier,"SU")!=0) {
          if ($dateJD+7 >= $startdateJD && $dateJD+7 <= $enddateJD) { $repeatlist[$ecount]=$dateJD+7; $ecount++; }
        }

        $i++;
	$dateJD = $weekfromJD + $i * $interval*7;
      }
    }
  }
  elseif ($frequency=="MP") { // recurring in monthly cycles like "MP2 3+ TH 20000512T..." or "MP12 1- FR 19990922T..."
    list($frequencymodifiernumber,$frequencymodifierday) = explode(" ",$frequencymodifier);

    if ($frequencymodifiernumber[1]=="-") { $last = 1; } else { $last = 0; }
    $frequencymodifiernumber = $frequencymodifiernumber[0];

    if ($frequencymodifierday=="SU") { $dow = 0; }
    elseif ($frequencymodifierday=="MO") { $dow = 1; }
    elseif ($frequencymodifierday=="TU") { $dow = 2; }
    elseif ($frequencymodifierday=="WE") { $dow = 3; }
    elseif ($frequencymodifierday=="TH") { $dow = 4; }
    elseif ($frequencymodifierday=="FR") { $dow = 5; }
    elseif ($frequencymodifierday=="SA") { $dow = 6; }

    $enddate = yearmonthday2timestamp($endyear,$endmonth,$endday);
    $year = $startyear;
    $month = $startmonth;
    $date=yearmonthday2timestamp($year,$month,1);
    while ($date <= $enddate) {

      $monthfromJD = JulianToJD($month,1,$year);
      $firstofmonth_dow = Day_of_Week($month,1,$year);

      // determine the date of the first occurrence of the specified weekday
      if ($firstofmonth_dow<=$dow) { $firstday = 1 + $dow-$firstofmonth_dow; }
      else { $firstday = 1 + (7-$firstofmonth_dow)+$dow; }
      $firstdayJD = $monthfromJD + $firstday-1;

      if ($last) {
        // determine if "last" means the 4th or the 5th weekday of the months
        // by testing whether the 5th weekday exist
	if (checkdate($month,$firstday+28,$year)) { $weeks=4; }
        else { $weeks=3; }
      }
      else {
        $weeks = $frequencymodifiernumber-1;
      }
      // e.g. we get the 3rd Thursday by adding 2 weeks to the first Thursday
      $dayJD = $firstdayJD + $weeks*7;

      if ($dayJD <= $enddateJD && $dayJD >= $startdateJD) {
        $repeatlist[$ecount]=$dayJD; // store this date in the list (array)
	      $ecount++;
      }

      $month+=$interval;
      if ($month>12) { $month -= 12; $year++; }
      $date=yearmonthday2timestamp($year,$month,1);
    }

  } // end: elseif ($frequency=="MP")


  return $repeatlist;
} // end: function repeatdefdisassembled2repeatlist

// takes the values from the input form and outputs a list containing dates
// it uses the vCalendar specification to store repeating event information
function producerepeatlist(&$event,&$repeat) {
  $repeatdef = repeatinput2repeatdef($event,$repeat);

  repeatdefdisassemble($repeatdef,
                       $frequency,$interval,$frequencymodifier,
                       $endyear,$endmonth,$endday);

  $repeatlist = repeatdefdisassembled2repeatlist($event['timebegin_year'],
                                                 $event['timebegin_month'],
                                                 $event['timebegin_day'],
                                                 $frequency,
                                                 $interval,
                                                 $frequencymodifier,
                                                 $endyear,
                                                 $endmonth,
                                                 $endday);
  return $repeatlist;
}

// prints out all the days contained in a recurrencelist (array)
function printrecurrencedetails(&$repeatlist) {
  if (sizeof($repeatlist)==0) {
    echo lang('recurrence_produces_no_dates');
  }
  else {
    echo "(",lang('resulting_dates_are');

    $comma = 0;
    while ($dateJD = each($repeatlist)) {
      if ($comma) { echo "; "; }
      $d = Decode_Date_US(JDToJulian($dateJD['value']));
      echo " ",Day_of_Week_Abbreviation(Day_of_Week($d['month'],$d['day'],$d['year'])),", ",JDToJulian($dateJD['value']);
      $comma = 1;
    }
    echo ")";
  }
} // end: function printrecurrencedetails

// translates the contents of a repeat definition string in vCalendar format
// to the input variables required for the input form
function repeatdef2repeatinput($repeatdef,&$event,&$repeat) {
  repeatdefdisassemble($repeatdef,$frequency,$interval,$frequencymodifier,$endyear,$endmonth,$endday);

  if ($frequency=="MP") {
    $repeat['mode'] = 2;
    list($frequency2modifier1,$frequency2modifier2) = explode(" ",$frequencymodifier);

    if ($frequency2modifier1=="1+") { $repeat['frequency2modifier1']="first"; }
    if ($frequency2modifier1=="2+") { $repeat['frequency2modifier1']="second"; }
    if ($frequency2modifier1=="3+") { $repeat['frequency2modifier1']="third"; }
    if ($frequency2modifier1=="4+") { $repeat['frequency2modifier1']="fourth"; }
    if ($frequency2modifier1=="1-") { $repeat['frequency2modifier1']="last"; }

    if ($frequency2modifier2=="SU") { $repeat['frequency2modifier2']="sun"; }
    if ($frequency2modifier2=="MO") { $repeat['frequency2modifier2']="mon"; }
    if ($frequency2modifier2=="TU") { $repeat['frequency2modifier2']="tue"; }
    if ($frequency2modifier2=="WE") { $repeat['frequency2modifier2']="wed"; }
    if ($frequency2modifier2=="TH") { $repeat['frequency2modifier2']="thu"; }
    if ($frequency2modifier2=="FR") { $repeat['frequency2modifier2']="fri"; }
    if ($frequency2modifier2=="SA") { $repeat['frequency2modifier2']="sat"; }

    if ($interval=="1") { $repeat['interval2']="month"; }
    if ($interval=="2") { $repeat['interval2']="2months"; }
    if ($interval=="3") { $repeat['interval2']="3months"; }
    if ($interval=="4") { $repeat['interval2']="4months"; }
    if ($interval=="6") { $repeat['interval2']="6months"; }
    if ($interval=="12") { $repeat['interval2']="year"; }
  }
  else {
    $repeat['mode'] = 1;

    if ($interval=="1") { $repeat['interval1']="every"; }
    if ($interval=="2") { $repeat['interval1']="everyother"; }
    if ($interval=="3") { $repeat['interval1']="everythird"; }
    if ($interval=="4") { $repeat['interval1']="everyfourth"; }

    if ($frequency=="D") { $repeat['frequency1']="day"; }
    if ($frequency=="M") { $repeat['frequency1']="month"; }
    if ($frequency=="YD") { $repeat['frequency1']="year"; }
    if ($frequency=="W") {
      if (empty($frequencymodifier)) { $repeat['frequency1']="week"; }
      elseif ($frequencymodifier=="MO WE FR") { $repeat['frequency1']="monwedfri"; }
      elseif ($frequencymodifier=="TU TH") { $repeat['frequency1']="tuethu"; }
      elseif ($frequencymodifier=="MO TU WE TH FR") { $repeat['frequency1']="montuewedthufri"; }
      elseif ($frequencymodifier=="SA SU") { $repeat['frequency1']="satsun"; }
    }
  } // end: else:   if ($frequency=="MP")

  $event['timeend_year'] = $endyear;
  $event['timeend_month'] = $endmonth;
  $event['timeend_day'] = $endday;

  return 1;
} // end: Function repeatdef2repeatinput($repeatdef,&$event,&$repeat)

function deletefromevent($eventid,$database) {
  $query = "DELETE FROM vtcal_event WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'";
  $result = DBQuery($database, $query ); 

  // delete event from default calendar if it had been forwarded
	if ( $_SESSION["CALENDARID"] != "default" ) {
	  // delete existing events in default calendar with same id
    $query = "DELETE FROM vtcal_event WHERE calendarid='default' AND id='".sqlescape($eventid)."'";
    $result = DBQuery($database, $query ); 
	} // end: if ( $_SESSION["CALENDARID"] != "default" )
} // end: function deletefromevent

function deletefromevent_public($eventid,$database) {
  $query = "DELETE FROM vtcal_event_public WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'";
  $result = DBQuery($database, $query ); 
} // end: function deletefromevent_public

function repeatdeletefromevent($repeatid,$database) {
  if (!empty($repeatid)) {
		$query = "DELETE FROM vtcal_event WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND repeatid='".sqlescape($repeatid)."'";
		$result = DBQuery($database, $query ); 
	
		// delete event from default calendar if it had been forwarded
		if ( $_SESSION["CALENDARID"] != "default" ) {
			// delete existing events in default calendar with same id
			$query = "DELETE FROM vtcal_event WHERE calendarid='default' AND repeatid='".sqlescape($repeatid)."'";
			$result = DBQuery($database, $query ); 
		} // end: if ( $_SESSION["CALENDARID"] != "default" )
	}
} // end: function repeatdeletefromevent

function repeatdeletefromevent_public($repeatid,$database) {
  if (!empty($repeatid)) {
    $query = "DELETE FROM vtcal_event_public WHERE calendarid='".$_SESSION["CALENDARID"]."' AND repeatid='".sqlescape($repeatid)."'";
    $result = DBQuery($database, $query ); 

		// delete event from default calendar if it had been forwarded
		if ( $_SESSION["CALENDARID"] != "default" ) {
			// delete existing events in default calendar with same id
			$query = "DELETE FROM vtcal_event_public WHERE calendarid='default' AND repeatid='".sqlescape($repeatid)."'";
			$result = DBQuery($database, $query ); 
		} // end: if ( $_SESSION["CALENDARID"] != "default" )
	}
} // end: function repeatdeletefromevent_public

function deletefromrepeat($repeatid,$database) {
  if (!empty($repeatid)) {
    $query = "DELETE FROM vtcal_event_repeat WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($repeatid)."'";
    $result = DBQuery($database, $query ); 
	}
} // end: function deletefromrepeat

function insertintoevent($eventid,&$event,$database) {
  return insertintoeventsql($_SESSION["CALENDARID"],$eventid,$event,$database);
} // end: function insertintoevent

function insertintoeventsql($calendarid,$eventid,&$event,$database) {
  $changed = date ("Y-m-d H:i:s");
  $query = "INSERT INTO vtcal_event (calendarid,id,approved,rejectreason,timebegin,timeend,repeatid,sponsorid,displayedsponsor,displayedsponsorurl,title,wholedayevent,categoryid,description,location,price,contact_name,contact_phone,contact_email,url,recordchangedtime,recordchangeduser,showondefaultcal,showincategory) ";
  $query.= "VALUES ('".sqlescape($calendarid)."','".sqlescape($eventid)."',0,'";
  if (!empty($event['rejectreason'])) {
		$query.= sqlescape($event['rejectreason']);
	}
	$query.= "','";
	$query.= sqlescape($event['timebegin'])."','";
	$query.= sqlescape($event['timeend'])."','".sqlescape($event['repeatid'])."','";
	$query.= sqlescape($event['sponsorid'])."','".sqlescape($event['displayedsponsor'])."','";
	$query.= sqlescape($event['displayedsponsorurl'])."','".sqlescape($event['title'])."','";
	$query.= sqlescape($event['wholedayevent'])."','".sqlescape($event['categoryid'])."','";
	$query.= sqlescape($event['description'])."','".sqlescape($event['location'])."','";
	$query.= sqlescape($event['price'])."','".sqlescape($event['contact_name'])."','";
	$query.= sqlescape($event['contact_phone'])."','".sqlescape($event['contact_email'])."','";
	$query.= sqlescape($event['url'])."','".sqlescape($changed)."','";
	if (isset($event['showondefaultcal'])) { $showondefaultcal = $event['showondefaultcal']; } else { $showondefaultcal = 0; }
	$query.= sqlescape($_SESSION["AUTH_USERID"])."','".sqlescape($showondefaultcal)."','";
	if (isset($event['showincategory'])) { $showincategory = $event['showincategory']; } else { $showincategory = 0; }
	$query.= sqlescape($showincategory)."')";
  $result = DBQuery($database, $query ); 
  return $eventid;
} // end: function insertintoevent

function insertintoevent_public(&$event,$database) {
  $changed = date ("Y-m-d H:i:s");
  $query = "INSERT INTO vtcal_event_public (calendarid,id,timebegin,timeend,repeatid,sponsorid,displayedsponsor,displayedsponsorurl,title,wholedayevent,categoryid,description,location,price,contact_name,contact_phone,contact_email,url,recordchangedtime,recordchangeduser) VALUES ";
  $query.= "('".sqlescape($_SESSION["CALENDARID"])."','".sqlescape($event['id'])."','";
	$query.= sqlescape($event['timebegin'])."','";
	$query.= sqlescape($event['timeend'])."','".sqlescape($event['repeatid'])."','";
	$query.= sqlescape($event['sponsorid'])."','".sqlescape($event['displayedsponsor'])."','";
	$query.= sqlescape($event['displayedsponsorurl'])."','".sqlescape($event['title'])."','";
	$query.= sqlescape($event['wholedayevent'])."','".sqlescape($event['categoryid'])."','";
	$query.= sqlescape($event['description'])."','".sqlescape($event['location'])."','";
	$query.= sqlescape($event['price'])."','".sqlescape($event['contact_name'])."','";
	$query.= sqlescape($event['contact_phone'])."','".sqlescape($event['contact_email'])."','";
	$query.= sqlescape($event['url'])."','".sqlescape($changed)."','";
	$query.= sqlescape($_SESSION["AUTH_USERID"])."')";

  $result = DBQuery($database, $query ); 
} // end: function insertintoevent_public

function updateevent($eventid,&$event,$database) {
  $changed = date ("Y-m-d H:i:s");
  $query = "UPDATE vtcal_event SET approved=0, rejectreason='".sqlescape($event['rejectreason']);
	$query.= "',timebegin='".sqlescape($event['timebegin'])."',timeend='".sqlescape($event['timeend']);
	$query.= "',repeatid='".sqlescape($event['repeatid'])."',sponsorid='".sqlescape($event['sponsorid']);
	$query.= "',displayedsponsor='".sqlescape($event['displayedsponsor'])."',displayedsponsorurl='".sqlescape($event['displayedsponsorurl']);
	$query.= "',title='".sqlescape($event['title'])."',wholedayevent='".sqlescape($event['wholedayevent']);
	$query.= "',categoryid='".sqlescape($event['categoryid'])."',description='".sqlescape($event['description']);
	$query.= "',location='".sqlescape($event['location'])."',price='".sqlescape($event['price']);
	$query.= "',contact_name='".sqlescape($event['contact_name'])."',contact_phone='".sqlescape($event['contact_phone']);
	$query.= "',contact_email='".sqlescape($event['contact_email'])."',url='".sqlescape($event['url']);
	$query.= "',recordchangedtime='".sqlescape($changed)."',recordchangeduser='".sqlescape($_SESSION["AUTH_USERID"]);
	$query.= "',showondefaultcal='".sqlescape($event['showondefaultcal'])."',showincategory='".sqlescape($event['showincategory'])."' ";
	$query.= "WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'";
  $result = DBQuery($database, $query ); 
} // end: function updateevent

function updateevent_public($eventid,&$event,$database) {
  $changed = date ("Y-m-d H:i:s");
  $query = "UPDATE vtcal_event_public SET timebegin='".sqlescape($event['timebegin']);
  $query.= "',timeend='".sqlescape($event['timeend'])."',repeatid='".sqlescape($event['repeatid']);
	$query.= "',sponsorid='".sqlescape($event['sponsorid'])."',displayedsponsor='".sqlescape($event['displayedsponsor']);
	$query.= "',displayedsponsorurl='".sqlescape($event['displayedsponsorurl'])."',title='".sqlescape($event['title']);
	$query.= "',wholedayevent='".sqlescape($event['wholedayevent'])."',categoryid='".sqlescape($event['categoryid']);
	$query.= "',description='".sqlescape($event['description'])."',location='".sqlescape($event['location']);
	$query.= "',price='".sqlescape($event['price'])."',contact_name='".sqlescape($event['contact_name']);
	$query.= "',contact_phone='".sqlescape($event['contact_phone'])."',contact_email='".sqlescape($event['contact_email']);
	$query.= "',url='".sqlescape($event['url'])."',recordchangedtime='".sqlescape($changed);
	$query.= "',recordchangeduser='".sqlescape($_SESSION["AUTH_USERID"]);
	$query.= "' WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'";
  $result = DBQuery($database, $query ); 
} // end: function updateevent_public

function insertintotemplate($template_name,&$event,$database) {
  $changed = date ("Y-m-d H:i:s");
  $query = "INSERT INTO vtcal_template (calendarid,name,sponsorid,displayedsponsor,displayedsponsorurl,title,wholedayevent,categoryid,description,location,price,contact_name,contact_phone,contact_email,url,recordchangedtime,recordchangeduser) ";
  $query.= "VALUES ('".sqlescape($_SESSION["CALENDARID"])."','".sqlescape($template_name);
	$query.= "','".sqlescape($event['sponsorid'])."','".sqlescape($event['displayedsponsor']);
	$query.= "','".sqlescape($event['displayedsponsorurl'])."','".sqlescape($event['title']);
	$query.= "','".sqlescape($event['wholedayevent'])."','".sqlescape($event['categoryid']);
	$query.= "','".sqlescape($event['description'])."','".sqlescape($event['location']);
	$query.= "','".sqlescape($event['price'])."','".sqlescape($event['contact_name']);
	$query.= "','".sqlescape($event['contact_phone'])."','".sqlescape($event['contact_email']);
	$query.= "','".sqlescape($event['url'])."','".sqlescape($changed)."','".sqlescape($_SESSION["AUTH_USERID"])."')";
  $result = DBQuery($database, $query ); 
} // end: function updatetemplate

function updatetemplate($templateid,$template_name,&$event,$database) {
  $changed = date ("Y-m-d H:i:s");
  $query = "UPDATE vtcal_template SET name='".sqlescape($template_name)."',sponsorid='".sqlescape($event['sponsorid']);
	$query.= "',displayedsponsor='".sqlescape($event['displayedsponsor'])."',displayedsponsorurl='".sqlescape($event['displayedsponsorurl']);
	$query.= "',title='".sqlescape($event['title'])."',wholedayevent='".sqlescape($event['wholedayevent']);
	$query.= "',categoryid='".sqlescape($event['categoryid'])."',description='".sqlescape($event['description']);
	$query.= "',location='".sqlescape($event['location'])."',price='".sqlescape($event['price']);
	$query.= "',contact_name='".sqlescape($event['contact_name'])."',contact_phone='".sqlescape($event['contact_phone']);
	$query.= "',contact_email='".sqlescape($event['contact_email'])."',url='".sqlescape($event['url']);
	$query.= "',recordchangedtime='".sqlescape($changed)."',recordchangeduser='".sqlescape($_SESSION["AUTH_USERID"]);
	$query.= "' WHERE sponsorid='".sqlescape($_SESSION["AUTH_SPONSORID"])."' AND id='".sqlescape($templateid)."'";
  $result = DBQuery($database, $query ); 
} // end: function updatetemplate

function insertintorepeat($repeatid,&$event,&$repeat,$database) {
  $repeat['startdate'] = datetime2timestamp($event['timebegin_year'],$event['timebegin_month'],$event['timebegin_day'],0,0,"am");
  $repeat['enddate'] = datetime2timestamp($event['timeend_year'],$event['timeend_month'],$event['timeend_day'],0,0,"am");
  $repeatdef = repeatinput2repeatdef($event,$repeat);
  $changed = date ("Y-m-d H:i:s");

  // write record into repeat table
  $query = "INSERT INTO vtcal_event_repeat (calendarid,id,repeatdef,startdate,enddate,recordchangedtime,recordchangeduser) ";
	$query.= "VALUES ('".sqlescape($_SESSION["CALENDARID"])."','".sqlescape($repeatid)."','".sqlescape($repeatdef)."','".sqlescape($repeat['startdate'])."','".sqlescape($repeat['enddate'])."','".sqlescape($changed)."','".sqlescape($_SESSION["AUTH_USERID"])."')";
  $result = DBQuery($database, $query ); 
  $repeat['id'] = $repeatid;
  
  return $repeat['id'];
} // end: function insertintorepeat

function updaterepeat($repeatid,&$event,&$repeat,$database) {
  $repeat['startdate'] = datetime2timestamp($event['timebegin_year'],$event['timebegin_month'],$event['timebegin_day'],0,0,"am");
  $repeat['enddate'] = datetime2timestamp($event['timeend_year'],$event['timeend_month'],$event['timeend_day'],0,0,"am");
  $repeatdef = repeatinput2repeatdef($event,$repeat);

  // write record into repeat table
  $query = "UPDATE vtcal_event_repeat SET repeatdef='".sqlescape($repeatdef)."',startdate='";
	$query.= sqlescape($repeat['startdate'])."',enddate='".sqlescape($repeat['enddate']);
	$query.= "' WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($repeatid)."'";
  $result = DBQuery($database, $query ); 

  return $repeatid;
} // end: function updaterepeat

function num_unapprovedevents($repeatid,$database) {
  $result = DBQuery($database, "SELECT id FROM vtcal_event WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND repeatid='".sqlescape($repeatid)."' AND approved=0"); 
  return $result->numRows();
} // end: function num_unapprovedevents

function publicizeevent($eventid,&$event,$database) {
  if (!empty($event['repeatid'])) { // if event delivers repeatid that's fine
    $r['repeatid'] = $event['repeatid'];
  }
  else { // get repeatid from old entry in event_public (important if event changes from recurring to one-time)
    $result = DBQuery($database, "SELECT repeatid FROM vtcal_event_public WHERE id='".sqlescape($eventid)."'" ); 
    if ($result->numRows()>0) { 
      $r = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
    }
  }

  if (!empty($r['repeatid'])) { repeatdeletefromevent_public($r['repeatid'],$database); }
  else { deletefromevent_public($eventid,$database); }
  
	$event['id'] = $eventid; // this line should not be necessary but some functions still have a bug that doesn't pass the id in event['id']
  
	insertintoevent_public($event,$database);

  $result = DBQuery($database, "UPDATE vtcal_event SET approved=1 WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND id='".sqlescape($eventid)."'" ); 

  // forward event to default calendar if that's indicated
	if ( $_SESSION["CALENDARID"] != "default" ) {
		// delete existing events in default calendar with same id
		$query = "DELETE FROM vtcal_event WHERE calendarid='default' AND id='".sqlescape($eventid)."'";
		$result = DBQuery($database, $query ); 
		
	  if ( $event['showondefaultcal'] == 1 ) {
		  // add new event in default calendar (with approved=0)
			$eventcategoryid = $event['categoryid'];
			$event['categoryid'] = $event['showincategory'];
			insertintoeventsql("default",$eventid,$event,$database);
			$event['categoryid'] = $eventcategoryid;
		} 
		else {
			$query = "DELETE FROM vtcal_event_public WHERE calendarid='default' AND id='".sqlescape($eventid)."'";
			$result = DBQuery($database, $query ); 
		}
	} // end: if ( $_SESSION["CALENDARID"] != "default" )
} // end: publicizeevent

function repeatpublicizeevent($eventid,&$event,$database) {
  deletefromevent_public($eventid,$database);
  if (!empty($event['repeatid'])) {
    repeatdeletefromevent_public($event['repeatid'],$database);
  }

	// forward events to default calendar: delete old events
	if ( $_SESSION["CALENDARID"] != "default" ) {
		// delete existing events in default calendar with same id
		$e = $eventid;
		$dashpos = strpos($e, "-");
		if ( $dashpos ) { 
		  $e = substr($e,0,$dashpos); 
		}
		$query = "DELETE FROM vtcal_event WHERE calendarid='default' AND id='".sqlescape($e)."'";
		$result = DBQuery($database, $query ); 
    
		if (!empty($event['repeatid'])) {
  		$query = "DELETE FROM vtcal_event WHERE calendarid='default' AND repeatid='".sqlescape($event['repeatid'])."'";
		  $result = DBQuery($database, $query ); 
		}
		
		if ( $event['showondefaultcal'] != 1 ) { // remove events if checkmark for forwarding is removed
			$query = "DELETE FROM vtcal_event_public WHERE calendarid='default' AND id='".sqlescape($e)."'";
			$result = DBQuery($database, $query ); 
			if (!empty($event['repeatid'])) {
  			$query = "DELETE FROM vtcal_event_public WHERE calendarid='default' AND repeatid='".sqlescape($event['repeatid'])."'";
		  	$result = DBQuery($database, $query ); 
			}
		}
	} // end: if ( $_SESSION["CALENDARID"] != "default" )

  // copy all events into event_public
  $result = DBQuery($database, "SELECT * FROM vtcal_event WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND repeatid='".sqlescape($event['repeatid'])."'" );
  for ($i=0;$i<$result->numRows();$i++) {
    $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
//    eventaddslashes($event);
    insertintoevent_public($event,$database);
		
		// forward event to default calendar if that's indicated
		if ( $_SESSION["CALENDARID"] != "default" ) {
			if ( $event['showondefaultcal'] == 1 ) {
				// add new event in default calendar (with approved=0)
				$eventcategoryid = $event['categoryid'];
				$event['categoryid'] = $event['showincategory'];
				insertintoeventsql("default",$event['id'],$event,$database);
				$event['categoryid'] = $eventcategoryid;
			}
		} // end: if ( $_SESSION["CALENDARID"] != "default" )
  } // end: for(...

  $query = "UPDATE vtcal_event SET approved=1 WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND approved=0 AND repeatid='".sqlescape($event['repeatid'])."'";
	$result = DBQuery($database, $query ); 
} // end: repeatpublicizeevent

function getFullCalendarURL () {
  if ( isset($_SERVER["HTTPS"]) ) { $calendarurl = "https"; } else { $calendarurl = "http"; } 
  $calendarurl .= "://".$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'], "/"))."/index.php?calendarid=".$_SESSION["CALENDARID"];
  return $calendarurl;
}

// sends an email to a sponsor
function sendemail2sponsor($sponsorname,$sponsoremail,$subject,$body) {
  $body.= "\n\n";
  $body.= "----------------------------------------\n";
  $body.= $_SESSION["NAME"]." \n";
  $body.= getFullCalendarURL()."\n";
  $body.= $_SESSION["ADMINEMAIL"]."\n";
  
  sendemail($sponsorname,$sponsoremail,lang('calendar_administration'),$_SESSION["ADMINEMAIL"],$subject,$body);
} // end: Function sendemail2sponsor
// sends an email to a sponsor

function sendemail2user($useremail,$subject,$body) {
  $body.= "\n\n";
  $body.= "----------------------------------------\n";
  $body.= $_SESSION["NAME"]."\n";
  $body.= getFullCalendarURL()."\n";
  $body.= $_SESSION["ADMINEMAIL"]."\n";
  
  sendemail($useremail,$useremail,lang('calendar_administration'),$_SESSION["ADMINEMAIL"],$subject,$body);
} // end: Function sendemail2user

// opens a DB connection to postgres
function DBopen() {
  $database = DB::connect( DATABASE );
  return $database;
} // end: openDB

// closes a DB connection to postgres
function DBclose($database) {
  $database->disconnect();
} // end: openDB

function getNumCategories($database) {
  $result = DBQuery($database, "SELECT count(*) FROM vtcal_category WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."'" ); 
  $r = $result->fetchRow(0);
  return $r[0];
}

function print_event(&$event) {
global $lang, $day_end_h;
?>
		  <table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#FFFFFF">
				<tr valign="top">
          <td align="center" valign="top" class="eventtimebig">
					  <img alt="" src="images/spacer.gif" width="1" height="6"><br>
<?php 
		if ($event['wholedayevent']==0) {
			echo timestring($event['timebegin_hour'],$event['timebegin_min'],$event['timebegin_ampm']);
			if ( ! ($event['timeend_hour']==$day_end_h && $event['timeend_min']==59) ) {
			  echo "<br>",lang('to'),"<br>";
			  echo timestring($event['timeend_hour'],$event['timeend_min'],$event['timeend_ampm']);
			}
    }
		else {
		  echo lang('all_day'),"\n";
		}
?>          </td>
          <td bgcolor="<?php echo $_SESSION["MAINCOLOR"]; ?>">&nbsp;</td>
          <td>
<span class="eventtitlebig"><?php echo $event['title']; ?></span>
&nbsp;<br>(<?php echo $event['category_name']; ?>)<br>
<br>
<?php 
  if (!empty($event['description'])) {
		echo "<p>",str_replace("\r", "<br>", $event['description']);

  }
?>
<?php 
  if (!empty($event['url']) && $event['url'] != "http://") {
?>
     <br><a href="<?php echo $event['url'],"\">",lang('more_information');?></a>
<?php
  } // end: if (!empty($event['url'])) {
	
  if (!empty($event['description'])) {
 	  echo"</p><br>";
	}
?>				

      <table border="0" cellspacing="5" cellpadding="0">
<?php 
  if (!empty($event['location'])) {
?>
        <tr> 
          <td align="left" valign="top" nowrap width="5%"><strong><?php echo lang('location'); ?>:</strong></td>
          <td width="95%"><?php echo $event['location']; ?></td>
        </tr>
<?php
  } // end: if (!empty($event['location'])) {
?>				
<?php 
  if (!empty($event['price'])) {
?>
        <tr> 
          <td align="left" valign="top" nowrap width="5%"><strong><?php echo lang('price'); ?>:</strong></td>
          <td width="95%"><?php echo $event['price']; ?></td>
        </tr>
<?php
  } // end: if (!empty($event['price'])) {
?>	        
<?php 
  if (!empty($event['displayedsponsor'])) {
?>
        <tr> 
          <td align="left" valign="top" nowrap width="5%"><strong><?php echo lang('sponsor'); ?>:</strong></td>
          <td width="95%"><?php 
    if (!empty($event['displayedsponsorurl'])) {
		  echo "<a href=\"",$event['displayedsponsorurl'],"\">";
			echo $event['displayedsponsor'];
			echo "</a>";
		}
		else {
		  echo $event['displayedsponsor'];
		}
?>          </td>
        </tr>
<?php
  } // end: if (!empty($event['displayedsponsor'])) {
?>				
<?php 
  if (!empty($event['contact_name']) ||
	    !empty($event['contact_email']) ||
			!empty($event['contact_phone']) 
	) {
?>
        <tr> 
          <td align="left" valign="top" nowrap width="5%"><strong><?php echo lang('contact'); ?>:</strong></td>
          <td width="95%">
<?php if (!empty($event['contact_name']) ) { echo $event['contact_name'],"<br>"; } ?>
<?php if (!empty($event['contact_email']) ) { 
  echo '<img src="images/email.gif" width="20" height="20" alt="',lang('email'),'" align="absmiddle">';
  echo " <a href=\"mailto:",$event['contact_email'],"\">",$event['contact_email'],"</a><br>"; } 
?>
<?php if (!empty($event['contact_phone']) ) { 
  echo '<img src="images/phone.gif" width="20" height="20" align="absmiddle"> ';
  echo $event['contact_phone'],"<br>"; } 
?>
          </td>
        </tr>
<?php
  } // end: if (...)
?>				
        <tr> 
          <td align="left" valign="top">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td align="left" valign="top" colspan="2">
<?php
  if (!empty($event['id'])) {
?>						
					<a 
            href="icalendar.php?eventid=<?php echo $event['id']; ?>"><img 
            src="images/vcalendar.gif" width="20" height="20" border="0" align="absmiddle"></a>
          <a href="icalendar.php?eventid=<?php echo $event['id']; ?>"><?php echo lang('copy_event_to_pda'); ?></a>
<?php
  } // end: if (!empty($event['id']))
?>					
					</td>
        </tr>
      </table>
					</td>
        </tr>
    </table>
<?php		
} // end: Function print_event

// highlights all occurrences of the keyword in the text
// case-insensitive
function highlight_keyword($keyword, $text) {
	$keyword = preg_quote($keyword);
	$newtext = preg_replace('/'.$keyword.'/Usi','<span style="background-color:#ffff99">\\0</span>',$text);
	return $newtext;
}
?>