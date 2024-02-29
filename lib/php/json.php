<?php

function getJSONSetting($setting,$category=null,$lang){
	return json_encode(getSetting($setting,$category));
}

function getJSONEvent($id,$lang=null,$category=null){
	if(!$id){
		$row=array("id"=>0);
		populateEventObject($row);
		if($category)
			$row["category"]=$category;
		return json_encode($row);
	}
	$sql='select * from events where  id='.$id.(!adminMode()?' and fadmin=0':'');
    global $mysqli;
	$result = $mysqli->query($sql);
	if($result&&$result->num_rows){
		$r = $result->fetch_assoc();
		if(adminMode())
			$r[]=array("admin"=>"true");
		$json=json_encode(getEventLocaleFields($r));
		return $json;
	}
	return json_encode(array("error"=>"event not found"));
}

?>