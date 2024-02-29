<?php
@require_once("utils.php");
$args{"id"}=$_POST{"id"};
$args{"category"}=$_POST{"category"};
callFactory($_POST{"factory"},"edit",$args);
returnToLastPage($args{"category"});
?>