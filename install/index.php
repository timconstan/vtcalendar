<html>
<head>
<title>VTCalendar Installation</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style><!--
body, td, p {
  font-family: Arial,Helvetica,Sans-Serif;
	font-size: 10pt;
}
//-->
</style>
</head>
<body>
<h1>VTCalendar Installation</h1>
<?php
	if (!function_exists("file_get_contents")) {
		function file_get_contents($filename, $use_include_path = 0) {
			$data = ""; // just to be safe. Dunno, if this is really needed
			$file = @fopen($filename, "rb", $use_include_path);
			if ($file) {
				while (!feof($file)) $data .= fread($file, 1024);
				fclose($file);
			}
			return $data;
		}
	}

  $configfilename = '../config.inc.php';
	if (isset($_POST['install'])) { // run install procedure
    // create config file config.inc.php from config.inc.php.template		
		$config = file_get_contents ("config.inc.template.php");

	if (isset($_POST['language'])) { 
  		$config = preg_replace('/define\("LANGUAGE","en"\);/','define("LANGUAGE","'.$_POST['language'].'");',$config);
	}
    
	if ($_POST['databasetype']=="mysql") { $databasetype = "mysql"; }
		elseif ($_POST['databasetype']=="postgres") { $databasetype = "pgsql"; }
		else { $databasetype = "other-please-specify"; }
		$connectionstring = $databasetype.'://'.$_POST['database_user'].':'.$_POST['database_password'].'@'.$_POST['database_host'].'/'.$_POST['database_name'];
		$config = ereg_replace('define\("DATABASE",[^\x0A\x0D]*\);','define("DATABASE", "'.$connectionstring.'");',$config);

    if ($_POST['auth_db'] == "1") {
  		$config = ereg_replace('define\("AUTH_DB",[^\x0A\x0D]*\);','define("AUTH_DB", true);',$config);
		} else {
  		$config = ereg_replace('define\("AUTH_DB",[^\x0A\x0D]*\);','define("AUTH_DB", false);',$config);
		}
		$config = ereg_replace('define\("AUTH_DB_USER_PREFIX",[^\x0A\x0D]*\);','define("AUTH_DB_USER_PREFIX", "'.$_POST['username_prefix'].'");',$config);

    if ($_POST['auth_ldap'] == "1") {
  		$config = ereg_replace('define\("AUTH_LDAP",[^\x0A\x0D]*\);','define("AUTH_LDAP", true);',$config);
		} else {
  		$config = ereg_replace('define\("AUTH_LDAP",[^\x0A\x0D]*\);','define("AUTH_LDAP", false);',$config);
		}
		$config = ereg_replace('define\("LDAP_HOST",[^\x0A\x0D]*\);','define("LDAP_HOST", "'.$_POST['ldap_url'].'");',$config);
		$config = ereg_replace('define\("LDAP_USERFIELD",[^\x0A\x0D]*\);','define("LDAP_USERFIELD", "'.$_POST['ldap_userfield'].'");',$config);
		$config = ereg_replace('define\("LDAP_BASE_DN",[^\x0A\x0D]*\);','define("LDAP_BASE_DN", "'.$_POST['ldap_basedn'].'");',$config);
		
		$base_url = $_POST['base_url']; 
		if ($base_url[strlen($base_url)-1]!="/") { $base_url .= "/"; }
		$base_secureurl = $_POST['base_secureurl'];
		if ($base_secureurl[strlen($base_secureurl)-1]!="/") { $base_secureurl .= "/"; }
		$config = ereg_replace('define\("BASEURL",[^\x0A\x0D]*\);','define("BASEURL", "'.$base_url.'");',$config);
		$config = ereg_replace('define\("SECUREBASEURL",[^\x0A\x0D]*\);','define("SECUREBASEURL", "'.$base_secureurl.'");',$config);

    $config = ereg_replace('define\("TIMEZONE_OFFSET",[^\x0A\x0D]*\);','define("TIMEZONE_OFFSET", "'.$_POST['timezone_offset'].'");',$config);
		
		$configfile = fopen($configfilename, "w");
  	fputs($configfile, $config);
		fclose($configfile);	

    // create database
    require_once( 'DB.php' );
    $database = DB::connect( $connectionstring );
		$query = file_get_contents ("database.sql");
		if (empty($_POST['mainadmin_password'])) {
		  // remove the statment that inserts the user since we assume ldap authentication
		  $query = ereg_replace("INSERT INTO vtcal_user \(id, password, email\) VALUES \('adminuserid', 'adminpassword', ''\);",'',$query);
		}
		else {
  		$query = ereg_replace('adminpassword',crypt($_POST['mainadmin_password']),$query);
		}
		$query = ereg_replace('adminuserid',$_POST['mainadmin_userid'],$query);
		if ( $databasetype == "pgsql") {
		  $query = ereg_replace('int NOT NULL auto_increment','serial NOT NULL',$query);
		}
		$query = ereg_replace('http://calendar.myorg.edu/',$base_url,$query);

		$queries = explode("\x0D\x0A\x0D\x0A",$query);
		$error = false;
		for($i=0; $i<count($queries); $i++) {
		  $q = trim($queries[$i]);
			if (!empty($q)) {
  			if ( DB::isError( $result = $database->query( $q ) ) ) {
	  			echo '<span style="color:red; font-size:16pt; font-weight:bold">Error! '.DB::errorMessage($result).':</span> '.$q.'<br><br>';
					$error = true;
		  	}
			} 
		}
    $database->disconnect();
		
		if (!$error) {
 			echo '<h2 style="color:#009900">Installation successful.</h2>';
			echo "If you want to make any configuration changes please modify the newly created file <b>config.inc.php</b>.<br><br>";
			echo '<span style="color:red; font-size:16pt; font-weight:bold">Security Notice:</span> Make sure to remove or secure the <b>/install</b> directory.<br><br>';
		  echo 'Your calendar is now available at: <a href="'.$base_url.'">'.$base_url.'</a>.';
		}
	}
	else { // show parameter screen
?>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#cccccc">
  <tr>
	  <td>
<table width="100%" border="0" cellspacing="1" cellpadding="5">
  <tr bgcolor="#CCCCCC">
    <td colspan="2" valign="baseline" nowrap><br>
      This installation procedure creates
      the file <font face="Courier New, Courier, mono"><strong>config.inc.php</strong></font> which
      can
be edited later by hand.<br>
<br></td>
    </tr>
  <tr bgcolor="#FFFFFF">
    <td valign="baseline" nowrap><strong>Language:</strong></td>
    <td valign="baseline"><select name="language"><?php
$dir = "../languages/";
if ($dh = opendir($dir)) {
	while (($file = readdir($dh)) !== false) {
		if (preg_match("|^(.*)\.inc\.php$|", $file, $matches)) {
			$languages[] = $matches[1];
		}
	}
	closedir($dh);
}
foreach ($languages as $language) {
    echo '<option value="',$language,'"';
	if ($language == "en") {
		echo ' selected';
	}
	echo '>',$language,"</option>\n";
}	
	?> 
	</select>
      (e.g. en stands for English; these match the file names of the translations in the &quot;languages/&quot; folder)</td>
  </tr>
  <tr bgcolor="#eeeeee">
    <td width="10%" valign="baseline" nowrap><strong>Database software:</strong></td>
    <td width="90%" valign="baseline">
      <input name="databasetype" type="radio" value="mysql" checked>
MySQL <br>
<input name="databasetype" type="radio" value="postgres">
PostgreSQL<br>
<input name="databasetype" type="radio" value="other"> 
other (manual setup)<br><br>
If you would like to use a  database different from MySQL and PostgreSQL you
should
finish
this
install and then modify config.inc.php by hand and manually create the database
structure
contained in the file tablestructure.sql.
Since
VT
Calendar
uses
PHP's
<a href="http://www.phpbuilder.com/columns/allan20010115.php3">PEAR DB</a> abstraction layer any database
should work. However, it has only been
tested with MySQL and PostgreSQL.</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td valign="baseline" nowrap><strong>Database host:</strong></td>
    <td valign="baseline"><input name="database_host" type="text" id="database_host" size="20" maxlength="100">
(e.g. localhost or database.myorg.com)</td>
  </tr>
  <tr bgcolor="#eeeeee">
    <td valign="baseline" nowrap><strong>Database name:</strong></td>
    <td valign="baseline"><input name="database_name" type="text" id="database_name" size="20" maxlength="100">
(e.g. calendar)</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td valign="baseline" nowrap><strong>Database user name:</strong></td>
    <td valign="baseline"><input name="database_user" type="text" id="database_user" size="20" maxlength="100"> 
      (e.g. user1)</td>
  </tr>
  <tr bgcolor="#eeeeee">
    <td valign="baseline" nowrap><strong>Database user password:</strong></td>
    <td valign="baseline"><input name="database_password" type="text" id="database_password" size="20" maxlength="100"> 
      (this is stored in <em>cleartext</em> in the config.inc.php file; don't
      use &quot;@&quot;,&quot;:&quot;,&quot;$&quot;,&quot;\&quot;)</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td valign="baseline" nowrap><strong>Authentication:</strong></td>
    <td valign="baseline"><input name="auth_db" type="checkbox" id="auth_db" value="1" checked>
enable authentication via internal calendar database:<br>
<table width="100%" border="0" cellspacing="3" cellpadding="3">
  <tr>
    <td width="1%" nowrap>&nbsp;&nbsp;&nbsp;</td>
    <td width="9%" valign="baseline" nowrap><strong>User name prefix:</strong></td>
    <td width="90%" valign="baseline"><input name="username_prefix" type="text" id="username_prefix" size="30" maxlength="100">
      (e.g. calendar.)<br>
      This can be used to keep the name space separate if you enable authentication
      via database <em>and</em> LDAP. If you just use database authentication,
      leave this field blank.</td>
  </tr>
</table><br>
  <input name="auth_ldap" type="checkbox" id="auth_ldap" value="1">
enable authentication via external LDAP server:
<table width="100%" border="0" cellspacing="3" cellpadding="3">
  <tr>
    <td width="1%" rowspan="3" nowrap>&nbsp;&nbsp;&nbsp;</td>
    <td width="9%" valign="baseline" nowrap><strong>LDAP URL:</strong></td>
    <td width="90%" valign="baseline"><input name="ldap_url" type="text" id="ldap_url" size="30" maxlength="100"> 
      (e.g. ldap://directory.myorg.edu/ or ldaps://...)</td>
  </tr>
  <tr>
    <td valign="baseline" nowrap><strong>LDAP user field:</strong></td>
    <td valign="baseline"><input name="ldap_userfield" type="text" id="ldap_userfield" size="30" maxlength="100"> 
        (e.g. uid)
      </td>
  </tr>
  <tr>
    <td valign="baseline" nowrap><strong>LDAP Base DN:</strong></td>
    <td valign="baseline"><input name="ldap_basedn" type="text" id="ldap_basedn" size="30" maxlength="100"> 
      (e.g. ou=users,dc=myorg,dc=edu)</td>
  </tr>
</table>
<br>
<table width="100%" border="0" cellspacing="3" cellpadding="3">
  <tr>
    <td width="9%" valign="baseline" nowrap><strong>Valid user-ID:</strong></td>
    <td width="90%" valign="baseline"><input name="ldap_userfield2" type="text" id="ldap_userfield22" value="/^[A-Za-z][_A-Za-z0-9\-]{1,7}$/" size="60" maxlength="300">
      (regular expression)<br>
      For example:
/^[A-Za-z][_A-Za-z0-9\-]{1,7}$/ or in case you have defined a user name prefix &quot;calendar.&quot; it
would be: /^(calendar\.){0,1}[A-Za-z][_A-Za-z0-9\-]{1,7}$/)      </td>
  </tr>
</table>
<br>
<table width="100%" border="0" cellspacing="3" cellpadding="3">
  <tr>
    <td colspan="2" valign="baseline" nowrap bgcolor="#eeeeee"><strong>Main administrator</strong></td>
    </tr>
  <tr>
    <td colspan="2" valign="baseline">The main administrator(s) can create
      and manage calendars. Below please enter the user-ID (and password if you
      do NOT use LDAP authentication). You can always add more admins later using
      the calendar web interface or by editing the database table &quot;vtcal_adminuser&quot;.</td>
    </tr>
  <tr>
    <td width="9%" valign="baseline" nowrap><strong> User-ID:</strong></td>
    <td width="90%" valign="baseline"><input name="mainadmin_userid" type="text" id="mainadmin_userid" size="20" maxlength="50">
      (e.g. jsmith)</td>
  </tr>
  <tr>
    <td valign="baseline" nowrap><strong>Password:</strong></td>
    <td valign="baseline"><input name="mainadmin_password" type="password" id="mainadmin_password" size="20" maxlength="50"> 
      (only necessary if you do NOT use LDAP auth)</td>
  </tr>
</table>
    </td>
  </tr>
  <tr bgcolor="#eeeeee">
    <td valign="top" nowrap><strong>Full URL:</strong></td>
    <td><input name="base_url" type="text" id="base_url" size="30" maxlength="100"> 
      (e.g. http://www.myorg.edu/calendar/)</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td valign="top" nowrap><strong>Secure URL:</strong></td>
    <td><input name="base_secureurl" type="text" id="base_url" size="30" maxlength="100">      
       (e.g. https://secure.myorg.edu/calendar/)<br>
       This URL is used when the user clicks the &quot;Update&quot; tab in order to secure
       all user-ID/password traffic. If you don't have a secure server with a
       copy of the calendar code, just put the same URL as you entered in &quot;Full
       URL&quot;.</td>
  </tr>
  <tr bgcolor="#eeeeee">
    <td valign="top" nowrap><strong>GMT-Timezone Offset:</strong></td>
    <td><input name="timezone_offset" type="text" id="timezone_offset" size="10" maxlength="10"> 
      (in hours; e.g. 5 for New York/USA, -1 for Berlin/Germany)</td>
  </tr>
  <tr bgcolor="#CCCCCC">
    <td valign="top" nowrap>&nbsp;</td>
    <td><br>
      <input type="submit" name="install" value="&nbsp;&nbsp;Install&nbsp;&nbsp;">
      <br>
      <br></td>
  </tr>
</table>
</td>
</tr>
</table>
</form>
<?php
  } // end: else:	if (isset($_POST['install'])) { // run install procedure
?>
</body>
</html>
