<?php
require_once("factories.php");

function setObjectKey(&$args)
{
	$index="session.".$args{"factory"};
	$_SESSION{$index}{"id"}=$args{"id"};
	$_SESSION{$index}{"category"}=$args{"category"};
}

function getFields(&$args)
{
	return $args{"fields"};
}

function getFactory($factoryID="default",$component="default",$which="default",&$args=null)
{
	global $_factories;
	$f=$_factories{$factoryID}{$component}{$which};
	if(!is_null($args)){
		$args{"factory"}=$factoryID;
	}
	return $f;
}

function callFactory($factoryID="default",$component,$which="default",&$args=null,$opt=null){

	global $_factories;

	if(!strlen($factoryID))
		return;

	if(!is_null($args))
		$args{"factory"}=$factoryID;

	if(!is_null($opt)){
		$args{$opt}=$_factories{$factoryID}{"options"}{$opt};
	}

	$f=getFactory($factoryID,$component,$which,$args);

	if($f){
		return $f($args);
	}else{
		return $which($args);
	}
}

function displayObject($factoryID="default",$component="default",$which="default",&$args){
	return callFactory($factoryID,$component,$which,$args);
}


?>
