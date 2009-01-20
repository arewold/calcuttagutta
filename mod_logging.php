<?php

function write_log_entry($affected_item_id, $log_type, $event_desc){
	if(isset($_SESSION['valid_user']))
		$username = $_SESSION['valid_user'];
		
	$ip =  $_SERVER['REMOTE_ADDR'];
	
	$query = "INSERT INTO eventlog (log_type, itemid, time, event_type, username, ip) VALUES ";
	$query .= "(" . $log_type . ",'" . $affected_item_id ."', NOW(), '" . $event_desc . "','" . $username . "', '" . $ip . "');";
	
	//echo $query;
	
	$result = DB_insert($query);
	
	if($result){
		return TRUE;		
	}else{
		return FALSE;
	}
	

	
}





?>