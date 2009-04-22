<?php
/* Contains functions for retrieving and saving article data
 */
 
 // Store new article
 function daoCreateArticle( $articleArray ){
 		
 	$articleArray['body'] = makeSafeForDAO($articleArray['body']);
	$articleArray['title'] = makeSafeForDAO($articleArray['title']);	
	
	if($articleArray['is_draft'] != "NULL"){
		$isdraft = 1;	
	}else{
		$isdraft = "NULL";
	}
 
 		$query = "INSERT INTO articles (" .
 				"author, author_username, body, category, comment_to, " .
 				"date_posted, time_posted, intro, is_draft, is_deleted, " .
				"picture_url, priority, title, view_count, language) " .
				"VALUES ('" . 
				$articleArray['author'] . "','" . 
				$articleArray['author_username'] . "','" . 
				$articleArray['body'] . "'," .
				$articleArray['category'] . "," .
				$articleArray['comment_to'] . ",'" . 
				$articleArray['date_posted'] . "','" . 
				$articleArray['time_posted'] . "','" .
				$articleArray['intro'] . "'," .
				$isdraft . "," . 
				"NULL" . ",'" . // is_deleted 
				$articleArray['picture_url'] . "'," .
				$articleArray['priority'] . ",'" .
				$articleArray['title'] . "'," .
				$articleArray['view_count'] . "," .
				$articleArray['language'] . ");";
 		
		debug($query);
 		$result = insertRow($query);
 		
 		if($result){
 			return lastAddedID();	
 		}else{
 			return false;
 		}
 		
 }


// Update an article
 function daoUpdateArticle( $articleArray ){
 		
 	$articleArray['body'] = makeSafeForDAO($articleArray['body']);
	$articleArray['title'] = makeSafeForDAO($articleArray['title']);	

	if($articleArray['is_draft'] != "NULL"){
		$isdraft = 1;	
	}else{
		$isdraft = "NULL";
	}
 
 		$query = "UPDATE articles SET " .
 				"title='" . $articleArray['title'] . "'," . 
				"body='" . $articleArray['body'] . "'," . 
 				"category=" . $articleArray['category'] . "," . 
 				"date_posted='" . $articleArray['date_posted'] . "'," . 
 				"time_posted='" . $articleArray['time_posted'] . "'," . 
				"language=" . $articleArray['language'] . "," . 
 				"is_draft=" . $articleArray['is_draft'] . 
 				" WHERE articleid = " . $articleArray['articleid'] . ";";
  		
		debug($query);
 		$result = insertRow($query);

 		if($result){
 			return 1;	
 		}else{
 			return false;
 		}
 		
 }
 
 // Delete article 
 function daoDeleteArticle($articleid){
 	//$query = "DELETE FROM articles WHERE articleid = " . $articleid . ";";
 	
 	// Safe delete - doesn't actually remove anything from the DB
 	$query = "UPDATE articles SET is_deleted = 1 WHERE articleid = " . $articleid . ";";
 	
 	return delete($query);
  }
 
 function getArticlesDAO($query){
 	return getTable($query);
 }

function nextArticleRowDAO($table){
	return getNextRow($table);	
} 

function articlesInTable($table){
	return getRowsAffected($table);	
}
 
?>
