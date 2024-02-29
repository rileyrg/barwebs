<?php
require_once("constants.php");
require_once("mysqldb.php");
require_once("factoryfunctions.php");
require_once("msgcodefuncs.php");
require_once("sharing.php");
require_once("datefuncs.php");
require_once("eventfuncs.php");
require_once("fixedelements.php");
require_once("parseurl.php");
require_once("admin-utils.php");
require_once("eventguards.php");

require_once("load-libraries.php");

require_once("faq.php");
require_once("popups.php");

function webmailLoginURL()
{
    return "http://www.".str_replace("www.","",$_SERVER['HTTP_HOST']).":2095/login";
}

function mobileMode($m=null)
{

    if(adminMode())
        return false;

    static $mobilemode=null;

    if(isset($m))
        return $mobilemode=$_SESSION['mobilemode']=$m?true:false;

    if(isset($_GET["mobilemode"]))
        return $mobilemode=$_SESSION['mobilemode']=$_GET["mobilemode"]?true:false;

    if(isset($_SESSION['mobilemode']))
        return $_SESSION['mobilemode'];

    if(!is_null($mobilemode))
        return $mobilemode;

    if(!getSetting("sys:allowmobilecsstoggle",null,true))
        return $mobilemode=false;

    $useragent=$_SERVER['HTTP_USER_AGENT'];

    return $mobilemode = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
}

function np($number,$n) {
	return str_pad((int) $number,$n,"0",STR_PAD_LEFT);
}

function disableExpiredEvents()
{
	$purgeSQL='update events set fenabled=0 where fenabled=1 and startdate<>"" and (now() > startdate + INTERVAL duration MINUTE)';
    global $mysqli;
	$result=$mysqli->query($purgeSQL);
}

function deleteExpiredEvents()
{
	$purgeSQL='delete from events where fenabled=1 and fdeletewhenexpired=1 and startdate<>"" and (now() > startdate + INTERVAL duration MINUTE)';
    global $mysqli;
	$result=$mysqli->query($purgeSQL);
}

function icalDate($d,$ftime=true){
	$eventtime=strtotime($d);
	$dmy=date("Ymd",$eventtime);
	$time=$ftime?date("Hi00",$eventtime):"000000";
	return $dmy."T".$time;
}

function stripslashes_deep($value)
{
	$value = is_array($value) ?
        array_map('stripslashes_deep', $value) :
        stripslashes($value);

	return $value;
}

function convertDuration($d,$tomins=false) /* minutes to hours:mins */
{
	if($tomins){
		if(strpos($d,":")===false){
			return $d;
		}else{
			$arr = explode(':', $d);
			return $arr[0] *  60 + $arr[1];
		}
	}else{
		return strpos($d,":")===false?(intval($d/60).":".($d%60)):$d;
	}
}

function checkWebLicense()

{
	$locked=getSetting("admin:weblocked",null,null,"startdate");
	$msg=@file_get_contents("web-locked",true);
	if($msg!==false || $locked){
		echo '<div class="webadmin"><div class="web-locked">';
		echoSysMsg("admin.license.maintenance","This Web Is Currently Locked");
		if($msg)
			echo '<div class="contents">'.$msg.'</div>';
		echo '</div></div>';
		die();
	}
	
	$expires=getSetting("admin:webexpired",null,null,"startdate");
	$msg=@file_get_contents("web-expired",true);
	if($msg!==false || ($expires && (strtotime($expires) < time()))){
		echo '<div class="webadmin"><div class="web-licenseexpired">';
		echoSysMsg("admin.license.expired","This Web License Has Expired");
		if($msg)
			echo '<div class="contents">'.$msg.'</div>';
		echo '</div></div>';
		die();
	}

}

function checkLocal()
/* look for locla file and highlight web if so*/
{
	if(@fopen("custom/local","r",true))
        @require_once("developmentsite.php");
}

