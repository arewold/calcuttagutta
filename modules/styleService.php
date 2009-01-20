<?php
include("../dao/mysql_dao.php");

function delStyle(){
	$styleid = $_REQUEST['styleid'];
	
	if(!userMayRemove($styleid)){
		//return false;
	}	
	
	$query = "DELETE FROM stylesheets WHERE styleid = " . $styleid . ";";
	echo $query;
	return delete($query);
}

function setStyle(){
	$styleid = $_REQUEST['styleid'];
	$user = $_SESSION['valid_user'];
	$query = "UPDATE user SET styleid =" . $styleid . " WHERE username='" . $user . "';";
	return insertRow($query);
}

function userMayRemove($styleid){
	$query = "SELECT styleid from user where styleid = " . $styleid . ";";
	$table = getArray($query);

	// The style is in use, do not permit deletion
	if($table == null){
		return true;	
	}else{
		return false;
	}
	
}

function getList(){
	$query = "SELECT s.creator as username, s.name as name, u.firstname as creator, s.styleid as stylesheet, u.firstname FROM stylesheets s, user u WHERE s.creator = u.username;";
	$table = getArray($query);	
	
	foreach ($table as $row){
		if ($row['stylesheet'])
			echo "-" . $row['name'] . " (". $row['creator'] . ")-" . $row['stylesheet'];
			if (userMayRemove($row['stylesheet'])){
				echo "-1";
			}else{
				echo "-0";
			}
	
	}
}

function addStyle(){
	$stylesheet = $_REQUEST['styleURL'];	
	$user = $_SESSION['valid_user'];
	$name = $_REQUEST['styleName'];
	$query = "INSERT INTO stylesheets(name,creator,url) VALUES ('". $name . "','" . $user . "','" . $stylesheet . "');";
	//$query = "UPDATE user SET stylesheet='". $stylesheet . "' WHERE username='" . $user . "'";
	echo $query;
	return insertRow($query);
}

session_start();
$action = $_REQUEST['do'];

if ($action == "fetch"){
	echo "success"; 
	getList();
}else if ($action == "add"){
	if (addStyle()){
		echo "success";	
		getList();
	}else{
		echo "failure";
	}
}else if ($action == "setStyle"){
	if (setStyle() == null){
		echo "failure";	
	}else{
		echo "success";
	}
}else if ($action == "delStyle"){
	if (delStyle() == null){
		echo "failure";	
	}else{
		echo "WHATsuccess";
	}
}else {
	echo "no task!";
}


?>