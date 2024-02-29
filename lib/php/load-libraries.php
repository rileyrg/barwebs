<?php
function loadLibraries($arrLibs,$forceOffline=false)
{
    $libs="";
    $forceOffline=$_GET["offline"]||$forceOffline||@fopen("custom/offlinejs","r",true)||@fopen("lib/offlinejs","r",true);
    
    foreach($arrLibs as $online=>$offline){
        $lib=$forceOffline||!@fopen($online,'r')?$offline:$online;
    	$ext=pathinfo($lib,PATHINFO_EXTENSION);
    	if($ext=="css"){
    	    $libs.='<link  type="text/css" href="'.$lib.'" rel="stylesheet"/>';
    	}else if ($ext="js"){
    	    $libs.='<script type="text/javascript" src="'.$lib.'"></script>';
    	}else{
    	    /* whoops */
    	}
    }
    return $libs;
}

$earlyLoadLibs = array( 
	(isset($_GET["debug"])?'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js':'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js')=>(isset($_GET["debug"])?'lib/js/jquery-ui/js/jquery-1.9.1.js':'lib/js/jquery-ui/js/jquery-1.9.1.js'),
    'http://code.jquery.com/jquery-migrate-1.2.1.min.js'=>'lib/js/migrate/jquery-migrate-1.2.1.min.js',
	);
	  
$loadLibs = array( 
	'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/'.getSetting("jq:ui-theme",null,"sunny").'/jquery-ui.css'=>'lib/js/jquery-ui/css/'.getSetting("jq:ui-theme",null,"sunny").'/jquery-ui.css',
	'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.js'=>'lib/js/jquery-ui/js/jquery-ui-1.10.3.custom.min.js',
	'http://cdn.jsdelivr.net/jquery.cookie/1.1/jquery.cookie.js'=>'lib/js/jquery-cookie/jquery.cookie.js',
    'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js'=>'jquery-validate/jquery.validate.min.js',
    "jquery-countdown/jquery.countdown.css"=>'',
    'http://s7.addthis.com/js/300/addthis_widget.js#pubid='.addThisID()=>''
	);

$localLoadLibs =array(
	'barwebs.scripts.js',
	'barwebs.calendars.js',
    'jquery.icalendar/jquery.icalendar.pack.js',
    'lib/js/jquery.icalendar/jquery.icalendar.css',
    'jquery-countdown/jquery.countdown.js'
);
?>