function undercon()
{
	$r="";
	if(@fopen("custom/undercon","r",true)){
		$r= '<link type="text/css" href="undercon.css" rel="stylesheet" title="Style Sheet"/>';
		$r.='<div title="Under Construction" id="undercon"></div>';
	}
	return $r;
}


function currentURL()
{
    if(!isset($_SERVER['REQUEST_URI'])){ $serverrequri = $_SERVER['PHP_SELF']; }else{ $serverrequri = $_SERVER['REQUEST_URI']; } $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : ""; $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s; $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); return $protocol."://".$_SERVER['SERVER_NAME'].$port.$serverrequri; 
}

function strleft($s1, $s2) { return substr($s1, 0, strpos($s1, $s2));}

function rememberPage($category=null)
{
	if(!$category){
		$category=currentPage();
	}
	global $_factories;
    return $_SESSION['lastpage']=$category;
}

/*return the last page remembered via a call to rememberPage */
function getRememberedPage()
{
	return isset($_SESSION['lastpage'])?$_SESSION['lastpage']:index;;
}

/* issue a header to return the browser to the last remembered page or page passed in*/
function returnToLastPage($category=null)
{
	$category=$category?$category:getRememberedPage();
	header("Location:".$category);
	ob_end_flush();
}

/* the userid of the user currently logged in as administrator */
function getCurrentUser()
{
	return isset($_SESSION["CurrentUser"])?$_SESSION["CurrentUser"]:NULL;
}

/* whether the user has the authority to watch a video cam */
function videoMode($mode){
	return adminMode() && hasAuthority(AUTH_VIDEO);
}

/* whether to run adverts or not*/
function advertsEnabled(){
	return adminMode()?false:true;
}

/* escape things for html */
function my_htmlentities($f)
{
	/* return $f; */
	return htmlentities($f,ENT_QUOTES,"UTF-8");
}

function mailtohtml($emailMsg,$img=null,$class=null)
{
	$imghtml=strlen($img)?('<img alt="'.altImg().'"src="'.$img.'"/>'):sysMsg("contact.email");
	$class=strlen($class)?' class="'.$class.'" ':"";
	$msgtext='<a '.$class.' href="mailto:'.sysMsg($emailMsg).'">'.$imghtml.'</a>';
	return $msgtext;
	
}

function getFileHTML($file){
    ob_start();
    require($file);
    return ob_get_clean();
}

function logText($o){
	if($handle=@fopen("/tmp/webs.log","a")){
		fwrite($handle,date("d/m/y : H:i:s",time())."\n".var_export($o,1)."\r\n");
		fclose($handle);
	}
	return $o;
}

function renderURL($url)
{
	/* if(file_exists($url.".php")) */
	/* 	$url=$url.".php"; */
	return $url;
}


function titleText($t)
{
	return htmlentities(str_replace ("<br>", " ", strip_tags($t,"<br>")),ENT_QUOTES);
}

function altImg($img="")
{
	return htmlentities("",ENT_QUOTES);
}


function createLink($url,$msgcode,$fTrans=true,$fNewBrowser=false,$image=null,$w=0,$h=0,$class=null){
	$ret="LINK UNKNOWN";
	// only put image if specified. dont put text if an image. only put title if msgcode passed.
	$size="";
	if($w>0)
		$size=' width="'.$w.'" ';
	if($h>0)
		$size=$size.' height="'.$h.'" ';

	$text=$fTrans?($msgcode?sysMsg($msgcode):""):$msgcode;
	$titleText=titleText($text);
	$bordertext=strlen($image)?strstr($image,".gif")?"":(strstr($image,".png")?"":"border"):"";
	$ret=  '<a href="'.renderURL($url).'" '.($class?' class="tick" "'.$class.'"':'') . ">" . ($image?'<img src="'.$image.'" class="imagelink'.$bordertext.'" alt="'.altImg().'" title="'.$titleText.'" '.$size.'/>':""). ($image?"":$text) . "</a>";
		
	return $ret;
}

