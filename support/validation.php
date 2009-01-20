<?php
/*
 * Created on 25.okt.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

// Creates HTML links from URLs embedded in $string
function createLinks($string){
	//<a href="www.gamespy.com">gamespy!</a>
	$pattern = '!(\A|[\s]|")www.!';
	$string = preg_replace($pattern, '$1http://www.', $string);
	
	$pattern2 = '!(\A|[^"=])http://[A-Za-z0-9/\\.?&=#_]*!';
	$string = preg_replace($pattern2, '<a href="$0">$0</a>', $string);
	return $string;
}

function categoryExists($category){
	// Requires categorydao.php
	return true;
}
 
function is_aToZLower($string){
	$invalid = preg_match("|[^a-z0-9]+?|", $string)	;
	if($invalid){
		echo $string . " was found!!!!!!!!!!!!!!!!!!!";	
	}
	return !$invalid;	
}

function isBoolean($value){
	if (($value == 1) || ($value == 0))
		return true;
	return false;	
}

function is_valid_alphanum($string){
	return justTextAndNumbers($string);	
}

function justTextAndNumbers($string){
// Will return false if contains anything but nice letters
	$invalid = ereg("[^[:alnum:]?!:; æøåÆØÅ\"\'\\\(\)/äöÄÖ%.,_-]", $string)	;
	return !$invalid;
}

function mayCreateArticles($user){
	// Requires userdao.php
	return true;	
}

/* Strictly speaking not necessary now - we restrict input
 * in the GUI */
function validDate($date){
	return true;	
}

/* Strictly speaking not necessary now - we restrict input
 * in the GUI */
function validTime($time){
	return true;
}	

function validURL($url){
	// Not implemented yet	
	return true;
}

function isLoggedIn(){
	if(isset($_SESSION['valid_user'])){
		return true;	
	}else{
		return false;
	}		
}

function isValidAdmin() {
	if(isset($_SESSION['valid_admin']) && isLoggedIn()){
		return true;
	} else {
		return false;	
	}
}

?>
