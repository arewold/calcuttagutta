<?php
/*
 * Created on 29.okt.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function listCommentsSearchService($author){
	$select = "SELECT * "; 	
	$from = " FROM articles";
	$where = " WHERE is_deleted IS NULL AND is_draft IS NULL AND comment_to IS NOT NULL AND author_username = '" . $author . "' ORDER BY date_posted DESC, time_posted DESC;";

	$query = $select . $from . $where; 
	
	$table = getTable($query);
	$num_rows = getRowsAffected($table);
	
	if($table && $num_rows > 0){
		return $table;
	}else{
		return NULL;	
	}	
}

function nextResultInTable($table){
	return getNextRow($table);	
}

function monthSearchService($month, $year){
	$month = $_REQUEST['month'];
	$year = $_REQUEST['year'];
	$query = "SELECT * FROM articles WHERE date_posted LIKE '" . $year . "-" . $month . "-%' AND is_deleted IS NULL AND comment_to IS NULL AND is_draft IS NULL ORDER BY date_posted ASC, time_posted ASC;	";
	$table = getTable($query);
	$num_rows = getRowsAffected($table);
	
	if($table && $num_rows > 0){
		return $table;
	}else{
		return NULL;	
	}
}
	
function textSearchService($text, $partialmatch, $author, $checkcomments){
	if (strlen($text) < 3)
		return NULL;

	$text = makeSafeForDAO($text);
	
	$selectfrom = "SELECT * FROM articles ";
	$where = "WHERE body LIKE '%" . $text . "%' AND is_deleted IS NULL AND is_draft IS NULL ";
	$orderby = " ORDER BY date_posted DESC, time_posted DESC;";
	
	$findWordAlone = "| " . $text . "[ !?,.:;'/)]|i";
	$findWordAnywhere = "|" . $text . "|i";
		
	if ($partialmatch == 0){
		$pattern = $findWordAlone;
	}else{
		$pattern = $findWordAnywhere;
		
	}
	
	if ($author == "0"){
		
	}else{
		$where .= " AND author_username = '" . $author . "' "; 
	}
	
	if ($checkcomments == "0"){
		$where .= " AND comment_to IS NULL";	
	}
	
	$query = $selectfrom . $where . $orderby;
	
	$newtable = array();
	
	$table = getTable($query);
	$num_rows = getRowsAffected($table);
	

	if($table && $num_rows > 0){
		while ($row = getNextRow($table)){
			if (preg_match($pattern, $row['body'])){
				$newtable[] = $row; // Add row to array			
			}
		}
		return $newtable;
	}else{
		return NULL;	
	}
	
}

?>
