<?php
function fixedElementsHTML()
{
    $html="";
    if(getSetting("contact:opening",null,true))
        $html.='<div id="opening" class="nomobile bgmax"><a title="'.getSetting("contact:openingtitle","contact","Opening Times").'" href="'.getSetting("contact:openingurl","contact","contact").'">'.getSetting("contact:openingdescription","contact","").'<div></div></a></div>';
    if(getSetting("contact:location",null,true))
        $html.='<div id="location" class="nomobile bgmax"><a title="'.getSetting("contact:locationtitle","contact","How To Get To Us").'" href="'.getSetting("contact:contacturl","contact","contact").'">'.getSetting("contact:locationdescription","contact","").'<div></div></a></div>';
    if(getSetting("sys:hosting",null,true))
        $html.='<div id="hosting" class="nomobile shadeblack25 bgmax"><a class="" title="'.getSetting("sys:hostingtitle",null,"Hostgator Hosting").'" href="'.getSetting("sys:hostingurl",null,"http://hostgator.com").'">'.getSetting("sys:hostingdescription",null,"").'<div></div></a></div>';
    $html.='<div id="barwebslink" class="shadeblack25 bgmax"><a class="" title="'.getSetting("sys:barwebslinktitle",null,"Powered By Barwebslink").'" href="'.getSetting("sys:barwebslinkurl",null,"http://barwebs.com").'">'.getSetting("sys:barwebslinkdescription",null,"").'<div></div></a></div>';
    return $html;
}
?>