<?php
  $baseurl = 'http://www.calendar.vt.edu/';
  $calendar = 'default';
  $rangedays = 2;
  $xml = simplexml_load_file($baseurl.
         'export.php?calendar='.$calendar.
         '&type=xml'.
         '&categoryid=0'.
         '&sponsortype=all'.
         '&specificsponsor='.
         '&timebegin_month='.date("n").
         '&timebegin_day='.date("j").
         '&timebegin_year='.date("Y").
         '&rangedays='.$rangedays);

  foreach ($xml->event as $event) {
    echo '<a href="',$baseurl,'main.php?view=event',
         '&calendar=',$calendar,
         '&eventid=',$event->eventid,'">',
         $event->title, 
         '</a>',        
         "<br>\n";
  }
?>
