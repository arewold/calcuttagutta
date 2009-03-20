<?php

// Contains code for UPDATE/DELETE/SELECT queries
// and connecting to the database

function getScalar($query){
	$result = getRow($query);
	if (!$result){
		return false;
	}
	return array_pop($result);
	
}


function getArray($query){
	$table = getTable($query);
	$array = array();
	
	while ($row = getNextRow($table)){
			$array[] = $row;		
	}	
	
	return $array;
}

// mysqli_searchDB(query), returnerer array med en rad
function connect(){
	global $DEBUG;
        //@$link = mysql_connect('mysql.domeneshop.no', 'calcuttagutt', 'icTrKf5');
        //@$db_selected = mysql_select_db('calcuttagutt');
	    $link = mysql_connect('localhost', 'root', '');
		$db_selected = mysql_select_db('calcuttagutta');

	if($DEBUG)
		if(!$link)
			echo "Could not connect: " . mysql_error();
		else if(!db_selected){
			echo "Could not select DB: " . mysql_error();
			$link = false;	
		}			

	return $link;
}

function delete($query){
	return insertRow($query);	
}

function getNextRow($result){
	return @mysql_fetch_assoc($result);	
}

function getRowsAffected(){
	return @mysql_affected_rows();	
}

function getNumFields($result){
	return @mysql_num_fields($result);	
}

// Returns a DB row as an associative array
function getRow($query){
	$link = connect();

	if($link){	
		// Performing SQL query
		$result = mysql_query($query);	
		if($result){
			$array = mysql_fetch_assoc($result);
			return $array;}
		else{
			return false;	
		}
		
	}else{
		return false;	
	}
}



function getTable($query){
	$link = connect();

	if($link){	
		// Performing SQL query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
		return $result;
	}else{
		return false;	
	}	
}

function lastInsertedID(){
	return mysql_insert_id();	
}

function lastAddedID(){
	return mysql_insert_id();	
}

function insertRow($query){
	$link = connect();
	
	if($link){
		$result = mysql_query($query);
		
	}else{
		return false;	
	}
	
	return $result;
}

function update($query){
	//FIXME: this method does not exist in any of the mysql-files, insertRow?
	// same goes for delete() above here. Maybe insertRow should be renamed to insert rather?
	return insertRow($query);
}

?>