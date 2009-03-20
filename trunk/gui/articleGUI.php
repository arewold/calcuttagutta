<?php
/* Contains GUI for article functionality */

 function enterArticleGUI(){
 	// Check if session contains variables from previous entry attempt
 	$formContents = buildArticleArray($_SESSION);
 	
 	h3("Legg opp en artikkel :)");
 	table_open();
 	form_start_post();
 		tr_open(); 
 			td_open(1); echo("Tittel"); td_close();
 			td_open(1); form_textfield("title", $formContents['title']);	td_close(); 
 		tr_close();
 		
 		tr_open();
 			td_open(1); echo("Publiseringsdato"); td_close();
 			td_open(1); form_datewidget($formContents['date_posted']); td_close();
 		tr_close();
 		
 		tr_open();
 			td_open(1);	echo("Publiseringstidspunkt"); td_close();
 			td_open(1); form_timewidget($formContents['time_posted']); td_close();
 		tr_close();
 		
		tr_open();
 			td_open(1);	echo("Språk"); td_close();
 			td_open(1); form_dropdown("language", getAllLanguageIds(), getAllLanguageNames(), 0); td_close();			
 		tr_close();
		
 		tr_open();
 			td_open(1); echo("Bare lagre, ikke publiser"); td_close();
 			td_open(1); form_checkbox("is_draft", "1", "0"); td_close();
 		tr_close();
 		
 		tr_open();
 			td_open(2);	echo("Tekst"); td_close();
 		tr_close();
 		
 		tr_open();
 			td_open(2);	form_textarea("body", $formContents['body'], 50, 20); td_close();
 		tr_close();
 		
 		tr_open();
 			td_open(1); 
 				form_submit("submit", "Legg opp");  
 				form_submit("submit", "Forhåndsvis");  
 				form_submit("submit", "Avbryt");
 			td_close();
 			td_open(1); td_close();
 		tr_close(); 
 		form_hidden("category", "0");	
 		form_hidden("m_c", "addArticleGUI"); br();		

 	form_end();
 	table_close();
 }
 
 /*
 function addArticle($articleid, $author, $author_username, 
$body, $category,  $date_REQUESTed, $time_REQUESTed, 
$intro, $is_draft, $is_deleted, $picture_url, $priority, $view_count)
  */
  
 function addArticleGUI(){
 	if($_REQUEST['submit'] == "Avbryt"){
 		deleteArticleFromSession();
 		if(isset($_REQUEST['comment_to']) && ($_REQUEST['comment_to']) != "NULL"){
 			
 			h3("Kommentar slettet eller artikkelredigering avbrutt");
			echo '<a href="index.php?articleid=' . $_REQUEST['comment_to'] . '&m_c=va">Klikk her for å gå tilbake til artikkelen</a>';
			
 		}else{
 			h3("Artikkel slettet eller redigering avbrutt");	
 		}
 		
 		return; 		
 	}
 	

 	
 	
 	// Call addArticle with the correct variables from REQUEST. This is internal 
 	// to the GUI - it knows which values are actually sent from enterArticleGUI 
 	// and which are irrelevant.
 	// Get the return values from addArticle and print user feedback accordingly
 	
	$articleArray = buildArticleArray($_REQUEST);
	
	if (!userMayPost($articleArray)){
		h3("Du er ikke innlogget eller har ikke rettigheter til å poste.");
		div_open();
		echo "Dette skjer automatisk etter omkring 20 minutters inaktivitet. Teksten du forsøkte å sende inn følger. Merk den, klipp den ut og lim den inn i artikkel/kommentarskjemaet når du har logget inn på ny."; 
		form_textarea("body", $articleArray['body'], 50, 10);
		div_close();
		return;
	}
	
 	backupToSession($articleArray); // So IE users can go 'back' and keep form contents 	
 
 	
 	$feedback = verifyArticle($articleArray);  	
 	
 	// If we get anything other than a "true" from verification
 	// we print the error messages and exit
 	if($feedback[0] != "1"){
	 	foreach ($feedback as $message){
	 		echo($message);	br();

	 	}	 	
	 	form_start_post(); 
 		form_submit("submit", "Tilbake til redigering");
 				
 		if($articleArray['comment_to'] != "NULL"){
 			form_hidden("m_c", "va");
 			form_hidden("articleid", $articleArray['comment_to']);
 		}else{
			form_hidden("m_c", "editArticle");	
		}
		
		$_SESSION['save_attempted'] = 1;
		form_end();
	 	return;	 	
 	}
 	
 	// Check if we want to add the article or just preview it
 	if ($_REQUEST['submit'] == "Forhåndsvis"){
 		h3("Forhåndsvisning :)");
 		previewArticle($articleArray);
 		table_open();
		tr_open();
 			td_open(1);
 				// Inserting the previewed data into the webpage
 				// so addArticle function can operate as if the data
 				// came straight from the input form.
 				// EXCEPTION: The body contents, which can contain HTML tags,
 				// is stored in a session variable.
 			 	form_start_post();
 			 	$_SESSION['newbody'] = $articleArray['body'];
 			 	$_SESSION['newtitle'] = $articleArray['title'];
 			 	

 			 	form_hidden("is_draft", $articleArray['is_draft']);
 			 	form_hidden("category", $articleArray['category']);
 			 	form_hidden("intro", $articleArray['intro']);
 			 	form_hidden("priority", $articleArray['priority']);
 			 	form_hidden("year", $articleArray['year']);
 			 	form_hidden("month", $articleArray['month']);
 			 	form_hidden("day", $articleArray['day']);
 			 	form_hidden("hours", $articleArray['hours']);
 			 	form_hidden("minutes", $articleArray['minutes']);
 			 	form_hidden("articleid", $articleArray['articleid']);
 			 	form_hidden("author_username", $articleArray['author_username']);
 			 	form_hidden("author", $articleArray['author_username']);
 			 	
 			 	if(isset($articleArray['comment_to']))
 			 		form_hidden("comment_to", $articleArray['comment_to']);
 			 	
 				form_hidden("m_c", "addArticleGUI");			 	
 				form_submit("submit", "Legg opp");   				  
 				form_submit("submit", "Avbryt");
 				form_end();
 				form_start_post();
 				form_submit("submit", "Tilbake til redigering");
 				form_hidden("m_c", "editArticle");	

 				if($articleArray['comment_to'] == "NULL"){
 					form_hidden("articleid", $articleArray['articleid']);
 				}else{	
 					form_hidden("articleid", $articleArray['comment_to']);
 					form_hidden("comment_to", $articleArray['articleid']);
 				}
 				
 				$_SESSION['save_attempted'] = 1;
 				form_end();
 			td_close();
 			td_open(1); td_close();
 		tr_close(); 
 		table_close();		
  	}else{
  		
  		// Add article
  		
		$result = addArticle($articleArray); 		

		if ($result[0] != -1){
			deleteArticleFromSession();
			if ($articleArray['comment_to'] != "NULL"){	
				echo 'Godt sagt, forhåpentligvis! Og husk <a href="http://www.presse.no/varsom.asp">Vær Varsom-plakaten</a>. <br/>';
				echo '<a href="index.php?articleid=' . $articleArray['comment_to'] . '&m_c=va#lastcomment">Klikk her for å gå tilbake til artikkelen</a>';
			} else if ($articleArray['articleid'] > 0 && ($articleArray['comment_to'] != "NULL")){
				echo 'Kommentaren er redigert. ';
				echo '<a href="index.php?articleid=' . $articleArray['comment_to'] . '&m_c=va">Klikk her for å gå til artikkelen</a>';
			} else if ($articleArray['articleid'] > 0){
				echo 'Artikkelen er redigert. ';
				if ($articleArray['is_draft'] == "1"){
					echo "Artikkelen er ikke publisert, men kan hentes frem fra din profilside.";
				}else{
					echo '<a href="index.php?articleid=' . $articleArray['articleid'] . '&m_c=va">Klikk her for å gå til artikkelen</a>';
				}
			} else if ($articleArray['articleid'] == -1){
				echo 'Artikkelen er opprettet. ';
				
				if ($articleArray['is_draft'] == "1"){
					echo "Artikkelen er ikke publisert, men kan hentes frem fra din profilside.";
				}else{
					$idofnewarticle = $result[0];
					echo '<a href="index.php?articleid=' . $idofnewarticle . '&m_c=va">Klikk her for å gå til artikkelen</a>';
			
				}
			}

		}

	 	foreach ($result as $message){
	 		//echo($message);	br();
	 	}	 	 


 	}
 	
 	

 }
 
 
 function editArticle(){
  	// Check if session contains variables from previous entry attempt
 	
 	if (isset ($_SESSION['save_attempted'])){
 		$formContents = buildArticleArray($_SESSION);	
 	}else{
 		$temp = getAnyArticle($_REQUEST['articleid']);
 		$formContents = buildArticleArray($temp[0]);
 	}
 	
 	if ($formContents['comment_to'] == "NULL"){
 		h3("Rediger artikkel");
 	}else{
 		h3("Rediger kommentar");	
 	}
 	
 	table_open();
 	form_start_post();
 		tr_open(); 
 			td_open(1); echo("Tittel"); td_close();
 			td_open(1); form_textfield("title", fix_quotes($formContents['title'])); td_close(); 
 		tr_close();

 		if ($formContents['comment_to'] == "NULL"){
	 		tr_open();
	 			td_open(1); echo("Publiseringsdato"); td_close();
	 			td_open(1); form_datewidget($formContents['date_posted']); td_close();
	 		tr_close();
	 		
	 		tr_open();
	 			td_open(1);	echo("Publiseringstidspunkt"); td_close();
	 			td_open(1); form_timewidget($formContents['time_posted']); td_close();
	 		tr_close();
	 		
	 		tr_open();
	 			td_open(1); echo("Bare lagre, ikke publiser"); td_close();
	 			td_open(1);
	 			if ($formContents['is_draft'] == "1"){
	 				form_checkbox("is_draft", "1", "1");
	 			}else{
	 				form_checkbox("is_draft", "1", "0");	
	 			} 
	 			 
	 			td_close();
	 		tr_close();

			 		
			tr_open();
				td_open(1);	echo("Språk");  echo($formContents['language']); td_close();
				td_open(1); form_dropdown("language", getAllLanguageIds(), getAllLanguageNames(), $formContents['language']); td_close();			
			tr_close();	
		}
 		
 		tr_open();
 			td_open(2);	echo("Tekst"); td_close();
 		tr_close();
 		
 		tr_open();
 			td_open(2);	form_textarea("body", stripslashes($formContents['body']), 50, 20); td_close();
 		tr_close();
 		
 		tr_open();
 			td_open(1); 
 				form_submit("submit", "Legg opp");  
 				form_submit("submit", "Forhåndsvis");  
 				form_submit("submit", "Avbryt");
 			td_close();
 			td_open(1); td_close();
 		tr_close(); 
 		form_hidden("author", $formContents['author']);
 		form_hidden("category", "0");	
 		form_hidden("m_c", "addArticleGUI"); br();		
		form_hidden("articleid", $formContents['articleid']);
		form_hidden("comment_to", $formContents['comment_to']);
		form_hidden("author_username", $formContents['author_username']);
	
 	form_end();
 	table_close();
 	
 }

 
 function deleteArticle	(){
 	if (isset ($_REQUEST['confirmDelete'])){
		if (mayDeleteArticle($_REQUEST['articleid'])){
			$feedback = deleteArticleService($_REQUEST['articleid']);
			$result = $feedback[0];
			array_shift($feedback);

			if ($result){
				
				echo "Kommentaren eller artikkelen ble slettet.";
				//echo($feedback[0]);
			}else{
				echo "En feil oppstod, kommentaren/artikkelen ble ikke slettet.";
				//echo($feedback[0]);
			}				
		}else{		
			h3("Du har ikke tillatelse til å slette denne artikkelen.");				
		}
	 	

 	}else{
	  	h1("Bekreft sletting");
	 	form_start_post();
	 	form_hidden("m_c", "deleteArticle");
	 	form_hidden("confirmDelete", "1");
	 	form_hidden("articleid", $_REQUEST['articleid']);
	 	form_submit("submit", "Bekreft sletting"); 	
	 	form_end();
	 	br();
	 	js_backbutton("Avbryt");
 	}	
 }
 
 function showValidArticleGUI($articleid){
 	$article = getValidArticle($articleid);
 	printArticleGUI($article);	
 }
 
 function showAnyArticleGUI($articleid){
 	$article = getArticle($article);
 	printArticleGUI($article);	
 }
 
 function showDraftArticleGUI($articleid){
 	$article = getDraftArticle($articleid);
 	printArticleGUI($article[0]); 	
 }
 
 function printArticleGUI($article){
 	if($article == NULL){
 		h3("Fant ikke artikkelen.");
 		return;	
 	}
 	
	h3($article['title']);
	table_open();
	tr_open();
		td_open(2); echo $article['date_posted']; td_close();
	tr_close();
	tr_open();
		td_open(2); echo $article['author']; td_close();
	 tr_close();
	 tr_open();
	 	td_open(2); echo $article['body']; td_close();
	 tr_close();
	 table_close();	
	 	
 }
 
 function printTopArticle($article){
 	$SOFTLIMIT = getSetting("article_length_soft_limit", 1500);
 	$HARDLIMIT = getSetting("article_length_hard_limit", 3000);
 	$length = strlen(makeReadyForPrint(($article['body'])));
	h1_link(stripslashes($article['title']), url_to_article($article['articleid'])); 
	 		
	
	div_open();		
	
	articleMetaInfo(getAuthorOfArticle($article['articleid']), getAuthorOfArticleUsername($article['articleid']), make_date($article['date_posted']), make_time($article['time_posted']));
	
	div_close();
	div_open("textbody", "");

	$paragraph = makeReadyForPrint(stripslashes(closeUnclosedTags(create_paragraph($article['body'], $HARDLIMIT, $SOFTLIMIT))));
	
	echo(nl2br($paragraph));
	

	
	div_close();
	debug("length: " . $length);
	debug("strlen para " . strlen($paragraph));
	$characters_remaining = $length - strlen($paragraph);
	debug("characters remaing " . $characters_remaining);
	if ($characters_remaining > 0)
		return $characters_remaining;
	else 
		return 0;
	
 }

 function printArticle($article){

	h1_link(stripslashes($article['title']), url_to_article($article['articleid'])); 
	 		
	
			
	articleMetaInfo(getAuthorOfArticle($article['articleid']), getAuthorOfArticleUsername($article['articleid']), make_date($article['date_posted']), make_time($article['time_posted']));
	
	div_open("textbody", "");
	// Skips inserting #continue anchor due to many broken links
	//$article['body'] = substr($article['body'], 0, getSetting("article_length_soft_limit", 1500)) . '<a name="continue"></a>' . substr($article['body'], getSetting("article_length_soft_limit", 1500));
	
	$paragraph = makeReadyForPrint(closeUnclosedTags(($article['body'])));
	
	echo(nl2br($paragraph));

	div_close();
	
 }

 
  function printThumbArticle($article){
 	$THUMBLENGTH = getSetting("article_thumb_length", 100);
 	$TITLELENGTH = getSetting("article_title_length", 25);
	$length = strlen($article['body']);
	$title = create_paragraph(nl2br($article['title']), $TITLELENGTH);
	
 	
	h2_link($title, url_to_article($article['articleid'])); 
	 		
	
	div_open();		
	articleMetaInfo(getAuthorOfArticle($article['articleid']), getAuthorOfArticleUsername($article['articleid']), make_date($article['date_posted']));

	div_close();
	div_open("textbody", "");
	$paragraph = create_paragraph(nl2br(strip_tags($article['body'])), $THUMBLENGTH);
	
	echo($paragraph);

	
	div_close();
	
	return $length - $THUMBLENGTH;
 }
 
 function articlesFrontpage(){
	global $layout;	
	$remaining_characters = 0;
 	$NO_COLUMNS = $layout;
 	
 	if ($layout == 1){
 		$NO_ARTICLES = 10;	
 	}else if ($layout == 2){
 		$NO_ARTICLES = 21;
 	}else if ($layout == 3){
 		$NO_ARTICLES = 34;
 	}else if ($layout == 4){
 		$NO_ARTICLES = 41;
 	}else{
 		$NO_ARTICLES = 10;
 	}
 	
 	$counter = 0;
 	$articles = getFrontpageArticles($NO_ARTICLES);
 	
 	table_open();
 	tr_open(); td_open($NO_COLUMNS);	
 	$remaining_characters = printTopArticle($articles[0]);
 	
	div_open("showarticlelink");
		print_article_link($articles[0]['articleid'], "Vis", $remaining_characters);
		print_comments_link($articles[0]['articleid'], "&nbsp; Kommentarer");
	div_close(); 	
	td_close(); tr_close();
 	
 	// Get rid of the first array element
 	array_shift($articles);
	
	
 	if($NO_COLUMNS == 1){
	 	foreach ($articles as $article){
	 		tr_open(); td_open(1);	
			$remaining_characters = printTopArticle($article);
			
			div_open("showarticlelink");
				print_article_link($article['articleid'], "Vis", $remaining_characters);
				print_comments_link($article['articleid'], "&nbsp; Kommentarer");
			div_close();
			td_close();
			tr_close();
	 	}
	 		
 	}else{
 		tr_open();
	 	foreach ($articles as $article){
	 		td_open(1);	
			$remaining_characters = printThumbArticle($article);
			div_open("showarticlelink");
				print_article_link($article['articleid'], "Vis", $remaining_characters);
				print_comments_link($article['articleid'], "&nbsp; Kommentarer");
			div_close();
			td_close();
			if (($counter+1) == $NO_COLUMNS){
				tr_close(); tr_open();
				$counter=0;	
			}else{
				$counter++;
			} 
	 	} 		
	 	tr_close();
	}
 	
 	table_close();	
 
  } 

