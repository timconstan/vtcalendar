<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  $database = DBopen();
  if (!authorized($database)) { exit; }

  if (isset($_POST['save'])) { setVar($save,$_POST['save'],'save'); } else { unset($save); }
  if (isset($_POST['cancel'])) { setVar($cancel,$_POST['cancel'],'cancel'); } else { unset($cancel); }
  if (isset($_POST['duration'])) { setVar($duration,$_POST['duration'],'duration'); } else { unset($duration); }

  if (isset($cancel)) {
    redirect2URL("update.php");
    exit;
  }

  if (isset($save) && isset($duration) && ($duration==1 || $duration==2 ||$duration==3) ) {
    $limitdate = Decode_Date_US(date("m/d/Y"));
		$limitdate['year']=$limitdate['year']-$duration;
    $limitdate['timestamp']=datetime2timestamp($limitdate['year'],$limitdate['month'],$limitdate['day'],12,0,"am");

		$query = "SELECT timebegin, sponsorid FROM vtcal_event WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."'";
		$query.= " ORDER BY sponsorid, timebegin ASC";
		$result = DBQuery($database, $query ); 
    $sponsors_latest_event = array();
		for ($i=0; $i<$result->numRows(); $i++) {
      $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
      $sponsors_latest_event[$event['sponsorid']]=$event['timebegin'];
		}
		
    // go through whole sponsor list and delete the ones that don't have new events
		$query = "SELECT * FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."'";
		$result = DBQuery($database, $query ); 
		$s = 0;
		for ($i=0; $i<$result->numRows(); $i++) {
      $sponsor = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
		  if ( $sponsor['admin'] == 0 ) {
				if ( !array_key_exists($sponsor['id'], $sponsors_latest_event) ||
							$sponsors_latest_event[$sponsor['id']] < $limitdate['timestamp']
					 ) { // mark this sponsor as deleted
					$deletesponsor[$s]=$sponsor['id'];
					$s++;
				}
			}
    }		

    // delete from sponsor table
		$query = "DELETE FROM vtcal_sponsor WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND (";
		for ($i=0; $i<count($deletesponsor); $i++) {
		  if ($i>0) { $query.=" OR "; }
			$query.="id='".sqlescape($deletesponsor[$i])."'";
		}
		$query.= ")";
		$result = DBQuery($database, $query ); 
    
    // delete from authorization table
		$query = "DELETE FROM vtcal_auth WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND (";
		for ($i=0; $i<count($deletesponsor); $i++) {
		  if ($i>0) { $query.=" OR "; }
			$query.="sponsorid='".sqlescape($deletesponsor[$i])."'";
		}
		$query.= ")";
		$result = DBQuery($database, $query ); 

    // delete from template table
		$query = "DELETE FROM vtcal_template WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND (";
		for ($i=0; $i<count($deletesponsor); $i++) {
		  if ($i>0) { $query.=" OR "; }
			$query.="sponsorid='".sqlescape($deletesponsor[$i])."'";
		}
		$query.= ")";
		$result = DBQuery($database, $query ); 

    // delete from event table
		$query = "DELETE FROM vtcal_event WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND (";
		for ($i=0; $i<count($deletesponsor); $i++) {
		  if ($i>0) { $query.=" OR "; }
			$query.="sponsorid='".sqlescape($deletesponsor[$i])."'";
		}
		$query.= ")";
		$result = DBQuery($database, $query ); 

    // delete from event_public table
		$query = "DELETE FROM vtcal_event_public WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND (";
		for ($i=0; $i<count($deletesponsor); $i++) {
		  if ($i>0) { $query.=" OR "; }
			$query.="sponsorid='".sqlescape($deletesponsor[$i])."'";
		}
		$query.= ")";
		$result = DBQuery($database, $query ); 
				
		// go through events and remember all the repeat-id's used
		$query = "SELECT repeatid FROM vtcal_event WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."'";
		$result = DBQuery($database, $query );
		for ($i=0; $i<$result->numRows(); $i++) {
      $event = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
			$repeatid_used[$event['repeatid']]=1;
		}

    // go through repeat table and remove all un-used records
		$r = 0;
		$query = "SELECT id FROM vtcal_event_repeat WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."'";
		$result = DBQuery($database, $query );
		for ($i=0; $i<$result->numRows(); $i++) {
      $repeat = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
			if ( !array_key_exists($repeat['id'],$repeatid_used) ) {
				$deleterepeat[$r]=$repeat['id'];
				$r++;
			}
		}
		
    // delete from repeat table
		$query = "DELETE FROM vtcal_event_repeat WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' AND (";
		for ($i=0; $i<count($deleterepeat); $i++) {
		  if ($i>0) { $query.=" OR "; }
			$query.="id='".sqlescape($deleterepeat[$i])."'";
		}
		$query.= ")";
		$result = DBQuery($database, $query ); 
		
    // reroute to sponsormenu page
    redirect2URL("update.php");
	  exit;
  }

  pageheader(lang('delete_inactive_sponsors'),
             lang('delete_inactive_sponsors'),
	           "Update","",$database);
  echo "<BR>";
  box_begin("inputbox",lang('delete_inactive_sponsors'));
?>
<FORM method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php echo lang('delete_inactive_sponsors_message'); ?> 
<select name="duration">
<option value="1" selected><?php echo lang('delete_inactive_sponsors_year'); ?></option>
<option value="2"><?php echo lang('delete_inactive_sponsors_2years'); ?></option>
<option value="3"><?php echo lang('delete_inactive_sponsors_3years'); ?></option>
</select>.
  <BR>
  <BR>
  <INPUT type="submit" name="save" value="<?php echo lang('ok_button_text'); ?>">
  <INPUT type="submit" name="cancel" value="<?php echo lang('cancel_button_text'); ?>">
</FORM>
<?php
  box_end();
  echo "<BR>";
  require("footer.inc.php");
?>