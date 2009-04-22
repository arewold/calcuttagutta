<?
include_once("mod_logging.php");

function module_enter_article(){
	echo "<!-- start enter article -->";
	if(isset($_REQUEST['edit'])) 
		$edit = $_REQUEST['edit'];
	else 
		$edit = "";
	
	if(isset($_REQUEST['preview']))	
		$preview = $_REQUEST['preview'];
	else 
		$preview = "";

	if(!isset($_SESSION['user_may_post']) && !isset($_SESSION['valid_admin'])){
		echo "Du har ikke tillatelse til å legge inn artikler.";
		return;	
	}

	if($edit){
	    echo '<table class="default_table"><tr><td colspan=2><div class="default_header">Rediger artikkelen</div></td></tr>';
		$query = "SELECT * FROM articles WHERE articleid = " . $_REQUEST['articleid'] . ";";
		$row = searchDB($query);
		
		if(!$row || ($_SESSION['valid_user'] != $row['author_username'] && !isset($_SESSION['valid_admin']))                      ){
		    echo "<tr><td colspan=2>Fant ikke artikkelen, eller du har ikke tilgang til å redigere den.</td></tr></table>";
		    return;
		}else{
			
			form_article();
		
		}

	}else{
		echo '<table class="default_table"><tr><td colspan=2><div class="default_header">Legg inn en artikkel</div></td></tr>';
		
		form_article();
		//do_article_form(); 
	}
	echo "<br/><br/>";
	do_cancel_article_form();
	 
}

// Wipe out everything stored in session about article
function module_cancel_article(){
	if(isset($_REQUEST["cancelarticle"])){
		unset_form_article();
		echo "Alle data midlertidig lagret i forbindelse med oppretting/redigering av artikkel er fjernet.";
	}
}


/* View article */
function m_va(){
	// Calls new view article function, rest of code should be dumped
	va();
	return;
	
	global $anyone_comments;
	if(isset($_REQUEST['c']))
		$skip_chars = $_REQUEST['c'];
	else
		$skip_chars = 0;	
	
	echo "<!-- start view article -->"; 
	global $article_author;
	$articleid = $_REQUEST['articleid'];

	echo '<div class="articles"><table><tr><td class="articlescell">';
	
	if(!$articleid){
		echo "Ingen artikkel er valgt.";	
	}else{
	
		$query = "SELECT * FROM articles WHERE articleid =" . $articleid . " AND is_deleted IS NULL  AND (is_draft IS NULL OR is_draft=0);";
	
		$result = DB_get_table($query);
		$num_results = DB_rows_affected($query);
	
		if($num_results == 0){
		    echo "Fant ikke ønsket artikkel.";
		}else{
			increment_view_count($articleid);
			$row = DB_next_row($result);
			
			echo '<div class="header2">' . stripslashes($row['title']) . '</div>';
			echo '<div class="metatext">' . $article_author;
			echo '<span class="author">: ';
				if(isset($row['author_username'])){
					echo '<a href="index.php?m_c=mvp&amp;username=';
					echo $row['author_username'] . '">' . stripslashes($row['author']) . '</a>';			
				}else{
					echo stripslashes($row['author']);				
				}
			echo '</span>';					
	
			echo ', postet <span class="date">' . make_date($row['date_posted']) . '</span><span class="time"> ' . make_time($row['time_posted']) . '</span></div>';
			echo '<div class="textbody">';
			
			$body = stripslashes(nl2br($row['body']));
			
			/* If reader continues an article read partly on front page, we 
			 * insert anchor tag that the browser can skip to
			 */
			 
			if($skip_chars == 0)
				echo $body;
			else{
				echo substr($body,0,$skip_chars);
				echo '<a name="continue"></a>';
				echo substr($body, $skip_chars, strlen($body)-$skip_chars);
				
			} 	
			echo '</div>';	
			
			$edit_ok = false;
			// hvis admin
			if (isset($_SESSION['valid_admin'])) {
				if ($_SESSION['valid_admin']) {
					$edit_ok = true;
				}
			}
		
			// hvis valid user, og forfatter av den.
			if (isset($_SESSION['valid_user']) && isset($row['author_username'])) {
				if(($row['author_username'] == $_SESSION['valid_user'])){
					$edit_ok = true;
				}
			}

			if ($edit_ok) {
				echo '<div class="editarticle">';
				echo '<a href="index.php?articleid=' . $row['articleid'] . '&m_c=module_delete_article">Slett</a> ';
				echo '<a href="index.php?articleid=' . $row['articleid'] . '&m_c=module_enter_article&edit=1">Rediger</a>';
				echo '</div>';
			}	  		
	
			$comments_query = "SELECT * FROM articles WHERE comment_to=" . $articleid . " AND is_deleted IS NULL ORDER BY date_posted, time_posted ASC;";
			$comments_results = DB_get_table($comments_query);
			$num_comments = DB_rows_affected($comments_results);
			
			if($num_comments != 0){
	  			echo '</td></tr><tr><td class="header4"><a name="comments">Kommentarer</a></tr></td>';		    
				display_comments_rows($comments_results);
			}else{
				echo '</td></tr>';
				
			}
			
			// End DIV articles
			echo '</table></div>';
			if($anyone_comments || isset($_SESSION["valid_user"])){
				echo '<div class="default_header"><a name="commentform">Legg til en kommentar</a></div>';
				do_comment_form();
			}
			else{
				echo '<div class="default_text">Du må være en <a href="index.php?m_c=module_register_form&amp;page_title=Register<+new+user">registrert bruker</a>';
				echo " og <span id='loginlink''><a href=\"javascript:showDiv('loginform', 'errorandlogout')\">logget inn</a> for å kunne kommentere.</span></div>";

			}
			
			
		}
	}
}

