<?php

/* function addArticle(){
 * Get values from session or form()
 * Validate values()
 *    resultsarray = errors;
 *    Return resultsarray;
 * 
 * Add to database
 *    Return resultsarray;
 *
 */
 
function deleteArticleFromSession(){
	unset($_SESSION['save_attempted']);
	unset($_SESSION['newbody']);	
	unset($_SESSION['newtitle']);
	unset($_SESSION['body']);
	unset($_SESSION['title']);
	unset($_SESSION['time_posted']);
	unset($_SESSION['date_posted']);
			
} 
 
 
function buildArticleArray($fromArray){
	
	if (isset($fromArray['year']) && isset($fromArray['month']) && isset($fromArray['day'])){
		$articleArray['year'] = $fromArray['year'];
		$articleArray['month'] = $fromArray['month'];
		$articleArray['day'] = $fromArray['day'];
		$articleArray['date_posted'] = $fromArray['year'] . "-" . $fromArray['month'] . "-" . $fromArray['day'];	
	}else if (isset($fromArray['date_posted'])){
		$articleArray['date_posted'] = $fromArray['date_posted'];
	}else{
		$articleArray['year'] = date("Y");
		$articleArray['month'] = date("m");
		$articleArray['day'] = date("d");
		$articleArray['date_posted'] = $articleArray['year'] . "-" . $articleArray['month'] . "-" . $articleArray['day'];	
	}
	
	
	if ( isset($fromArray['hours']) && isset($fromArray['minutes']) ){
		$articleArray['hours'] = $fromArray['hours'];
		$articleArray['minutes'] = $fromArray['minutes'];		
		$articleArray['time_posted'] = $fromArray['hours'] . ":" . $fromArray['minutes'];
	}else if (isset($fromArray['time_posted'])){
		$articleArray['time_posted'] = $fromArray['time_posted'];
	}else{
		$articleArray['hours'] = date("H");
		$articleArray['minutes'] = date("i");
		$articleArray['time_posted'] = $articleArray['hours'] . ":" . $articleArray['minutes'];	
	}	
	


	
	if (isset($fromArray['author_username'])){
		$articleArray['author_username'] = $fromArray['author_username'];
		$articleArray['author'] = $fromArray['author'];
	}else{
		if ( isset ($_SESSION['valid_user'])) {
			$articleArray['author_username'] = $_SESSION['valid_user'];	
			$articleArray['author'] = $_SESSION['user_firstname'];
		}else{
			$articleArray['author_username'] = 0;
			$articleArray['author'] = 0;
		}
	}
	
	if (isset ($fromArray['category'])){
		$articleArray['category'] = $fromArray['category'];		
	}else{
		$articleArray['category'] = 0;	
	}


	if (isset ($fromArray['picture_url'])){
		$articleArray['picture_url'] = $fromArray['picture_url'];		
	}else{
		$articleArray['picture_url'] = "";	
	}


	if (isset ($fromArray['priority'])){
		$articleArray['priority'] = $fromArray['priority'];		
	}else{
		$articleArray['priority'] = 0;	
	}


	if (isset ($fromArray['view_count'])){
		$articleArray['view_count'] = $fromArray['view_count'];		
	}else{
		$articleArray['view_count'] = 0;	
	}

	if (isset ($fromArray['is_draft'])){
		if ($fromArray['is_draft'] == "on" || $fromArray['is_draft'] == "1"){
			$articleArray['is_draft'] = 1;	
		}else{
			$articleArray['is_draft'] = "NULL";
		}
	}else{
		$articleArray['is_draft'] = "NULL";
	}

	if (isset ($fromArray['title'])){
		$articleArray['title'] = $fromArray['title'];		
	}else{
		if (isset ($_SESSION['newtitle'])){
			 $articleArray['title'] = $_SESSION['newtitle'];	
		}else{
			$articleArray['title'] = "";
		}	
	}
	
	if (isset ($fromArray['body'])){
		$articleArray['body'] = ($fromArray['body']);		
	}else{
		if (isset ($_SESSION['newbody'])){
			$articleArray['body'] = $_SESSION['newbody'];
		}else{
			$articleArray['body'] = "";
		}	
	}



	
	if (isset ($fromArray['comment_to'])){
		if (strlen($fromArray['comment_to']) == 0){
			$articleArray['comment_to'] = "NULL";
		}else{
			$articleArray['comment_to'] = $fromArray['comment_to'];	
		}				
	}else{
		$articleArray['comment_to'] = "NULL";	
	}
	
	if (isset ($fromArray['intro'])){
		$articleArray['intro'] = $fromArray['intro'];		
	}else{
		$articleArray['intro'] = "";	
	}
	
	if (isset ($fromArray['articleid'])){
		$articleArray['articleid'] = $fromArray['articleid'];		
	}else{
		// CONVENTION: -1 means this is a new article.
		$articleArray['articleid'] = "-1";	
	}

		
	return $articleArray;	
}

