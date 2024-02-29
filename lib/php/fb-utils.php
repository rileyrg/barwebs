<?php

require_once("facebook-php-sdk/src/facebook.php");

$fb_appid=null;
$fb_secret=null;

function fbInit()
{
    global $fb_appid;
    global $fb_likeid;
    global $fb_secret;
    global $fb_perms;
    $fb_appid=getSetting("fb:app_id");
    $fb_likeid=getSetting("fb:like_id",null,$fb_appid);
    $fb_secret=getSetting("fb:secret");
    $fb_perms=getSetting("fb:loginperms",null,"email,user_likes");
}

function isFBAdmin() /* doesnt mean he is logged in */
{
return false;
	if(!isset($_SESSION["fbuser"])){
		/* call auth in case this is called before a fbstatus change */
		fbAuthentication();
	}
	$isfb=  isset($_SESSION["fbuser"]) && strstr(getSetting("fb:admins"),$_SESSION["fbuser"])!=false;
	return $isfb;
}


function fbAppMetaData(){
    fbInit();
    global $fb_appid;
    global $fb_perms;
    global $fb_likeid;
    $m="";

    if($fb_appid){
        $m='<script>var fb_page="'.getSetting("fb:adminurl",null,"http://www.facebook.com").'"; var fb_perms="'.$fb_perms.'"; var fb_likeid="'.$fb_likeid.'";</script><meta property="fb:app_id" content="'.$fb_appid.'" />';
      $admins=explode(",",getSetting("fb:admins"));
      foreach($admins as $a=>$b)
          $m.='<meta property="fb:admins" content="'.$b.'" />';
    }
    return $m;
}

function fbLoginContainer()
{
    global $fb_appid;
    $s="";
    if($fb_appid && (!isset($_GET["noshare"])) && getSetting("fb:enablelike",null,true)){
        $s.='<div id="barwebs-fblogincontainer" class="barwebs-fblogincontainer noprint displayafterfbloaded">';
        $s.='<div class="registeronfacebook"><img src="'.getSetting("fb:registerimage",null,"/lib/common-images/fb/registeronfb.png").'"></div>';
        $like= urlSubstitutions('<div class="fb-like" data-href="%s" data-send="true" data-show-faces="'.(getSetting("fb:showfaces",null,true)?"true":"false").'"  data-width="300" data-stream="false" data-header="false" data-action="like" data-colorscheme="dark"></div>');
        $s.=$like;
        $s.='<div class="fbadminlink"><a href="'.getSetting("fb:adminurl",null,"http://www.facebook.com").'"><img class="fbuserimg" src="/lib/common-images/fb/fb-logged-in.gif"/></a></div>';
        $s.='</div>';
    }
    return $s;
}


function fbLint($page)
{
	if(getSetting("fb:app_id")){
		$url="https://developers.facebook.com/tools/lint/?url=http://".getSetting("sys:appdomain",null,$_SERVER['HTTP_HOST'])."/".$page;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
	}

}

function fbComments($row=null)
{
	if(!$row)
		return "";
	
	global $singleEvent;
        $href=htmlspecialchars(shareURL($row));
		if($singleEvent){
			if(getSetting("fb:app_id") && getSetting("fb:enablecomments",null,false))
				if($fb_comments=getSetting("fb:comments",null,'<div class="fb-comments" data-href="%u" data-num-posts="2" data-width="470" data-colorscheme="dark"></div>'))
					return '<div class="fbcomments">'.str_replace("%u",$href,$fb_comments).'</div>';
		}else
			return '<a href="'.$href.'"><img alt="comment" title="Comment!" src="common-images/fb/fbcomment.png" /></a>';
	return "";
}

function fbActionHTML($row,$defaction=null)
{
	$fbPlaceID=getSetting("fb:place");
	$html="";
	$ogtype=$row["ogtype"];
	if($ogActions=$row["ogactions"]){
		$arrOGActions=explode(",",$ogActions);
		foreach($arrOGActions as $index=>$act){
			$i='/images/fbactions/'.str_replace("data-","",$row["category"]).'/'.$act;
			$html.='<div onclick="fbCreateAction(\''.$act.'\',\''.$ogtype.'\',\''.shareURL($row,false).'\',\''.$row["startdate"].'\',\''.$row["duration"].'\',\''.$fbPlaceID.'\');$(this).css(\'display\',\'none\');"><a title="'.$act.'"><span class="fbactiontitle">'.$act.'</span><img class="fbactionimg" src="'.$i.'"/></a></div>';
		}
	}
	return $html;
}

function facebook()
{
    global $fb_appid;
    global $fb_secret;
	$facebook = new Facebook(
		array(
			'appId' =>$fb_appid,
			'secret'=>$fb_secret
			));

	return $facebook;
}

function fbAuthentication(){

	unset($_SESSION["facebook"]);
	unset($_SESSION["fbuser"]);
	unset($_SESSION["fbme"]);

	if(isset($_POST["fbLoginStatusChanged"])){
		$arr=json_decode($_POST["fbLoginStatusChanged"],1);
		if($authresponse=$arr["authResponse"]){
			$accesstoken=$authresponse["accessToken"];
		}else
			return false;

	}else{
		return false;
	}

	$facebook= facebook();
	if($facebook){

		$_SESSION["facebook"]=$facebook;

		if($accesstoken){
			$facebook->setAccessToken($accesstoken);
		}

		if($facebook->getUser()){
			try {
				$_SESSION["facebook"]=$facebook;
				$_SESSION["fbuser"]=$facebook->getUser();
				$_SESSION["fbme"] = $facebook->api('/me');
				return true;
			} catch (FacebookApiException $e) {
				return false;
			}
			return true;
		}
		return false;
	}
}

function fbScripts()
{
    if(adminMode())
        return "";
    global $localLoadLibs;
    $localLoadLibs[]='barwebs.fb-utils.js';
    return '<div id="fb-root"></div>';
}

function fbUpdateStats($force=false)
{
	$facebook=$_SESSION["facebook"];
	$md5=md5($facebook->getAccessToken());
	$updatedID=null;
	$sql='select * from facebookuser where userid = '.$_SESSION["fbuser"];
        global $mysqli;
	if(isset($_SESSION["fbme"])&&($result=$mysqli->query($sql))){
		if($result->num_rows==0){
			$sql='insert into facebookuser (userid,accesstoken,fullname,email,birthday) values('.$_SESSION["fbuser"].',"'.$md5.'","'.$_SESSION["fbme"]["name"].'","'.$_SESSION["fbme"]["email"].'","'.$_SESSION["fbme"]["birthday"].'")';
			$result=$mysqli->query($sql);
			$updatedID=$_SESSION["fbuser"];
		}else{
			$r=$result->fetch_assoc();
			if($force||($r["accesstoken"])!=$md5){
				$sql='update facebookuser set numlogins='.($r["numlogins"]+1).',accesstoken="'.$md5.'"'.',fullname="'.$_SESSION["fbme"]["name"].'"'.(strstr($_SESSION["fbme"]["email"],"@")?',email="'.$_SESSION["fbme"]["email"].'"':"").($_SESSION["fbme"]["birthday"]?',birthday="'.$_SESSION["fbme"]["birthday"].'"':"").' where userid='.$_SESSION["fbme"]["id"];
				$mysqli->query($sql);
				$updatedID=$_SESSION["fbme"]["id"];
			}
		}
	}
	/* if($updatedID){ */
	$result=$mysqli->query('select * from facebookuser where userid='.$_SESSION["fbme"]["id"]);
	if($result){
		return $result->fetch_assoc();
	}else
		return null;
}
?>