function display_comments_rows ($result) {
$num_results = DB_rows_affected($result);
global $unknown_author;

for ($i=$num_results; $i > 0; $i--)
  {
		$row = DB_next_row($result);
		
		if($i == 1)
			echo '<a name="lastcomment"></a>'; 	

		echo "<tr ><td class=\"commentscell\"><div class=\"commentsheader\">" . stripslashes($row['title']) . "</div>";



		
		if(strlen($row['author_username']) > 1)
			echo "<div class=\"commentmetatext\">Av: <span class=\"commentauthor\">" . "<a href=\"index.php?m_c=mvp&username=" . $row['author_username'] . "\" >" .  stripslashes($row['author']) . "</a></span>";
		else
			echo "<div class=\"commentmetatext\">Av: <span class=\"commentauthor\">" . stripslashes($row['author']) . "</span>";


		echo ", postet <span class=\"commentdate\">" . make_date($row['date_posted']) . "</span> <span class=\"commenttime\">" . make_time($row['time_posted']) . "</span>";
		
	  	// You may delete if you are admin OR if you are a logged in user AND this is your comment AND your comment is the last one

		$delete_ok = false;
		// hvis admin
		if (isset($_SESSION['valid_admin'])) {
			if ($_SESSION['valid_admin']) {
				$delete_ok = true;
			}
		}
		
		// hvis bruker, og det finnes et author_username.
		if (isset($_SESSION['valid_user']) && isset($row['author_username'])) {
			// hvis samme, og siste post.
			if (($row['author_username'] == $_SESSION['valid_user']) && ($i == 1)){
				$delete_ok = true;
			}
		}

		if ($delete_ok) {
			echo '<span class="delcomment"> <a  href="index.php?articleid=' . $row['articleid'] . '&m_c=module_delete_article">(slett kommentar)</a></span>';
		}
				
		echo "</div><div class=\"textbody_comment\">" . stripslashes(nl2br($row['body'])) . "</div>";
  		

	echo "</td></tr>";
  }
}
  


