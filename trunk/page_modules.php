<?
// Encoding: UTF-8
require_once("common.php");

function styleConfig(){
	echo '<div class="beta_box">';
	div_open("beta_header");
		echo "Personlig stilvalg - under utprøving! (beta)";
	div_close();
	echo'<div id="testediv"> </div>';
	echo'<div id="xhrfeedback"> </div>';
	h3("Velg en stil");
	form_start_custom("styleedit", "javascript:changeUserStyle('modules/styleService.php')", "POST");
	?>

	<select id="userStyles">
		<option value="NULL">Velg stilark</option>
		<option value="NULL">Gå tilbake til standard</option>
	</select>

	<script type="text/javascript">
		fetchStyles('modules/styleService.php');
	</script>
	<?php
	form_submit("submit", "Bruk valgt stil");
	form_end();


	h3("Legg opp en ny stil");
	form_start_custom("styleadd", "javascript:addUserStyle('modules/styleService.php')", "POST");
	form_textfield2("Navn", "styleName", ""); br();
	form_textfield2("URL", "styleURL", ""); br();
	form_submit("submit", "Legg opp stil"); br();
	form_end();

	h3("Stiler du kan slette");
	echo ("(Disse er lagt opp av deg og ikke i bruk av noen andre for øyeblikket.)");
	div_open("", "", "deleteStyleList");

	div_close();
	div_close();
}

// Module om Calcuttagutta
function mcg(){
	// Om Calcuttagutta
	echo '<table class="default_table">';
	echo "<tr><td colspan=2><div class=\"default_header\">Om Calcuttagutta</div>";
	echo '</td></tr><tr><td>Vi er en gjeng med gutter og jenter som skriver om alt mellom himmel og jord, anført av sjefsskribent Tor. Teknologien tar Are og Anders seg av; Calcuttagutta hviler på kilovis med spaghettikode kokt i PHP og en SQL-database.';
	echo '<br/><br/>For en introduksjon til Calcuttagutta anbefaler vi Tors artikkel <a href="http://www.calcuttagutta.com/index.php?m_c=va&articleid=4287">An introduction to Calcuttagutta</a>.';
	echo '</td></tr><tr><td><div class="default_header">Diverse</div>';
	echo '<br/>Vi har en butikk på cafepress.com: <a href="http://www.cafepress.com/calcuttagutta">http://www.cafepress.com/calcuttagutta</a>';
	echo '<br><br>Liste over alle avstemninger finner du her: <a href="index.php?m_c=module_oldpolls">Gamle avstemninger</a>';

	echo '</td></tr></table>';
	echo '';
}

function module_birthday(){
 $today = date("m-d");
 $query = "SELECT username, firstname FROM user WHERE birthdate LIKE '%" . $today . "'";

 $result = DB_get_table($query);

 $num_results = DB_rows_affected($result);

 if($num_results > 0){

	 echo '<div class="poll">';
	 echo '<div class="pollheader">';
	 echo 'Bursdagsbarn!</div>';
	 echo '<div class="birthdaylink">';
	 while($row = DB_next_row($result)){
	 	echo '<a href="index.php?m_c=mvp&username=' . $row['username'] . '">' . $row['firstname'] . '</a><br/>';
	 }
	  echo '</div> Hipp hurra, gratulerer med dagen!';


 echo '</div>';

 }


}

function module_recentarticles(){
 /*
 $today = date("m-d");
 $LIMIT = 10;
 $query = "SELECT articleid, title FROM articles WHERE comment_to IS NULL AND is_draft IS NULL ORDER BY DATE_POSTED DESC, TIME_POSTED DESC LIMIT " . $LIMIT . ";";

 $result = DB_get_table($query);

 $num_results = DB_rows_affected($result);

 if($num_results > 0){


	 echo '<div class="recentarticles_header">';
	 echo $LIMIT . ' siste artikler</div>';
	 echo '<div class="recentarticles_links">';
	 while($row = DB_next_row($result)){
	 	echo '<div class="recentarticles_link"><a href="index.php?m_c=m_va&articleid=' . $row['articleid'] . '">' . $row['title'] . '</a></div>';
	 }
	  echo '</div>';



 }
 */
 echo '<div class="recentarticles">';
 echo '<div class="rss_feed"><a href="rss.xml">RSS-mating</a></div>';

 echo '</div>';

}

function module_admin(){
 	// adminpage, stop here if not logged in/right access-level
	if (!isValidAdmin()) {
		echo (getString("not_valid_admin", "Administratorside, du mÃ¥ logge inn for Ã¥ fÃ¥ tilgang her"));
		return;
	}

	if (isset($_REQUEST['admin_action']))
		$action = $_REQUEST['admin_action'];
	else
		$action = "";

	if($action == "confirmpurge"){
		$articleid = $_REQUEST['purgeid'];
		echo '<div class="default_header">Bekreft sletting av fil '  . $articleid . '</div>';
		form_start_post();
		form_hidden("m_c", "module_admin");
		form_hidden("admin_action", "deleteforgood");
		form_hidden("purgeid", $articleid);
		form_submit("submit", "Ja, slett filen for godt");
		form_end();


	}else if($action == "deleteforgood"){
		$articleid = $_REQUEST['purgeid'];
		$query = "DELETE FROM articles WHERE articleid = " . $articleid . ";";
		$result = DB_update($query);
		if($result){
			global $logtype;
			write_log_entry($_SESSION['valid_user'], $logtype['user'], "admin_purgedfile,");
			echo '<div class="default_header">Filen er slettet.</div>';
		}else{
			echo '<div class="default_header">Sletting mislyktes.</div>';
		}

	}else if($action == "showfile"){
		$articleid = $_REQUEST['articleid'];
		$query = "SELECT * FROM articles WHERE articleid =" . $articleid . " AND (is_draft IS NULL OR is_draft=0);";

		$result = DB_get_table($query);
		$num_results = DB_rows_affected($query);
		$row = DB_next_row($result);

		echo "<div class=\"header2\">" . $row['title'] . "</div>";
		echo "<div class=\"metatext\">";
		echo "<span class='author'>Forfatter: ";
			if(isset($row['author_username'])){
				echo '<a href="index.php?m_c=mvp&amp;username=';
				echo $row['author_username'] . '">' . stripslashes($row['author']) . '</a>';
			}else{
				echo stripslashes($row['author']);
			}
		echo ', ';

		if($row['author_username']){
		    echo "  (" . stripslashes($row['author_username']) . ").</span>";
		}else{
		    echo "  (" . $unknown_author . ").</span>";
		}


		echo "<span class=\"time\">Lagt opp: " . make_date($row['date_posted']) . ", " . make_time($row['time_posted']) . "</span></div>";
		echo "<div class=\"textbody\">" . stripslashes(nl2br($row['body'])) . "</div>";




	}else if($action == "showstats"){
		$query = "SELECT articleid, author, view_count, title FROM articles WHERE view_count > 0 ORDER BY view_count DESC;";
		$result = DB_get_table($query);
		$num_rows = DB_rows_affected($result);

		echo '<table class="default_table">';
			echo "<tr>";
			echo '<td>Artikkel-ID</td><td>Tittel</td><td>Forfatter</td>';
			echo '<td>Antall visninger</td></tr>';
		while($row = DB_next_row($result)){
			echo "<tr>";
			echo '<td>' . $row['articleid'] . '</td><td>' . $row['title'] . '</td><td>' . $row['author'] . '</td>';
			echo '<td>' . $row['view_count'] . '</td><td>';
			form_start_get();
			form_hidden("m_c", "m_va");
			form_hidden("articleid", $row['articleid']);
			form_submit("submit", "Les artikkel");
			form_end();
			echo '</td></tr>';

		}
		echo '</table>';

	}else if($action == "undelete"){
		$articleid = $_REQUEST['articleid'];
		$query = "UPDATE articles SET is_deleted = NULL WHERE articleid = " . $articleid . ";";
		$result = DB_update($query);
		echo $query;
		if($result){
			global $logtype;
			write_log_entry($_SESSION['valid_user'], $logtype['user'], "admin_restoredfile,");
			echo "Vellykket gjenoppretting.";
		}else{
			echo "Gjenoppretting mislyktes.";
		}


	}else if($action == "listdeleted"){
		$query = "SELECT time, comment_to, articleid, title, author, ip FROM articles,eventlog WHERE articleid = itemid AND is_deleted IS NOT NULL;";
		$result = DB_get_table($query);
		$num_rows = DB_rows_affected($result);

		echo '<table class="default_table">';
			echo "<tr>";
			echo '<td>Artikkel-ID</td><td>Kommentar til</td><td>Tittel</td><td>Forfatter</td>';
			echo '<td>IP</td><td>Tidspunkt</td></tr>';
		while($row = DB_next_row($result)){
			echo "<tr>";
			echo '<td>' . $row['articleid'] . '</td><td>' . $row['comment_to'] . '</td><td>' . $row['title'] . '</td><td>' . $row['author'] . "</td><td>" . $row['ip'] . '</td><td>'. $row['time'] . '</td>';
			echo '<td>';
			form_start_post();
			form_hidden("m_c", "module_admin");
			form_hidden("admin_action", "confirmpurge");
			form_hidden("purgeid", $row['articleid']);
			form_submit("submit", "Slett for godt");
			form_end();
			echo '</td><td>';
			form_start_get();
			form_hidden("m_c", "module_admin");
			form_hidden("admin_action", "showfile");
			form_hidden("articleid", $row['articleid']);
			form_submit("submit", "Les");
			form_end();
			echo '</td><td>';
			form_start_get();
			form_hidden("m_c", "module_admin");
			form_hidden("admin_action", "undelete");
			form_hidden("articleid", $row['articleid']);
			form_submit("submit", "Gjenoppliv");
			form_end();

			echo '</td><td>';


			echo '</td></tr>';


		}
		echo '</table>';

	}else if($action == "viewlog"){
		$type = $_REQUEST['type'];
		$date = $_REQUEST['date'];
		global $logtype;

		if(isset($type)){
			$sqldate = $_REQUEST['year'] . "-" . $_REQUEST['month'] . "-" . $_REQUEST['day'];

			$sqldate = date("Y-m-d",strtotime($sqldate)	);

			$query = "SELECT * FROM eventlog WHERE time LIKE '" . $sqldate . "%' AND log_type=" . $type . ";";


			$result = DB_get_table($query);
			$num_results = DB_rows_affected($result);
			$types_array = array_flip($logtype);
			echo '<table class="default_table">';
			echo '<tr><td>Event ID</td><td>Logtype</td><td>Affected item-ID</td><td>Resp. user</td><td>Description</td><td>IP</td><td>Time</td></tr>';
			while($row = DB_next_row($result)){
				echo '<tr>';
				echo '<td>' . $row["eventid"] . '</td><td>'.  $types_array[$row["log_type"]] . '</td><td>' . $row["itemid"] . '</td><td>' . $row["username"] . '</td>';
				echo '<td>'  . $row["event_type"] . '</td><td>'  . $row["ip"] . '</td><td>'  . $row["time"] . '</td></tr>';

			}

			echo '</table>';

		}else{
			form_start_post();
			echo 'Type of log-entry:';
			echo '<select name="type" >';
			;
			foreach($logtype as $id => $logtypes){
				echo '<option value="' . $logtypes . '">' . $id . '</option>\n';
			}

			echo '</select>';
			echo '<br/>For this day: ';
			form_datewidget(date("Y-m-d"));
			form_hidden("m_c", "module_admin");
			form_hidden("admin_action", "viewlog");
			form_submit("Submit", "Vis");
			form_end();


		}


	}else{

		echo '<div class="default_list"><a href="index.php?m_c=module_admin&amp;page_title=Slettede_filer&amp;admin_action=listdeleted">Vis slettede filer</a>';
		echo '<br/><a href="index.php?m_c=module_admin&amp;page_title=Stat&amp;admin_action=showstats">Vis statistikk</a>';
		echo '<br/><a href="index.php?m_c=module_files&amp;page_title=Filadmin">Fil(bilde)admin</a>';
		echo '<br/><a href="index.php?m_c=module_user_admin&amp;page_title=User+administration">Brukeradmin</a>';
		echo '<br/><a href="index.php?m_c=module_admin&amp;page_title=User+administration&amp;admin_action=viewlog">Logger</a>';
		echo '<br><a href="index.php?m_c=module_register_form&amp;page_title=Register<+new+user">Registrer en ny bruker</a>';
		echo '<br><a href="index.php?m_c=module_admininput&amp;page_title=Admininput">Legg inn en artikkel</a>';
		echo '<br><a href="index.php?m_c=module_polladmin&amp;page_title=Polladmin">Administrer polls</a>';
		echo '<br><a href="index.php?m_c=module_categoryadmin&amp;page_title=Categoryadmin">Opprett, slett og endre kategorier</a>';
		echo '<br><a href="index.php?m_c=showSettingsGUI">' . getString("admin_edit_settings", "Innstillinger") . '</a>';
		echo '</div>';

	}







}