function printAuthorArticlesMenu($articleid){ return printAdminArticlesMenu($articleid); } 
 function printAdminArticlesMenu($articleid){	
	$string = '<a href="index.php?articleid=' . $articleid . '&m_c=deleteArticle">Slett</a> ';
	$string .= '<a href="index.php?articleid=' . $articleid . '&m_c=editArticle">Rediger</a>';
	return $string;
 }

function printAuthorCommentsMenu($articleid){ return printAdminCommentsMenu($articleid); } 
 function printAdminCommentsMenu($articleid){	
	$string = '<a href="index.php?articleid=' . $articleid . '&m_c=deleteArticle">Slett</a> ';

	return $string;
 } 
 
 function va(){ viewArticle(); }
 function viewArticle(){
 	$articleid = $_REQUEST['articleid'];
 	$article = getValidArticle($articleid);
 	
 	if(!$article){
 		h3("Fant ikke artikkelen.");
 	}else{
 		increment_view_count($articleid);
 		table_open();
 		tr_open(); td_open(1);
 		printArticle($article[0]);
 		
 		div_open("showarticlelink");
 		
 		if (isset($_SESSION['valid_admin'])){
 			echo printAdminArticlesMenu($article[0]['articleid']);	 			
 		}else if (isset($_SESSION['valid_user'])) {
 			
 			if ($_SESSION['valid_user'] == $article[0]['author_username']){
 				echo printAuthorArticlesMenu($article[0]['articleid']);	 				
 			}
 			
 		}
 		
 		div_close();
 		
 		td_close(); tr_close();
 		table_close();
		 		
		makeAnchor("comments"); 
		 	
		listComments($article[0]['articleid']); 
		if (isset ($_SESSION['valid_user'])){
			makeAnchor("entercomment");	
			enterComment($article[0]['articleid']);	
		}else{
			echo '<a href="javascript:viewLogin()">Logg inn for å legge inn kommentar</a>';	
		}
 	}
 

 	
 	
 }

