<?
function getAuthorOfArticle($articleid){
	$select = "SELECT firstname ";
	$from = " FROM articles, user ";
	$where = " WHERE articles.author_username = user.username AND articleid=" . $articleid .";"; 
	$query = $select . $from . $where;
	$userArray = getArray($query);
	
	if(!$userArray){
		$query = "SELECT author FROM articles WHERE articleid = " . $articleid .";";
		$userArray = getArray($query);
		if (!$userArray){
			return -1;
		}else{
			return $userArray[0]['author'];
		}
			
	}else{
		return $userArray[0]['firstname'];
	}
	
}

function getAuthorOfArticleUsername($articleid){
	$select = "SELECT username ";
	$from = " FROM articles, user ";
	$where = " WHERE articles.author_username = user.username AND articleid=" . $articleid .";"; 
	$query = $select . $from . $where;
	$userArray = getArray($query);
	
	if(!$userArray){
		return -1;	
	}else{
		return $userArray[0]['username'];
	}
	
}

function getAllAuthorsUsernames(){
	$query = "SELECT username FROM user WHERE may_post = 1 ORDER BY firstname ASC";
	$table = getTable($query);
	
	while ($row = getNextRow($table)){
		$newtable[] = $row['username']; // Add username to array			
	}
			
	return $newtable;
}

function getAllAuthorsNames(){
	$query = "SELECT firstname FROM user WHERE may_post = 1 ORDER BY firstname ASC";
	$table = getTable($query);
	
	while ($row = getNextRow($table)){
		$newtable[] = $row['firstname']; // Add name to array			
	}
			
	return $newtable;
}

function getAllUsersUsernames(){
	$query = "SELECT username FROM user ORDER BY firstname ASC";
	$table = getTable($query);
	
	while ($row = getNextRow($table)){
		$newtable[] = $row['username']; // Add username to array			
	}
			
	return $newtable;
}


function getAllUsersNames(){
	$query = "SELECT firstname FROM user ORDER BY firstname ASC";
	$table = getTable($query);
	
	while ($row = getNextRow($table)){
		$newtable[] = $row['firstname']; // Add name to array			
	}
			
	return $newtable;
}

?>