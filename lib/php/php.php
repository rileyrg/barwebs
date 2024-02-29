<?php
@require_once("utils.php");
if(adminMode())
	phpinfo();
else
	header('Location: notauthorized');
?>