function module_categoryadmin(){
 	// adminpage, stop here if not logged in/right access-level
	if (!isValidAdmin()) {
		echo (getString("not_valid_admin", "Administratorside, du mÃ¥ logge inn for Ã¥ fÃ¥ tilgang her"));
		return;
	}

	// Check whether user has sent category creation data
	if(isset($_REQUEST['action'])) $action = $_REQUEST['action'];
	else $action ="";

	if ($action == "createcategory"){
		if (isset($_REQUEST['categoryname'])){
			$insertquery = "INSERT INTO category (name) VALUES ('" . $_REQUEST['categoryname'] . "')";
			$insertresult = DB_insert($insertquery);
			if ($insertresult)
				echo "Category " . $_REQUEST['categoryname'] . " added.<br/>";
			else
				echo "Adding category failed.<br/>";
		}

	} else if ($action == "deletecategory"){

		if (isset($_REQUEST['categoryid'])){
			if (isset($_REQUEST['confirmdeletecategory'])){
				$deletequery = "DELETE FROM category WHERE categoryid = " . $_REQUEST['categoryid'] . ";";

				$deleteresult = DB_update($deletequery);
				if ($deleteresult){
					echo "Category deleted.<br/>";
				}else{
					echo "Deletion failed. Maybe there are existing articles in this category?<br/>";
				}
			}else{
				echo "Please confirm that you wish to delete category " . $_REQUEST['categoryname'] . ". Click anywhere else to abort.<br/>";
				form_start_post();
				form_hidden("m_c", "module_categoryadmin");
				form_hidden("action", "deletecategory");
				form_hidden("categoryid", $_REQUEST['categoryid']);
				form_hidden("confirmdeletecategory", "1");
				form_submit("submit", "Confirm deletion");
				form_end();
				br();
			}

		}


	} else if ($action == "editcategory"){

		if (isset($_REQUEST['categoryid'])){

			if(isset($_REQUEST['confirmeditcategory'])){
				$updatequery = "UPDATE category SET name = '" . $_REQUEST['categoryname'] . "' WHERE categoryid = " . $_REQUEST['categoryid'] . ";";

				$updateresult = DB_insert($updatequery);
				if ($updateresult)
					echo "Category " . $_REQUEST['categoryname'] . " updated.<br/><br/>";
				else
					echo "Updating category failed.<br/><br/>";

			}else{
				form_start_post();
				echo("Category name:");
				br();
				form_textfield("categoryname",$_REQUEST['categoryname']);
				form_hidden("m_c", "module_categoryadmin");
				form_hidden("action", "editcategory");
				form_hidden("categoryid", $_REQUEST['categoryid']);
				form_hidden("confirmeditcategory", "1");
				form_submit("submit", "Save edit");
				form_end();
				br();br();
			}
		}
	}

	// Query for existing categories
	$query = "SELECT categoryid, name FROM category;";
	$result = DB_get_table($query);
	$num_results = DB_rows_affected($result);

	if($num_results == 0)
		echo "No existing categories.";
	else
		echo "Existing categories:";

	table_open();
	while ($row = DB_next_row($result)){
		tr_open();
			td_open(1); echo $row['categoryid']; td_close();
			td_open(1); echo $row['name']; td_close();
			td_open(1);
				form_start_post();
				form_hidden("m_c", "module_categoryadmin");
				form_hidden("action", "editcategory");
				form_hidden("categoryname", $row['name']);
				form_hidden("categoryid", $row['categoryid']);
				form_submit("submit", "Edit");
				form_end();
			td_close();
			td_open(1);
				form_start_post();
				form_hidden("m_c", "module_categoryadmin");
				form_hidden("action", "deletecategory");
				form_hidden("categoryname", $row['name']);
				form_hidden("categoryid", $row['categoryid']);
				form_submit("submit", "Delete");
				form_end();
			td_close();
		tr_close();
	}
	table_close();


	echo "<br/>Create new category<br/>";
	form_start_post();
	echo "Name: "; form_textfield("categoryname", "");
	form_hidden("m_c", "module_categoryadmin");
	form_hidden("action", "createcategory");
	form_submit("submit", "Create");
	form_end();


}

