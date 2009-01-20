<?php
/*
 * Created on 29.okt.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function listSearchesGUI(){
	global $months;
	
	h3("Vis artikler fra gitt måned");
	form_start_post();
	select_open("month");
	for($i = 1; $i<10; $i++){
		option_open("0" . $i);
		echo($months[$i]);
		option_close();
	} 

	for($i = 10; $i<13; $i++){
		option_open($i);
		echo($months[$i]);
		option_close();	
	} 		
	select_close();
	form_hidden("m_c", "monthSearchResultGUI");	
	form_select_number("year", 2004,date("Y"), date("Y"));	
	form_submit("submit", "Søk");
	form_end();
	
	
	br();br();
	h3("Fritekstsøk");
	$author_usernames = array();
	$author_names = array();
	$author_usernames[] = "0";
	$author_names[] = "(ikke begrens)";
	$author_usernames = array_merge($author_usernames, getAllAuthorsUsernames());
	$author_names = array_merge($author_names, getAllAuthorsNames());

	form_start_post();
	form_textfield("text", ""); br();
	echo("Sjekk mot hele ord ");
	form_checkbox("nopartialmatch", "1", "1");
	br();
	echo("Søk i kommentarer ");
	form_checkbox("searchcomments", "1", "0");
	form_hidden("m_c", "textSearchResultGUI");
	br();
	echo("Begrens til én forfatter ");
	form_dropdown("author", $author_usernames, $author_names, 0);
	br();
	form_submit("submit", "Fritekstsøk");
	form_end();


	br();br();
	h3("Vis alle kommentarer av gitt bruker");
	$author_usernames = getAllUsersUsernames();
	$author_names = getAllUsersNames();
	form_start_post();
	echo("Velg forfatter ");
	form_dropdown("author", $author_usernames, $author_names, 0);
	form_submit("submit", "Vis kommentarer");
	form_hidden("m_c", "listCommentsSearchResultGUI");
	form_end();	
}


function listCommentsSearchResultGUI(){

	$table = listCommentsSearchService($_REQUEST['author']);

	$num_rows = getRowsAffected($table);
	
	h3("Søkte etter kommentarer skrevet av bruker " . $_REQUEST['author'] . ", " . $num_rows . " treff");

	if($table== NULL){
		echo("Finner ingen kommentarer av valgt bruker.");	
	}else{
		table_open();
		while ($row = nextResultInTable($table)){
			if(isArticleAndAlive($row['comment_to'])){
				tr_open();
					echo('<td style="width:80px">');	
					echo make_ddmmyy_date($row['date_posted']); 
				td_close();
				td_open(1);
					print_article_link($row['comment_to'], $row['title']);
					echo("(Kommentar til: "); print_parent_article_link($row['comment_to']); echo ")";
						
				td_close();			
				tr_close();
			}
		
		}		
		table_close();		
	}
}	


function monthSearchResultGUI(){
	global $months;
	$chosenMonth = $_REQUEST['month'] + 0;
	h3($months[$chosenMonth] . " " . $_REQUEST['year']);
	
	$table = monthSearchService($_REQUEST['year'], $_REQUEST['month']);

	

	if($table== NULL){
		echo("no_articles_in_that_month");	
	}else{
		table_open();
		while ($row = nextResultInTable($table)){
			tr_open();
				echo('<td style="width:80px">');	
				echo make_ddmmyy_date($row['date_posted']); 
			td_close();
			td_open(1);
				print_article_link($row['articleid'], $row['title']);					
			td_close();			
			td_open(1);
				echo $row['author'];
			td_close();
			tr_close();
		}		
		table_close();		
	}
}

function textSearchResultGUI(){
	

	if (isset($_REQUEST['nopartialmatch']))
		$partialmatch = 0;
	else
		$partialmatch = 1;
	
	if (isset($_REQUEST['searchcomments'])){
		$searchcomments = 1;
	}else{
		$searchcomments = 0;	
	}

	$table = textSearchService($_REQUEST['text'], $partialmatch, $_REQUEST['author'], $searchcomments);

	if($searchcomments){
		h3("Søkte etter '" . $_REQUEST['text'] . "' i alle artikler og kommentarer, " . count($table) . " treff, nyeste først");
	}else{
		h3("Søkte etter '" . $_REQUEST['text'] . "' i alle artikler, " . count($table) . " treff, nyeste først");	
	}
	
	if($table== NULL){
		//echo("no_articles_with_that_text");
		echo "Sorry Mac!";	
	}else{
		table_open();
		foreach ($table as $row){
			tr_open();
				echo('<td style="width:80px">');	
				echo make_ddmmyy_date($row['date_posted']); 
			td_close();
			td_open(1);
				
				if(isset($row['comment_to'])){
					print_article_link($row['comment_to'], $row['title']);
					echo("(Kommentar til: "); print_parent_article_link($row['comment_to']); echo ")";
				}else{
					print_article_link($row['articleid'], $row['title']);
				}		
									
			td_close();			
			td_open(1);
				echo $row['author'];
			td_close();
			tr_close();
			
			tr_open();
			td_open(3);
				echo create_paragraph($row['body'], 200, 200);
				echo "...";
				br();br();
			td_close();
			
		}		
		table_close();		
	}
}
	

?>
