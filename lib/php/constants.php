<?php
require_once("paths.php");

$DB_PREFIX="rileyrg_"; /* todo */

$languages= array(
	"en" => "English",
	"de" => "Deutsch"
);

// noupdatfields list the fields not to put into the update sql (eg title and desc go into msgcodes)
$noupdatefields = array("enddate","lang","factory","orgcategory","title","description","id");
// dontshow settings deermines which token types dont show in normal mode
$dontShowSettings=array("css","userinclude","embeddedlist");

define('AUTH_ALL','A');
define('AUTH_TRANSLATE','T');
define('AUTH_DUPLICATE','d');
define('AUTH_DELETE','D');
define('AUTH_CREATE','C');
define('AUTH_EDIT','E');

$hiddenEventTokens = array(
	"js","raw"
	);

require_once("custom/site-constants.php");

?>