function libChanged()
{
	$last_modified = filemtime(".");
	return '<div id="lastlibchange"><a href="'.getSetting("sys:developerlinkurl",null,"http://barwebs.com").'">'.sysMsg("admin.libchanged","Barwebs Library changed")." ".date("F j, Y, g:i a", $last_modified)."</a></div>";
}

function currentPageURL() {
    $categoryURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {$categoryURL .= "s";}
    $categoryURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $categoryURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $categoryURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    $parts=explode("?",$categoryURL);
    return $parts['0'];
}

function currentPage($ext=".php") {
	global $page;
	if(isset($category))
		return $category;
	if(isset($_GET["category"])){
		return $category=$_GET["category"];
	}
	return isset($_GET["page"])?str_replace("/","-",$_GET["page"]):"index";
}

function teamNames($s)
{
    $t=explode(" v ",$s);
    $t[0]=trim($t[0]);
    if(isset($t[1]))
        $t[1]=trim($t[1]);
    return $t;
}



function createNavigationLinks($category,$id=null){
	$a= array ();
	$a{"listid"}=$id;
	$a{"category"}=$category;
	global $_factories;
	$a{"sql"}{"default"}='select *,(select msgtext from eventmsgcodes where  field="title" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id limit 1) as title, (select msgtext from eventmsgcodes where field="description" and (lang ="'.getCurrentLanguage().'" or lang="'.getDefaultLanguage().'") and eventid=events.id  limit 1) as description from events where category="'.$category.'" and fenabled=1 '.(adminMode()?'':' and fadmin=0 ').'order by orderby';
	$a{"disallowshare"}=true;
	$a{"nonerd"}=true;
	$a{"disallowdefaultclasses"}=true;
    $a["mainclass"]="navlink hoverbrightsubtle focuscontrol";
    $a["nocalurl"]=true;
	$h=eventListHTML($a);
	return $h;
}	

function sendContactform($contactform)
{
    if(!filter_var($email=($contactform["email"]), FILTER_VALIDATE_EMAIL)){
        return false;
    }
    $sub='Web site query received from: "'.$contactform["fullname"].'"<'.$email.'>';
    $headers='From: "'.$contactform['fullname'].'" <'.$contactform['email'].'>';
    $headers.="\r\n".'reply-to: '.$email;
    $headers.="\r\n".'X-Mailer: PHP/' . phpversion();
    return  mail($contactform["to"],$sub, $contactform['emailbody'],$headers,'-f '.$email);
}

function setFilterFields(&$args){
	$_SESSION["filtertable"]=$args["factory"];
	$_SESSION["filterfields"]=$args["filterfields"];
}

function getFilter(){
	return $_SESSION["curfilter"];
}

function clearFilter(){
	setCurFilter(null);
}

function setCurFilter($v){
	return $_SESSION["curfilter"]=$v;
}

function filtersubsel(){

	$v=getFilter();

	$fFirst=true;
	$fields=explode(",",$_SESSION["filterfields"]);
	$rc="";
	if(strlen($v)){
		foreach($fields as $key=>$value){
			$rc=$rc.($fFirst?' and ':' or ').'(upper('.$value.') like "%'.strtoupper($v).'%" )';
			$fFirst=false;
		}
	}
	return $rc;
}

function title($category)
{
	echo '<div class="'.$category.'">';
}

function echoHeaderContent($type,$category="")
{
	if($category=="")
		$headerText = sysMsg("header.".$type);
	else
		if(!strlen($headerText=sysMsg("header.".$type.".".$category)))
			$headerText = sysMsg("header.".$type);

	echo	'<meta name="'.$type.'" content="'.htmlentities($headerText,ENT_QUOTES).'"/>';

}

