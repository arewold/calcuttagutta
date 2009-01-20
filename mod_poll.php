<?php

function module_oldpolls(){
	// Arrays for storing results until we compute percentages
	$questions = array();
	$votecounts = array();
	$votecount = 0;
	
	// Find active polls
	$current_time = date('Y-m-d H:i');
	//echo $current_time;
	$oldtimelimit = "2000-01-31 00:00";
	$oldtimelimit_time = "";
	$findpolls = "SELECT pollid, title, time_opened, time_closed, description FROM poll WHERE time_opened > '" . $oldtimelimit . "' AND time_closed > '" . $oldtimelimit_time . "' ORDER BY time_opened;";
	$activepolls = DB_get_table($findpolls);

	echo '<table class="default_table">';
	
	
	while($pollrow = DB_next_row($activepolls)){
		if(!isset($pollid))
			echo '<tr><td colspan=2 class="pollheader">Alle avstemninger</td></tr>';
		$pollid = $pollrow['pollid'];
		// Find the alternatives for this poll
		$findalts = "SELECT pollid, question,questionid FROM pollquestion WHERE pollid =" . $pollid . ";";
		$alternatives = DB_get_table($findalts);
		echo '<tr><td colspan=2><a NAME="' . $pollid . '"></a><span class="polltitle">' . $pollrow['title'] . '</span></td></tr>';
		echo '<tr><td>Startet</td><td>' . $pollrow['time_opened'] . '</td></tr>';
		echo '<tr><td>Sluttet</td><td>' . $pollrow['time_closed'] . '</td></tr>';
		while($altrow = DB_next_row($alternatives)){
			$altid = $altrow['questionid'];
			// How many voted for this alternative?
			$countvotes = "SELECT COUNT(questionid) AS votes FROM vote WHERE pollid =" . $pollid . " AND questionid =" . $altid . ";";
			$counted = DB_search($countvotes);
			$questions[] = $altrow['question'];
			$votecounts[] = $counted['votes'];
			$votecount += $counted['votes'];
		}
		for($i = 0; $i < count($questions); $i++){
			if($votecount == 0){
				echo '<tr><td colspan=2>Ingen stemmer avgitt.</td></tr>';
				break;	
			}
			else
				echo '<tr><td>' . $questions[$i] . "</td><td>" . (int)($votecounts[$i]/$votecount*100) . '% (' . $votecounts[$i] . ')</td></tr>';				
		}
		if(strlen($pollrow['description']) > 1)
			echo '<tr><td colspan=2>' . $pollrow['description'] . '</td></tr>';
		echo '<tr><td colspan=2 style="background-color:transparent;" ></td></tr>';
		$questions = array();
		$votecounts = array();
		$votecount = 0;		
		
	}
	
	echo '</table>';
	
}

// Checks if the logged-in user has voted
// on this poll
function has_voted($username, $pollid){
	$query = "SELECT username FROM vote WHERE username='" . $username . "' AND pollid=" . $pollid . ";";
	$result = DB_get_table($query);
	$num = DB_rows_affected($result);

	if($num > 0)
		return true;
		
	return false;

}

