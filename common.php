<?php
//require_once("mod_pick_style.php");
require_once("page_modules.php");
require_once("mysql.php");
require_once("forms.php");
require_once("mod_articles.php");
require_once("mod_admininput.php");
require_once("mod_poll.php");
require_once("mod_memberlist.php");
require_once("mod_menu.php");
require_once("dao/articledao.php");
require_once("dao/mysql_dao.php");
require_once("dao/settingsdao.php");
require_once("dao/i18ndao.php");
require_once("gui/articleGUI.php");
require_once("gui/searchGUI.php");
require_once("gui/settingsGUI.php");
require_once("support/html.php");
require_once("support/util.php");
require_once("support/validation.php");
require_once("modules/searchService.php");
require_once("modules/userService.php");
require_once("modules/articleService.php");
require_once("modules/settingsService.php");
require_once("modules/i18nService.php");

function closeUnclosedTags($unclosedString){
	$tags = array('<i>', '<b>', '<div>', '<br>', '<a>');
	// created by Adam Gundry, http://www.agbs.co.uk
	preg_match_all("/<([^\/]\w*)>/", $closedString = $unclosedString, $tags);
	for ($i=count($tags[1])-1;$i>=0;$i--){
		$tag = $tags[1][$i];
		if (substr_count($closedString, "</$tag>") < substr_count($closedString, "<$tag>")) 
			$closedString .= "</$tag>";
	}
	return $closedString;
}

/* Checks if the user is logged in or has a valid cookie */
function is_logged_in(){
	if(isset($_SESSION['valid_user'])){
		return true;
		
	}elseif (isset($_COOKIE['kengu10']) && isset($_COOKIE['kengu100'])){
		/* Superfluous code - module_login is run before this, so valid_user is already
		 * set in the session data. */
		   	if(login($_COOKIE['kengu10'],$_COOKIE['kengu100'])){
		   		// Login with cookie info successful
		   		$username = $_COOKIE['kengu10'];
		        $_SESSION['valid_user'] = $username;
		        $loginfeedback = "loggedin";  

			    $row = DB_search("SELECT * FROM user WHERE username=\"" . $username . "\";");		
				if($row['admin'] == 1){
				    $_SESSION['valid_admin'] = $username;
				    $loginfeedback = "adminloggedin";
				}		
				
				if($row['may_post'] == 1){
					$_SESSION['user_may_post'] = 1;	
	
				}
				
				$_SESSION['user_firstname'] = $row['firstname'];
				//echo "COOKIELOGIN GOOD";
				return true;
				
		   	}else{
		   		// Login failed
			    setcookie("kengu10", $username, time()-60*60*24*100);
			    setcookie("kengu100", $row['password'], time()-60*60*24*100);
			    return false;
		   	}		
		
	}else{
		return false;	
	}
	
	
	
}

function fix_quotes($string){
	$string = str_replace('"', "&quot;",$string);
	$string = str_replace("'", "&#39;",$string);
	return $string;
}

function increment_view_count($articleid){
	$query = "UPDATE articles SET view_count = view_count+1 WHERE articleid =" . $articleid . ";";
	$result = DB_update($query);


}


function approved_func($functionname){
	global $approved_functions;
	
	if (is_callable($functionname)){	
		if(in_array($functionname, $approved_functions)){			
			return true;			
		}				
	}	
	return false;	
}

function number_of_comments($articleid){
	$query = "SELECT COUNT(articleid) AS count FROM articles WHERE is_deleted IS NULL AND comment_to=" . $articleid . ";";
	$row = DB_search($query);
	return $row['count'];
	
}

