<?php
$path = $_SERVER{"DOCUMENT_ROOT"};
ini_set('include_path',
        ini_get('include_path') . PATH_SEPARATOR. $path . PATH_SEPARATOR .$path."/lib". PATH_SEPARATOR .$path."/lib/php". PATH_SEPARATOR .$path."/db".  PATH_SEPARATOR . $path."/custom".PATH_SEPARATOR.$path."/facebook" );
?>