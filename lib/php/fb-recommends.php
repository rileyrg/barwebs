<?php
$pageHTML.=(str_replace("%s",	str_replace("www.","",getSetting("sys:appdomain",null,$_SERVER['HTTP_HOST'])),'<fb:recommendations site="%s" "width="640" height="256" header="true" colorscheme="dark" font="arial" border_color="003300"></fb:recommendations>'));
?>