function module_files(){
	// Switch case on REQUEST['files_action'].

	// action == no set  -display images

	// action == delete image, get image ID and ask for confirmation

	// action == delete image confirmed, get image ID and delete it

	// action == upload. Upload image and return ID and URL.

 	// adminpage, stop here if not logged in/right access-level
	if (!isValidAdmin()) {
		echo (getString("not_valid_admin", "Administratorside, du mÃ¥ logge inn for Ã¥ fÃ¥ tilgang her"));
		return;
	}

	$action = $_REQUEST['files_action'];

	if ($action == "confirmdelete"){
		$query = "SELECT username FROM user WHERE picture=" . $_REQUEST['fileid'] . ";";
		$result = DB_get_table($query);

		if($result){
				while($row = DB_next_row($result)){
				$query = "UPDATE user SET picture = NULL where username='" . $row['username'] . "'";
				$result = DB_update($query);
				if(!$result){
					echo "Kunne ikke nullstille bildefeltet i brukertabellen.";
					return;

				}
			}

		}

		$querydelete = "DELETE FROM files WHERE picid =" . $_REQUEST['fileid'];
		$result = DB_update($querydelete);

		if(!$result){
			echo "Bildet ble ikke slettet.";
		}else{
			echo "Fil slettet.";
		}

	}else if ($action == "delete"){
		echo "Bekreft sletting av fil med id " . $_REQUEST['fileid'] . ".";
		form_start_post();
		form_hidden("fileid", $_REQUEST['fileid']);
		form_hidden("files_action", "confirmdelete");
		form_hidden("m_c", "module_files");
		form_submit("submit", "Slett filen");
		form_end();

	}else if ($action == "upload"){

	}else if ($action == "viewfile"){
		$query = "SELECT username FROM user WHERE picture=" . $_REQUEST['fileid'] . ";";
		$result = DB_get_table($query);

		if(strstr($_REQUEST['filetype'], 'image') != false){
			echo '<img class="image_admin" src="mod_pic.php?id=' . $_REQUEST['fileid']. '" alt="' . $result['firstname'] . '" /><br/>';
			form_start_post();
			form_hidden("fileid", $_REQUEST['fileid']);
			form_hidden("files_action", "delete");
			form_hidden("m_c", "module_files");
			form_submit("submit", "Delete file");
			form_end();
			echo "<br/>";

			if($result){
				echo "File linked to in these user's profiles (deleting will set the concerned field to NULL):<br/>";
				while($row = DB_next_row($result)){
					echo $row['username'] . '<br/>';
				}

			}
		}else{
			echo "Kan ikke vise filtypen.";
		}



	}else{

		$query = "SELECT picid, description, filetype, pic_width, pic_height, size FROM files";
		$result = DB_get_table($query);
		echo '<table class="default_table">';
		echo '<tr>';
		echo '<td>File ID</td>';
		echo '<td>Description</td>';
		echo '<td>File type</td>';
		echo '<td>Picture size</td>';
		echo '<td>Size (KB)</td>';
		echo '<td>Edit</td>';
		echo '</tr>';

		$num_rows = DB_rows_affected($result);
		if($num_rows < 1)
		echo '<tr><td colspan=6>Ingen filer i databasen.</td></tr>';

		while($row = DB_next_row($result)){
			echo '<tr>';
			echo '<td>' . $row['picid'] . '</td>';
			echo '<td>' . $row['description'] . '</td>';
			echo '<td>' . $row['filetype'] . '</td>';
			echo '<td>' . $row['pic_width'] . ' x ' . $row['pic_height'] . '</td>';
			echo '<td>'; printf	("%u", ($row['size'] / 1024)); echo '</td>';
			echo '<td>';
			form_start_post();
			form_hidden("fileid", $row['picid']);
			form_hidden("files_action", "viewfile");
			form_hidden("filetype", $row['filetype']);
			form_submit("submit", "View file");
			form_hidden("m_c","module_files");
			form_end();

			form_start_post();
			form_hidden("fileid", $row['picid']);
			form_hidden("files_action", "delete");
			form_hidden("m_c", "module_files");
			form_submit("submit", "Delete file");
			form_end();

			echo '</td>';
			echo '</tr>';

		}
		echo '</table>';


	}
}

function module_listusers(){
	echo "<!-- start module listusers -->";
	echo '<div class="userlist">';
	echo '<div class="userlist_heading">Spaltister</div>';

	// Old query, giving a list of all users with article-creation permissions
	// $query = "SELECT firstname, username FROM user WHERE may_post=1 ORDER BY firstname, username";

	// New query - all writers active during last X days
	// and top X writers
	$query = "SELECT distinct u.firstname, a.username FROM (
		(SELECT author_username username, count(author_username) AS sum_posts
		FROM articles
		WHERE comment_to IS NULL
		GROUP BY author_username
		ORDER BY sum_posts DESC
		LIMIT 5)
		UNION
		(SELECT author_username username, count(author_username) AS sum_posts
		FROM articles
		WHERE comment_to IS NULL
		AND DATE_SUB(CURDATE(), INTERVAL 14 DAY) <= date_posted
		GROUP BY author_username
		ORDER BY sum_posts DESC)
		) AS a, user u WHERE a.username=u.username;";

	$result = DB_get_table($query);

	echo '<div class="userlist_links">';
	while($row = DB_next_row($result)){
		if($row['username'] == "admin"){
			;
		}else{

			echo '<a href="index.php?m_c=mvp&amp;username=' . $row['username'] . '">';
			if(strlen($row['firstname']) > 1){
				echo $row['firstname'] ."<br/></a>";
			}else{
				echo $row['username'] ."<br/></a> ";
			}
		}
	}

	echo '</div>';
	echo '<div class="viewmemberlist"><a href="index.php?m_c=module_memberlist">Medlemsliste</a></div>';
	echo '</div>';
}

// module flashforum archive
function mfa(){
	//module flashforum archive
	global $months;
	$monthkeys = array_keys($months);

	$START_YEAR = 2005;
	$END_YEAR = date("Y");

	echo '<table class="default_table">';
	echo '<tr><td colspan=2 class="default_header">Lynforumarkiv</td></tr>';
	echo '<form method="post" action="index.php">';
	form_hidden("m_c", "mfa");
	echo '<tr><td>Velg år</td><td>';

	if(isset($_REQUEST['mfa_y']))
		form_select_number("mfa_y",$START_YEAR,$END_YEAR,$_REQUEST['mfa_y']);
	else
		form_select_number("mfa_y",$START_YEAR,$END_YEAR,date("y"));

	echo '</td></tr>';
	echo '<tr><td>Velg måned</td><td>';

	if(isset($_REQUEST['mfa_m']))
		form_dropdown("mfa_m", $monthkeys, $months,$_REQUEST['mfa_m'] - 1);
	else
		form_dropdown("mfa_m", $monthkeys, $months,date(m) - 1);

	echo '</td></tr>';
	echo '<tr><td colspan=2>'; form_submit("mfa_s", "Vis"); echo '</td></tr></form>';

	if(isset($_REQUEST['mfa_y']) && isset($_REQUEST['mfa_m'])){
		if(strlen($_REQUEST['mfa_m']) == 1){
			$month = "0" . $_REQUEST['mfa_m'];
		}

		$year = $_REQUEST['mfa_y'];
		$query = "SELECT * FROM flashforum WHERE time_posted LIKE '" . $year . "-" . $month . "%';";
		$result = DB_get_table($query);
		echo '<tr><td colspan=2 class="default_header">Lynformposter fra ' . $months[$_REQUEST['mfa_m']] . ' ' . $_REQUEST['mfa_y'] . '</td></tr>';

		$num_res = DB_rows_affected($result);
		if($num_res == 0){
			echo '<tr><td colspan=2 class="default_cursive">Ingen lynforumposter fra ' . $months[$_REQUEST['mfa_m']] . ' ' . $_REQUEST['mfa_y'] . '.</td></tr>';
		}

		while($row = DB_next_row($result)){
			$date = date_nor_sql(substr($row['time_posted'],0,10));
			echo '<tr><td colspan=2 class="commentscell"><div class="commentmetatext"><span class="commentauthor">' . $row['author']. "</span>, ";
			echo '<span class="commentdate">' . substr($date,0,5) . '</span> ';
			echo '<span class="commenttime">' . substr($row['time_posted'],11,5) . '</span></div>';
			echo '<div class="textbody_comment">' . $row['message'] . "</div></td></tr>";




		}

	}else{


	}
	echo '</table>';


}


