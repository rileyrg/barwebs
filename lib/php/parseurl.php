<?php
require_once("json.php");

function getDomain()
{
    return str_replace("www.","",getSetting("sys:appdomain",null,$_SERVER['HTTP_HOST']));
}

function urlSubstitutions($s)
{
	$s = str_replace("%p",currentPage(),str_replace("%s",getSetting("fb:adminurl",null,getDomain()) ,$s));
	return $s;
}


function ajaxFilter($category="index",$search=null)
{
	$a=array();
	$a["search"]=$search;
	$a["category"]=$category;
	$html= centercontainerHTML($a);
	return $html;
}

function checkURLCommands()
{
	global $row;

	if(isset($_GET["isadmin"])){
		$i=isAdmin()?1:0;
		echo json_encode($i);
		exit;
	}
	
	if(isset($_GET["ajaxfilter"])){
		echo ajaxFilter($_GET["category"],$_GET["seach"],$_GET["settings"]);
		exit;
	}

	if(isset($_GET["jsonscheduledevents"])){
        $recs=array();
		echo getScheduledEvents($recs,$_GET["lang"]?$_GET["lang"]:getDefaultLanguage());
        echo json_encode($recs);
		exit;
	}
	
	if(isset($_GET["resetcache"])){
		unset($_SESSION['cache']);
		header("Location:".$row["category"]);
	}

    if(isset($_POST["contactform"])){
        sleep(5);
        $res=array();
        if($_POST['captcha'] != $_SESSION['captcha_answer']) {
            $res["success"]=false;
            $res["response"]=sysMsg("contactform.checkfailed","Security check didn't match. Try again");
            echo json_encode($res);
            exit;
        }
        $cf=json_decode($_POST["contactform"],1);
        $cf["to"]=getSetting("contact:email","contact");
        if($res["success"]=sendContactForm($cf))
            $res["response"]=sysMsg("contactform.sent","Your query was submitted");
        else
            $res["response"]=sysMsg("contactform.notsent","Sorry, there was an error sending your question");
        
        echo json_encode($res);
        exit;
    }

	if(isset($_GET["mobilemode"])||isset($_POST["mobilemode"]))
        mobileMode(isset($_GET["mobilemode"])?$_GET["mobilemode"]:$_POST["mobilemode"]);

	if(isset($_POST["fbLoginStatusChanged"])){
		$response=json_decode($_POST["fbLoginStatusChanged"],1);
		fbAuthentication();
        $r=array();
		if($response["authResponse"]){
			$r=fbUpdateStats();
			$r["fbuser"]=$_SESSION["fbuser"];
			$r["fbadmin"]=isFBAdmin();
			$r["adminmode"]=adminMode()?1:0;
            $r["loggedintext"]=getSetting("fb:loggedintext",null,"Please Connect");
		}else
            {
            }
        echo json_encode($r);
		exit;
	}

	if(isset($_GET["jsonsetting"])){
		echo getJSONSetting($_GET["jsonsetting"],$_GET["page"]);
		exit;
	}

	if(isset($_GET["msgcode"])){
		echo json_encode(sysMsg($_GET["msgcode"],"No Message Text Found : ".$_GET["msgcode"],null,false,$_GET["lang"]));
		exit;
	}
	
	if(isset($_GET["jsonevent"])){
		echo getJSONEvent($_GET["jsonevent"],$_GET["lang"],$_GET["category"]);
		exit();
	}
	
	if(adminMode()){
	
		if(isset($_GET["setting"])){
            echo getSetting($_GET["setting"]);
			exit;
		}
		
		if(isset($_POST["saveeventrec"])){
			$ev=json_decode($_POST["saveeventrec"],1);
            saveEvent($ev);
            $response=array();
            $response["id"]=$ev["id"];
            $response["html"]=$_POST["fullhtml"]?ajaxFilter(isset($_POST["category"])?$_POST["category"]:$ev["category"],$_POST["search"],$_POST["settings"]):displayEvent($ev);
            echo json_encode($response);
            exit;
        }

 		if(isset($_GET["delete"])&&hasAuthority(AUTH_DELETE)){
			deleteEvent($_GET["delete"]);
			exit;
		}
	
        if(isset($_GET["edited"])){
            if(isset($_GET["newlang"])){
                $row=array();
                $row["id"]=$_GET["edited"];
                $row["lang"]=$_GET["newlang"];
                getEventLocaleFields($row);
                eventLanguages($row);
                echo json_encode($row);
                exit;
            }
        }
	}

	if(isset($_GET["newlang"])){
		setCurrentLanguage($_GET["newlang"]);
        exit;
	}
        
	if(isset($_GET["login"])){
		if(fbAuthentication()){
			if(isAdmin()){
				setAdminMode("anon",array ( 0 => AUTH_ALL));
			}
		}
	}

	if(isset($_GET["logout"])){
		logout();
	}
		
	
}
?>