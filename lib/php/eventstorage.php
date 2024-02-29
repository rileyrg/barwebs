<?php
function getEvent(&$row)
{

	$sql = 'SELECT *,startdate + INTERVAL duration MINUTE as enddate,t.msgtext as title,d.msgtext as description from events,(select msgtext from eventmsgcodes where eventid='.$row["id"].' and field="title"  and (lang="'.($lang=eventLanguage($row)).'" or lang="'.getDefaultLanguage().'" or lang="en") order by field(lang,"'.$lang.'","'.getDefaultLanguage().'","en") limit 1) as t,(select msgtext from eventmsgcodes where eventid='.$row["id"].' and field="description"  and (lang="'.$lang.'" or lang="'.getDefaultLanguage().'" or lang="en") order by field(lang,"'.$lang.'","'.getDefaultLanguage().'","en") limit 1) as d where id='.$row["id"];
    global $mysqli;
	if((!$result = $mysqli->query($sql)) || $result->num_rows==0)
		return false;
	$row = $result->fetch_assoc();
	return $row;
}
function getSettingKey($token)
{
	$sql = 'SELECT id from events where token="'.$token.'" and category="'.currentPage().'"';
    global $mysqli;
	if((!$result = $mysqli->query($sql)) || $result->num_rows==0)
		return null;
	$row = $result->fetch_assoc();
	return $row["id"];
}

function getScheduledEvents(&$recs,$lang)
{
    $sql='SELECT id,category,cssclass,startdate,duration, startdate + INTERVAL duration MINUTE as enddate,(select msgtext from eventmsgcodes where eventid=events.id and field="title" and (lang ="'.$lang.'" or lang="en") limit 1) as title,(select lang from eventmsgcodes where eventid=events.id and field="title" and (lang ="'.$lang.'" or lang="en") limit 1) as titlelang, (select msgtext from eventmsgcodes where eventid=events.id and field="description" and (lang ="'.$lang.'" or lang="en") limit 1) as description,(select lang from eventmsgcodes where eventid=events.id and field="description" and (lang ="'.$lang.'" or lang="en") limit 1) as descriptionlang from events where fenabled=1 and (fadmin is null or fadmin=0)   and (now() <= startdate + INTERVAL duration MINUTE ) order by groupby,orderby,startdate;';
    global $mysqli;
    if($result=$mysqli->query($sql)){
        while($r=$result->fetch_assoc()){
            convertRecordFieldsForFrontEnd($r);
            $c=str_replace("data-","",$r["category"]);
            unset($r["category"]);
            $recs[$c][]=$r;
        }
        $result->free();
    }
}

function populateEventObject(&$row,$category=null){
	/* lets add a comment */
	if(!$row["id"]){
		$row["category"]=getSetting("ev:category",null,currentPage(),$category);
		$row["cssclass"]=getSetting("ev:cssclass",$category);
		$row["cssid"]="";
		/* $row["cssoverride"]=getSetting("ev:cssoverride",$category); */
		$row["cssstyle"]=getSetting("ev:cssstyle",$category);
		$row["description"]=getSetting("ev:description",$category);
		$row["duration"]=eventDuration($row);
		$row["specialinfo"]=getSetting("ev:specialinfo",$category,null);
		$row["embed"]=getSetting("ev:embed",$category);
		$row["eventlink"]=getSetting("ev:eventlink",$category,null);
		$row["fbcomments"]=getSetting("ev:fbcomments",$category);
		$row["fenabled"]=getSetting("ev:fenabled",$category,"1");
		$row["fdeletewhenexpired"]=getSetting("ev:fdeletewhenexpired",$category,"1");
		$row["fenableshare"]=getSetting("ev:fenableshare",$category,false); /* to do */
		$row["fjsonenabled"]=getSetting("ev:fjsonenabled",$category);
		$row["groupby"]=getSetting("ev:groupby",$category,"default");
		$row["image"]=getSetting("ev:image",$category);
		$row["javascript"]=getSetting("ev:js",$category);
		$row["cookietimeout"]=null;
		$row["lang"]=getDefaultLanguage();
		$row["ogactions"]=getSetting("og:actions",$category);
		$row["ogtype"]=getSetting("og:type",$category);
		$row["mdtype"]=NULL;
		$row["orderby"]=getSetting("ev:orderby",$category);
		$row["setting"]=null;
		$row["startdate"]=getSetting("ev:startdate",$category);
		$row["title"]=getSetting("ev:title",$category);
		$row["token"]=getSetting("ev:token",$category);
	}else{
		if(getEvent($row)===false){
			$row["id"]=null;
		}
        convertRecordFieldsForFrontEnd($row);
	}

}

