<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  if ( isset($_SERVER["HTTPS"]) ) { $calendarurl = "https"; } else { $calendarurl = "http"; } 
	$calendarurl .= "://".$_SERVER['HTTP_HOST'].substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'], "/"))."/";
  $database = DBopen();
  helpbox_begin();
?>
<H3><IMG alt="" border=0 height=16 src="images/nuvola/16x16/actions/help.png" width=16>
<?php echo lang('help_import'); ?>
</H3>
<?php echo lang('help_import_intro'); ?>
<hr size="1">
<pre style="font-size:10pt">
&lt;events&gt;
  &lt;event&gt;
    &lt;displayedsponsor&gt;Athletics Department&lt;/displayedsponsor&gt;
    &lt;displayedsponsorurl&gt;http://www.hokiesports.com/&lt;/displayedsponsorurl&gt;
    &lt;date&gt;2000-03-15&lt;/date&gt;
    &lt;timebegin&gt;15:00&lt;/timebegin&gt;
    &lt;timeend&gt;&lt;/timeend&gt;
    &lt;categoryid&gt;9&lt;/categoryid&gt;
    &lt;title&gt;Baseball vs. Kent&lt;/title&gt;
    &lt;description&gt;VT is playing vs. Kent...&lt;/description&gt;
    &lt;location&gt;English Field&lt;/location&gt;
    &lt;price&gt;free&lt;/price&gt;
    &lt;contact_name&gt;Jennifer Meyers&lt;/contact_name&gt;
    &lt;contact_phone&gt;231-4933&lt;/contact_phone&gt;
    &lt;contact_email&gt;jmeyer@vt.edu&lt;/contact_email&gt;
    &lt;url&gt;http://www.hokiesportsinfo.com/baseball/&lt;/url&gt;
  &lt;/event&gt;
  &lt;event&gt;
    &lt;displayedsponsor&gt;Indian Student Association&lt;/displayedsponsor&gt;
    &lt;displayedsponsorurl&gt;http://fbox.vt.edu:10021/org/isa/&lt;/displayedsponsorurl&gt;
    &lt;date&gt;1999-11-06&lt;/date&gt;
    &lt;timebegin&gt;17:00&lt;/timebegin&gt;
    &lt;timeend&gt;21:00&lt;/timeend&gt;
    &lt;categoryid&gt;9&lt;/categoryid&gt;
    &lt;title&gt;Diwali '99&lt;/title&gt;
    &lt;description&gt;A two and half hour cultural show at Buruss Auditorium. 
    The show includes traditional Indian dance, a fashion show featuring traditional 
    clothes from different parts of India, a live orchestra playing popular hindi songs, 
    a tickle-your-belly skit based on the recent elections in India, a jam of guitar and 
    Indian classical musical instruments, children's show among others events.
    &lt;/description&gt;
    &lt;location&gt;Buruss Auditorium&lt;/location&gt;
    &lt;price&gt;free&lt;/price&gt;
    &lt;contact_name&gt;Akash Rai&lt;/contact_name&gt;
    &lt;contact_phone&gt;540-951-7764&lt;/contact_phone&gt;
    &lt;contact_email&gt;arai@vt.edu&lt;/contact_email&gt;
    &lt;url&gt;http://fbox.vt.edu:10021/org/isa/diwali99/&lt;/url&gt;
  &lt;/event&gt;
  &lt;event&gt;
    ...
  &lt;/event&gt;
  ...  
&lt;/events&gt;
</pre>
<hr size="1">
<br>
<?php echo lang('help_import_data_format_intro'); ?>
<table border="1" cellspacing="0" cellpadding="5">
  <tr>
    <th><?php echo lang('help_categoryid_index'); ?></th>
    <th><?php echo lang('help_categoryid_name'); ?></th>
  </tr>
<?php
  // read event categories from DB
  $result = DBQuery($database, "SELECT * FROM vtcal_category WHERE calendarid='".sqlescape($_SESSION["CALENDARID"])."' ORDER BY name ASC" ); 

  // print list with categories and select the one read from the DB
  for ($i=0;$i<$result->numRows();$i++) {
    $category = $result->fetchRow(DB_FETCHMODE_ASSOC,$i);
		echo "  <tr>\n";
		echo "    <td>",$category['id'],"</td>";
		echo "    <td>",$category['name'],"</td>";
		echo "  </tr>";
  }
?>
</table>
</li>            
</ul>
<?php echo lang('help_import_data_format'); ?>
<br>
<?php
  helpbox_end();
?>