function module_flashforum(){
	echo "<!-- start module flashforum -->";
	global $flashformid;
	$thisdate = "";

	if(isset($_REQUEST['quickpassword']))
		$quickpassword = $_REQUEST['quickpassword'];
	else
		$quickpassword = "";


	if(isset($_REQUEST['message']))
		$message = $_REQUEST['message'];

	if(isset($_REQUEST['deleteflash']))
		$deleteflash = $_REQUEST['deleteflash'];

	if(isset($_REQUEST['deleteflash'])){

		if(isset($_SESSION['valid_admin']) && isset($_REQUEST['dfc'])){
			$query = "DELETE FROM flashforum WHERE postid=" . $_REQUEST['deleteflash'] . ";";
			$result = DB_update($query);
			if($result){
					global $logtype;
					write_log_entry($_REQUEST['deleteflash'], $logtype['flashforum'], "del_flashpost,");
			}
		}else{
		echo '<a href="index.php?deleteflash=' . $_REQUEST['deleteflash'] . '&amp;dfc=1">Bekreft sletting</a><br/>';
		}
	}

	echo '<div class="flashforum"><div class="flashforumheader">Lynforum</div>';
	echo '<div class="flashforumlist">';

	// Insert new message into the database if the spampassword is correct
	if (isset($message) && !isset($REQUEST['showall']) && ($flashformid == $_REQUEST['flashformidvar'])) {
		if(stristr($quickpassword, "hurra") == FALSE){
			echo "<b>Du må fylle inn spampassordet.</b>";
			if (isset($_REQUEST['author'])){
				$author = $_REQUEST['author'];
			}else{
				$author = "Ditt navn";
			}
			$retry = $message;
		}else{
			$flashformid++;
			$_SESSION['flashformid'] = $flashformid;

				if(($_REQUEST['author'] != "Ditt navn") && (strlen($_REQUEST['message'])>5) && (strlen($_REQUEST['author']) > 1)){
					// Insert new msg into DB if possible
					$author = $_REQUEST['author']; $message = $_REQUEST['message'];
					if(strlen($author) > 15)
						$author = substr($author, 0, 15);

					if(strlen($message) > 200)
						$message = substr($message, 0,200);

					if(isset($_SESSION['valid_user'])){
						$query = "INSERT INTO flashforum VALUES ('', '" . strip_tags($author) . "', '', '" . strip_tags($message, "<a>") . "', NOW(),'" . $_SESSION['valid_user'] . "');";
					}else{
						$query = "INSERT INTO flashforum VALUES ('', '" . strip_tags($author) . "', '', '" . strip_tags($message, "<a>") . "', NOW(),'');";
					}


					$result = DB_insert($query);
					if(!$result)
						echo "Whups, melding ikke lagret.<br/>";
					else{
						global $logtype;
						write_log_entry(mysql_insert_id(), $logtype['flashforum'], "new_flashpost,");
					}

				}else{
					echo "<b>Navn og beskjed må fylles inn.</b><br/>";
				}
		}
	}



	if(isset($_REQUEST['showall'])){
		// Display current articles
		$query = "SELECT postid, author,message,UNIX_TIMESTAMP(time_posted) as time_posted FROM flashforum ORDER BY time_posted DESC";
		$result = DB_get_table($query);
		$num_results = DB_rows_affected($result);


		if(!$num_results > 0){
			echo "Tomt for øyeblikket.<hr/>";
		}else{
			$i=0;
			while($num_results > 0 && ($row = DB_next_row($result)) && $i<100){
				if(date("d/m",$row['time_posted']) != $thisdate ){
					if(date("d/m",$row['time_posted']) == date("d/m")){
						$thisdate = date("d/m",$row['time_posted']);
						echo '<div class="flashdate">I dag:</div>';
					}else{
						$thisdate = date("d/m",$row['time_posted']);
						echo '<div class="flashdate">'. $thisdate . '</div>';
					}


				}
				$i++;
				echo '<div class="flashpost"><span class="flashauthor">' . $row['author'] . ': </span>' . htmlwrap($row['message'],20) . ' ('. date("H:i",$row['time_posted']);
				if(isset($_SESSION['valid_admin'])){
					echo ', <a href="index.php?deleteflash=' . $row['postid'] . '">slett</a>';
				}
				echo ")</div>";			}


			}




	}else{

		// Display current articles
		$query = "SELECT postid, author,message,UNIX_TIMESTAMP(time_posted) as time_posted FROM flashforum ORDER BY time_posted DESC";
		$result = DB_get_table($query);
		$num_results = DB_rows_affected($result);

		if(!$num_results > 0){
			echo "Tomt for øyeblikket.<br/>";
		}else{
			$i=0;
			while($num_results > 0 && ($row = DB_next_row($result)) && $i<10){
				if(date("d/m",$row['time_posted']) != $thisdate ){
					if(date("d/m",$row['time_posted']) == date("d/m")){
						$thisdate = date("d/m",$row['time_posted']);
						echo '<div class="flashdate">I dag:</div>';
					}else{
						$thisdate = date("d/m",$row['time_posted']);
						echo '<div class="flashdate">'. $thisdate . '</div>';
					}


				}
				$i++;
				echo '<div class="flashpost"><span class="flashauthor">' . $row['author'] . ': </span>' . htmlwrap(createLinks($row['message']),20) . ' ('. date("H:i",$row['time_posted']);
				if(isset($_SESSION['valid_admin'])){
					echo ', <a href="index.php?deleteflash=' . $row['postid'] . '">slett</a>';
				}
				echo ")</div>";			}


		}
		

	}
	?>
	</div>
	<form method="get" action="index.php">
	<?php
		if(isset($_SESSION['valid_user'])){
			echo '<input style="width:140px" type="text" name="author" value=' . $_SESSION['user_firstname'] . ' /><br/>';
			echo 'Hipp, hipp, <input size="5" readonly type="textfield" name="quickpassword" value="hurra"/>!';
		}else{
			if (isset($retry)){
				echo '<input style="width:140px" type="text" name="author" onFocus="this.value=wipeOut(this.value);" value="' . $author . '" /><br/>';
			} else {
				echo '<input style="width:140px" type="text" name="author" onFocus="this.value=wipeOut(this.value);" value="Ditt navn" /><br/>';
			}
			echo  'Hipp, hipp, <input size="5" type="textfield" name="quickpassword" />!';
		}

	?>


	<?php
		if(isset($retry)){
			?>
			<input onKeyDown="limitText(this.form.message,this.form.countdown,180);"
			onKeyUp="limitText(this.form.message,this.form.countdown,180);" style="width:140px" type="text" name="message" onFocus="this.value=wipeOut(this.value);" value="<?php echo $retry; ?>" />
			<?php
		} else {
			?>
			<input onKeyDown="limitText(this.form.message,this.form.countdown,180);"
			onKeyUp="limitText(this.form.message,this.form.countdown,180);" style="width:140px" type="text" name="message" onFocus="this.value=wipeOut(this.value);" value="Maks 180 tegn" />
			<?php
		}
	?>

	<input class="nice1" type="submit" name="submit" value="Post" style="width:80px">
	<input readonly type="text" name="countdown" size="3" value="180" style="width:30px">
	<input type="hidden" value="<? echo $flashformid; ?>" name="flashformidvar">

	</form>
	<br/><a href="index.php?showall=1">Vis siste 100</a>
	<br/><a href="index.php?m_c=mfa">Arkiv</a>
	<br/><a href="rss.xml?type=flash">RSS</a>
	</div>
	<?
}

function module_archive(){
	echo "<!-- start archive -->";
	global $months;
	$query = "SELECT DATE_FORMAT(date_posted, '%m') AS Maned, DATE_FORMAT(date_posted, '%c') AS ManedUtenNull, DATE_FORMAT(date_posted, '%Y') AS Ar FROM  articles WHERE comment_to IS NULL AND is_draft IS NULL GROUP BY DATE_FORMAT(date_posted, '%m %Y') ORDER BY date_posted DESC;";
	$result = DB_get_table($query);
	$num_results = DB_rows_affected($result);

	echo '<div class="archive">';
	echo '<div class="archive_heading">Arkiv</div>';
	if(!$num_results > 0){
		echo "Ingen artikler i arkivet.<br/>";
	}else{
		echo '<div class="archive_list">';
			while($row = DB_next_row($result)){
				echo '<a href="index.php?searchtype=bymonth&amp;m_c=module_article_search&amp;month=' . $row['Maned'] .  '&amp;year=' . $row['Ar'] . '">' . substr($months[$row['ManedUtenNull']],0,3) . ' ' . $row['Ar'] . '</a><br/>';
			}
		echo '</div>';
	}
	echo '</div>';
}

