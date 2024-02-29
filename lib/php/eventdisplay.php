<?php

function emptyListHTML(&$a)
{
    return '<ol class="eventgroup emptylist"><li><div class="title eventline">(Currently there are no '.dataList($a["category"]).' entries.)</div><li class="event noeventwarning"></li></ol>';
}

function groupcmp($a,$b)
{
    return $a=="popup"?-1:($b=="popup"?1:strcasecmp($a, $b));
}


function htmlFromSQL(&$a,$fAdminEvents=false)
{
	$html="";
	if(isset($a["sql"])){
		global $singleEvent;
		foreach($a["sql"] as $index=>$sql){ /*iterate over different sets */
			$factoryID=$a["factory"];
			if(!($which=$a["which"]))
				$which="default";

            global $mysqli;
			$result=$mysqli->query($sql);

			if(!$result){
				continue;
			}
                        
            if($result->num_rows==0){
                $result->free();
                continue;
            }

			while($r=$result->fetch_assoc())
				$g[$r["groupby"]][]=convertRecordFieldsForFrontEnd($r);
			$result->free();

            uksort($g,"groupcmp");

			if($fAdminEvents){
				$html.= '<div class="settingscontainer shadered25">';
				$html.= '<div class="type-seperator">Type : '.$index.'</div>';
            }
                        
			foreach($g as $i=>$v){

				if($fAdminEvents)
					$html.= '<div class="group-seperator"><img src="common-images/groupby.png"/>Grouping :<b>'.$i.'</b></div>';

				$html.= '<ol itemscope itemtype="http://schema.org/ItemList" class="'.$a["category"].'-list '.($fAdminEvents?'':$i.'-group')." ".($a["listclass"]?"".$a["listclass"]:" eventgroup ").($fAdminEvents?'':$a["embedded"]?' embeddedlist ':'')." ".$a["classes"].'" '.($a["listid"]?'id="'.$a["listid"].'"':'').' data-category="'.$a["category"].'" data-group="'.$i.'">';
				foreach($v as $i2=>$r){
                    if(!getSetting("web:showallinmobile",null,false)){
                        if(mobileMode()){
                            if(strpos($r["cssclass"],"nomobile")!==false){
                                continue;
                            }
                        }else{
                            if(strpos($r["cssclass"],"onlymobile")!==false){
                                continue;
                            }
                        }
                    }
					$a["row"]=$r;
                    $h=displayObject($factoryID,"container",$which,$a); /* events default */;
                    $html.=$h;
				}
				$html.= '</ol>';
			}
			if($fAdminEvents)
				$html.= '</div>';
		}
	}
	return $html;
}

function eventListHTML(&$a,$fSettings=false){

	if(!isset($a["factory"]))
		$a["factory"]="event";
	if(!isset($a["which"]))
		$a["which"]="default";
	if(!isset($a["category"]))
		$a["category"]=currentPage();

	if(!$a["sql"]){
		validEventListSQL($a);
	}

    $html=htmlFromSQL($a,$fSettings);
    return $html;
}

