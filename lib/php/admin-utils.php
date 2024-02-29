<?php

function isAdmin() /* doesnt mean he is logged in */
{
	return adminMode()||isFBAdmin();
}

/* returns whether the web is in administrator mode : returns set of priviliges*/
function adminMode(){
	return 	isset($_GET["adminoff"])?null:isset($_SESSION['adminpriviliges'])?$_SESSION['adminpriviliges']:null;
}

/* try to log in. Store a history of login attempt. If successful save the ID token associated with this session */
function login($user,$pwd){

        global $mysqli;
	$user = $mysqli->real_escape_string($user);
	$pwd = $mysqli->real_escape_string($pwd);

	$sql='select * from users where id="'.$user.'" and (expires is null or expires > now()) and password=MD5("'.$pwd.'")';
	
	if(($result=$mysqli->query($sql))&&$result->num_rows){
		updateloginstats($user,$result->fetch_assoc());
		return true;
	}
	
	return false;
}

/* the user has passed validation - mark him as logged in */
function updateloginstats($user,$row){
	$sql='update users set lastloggedin=now()  where users.id="'.$user.'"'; 
        global $mysqli;
	$mysqli->query($sql);
	setAdminMode($user,explode(",",$row{"priviliges"}));
}

function logout(){
	setAdminMode();
	if(fbAuthentication()){
		header('Location:'.facebook()->getLogoutURL());
	}
}

/* set admin mode on and stores the priviliges associated with the user */
function setAdminMode($user=null,$privs=null){
	$_SESSION["CurrentUser"]=$user;
	$_SESSION['adminpriviliges']=$privs;
}

/* returns true if the user has the correct permission token in their priviliges */
function hasAuthority($args){
	if(isAdmin())return true;
	if(!$privs=adminMode())
	   return false;
	$secToken=!$args["authtoken"]?$args:$args["authtoken"];
	$ret= strstr($privs[0],AUTH_ALL)==false?(strstr($privs[0],$secToken)==false?false:true):true;
	return $ret;
}

?>