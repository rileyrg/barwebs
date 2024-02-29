<?php
function GMTTime($t){
	return $t - date('Z', time());
}

function localSiteTime($t){
/* $t is the site on the server */
	return $t; /* GMTTime($t)+TIME_OFFSET; obsolete after putenv timezone in constants.php*/
}

function expireTime($t,$dur=120){
	return $t-(60*$dur);
}

function eventExpired($e)
{

    $expired= time() > strtotime($e["enddate"]);

    if($expired){
        /* logText("time(): ".time()); */
        /* logText("enddate : ".strtotime($e["enddate"])); */
        /* logText($e); */
    }
	return $expired;

}

function eventScheduleClass($e){

    if(!$e["startdate"])
        return "unscheduled";

    $t=time();
    $nowOn=($t>strtotime($e["startdate"])) && ($t<strtotime($e["enddate"]));
    if($nowOn)
        return "scheduled nowon";

	if(eventExpired($e))
        return "expired";
	 
	$a1 = getdate(strtotime($e["startdate"]));
	$a2 = getdate();
	if(!$a2 || !$a1)
		return null;
	
	$diff=$a1{"yday"}-$a2{"yday"};
	
	$class=null;
	switch ($diff) {
	case 0:
		$class="scheduled today";
		break;
	case 1:
		$class="scheduled tomorrow";
		break;
	default:
		$class="scheduled";
	}
	return $class;
}

?>