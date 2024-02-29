<?php
require_once("fb-utils.php");

function shareURL($row,$withlang=true){
	if($row["eventlink"][0]=="@") /* the share will share direct to outside of the web */
		return substr($row["eventlink"],1);
    $l='http://'.getDomain().'/'.$row["category"].'?id='.$row["id"].(getSetting("sys:shareanchors",null,false)?'#ID'.($row["cssid"]?$row["cssid"]:$row["id"]):'');
    /* $l='http://'.getDomain().'/'.$row["category"].'?id='.$row["id"]; */
	if($withlang&&getCurrentLanguage()!=getDefaultLanguage())
		$l.="&lang=".getCurrentLanguage();
	return $l;
}


function shareTitle($row)
{
    $date=$row["startdate"]?" - ".eventTimeStr($row["startdate"])." ":"";
    $desc=getSetting("list:sharedescription",$row["category"],"(".str_replace("data-","",$row["category"]).")");
	$title=$row["title"].($row["embeddedlist"]?getSetting("sys:embeddedlistsharetitle",null," - (Schedule Summary)"):"");
	return $desc." ".$title.$date.sysMsg("sys:reshare"," : because sharing is caring.");
}

function embeddedListShareDescription($a,$html=false)
{
    $row=$a["id"]?$a:$a["row"];
    $listsettings = explode(",",$row["embeddedlist"]);
    $category=$listsettings[0];
    $groupby=$listsettings[1];
	$sql='select *,(select msgtext from eventmsgcodes where   (eventid=events.id) and field="title" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") limit 1) as title, (select msgtext from eventmsgcodes where (eventid=events.id) and field="description" and   (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") limit 1) as description from events where category="'.$category.'" and groupby="'.$groupby.'" and startdate<>"" and (unix_timestamp(startdate) >= "'.expireTime(localSiteTime(time())).'") and fenabled=1 order by orderby,startdate';

    global $mysqli;
    $result=$mysqli->query($sql);
    
    if(!$result || $result->num_rows==0){
        return "";
    }
                  
    $t=$html?"<ol>":"";

    while($r=$result->fetch_assoc()){
        $t.=$html?"<li>":"";
        if($r["startdate"])
            $t.=eventTimeStr($r["startdate"])." : ";
        if($r["title"])
            $t.=$r["title"];
        if($r["description"])
            $t.=' ('.$r["description"].')'.($html?"<br>":"");
        $t.=$html?"</li>":" - ";
    }
    $t.=$html?"</ol>":"";
			
	$result->free();


    return $t;
}

function fbShareButton($row)
{
return '<div class="fb-like" data-href="'.htmlspecialchars(shareURL($row,false)).'" data-colorscheme="light" data-layout="button_count" data-action="like" data-show-faces="false" data-send="false"></div>';
}

function addThisButton($row=null)
{
    /* return fbShareBUtton($row); */
	$desc=$row["embeddedlist"]?embeddedListShareDescription($row,false):$row["description"];
	$title=shareTitle($row);
	$linkbackNoLang=shareURL($row,false);
	return '<div class="addthisshare addthis_toolbox addthis_default_style" addthis:title="'.$title.'"  addthis:description="'.htmlspecialchars($desc).'" addthis:url="'.htmlspecialchars($linkbackNoLang).'"><a class="addthis_button_preferred_1"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a></div>';
}

function addThisID()
{
	$addThisID=getSetting("addthis:shareid");
	if(!$addThisID)
		$addThisID="rileyrg";
	return $addThisID;
}


function shareButton($row,$text=null)
{
    /* test */
    $href=htmlspecialchars(shareURL($row));
	return '<div class="shareurl" ><a title="'.$href.'" alt="Share" href="'.$href.'">'.$text.'</a></div>';
}

?>