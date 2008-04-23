<?php
  define("ALLOWINCLUDES", TRUE); // in effect, any file that includes "globalsettings.inc.php" is also authorized to call other include files
  require_once('config.inc.php');
  require_once('DB.php');
  require_once('inputvalidation.inc.php');
	
	// set the correct calendarid
  if ( isset($_GET['calendarid']) && isValidInput($_GET['calendarid'],'calendarid') ) {	$calendarid = $_GET['calendarid']; }
  elseif ( isset($_GET['calendar']) && isValidInput($_GET['calendar'],'calendarid')) { $calendarid = $_GET['calendar']; }
	else { unset($calendarid); }

  if ( isset($calendarid) ) { 
    if ( calendar_exists ( $calendarid ) ) { // switch to different calendar
      $_SESSION["CALENDARID"] = $calendarid;
      setCalendarPreferences();
			logout();
	  }
  }
  if ( !isset($_SESSION["CALENDARID"]) ) {
    $_SESSION["CALENDARID"] = "default";
    setCalendarPreferences();
		logout();
  }
	
	// exclude month view for certain browsers because of extremely slow load times
	if ( $_SERVER["HTTP_USER_AGENT"] == "Mozilla/4.0 (compatible; MSIE 5.22; Mac_PowerPC)" ) {
		$enableViewMonth = false;		
	} 
	else { 
	  $enableViewMonth = true; 
	}
  //sets variable to according to week starting day specified in "config.inc.php". Sunday is default week starting day if WEEK_STARTING_DAY isn't defined in "config.inc.php'
   
  if(WEEK_STARTING_DAY == 0 || WEEK_STARTING_DAY == 1 ) {
      $week_start = WEEK_STARTING_DAY;
   }else{
      $week_start = 0;  
   }
   if(USE_AMPM == false){
      $use_ampm=false;
      $day_beg_h=0;    // if 0:00 - 23:00 time format is used,appropriate day start, end hours will be passed to datetime2timestamp funtions where calculating day edges
      $day_end_h=23;
   }else{
      $use_ampm=true;
      $day_beg_h=0;
      $day_end_h=11;
   }


  function calendar_exists ( $calendarid ) {
    $database = DB::connect( DATABASE );
    $result = DBQuery($database, "SELECT count(id) FROM vtcal_calendar WHERE id='".sqlescape($calendarid)."'" ); 
    $r = $result->fetchRow(0);
    $database->disconnect();
	  return ($r[0]==1);
  }
  
  function setCalendarPreferences() {
    $database = DB::connect( DATABASE );
    $result = DBQuery($database, "SELECT * FROM vtcal_calendar WHERE id='".sqlescape($_SESSION["CALENDARID"])."'" ); 
    $calendar = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
	$_SESSION["TITLE"] = $calendar['title'];
	$_SESSION["NAME"] = $calendar['name'];
	$_SESSION["HEADER"] = $calendar['header'];
	$_SESSION["FOOTER"] = $calendar['footer'];
	$_SESSION["VIEWAUTHREQUIRED"] = $calendar['viewauthrequired'];
	$_SESSION["FORWARDEVENTDEFAULT"] = $calendar['forwardeventdefault'];
	
	$_SESSION["BGCOLOR"] = $calendar['bgcolor'];
	$_SESSION["MAINCOLOR"] = $calendar['maincolor'];
	$_SESSION["TODAYCOLOR"] = $calendar['todaycolor'];
	$_SESSION["PASTCOLOR"] = $calendar['pastcolor'];		
	$_SESSION["FUTURECOLOR"] = $calendar['futurecolor'];		
	$_SESSION["TEXTCOLOR"] = $calendar['textcolor'];		
	$_SESSION["LINKCOLOR"] = $calendar['linkcolor'];		
	$_SESSION["GRIDCOLOR"] = $calendar['gridcolor'];
  
    $result = DBQuery($database, "SELECT * FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND admin='1'" ); 
    $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,0);
	$_SESSION["ADMINEMAIL"] = $sponsor['email'];
  }

	function logout() {
		unset($_SESSION["AUTH_USERID"]);
		unset($_SESSION["AUTH_SPONSORID"]);
		unset($_SESSION["AUTH_SPONSORNAME"]);
		unset($_SESSION["AUTH_ADMIN"]);
	}	

	function DBQuery($database, $query) {
		$result = $database->query( $query );
		if ( SQLLOGFILE!="" ) {
			$logfile = fopen(SQLLOGFILE, "a");
			if (!empty($_SESSION["AUTH_USERID"])) { $user = $_SESSION["AUTH_USERID"]; } else { $user = "anonymous"; }
			fputs($logfile, date( "Y-m-d H:i:s", time() )." ".$_SERVER["REMOTE_ADDR"]." ".$user." ".$_SERVER["PHP_SELF"]." ".$query."\n");
			fclose($logfile);	
		}
		
		return $result;
	}	

	// run a sanity check on incoming request variables and set particular variables if checks are passed
	function setVar(&$var,$value,$type) {
		if (isset($value)) {
		  // first, remove any escaping that may have happened if magic_quotes_gpc is set to ON in php.ini
			if (get_magic_quotes_gpc()) {
			  if (is_array($value)) {
				  foreach ($value as $key=>$v) {
					  $value[$key] = stripslashes($v);
					}
				}
				else {
				  $value = stripslashes($value);
				}
			}
			
		  if (isValidInput($value, $type)) {
			  $var = $value;
				return;
			}
		}
	  // unless something is explicitly allowed unset the variable
		$var = NULL;
		return;
	}
	
	// escapes a value to make it safe for a SQL query
	function sqlescape($value) {
	  if (preg_match("/^pgsql/",DATABASE)) {
		  return pg_escape_string($value);
		}
		else {
			return mysql_escape_string($value);
		}
	}
	
    require_once('languages/'.LANGUAGE.'.inc.php');
	// returns a string in a particular language
	function lang($sTextKey) {
	  if (isset($GLOBALS['lang'][$sTextKey])) {
		return $GLOBALS['lang'][$sTextKey];
	  }
	  else {
	    require('languages/en.inc.php');
	  	return $lang[$sTextKey];
	  }
	}
?>