function deleteEvent($id){
	$sql='delete from events where id='.$id;
    global $mysqli;
    $result=$mysqli->query($sql);
    if($result){
        return true;
    }
	return false;
}


function mysqlUpdateValue($i,$v)
{
    global $noupdatefields; 
    $t=columnType($i);
    if(!in_array($i,$noupdatefields)){
        if($t=="bool"){
            return "b'".($v?1:0)."'";
        }else if($v||$v=="0"){
            global $mysqli;
            return "'".$mysqli->real_escape_string($v)."'";
        }else if($t=="varchar"){
            return 'null';
        }else
            return null;
                
    }else
        return null;
}

function saveSQL(&$row)
{
    $sql="";
    $fupd=false;
    if($row["id"]){
        $sets="";
        foreach($row as $i=>$v){
            $v=mysqlUpdateValue($i,$v);
            if($v!==null){
                $sets.=($fupd?",":"").$i."=".($v=="null"?"null":$v);
                $fupd=true;
            }
        }
        if($fupd){
            $sql='update events set '.$sets.' where id='.$row["id"].";";
        }
    }else{
        $insertrow="";
        $insertvalues="";
        foreach($row as $i=>$v){
            $v=mysqlUpdateValue($i,$v);
            if($v!==null){
                $insertrow.=($insertrow?",":"").$i;
                $insertvalues.=($insertvalues?",":"").($v=="null"?"null":$v);
            }
        }
        $sql='insert into events ('.$insertrow.') values ('.$insertvalues.');set @eventid:=last_insert_id();';
    }

    return $sql;
}



function saveEvent(&$row)
{
	$user=$_SESSION["CurrentUser"];
    if(!$row["id"]){
        $row["updatedby"]=$row["createdby"]=$_SESSION["CurrentUser"];
    }else{
        $row["updatedby"]=$_SESSION["CurrentUser"];
    }

    if(isset($row["duration"]))
        $row["duration"]=convertDuration($row["duration"],true);

    $sql="set foreign_key_checks=0; start  transaction;".saveSQL($row);

	if($fNew=!$row["id"]){
	}

    $msg=array ();
    $msg["lang"]=eventLanguage($row);
    $msg["eventid"]=$row["id"]?$row["id"]:"@eventid";
    if(isset($row["title"])){
        $msg["field"]="title";
        $msg["msgtext"]=$row["title"];
        $sql.=saveMsgCodeSQL($msg);
    }

    if(isset($row["description"])){
        $msg["field"]="description";
        $msg["msgtext"]=$row["description"];
        $sql.=saveMsgCodeSQL($msg);
    }

    $sql.='delete from eventmsgcodes where msgtext="";';

    $sql.="commit; set foreign_key_checks=1;";
    global $mysqli;
    if($sql&&!$res=$mysqli->multi_query($sql)){
        return null;
    }

    do
        {
            if ($res = $mysqli->store_result())
                {
                    /* while ($savedrow = $res->fetch_row()) */
                    /* { */
                    /*     logText($savedrow); */
                    /* } */
                    $res ->close();
                }
        }
    while ($mysqli->next_result());
        
    unset($_SESSION['cache']);

	fbLint($row["category"]."?id=".$row["id"]);

    if($fNew){
        $result=$mysqli->query('select @eventid;');
        $r = $result->fetch_assoc();
        $row["id"]=$r["@eventid"];
    }
    return $row["id"];
}

