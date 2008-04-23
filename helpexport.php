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
<?php echo lang('help_export'); ?>
</H3>
<?php echo lang('help_export_intro'); ?>
  &quot;<?php echo $calendarurl; ?>export.php?calendar=<?php echo $_SESSION["CALENDARID"]; ?>&amp;type=xml&amp;timebegin=2000-03-17&amp;timeend=2000-05-20</a>&quot;.</li>
</ul>
<?php echo lang('help_export_formats'); ?>
<?php echo lang('help_export_xmlformat_example'); ?> 

<hr size="1">
<pre style="font-size:10pt">
&lt;events&gt;
  &lt;event&gt;
    &lt;eventid&gt;3454&lt;/eventid&gt;
    &lt;sponsorid&gt;uusa&lt;/sponsorid&gt;
    &lt;inputsponsor&gt;University Unions & Student Activities&lt;/inputsponsor&gt;
    &lt;displayedsponsor&gt;Graduate Student Assembly&lt;/displayedsponsor&gt;
    &lt;displayedsponsorurl&gt;http://gsa.uusa.vt.edu&lt;/displayedsponsorurl&gt;
    &lt;date&gt;2000-03-27&lt;/date&gt;
    &lt;timebegin&gt;18:00&lt;/timebegin&gt;
    &lt;timeend&gt;19:00&lt;/timeend&gt;
    &lt;repeat_vcaldef&gt;&lt;/repeat_vcaldef&gt;
    &lt;repeat_startdate&gt;&lt;/repeat_startdate&gt;
    &lt;repeat_enddate&gt;&lt;/repeat_enddate&gt;
    &lt;categoryid&gt;8&lt;/categoryid&gt;
    &lt;category&gt;Lectures&lt;/category&gt;
    &lt;title&gt;Lethal Viruses: Ebola&lt;/title&gt;
    &lt;description&gt;Colonel Nancy Jaax is delivering this keynote address for the Research Symposium.  
    She is a leading specialist in biological hazards. &lt;/description&gt;
    &lt;location&gt;Commonwealth Ballroom&lt;/location&gt;
    &lt;price&gt;free&lt;/price&gt;
    &lt;contact_name&gt;Kali Phelps&lt;/contact_name&gt;
    &lt;contact_phone&gt;231-7919&lt;/contact_phone&gt;
    &lt;contact_email&gt;kkniel@vt.edu&lt;/contact_email&gt;
    &lt;url&gt;http://gsa.uusa.vt.edu&lt;/url&gt;
    &lt;recordchangedtime&gt;2000-03-27 09:50:08&lt;/recordchangedtime&gt;
    &lt;recordchangeduser&gt;jsmith&lt;/recordchangeduser&gt;
  &lt;/event&gt;
  &lt;event&gt;
    ...
  &lt;/event&gt;
  ...
&lt;/events&gt;
</pre>
<hr size="1">
<br>
<P>
<?php echo lang('help_export_data_format'); ?>
<br>
<strong><?php echo lang('help_export_categoryid_intro'); ?></strong><br>
<br>
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
<br>
<?php
  helpbox_end();
?>