function module_add_article(){
	global $flashformid;
	echo "\n<!-- start add article -->";
	$all_ok = true; // Error checking
	// create short variable names
	$edit = $_REQUEST['editarticle']; // Is this an edit operation?
	
	// User has confirmed a previewed article, get values from session
	if(isset($_REQUEST['previewconfirmed'])){
		$author = $_SESSION['author'];
		$title = $_SESSION['title'];
		$intro = $_SESSION['intro'];
		$body = $_SESSION['body'];
		$priority = $_SESSION['priority'];

		$date_posted = $_SESSION['date_posted'];
		$time_posted = $_SESSION['time_posted'];
		$comment_to = $_SESSION['comment_to'];
		$articleid = $_SESSION['articleid'];
		$category = $_SESSION['category'];	
		$is_draft = $_SESSION['is_draft'];
	}else{
		$author = $_POST['author'];
		$title = $_POST['title'];
		$intro = $_POST['intro'];
		$body = $_POST['body'];
		$priority = $_POST['priority'];
		$date_posted = $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'];
		$time_posted = $_POST['hours'] . ":" . $_POST['minutes'];
		$comment_to = $_POST['comment_to'];
		$articleid = $_POST['articleid'];
		$category = $_REQUEST['category'];
		$category = 0;
		$is_draft = $_POST['is_draft'];
		$article_form_id = $_REQUEST['article_form_id'];
	}
	
	// Replace form-given time with real time if this is a comment
	if($comment_to > 0 ){
		$date_posted = date("Y") . "-" .  date("m") . "-" . date("d");
		$time_posted = date("H") . ":" . date("i");
	}else{

	}
	
	// Can occur if someone posts after session is deleted. Return whatever 
	// contents is sent to us.
	global $anyone_comments; 
	if(!$anyone_comments && !is_logged_in()){
		echo "Du må være innlogget for å kunne kommentere; dersom du ikke gjør noe på nettstedet i løpet av omtrent 25 minutter blir du logget ut. Teksten du forsøkte å poste følger under. " .
				"Merk den og bruk CTRL+C for å kopiere den og CTRL+V for å lime den inn i et tekstfelt når du har logget inn igjen.<br/><br/>";
		echo $body;
		return;	
	}

	// Reject if this isn't a comment and user has no posting rights
	if($comment_to < 1 && !is_logged_in()){
		echo "Du har ikke tillatelse til å legge inn artikler.";
		echo "Antakelig ser du dette fordi du ikke har gjort noe på nettstedet de siste 20 minuttene, slik at du har blitt automatisk utlogget. Teksten du forsøkte å poste følger under. Merk den og bruk CTRL+C for å lage en kopi og CTRL+V til å lime den inn i et tekstfelt når du har logget inn igjen.<br/>";
		echo $body;
		return;	
	}

	

	/* If the unique form ID doesn't match with the current session counter, we do _nothing */
	/* since this is most likely the result of the user tumbling back and forth. 
	 * If the session has timed out (because the user has been writing too long, hopefully)
	 * we still accept the new post (i.e. NEW_SESSION is TRUE).
	 */
	
	/*
	global $NEW_SESSION;
	if($NEW_SESSION == TRUE)
		echo "1";
	else
		echo "0";
	*/
			
	if((isset($article_form_id) && ($flashformid != $article_form_id)) || ($NEW_SESSION == TRUE)){
		echo "Kommentar innsendt tidligere, eller du har logget ut. Du prøvde å poste dette: <br/>";
		echo $body;
		}else{
		
		$preview = $_REQUEST['preview'];
		$_SESSION['editarticle'] = "true";
		
		save_form_article();
	
		if(!($_SESSION['valid_user'] || $comment_to && article_exists($comment_to))){
			echo "Du må være en registrert bruker for å kunne legge inn artikler.";
		}else{
			if($_SESSION['valid_user']){
				$author_username = $_SESSION['valid_user'];
			}else{
				$author_username = '';
			}
		
			if (!$author || !$body || !$date_posted || !$time_posted)
			{
				$all_ok = false; $error_msg .= "Forfatter, tidspunkt og tekst må fylles inn.";
			}
	
			if (!$comment_to && !$title){
				$all_ok = false; $error_msg .= " Tittel må være med!";	
			}
		
			if(!(is_valid_alphanum($author) && is_valid_alphanum($title))){
				$all_ok = false; $error_msg .= "Systemutvikleren gnir sitt fjes i grusen og beklager på det dypeste at forfatterfelt og tittelfelt nå inneholder et eller annet spesialtegn som ikke er godkjent. Ta vennligst kontakt snarest slik at deres personlige programmeringskonsulent kan rette opp denne pinlige feilen.";	
			}
	
			$title = addslashes(strip_tags($title));
			$author = addslashes(strip_tags($author));	
			$date_posted = addslashes(strip_tags($date_posted));
			$time_posted = addslashes(strip_tags($time_posted));
			$body = addslashes(strip_tags($body, "<a> <img> <br> <i> <b> <div>"));	
			$category = addslashes(strip_tags($category));		
	
		
			if($is_draft == "ON"){
				$is_draft = 1; $log_description .= "savedraft,";
			}else{
				$is_draft = 'NULL';
			}
				
			if(!$comment_to > 0)
				$comment_to = 'NULL';
			else
				$log_description .= "comment,";
			

			
			// Inserting into DB
			if($edit){
		
				$log_description .= "editarticle,";
			    $query = "UPDATE articles SET title=\"$title\", author=\"$author\", intro=\"$intro\", body=\"$body\", date_posted=\"$date_posted\", time_posted=\"$time_posted\", comment_to=$comment_to, priority=\"$priority\", picture_url=\"$picture_url\", category=\"$category\", is_draft=$is_draft WHERE articleid=" . $articleid .";";
			}else{
				$log_description .=  "newarticle,";
		    	$query = "INSERT INTO articles (title, author, author_username, body, date_posted, time_posted, comment_to, is_draft, view_count) VALUES(\"$title\", \"$author\", \"$author_username\",  \"$body\", \"$date_posted\", \"$time_posted\", $comment_to,$is_draft,0);";
			}
	

			
			if($all_ok == true){
				
				
				if(isset($preview)){
					echo "<div class=\"header2\">" . stripslashes($_REQUEST['title']) . "</div>";
					echo "<div class=\"metatext\"><span class=\"author\">Av: " . $_REQUEST['author'];
					
					if(isset($_SESSION['valid_user'])){
						echo "  (" . $_SESSION['valid_user'] . ").</span>";
					}else{
						echo "  (" . $unknown_author . ").</span>";
					}
					echo "<span class=\"time\">Lagt opp: " . date_nor_sql($date_posted) . ", " . $time_posted . "</span></div>";
					echo "<div class=\"textbody\">" . stripslashes(nl2br($_REQUEST['body'])) . "</div>";
					form_start_post();
					form_submit("previewconfirmed", "Lagre artikkel");
				
					if(isset($edit))
						form_hidden("editarticle", "editarticle");	
					
					form_hidden("m_c", "module_add_article");
					form_end();
					form_start_post();
					if(isset($edit))
						form_hidden("edit", "edit");	
					form_hidden("articleid", $articleid);				
					form_submit("backtoedit", "Rediger artikkel");
					form_hidden("m_c", "module_enter_article");
					form_end();		
					
					echo "<br/><br/>";
					do_cancel_article_form();		
					
													
				}else{			
					$result = DB_insert($query);

					if($result > 0){
						global $logtype; global $eventdesc;
						if($edit){
							if($comment_to > 0)
								write_log_entry($articleid, $logtype['comment'], $log_description);
							else
								write_log_entry($articleid, $logtype['article'], $log_description);
						}else{
							if($comment_to > 0)
								write_log_entry(mysql_insert_id(), $logtype['comment'], $log_description);
							else
								write_log_entry(mysql_insert_id(), $logtype['article'], $log_description);
						}	
						$_SESSION['flashformid'] = $flashformid+1;
						if($comment_to != 'NULL'){
							echo "Kommentar lagt til! Godt sagt, forhåpentligvis. Husk Vær Varsom-plakaten.<br/>";
							form_start_get();
							form_submit("submit", "Gå tilbake til artikkelen");
							form_hidden("m_c", "m_va");
							form_hidden("articleid", $comment_to);
							form_end();
							
							unset_form_article();
							
						}else if($edit){
							echo "Redigering fullført. Håper det ble bedre.";
							if($is_draft != 'NULL')
								echo " Denne artikkelen er lagret og er tilgjengelig fra din profilside. " .
										"Den blir ikke lagt ut på forsiden eller gjort tilgjengelig  gjennom artikkelsøk.";
							unset_form_article();
						
						}else{
						    echo "Ny artikkel lagt inn! Husk: Sist gang noen sjekket, var det bare 1 av 10 lesere som gadd &aring kommentere. ;)";
							if($is_draft != 'NULL')
								echo "Denne artikkelen er lagret og er tilgjengelig fra din profilside. " .
										"Den blir ikke lagt ut på forsiden eller gjort tilgjengelig  gjennom artikkelsøk.";
				
						    unset_form_article();
						}
					}else{
						echo $query;
						echo ("Artikkel ikke lagt opp, databaseproblem.");
					}
				}
	
			}else{
				echo $error_msg;				
			}
	
		}
	}
}

