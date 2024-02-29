
<?php

if(numEvents("footerlinks")){
	$pageHTML.=createNavigationLinks("footerlinks","footers");
}

if(getSetting("sys:developerlinkdisplay",null,true))
	$pageHTML.=libChanged();
$pageHTML.='</div>'; /* <!-- wcbg--> */

if(adminMode()){
	require_once("adminmode.php");
}

$pageHTML.='</body>';

$localLoadLibs[]="barwebs.interactive.js";

$pageHTML.=loadLibraries($loadLibs);
$pageHTML.=loadLibraries($localLoadLibs,true);

if(!getSetting("sys:disablescripts")){
        global $eventJavaScriptSnippets;
	foreach($eventJavaScriptSnippets as $index=>$js)
		$pageHTML.=$js;
}

$pageHTML.='</html>';

        if(!getSetting("sys:disablescripts")){
			$pageHTML.=getPageJS($category);
			$pageHTML.=getRaw($category);
		}
        if(@fopen("custom/site.js","r",true))
            $pageHTML.='<script  src="/custom/site.js"/>';

?>
