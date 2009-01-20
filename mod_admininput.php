<?php

// Handles admin input to database
function module_admininput(){
 	// adminpage, stop here if not logged in/right access-level
	if (!isValidAdmin()) {
		echo (getString("not_valid_admin", "Administratorside, du må logge inn for å få tilgang her"));
		return;
	}	
	
	$inputaction = $_REQUEST['inputaction'];
	
	if($inputaction == "addarticle"){
		save_form_article();
		$title = $_REQUEST['title'];
		$author = $_REQUEST['author'];
		$author_username = $_REQUEST['author_username'];
		$date_posted = $_REQUEST['year'] . "-" . $_REQUEST['month'] . "-" . $_REQUEST['day'];
		$time_posted = $_REQUEST['hours'] . ":" . $_REQUEST['minutes'];
		$comment_to = $_REQUEST['comment_to'];
		$is_draft = $_REQUEST['is_draft'];
		$body = $_REQUEST['body'];
		
		if($is_draft == "ON"){
			$is_draft = 1; $log_description .= "savedraft,";
		}else{
			$is_draft = 'NULL';		
		}
		if(strlen($comment_to) < 1){
			$comment_to = "NULL"; $log_description .= "savenewarticle,";
		}else{
			$log_description .= "savenewcomment,";
		}
				
		$query = "INSERT INTO articles (title, author, author_username, body, date_posted, time_posted, comment_to, is_draft, view_count) VALUES(\"$title\", \"$author\", \"$author_username\",  \"$body\", \"$date_posted\", \"$time_posted\", $comment_to,$is_draft,0);";
		echo $query;
		$result = DB_insert($query);
		
		global $logtype;
		
		if($result){
			echo "Artikkel lagt inn med id: " . mysql_insert_id();
			unset_form_article();
			
			if($comment_to != "NULL"){
				$log_description .= "commentadded!,";
				write_log_entry(mysql_insert_id(), $logtype['comment'], $log_description);
			}else{
				$log_description .= "articleadded!,";
				write_log_entry(mysql_insert_id(), $logtype['article'], $log_description);
			}
			
		}else{
			echo "Oops: " . mysql_error();	
		}
		
		
	}else{
		form_start_post();
		
		echo '<table class="default_table">';
		echo '<tr><td>Forfatter</td><td>'; form_textfield("author",stripslashes($_SESSION['author'])); echo '</td></tr>';
		echo '<tr><td>Forfatter_brukernavn</td><td>'; form_textfield("author_username",stripslashes($_SESSION['author'])); echo '</td></tr>';
		echo '<tr><td>Tittel</td><td class="form_article_title">'; form_textfield("title", stripslashes(fix_quotes($_SESSION['title']))); echo '</td></tr>';
		echo '<tr><td>Dato</td><td>'; form_datewidget($_SESSION['date_posted']); echo '</td></tr>';
		echo '<tr><td>Tidspunkt</td><td>'; form_timewidget($_SESSION['time_posted']); echo '</td></tr>';
		echo '<tr><td>Kommentar til</td><td>'; form_textfield("comment_to",$_SESSION['comment_to']); echo '</td></tr>';
		if($_SESSION['is_draft'] == "ON"){
			echo '<tr><td>Bare lagre, <br/>ikke publiser</td><td>'; form_checkbox("is_draft", "ON", "1"); echo '</td></tr>';
		}else{
			echo '<tr><td>Bare lagre, <br/>ikke publiser</td><td>'; form_checkbox("is_draft", "ON", "0"); echo '</td></tr>';}

		echo '<tr><td colspan=2 class="form_article_text">'; form_textarea("body",stripslashes($_SESSION['body']),30,10); echo '</td></tr>';
		echo '<tr><td colspan=2>'; form_submit("Button", "Lagre artikkelen"); echo '</td></tr>';
		echo '<tr><td colspan=2>'; form_submit("preview", "Forhåndsvis artikkel"); echo '</td></tr>';

		form_hidden("m_c", "module_admininput");
		form_hidden("inputaction", "addarticle");
		form_hidden("articleid", $_SESSION['articleid']);
		
		if(isset($edit)){
			form_hidden("editarticle", "editarticle");	
		}
		echo '</table>';
		form_end();	
	}

}





?>