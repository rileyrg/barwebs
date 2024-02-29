<?php
if(adminMode())
    exit;
if(getSetting("fb:enableactivity")){
	$fb_activity=getSetting("fb:activity",null,'<fb:activity site="%s" width="300" height="300" header="true" colorscheme="dark" font="" border_color="" recommendations="false"></fb:activity>');
	if($fb_activity){
		$pageHTML.='<div class="fbactivity">'.urlSubstitutions($fb_activity).'</div>';
    }
 }

?>