// Returns the amount in hours:minutes:seconds of given timestamp
function GetTime ($timedifference) {

   if ($timedifference >= 3600) {
       $hval = ($timedifference / 3600);
       $hourtime = intval($hval);

       $leftoverhours = ($timedifference % 3600);

       $mval = ($leftoverhours / 60);
       $minutetime = intval($mval);

       $leftoverminutes = ($leftoverhours % 60);
       $secondtime = intval($leftoverminutes);

       $hourtime = str_pad($hourtime, 2, "0", STR_PAD_LEFT);
       $minutetime = str_pad($minutetime, 2, "0", STR_PAD_LEFT);
       $secondtime = str_pad($secondtime, 2, "0", STR_PAD_LEFT);

       return "$hourtime:$minutetime:$secondtime";
   }

   if ($timedifference >= 60) {

       $hourtime = 0;

       $mval = ($timedifference / 60);
       $minutetime = intval($mval);

       $leftoverminutes = ($timedifference % 60);
       $secondtime = intval($leftoverminutes);

       $hourtime = str_pad($hourtime, 2, "0", STR_PAD_LEFT);
       $minutetime = str_pad($minutetime, 2, "0", STR_PAD_LEFT);
       $secondtime = str_pad($secondtime, 2, "0", STR_PAD_LEFT);

      return "$hourtime:$minutetime:$secondtime";


   
   $hourtime = 0;
   $minutetime = 0;
   if ($timedifference < 0 ) { $secondtime = 0; }
   else {    $secondtime = $timedifference; }

   $hourtime = str_pad($hourtime, 2, "0", STR_PAD_LEFT);
   $minutetime = str_pad($minutetime, 2, "0", STR_PAD_LEFT);
   $secondtime = str_pad($secondtime, 2, "0", STR_PAD_LEFT);

   return "$hourtime:$minutetime:$secondtime";
   
}
}






function article_exists($articleid){

	$query = "SELECT * FROM articles WHERE articleid=" . $articleid .";";
	$row = DB_search($query);
	
	if(!$row || $row['is_deleted'] == '1')
	    return false;
	    
	return true;
}

// Converts SQL date (YYYY-MM-DD) to norwegian format DD/MM/YYYY
function date_nor_sql($date){
	return date("d/m/Y", strtotime($date));	
}


function make_date($date){
// Requires SQL date/time format (2004-11-29)
	return date("d/m/y", strtotime($date));
}

function make_time($time){
// Requires 14:59:59, will give 14:59
	return substr($time,0,5);
}




function do_article_form(){
	?>
	<form action="index.php" method="post">

    Author: <input name="author" type="text"><br/>
    Title: <input name="title" type="text"><br/>
    Intro: <input name="intro" type="text"><br/>
    Text body: <br/><textarea name="body" cols="30" rows="10" value="cheese"></textarea><br/>
    Date: <input name="date_posted" type="text" value=<? echo date("Y-m-d"); ?>><br/>
    Time: <input name="time_posted" type="text" value=<? echo date("H:i"); ?>><br/>
    Priority: <input name="priority" type="text"><br/>
    <br />
    <input type="submit" value="Add article">
    <input type="hidden" name="m_c" value="module_add_article" />
  	</form>
  	
  	<?

}




function do_logout_button(){
	form_start_get();
	form_hidden("logmeout", "logout");
	form_submit("logout", "Logg ut");
	form_end();
}

function do_login_button(){
?>(<a href="login.php">log in</a>)<?
}

function do_greeting(){
	global $loginfeedback;
	if (isset($_SESSION['valid_user'])){
		//do_logout_button();
		echo '<span class="welcome_msg"><b>' . $_SESSION['valid_user'] . "</b> - innlogget</span>";

		
	} else {
		
		if($loginfeedback == "failed"){
			echo '<span class="goodbye_msg" id="errorandlogout">';
			echo 'Innlogging slo feil. Dobbeltsjekk brukernavn og passord.</span>';
		}else if($loginfeedback == "loggedout"){
			echo '<span class="goodbye_msg" id="errorandlogout">';
			echo 'Du har logget ut.</span>';
		}else
			;
			
		echo '<span id="loginform" class="login_form">'; print_login_form(); echo '</span>';
		
	}
}

function do_html_url($url, $name){
 echo "<a href=\"$url\">$name</a>";
}

