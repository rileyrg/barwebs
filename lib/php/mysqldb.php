<?php
function dbConnect($dbprefix=null)
{
    $f=@fopen("authinfo","r",true);
    $auth=explode(" ",@fgets($f));
    @fclose($f);
    $dbprefix= $dbprefix?$dbprefix:$auth{4};
    $mysqli = new mysqli($auth{3},$dbprefix.$auth{0},$auth{1},$dbprefix.$auth{2});
    if ($mysqli->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') '
            . $mysqli->connect_error);
    }
    return $mysqli;
}
session_start();
$mysqli=dbConnect($DB_PREFIX);

?>
