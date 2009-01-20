<?
// Encoding: UTF-8

// mysqli_searchDB(query), returnerer array med en rad
function searchDB($query){

	$result = mysqli_searchDB($query);	
	if(!$result)
		return null;
		
	$row = $result->fetch_assoc(); 
	return $row;
}
// returnerer hele resultat-objektet
function get_resultsDB($query){
	return mysqli_searchDB($query);	
}

function insertDB($query){
return mysqli_insertDB($query);	
}

function updateDB($query){
	return mysqli_insertDB($query);	
}


// Executes query and returns associative array with results
function mysqli_searchDB($query){
	global $DEBUG;
	$db = connect_db();
	$result = $db->query($query);
	
	if($result->num_rows == 0){
		$db->close;
		if($DEBUG) echo $query;
		echo "Database search gave no results. Please try again.";
		return null;			
	}
	
	return $result;
}

// Run insert or update, returns number of rows affected
function mysqli_insertDB($query){
	global $DEBUG;
	$db = connect_db();
	$result = $db->query($query);
	
	
	if($db->errno != 0){
		if($DEBUG) echo $query;
		echo ("Databasefeil - la klagene strømme! :) <br/>" . $db->errno);		
		return null;
	}
	
	
	if(($db->affected_rows == 0)){
		$db->close;	
		if($DEBUG) echo $query;
		echo ("Ingen data ble endret.");
		return null;	
	}
	
	$db->close;
	return $db->affected_rows;	
}

function connect_db(){

	@$db = new mysqli('localhost', 'root', 'dummy', 'calcuttagutta');
	
  	if (mysqli_connect_errno())
	  {
	     echo ("Kunne ikke koble til database.");
	     return null;
	  }

	return $db;
}

function valid_query($query){
	
	
}


?>