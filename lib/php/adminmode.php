<?php
if(!adminMode())
        exit("not admin");

$pageHTML.=getFileHTML("admintoolbar.php");
$pageHTML.=getFileHTML("editor.php");
$pageHTML.=getFileHTML("wizards.php");

global     $localLoadLibs;

$localLoadLibs[]='barwebs.adminmode.js';
?>
