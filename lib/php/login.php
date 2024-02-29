<?php
$localLoadLibs[]='barwebs.login.js';
$pageHTML.='<link rel="stylesheet" href="login.css" type="text/css">
<div class="login" title="format c:"><div></div></div>
<form class="loginform focuscontrol draggable bgshade" method="post" action="act_checkadminlogin.php">
  <input type="text" name="username">
  <input type="password" name="password">
  <input class="loginbuttonsubmit" type="submit" value="">
</form>';
?>