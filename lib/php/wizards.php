<?php

global     $localLoadLibs;
$localLoadLibs[]='barwebs.wizards.js';

/* scan a directory and read in the contents of all .json files */
function directoryWizards($d,&$o,$p="wizardbasic")
{
    $filedir=$_SERVER["DOCUMENT_ROOT"]."/".$d;
    $h = opendir($filedir); 
    $files=array();
    while ($f = readdir($h)){
        if(strpos($f,"flymake")===false){
            $files[$f]=true;
        }
    }
    ksort($files);
    foreach($files as $key=>$value){
        $f=$d."/".$key;
        if (preg_match('/\.json$/i',$key)){
            $wizkey=preg_replace('/\.[^.]*$/', '', $key);
            $c=file_get_contents($f,true);
            $wizobj=json_decode($c,true);
            if(!$wizobj["wizardclass"])
                $wizobj["wizardclass"]=$p;
            if($wizobj["category"]=="*")
                $wizobj["category"]=currentPage();
            $wizobj->title->en=$p;
            foreach($wizobj["fields"] as $i=>$v){ /* process @settings values */
                if(strpos($v["value"],"@")===0){
                    $setting=substr($v["value"],1);
                    $sv = getSetting($setting,$wizobj["fields"]["category"]["value"],null,"description",false);
                    $wizobj["fields"][$i]["value"] = $sv;
                    if(!$sv && !$wizobj["optional"])
                        $wizobj["shouldhave"] = true;
                    $wizobj["fields"]["id"]["value"] = getSettingKey($setting);
                    $wizobj["fields"]["id"]["type"] = "hidden";
                    $wizobj["fields"]["category"]["value"] = $wizobj["fields"]["category"]?$wizobj["fields"]["category"]["value"]:currentPage();
                    $wizobj["fields"]["category"]["type"] = "hidden";
                    $wizobj["fields"]["token"]["value"] = $setting;
                    $wizobj["fields"]["token"]["type"] = "hidden";
                }
            }

            $o[$wizkey]=$wizobj;
        }
    }
}

/* get all the wizards valid on the specified page */
function wizardJSON($p)
{
    $o=array();
    if($p=="settings"){
                directoryWizards("lib/wizards/settings",$o,"wizardpage");
    }else{
        directoryWizards("lib/wizards",$o,"wizardsys");
        directoryWizards("custom/wizards",$o,"wizardlocal");
        directoryWizards("custom/wizards/".$p,$o,"wizardlocal");
    }
    return json_encode($o);
}

function metaWizardJSON($p)
{
    $o=array();
    if($p=="settings"){
    }else{
        directoryWizards("lib/wizards/google",$o,"wizardgoogle");
        directoryWizards("lib/wizards/contact",$o,"wizardcontact");
    }
    return json_encode($o);
}

?>

<script type="text/javascript">currentuser="<?php echo getCurrentUser();?>";wizardjson=<?php echo wizardJSON(currentPage());?>;metawizardjson=<?php echo metaWizardJSON(currentPage());?>;</script>
