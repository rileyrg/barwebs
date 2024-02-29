<?php
function getPrintHeader(){
    $a=getSetting("contact:address","contact");
    $e=getSetting("contact:email","contact");
    $p=getSetting("contact:phone","contact");
    $h='<div class="printheadertitle printheaderitem">'.getSetting("list:printingtitle",null,getSetting("web:h1title",null,getSetting("web:title"))).'</div>';
	$h.='<div class="printheader">';
    $h.='<div class="printheaderitem"><img class="printheadericon" src="common-images/printing/www.png"/>'.htmlspecialchars(currentPageURL()).'</div>';
    if($a)
        $h.='<div class="printheaderitem"><img class="printheadericon" src="common-images/printing/addressicon.png"/>'.htmlspecialchars($a).'</div>';
    if($e)
        $h.='<div class="printheaderitem"><img class="printheadericon" src="common-images/printing/emailicon.png"/>'.htmlspecialchars($e).'</div>';
    if($p)
        $h.='<div class="printheaderitem"><img class="printheadericon" src="common-images/printing/phoneicon.png"/>'.htmlspecialchars($p).'</div>';
    $h.= '<div class="printheaderitem qrcode"><img src="'.getSetting("ev:qricon",null,"/images/printing/qr").'"/></div>';
	$h.='</div>';
    return $h;
}

?>
