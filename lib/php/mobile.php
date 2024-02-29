<?php

if(mobileMode()){
    global     $localLoadLibs;
    $localLoadLibs[]="barwebs.mobile.js";
    global     $earlyLoadLibs;
    $earlyLoadLibs[]="mobile.css";
}
?>