// Output result graph for poll
function display_results(){
	// Arrays for storing results until we compute percentages
	$questions = array();
	$votecounts = array();
	$votecount = 0;
	
	// Find active polls
	$current_time = date('Y-m-d H:i');
	//echo $current_time;
	$findpolls = "SELECT pollid, title FROM poll WHERE time_opened < '" . $current_time . "' AND time_closed > '" . $current_time . "';";
	$activepolls = DB_get_table($findpolls);

	$output = '<table class="polltable">';
	
	
	while($pollrow = DB_next_row($activepolls)){
		if(!isset($pollid))
			$output .= '<tr><td colspan=2 class="pollheader">Meninger</td></tr>';
		$pollid = $pollrow['pollid'];
		// Find the alternatives for this poll
		$findalts = "SELECT pollid, question,questionid FROM pollquestion WHERE pollid =" . $pollid . ";";
		$alternatives = DB_get_table($findalts);
		$popuplink = '<a href="index.php?m_c=module_oldpolls&amp;#' . $pollid . '">' . $pollrow['title'] . '</a>';
		$output .= '<tr><td colspan=2><span class="polltitle">' . $popuplink . '</span></td></tr>';
		while($altrow = DB_next_row($alternatives)){
			$altid = $altrow['questionid'];
			// How many voted for this alternative?
			$countvotes = "SELECT COUNT(questionid) AS votes FROM vote WHERE pollid =" . $pollid . " AND questionid =" . $altid . ";";
			$counted = DB_search($countvotes);
			//echo '<tr><td>' . $altrow['question'] . "</td><td>" . $counted['votes'] . '</td></tr>';
			$questions[] = $altrow['question'];
			$votecounts[] = $counted['votes'];
			$votecount += $counted['votes'];
		}
		for($i = 0; $i < count($questions); $i++){
			if($votecount == 0){
				$output .= '<tr><td colspan=2>Ingen stemmer avgitt.</td></tr>';
				break;	
			}
			else
				$output .= '<tr><td class="pollquestion">' . $questions[$i] . "</td><td>" . (int)($votecounts[$i]/$votecount*100) . '%</td></tr>';				
		}
		

		$questions = array();
		$votecounts = array();
		$votecount = 0;		
		
	}
	
	$output .= '<tr><td colspan=2 class="showallpolls"><a href="index.php?m_c=module_oldpolls">Vis alle</a></td></tr></table>';
	return $output;
}

function module_poll(){
	$activepolls = display_results();	
	
	if(strlen($activepolls) > 160){
		echo '<div class="poll">';
	}	
	
	if(isset($_SESSION['valid_user'])){

		if(!isset($_REQUEST['pollaction']))
			$pollaction ="";
		else
			$pollaction = $_REQUEST['pollaction'];			
			
		if($pollaction == "votecast"){
			$pollid = $_REQUEST['pollid'];
			$regvote = "INSERT INTO vote VALUES (" . $pollid . ", '" . $_SESSION['valid_user'] . "'," . $_REQUEST['chosenalt'] . ");";

			$result = DB_insert($regvote);
			
			if($result){
				echo "Takk for din stemme. Resultatet under oppdateres neste gang du viser siden.";	
			}else{
				echo "Databaseproblem - din stemme ble ikke registrert. Skriv gjerne en lynforumpost om nøyaktig hva som skjedde, her er en bug et sted.";
			}
			
		}


		$current_time = date('Y-m-d H:i');
		//echo $current_time;
		$findpolls = "SELECT pollid, title FROM poll WHERE time_opened < '" . $current_time . "' AND time_closed > '" . $current_time . "';";
		$pollresult = DB_get_table($findpolls);
		//echo $findpolls;
		
		$num_polls = DB_rows_affected($pollresult);
		
	
		
		
		while($rowpoll = DB_next_row($pollresult)){
			$pollid = $rowpoll['pollid'];
			if(!has_voted($_SESSION['valid_user'], $pollid)){
				if(!isset($findalt)){
					echo '<table class="polltable">';
					echo '<tr><td colspan=2 class="pollheader">Stem!</td></tr>';					
				}	
				echo '<tr><td colspan=2 class="polltitle">' . $rowpoll['title'] . '</td></tr>';	
				
				$findalt = "SELECT pollid, question,questionid FROM pollquestion WHERE pollid =" . $pollid . ";";
				$result = DB_get_table($findalt);
				$num_results = DB_rows_affected($result);
				
				if($num_results > 0){
					
					form_start_post();
					form_hidden("pollid", $pollid);		
					form_hidden("pollaction", "votecast");	
					
					while($row = DB_next_row($result)){
						echo '<tr><td class="pollquestion">' . $row['question'];	
		
						echo '</td><td><input type="radio" value="' . $row['questionid'] . '" name="chosenalt"></td></tr>';
						
					}
					echo '<tr><td colspan=2 class="pollinput">'; form_submit("s", "Stem"); echo '</td></tr>';
					form_end();
				
				}
			}		
			
		}
		if(isset($findalt))
		echo '</table>';
		
		
	}
	
	// Vis resultater for alle aktive polls
	if(strlen($activepolls) > 160){
		echo $activepolls . '</div>';
	}	
	

	
	
	
	
	
	
	
}