function module_articles_frontpage(){
	echo "<!-- start articles frontpage -->";
	global $article_author;
	global $no_articles_text; global $jokes, $layout, $chars_showing_articles, $chars_showing_first_article;

	$query = "select * from articles WHERE is_deleted IS NULL AND comment_to IS NULL AND is_draft IS NULL ORDER BY date_posted DESC, time_posted DESC LIMIT 8";
	
	$result = DB_get_table($query);


	?>
	
	<!-- start articles frontpage -->

	
	<?
	if($layout == "newspaper")
		echo '<table class="frontpage_table_2columns">';
	else
		echo '<table class="frontpage_table">';
	
	
	if(!$result || DB_rows_affected($result) < 1){
		// hvis noe er feil, vis en vits.
		echo '<tr>';
			echo '<td colspan="2" class=\"articles_frontpage\">';
				echo $no_articles_text . ' Vi presenterer i stedet en vits.<br/><br/>';
				echo $jokes[array_rand($jokes, 1)]; 
			echo '</td>';
		echo '</tr>';
	}else{
		$num_results = DB_rows_affected();

		// øverste artikkel, spenner over begge kolonnene.
		echo "<tr>";
			echo '<td colspan="2" class="articles_frontpage">';
			$row = DB_next_row($result);
			echo '<div class="header2 articletitlefront"><a href="index.php?m_c=m_va&amp;articleid=' . $row['articleid'] . '">' . stripslashes($row['title']) . '</a></div>';
			echo '<div class="metatext">' . $article_author;
			echo '<span class="author">: ';
				if(isset($row['author_username'])){
					echo '<a href="index.php?m_c=mvp&amp;username=';
					echo $row['author_username'] . '">' . stripslashes($row['author']) . '</a>';			
				}else{
					echo stripslashes($row['author']);				
				}
			echo '</span>';					
	
			echo ', postet <span class="date">' . make_date($row['date_posted']) . ' </span><span class="time">' . make_time($row['time_posted']) . '</span></div>';

			echo '<div class="textbody">';
				if(strlen($row['body']) < $chars_showing_first_article*2){
					echo stripslashes(nl2br($row['body']));
				}else{
					echo closeUnclosedTags ( stripslashes( substr(nl2br($row['body']),0,$chars_showing_first_article) ) ) ; 
					echo " ..."; 
					$chars_left = strlen($row['body']) - $chars_showing_first_article;
					
				}				
			echo '</div>';
	
			$number_of_comments = number_of_comments($row['articleid']);	
	 
			echo '<div class="showarticlelink">';
			
			if(strlen($row['body']) < $chars_showing_first_article*2){
				echo '<a href="index.php?m_c=m_va&amp;articleid=' . $row['articleid'] . '">Vis artikkelside</a>&nbsp;&nbsp;';
			}else{
				echo '<a  href="index.php?c='. $chars_showing_first_article . '&amp;m_c=m_va&amp;articleid=' . $row['articleid'] . '#continue">Les hele artikkelen <span class="notice">(' . $chars_left . ' flere tegn)</span></a>&nbsp;&nbsp;';
			}				

			// Give link to comments if any, else link to the commenting form
			if($number_of_comments > 0)
				echo '<a href="index.php?m_c=m_va&amp;articleid=' . $row['articleid'] . '#comments">Les kommentarer (' . $number_of_comments . ')</a>&nbsp;&nbsp;';                        
			else{
				global $anyone_comments;
				if($anyone_comments || isset($_SESSION['valid_user']))
					echo '<a href="index.php?m_c=m_va&amp;articleid=' . $row['articleid'] . '#commentform">Skriv kommentar</a>&nbsp;&nbsp;';
				else
					echo  "<span id='loginlink'><a href=\"javascript:showDiv('loginform', 'errorandlogout')\">Logg inn og kommenter</a></span>";

			}
			echo '</div>';
			echo '</td>';
		echo '</tr>';
			
		// resten av artiklene
		if($layout == "newspaper")
			$chars_showing_first_article = $chars_showing_first_article / 4;
			
		for ($i=1; $i < $num_results; $i++){
		  	// sjekker layout og hvilken 'side' man er på, siden man kan velge mellom 1 eller 2 kolonner.
			if((!($i % 2 == 0)) || $layout == "weblog"){
				echo "<tr>";    
				if($layout == "weblog")
					echo '<td colspan="2" class="articles_frontpage">';
				else
					echo '<td class="articles_frontpage_2column">';
			    
			}else{		    	
				if($layout == "weblog")
					echo '<td colspan="2" class="articles_frontpage">';
				else
					echo '<td class="articles_frontpage_2column">';
			}
		 
			 $row = DB_next_row($result);
			echo '<div class="header2 articletitlefront"><a href="index.php?m_c=m_va&amp;articleid=' . $row['articleid'] . '">' . stripslashes($row['title']) . '</a></div>';
			echo '<div class="metatext">' . $article_author;
			echo '<span class="author">: ';
				if(isset($row['author_username'])){
					echo '<a href="index.php?m_c=mvp&amp;username=';
					echo $row['author_username'] . '">' . stripslashes($row['author']) . '</a>';			
				}else{
					echo stripslashes($row['author']);				
				}
			echo '</span>';					
	
			echo ', postet <span class="date">' . make_date($row['date_posted']) . ' </span><span class="time">' . make_time($row['time_posted']) . '</span></div>';
			
				echo '<div class="textbody">';
					if(strlen($row['body']) < $chars_showing_first_article*2){
						echo stripslashes(nl2br($row['body']));
					}else{
						echo stripslashes(substr(nl2br($row['body']),0,$chars_showing_first_article)); 
						echo " ..."; $chars_left = strlen($row['body']) - $chars_showing_first_article;

					}
			    echo '</div>';
	
			    $number_of_comments = number_of_comments($row['articleid']);	
			     
				echo "<div class=\"showarticlelink\">";

				if(strlen($row['body']) < $chars_showing_first_article*2){
					echo '<a href="index.php?m_c=m_va&amp;articleid=' . $row['articleid'] . '">Vis artikkelside</a>&nbsp;&nbsp;';
				}else{
					echo '<a  href="index.php?c='. $chars_showing_first_article . '&amp;m_c=m_va&amp;articleid=' . $row['articleid'] . '#continue">Les hele artikkelen <span class="notice">(' . $chars_left . ' flere tegn)</span></a>&nbsp;&nbsp;';
				}				
	
				if($number_of_comments > 0)
					echo '<a href="index.php?m_c=m_va&amp;articleid=' . $row['articleid'] . '#comments">Les kommentarer (' . $number_of_comments . ')</a>&nbsp;&nbsp;';                        
				else
					echo '<a href="index.php?m_c=m_va&amp;articleid=' . $row['articleid'] . '#commentform">Skriv kommentar</a>&nbsp;&nbsp;';
				

				
				echo '</div>';
			echo '</td>';
			 
			if ((!($i % 2 != 0)) || $layout == "weblog"){
				echo "</tr>";
			}
		} // slutt for-løkke

		// helt til slutt, hvis antall artikler på forsiden er et partall,
		// og dette ikke er weblog (en artikkel pr rad) 
		// vil vi bare ha en artikkel på slutten, og må huske å avslutte raden.
		if (($num_results % 2 == 0) && $layout <> "weblog"){
			echo "</tr>";
		}
	}
	
	echo "</table>";
}

