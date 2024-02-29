<?php

error_reporting(E_NONE);

require_once("utils.php");
checkURLCommands();

ob_start();

rememberPage($page=currentPage());

if(($newlocation=isDataPage($page))&&!adminMode()&&!$_GET["id"]){
        header("Location:".$newlocation);
}

global $_factories;
                
$row["id"]=$_GET["id"];
$singleEvent=$row["id"];

@require_once('header.php');

if(!pageAllowed($page)){
        echo("Page Guarded");
        exit;
}

if(!$singleEvent){
	if(adminMode()||getSetting("list:ajaxsearch",null,true))
                @require_once("search.php");
}

if(!adminMode()){
        $fNoWay=false;
        if(getSetting("page:adminonly")){
                $fNoWay=true;
        }else{
                foreach($_factories["adminpage"] as $p=>$v){
                        if((strpos($page,$p)===0)){
                                $fNoWay=true;
                                break;
                        }
                }
        }
        if($fNoWay){
                echo("Admin Only");
                exit;
        }
        
	if($_GET["print"])
		$pageHTML.=getPrintHeader();
	else if(!mobileMode())
        @require_once('login.php');
}

$displayRules=array();
if(isset($_GET["search"])){
    $displayRules["search"]=$_GET["search"];
}
$displayRules["jsonrecs"]=true;

$pageHTML.='<div class="centercontainer '.$page.'" id="centercontainer">';
$pageHTML.=centercontainerHTML($displayRules);
$pageHTML.='</div>'; /* centercontainer */

if(adminMode()){
	$row=array();
	populateEventObject($row);
	$pageHTML.='<script>$(".centercontainer").data("eventtemplate",'.json_encode($row).');</script>';
}

@require_once('footer.php');
echo $pageHTML;

ob_end_flush();
?>
