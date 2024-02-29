<?php
function faqHTML()
{
    $f="pagespecific/".currentPage()."/faq.txt";

    if(($t=getSetting("page:faq")) || @fopen($f,"r",true))
        {
            global     $localLoadLibs;
            $localLoadLibs[]='barwebs.faq.js';
            $mc = sysMsg("faq.closemsg","Click to close ".currentPage()." FAQ",currentPage());
            $mo = sysMsg("faq.openmsg","Click to open ".currentPage()." FAQ",currentPage());
            return '<link rel="stylesheet" href="faq.css" type="text/css"><div class="hoverbright faqcontainer"><div class="faq" title="'.$mo.'"></div><div class="faqclose" title="'.$mc.'"></div><div class="faqpopup">'.nl2br($t?$t:htmlentities(@file_get_contents($f,true),ENT_COMPAT, "UTF-8")).'</div></div>';
        }
    return null;
}
?>