function mvp(){
	echo "<!-- start view profile -->";
	$username = $_REQUEST['username'];

	if(!isset($username)){
		echo "Ingen gyldig bruker angitt.";
	}else{

		$query = "SELECT may_post, username, firstname, lastname, webpage, description, birthdate, picture FROM user WHERE username='" . $username . "';";

		$query_articles = "SELECT articleid, title, date_posted FROM articles WHERE (date_posted <= '" . date("Y-m-d") . "' OR (time_posted <= '" . date("H:i") . "' AND date_posted <= '" . date("Y-m-d") . "')) AND author_username ='" . $username . "' AND is_deleted IS NULL AND comment_to IS NULL AND (is_draft = 0 OR is_draft IS NULL) ORDER BY date_posted DESC, time_posted DESC";
		debug($query_articles);
		$result = searchDB($query);

		if(!$result){
			echo "Fant ikke $username i databasen.";
		}else{
			echo '<table class="default_table">';
			if ($result['picture'] <> "") {
				echo "<tr><td><div class=\"default_header\">";

					if($result['may_post'] == 1)
						echo "Spaltistside: ";
					else
						echo "Medlemsside: ";

					echo $result['firstname'] . " " . $result['lastname'] . " " . "(" . $result['username'] . ")</div></td>";
					echo '<td><div class="user_picture"><img src="mod_pic.php?id=' . $result['picture'] . '" alt="' . $result['firstname'] . '" /></div></td>';
				echo "</tr>";
			} else {

				echo "<tr><td colspan=2><div class=\"default_header\">";
				if($result['may_post'] == 1)
					echo "Spaltistside: ";
				else
					echo "Medlemsside: ";

				echo $result['firstname'] . " " . $result['lastname'] . " " . "(" . $result['username'] . ")</div></td></tr>";

			}

			// Bad code; edit_profile introduces 1851 instead of 0000 in the DB for no birthdate given
			if(!strstr($result['birthdate'], "0000") && !strstr($result['birthdate'], "1851"))
				echo "<tr><td>Fødselsdato </td><td>" . date_nor_sql($result['birthdate']) . "</td></tr>";
			if(strlen($result['webpage']) > 1){
				if(substr($result['webpage'], 0, 4) == "http")
					echo "<tr><td>Webside</td><td> <a href=\"" . $result['webpage'] . "\" target=\"_blank\">" . $result['webpage'] . "</a></td></tr>";
				else
					echo "<tr><td>Webside</td><td> <a href=\"http://" . $result['webpage'] . "\" target=\"_blank\">http://" . $result['webpage'] . "</a></td></tr>";
			}

			/* Output user statistics */

			$statquery = "SELECT COUNT(articleid) AS count FROM articles WHERE is_draft IS NULL AND comment_to IS NULL AND is_deleted IS NULL AND author_username='" . $result['username'] . "';";
			$article_count = DB_search($statquery);
			if($article_count['count'] > 0)
				echo "<tr><td>Artikler</td><td>". $article_count['count']. "</td></tr>";

			$statquery = "SELECT COUNT(articleid) AS count FROM articles WHERE is_draft IS NULL AND comment_to IS NOT NULL AND is_deleted IS NULL AND author_username='" . $result['username'] . "';";
			$comment_count = DB_search($statquery);
			if($comment_count['count'] > 0)
				echo "<tr><td>Kommentarer</td><td>". $comment_count['count']. "</td></tr>";

			$statquery = "SELECT COUNT(postid) AS count FROM flashforum WHERE author_username='" . $result['username'] . "';";
			$flash_count = DB_search($statquery);
			if($flash_count['count'] > 0)
				echo "<tr><td>Lynforumposter</td><td>". $flash_count['count']. "</td></tr>";


			if(strlen($result['description']) > 1)
			echo "<tr><td colspan=2>" . stripslashes($result['description']) . "</td></tr>";

			$result2 = DB_get_table($query_articles);
			$num_results = DB_rows_affected($result2);
			if($num_results > 0){

				echo '<tr><td colspan=2><div class="default_header">Artikler</div><div class="default_list">';


			}else
				if($result['may_post'] == 1)
					echo '<tr><td colspan=2><div class="default_cursive">Ingen artikler av denne spaltisten i databasen.</div><div class="default_list">';

			while($row = DB_next_row($result2)){
				echo '<a href="index.php?m_c=m_va&amp;articleid=' . $row['articleid'] . '">' . stripslashes($row['title']) . '</a> (' . date_nor_sql($row['date_posted']) . ')<br/>';
			}
			echo '</div>';
			echo '</td></tr></table>';
		}
	}
}








function module_article_search(){
	global $months;
	echo "<!-- start search for article -->";

	echo "<table class=\"default_table\"><tr><td colspan=2><div class=\"default_header\">Artikkelsøk</td></tr></div>";

	if(isset($_REQUEST['searchtype']))
		$searchtype = $_REQUEST['searchtype'];

	if(isset($searchtype)){
		if(!isset($_REQUEST['table'])) $_REQUEST['table'] = "";
		if(!isset($_REQUEST['column'])) $_REQUEST['column'] = "";
		if(!isset($_REQUEST['condition'])) $_REQUEST['condition'] = "";

		$table = strip_tags($_REQUEST['table']);
		$column = strip_tags($_REQUEST['column']);
		$condition = strip_tags($_REQUEST['condition']);

		if(($searchtype == "selectquery") && ($table && $column) && $_SESSION['valid_admin']){

			if($condition)
				$query = "SELECT " . $_GET['column'] . " FROM " . $_GET['table'] . " WHERE " . stripslashes($_GET['condition']) . ";";
			else
				$query = "SELECT " . $_GET['column'] . " FROM " . $_GET['table'] . ";";

			$result = DB_get_table($query);
			$num_results = DB_rows_affected($result);
			$field_count = DB_num_fields($result);


			for($i = 0; $i < $num_results; $i++){
				$row = DB_next_row_numeric($result);
				echo '<tr><td colspan=2>';
				for($j = 0; $j < $field_count; $j++){
					echo strip_tags($row[$j]) . " - ";
				}
				$j = 0;
				echo "</td></tr>";
			}

		}else if($searchtype == "commentquery"){
			global $article_author;
			$comment_query = "SELECT title,articleid,author_username,author,intro,body,date_posted,time_posted FROM articles WHERE author='" . strip_tags($_GET['author']) . "' AND is_deleted IS NULL AND (is_draft=0 OR is_draft IS NULL) AND (comment_to IS NOT NULL) ORDER BY date_posted, time_posted DESC;";


			$result = DB_get_table($comment_query);
			$num_results = DB_rows_affected($result);

			if(!$num_results || $num_results == 0){
				echo "Fant ingen artikler.";
			}else{
				list_articles($result,$num_results);


			}



		}elseif ($searchtype == "bymonth"){
			$month = $_REQUEST['month'];
			$year = $_REQUEST['year'];
			$query = "SELECT * FROM articles WHERE (date_posted <= '" . date("Y-m-d") . "' OR (time_posted <= '" . date("H:i") . "' AND date_posted <= '" . date("Y-m-d") . "'))  AND date_posted LIKE '" . $year . "-" . $month . "-%' AND is_deleted IS NULL AND comment_to IS NULL AND is_draft IS NULL ORDER BY date_posted DESC, time_posted DESC;	";
			debug($query);
			$result = DB_get_table($query);
			$num_rows = DB_rows_affected($result);
			if($result && $num_rows > 0){
				echo $num_rows . " artikler funnet.<br/>";
				list_articles($result, $num_rows);
			}else{
				$month += 0; // VERY corny way of converting $month from string to int to remove leading zero
				echo "Fant ingen artikler fra " . $months[$month]	 . " " .  $year . ".<br/><br/>";
			}

		}else if($searchtype == "username"){
			$query = "SELECT comment_to,title,articleid,author_username,author,intro,body,date_posted,time_posted FROM articles WHERE author_username='" . strip_tags($_REQUEST['username']) . "' AND is_deleted IS NULL AND is_draft IS NULL ORDER BY date_posted DESC, time_posted DESC;";

			$result = DB_get_table($query);
			$num_results = DB_rows_affected($result);

			if(!$num_results || $num_results == 0){
				echo "Fant ingen artikler.";
			}else{
				list_articles($result,$num_results);


			}

		}

	}else{
		//echo "Ugyldig søk.";

	}


	if(isset($_SESSION['valid_admin'])){
		echo '<tr><td>';
		echo "Altmuligsøk, eksklusivt for admins</td><td>";
		form_start_get();
		form_hidden("searchtype", "selectquery");
		form_hidden("m_c", "module_article_search");
		echo "<br/>SELECT "; form_textfield("column", "");
		echo "<br/>FROM "; form_textfield("table", "");
		echo "<br/>WHERE "; form_textfield("condition", "");
		echo "<br/>"; form_submit("submit","Søk");
		form_end();
		echo "</td></tr>";

	}

	echo '<tr><td>';
	$query = "SELECT firstname,username FROM user;";
	$result = DB_get_table($query);
	$num_results = DB_rows_affected($result);
	form_start_get();
	form_hidden("searchtype", "username");
	form_hidden("m_c", "module_article_search");
	echo "Vis alle artikler og kommentarer skrevet av forfatter:</td><td>";
	echo '<select name="username">';
	while($row = DB_next_row($result)){
		echo '<option value="' . $row['username'] . '" >' . $row['firstname'] . " (" .  $row['username'] . ')</option>';
	}
	echo '</select>';
	form_submit("submit", "Søk");
	form_end();
	echo '</td></tr>';

	echo '<tr><td>';
	form_start_get();
	form_hidden("searchtype", "commentquery");
	form_hidden("m_c", "module_article_search");
	echo "Vis alle kommentarer skrevet av forfatter:</td><td>";
	form_textfield("author","");
	form_submit("submit", "Søk");
	form_end();
	echo '</td></tr><tr><td>';

	form_start_get();
	form_hidden("searchtype", "bymonth");
	form_hidden("m_c", "module_article_search");
	echo "Vis alle artikler publisert i:</td><td>";
	echo '<select name="month">';
	for($i = 1; $i<10; $i++){
		echo '<option value="0' . $i . '">' . $months[$i] . '</option>\n';
	}
	for($i = 10; $i<13; $i++){
		echo '<option value="' . $i . '">' . $months[$i] . '</option>\n';
	}
	echo '</select>';

	form_select_number("year", 2004,date("Y"), date("Y"));
	form_submit("submit", "Søk");
	form_end();
	echo '</td><tr>';

	echo '</table>';
	}




