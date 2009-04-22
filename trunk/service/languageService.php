<?

function getAllLanguageNames(){
	$query = "SELECT name FROM Languages;";
	$table = getTable($query);
	
	while ($row = getNextRow($table)){
		$newtable[] = $row['name']; // Add name to array			
	}
			
	return $newtable;
}

function getAllLanguageIds(){
	$query = "SELECT id FROM Languages";
	$table = getTable($query);
	
	while ($row = getNextRow($table)){
		$newtable[] = $row['id']; // Add id to array			
	}
			
	return $newtable;
}

?>