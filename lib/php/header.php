<?Php
$og_namespace = getSetting("og:namespace",null, preg_replace("/\.[^$]*/","", str_replace("www.","",$_SERVER['HTTP_HOST'])));
$pageHTML='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/loose.dtd">';
$xmlns=getSetting("xmlns",null,'xmlns="http://www.w3.org/1999/xhtml" xml:lang="%LANG" lang="%LANG"');
$pageHTML.='<html '.str_replace("%LANG",getCurrentLanguage(),$xmlns).'>';
$pageHTML.='<head'.($og_namespace?' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# '.$og_namespace_.': http://ogp.me/ns/fb/'.$og_namespace_.'#"':'').'>';
$pageHTML.='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>';
$pageHTML.='<link rel="shortcut icon" href="images/favicon.ico">';

$fb_head=array();

$pageHTML.= fbAppMetaData();

$row=array();
$pagemeta="";

$description="";
$googledesc="";
$googleimg="";
$title="";
$bodymicrodata='itemscope itemtype="'.getSetting("md:pagetype",null,"http://schema.org/WebPage").'"';

if($singleEvent){

  $row["id"]=$singleEvent;
  /* We are displaying the meta data for a specific event */
  populateEventObject($row);

  $timestr=eventTimeStr($row["startdate"]);

  $title=$row["title"].($timestr?' - '.$timestr:"");
  if(!$title){
      $title==getSetting("web:title");
  }

  if($row["embeddedlist"]){
      $a=array();
      $a["row"]=$row;
      $description= embeddedListShareDescription($a,false);
  }else
      $description=$row["description"];

  if(!$description)
  $description=getSetting("web:description",null,"set web:description");
  
  if(!$image=$row["image"])
  $image=$row["embeddedlist"]? getSetting("sys:embeddedshare",null,"common-images/embeddedlistshare.jpg"):getSetting("fb:default-image",null, "common-images/default-share.png");
  

  $type=$row["ogtype"]?$row["ogtype"]:getSetting("og:eventtype",null,"article");

  $ogSettings=array (
    "og:title"=>htmlspecialchars(shareTitle($row)),
    "og:description"=>$row["fadmin"]?"secret":htmlspecialchars($description.($row["specialinfo"]?" ** ".$row["specialinfo"]." **":"")),
    "og:image"=>$googleimg=FQimageurl($image),
    "og:type"=>$og_namespace.":".$type
  );

  
  if($row["startdate"]){
    if($og_namespace){
      $ogSettings{$og_namespace.":when"}=$row["startdate"];
    }else{
      $ogSettings{"og:start_time"}=$row["startdate"];
      $ogSettings{"og:end_time"}=$row["enddate"];
    }
    if($type=="article")
    $ogSettings{"og:article:expiration_time"}=date("c",$row["enddate"]);
  }


}else{

  $title=getSetting("web:title");
  $pagemeta= getPageMetaHTML($page);
  $type=getSetting("og:pagetype");
  if($type){
    $type=$og_namespace.":".$type;
  }else{
    $type="website";
  }
  $description=htmlspecialchars(ereg_replace("\n", " ", getSetting("web:description")));
  $ogSettings=array (
    "og:title"=>htmlspecialchars($title),
    "og:description"=>$description,
    "og:image"=>$googleimg=FQimageurl(getSetting("og:image")),
    "og:type"=>$type
  );

}

$pageHTML.='<title >'.htmlspecialchars($title).'</title>';
$pageHTML.='<meta itemprop="name" name="title" content="'.htmlspecialchars($title).'"/>';
$pageHTML.='<meta itemprop="description" name="description" content="'.$description.'">';

$pageHTML.=$pagemeta;

getContactOGMeta($ogSettings,$og_namespace);


$ogSettings{"og:site_name"}=getSetting("og:site_name",null,getSetting("web:title"));
$ogSettings{"og:url"}=currentURL();

$pageHTML.=formatOGSettings($ogSettings);

$gsv=getSetting("google:site-verification");
if($gsv)
$pageHTML.='<meta name="google-site-verification" content="'.$gsv.'" />';

$bsv=getSetting("bing:site-verification");
if($bsv)
$pageHTML.='<meta name="msvalidate.01" content="'.$bsv.'" />';


$fbclass= (getSetting("fb:app_id")?" facebook":"");

$pageHTML.='
    <script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>';

$earlyLoadLibs[]="barwebs.css";

$earlyLoadLibs[]="barwebs-facebook.css";

if(adminMode())
$earlyLoadLibs[]="adminmode.css";

$pageHTML.=undercon();

if(getSetting("contact:location","contact",true))
$earlyLoadLibs[]="location.css";
if(getSetting("contact:opening","contact",true)) 
$earlyLoadLibs[]="opening.css";

if(!adminMode()&&$_GET["print"]){
  @require_once("printing.php");
  if(getSetting("sys:allowprintcss",null,true)){
    $earlyLoadLibs[]="barwebs-print.css";
    if(@fopen("custommanual/print.css","r",true))
    $earlyLoadLibs[]="custom/manual/print.css";
    if($_GET["printimages"])
    $pageHTML.='<script type="text/css">.centercontainer li.eventicon  { display:block;}</script>';
  }
}

if(@fopen("custom/site.php","r",true))
@include_once("custom/site.php");

if(getSetting("sys:hosting",null,true))
$earlyLoadLibs[]="hosting.css";
$earlyLoadLibs[]="barwebslink.css";
$earlyLoadLibs[]="barwebs-modifiers.css";

getCustomCSS();

if(mobileMode()){
    require_once("mobile.php");
}

$pageHTML.=loadLibraries($earlyLoadLibs);

$pageHTML.=getPageCSS($page);

$pageHTML.=fbScripts();

$pageHTML.='<script>og_namespace="'.$og_namespace.'";adminmode='.(adminMode()?1:0).';channelfile="//'.getDomain().'/lib/facebook/channel.html";eventlocation="'.getSetting("web:title").'"</script>';


$pageHTML.='<meta name="AUTHOR" content="Richard G. Riley"/>
	<meta name="copyright" content="Copyright Richard G. Riley 2010"/>
	</head>';

$pageHTML.='<body '.$bodymicrodata.' id="barwebs" class="barwebs '.(adminMode()?"adminmode ":"normalmode ").(mobileMode()?"mobilemode ":"notmobilemode ").($_GET["print"]?"printing ":"").($singleEvent?' singleevent':'').'">';

if(!mobileMode())
$pageHTML.=fixedElementsHTML();

checkLocal();

checkWebLicense(); /* lock  in custom stops web working */
$pageHTML.='<div id="wcbg" class="wcbg">';
if(mobileMode())
    $pageHTML.='<div title="Turn off mobile formatting" class="fadeinslow mobilemodetext mobileoff"></div>';
$pageHTML.=getFileHTML("languageselect.php");
$pageHTML.=createNavigationLinks("pages","pages");
$pageHTML.="<div>".fbLoginContainer()."</div>";

 

?>