function validEventListSQL(&$a) 
{
	global $singleEvent;
	global $hiddenEventTokens;
	$noshowtokens=implode('","', $hiddenEventTokens);
	$sql=$a["sql"]["validevents"]='SELECT *, startdate + INTERVAL duration MINUTE as enddate,(select msgtext from eventmsgcodes where  eventid=events.id and field="title" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'")  limit 1) as title, (select msgtext from eventmsgcodes where eventid=events.id and field="description" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") limit 1) as description  from events where ('.(adminMode()&&!isset($_GET["nodisabled"])?"true":"fenabled=0x01 ").' and (fadmin is null or fadmin=0) and (token is null or token not in ("'.$noshowtokens.'")) '.($singleEvent?" ":'and  (category in ("'.$a["category"].'") '.(!isDataPage($a["category"])?' or cssclass like "%everypage%" ':'').')').' and ('.($singleEvent&&!$a["row"]["embeddedlist"]? 'id="'.$singleEvent.'"':'startdate is null or now() <= startdate + INTERVAL duration MINUTE ').')'.($a["groupby"]&&!$a["row"]["embeddedlist"]?' and groupby="'.$a["groupby"].'"':'').')'.($a["sql_or_clause"]).' order by '.getSetting("list:orderby",null,'groupby,orderby,startdate').' '.(getSetting("list:descending",null,0)?"DESC":"").($a["count"]?' limit '.$a["count"]:'');
	if(isset($_GET["showsql"]))
		echo $sql;
}

function settingEventListSQL(&$a)
{
	if(isset($_GET["expired"])||getSetting("list:showexpiredevents",null,false))
		$a["sql"]["expired events"] = 'SELECT * from events where category="'.$a["category"].'" and fadmin=0 and  (now() > startdate + INTERVAL duration MINUTE) order by '.getSetting("list:orderby",null,'groupby,orderby,fadmin,startdate').' '.(getSetting("list:descending",null,0)?"DESC":"");
	$a["sql"]["adminonly"] = 'SELECT *, (select msgtext from eventmsgcodes where  field="title" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id limit 1) as title, (select msgtext from eventmsgcodes where field="description" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id  limit 1) as description from events where category="'.$a["category"].'" and  token=""  and fadmin=1 order by token';
	$a["sql"]["css"] = 'SELECT *,(select msgtext from eventmsgcodes where  field="title" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id limit 1) as title, (select msgtext from eventmsgcodes where field="description" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id  limit 1) as description from events where category="'.$a["category"].'" and  token="css" order by token';
	$a["sql"]["settings"] = 'SELECT *,(select msgtext from eventmsgcodes where  field="title" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id limit 1) as title, (select msgtext from eventmsgcodes where field="description" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id  limit 1) as description from events where category="'.$a["category"].'" and length(token) and token not like "%:%" and token not in ("","css","meta","embeddedlist") order by token';
	$a["sql"]["metas"] = 'SELECT *,(select msgtext from eventmsgcodes where  field="title" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id limit 1) as title, (select msgtext from eventmsgcodes where field="description" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id  limit 1) as description from events where category="'.$a["category"].'" and  token="meta" order by token';
	$a["sql"]["grouped settings"] = 'SELECT *,(select msgtext from eventmsgcodes where  field="title" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id limit 1) as title, (select msgtext from eventmsgcodes where field="description" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id  limit 1) as description from events where category="'.$a["category"].'" and token like "%:%" and category<>"msgcode" order by token';	
}

function convertRecordFieldsForFrontEnd(&$row)
{
    foreach($row as $f=>$v){
        if(columnType($f)=="bool"){
            $row[$f]=($v==1||($v&0x01))?"1":"0";
        }
    }
    $row["duration"]=convertDuration($row["duration"]);
    if(!$row["enddate"])
        $row["enddate"]=$row["startdate"];
    return $row;
}

function columnMetaData($t="events")
{
    global $mysqli;
    if(!isset($_SESSION["barwebs-columndata"][$t])){
        if(!$result = $mysqli->query('select * from '.$t.' limit 1;'))
            return null;
        $n=$result->field_count;
        for($i=0;$i<$n;$i++){
            $finfo= $result->fetch_field_direct($i);
            $_SESSION["barwebs-columndata"][$t][$finfo->name]=$finfo;
        }
    }
    return $_SESSION["barwebs-columndata"][$t];
}

function columnInfo($f,$t="events")
{
    $cm=columnMetaData($t);
    return $cm[$f];
}

function columnType($f,$t="events")
{
    $ci=columnInfo($f,$t);
    if($ci->type==16&&$ci->max_length==1)
        return "bool";
    if($ci->type==16)
        return "bitfield";
    if($ci->type==12)
        return "datetime";
    if($ci->type==253)
        return "varchar";
    if($ci->type==2)
        return "smallint";
    if($ci->type==3)
        return "int";
    if($ci->type==1)
        return "tinyint";
        
}

function columnNames($t="events")
{
    $c=columnMetaData($t);
    $cn=array ();
    foreach($c as $f=>$t){
        $cn[$f]=columnType($f);
    }
    return $cn;
}

?>