function displayEvent(&$a){
	global $_factories;

    if(adminMode())
       eventLanguages($a["row"]);

	$row=$a["row"];
        
    if(!eventAllowed($row)){
        return "";
    }

	global $singleEvent;
	$cat=isset($row["category"])?str_replace("data-","",$row["category"]):currentPage();
	$html="";

	if(strtolower($row["token"])!="css"&&!(isset($_GET["noeventcss"])||isset($_GET["nocss"]))){
        $inlinestyle=$row["cssid"]?"":$row["cssstyle"];
		if($row["csslink"]){
			$html.='<link rel="stylesheet" href="'.$row["csslink"].'" type="text/css"/>';
		}
        if($row["cssid"]){
            $html.='<style type="text/css">'.$row["cssstyle"].'</style>';
        }
	}
	
	$classes=$cat." ".$_factories{"event"}{$cat}{"classes"};

	if($row["cssid"]){
		$classes=$row["cssclass"];
	}else{
		$classes= ($row["fadminonly"]? ' setting ':'')." ".(!isset($a["disallowdefaultclasses"])?getSetting("ev:defaultclasses"):"")." ".$classes;
		$classes.=" ".str_replace(","," ",$row["cssclass"]);
	}

	if(adminMode()){
        if(!$row["fenabled"])
            $classes.=" disabledevent";
    }
		
	if($row["embeddedlist"])
		$classes="containsembeddedlist ".$classes;
    if(adminMode())
        $classes="adminmode ".$classes;
    
    if($row["cookietimeout"]||$row["cookiename"])
        $classes="cookieprotected ".$classes;

    if($row["displaydelay"])
        $classes="displaydelayed ".$classes;

    if($row["fpopup"])
        $classes="eventpopup ".$classes;

    if($row["fclosebutton"])
        $classes="addclosebutton ".$classes;

    $fEnableShare=false;
    
    if(!adminMode()){
        if(!isset($a["disallowshare"])&&!isset($_GET["noshare"]))
            //noshare on the url (&noshare ) turns off all shares. evshare turns them on.
            if(isset($_GET["evshare"]))
                $fEnableShare=$_GET["evshare"];
            else
                $fEnableShare=$row["fenableshare"] && getSetting("list:enableshare",null,true);
        if($fEnableShare)
            $classes.=" shareable";
    }

    $classes.=" ".eventScheduleClass($row);
        
	$html.= '<li data-pkey="'.$row["id"].'"'.($row["popupposition"]?' data-popupposition="'.$row["popupposition"].'"':'').($row["displaydelay"]?' data-displaydelay="'.$row["displaydelay"].'"':'').($row["displayfor"]?' data-displayfor="'.$row["displayfor"].'"':'').($row["attachto"]?' data-attachto="'.$row["attachto"].'"':'').($row["cookietimeout"]?' data-cookietimeout="'.$row["cookietimeout"].'"':'').($row["cookietimeout"]?' data-orderby="'.$row["orderby"].'"':'').($row["cookietimeout"]?' data-cookiename="'.($row["cookiename"]?$row["cookiename"]:"eventcookie-".$row["id"]).'"':'').' id="'.($row["cssid"]?$row["cssid"].'"':'ID'.$row["id"]).'"  data-category="'.$row["category"].'" class="'.(isset($a["mainclass"])?$a["mainclass"]:'event').' '.$classes.'" '.($inlinestyle?' style="'.$inlinestyle.'"':"").'>';

    if(adminMode()){
        if(!isset($a["nonerd"])){
            $html.='<div class="iddisplay nerdmodeonly">'.$row["id"].'</div>';
            global $jsonrecords;
            $jsonrecords[$row["id"]]=$row;;
        }
    }
    
    if($fEnableShare&&!$_GET["print"]){
        if((!$singleEvent || $row["id"]==$singleEvent)){
            $html.='<div class="barwebsshare">'.shareButton($row).addThisButton($row);
            if(isset($_SESSION["fbuser"])){
                $html.='<div class="fbaction">'.fbActionHTML($row).'</div>';
            }
            if($row["startdate"]){
                $html.='<div class="addtocal"></div>';
            }
            $html.='</div>';
        }
    }
    if($row["startdate"]){
        $dateprops="";
        if(getSetting("ev:addtocal",null,true)){
            $dateprops='data-starttime="'.strtotime($row["startdate"]).'" ';
            if($row["enddate"])
                $dateprops.='data-endtime="'.strtotime($row["enddate"]).'" ';
            if(!isset($a["nocalurl"]))
                $dateprops.='data-calurl="'.shareURL($row).'" ';
        }
        $html.='<div '.$dateprops.' class="hoverbright times"></div>';
    }
    

    $html.=displayObject($a["factory"],"content",$a["which"],$a); /* events default */
	$html.= '</li>';
        
	if(($row["token"]!="js")&&(!adminMode()||$_GET["js"])){
		global $eventJavaScriptSnippets;
		$eventJavaScriptSnippets[]=getJSFromEvent($row);
	}
	return $html;
}

function embeddedListHTML($a)
{
    $row=$a["row"];
    $a["embeddedlistsettings"]=$listsettings = explode(",",$row["embeddedlist"]);
    $a["search"]=null;
    $a["embedded"]=true;
    $a["category"]=$listsettings[0];
    $a["groupby"]=$listsettings[1];
    $a["count"]=$listsettings[2];
    $a["listid"]=$listsettings[3];
    unset($a["sql"]);
    if($listsettings[5])
        $a["which"]=$listsettings[5];
    if($listsettings[6])
        $a["factory"]=$listsettings[6];
    return eventListHTML($a,$a["listid"]);
}

function htmlDisplayTitle($t)
{
    $teams=teamNames($t);
    if(count($teams)==2)
        return '<div class="hometeam">'.$teams[0].'</div><div class="teamversus"> v </div><div class="awayteam">'.$teams[1].'</div>';
    return nl2br($t);
    
}