function getComments($articleid){
	$select = "SELECT * FROM articles ";
	$where = " WHERE comment_to = " . $articleid . " AND is_deleted IS NULL ORDER BY date_posted ASC, time_posted ASC; ";
	$query = $select . $where;
	return getArray($query);
}

function getAnyArticle($articleid){
	$select = "SELECT * FROM articles ";
	$where = " WHERE articleid = " . $articleid . " AND is_deleted IS NULL; ";
	$query = $select . $where;
	return getArray($query);
}


// Fetches a given article from the DB, provided it isn't 
// a draft, deleted or a comment 
function getValidArticle($articleid){
	$select = "SELECT * FROM articles ";
	$where = " WHERE articleid = " . $articleid . " AND is_deleted IS NULL AND is_draft IS NULL AND comment_to IS NULL; ";
	$query = $select . $where;
	return getArray($query);
}

function getFrontpageArticles($limit){

	
 	$select = "SELECT * ";
 	$from = " FROM articles ";
 	 
 	$where = "WHERE is_draft IS NULL AND is_deleted IS NULL AND comment_to IS NULL ";
 	$timecompare = " AND (date_posted <= '" . date("Y-m-d") . "' OR (time_posted <= '" . date("H:i") . "' AND date_posted <= '" . date("Y-m-d") . "')) ";
 	$orderby = " ORDER BY date_posted DESC, time_posted DESC LIMIT ".$limit .";";
	$query = $select . $from . $where . $timecompare . $orderby;
	return getArray($query);	
	
}

function getArticlesByAuthor($username){
	$select = "SELECT * FROM articles ";
	$where = " author_username = '" . $username . "' AND is_deleted IS NULL AND is_draft IS NULL AND comment_to IS NULL ";
	$orderby = " ORDER BY date_posted DESC, time_posted DESC;";
	
	$query = $select . $where . $orderby;
	
	$array = getArray($query);
	
	foreach ($array as $key => $value){
		
	} 
	
}

function ownerMayDeleteThisComment($id){
	
	
	
}

function mayDeleteArticle($articleid){
	if (isset($_SESSION['valid_admin'])){
		return 1;
	}else if (isset ($_SESSION['valid_user'])){
		if ($_SESSION['valid_user'] == getAuthorOfArticleUsername($articleid)){
			return 1;
		}	
	}
	return 0;	
}

function userMayPost($articleArray){
	if (!isset($_SESSION['valid_user'])){
		return false;	
	}
	

	// -1 means new article, else this is an edit operation
	if ($articleArray['articleid'] == "-1"){
		// New post - user must have posting rights or be a valid user
		if ($articleArray['comment_to'] == "NULL"){
						
			$query = "SELECT may_post FROM user WHERE username = '" . $_SESSION['valid_user'] . "';";

			$array = getArray($query);

			if ($array[0]['may_post'] == "1"){
				debug ("new post, user may post");
				return true;
			}else{
				debug ("new post, user not post");
				return false;
			}
				
		// This is a valid user, so comments are fine		
		}else{
			return true;
		}			
		
	}else{
		if (($articleArray['author_username'] == $_SESSION['valid_user']) OR isset($_SESSION['valid_admin'])){
			debug ("user is admin or owns post (edit)");
			return true;	
		}else{
			debug ("user not admin or owner (edit)");
			return false;
		}		
	}




	
}