function list_articles($result, $num_results){
	global $article_author;
		for($i = 0; $i < $num_results; $i++){
			echo "<tr><td colspan=2>";
			$row = DB_next_row($result);
		     echo "<div class=\"default_header\">" . (stripslashes($row['title'])) . "</div><div class=\"metatext\">";
		     echo "<span class=\"author\">" . $article_author . ": ";
		     echo stripslashes($row['author']) . ' (' . $row['author_username'] . ')';
		     echo ',</span> <span class="time">postet ';
		     echo make_date($row['date_posted']) . " ";
		     echo make_time($row['time_posted']);
		     echo '</span><div> ';
		     echo substr(nl2br(stripslashes($row['body'])),0,240);
		     if ((strlen($row['body'])) > 240)
		     	echo "...";
		     echo '</div><div class="editarticle">';

			 if (isset($_SESSION['valid_user'])|| isset($_SESSION['valid_admin'])){
			     if((($row['author_username'] == $_SESSION['valid_user']))){
					echo '<a href="index.php?articleid=' . $row['articleid'] . '&m_c=module_delete_article">Slett</a>';
					echo ' <a href="index.php?articleid=' . $row['articleid'] . '&m_c=module_enter_article&edit=1">Rediger</a>';
			     }
			 }

		    if ($row['comment_to'] > 0)
		    	echo ' <a href="index.php?articleid=' . $row['comment_to'] . '&m_c=m_va">Vis artikkelen denne kommentaren tilhører</a></div>';
		    else
		    	echo ' <a href="index.php?articleid=' . $row['articleid'] . '&m_c=m_va">Vis</a></div>';

			echo '</td></tr>';

		}



}

function age_hours($sqldate, $sqltime){
	$thentime = strtotime($sqldate . " " . $sqltime);
	$nowtime = strtotime(date("Y-m-d H:i"));
	$spanseconds = $nowtime - $thentime;
	$hours = $spanseconds / 60 / 60;

	return (int)$hours;
}

function age_minutes($sqldate, $sqltime){
	$thentime = strtotime($sqldate . " " . $sqltime);
	$nowtime = strtotime(date("Y-m-d H:i"));
	$spanseconds = $nowtime - $thentime;
	$minutes = $spanseconds / 60;

	return (int)$minutes;

}


function recent_sql_days($days){
	$two_days_ago  = mktime(0, 0, 0, date("m")  , date("d")-$days, date("Y"));
	$in_sql = date("Y-m-d",$two_days_ago);
	return $in_sql;
}

// Show a list of articles with comments posted to them in the last
// X days.
// Depends on: module_recent_comments_find_age() recent_sql()
function module_recent_comments(){
	echo "<!-- start recent comments -->";

	if(isset($_REQUEST['tl'])){
		$HOURS_LIMIT = $_REQUEST['tl'];
		if(!is_numeric(($HOURS_LIMIT))){
			$HOURS_LIMIT = 512;
		}else if($HOURS_LIMIT > 192){
			$HOURS_LIMIT = 192;
		}
	}else{
		$HOURS_LIMIT = 24;
	}


	$DAYS_LIMIT = (int) ($HOURS_LIMIT / 24);
	$DAYS_LIMIT++;
	// LANGUAGE DEPENDENT VARIABLES
	global $lang;


	$recent_comment_by = array("no" => "Kommentar av: ",
		"en" => "Comment by: ");
	$recent_comment_age = array("no" => "Alder: ",
		"en" => "Age: ");

	//$title = $recent_comments_title[$lang];
	$comment_by = $recent_comment_by[$lang];
	$comment_age = $recent_comment_age[$lang];

	echo '<div class="recentcomments">';
	echo '<div class="recentcomments_heading">Kommentarer siste ' . $HOURS_LIMIT . ' timer</div>';

	$recent_date = recent_sql_days($DAYS_LIMIT); // Compose SQL value for getting recent comments
	$query ="select distinct b.articleid, b.title, a.comment_to from articles a, articles b where a.comment_to = b.articleid AND a.comment_to IS NOT NULL AND a.is_deleted IS NULL AND b.is_draft IS NULL AND b.is_deleted IS NULL AND a.date_posted > '". $recent_date . "' order by b.date_posted, b.time_posted;";




	$result = DB_get_table($query);
	$num_results = DB_rows_affected($result);

	if(!$result || DB_rows_affected($result) < 1){
		echo "Ingen nye kommentarer siste " . $HOURS_LIMIT . " timer.";

	}else{


		$row = DB_next_row($result);
		while($row){

			$query2 = "SELECT firstname as author, date_posted, time_posted FROM articles, user WHERE articles.author_username = user.username AND is_deleted IS NULL AND comment_to=" . $row['articleid'] . " ORDER BY date_posted DESC, time_posted DESC;";
			$row2 = DB_search($query2);

			$age_hours = age_hours($row2['date_posted'], $row2['time_posted']);

			if($age_hours < 1){
				$age_minutes = age_minutes($row2['date_posted'], $row2['time_posted']);
			}


			if(strlen($row['title']) > 35){
				$title = substr(stripslashes($row['title']),0, 30) . "...";
			}else{
				$title = stripslashes($row['title']);
			}


			if($age_hours < $HOURS_LIMIT){
				echo "<a class=\"recentcomments_links\" href=\"index.php?m_c=va&amp;articleid=" . $row['articleid'] . "\">" . ($title) . "</a><br/>";
				echo '<div class="recentcomments_list">';
				echo "(<a class=\"recentcomments_links\" href=\"index.php?m_c=va&amp;articleid=" . $row['articleid'] . "#lastcomment\">" .  stripslashes($row2['author']) . ", ";

				if(isset($age_minutes)){
					if($age_minutes == 0){
						echo "helt fersk!</a>)";
					}else{
						echo $age_minutes . " minutter</a>)";
					}
				}else{
					if($age_hours == 1){
						echo "en time</a>)";
					}else{
						echo $age_hours . " timer</a>)";
					}

				}

				echo "</div>";
			}
			unset($age_minutes);
			$row = DB_next_row($result);
		}
	}
	echo '<div class="viewmemberlist">';
	echo '<a href="index.php?tl=24">Siste 24</a> | <a href="index.php?tl=48">48</a> | <a href="index.php?tl=72">72</a> | <a href="index.php?tl=168">Uke</a>';
	echo '</div></div>';
}

// Compute the age in hours and minutes of a given date and time
// in SQL format.
// Depends on: GetTime()



function module_register_form(){
	echo "<!-- start register form -->";
	echo '<table class="default_table">';
	echo "<tr><td colspan=2><div class=\"default_header\">Brukerregistrering</div>Du m&aring være registrert for &aring kunne legge inn kommentarer.</td></tr>";
	form_register();
	echo '</table>';
}


