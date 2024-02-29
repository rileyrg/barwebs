<?php

$eventGuards = array(
    "noGuard" => "allow always" ,
    "guarded" => "blocked",
    "loggedIntoFB" => "logged into FB"
);

function noGuard()
{
    return true;
}

function guarded()
{
    return false;
}

function addGuardSelectValues($v)
{
    global $eventGuards;
    $h="";
	foreach($eventGuards as $key => $value){
		$str= '<option value="'.$key.'"';
		if($key==$v)
			$str.=' selected="selected"';
		$h.= $str .'>' . $value . '</option>';
	}
    return $h;
}

function pageAllowed($page)
{
    if(adminMode())
        return true;
        
    /* See if a page link exists with a guard, if so check that event */
    global $mysqli;
    $sql='select guard from events where category in ("page","footer") and eventlink="'.$page.'" and fenabled=1 and guard is not null';
	if(!$result = $mysqli->query($sql))
        return true;
    return eventAllowed($result->fetch_assoc());
}

function eventAllowed(&$row)
{
    global $eventGuards;
    if($guard=$row["guard"]){
        $rc=$guard($row);
        return $rc;
    }
    return true;
}


function loggedIntoFB(&$row)
{
    if(isset($_SESSION["fbuser"])){
        $row["cssclass"].=" fbloggedinonly";
        return true;
    }
    return false;
}

?>