function createDefaultSettings(){

	if(!adminMode())
		return;

	/* if($_SESSION["defaultDataDone"]){ */
	/* 	return; */
	/* } */
	
	global $_factories;

	if(isset($_factories["event"]["defaultmetas"]))
		foreach($_factories["event"]["defaultmetas"] as $category=>$list){
			foreach($list as $key=>$value){
				$args = array();
				$args{"fadmin"}=1;
				$args{"fenabled"}=1;
				$args{"fenablesharing"}=0;
				$args{"category"}=$category;
				$args{"token"}="meta";
				$args{"title"}=$key;
				$args{"event"}=$value{0};
				$args{"image"}=$value{1};
				saveEvent($args);
			}
		}
	
	
	if(isset($_factories["event"]["defaultadminsettings"]))
		foreach($_factories["event"]["defaultadminsettings"] as $category=>$list){
			foreach($list as $key=>$value){
				$args = array();
				$args{"fadmin"}=1;
				$args{"fenabled"}=1;
				$args{"fenablesharing"}=0;
				$args{"category"}=$category;
				$args{"token"}=$key;
				$args{"title"}=$key;
				$args{"event"}=$value{0};
				$args{"image"}=$value{1};
				saveEvent($args);
			}
		}
	
	if(isset($_factories["event"]["defaultvisiblesettings"]))
		foreach($_factories["event"]["defaultvisiblesettings"] as $category=>$list){
			foreach($list as $key=>$value){
				$args = array();
				$args{"fadmin"}=0;
				$args{"fenabled"}=1;
				$args{"fenablesharing"}=1;
				$args{"category"}=$category;
				$args{"token"}=$key;
				$args{"title"}=$key;
				$args{"event"}=$value{0};
				$args{"image"}=$value{1};
				saveEvent($args);
			}
		}
	
}

function FQimageurl($img)
{
	if(strpos($img,"http")!==0){
		$img="http://".$_SERVER["HTTP_HOST"]."/".$img;
	}
	return $img;
}

function startsWith($haystack,$needle,$case=true) {
    if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
    return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
}

function endsWith($haystack,$needle,$case=true) {
    if($case){return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);}
    return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
}


function getSettings($setting,$category=null,$def=null,$num=-1,$global=true)

{
	if($category==null){
		$category=currentPage();
	}

	if($setting[strlen($setting)-1]==":"){
		/* e.g og: or fb: */
		$restriction= 'like "'.$setting.'%"';
	}else{
		$restriction= '="'.$setting.'"';
	}
	
	$sql='select * from events where  token '.$restriction.' and fenabled=1 and (category="'.$category.'" '.($global?'or category="settings"':'').') order by category<>"'.$category.'", groupby,orderby';

	$settings=array();
	
    global $mysqli;
	if(($result=$mysqli->query($sql))==false){
    	$settings{0}{"category"}=$category;
		$settings{0}{"description"}=$def;
	}else{
	
		
		if($result->num_rows==0){
			$settings{0}{"category"}=$category;
			$settings{0}{"setting"}=$def;
		}else{
			$r=Array();
			$i=0;
			while($num && ($r=$result->fetch_assoc())){
				if(!$r["setting"]){
					getEventLocaleFields($r);
				}else{
					/* use the setting field value  : no multi langauge */
				}
				$settings{$i++}=$r;
				if($num!=-1)
					$num--;
			}
		}
	}
	
	return $settings;
}

function getSetting($setting,$category=null,$def=null,$field="description",$global=true)
{
    
    $usegeneral=true;

	if(!$category){
		$category=currentPage();
	}else
        $usegeneral=false;

	$lang=getCurrentLanguage();

	if(!isset($_SESSION['cache'])){
		$_SESSION['cache']=false;
		$enablecache=getSetting("sys:cachesettings",null,1);
		$_SESSION['cache']=$enablecache?true:false;
	}
	/* if($_SESSION['cache']===true) */
	/* 	$_SESSION['cache']=array(); */
	/* else if(isset($_SESSION['cache'][$category][$lang][$setting])){ */
	/* 	return $_SESSION['cache'][$category][$lang][$setting]; */
	/* } */

	   
    $r=array();
	$r=getSettings($setting,$category,$def,1,$global);
	if(isset($r{0}{"setting"})){
		$field="setting";
	}
	$value=isset($r{0}{$field})?$r{0}{$field}:"";
	if(is_array($_SESSION['cache'])){
		$_SESSION['cache'][$category][$lang][$setting]=$value?$value:"";
	}
	return $value;
}