function module_polladmin(){
 	// adminpage, stop here if not logged in/right access-level
	if (!isValidAdmin()) {
		echo (getString("not_valid_admin", "Administratorside, du må logge inn for å få tilgang her"));
		return;
	}	
	
	echo '<a href="http://localhost/avisCMS/index.php?m_c=module_polladmin&page_title=Polladmin">Tilbake til oversikt</a>';
	
	$pollaction = $_REQUEST['pollaction'];
	
	
	if($pollaction == 'addpoll'){
		
		if(strlen($_REQUEST['polltitle']) < 1){
			echo "Husk tittel.";
			return;	
		}
		
		echo '<div class="default_header">Avstemning opprettet.</div>';	

		$query = "INSERT INTO poll SET title='" . $_REQUEST['polltitle'] . "';";
		$result = DB_insert($query);
		

		
		if($result){
			echo '<a href="index.php?m_c=module_polladmin&amp;pollaction=editpoll&amp;pollid=' . mysql_insert_id() . '">Rediger den nye pollen</a>';	
		}else{
			echo "Feilmelding: " . mysql_error();;
		}
	
	}else if($pollaction == 'delpoll'){
		$confirm = $_REQUEST['dc'];
		$pollid = $_REQUEST['pollid'];
		
		if($confirm == "yes"){
			$query = "DELETE FROM poll WHERE pollid = " .  $pollid . ";";
			$result = DB_update($query);
			$num_results += DB_rows_affected($query);
			
			$query = "DELETE FROM pollquestion WHERE pollid = " .  $pollid . ";";
			$result = DB_update($query);
			$num_results += DB_rows_affected($query);
			
			$query = "DELETE FROM vote WHERE pollid = " .  $pollid . ";";
			$result = DB_update($query);
			$num_results += DB_rows_affected($query);			
			
			
			if($num_results < 1){
				echo "<br/>Ingenting slettet - feilmelding: " . mysql_error();
			}else{
				echo "<br/>Avstemningen med tilhørende stemmer og det hele aldeles pulverisert.";
			}
			
		}else{
			echo "<br/><br/>Sikker på at du vil slette avstemning med id " . $pollid . "? Dette medfører også sletting av alle tilknyttede spørsmål og avlagte stemmer!!<br/>";
			echo '<a href="index.php?m_c=module_polladmin&amp;pollaction=delpoll&amp;dc=yes&amp;pollid=' . $pollid . '">Ja!</a>';
		}
		
	
	}else if($pollaction == 'editpoll'){
		
		$pollaction2 = $_REQUEST['pollaction2'];
		$pollid = $_REQUEST['pollid'];
		$question = $_REQUEST['question'];
		$description = $_REQUEST['description'];
		
		if($pollaction2 == "changetime"){
			$query = "UPDATE poll SET description = '" . $description . "', time_opened='" . $_REQUEST['time_opened'] . "', time_closed='" . $_REQUEST['time_closed'] . "' WHERE pollid=" . $pollid . ";";
			DB_update($query);
			
			if(!result){
				echo 'mysql_error()';
					
			}
			
			
			
			
			
		}
		
		if($pollaction2 == "delquestion"){
			$altid = $_REQUEST['altid'];
			$query = "DELETE FROM pollquestion WHERE questionid=" . $altid . " AND pollid=" . $pollid . ";";
			$result = DB_update($query);
			//echo $query;
			if(!$result){
				echo mysql_error();	
			} 
			
		}
		
		if($pollaction2 == 'addquestion'){
			$querymax = "SELECT MAX(questionid) as maxid FROM pollquestion;";
			$row = DB_search($querymax);
			$newid = $row['maxid'] + 1;
			$query = "INSERT INTO pollquestion SET pollid=" . $pollid . ", questionid='" . $newid . "', question='" . $question . "';";
			//echo $query;
			$result = DB_insert($query);
			if(!result){
				echo mysql_error();	
			}
		}
		
		
		$pollid = $_REQUEST['pollid'];
		$query = "SELECT * FROM poll WHERE pollid=" . $pollid . ";";
		$row = DB_search($query);
		$query_questions = "SELECT * FROM pollquestion WHERE pollid=" . $pollid . ";";
		$result = DB_get_table($query_questions);
		$pollid = $row['pollid'];
		echo '<table class="default_table">';
			
		echo '<tr><td colspan=2><div class="default_header">Rediger spørreundersøkelse</div></td></tr>';	
		echo "<tr><td>Tittel</td><td>" . $row['title'] . "</td></tr>";
		form_start_post();
		form_hidden("pollid", $pollid); form_hidden("m_c", "module_polladmin");
		form_hidden("pollaction", "editpoll"); form_hidden("pollaction2", "changetime");
		echo "<tr><td>Beskrivelse (300 tegn)</td><td>" . $row['description'] . "</td><td>"; form_textarea("description", $row['description'],10,10); echo "</td></tr>";
		echo "<tr><td>Dato start</td><td>" . $row['time_opened'] . "</td><td>"; form_textfield("time_opened", $row['time_opened']); echo "</td></tr>";				
		echo "<tr><td>Date slutt</td><td>" . $row['time_closed'] . "</td><td>"; form_textfield("time_closed", $row['time_closed']); echo "</td></tr>";	
		echo "<tr><td colspan=2>Datoformat: 2005-01-31 23:10<br/>Utelat tidspunkt og det settes til 00:00.</td><td>"; form_submit("submit", "Lagre endringer"); form_end(); echo "</tr>";
		
		while($row = DB_next_row($result)){
			echo '<tr>';
			echo '<td>' . $row['questionid'] . '</td>';
			echo '<td>' . $row['question'] . '</td>';
			echo '<td>'; form_start_post(); form_submit("submit", "Slett"); 
			form_hidden("m_c", "module_polladmin"); form_hidden("pollaction2", "delquestion");
			form_hidden("altid", $row['questionid']); form_hidden("pollaction", "editpoll"); 
			form_hidden("pollid", $pollid);form_end(); 
			echo '</td>';
			echo '</tr>';
			
		}
		echo '</table><br/><br/>';
		
		echo '<table class="default_table">';
		echo '<tr><td colspan=2>Legg til et alternativ</td></tr>'; 

		form_start_post();
		echo '<tr><td>Alternativnavn</td><td>'; form_textfield("question", $_SESSION['question']); echo '</td></tr>';
		echo '<tr><td colspan=2>'; form_submit("submit", "Legg til"); echo '</td></tr>';
		form_hidden("pollaction", "editpoll");
		form_hidden("pollaction2", "addquestion");
		form_hidden("pollid", $pollid);
		form_hidden("m_c", "module_polladmin");
		form_end();
		echo '</table>';
	
	
	
	
	}else{
		


		echo '<table class="default_table">';
			
		echo '<tr><td colspan=4><div class="default_header">Polladmin</div></td></tr>';	
		echo "<tr><td colspan=4>Lag en ny</td></tr>";
		form_start_post();
		echo "<tr><td colspan=2>Tittel</td><td colspan=2>"; form_textfield("polltitle", $_SESSION['polltitle']); echo '</td></tr>';
		echo '<tr><td colspan=4>'; form_submit("submit", "Opprett(rediger den for å fullføre)"); echo '</td></tr>';
		form_hidden("pollaction", "addpoll");
		form_hidden("m_c", "module_polladmin");
		form_end();
		echo '<tr><td colspan=2></td></tr>';
		
		echo '<tr><td colspan=4><div class="default_header">Eksisterende polls</div></td></tr>';	
		$query = "SELECT * FROM poll";
		$result = DB_get_table($query);
		
		
		echo '<tr><td>Tittel</td><td>Start</td><td>Slutt</td><td>Rediger</td></tr>';
		while($row = DB_next_row($result)){
			echo '<tr><td>' . $row['title'] . '</td><td>' . $row['time_opened'] . '</td>';
			echo '<td>' . $row['time_closed'] . '</td>';
			echo '<td><a href="index.php?m_c=module_polladmin&amp;pollaction=editpoll&pollid=' . $row['pollid'] . '">Rediger</a>';
			
			echo '<br/><a href="index.php?m_c=module_polladmin&amp;pollaction=delpoll&pollid=' . $row['pollid'] . '">Slett</a></td>'; 
			echo '</tr>';
		}	
		
		echo '</table>';
	}

}









?>