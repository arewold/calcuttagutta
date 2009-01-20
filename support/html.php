<?php
/*
 * Created on 23.okt.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 function debug($message){
 	if (0){
 		echo '<div style="text-decoration:underline; font-family:courier; text-size:8px">' . $message . '</div>';
 	}	
 }
 
 function br(){
 	echo '<br/>';	
 }
 
 function h3($string){
 	echo('<div class="default_header">' . $string . '</div>');
 }

function h1($string){
	echo('<div class="header2 articletitlefront">' . $string . '</div>');
}

function h2($string){
	echo('<div class="commentsheader">' . $string . '</div>');
}

// Create javascript button to return to previous page
// title is the link button
function js_backbutton($title){
	echo( '<input type=button class="nice1" value="' . $title . '" onClick="history.go(-1)">' );
}


function div_open($class = '', $style = '', $id = ''){
	echo '<div id="' . $id . '" class="' . $class . '" style="' . $style . '">';
	
}

/* Creates the meta info line for an article, with author + link to author's profile
 * as well as a given date and time. Used both for articles and comments.
 */
function articleMetaInfo($author, $author_username, $date, $time = ""){
	echo '<div class="metatext">';
	echo '<span class="author">';
	
	if ($author_username == -1){
		echo stripslashes($author);
	}else{
		echo '<a href="index.php?m_c=mvp&amp;username=';
		echo $author_username . '">' . stripslashes($author) . '</a>';			
							}
	echo '</span>';	
		
	if (strlen($time) > 1){
		
		echo ', postet <span class="date">' . $date . '</span>';
		echo '<span class="time"> ' . $time . '</span>';
	}else{
		echo ', <span class="date">' . $date . '</span>';
		
	}	
	
	echo '</div>';
}
function div_close(){
	echo "</div>";
}

function span_open($class = '', $style = ''){
	echo '<span class="' . $class . '" style="' . $style . '">';
}

function span_close(){
	echo "</span>";
}

function h1_link($string, $link){
	echo('<div class="header2 articletitlefront"><a href="' . $link . '">' . $string . '</a></div>');
}

function h2_link($string, $link){
	echo('<div class="header3 articletitlefront"><a href="' . $link . '">' . $string . '</a></div>');
}

function makeAnchor($name, $contents=""){
	echo('<a name="'.$name.'" >' . $contents . '</a>');		
}


function option_open($value){
	echo '<option value="' . $value . '">';
}

function option_close(){
	echo '</option>';
}
 
 function select_open($name){
 	echo '<select name="' . $name . '">';
 }
 
 function select_close(){
 	echo '</select>';
 }
 
 function table_open(){
 	echo '<table class="default_table">';	
 	
 }
 
 function table_close(){
 	echo '</table>';	
 }
 
 function tr_open(){
 	echo '<tr>';
 }
 
 function tr_close(){
 	echo '</tr>';	
 }
 
 function td_open($colspan){
 	echo '<td colspan=' . $colspan . '>';	
 }
 
 function td_close(){
 	echo '</td>';	
 }

 function image($source, $alt = "", $style = "") {
 	echo '<img src="' . $source . '" alt="' . $alt . '" style="' . $style . '"/>';
 }
 
?>
