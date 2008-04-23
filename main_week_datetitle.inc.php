<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

  echo week_view_date_format ($weekfrom['day'],Month_to_Text($weekfrom['month']), $weekfrom['year'], $weekto['day'],Month_to_Text($weekto['month']), $weekto['year']);

?>