function module_register_user(){
	echo "<!-- start register user -->";
	$all_ok = true;
	global $magic_number;
	global $max_profile_image_size;

	if(isset($_REQUEST['cancelreg']))
		$cancelreg=$_REQUEST['cancelreg'];

	if(isset($_REQUEST['canceledit']))
		$canceledit=$_POST['canceledit'];

	  if(isset($cancelreg)){
	  	form_unset_user();
	  	echo ("Registration cancelled.");
	  }else{
	 	$email=$_POST['email'];
		$username=$_REQUEST['username'];
		$password1=$_POST['password1'];
		$password2=$_POST['password2'];
		$firstname=$_POST['firstname'];
		$lastname=$_POST['lastname'];
		$webpage=$_POST['webpage'];
		$birthdate = $_REQUEST['birthyear'] . "-" . $_REQUEST['birthmonth'] . "-" . $_REQUEST['birthday'];
	  	$description = $_REQUEST['description'];
	 	$username = strip_tags($username); $password1 = strip_tags($password1);
	 	$password2 = strip_tags($password2); $firstname = strip_tags($firstname);
	 	$lastname = strip_tags($lastname); $webpage = strip_tags($webpage);
	 	$birthdate = strip_tags($birthdate);


	  	save_form_user();



		if(!(is_valid_alphanum($firstname) && is_valid_alphanum($lastname) && is_aToZLower($username))){
			$all_ok = false;
			$error_msg .= " Brukernavn kan kun inneholde små bokstaver mellom a og z. Fornavn og etternavn må skrives kun med tall og bokstaver.";

		}

		if(!(is_valid_url($webpage))){
			$all_ok=false;
			$error_msg .= " Webadresse kan kun skrives med tall, bokstaver, /, ~, & og =.";

		}

		if(!($password1 && $username && $email && $password2)){
			$all_ok=false;
			$error_msg .= " Du må oppgi minst brukernavn, e-post og passord.";
		}

	    // email address not valid
	    if (!valid_email($email))
	    {
	    	$all_ok=false;
	    	$error_msg .= " E-postadressen er ikke gyldig.";
	    }

	    // passwords not the same
	    if ($password1 != $password2)
	    {
	    	$all_ok=false;
	    	$error_msg .= " Passordene stemmer ikke.";
	    }


	    // check password length is ok
	    // ok if username truncates, but passwords will get
	    // munged if they are too long.
	    if (strlen($password1)<6 || strlen($password1) >16)
	    {
	      $all_ok=false;
	      $error_msg .= " Passordet må være mellom 6 og 16 tegn.";
	    }

		if(strlen($_FILES['picturepath']['tmp_name']) == 0){
			$picture_result = 0; // Not uploading a picture

	  	}else if($max_profile_image_size < $_FILES['picturepath']['size']){
    		$picture_result = -1;
    		$error_msg .= " Maksimal bildestørrelse er ." .  ($max_profile_image_size / 1000) . " kilobytes. Ditt bilde er " . ($_FILES['picturepath']['size'] / 1000) . " kilobytes.";
    	}else{

    		$picture_result = file_upload($username);

			if(($all_ok == true) && ($picture_result < 0)){
				if($picture_result == -1){
					$error_msg .= " Bildet er for stort.";
				}else if($picture_result == -4){
					$error_msg .= " Lagring av bilde i databasen gikk galt.";
				}else if($picture_result == -3){
					$error_msg .= " Fant ikke bildefilen.";
				}else{
					$error_msg .= " Uspesifisert feil i bildeopplasting.";
				}
			}else{


			}

    	}



		if($all_ok){
		    $all_ok = register($username, $email, $password1, $firstname, $lastname, $webpage, $birthdate, $description);
			if($all_ok){
				$log_description .= "newuser,";

				// forsøker å legge inn bildet også
				if($picture_result > 0){
			        $sql = "UPDATE user SET picture=" . $picture_result . " WHERE username='" . $username . "'";
			        $result = DB_insert($sql);
			        if (!$result) {
			        	echo 'Bildet ble lastet opp, men greide ikke å knytte det til bruker: ' . $username . '<br />';
						echo 'Du er registrert, men bildet er ikke lagt opp. Prøv gjerne å legg det til gjennom Min Profil. Feilmelding: ' . mysql_error() . '<br />';
						$log_description .= "picfailed,";
			        } else {
			        	echo 'Registreringen var vellykket. Logg inn og skriv noe lurt!<br />';
			        	$log_description .= "withpic,";
			        }

				} else {
					if($picture_result != 0){
						echo 'Registreringen var vellykket, men profilbildet ditt ble ikke lagt opp. Årsak: ' . $error_msg  . "<br/> Prøv gjerne igjen senere via profilsiden.";
						$log_description .= "picfailed,";
					}else{
						$log_description .= "";
		        		echo 'Registreringen var vellykket. Logg inn og skriv noe lurt!<br />';
					}
				}
				global $logtype;
				write_log_entry($username, $logtype['user'], $log_description);
			    form_unset_user();
		    }else{
		    	//echo "Registrering mislyktes. Ta kontakt med en administrator, f.eks. redpilot@online.no.";
			}
		}
	    else{
	    	echo $error_msg;
	    }
	}
}




function module_edit_profile(){
	echo "<!-- start edit profile -->";
	global $href_edit_profile;
	global $max_profile_image_size;

	if (isset($_REQUEST['savechanges'])){
		$savechanges = $_REQUEST['savechanges'];
	}

	$edituser = $_SESSION['valid_user'];

	if (isset($_REQUEST['canceledit'])){
		$canceledit = $_REQUEST['canceledit'];
	}

	if(!isset($edituser) || isset($canceledit)){
		if(isset($canceledit)){
			form_unset_user();
			echo "Redigering avbrutt.";
		}else{
			echo "Du må ha logget deg inn for å ha tilgang til denne siden.";
		}

	}else
		{

		styleConfig();



		if(isset($savechanges)){
			$all_ok = true;
			$email = $_POST['email'];
			$password1 = $_POST['password1'];
			$password2 = $_POST['password2'];
			$description = $_REQUEST['description'];
			$webpage = $_REQUEST['webpage'];
			$picture_url = $_REQUEST['picture_url'];
			$password1 = strip_tags($password1);
		 	$password2 = strip_tags($password2); $firstname = strip_tags($firstname);
		 	$lastname = strip_tags($lastname); $webpage = strip_tags($webpage);
		 	$description = strip_tags($description); $birthdate = strip_tags($birthdate);
			$picture = strip_tags($_REQUEST['picture']);
			$admin = $_REQUEST['admin'];
			$may_post = $_REQUEST['may_post'];

			// Assemble SQL birthdate
			$birthdate = $_REQUEST['birthyear'] . "-" . $_REQUEST['birthmonth'] . "-" . $_REQUEST['birthday']	;
			$birthdate = strip_tags($birthdate);
			$_SESSION['existing_edit'] = "true";
			save_form_user();

	    	if(strlen($password1) > 0){
	    		// Do password relevant checks
		        if(!($password1 == $password2)){
		        	$all_ok=false; $error_msg .= " Passordene stemmer ikke.";
		        }

			    if (strlen($password1)<6 || strlen($password1) >16){
			    	$all_ok=false; $error_msg .= " Passoerdet må være mellom 6 og 16 tegn.";
			    }
	    	}


	    	if(!isset($email) || !valid_email($email)){
	    		$all_ok=false; $error_msg .= " Ugyldig e-postadresse.";
	    	}

	    	// Check what to do with the admin flag
	    	if($admin == 1){
	    		$admin = 1;
	    	}else{
	    		$admin = 0;
	    	}
		        // Saveuser() checks whether a password is given or not :p
			if(strlen($_FILES['picturepath']['tmp_name']) == 0){
				$info .= " Ingen endringer gjort med profilbilde."; // Fair enough, no image set OR IT DOESNT EXIST. OOPS.
			}else if($max_profile_image_size < $_FILES['picturepath']['size']){
	    		$all_ok=false; $error_msg .= " Maksimal bildestørrelse er ." .  ($max_profile_image_size / 1000) . " kilobytes. Ditt bilde er " . ($_FILES['picturepath']['size'] / 1000) . " kilobytes.";
	    	}else{

	    		$picture_result = file_upload($_SESSION['valid_user']);
				if(($all_ok == true) && ($picture_result < 0)){
					if($picture_result == -1){
						$all_ok=false; $error_msg .= " Bildet er for stort.";
					}else if($picture_result == -4){
						$all_ok=false; $error_msg .= " Lagring av bilde i databasen gikk galt.";
					}else if($picture_result == -3){
						$all_ok=false; $error_msg .= " Fant ikke bildefilen.";
					}else{
						$all_ok=false; $error_msg .= " Uspesifisert feil i bildeopplasting.";
					}
				}else{
		        	$username = $_SESSION['valid_user'];

			        $sql = "UPDATE user SET picture=" . $picture_result . " WHERE username='" . $username . "'";

			        $result = DB_insert($sql);
			        if (!$result || DB_rows_affected($result) == 0) {
			        	$log_description .= "imgnotattached,";
						$all_ok=false; $error_msg .= " Bilde lastet opp, men ikke knyttet til bruker. ";
			        } else {
			        	$log_description .= "goodimgupload,";
			        }
				}

	    	}





		    if($all_ok){
	       	 $result = saveuser(addslashes($_REQUEST['username']), addslashes($_REQUEST['password1']), addslashes($_REQUEST['email']), ($_REQUEST['firstname']),addslashes($_REQUEST['lastname']), addslashes($_REQUEST['webpage']), addslashes($birthdate), addslashes($_POST['description']), $admin, $may_post);
				if($result){
					$log_description .= "goodedit,";
					form_unset_user();
					echo "<h3>Profil oppdatert</h3>";
				    echo "Endringene er lagret." . $info;
				}

		    }else{
		    	$log_description .= "badedit,";
		    	echo $error_msg;
		    }
				global $logtype;
				write_log_entry($_POST['username'], $logtype['user'], $log_description);

		}else{
			if (isset($_REQUEST['existing_edit'])){
				$existing_edit = $_SESSION['existing_edit'];
			}


		   	if(isset($existing_edit)){
				form_edit_profile($_SESSION);
		   	}else{
		   		$edituser = $_SESSION['valid_user'];
		   		$query = "SELECT * FROM user WHERE username=\"" . $edituser . "\";";
				$row = searchDB($query);

				form_edit_profile($row);

		   	}

		   	module_my_drafts();

		}

		}


}

