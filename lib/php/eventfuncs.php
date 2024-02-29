<?php
require_once("factoryfunctions.php");
require_once("eventstorage.php");
require_once("eventdisplay.php");

function isDataPage($p)
{
        if(strpos($p,"data-")===0)
                return preg_replace('/^data-/', '', $p);
        return false;
}

function dataList($category) /* if the cat is a data-cat then return the cat part */
{
        return str_replace("data-","",$category);
}

function numEvents($category)
{
	$query = 'SELECT COUNT(ID) FROM events where fenabled=1 and category="'.$category.'"'; 
        global $mysqli;
	$result = $mysqli->query($query);
	if(!$result)
		return 0;
	$row=array();
	$row = $result->fetch_assoc();
	$result->free();
	return $row["COUNT(ID)"];
}

function settingTitle(&$r)
{
        $t=$r["title"];
        return $r["token"].($t?" : ".$t:"");;
}

function getSettingHTML(&$a){
        $row=$a["row"];
        return '<div class="eventline title">'.settingTitle($row).'</div><div class="eventline">'.htmlspecialchars(getSetting($row["token"])).'</div>';
}
 

function eventTime($row)
{
	if($row["token"])
		return null;
	$t=date("H:i",strtotime($row["startdate"]));
	if(!$row["id"])
		if(!$t){
			global $_factories;
			$t=$_factories["event"][$row["category"]]["time"];
		}
	return $t;
}

function eventDate(&$row,$time=false,$cat=null,$end=false)
{

	if($row["token"])
		return "";


	if($row["startdate"])
		return $row["startdate"]=date("Y-m-d H:i",strtotime($row["startdate"])+($end?60*$row["duration"]:0));

	$deftime=getSetting("ev:defaulttime");
	
	if( $row["id"] || !$deftime)
		return "";
	
	return  $row["startdate"]=date("Y-m-d ".$deftime,localSiteTime(time()));
}

function getPageMetaHTML($category)
{
	$metas=getSettings("meta");
	$res="";
	foreach($metas as $key => $event){
        if($event["title"])
            $res.='<meta name="'.htmlspecialchars($event["title"]).'" content="'.htmlspecialchars($event["description"]?$event["description"]:$event["setting"]).'" />';
	}
	return $res;
}

function getCSSFromEvent(&$event)
{
	$res="";
	if($event["csslink"])
		$res.='<link rel="stylesheet" href="'.$event["csslink"].'" type="text/css" />';
	if($event["cssstyle"])
		$res.='<style type="text/css">'.$event["cssstyle"].'</style>';
	return $res;
}

function getJSFromEvent(&$event)
{
	$res="";
	if($event["javascriptlink"])
		$res='<script type="text/javascript" src="'.$event["javascriptlink"].'">/*ID:'.$event["id"].' */</script>';
	if($event["javascript"])
		$res.='<script type="text/javascript">try { eventEl('.$event["id"].').each(function(){'.str_replace('%id%',$event["uniqueid"],str_replace('%me%','$(this)',str_replace("%pkey%",$event["id"],$event["javascript"]))).'});}catch(err){eventJavaScriptError(el,"error executing java snippet for event pkey='.$event["id"].'");}</script>';
	return $res;
}

function getPageCSS($category=NULL)
{
	if(adminMode()&&isset($_GET["nocss"]))
		return"";
	$csslinks=getSettings("css",$category);
	$res="";
	foreach($csslinks as $key => $event){
		if(!$event["cssid"])
            $res.=getCSSFromEvent($event);
	}
	return $res;
}

	
function getPageJS($category=NULL)
{
	if(adminMode()&&!$_GET["js"])
		return "";
	$jslinks=getSettings("js",$category);
	$res="";
	foreach($jslinks as $key => $event){
		$res.=getJSFromEvent($event);
	}
	return $res;
}

	
function getRaw($category=NULL)
{
	if(isset($_GET["noraw"]))
		return "";
	$raw=getSettings("raw",$category);
	$res="";
	foreach($raw  as $key => $event){
			$res.=$event["description"];
	}
	return $res;
}

function getEventCategory($id)
{
        global $mysqli;
	if($result=$mysqli->query('select category from events where id='.$id)){
		$r=$result->fetch_assoc();
		return $r["category"];
	}
	return null;
}

function eventLanguage(&$row)
{
	/* return stripos(isset($row["token"])?$row["token"]:null,"css")===0&&$row["fadmin"]?"en":(isset($row["lang"])?$row["lang"]:getCurrentLanguage()); */
	return isset($row["lang"])?$row["lang"]:getCurrentLanguage();
}


function eventDuration(&$row)
{
	$row["duration"]=convertDuration($row["duration"]?$row["duration"]:getSetting("ev:defaultduration",null,"2:0"));
	return $row["duration"];
}

function externalURL($row)
{
        return $row["eventlink"];
}

?>