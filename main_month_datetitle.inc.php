<?php
  if (!defined("ALLOWINCLUDES")) { exit; } // prohibits direct calling of include files

  echo month_view_date_format (Month_to_Text($showdate['month']), $showdate['year']);

?>