<?php
  session_start();
  require_once('globalsettings.inc.php');
  require_once('functions.inc.php');

  helpbox_begin();
?>
<H3><IMG src="images/nuvola/16x16/actions/help.png" width="16" height="16" alt="" border="0">
<?php echo lang('help_copyevent'); ?>
</H3>
<?php echo lang('help_copyevent_contents'); ?>
<?php
  helpbox_end();
?>