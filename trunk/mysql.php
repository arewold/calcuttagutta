<?php
// mysqli_searchDB(query), returnerer array med en rad
function DB_connect(){
	return connect();
}

function searchDB($query){
	return DB_search($query);		
}

// Returns a DB row as an associative array
function DB_search($query){
	$link = DB_connect();

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

function DB_next_row($result){
	return @mysql_fetch_assoc($result);	
}

function DB_next_row_numeric($result){
	return @mysql_fetch_array($result);	
}

function DB_rows_affected(){
	return @mysql_affected_rows();	
}

function DB_num_fields($result){
	return @mysql_num_fields($result);	
}

function DB_get_table($query){
	$link = DB_connect();

	if($link){	
		// Performing SQL query
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());	
		return $result;
	}else{
		return false;	
	}	
	
}



// returnerer hele resultat-objektet
function get_resultsDB($query){
	
	
}

function insertDB($query){
	return DB_insert($query);	
}

function DB_insert($query){
	$link = DB_connect();
	
	if($link){
		$result = mysql_query($query);
		
	}else{
		return false;	
	}
	return $result;
}




function DB_update($query){
	return DB_insert($query);
}


function updateDB($query){
	return DB_update($query);	
}

?>