function module_my_drafts(){

	$query = "SELECT * FROM articles WHERE is_deleted IS NULL AND author_username='" . $_SESSION['valid_user'] . "' AND (is_draft=1 OR (date_posted > '" . date("Y-m-d") . "' OR (time_posted > '" . date("H:i") . "' AND date_posted > '" . date("Y-m-d") . "'))) ORDER BY date_posted DESC, time_posted DESC;";
	debug($query);
	$result = DB_get_table($query);
	$num_results = DB_rows_affected($result);

	if($num_results > 0){
		echo "<br/><br/><h3>Dine artikkelutkast og fremdaterte artikler</h3>";
		echo "<table>";
		for($i = 0; $i < $num_results; $i++){
			$row = DB_next_row($result);
			echo '<tr><td><span class="header3">' . $row['title'] . '</span><br/> (' . $row['date_posted'] . "/" . $row['time_posted'] . ')</td></tr><tr><td>';
			form_start_post();
			form_hidden("articleid", $row['articleid']);
			form_hidden("m_c", "editArticle");
			form_submit("edit","Rediger / publiser denne artikkelen");
			form_end();
			form_start_post();
			form_hidden("articleid", $row['articleid']);
			form_hidden("m_c", "deleteArticle");
			form_submit("edit","Slett denne artikkelen");
			form_end();
			echo "</td></tr>";
		}
		echo "</table>";
	}
}


function module_user_admin(){
	// adminpage, stop here if not logged in/right access-level
	if (!isValidAdmin()) {
		echo (getString("not_valid_admin", "Administratorside, du mÃ¥ logge inn for Ã¥ fÃ¥ tilgang her"));
		return;
	}

	echo "<!-- start user admin -->";
	$all_ok = true;
	global $menu_files;


		if(isset($_REQUEST['edituser'])){
		    if(isset($_REQUEST['savechanges'])){


	            if(!($password1 == $password2)){
	                $all_ok=false; $error_msg .= " Passwords don't match!";
	            }

				if($all_ok){
		        	$result = saveuser($_POST['username'], $_POST['password1'], $_POST['email'], $_POST['firstname'],$_POST['lastname'], $_POST['webpage'], $_POST['birthdate'], $_POST['description'], $_POST['admin'], $_POST['may_post']);
				}else{
					echo $error_msg;
				}

				if($result){
				    echo "Changes saved. Jolly good.";
				   	global $logtype;
					write_log_entry($_POST['username'], $logtype['user'], "admin_useredit,");
				}else{
					echo "No changes were made.";
				}


		    }else{
				$query = "SELECT * FROM user WHERE username=\"" . $_POST['edituser'] . "\";";
				$row = DB_search($query);



				form_start_post();
				echo '<table class="default_table">';
				echo '<tr><td>Brukernavn</td><td>'; echo $row['username']; echo '</td></tr>';
				echo '<tr><td>E-post</td><td>'; form_textfield("email", stripslashes($row['email'])); echo ' (må ligne på en ordentlig adresse)</td></tr>';
				echo '<tr><td>Fornavn</td><td>'; form_textfield("firstname", ($row['firstname'])); echo ' (det dine venner kaller deg)</td></tr>';
				echo '<tr><td>Etternavn</td><td>'; form_textfield("lastname", stripslashes($row['lastname'])); echo ' (det du het i militæret)</td></tr>';
				echo '<tr><td>Passord</td><td>'; form_password("password1", ""); echo ' (minst 6 tegn)</td></tr>';
				echo '<tr><td>Gjenta passord</td><td>'; form_password("password2", ""); echo ' (helst likt det i feltet over)</td></tr>';
					echo '<tr><td>Fødselsdato</td><td>'; form_select_number("birthday",0,0, $birthday);
					form_select_number("birthmonth",0,0, $birthmonth);
					form_select_number("birthyear", 0,0,$birthyear);
					echo '</td></tr>';
				echo '<tr><td>Webside</td><td>'; form_textfield("webpage", stripslashes($row['webpage'])); echo ' (gjerne en som fins)</td></tr>';
				echo '<tr><td>Er administrator</td><td>'; form_textfield("admin", stripslashes($row['admin'])); echo ' (er brukeren admin?)</td></tr>';
				echo '<tr><td>Kan skrive artikler</td><td>'; form_textfield("may_post", stripslashes($row['may_post'])); echo ' 0=nei, 1=ja</td></tr>';
				echo '<tr><td>Eventuelt tilknyttet bildes fil-id:</td><td>'; echo $row['picture']; echo ' (fjernes via filadmin: ' . $menu_files . ')</td></tr>';
				echo '<tr><td colspan=2>Ymse visvas<br/>'; form_textarea("description",stripslashes($row['description']),30,10); echo '<br/>(hvis det er noe mer vi bør vite om deg)<br/><br/></td></tr>';

				echo '<tr><td colspan=2>'; form_submit("Button", "Lagre profilendringer"); echo '</td></tr>';
				echo '<tr><td colspan=2>'; form_submit("canceledit", "Avbryt profilendring"); echo '</td></tr>';
				form_hidden("username", $row['username']);
				form_hidden("savechanges", "savechanges");
				form_hidden("edituser", "savechanges");
				form_hidden("m_c", "module_user_admin");
				echo '</table>';
				form_end();




		    }



		}else if ($_POST['deleteuser']){
			if($_POST['reallysure']){
			    $query = "DELETE FROM user WHERE username =\"" . $_POST['deleteuser'] . "\";";


				$result = DB_update($query);

				if($result == 1){
					global $logtype;
					write_log_entry($_POST['username'], $logtype['user'], "admin_deleteduser,");
				    echo "Bruker " .$_POST['deleteuser'] . " er slettet.";
				}else if ($result == 0){
					echo "Kunne ikke slette brukeren - fins fyren?";
				}else if ($result > 1){
				    echo "Du har prestert &aring slette flere eksemplarer av denne brukeren :p";
				}



			}else{
				echo "<div>Sikker på at du vil slette " . $_POST['deleteuser'] . "? <a href=\"index.php\">No, go back!</a></div>";
				form_start_post();
				form_hidden("m_c", "module_user_admin");
				form_hidden("reallysure", "yes");
				form_hidden("deleteuser", $_POST['deleteuser']);
				form_submit("submit", "Ja, slett!");
				form_end();


			}

		}

		else{
			$query = "SELECT * FROM user";
			$result = DB_get_table($query);
			$num_users = DB_rows_affected($result);
			echo '<table class="default_table">';

			for($i = 0; $i < $num_users; $i++){
			     $row = DB_next_row($result);
			     echo '<tr><td><b>Bruker</b></td><td><b>';
			     echo $row['username'];
			     echo '</b></td></tr><tr><td>Fornavn</td><td>';
			     echo $row['firstname']	;
			     //echo '</td></tr><tr><td>Etternavn</td><td>';
			     //echo $row['lastname'];
			     echo '</td></tr><tr><td>E-post</td><td>';
			     echo $row['email'];

			     echo '</td></tr><tr><td>Fødselsdato</td><td>';
			     echo date_nor_sql($row['birthdate']);
			     echo '</td></tr><tr><td>Admin?</td><td>';
			     if ($row['admin'] <> "" && $row['admin'] <> 0) {
			     	echo 'Ja (' . $row['admin'] . ')';
			     } else {
			     	echo 'Nei (' . $row['admin'] . ')';
			     }

			     echo '</td></tr><tr><td>Kan poste?</td><td>';
			     if ($row['may_post'] <> "" && $row['may_post'] <> 0) {
			     	echo 'Ja (' . $row['may_post'] . ')';
			     } else {
			     	echo 'Nei (' . $row['may_post'] . ')';
			     }

				echo '</td></tr><tr><td>Tilknyttet bilde, fil-id</td><td>';
			     echo $row['picture'];
				echo '</td></tr><tr><td colspan=2>'
				 ?>
			  	<form action="index.php" method="post">
			  	<input type="hidden" value="module_user_admin" name="m_c" />
				 <input type="submit" name="edit" value="Edit user" />
			     <input type="hidden" name="edituser" value=<? echo $row['username']; ?> />
				 </form>
			  	<form action="index.php" method="post">
			  	<input type="hidden" value="module_user_admin" name="m_c" />
				 <input type="submit" name="delete" value="Delete user" />
			     <input type="hidden" name="deleteuser" value=<? echo $row['username']; ?> />
				 </form>

			 	<?
			        echo '</td></tr><tr><td colspan=2><hr/></td></tr>';
			}
			echo '</table>';
		}

}








?>