function htmlhead(){
	global $usermenu, $adminmenu, $freemenu, $css_file;
	?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
		
	<html>
	<head>
	<link rel="shortcut icon" href="/pics/favicon.ico" type="image/x-icon" />

	<!-- rss recognition -->
	<link rel="alternate" type="application/rss+xml" title="Calcuttagutta RSS Feed" href="http://www.calcuttagutta.com/rss.xml" />

	<?php
	if(isset($_REQUEST['redirect'])){
		echo '<script src="support/common.js" language="javascript" type="text/javascript">';
		echo '</script>';		
	}
	
	?>
	
	
	<script src="support/common.js" language="javascript" type="text/javascript">
	
	</script>
	
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<? echo $css_file; ?>" >
	<? if (isset($title)) {
	    echo "<title>$title</title>";
	}else{
	    echo "<title>Gutta fra Calcutta</title>";
	}
	?>

	</head>
	<?	
}

function module_html_header_admin ($title)
{
	global $usermenu, $adminmenu, $freemenu, $css_file;
	htmlhead();
	?>
	
	<body>
	<div class="banner">
	<h1><a href="index.php">Gutta fra Calcutta</a></h1>
	<?
	$query = "SELECT count(articleid) AS no_articles FROM articles WHERE is_deleted IS NULL AND comment_to IS NULL;";
	$result = DB_search($query);
	echo "<span>NÃ¥ med " . $result['no_articles'] . " artikler om spennende ting</span>";
	?>
	
	</div>

	<?php do_greeting(); 
	
}


function module_html_header ($title)
{
	module_html_header_admin($title);
}













function valid_email($address)
{

  // check an email address is possibly valid
  if (ereg('^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$', $address))
    return true;
  else
    return false;
}

function do_html_footer(){
	echo '<!-- Google analytics kodesnutt -->';
	echo '<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">';
	echo '</script>';
	echo '<script type="text/javascript">';
	echo '_uacct = "UA-305664-1";';
	echo 'urchinTracker();';
	echo '</script>';

	// slutt paa siden
	echo "</body></html>";

}


// Uploads file from form. Assumes file info in $_FILES['picturepath']
// Returns the file ID if successful or 
// -1 == file too large
// -2 == local (client) file name invalid
// -3 == no filename given
// -4 == db insert failed
// -5 == failed convertion
function file_upload($description){
	global $netpbmpath;
	global $pic_max_height;
	global $pic_max_width;
	
	if(!isset($description))
		$description = "no";
	
	global $max_image_size;
	if(isset($_FILES['picturepath'])){
		if(is_uploaded_file($_FILES['picturepath']['tmp_name'])) {
	        if($_FILES['picturepath']['size'] > $max_image_size) {
				return -1;
	        }
		} else {
			if ($_FILES['picturepath']['name'] <> "") {
				return -2;
			}
		}
	}else{
		return -3;
	}
	
	if(strlen($_FILES['picturepath']['tmp_name']) < 1){
		return -3;	
	}
	
	if($_FILES['picturepath']['error'] == UPLOAD_ERR_NO_FILE){
		return -3;
	}


	$tmpname = $_FILES['picturepath']['tmp_name'];
	$size = getimagesize($tmpname);
	if ($size[0] > $pic_max_width || $size[1] > $pic_max_height) {
		$command = $netpbmpath . 'anytopnm ' . $tmpname . ' | ' . $netpbmpath . 'pamscale -xyfit ' . $pic_max_width . ' ' . $pic_max_height . ' | ' . $netpbmpath . 'pnmtojpeg >' . $tmpname . '.jpg';

		$commandresult = system($command, $retval);
		
		if($retval == 1){
			//echo "Whhops, fila ble ikke korrekt lastet opp, mangler path, eller ukjent filtype.";
			//echo "Feilmelding: " . $commandresult . "<br/>";
			return -5;
		} else {
			$imgData = addslashes(file_get_contents($tmpname . '.jpg'));
			$size = getimagesize($tmpname . '.jpg');
		}
	} else {
		$imgData = addslashes(file_get_contents($tmpname));
	}

	if($imgData == FALSE) {
		return -3;
	}
	
	//FIXME: addslashes og flytting til common.php etterhvert
    $sql = "INSERT INTO files (data, description, filename, size, pic_width, pic_height, filetype) VALUES ('" . $imgData . "', '" . $description . "', '" . $_FILES['picturepath']['name'] . "', " . $_FILES['picturepath']['size'] . ", " . $size[0] . ", " . $size[1] .", '" . $size['mime'] . "')";
   	$result = DB_insert($sql);
    if (!$result) {
		return -4;
    } else {
    	$sqlresult = mysql_insert_id();
    	return $sqlresult;
    }
}

