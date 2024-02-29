<?php
@require_once("utils.php");

if (adminMode()){
	returnToLastPage();
}else{
        global $mysqli;
	$user = $mysqli->real_escape_string($_POST['username']);
	$pass = $mysqli->real_escape_string($_POST['password']);
	if(login($user,$pass)){
		returnToLastPage();
	}else{
		header("Location:notauthorized");
       }
}
?>