function module_delete_article(){
	echo "<!-- start delete article -->";
	
	if (!$_SESSION['valid_user']){
		echo "Du m&aring være innlogget for &aring f&aring tilgang til denne siden.";
	}else{
		$reallydelete = $_REQUEST['reallydelete'];
		$articleid = $_REQUEST['articleid'];
		
		// TODO: this smells like shit..
		if($reallydelete){
			
		
		
		

		  $query = "select * from articles where articleid=$articleid";
		  $result = DB_get_table($query);
	
		  $num_results = DB_rows_affected($result);
	
			if($num_results == 1){

			    //$deletequery = "DELETE FROM articles WHERE articleid = $articleid;";
			    $deletequery = "UPDATE articles SET is_deleted = 1 WHERE articleid = $articleid;";
			    $deleteresult = DB_update($deletequery);
			    global $logtype; global $eventdesc;
			    write_log_entry($articleid, $logtype['article'], "deletearticle,");
			    
			    echo "<h3>F&oslash;lgende artikkel er n&aring; slettet fra databasen</h3>";
		    	$row = DB_next_row($result);
			     echo '<p><strong>'.($i+1).'. Title: ';
			     echo htmlspecialchars(stripslashes($row['title']));
			     echo '</strong><br />Author: ';
			     echo stripslashes($row['author']);
			     echo '<br />Date: ';
			     echo stripslashes($row['date']);
				 echo '<br />Article ID: ';
			     echo stripslashes($row['articleid']);
				 echo '<br />Article priority: ';
			     echo stripslashes($row['priority']);
			     echo '<br />Text:<br/> ';
			     echo stripslashes($row['body']);
			     echo '</p>';

			}
			else {
			    echo "<h3>Artikkelen fins ikke i databasen.</h3>";
			}

	
		}else{
			$query = "select * from articles where articleid=$articleid";
			$row = searchDB($query);
			
			// lov til � slette?
			$ok = false;
			
			if (isset($_SESSION['valid_user'])) {
				if ($_SESSION['valid_user'] == $row['author_username']) {
					$ok = true;
				}
			}
			
			if (isset($_SESSION['valid_admin'])) {
				if ($_SESSION['valid_admin']) {
					$ok = true;
				}
			}
			
			if ($ok) {
				echo "Vil du virkelig slette " . stripslashes($row['title']) . "?";
			
				form_start_post();
				form_hidden("m_c", "module_delete_article");
				form_hidden("articleid", $articleid);
				form_hidden("reallydelete", "yes");
				form_submit("submit", "Ja, slett artikkelen.");
				form_end();
			} else {
				echo "Du må være forfatteren av denne teksten for å kunne slette den.";
			}
		}
	}	
}


?>