function saveuser($username, $password, $email, $firstname, $lastname, $webpage, $birthdate, $description, $admin, $may_post){
// Saves changes to user in DB.
	if(!isset($username)){
		$username = $_SESSION['valid_user'];	
	}
	
	if($password){
	    $password = sha1($password);
		$query = "UPDATE user SET password=\"$password\", birthdate=\"$birthdate\",email=\"$email\", firstname=\"$firstname\", lastname=\"$lastname\", webpage=\"$webpage\", description=\"$description\", admin=\"$admin\", may_post=\"$may_post\" WHERE username=\"$username\";";
	}else{
		
		$query = "UPDATE user SET email=\"$email\", birthdate=\"$birthdate\",firstname=\"$firstname\", lastname=\"$lastname\", webpage=\"$webpage\", description=\"$description\", admin=\"$admin\", may_post=\"$may_post\" WHERE username=\"$username\";";

	}

	$result = DB_insert($query);

	return true;
}

//FIXME: addslashes over alt!
function register($username, $email, $password, $firstname, $lastname, $webpage, $birthdate, $description)
// register new person with db
// return true or error message
{
	$username = addslashes($username);
	$email = addslashes($email);
	$password = addslashes($password);
	$firstname = addslashes($firstname);
	$lastname = addslashes($lastname);
	$webpage = addslashes($webpage);
	$birthdate = addslashes($birthdate);
	$description = addslashes($description);
	
  // check if username is unique
  $result = DB_search("select * from user where username='$username'");

  if (DB_rows_affected($result) > 0){
    echo ('Brukernavnet er opptatt. GÃÂ¥ tilbake og velg et annet.');
  }else{
	  // if ok, put in db

	  $result = DB_insert("insert into user (username, password, email, firstname, lastname, webpage, birthdate, description, admin, may_post) VALUES
	                         ('$username', sha1('$password'), '$email', '$firstname', '$lastname', '$webpage', '$birthdate', '$description', 0,0)");
	  if (!$result){	
	  	echo "Registrering mislyktes. Ta kontakt med en administrator.";
	  	echo "Feilmelding: " . mysql_error();
		return false;
	  }
	  return true;
  }
}

function login($username, $password){

  	
	if($password){
		if(strlen($password) > 16)
			$result = DB_search("select * from user
                         where username='$username'
                         and password = ('$password')");
        else
  			$result = DB_search("select * from user
                         where username='$username'
                         and password = sha1('$password')");
	}else{
		$result = DB_search("select * from user
                         where username='$username'
                         and password = \"\"");
	}
	
  if (!$result)
     return false;

  if (DB_rows_affected() >0)
     return true;
     
     return false;
}



function change_password($username, $old_password, $new_password)
// change password for username/old_password to new_password
// return true or false
{
  // if the old password is right
  // change their password to new_password and return true
  // else throw an exception
  login($username, $old_password);
  $conn = connect_db();
  $result = $conn->query( "update user
                            set passwd = sha1('$new_password')
                            where username = '$username'");
  if (!$result){
    echo('Password could not be changed.');
    return false;}
  else
    return true;  // changed successfully
}

function get_random_word($min_length, $max_length)
// grab a random word from dictionary between the two lengths
// and return it
{
   // generate a random word
  $word = '';
  // remember to change this path to suit your system
  $dictionary = '/usr/dict/words';  // the ispell dictionary
  $fp = @fopen($dictionary, 'r');
  if(!$fp)
    return false;
  $size = filesize($dictionary);

  // go to a random location in dictionary
  srand ((double) microtime() * 1000000);
  $rand_location = rand(0, $size);
  fseek($fp, $rand_location);

  // get the next whole word of the right length in the file
  while (strlen($word)< $min_length || strlen($word)>$max_length || strstr($word, "'"))
  {
     if (feof($fp))
        fseek($fp, 0);        // if at end, go to start
     $word = fgets($fp, 80);  // skip first word as it could be partial
     $word = fgets($fp, 80);  // the potential password
  };
  $word=trim($word); // trim the trailing \n from fgets
  return $word;
}

function reset_password($username)
// set password for username to a random value
// return the new password or false on failure
{
  // get a random dictionary word b/w 6 and 13 chars in length
  $new_password = get_random_word(6, 13);

  if($new_password==false)
    echo ('Could not generate new password.');
  // add a number  between 0 and 999 to it
  // to make it a slightly better password
  srand ((double) microtime() * 1000000);
  $rand_number = rand(0, 999);
  $new_password .= $rand_number;

  // set user's password to this in database or return false
  $conn = connect_db();
  $result = $conn->query( "update user
                          set passwd = sha1('$new_password')
                          where username = '$username'");
  if (!$result)
    echo ('Could not change password.');  // not changed
  else
    return $new_password;  // changed successfully
}

function notify_password($username, $password)
// notify the user that their password has been changed
{
    $conn = connect_db();
    $result = $conn->query("select email from user
                            where username='$username'");
    if (!$result)
    {
      echo ('Could not find email address.');
    }
    else if ($result->num_rows==0)
    {
      echo ('Could not find email address.');   // username not in db
    }
    else
    {
      $row = $result->fetch_object();
      $email = $row->email;
      $from = "From: support@phpbookmark \r\n";
      $mesg = "Your PHPBookmark password has been changed to $password \r\n"
              ."Please change it next time you log in. \r\n";


      if (mail($email, 'PHPBookmark login information', $mesg, $from))
        return true;
      else
        echo ('Could not send email.');
    }
}

// Must be on front page index.html
function module_login(){
	if(isset($_COOKIE['kengu10']))
		$returning_user = $_COOKIE['kengu10'];
	
	if(isset($_REQUEST['username']))
		$username = $_REQUEST['username'];
		
	if(isset($_REQUEST['password']))
		$password = $_REQUEST['password'];
		
	if(isset($_REQUEST['logmeout']))	
		$logout = $_REQUEST['logmeout'];
		
	if(isset($_REQUEST['registration']))	
		$registration = $_REQUEST['registration'];
	
	if(isset($_REQUEST['remember'])){	
		$remember = $_REQUEST['remember'];
		
	}else{
		$remember = "";
	}
		
	global $loginfeedback;
	
	if (isset($username) && !isset($registration) && isset($password)){
		$username = strip_tags($username);
		$password = strip_tags($password);
		
	    if(login($username, $password)){
		    $_SESSION['valid_user'] = $username;	    
		    $row = DB_search("SELECT * FROM user WHERE username=\"" . $username . "\";");		
			if($row['admin'] == 1){
			    $_SESSION['valid_admin'] = $username;
			    $loginfeedback = "adminloggedin";
			}		
			
			if($row['may_post'] == 1){
				$_SESSION['user_may_post'] = 1;	

			}
			
			if($remember == "on"){
			    setcookie("kengu10", $username, time()+60*60*24*100);
			    setcookie("kengu100", $row['password'], time()+60*60*24*100);				
			}
				
			$_SESSION['user_firstname'] = $row['firstname'];
			$loginfeedback = "loggedin";   	
	    }else{
	    	$loginfeedback = "failed";
	    }	    

	}

	else if (isset($logout)) {
		setcookie("kengu10", "", time()-3600);
		setcookie("kengu100", "", time()-3600);	
		$_SESSION = array();
		session_destroy();
		$loginfeedback = "loggedout";


	} else if (isset($returning_user) && !isset($_SESSION['valid_user'])) {
   		if(isset($_COOKIE['kengu10']) && isset($_COOKIE['kengu100'])){
		   	if(login($_COOKIE['kengu10'],$_COOKIE['kengu100'])){
		   
		   		// Login with cookie info successful
		   		$username = $_COOKIE['kengu10'];
		        $_SESSION['valid_user'] = $username;
		        $loginfeedback = "loggedin";  

			    $row = DB_search("SELECT * FROM user WHERE username=\"" . $username . "\";");		
				if($row['admin'] == 1){
				    $_SESSION['valid_admin'] = $username;
				    $loginfeedback = "adminloggedin";
				}		
				
				if($row['may_post'] == 1){
					$_SESSION['user_may_post'] = 1;	
	
				}
				
				$_SESSION['user_firstname'] = $row['firstname'];
				
		   	}else{
		   		// Login failed
			    setcookie("kengu10", $username, time()-60*60*24*100);
			    setcookie("kengu100", $row['password'], time()-60*60*24*100);
		   	}
	   	}	

	}else{
		$loginfeedback = "nothinghappening";
		
	}
}


function print_login_form(){

		form_start_post("form_login");
				form_label("Brukernavn:");
				form_textfield("username",""); 
			
				form_label("Passord:"); 
				form_password("password", ""); 
				
				
				// Returns us to the same module (usually an article view)
				// when the login sequence is completed
				if (isset($_REQUEST['m_c']))
					form_hidden("m_c", $_REQUEST['m_c']);
				
				if (isset($_REQUEST['articleid']))
					form_hidden("articleid", $_REQUEST['articleid']);
					
				form_hidden("logging_in","set");
				form_label("Husk meg: ");
				form_checkbox("remember", "yes", "0");
				form_submit("login", "Logg inn");
		form_end();
}




// PHP Word Wrap routine
// Version 1.0.1, Jan 5th 2004
// Copyright 2004 Kohan Ikin
// syneryder@namesuppressed.com
// http://www.namesuppressed.com/syneryder/

// This software is provided 'as-is', without any express or implied
// warranty.  In no event will the author be held liable for any damages
// arising from the use of this software.

// Permission is granted to use this code for any purpose, but its origin
// must not be misrepresented (ie you must not claim you wrote the code or
// the accompanying tutorial).  If you use this code I would appreciate a
// short email explaining where and how the code is being used, but you
// don't have to if you're too shy :)

// Version History
// 1.00  2000.12.22  Initial release
// 1.01  2004.01.05  Fixed a bug causing tokens that evaluate
//                   to zero to be truncated.  Thanks to Joe
//                   Pfeiffer at New Mexico State University
//                   Computer Science Department and Dave Holle
//                   for alerting me to the problem.



/* **************************************************************
* htmlwrap() function - v1.1
* Copyright (c) 2004 Brian Huisman AKA GreyWyvern
*
* This program may be distributed under the terms of the GPL
*   - http://www.gnu.org/licenses/gpl.txt
*
*
* htmlwrap -- Safely wraps a string containing HTML formatted text (not
* a full HTML document) to a specified width
*
*
* Changelog
* 1.1  - Now optionally works with multi-byte characters
*
*
* Description
*
* string htmlwrap ( string str [, int width [, string break [, string
* nobreak [, string nobr [, bool utf]]]]])
*
* htmlwrap() is a function which wraps HTML by breaking long words and
* preventing them from damaging your layout.  This function will NOT
* insert <br /> tags every "width" characters as in the PHP wordwrap()
* function.  HTML wraps automatically, so this function only ensures
* wrapping at "width" characters is possible.  Use in places where a
* page will accept user input in order to create HTML output like in
* forums or blog comments.
*
* htmlwrap() won't break text within HTML tags and also preserves any
* existing HTML entities within the string, like &nbsp; and &lt;  It
* will only count these entities as one character.  Output is auto-
* matically nl2br()'ed.
*
* The function also allows you to specify "protected" elements, where
* line-breaks, block-returns or both are not inserted.  This is useful
* for elements like <pre> where you don't want the code to be damaged
* by the insertion of HTML block-returns.  Add the names of the
* elements you wish to protect from line-breaks (nobreak) and/or block-
* returns (nobr) as space separated lists.  Only names of valid HTML
* tags are accepted.  (eg. "code pre blockquote")
*
* The optional "utf" parameter enables the function to treat multi-
* byte characters in UTF-8 as single characters.  The default is false.
* "This modifier is available from PHP 4.1.0 or greater on Unix and
* from PHP 4.2.3 on win32."
*  - http://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
*
* htmlwrap() will *always* break long strings of characters at the
* specified width.  In this way, the function behaves as if the
* wordwrap() "cut" flag is always set.  However, the function will try
* to find "safe" characters within strings it breaks, where inserting a
* line-break would make more sense.  You may edit these characters by
* adding or removing them from the $lbrks variable.
*
* htmlwrap() is safe to use on strings containing multi-byte
* characters as of version 1.1.
*
* See the inline comments and http://www.greywyvern.com/php.php
* for more info
******************************************************************** */

function htmlwrap($str, $width = 60, $break = "\n", $nobreak = "", $nobr = "pre", $utf = false) {

  // Split HTML content into an array delimited by < and >
  // The flags save the delimeters and remove empty variables
  $content = preg_split("/([<>])/", $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

  // Transform protected element lists into arrays
  $nobreak = explode(" ", $nobreak);
  $nobr = explode(" ", $nobr);

  // Variable setup
  $intag = false;
  $innbk = array();
  $innbr = array();
  $drain = "";
  $utf = ($utf) ? "u" : "";

  // List of characters it is "safe" to insert line-breaks at
  // Do not add ampersand (&) as it will mess up HTML Entities
  // It is not necessary to add < and >
  $lbrks = "/?!%)-}]\\\"':;";

  // We use \r for adding <br /> in the right spots so just switch to \n
  if ($break == "\r") $break = "\n";

  while (list(, $value) = each($content)) {
    switch ($value) {

      // If a < is encountered, set the "in-tag" flag
      case "<": $intag = true; break;

      // If a > is encountered, remove the flag
      case ">": $intag = false; break;

      default:

        // If we are currently within a tag...
        if ($intag) {

          // If the first character is not a / then this is an opening tag
          if ($value{0} != "/") {

            // Collect the tag name   
            preg_match("/^(.*?)(\s|$)/$utf", $value, $t);

            // If this is a protected element, activate the associated protection flag
            if ((!count($innbk) && in_array($t[1], $nobreak)) || in_array($t[1], $innbk)) $innbk[] = $t[1];
            if ((!count($innbr) && in_array($t[1], $nobr)) || in_array($t[1], $innbr)) $innbr[] = $t[1];

          // Otherwise this is a closing tag
          } else {

            // If this is a closing tag for a protected element, unset the flag
	            if (in_array(substr($value, 1), $innbk)) unset($innbk[count($innbk)]);
            if (in_array(substr($value, 1), $innbr)) unset($innbr[count($innbr)]);
          }

        // Else if we're outside any tags...
        } else if ($value) {

          // If unprotected, remove all existing \r, replace all existing \n with \r
          if (!count($innbr)) $value = str_replace("\n", "\r", str_replace("\r", "", $value));

          // If unprotected, enter the line-break loop
          if (!count($innbk)) {
            do {
              $store = $value;

              // Find the first stretch of characters over the $width limit
              if (preg_match("/^(.*?\s|^)(([^\s&]|&(\w{2,5}|#\d{2,4});){".$width."})(?!(".preg_quote($break, "/")."|\s))(.*)$/s$utf", $value, $match)) {

                // Determine the last "safe line-break" character within this match
                for ($x = 0, $ledge = 0; $x < strlen($lbrks); $x++) $ledge = max($ledge, strrpos($match[2], $lbrks{$x}));
                if (!$ledge) $ledge = strlen($match[2]) - 1;

                // Insert the modified string
                $value = $match[1].substr($match[2], 0, $ledge + 1).$break.substr($match[2], $ledge + 1).$match[6];
              }

            // Loop while overlimit strings are still being found
            } while ($store != $value);
          }

          // If unprotected, replace all \r with <br />\n to finish
          if (!count($innbr)) $value = str_replace("\r", "<br />\n", $value);
        }
    }

    // Send the modified segment down the drain
    $drain .= $value;
  }

  // Return contents of the drain
  return $drain;
}