function singleEventHTML(&$a)
{

	$row=$a["row"];

	$href=htmlspecialchars(($exturl=externalURL($row))?$exturl:shareURL($row));
	$rel =$row["eventlink"]?"next":"alternate";

	global $singleEvent;
	$eventHTML='<div class="barwebsevent" itemscope itemtype="'.($row["mdtype"]?$row["mdtype"]:getSetting("md:type",$row["category"],$row["startdate"]?"http://schema.org/Event":"http://schema.org/thing")).'">';

    if($row["startdate"]){
        $eventHTML.='<div class="eventline startdate">'.htmlspecialchars(eventTimeStr($row["startdate"])).'</div>';
    }

    $a["titletag"]=$a["embedded"]?"h3":"h2";

    $title=htmlspecialchars($row["title"]);
    if($title){
        $h="";
        if(!isset($a["nonerd"]) && adminMode()){
            $h.='<ul class="languageflags">';
            foreach($row["languages"]  as $i=>$lang){
                $h.='<li class="flag scalehalf halfdim '.$lang.'"></li>';
            }
            $h.='</ul>';
        }

        $eventHTML.='<div title="'.$title.'" data-field="title" data-field-type="text" class="eventline title"><'.$a["titletag"].'><a rel="'.$rel.'" href="'.$href.'" alt="'.$title.'" itemprop="name">'.htmlDisplayTitle($row["title"]).'</'.$a["titletag"].'></a>'.$h.'</div>';
    }

    if($row["startdate"])
        $eventHTML.='<div class="comingupfield"></div>';

    if($row["fbcomments"]){
        $eventHTML.='<div class="fbcomments">'.fbComments($row).'</div>';
    }
        
    if((!mobileMode()|| (strpos($row["cssclass"],"mobileimage")!==false) || !$a["embedded"]) && $img=$row["image"] ){
        $eventHTML.='<div data-field="image" data-field-type="imgurl" class="eventline eventicon" style="url('.$img.')">';
        if($href)
            $eventHTML.='<a rel="'.$rel.'" href="'.$href.'" alt="'.$title.'"><img itemprop="image" title="'.$title.'" src="'.$img.'"/></a>';
        else
            $eventHTML.='<img  itemprop="image" src="'.$img.'"/>';
        $eventHTML.='</div>';
    }

    if($row["embed"]){
        if($row["embed"]=="fblike")
            $row["embed"]=urlSubstitutions('<div class="fb-like" data-href="%s" data-send="true" data-show-faces="'.(getSetting("fb:showfaces",null,true)?"true":"false").'"  data-width="300" data-stream="false" data-header="false" data-action="like" data-colorscheme="dark"></div>');
        $eventHTML.='<div class="eventline embed" id="embed'.$row["id"].'">'.$row["embed"].'</div>';
    }

    if($row["includelink"]){
        ob_start();
        include($row["includelink"]);
        $eventHTML.= '<div class="eventline userinclude">'.ob_get_clean().'</div>';
    }

    $embeddedlisthtml=null;
    if($row["description"]|| $row["setting"]|| $row["embeddedlist"]){
        global $singleEvent;
        if(!$singleEvent&&$row["embeddedlist"]&&!$a["embedded"]){ /* dont allow embeds of embed */
            $embeddedlisthtml = embeddedListHTML($a);
        }
        global $dontShowSettings;
        $ev=$row["description"]?$row["description"]:(in_array($row["token"],$dontShowSettings)?"":$row["setting"]);
        $ev=isClass($r,"raw")?$ev:nl2br(htmlspecialchars($ev));
        $desc='<a  itemprop="description" rel="'.$rel.'" href="'.$href.'">'.$ev.'</a>';
        if($embeddedlisthtml){
            $eventHTML.='<div class="eventline embeddedlistcontainer">'.$embeddedlisthtml.'</div>';
            /* // this bit sees if there are events on the page any data- type events might be stored on. e.g data-tvsport : the see more will take */
            /* // the reader to tvsport and not to data-tvsport */
            /* $userListPage = dataList($listsettings[0]); */
            /* if(strpos($listsettings[0],currentPage())==false && numEvents($userListPage)) */
            /*     $eventHTML.='<div class="eventline seemore"><a rel="'.$rel.'" href="'.$userListPage.'"><div class="" title="See More"></div></a></div>'; */
        }
        $eventHTML.='<div title="'.$title.'" data-field="description" data-field-type="text" class="eventline description '.($row["eventlink"]?"link":"").'"  name="description">'.$desc.'</div>';
    }

    if($row["specialinfo"]){
        $eventHTML.='<div  title="'.htmlspecialchars($row["specialinfo"]).'" data-field="specialinfo" data-field-type="text" class="eventline specialinfo"  name="specialinfo">'.htmlspecialchars($row["specialinfo"]).'</div>';
    }

        
    if($row["eventlink"]){
        if($icon=getSetting("ev:displayurlicon"))
            $eventHTML.='<div class="eventline displayurl"><a rel="'.$rel.'" href="'.$href.'"><div><img src="'.$icon.'"></img></div><div>'.$href.'</div></a></div>';
    }

    return $eventHTML.'</div>';

}

function centercontainerHTML($displayRules)
{
    $p="";
    $p.='<h1 class="h1title">'.htmlspecialchars(getSetting("web:h1title",null,$title)).'</h1>';
    $p.=faqHTML();

    $p.=eventListHTML($displayRules);

    global $singleEvent;
    if(adminMode()&&!$singleEvent){
        unset($displayRules["sql"]);
        settingEventListSQL($displayRules);
        $displayRules["factory"]="settings";
        $displayRules["which"]="default";
        $p.=eventListHTML($displayRules,true);
    }
    
    global $jsonrecords;
    $p.='<script>jsonrecords='.json_encode($jsonrecords).';</script>';

    return $p;

}

?>