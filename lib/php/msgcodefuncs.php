<?php
 
function getCurrentLanguage(){
		if(!isset($_SESSION["currentlanguage"])){
			$_SESSION["currentlanguage"]=$_COOKIE["lang"]?$_COOKIE["lang"]:getDefaultLanguage();;
		}
		return $_SESSION["currentlanguage"];
}

function setCurrentLanguage($lang){
	$_SESSION["currentlanguage"]=$lang;
}

function getEventMsgField($field,$id,$lang=null,$encode=false)
{
	if(is_null($lang)){
		$lang= getCurrentLanguage();
	}

	$r=array();
	$sql = 	'select msgtext from eventmsgcodes where field="'.$field.'" and eventid="'.$id.'" and (lang="'.$lang.'" or lang="'.getDefaultLanguage().'" or lang="en") order by field(lang,"'.$lang.'","'.getDefaultLanguage().'","en") limit 1';
        global $mysqli;
	if($result=$mysqli->query($sql)){
		if($result->num_rows){
			$r=$result->fetch_assoc();
			return $encode?htmlspecialchars($r["msgtext"]):$r["msgtext"];
		}
	}
	return null;
}


function sysMsg($msgcode,$fallback="",$subst=NULL,$raw=false,$lang=null){

	if(is_null($lang))
		$lang=getCurrentlanguage();
	
	$sql='SELECT msgtext FROM sysmsgcodes where id="'.$msgcode.'" and (lang="'.$lang.'" or lang="'.getDefaultLanguage().'" or lang="en") order by field(lang,"'.$lang.'","'.getDefaultLanguage().'","en") limit 1';

	$row=array();

    global $mysqli;
	$result=$mysqli->query($sql);

	if($result && $result->num_rows){
		$row=$result->fetch_assoc();
		$result->free();
		$msgtext=$row["msgtext"];
	}else{
		$msgtext="";
	}
	
	if(!$raw){
		if($subst){
			$msgtext=str_replace('%s',$subst,$msgtext);
		}
	}

	return htmlspecialchars($msgtext?$msgtext:$fallback);

}

function echoSysMsg($msgcode,$fallback=NULL,$subst=""){
	echo sysMsg($msgcode,$fallback,$subst);
}

function echoRaw($msgcode){
	$msgtext=sysMsg($msgcode,null,null,true);
	if(strlen($msgtext)==0)
		$msgtext=$msgcode;
	echo $msgtext;
}

function echoSysMsgSub($msgcode,$subst){
	$msgcode=str_replace('%s',$subst,$msgcode);
	echoSysMsg($msgcode);
}

function getDefaultLanguage(){
	if(!isset($_SESSION["defaultlanguage"])){
			global $languages;
			current($languages);
			$_SESSION["defaultlanguage"]=key($languages);
	}
	return $_SESSION["defaultlanguage"]; 
}

/* return array of languages for this event */
function eventLanguages(&$row)
{
	$sql = 'SELECT distinct lang from eventmsgcodes where eventid='.$row["id"];
    global $mysqli;
	if((!$result = $mysqli->query($sql)) || $result->num_rows==0)
		return false;
    
    global $languages;

    while($l = $result->fetch_assoc()){
        $l=$l["lang"];
        if(isset($languages[$l]))
            $row["languages"][]=$l;
    }
}


function getEventLocaleFields(&$row,$lang=null,$encode=false)
{
	if(is_null($lang))
		$lang=eventLanguage($row);
	$row["lang"]=$lang;
	$row["title"]=getEventMsgField("title",$row["id"],$lang,$encode);
	$row["description"]=getEventMsgField("description",$row["id"],$lang,$encode);

	return $row;
}

function addLanguageSelectValues($lang)
{
	$h="";
	global $languages;
	foreach($languages as $key => $value){
		$str= '<option value="'.$key.'"';
		if($key==$lang)
			$str=$str.' selected="selected"';
		$h.=$str . '>' . $value . '</option>';
	}
	return $h;
}

function saveMsgCodeSQL(&$msg)
{
	$eventid=$msg["eventid"];
	$field=$msg["field"];
        global $mysqli;
	$msgtext=$mysqli->real_escape_string($msg["msgtext"]?$msg["msgtext"]:$_POST['msgtext']);
	$lang=$msg["lang"];
	$user=$_SESSION["CurrentUser"];

        $sql='insert into eventmsgcodes (eventid, field, msgtext, lang, createdby, updatedby) values ('.$eventid.',"'.$field.'","'.$msgtext.'","'.$lang.'","'.$user.'","'.$user.'") on duplicate key update msgtext="'.$msgtext.'",updatedby="'.$user.'";';
        return $sql;
}

?>