function isClass(&$r,$c)
{
	return strpos($r{"cssclass"},$c)===false?false:true;
}

function alwaysDisplay(&$r)
{
	return isset($_GET["ajaxfilter"])?false:isClass($r,"always");
}


/* dummy func to wrap data in sql builder */
function nef($s){
	return $s;
}

function saveImage($event)
{
    global $mysqli;
	$stmt = $mysqli->prepare("INSERT INTO images (image) VALUES (?)");
	$null = NULL;
	$data = file_get_contents("~/messages.txt");
	$stmt->bind_param("b", $data);
	$stmt->execute();
}

function getContactOGMeta(&$ogSettings,$ns="og:")
{
	$ogSettings{$ns.":contact:email"}=getSetting("contact:email","contact");
	$ogSettings{$ns.":contact:phone_number"}=getSetting("contact:phone","contact");
	$ogSettings{$ns.":contact:street_address"}=ereg_replace("\n", ",", getSetting("contact:address","contact"));
	$ogSettings{$ns.":contact:locality"}=getSetting("og:locality");
	$ogSettings{$ns.":contact:region"}=getSetting("og:region");
	$ogSettings{$ns.":contact:country_name"}=getSetting("og:country-name");
	$ogSettings{$ns.":contact:postal_code"}=getSetting("og:postal-code");
	$ogSettings{$ns.":contact:website"}=currentURL();

	if($ll=getSetting("og:latlong")){
        $a = explode(",",$ll);
		$ogSettings{$ns.":location:latitude"}=$a[0];
		$ogSettings{$ns.":location:longitude"}=$a[1];
	}

}

function formatOGSettings(&$ogSettings)
{
	$metas="";
	foreach($ogSettings as $meta=>$content){
		$metas.='<meta property="'.$meta.'" content="'.htmlspecialchars($content).'" />';
	}
	return $metas;

}

function popupInfo($t=null,$pt=null,$u=null,$img=null,$spanclass=null)
{
	if(!$pt)
		$pt=$t;
	$html='<a class="info tooltip" '.($u?'href="'.$u.'"':"").' title="'.htmlspecialchars($pt,ENT_QUOTES).'">'.($img?'<img src="'.$img.'" />':"").'</a>';
	return $html;
}

function motd($s="")
{
	if($s){
		echo '<div id="motd"><a>('.$s.')</a></div>';
	}else{
		global $motd;
		if($motd)
			echo '<div id="motd"><a>('.$motd.')</a></div>';
		if($motd=getSetting("sys:motd"))
			echo '<div id="motd"><a>('.$motd.')</a></div>';
	}
}
function clearEditWidget()
{
    return "";
	return '<div class="icon clearedit editmod"></div>';
}

function eventTimeStr($startdate)
{
	return $startdate?date("D, j F @ H:i",strtotime($startdate)):null;
}


function getFiles($ext,$dir)
{
    $files=array();
    $dir = opendir($_SERVER["DOCUMENT_ROOT"].$dir); 
    while ($f = readdir($dir)){
        if( strpos($f,"flymake")===false && preg_match('/^[a-z0-9]*.'.$ext.'$/',$f) )
            $files[$f]=true;
    }
    ksort($files);
    return $files;
}

function getCustomCSS(){
    global $earlyLoadLibs;
    $files= getFiles("css",'/custom/css');
    foreach($files as $key=>$value){
        $earlyLoadLibs[]='/custom/css/'.$key;
    }
}

$tz=getSetting("sys:sql-timezone",null,'+1:00');
$mysqli->query('set time_zone = "'.$tz.'";');
$tz=getSetting("sys:php-timezone",null,'Europe/Berlin');
date_default_timezone_set($tz);

?>