function enterComment($articleid){
	 	// Check if session contains variables from previous entry attempt
 	$formContents = buildArticleArray($_SESSION);
 	
 	h3("Legg inn en kommentar");
 	table_open();
 	form_start_post();
 		tr_open(); 
 			td_open(1); echo("Tittel"); td_close();
 			td_open(1); form_textfield("title", $formContents['title']);	td_close(); 
 		tr_close();
 		
 		//tr_open();
 			//td_open(2);	echo("Tekst"); td_close();
 		//tr_close();
 		
 		tr_open();
 			td_open(2);	form_textarea("body", $formContents['body'], 50, 10); td_close();
 		tr_close();
 		
 		tr_open();
 			td_open(1); 
 				form_submit("submit", "Legg opp");  
 				form_submit("submit", "Forhåndsvis");  
 				form_submit("submit", "Avbryt");
 			td_close();
 			td_open(1); td_close();
 		tr_close(); 
 		form_hidden("category", "0");
 		form_hidden("comment_to", $articleid);	
 		form_hidden("m_c", "addArticleGUI"); br();	
 		if (isset($_REQUEST['commentid'])){ 			
 			form_hidden("articleid", $_REQUEST['commentid']);	
 		}	

 	form_end();
 	table_close();
	
}

function listComments($articleid){
	$comments = getComments($articleid);	
	$no_comments = count($comments);
	$i = 0;
	if ($no_comments > 0)
		h3("Kommentarer");
	
	table_open();
	foreach ($comments as $comment){
		$i++;
		$author = getAuthorOfArticle($comment['articleid']);
		$author_username = getAuthorOfArticleUsername($comment['articleid']);
				

				
		tr_open(); td_open(1);
		if ($i == $no_comments){
			makeAnchor("lastcomment", "");	
		}
		
		if(strlen($comment['title']) > 0){
			div_open();
			h2($comment['title']);
			div_close();	
		}
		articleMetaInfo($author, $author_username, make_date($comment['date_posted']), make_time($comment['time_posted']));
		div_open("textbody");
		echo makeReadyForPrint(nl2br($comment['body']));
		div_close();
		div_open("showarticlelink");
		if (isset($_SESSION['valid_admin'])){
			//echo printAdminCommentsMenu($comment['articleid']);
			echo printAuthorArticlesMenu($comment['articleid']);	
		}else if (isset($_SESSION['valid_user'])){
			if ($_SESSION['valid_user'] == $author_username){
				if ($i == $no_comments){
					echo printAuthorArticlesMenu($comment['articleid']);		
				}	
		}
			
		}
		div_close();
		td_close(); tr_close();	
		
	}
	table_close();
} 
 
 function previewArticle($article){
 	
 	if(!$article){
 		h3("Fant ikke artikkelen.");
 		
 	}else{
 		table_open();
 		tr_open(); td_open(1);
 		
		h1_link($article['title'], url_to_article($article['articleid'])); 
			
		articleMetaInfo($article['author'], $article['author_username'], make_date($article['date_posted']), make_time($article['time_posted']));
	
		div_open("textbody", "");

		$paragraph = makeReadyForPrint(nl2br($article['body']));
	
		echo($paragraph);

		div_close();	
		
 		td_close(); tr_close();
 		table_close();
 	}
 	
 	
 	
 }
 
 
 
 function vpb(){
 	viewPersonalBlogGUI();	
 }
 
 function viewPersonalBlogGUI(){
 	$author = $_REQUEST['username'];
 	
 	// Get array of articles
 	// Use function on each article to make a frontpage-version of it
 	// (which also provides link to view complete article, edit it etc)
 	
 }
 
?>