function verifyArticle($articleArray){
	$feedback = array();
	
	if($articleArray['comment_to'] == "NULL"){
		if (strlen($articleArray['title']) < 2)
			$feedback[] = getString("title_is_required", "Tittelen mangler.");
	}
		
	if (strlen($articleArray['body']) < 2)
		$feedback[] = getString("body_is_required", "Teksten mangler.");
	
	if (!justTextAndNumbers($articleArray['author']))
		$feedback[] = "author_just_text_and_numbers";
	
	if (!isLoggedIn($articleArray['author_username']))
		$feedback[] = "user_does_not_exist";
			
	if (!mayCreateArticles($articleArray['author_username']))
		$feedback[] = "user_cannot_post_article";
		
	if (!categoryExists($articleArray['category']))
		$feedback[] = "category_does_not_exist";
		
	if (!validDate($articleArray['date_posted']))
		$feedback[] = "invalid_date";
		
	if (!validTime($articleArray['time_posted']))
		$feedback[] = "invalid_time";

	//if (!isBoolean($articleArray['is_draft']))
		//$feedback[] = "isdraft_must_be_boolean";
		
	if (!validURL($articleArray['picture_url']))
		$feedback[] = "invalid_url";
		
	// More than 0 errors, return now	
	// First element in array must be -1 to signal error
	if (count($feedback) > 0){
		array_push($feedback, "-1");
		return array_reverse($feedback);		
	}

	$feedback[] = "1";
	
	return $feedback;
}



function addArticle($articleArray){
	
	if (isset($articleArray['articleid'])){
		if ($articleArray['articleid'] > 0){

			$result = daoUpdateArticle(	$articleArray );
		}else{
			$result = daoCreateArticle(	$articleArray );
		}		
	} else {
		$result = daoCreateArticle(	$articleArray );	
	}
			
		
	
	if($result){
		global $logtype; global $eventdesc;	
		$feedback[] = "creating_article_successful";
		$feedback[] = $result;	
		if (isset($articleArray['articleid']) && ($articleArray['articleid'] > 0)){	
			write_log_entry($articleArray['articleid'], $logtype['article'], "editarticleORcomment,");
		}else{
		    write_log_entry(lastAddedID(), $logtype['article'], "createarticleORcomment,");
		}
			
	}else{
		$feedback[] = "creating_article_failed";
		$feedback[] = "-1";
	}

	return array_reverse($feedback);		
}


function countCommentsOnArticle($articleid){
	 $query = "SELECT COUNT(articleid) FROM articles WHERE is_deleted IS NULL AND comment_to =" . $articleid . ";";
	$result = getScalar($query);
	return $result;
}


/* function updateArticle(){
 * Get values from session or form()
 * Validate values()
 *    resultsarray = errors;
 *    Return resultsarray;
 * 
 * Add to database
 *    Return resultsarray;
 *
 */
 
  
/* deleteArticle
 * Check that an article exists, attempts to remove it,
 * and returns an array containing feedback to the user / GUI.
 */
 
 function deleteArticleService($articleid){
 	// Array for storing i18n strings used by GUI
 	$feedback = array();
 	
	if (!article_exists($articleid)){
		$feedback[] = "0";
		$feedback[] = "no_such_article"; 		
		return $feedback;
	}	
 	
 	if (daoDeleteArticle($articleid)){
 		$feedback[] = "1";
 		$feedback[] = "article_deleted";	

		// Log this deletion
	    global $logtype; global $eventdesc;
	    write_log_entry($articleid, $logtype['article'], "deletearticle,");
 		
 	}else{
 		$feedback[] = "0";
 		$feedback[] = "article_not_deleted";
 	}
 	return $feedback;
 }
 

?>
