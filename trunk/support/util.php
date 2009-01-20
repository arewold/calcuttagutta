<?

function backupToSession($array){
	$_SESSION = array_merge($_SESSION, $array);	
}


function make_ddmmyy_date($date){
// Requires SQL date/time format (2004-11-29)
	return date("d/m/y", strtotime($date));
}


function isArticleAndAlive($articleid){
	$query = "SELECT * FROM articles WHERE articleid=" . $articleid ." AND comment_to IS NULL and is_deleted IS NULL AND is_draft IS NULL;";
	$table = getTable($query);
	
	$numRows = getRowsAffected($table);
	
	if($numRows == 0)
	    return false;
	    
	return true;
		
	
}	


function makeSafeForDAO($string){
	$string = addslashes($string);	
	return $string;
}

function makeReadyForPrint($string){
	$string = stripslashes($string);	
	return $string;
}

function url_to_article($articleid){
	 		return 'index.php?articleid=' . $articleid . '&m_c=m_va"';
}

 function print_article_link($articleid, $text, $remaining = 0){
 	if ($remaining > 0){
 		echo  '<a href="index.php?articleid=' . $articleid . '&m_c=va#continue">' . stripslashes($text) . ' (' . $remaining . ' flere tegn)</a> ';
 	}else{
 		echo  '<a href="index.php?articleid=' . $articleid . '&m_c=va">' . stripslashes($text) . '</a> ';
 	}
 		
 }
 
 function print_comments_link($articleid, $text){
 		$no_comments = countCommentsOnArticle($articleid);
 		if ($no_comments != 0){
 			echo  '<a href="index.php?articleid=' . $articleid . '&m_c=va#comments">' . $text . ' (' . $no_comments . ')</a> ';
 		}
 }
 
 function print_parent_article_link($articleid){
 	$query = "SELECT title FROM articles WHERE articleid = " . $articleid . ";";
 	$table = getTable($query);
 	$row = getNextRow($table);
 	
 	echo  '<a href="index.php?articleid=' . $articleid . '&m_c=va">' . stripslashes($row['title']) . '</a>';
 }
 
function create_paragraph($text, $MAX_LENGTH, $CUTOFF = 1500){
	
	if(strlen($text) <= $MAX_LENGTH){
		return $text;	
	}

	$text = stripslashes($text);
	$textlength = strlen($text);	
	$length = min($MAX_LENGTH, $textlength);

	$p = "";
	$p = $p . strtok($text, " ");

	while (strlen($p) < $CUTOFF){
		$newtoken = strtok(" ");
		$p = $p . " " . $newtoken;	
	} 	
	
	if (strlen($p) != strlen($text)){
		$p .= "...";	
	}
	
	return $p;	
}  