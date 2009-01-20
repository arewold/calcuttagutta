<?
// Endret char-encoding til UTF8

function form_start_post($formname = "calcutta"){
	echo '<form action="index.php" method="post" name="' . $formname . '">';
}

function form_start_custom($formname, $action, $method){
	echo '<form action="' . $action . '" method="' . $method . '" name="' . $formname . '">';
}

function form_start_post_file(){
	echo '<form action="index.php" method="post" name="calcutta" enctype="multipart/form-data">';
}

function form_start_get(){
	echo '<form action="index.php" method="get" name="calcutta">';
}


function form_end(){
	echo '</form>';		
}

// Receives date in SQL format, fills form boxes
function form_datewidget($date){
	$datearray = explode("-", $date);
	$year = $datearray[0];
	$month = $datearray[1];
	$day = $datearray[2];
	
	form_select_number("day", 1, 31, $day);
	form_select_number("month", 1, 12, $month);
	form_select_number("year", 2004, date("Y"), $year);
}

// Accepts time as 00:10 (ten past midnight)
function form_timewidget($time){
	$timearray = explode(":", $time);
	$hours = $timearray[0];
	$minutes = $timearray[1];
	form_select_number("hours", 0, 23, $hours);
	form_select_number("minutes", 0, 59, $minutes);

	
}

function form_dropdown($name, $valuearray, $labelarray, $selected){

	echo '<select name=' . $name . '>';
	
	foreach ($valuearray as $key => $value) { 
		if($key == $selected)
			echo '<option selected="selected" value=' . $value . '>' . current($labelarray) . '</option>';
		else
			echo '<option value=' . $value . '>' . current($labelarray) . '</option>';
		
		next($labelarray);
	}
	echo '</select>';	
	
	
	
}

function form_select($name) {
	echo '<select name="' . $name . '">';
}

function form_select_end() {
	echo '</select>';
}

function form_option($text, $value, $selected = "") {
	if ($selected) {
		if ($selected == "1" || $selected == "true" || $selected = "selected") {
			echo '<option selected="selected" value="' . $value . '">' . $text . '</option>';
		} else {
			echo '<option value="' . $value . '">' . $text . '</option>';
		}
	} else {
		echo '<option value="' . $value . '">' . $text . '</option>';
	}
}

function form_select_number($name, $startnumber, $endnumber, $selected){
	if($name == "birthday"){
		$startnumber = 1;
		$endnumber = 31;	
	}else if($name == "birthmonth"){
		$startnumber = 1;
		$endnumber = 12;	
	}else if($name == "birthyear"){
		$startnumber = 1851;
		$endnumber = date("Y");	
	}
	
	echo '<select name=' . $name . '>';
	
	for($i = $startnumber; $i <= $endnumber; $i++){
		if($i == $selected)
			echo '<option selected="selected" value=' . $i . '>' . $i. '</option>';
		else
			echo '<option value=' . $i . '>' . $i. '</option>';
		
	}
	echo '</select>';
	
}


// Will return false if contains anything but nice letters
function is_plain_text($string){
	$invalid = ereg("[^[:alpha:]]", $string)	;
	
	if($invalid){
		//echo $string . " Matched";	
	}else{
		//echo $string . " Didn't match'";
	}
	return !$invalid;
}



function is_valid_url($string){
	$invalid = ereg("[^[:alnum:]?!:;~/ æøåÆØÅ.,]", $string);
	
	if($invalid){
		//echo $string . " Matched<br/>";	
	}else{
		//echo $string . " Didn't match<br/>";
	}
	return !$invalid;
}	
	
function form_label($value) {
	echo '<label style="padding-right:3px;padding-left:10px;">' . $value. '</label>';
}

function form_checkbox($name, $enabled=true, $checked=false){
	if($checked == true) {
		if ($enabled == true) {
			echo '<input type="checkbox" name="' . $name . '" checked="checked" />';
		} else {
			echo '<input type="checkbox" name="' . $name . '" checked="checked" disabled="disabled" />';
		}
	} else {
		if ($enabled == true) {
			echo '<input type="checkbox" name="' . $name . '" />';
		} else {
			echo '<input type="checkbox" name="' . $name . '" disabled="disabled" />';
		}
	}
}

function form_textfield($name, $value, $enabled=true){
	if ($enabled == true) {
		echo '<input type="text" name="' . $name . '" value="' . $value . '" />';
	} else {
		echo '<input type="text" name="' . $name . '" value="' . $value . '" disabled="disabled" />';
	}
}

function form_textfield2($description, $name, $value, $enabled=true){
	if ($enabled == true) {
		echo '<label for="' . $name . '">' . $description . '</label><input type="text" id="' . $name . '" value="' . $value . '" />';
	} else {
		echo '<label for="' . $name . '">' . $description . '</label><input type="text" id="' . $name . '" value="' . $value . '" disabled="disabled" />';
	}
}

function form_file($name){
	echo '<input type="file" name="' . $name . '" />';
}

function form_sized_textfield($name, $value, $size){
	//onblur="this.value=removeSpaces(this.value);"
	//echo '<input onclick="this.value=removeSpaces(this.value)" type="text" size="' . $size . '" name="' . $name . '" value="' . $value . '" />';
	echo '<input onFocus="this.value=wipeOut(this.value);"  type="text" size="' . $size . '" name="' . $name . '" value="' . $value . '" />';
}

function form_password($name,$value){
	echo '<input type="password" name="' . $name . '" value="' . $value . '" />';
}

function form_hidden($name,$value){
	echo '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
}

function form_submit($name,$value){
	echo '<input type="submit" class="nice1" name="' . $name . '" value="' . $value . '" />';
}

function form_textarea($name, $content, $cols, $rows){
	echo '<textarea name="' . $name . '" cols=' . $cols . ' rows=' . $rows . '>' . $content . '</textarea>';	
}

function form_jsbutton($name, $value, $onclick){

	echo '<input id="PreviewButton" onclick="' . $onclick . '" type="button" class="nice1" name="' . $name . '" value="' . $value . '" />';
}


function form_article(){
	$edit = $_REQUEST['edit']; // Set if edit requested on an article
	 
	$re_edit = $_SESSION['editarticle']; // Set if user got an error on first attempt to edit
	
	if(isset($re_edit)){
		
		
		echo '<table class="default_table">';
		form_start_post();
		echo '<tr><td>Forfatter</td><td>'; form_textfield("author",stripslashes($_SESSION['author'])); echo '</td></tr>';
		echo '<tr><td>Tittel</td><td class="form_article_title">'; form_textfield("title", stripslashes(fix_quotes($_SESSION['title']))); echo '</td></tr>';
		echo '<tr><td>Dato</td><td>'; form_datewidget($_SESSION['date_posted']); echo '</td></tr>';
		echo '<tr><td>Tidspunkt</td><td>'; form_timewidget($_SESSION['time_posted']); echo '</td></tr>';

		if($_SESSION['is_draft'] == "ON"){
			echo '<tr><td>Bare lagre, <br/>ikke publiser</td><td>'; form_checkbox("is_draft", "ON", "1"); echo '</td></tr>';
		}else{
			echo '<tr><td>Bare lagre, <br/>ikke publiser</td><td>'; form_checkbox("is_draft", "ON", "0"); echo '</td></tr>';}



		echo '<tr><td colspan=2 class="form_article_text">'; form_textarea("body",stripslashes($_SESSION['body']),30,10); echo '</td></tr>';
		echo '<tr><td colspan=2>'; form_submit("Button", "Lagre artikkelen"); echo '</td></tr>';
		echo '<tr><td colspan=2>'; form_submit("preview", "Forhåndsvis artikkel"); echo '</td></tr>';

		form_hidden("m_c", "module_add_article");
		form_hidden("articleid", $_SESSION['articleid']);
		
		if(isset($edit)){
			form_hidden("editarticle", "editarticle");	
		}
		echo '</table>';
		form_end();

		
	}else if(isset($edit)){
		$articleid = $_REQUEST['articleid'];
		$query = "SELECT * FROM articles WHERE articleid = " . $_REQUEST['articleid'] . ";";
		$row = DB_search($query);

		form_start_post();
		echo '<tr><td>Forfatter</td><td>'; form_textfield("author", $row['author']); echo '</td></tr>';
		echo '<tr><td>Tittel</td><td class="form_article_title">'; form_textfield("title", stripslashes(fix_quotes($row['title']))); echo '</td></tr>';
		echo '<tr><td>Dato</td><td>'; form_datewidget($row['date_posted']); echo '</td></tr>';
		echo '<tr><td>Tidspunkt</td><td>'; form_timewidget($row['time_posted']); echo '</td></tr>';

		if(isset($row['is_draft']) && $row['is_draft'] == 1){
			echo '<tr><td>Bare lagre, <br/>ikke publiser</td><td>'; form_checkbox("is_draft", "ON", "1"); echo '</td></tr>';
		}else
			{echo '<tr><td>Bare lagre, <br/>ikke publiser</td><td>'; form_checkbox("is_draft", "ON", "0"); echo '</td></tr>';}

		echo '<tr><td colspan=2 class="form_article_text">'; form_textarea("body",stripslashes($row['body']),30,10); echo '</td></tr>';
		echo '<tr><td colspan=2>'; form_submit("Button", "Lagre endringene"); echo '</td></tr>';
		echo '<tr><td colspan=2>'; form_submit("preview", "Forhåndsvis artikkel"); echo '</td></tr>';
		form_hidden("editarticle","true");
		form_hidden("m_c", "module_add_article");
		form_hidden("articleid", $row['articleid']);
		echo '</table>';
		form_end();
		
		
	}else{
		form_start_post();
		echo '<tr><td>Forfatter</td><td>'; form_textfield("author",$_SESSION['user_firstname']); echo '</td></tr>';
		echo '<tr><td>Tittel</td><td class="form_article_title">'; form_textfield("title", ""); echo '</td></tr>';
		echo '<tr><td>Dato</td><td>'; form_datewidget(date("Y-m-d")); echo '</td></tr>';
		echo '<tr><td>Tidspunkt</td><td>'; form_timewidget(date("H:i")); echo '</td></tr>';
		echo '<tr><td>Bare lagre, <br/>ikke publiser</td><td>'; form_checkbox("is_draft", "ON", "0"); echo '</td></tr>';
		echo '<tr><td colspan=2 class="form_article_text">'; form_textarea("body","",30,10); echo '</td></tr>';
		echo '<tr><td colspan=2>'; form_submit("Button", "Lagre artikkelen"); echo '</td></tr>';
		echo '<tr><td colspan=2>'; form_submit("preview", "Forhåndsvis artikkel"); echo '</td></tr>';
		form_hidden("m_c", "module_add_article");
		echo '</table>';
		form_end();
		
	}
 

 
 
}

function save_form_article(){
	$_SESSION['author'] = strip_tags($_POST['author']);
	$_SESSION['title'] = strip_tags($_POST['title']);
	$_SESSION['intro'] = strip_tags($_POST['intro']);
	$_SESSION['body'] = ($_POST['body']);
	$_SESSION['date_posted'] = $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day'];
	$_SESSION['time_posted'] = $_POST['hours'] . ":" . $_POST['minutes'];
	$_SESSION['articleid'] = strip_tags($_POST['articleid']);
	$_SESSION['prority'] = strip_tags($_POST['priority']);
	$_SESSION['comment_to'] = strip_tags($_POST['comment_to']);
	$_SESSION['is_draft'] = strip_tags($_POST['is_draft']);
}

function save_form_user(){
	$_SESSION['stored_reg_info'] = "true";
	$_SESSION['username'] = strip_tags($_POST['username']);
	$_SESSION['firstname'] = strip_tags($_POST['firstname']);
	$_SESSION['lastname'] = strip_tags($_POST['lastname']);	
	$_SESSION['email'] = strip_tags($_POST['email']);	
	$_SESSION['webpage'] = strip_tags($_POST['webpage']);	
	$_SESSION['birthdate'] = strip_tags($_POST['birthdate']);	
	$_SESSION['description'] = strip_tags($_POST['description']);	
	$_SESSION['birthyear'] = strip_tags($_POST['birthyear']);	
	$_SESSION['birthday'] = strip_tags($_POST['birthday']);	
	$_SESSION['birthmonth'] = strip_tags($_POST['birthmonth']);	
	$_SESSION['picture'] = ($_REQUEST['picture']);
	$_SESSION['picturepath'] = ($_FILES['picturepath']['name']);
	$_SESSION['admin'] = $_REQUEST['admin'];
	$_SESSION['may_post'] = $_REQUEST['may_post'];

}

function form_unset_user(){
	unset($_SESSION['existing_edit']);
	unset($_SESSION['stored_reg_info']);
	unset($_SESSION['username']);
	unset($_SESSION['firstname']);
	unset($_SESSION['lastname']);
	unset($_SESSION['email']);
	unset($_SESSION['webpage']);
	unset($_SESSION['birthdate']);
	unset($_SESSION['description']);
	unset($_SESSION['birthday']);
	unset($_SESSION['birthmonth']);
	unset($_SESSION['birthyear']);
	unset($_SESSION['magic']);
	unset($_SESSION['picture']);
	unset($_SESSION['picturepath']);
	unset($_SESSION['admin']);
	unset($_SESSION['maypost']);
	
}

function unset_form_article(){
	unset($_SESSION['author']);
	unset($_SESSION['title']);
	unset($_SESSION['intro']);
	unset($_SESSION['body']);
	unset($_SESSION['date_posted']);
	unset($_SESSION['time_posted']);
	unset($_SESSION['articleid']);
	unset($_SESSION['prority']);
	unset($_SESSION['comment_to']);
	unset($_SESSION['editarticle']);
	unset($_SESSION['edit']);
	unset($_SESSION['is_draft']);
	
}

function do_cancel_article_form(){
	form_start_post();
	form_submit("cancelarticle", "Avbryt, og slett midlertidig lagrede data");
	form_hidden("m_c", "module_cancel_article");
	form_end();		
}

function do_comment_form(){
	global $flashformid;
	if(isset($_SESSION['title'])) $session_title = $_SESSION['title'];
	else $session_title = "";
	
	if(isset($_SESSION['body'])) $session_title = $_SESSION['body'];
	else $session_body = "";
	
	
	form_start_post();
	echo '<table class="default_table">';
	echo '<tr><td>Forfatter</td><td class="form_comment_author">'; form_textfield("author",$_SESSION['user_firstname']); echo '</td></tr>';
	echo '<tr><td>Tittel</td><td class="form_comment_title">'; form_textfield("title", $session_title); echo '</td></tr>';
	form_hidden("day", date("d")); 
	form_hidden("month", date("m")); 
	form_hidden("year", date("Y")); 
	form_hidden("hours", date("H")); 
	form_hidden("minutes", date("i")); 

	echo '<tr><td colspan=2 class="form_comment_text">'; form_textarea("body",$session_body,30,10); echo '</td></tr>';
	echo '<tr><td colspan=2 class="form_comment_button">'; form_submit("Button", "Lagre kommentaren"); echo '</td></tr>';
	form_hidden("comment_to", $_GET['articleid']);
	form_hidden("m_c", "module_add_article");
	form_hidden("article_form_id", $flashformid);
	echo '</table>';
	form_end();
}

function form_edit_profile($row){

		if(!isset($_SESSION['valid_user']))
			echo ("You must be logged in to edit your profile.");
		else{
			global $max_image_size;
			
			$birthyear = substr($row['birthdate'], 0, 4);
			$birthday = substr($row['birthdate'], 8, 2);
			$birthmonth = substr($row['birthdate'], 5, 2);
			form_start_post_file();
			echo '<table class="default_table">';
			echo '<tr><td colspan=2><div class="default_header">Rediger din profil</div></td></tr>';
			echo '<tr><td>Brukernavn</td><td>'; echo $row['username']; echo '</td></tr>';
			echo '<tr><td>E-post</td><td>'; form_textfield("email", stripslashes($row['email'])); echo ' (må ligne på en ordentlig adresse)</td></tr>';
			echo '<tr><td>Fornavn</td><td>'; form_textfield("firstname", ($row['firstname'])); echo ' (det dine venner kaller deg)</td></tr>';
			echo '<tr><td>Etternavn</td><td>'; form_textfield("lastname", stripslashes($row['lastname'])); echo ' (det du het i militæret)</td></tr>';
			echo '<tr><td>Passord</td><td>'; form_password("password1", ""); echo ' (minst 6 tegn)</td></tr>';
			echo '<tr><td>Gjenta passord</td><td>'; form_password("password2", ""); echo ' (helst likt det i feltet over)</td></tr>';
			echo '<tr><td>Fødselsdato</td><td>'; form_select_number("birthday",0,0, $birthday); 
			form_select_number("birthmonth",0,0, $birthmonth);
			form_select_number("birthyear", 0,0,$birthyear);
			echo '<tr><td>Webside</td><td>'; form_textfield("webpage", stripslashes($row['webpage'])); echo ' (gjerne en som fins)</td></tr>';
		
			echo '</td></tr>';
			
			if(isset($row['picture']) && $row['picture'] > 0){
				echo '<tr><td><div class="user_picture"><img src="mod_pic.php?id=' . $row['picture'] . '" alt="' . $_SESSION['valid_user'] . '" /></div>';
				echo '</td><td>'; 				
			}else{
				echo '<tr><td colspan="2">'; 	
			}
			form_hidden("admin", $row['admin']);
			form_hidden("may_post", $row['may_post']);
			form_hidden("picture", $row['picture']);
			form_hidden("MAX_FILE_SIZE", $max_image_size);
			echo "Legg inn / endre bilde<br/>";
			form_file("picturepath");
			global $max_profile_image_size;
			echo ' Maksimalt ' . $max_profile_image_size / 1000 . 'KB filstørrelse. Bildet blir skalert ned til 400 pikslers bredde og 600 pikslers høyde dersom det er større. Å sende inn skjemaet kan gå litt sakte om bildet ditt er stort.</td></tr>';
	
			echo '<tr><td colspan=2>Ymse visvas<br/>'; form_textarea("description",stripslashes($row['description']),30,10); echo '<br/>(hvis det er noe mer vi bør vite om deg)<br/><br/></td></tr>';
			
			echo '<tr><td colspan=2>'; form_submit("Button", "Lagre profilendringer"); echo '</td></tr>';
			echo '<tr><td colspan=2>'; form_submit("canceledit", "Avbryt profilendring"); echo '</td></tr>';
			form_hidden("username", $row['username']);
			form_hidden("savechanges", "savechanges");
			form_hidden("edituser", "savechanges");
			form_hidden("m_c", "module_edit_profile");		
			echo '</table>';
			form_end();			
		}
}




function form_register(){
	global $max_profile_image_size;
	
	if(isset($_SESSION['stored_reg_info']))
		$stored_reg_info = $_SESSION['stored_reg_info'];
	if(isset($_SESSION['editing_profile']))
		$editing_profile = $_SESSION['editing_profile'];
	
	$username = "";
	$email = "";
	$firstname = "";
	$lastname = "";
	$magic = "";
	$birthday = "";
	$birthmonth = "";
	$birthyear = "";
	$webpage = "";
	$description = "";
	
	if(isset($stored_reg_info)){
		$username = $_SESSION['username'];
		$email = $_SESSION['email'];
		$firstname = $_SESSION['firstname'];
		$lastname = $_SESSION['lastname'];
		$magic = $_SESSION['magic'];
		$birthday = $_SESSION['birthday'];
		$birthmonth = $_SESSION['birthmonth'];
		$birthyear = $_SESSION['birthyear'];
		$webpage = $_SESSION['webpage'];
		$description = $_SESSION['description'];
	}

	form_start_post_file();
	echo '<tr><td>Brukernavn</td><td>'; form_textfield("username",$username); echo ' (maks 16 tegn, kun tall og små bokstaver)</td></tr>';
	echo '<tr><td>E-post</td><td>'; form_textfield("email", $email); echo ' (må ligne på en ordentlig adresse)</td></tr>';
	echo '<tr><td>Fornavn</td><td>'; form_textfield("firstname", $firstname); echo ' (det venner kaller deg)</td></tr>';
	echo '<tr><td>Etternavn</td><td>'; form_textfield("lastname", $lastname); echo ' (det du het i militæret)</td></tr>';
	echo '<tr><td>Passord</td><td>'; form_password("password1", ""); echo ' (minst 6 tegn)</td></tr>';
	echo '<tr><td>Gjenta passord</td><td>'; form_password("password2", ""); echo ' (helst likt det i feltet over)</td></tr>';
	//echo '<tr><td>Magisk nummer</td><td>'; form_textfield("magic", $magic); echo ' (gitt til deg av et medlem)</td></tr>';
		echo '<tr><td>Fødselsdato</td><td>'; form_select_number("birthday",0,0, $birthday); 
		form_select_number("birthmonth",0,0, $birthmonth);
		form_select_number("birthyear",0,0, $birthyear);
		echo '</td></tr>';
	echo '<tr><td>Webside</td><td>'; form_textfield("webpage", $webpage); echo ' (gjerne en som fins)</td></tr>';
	echo '<tr><td>Bilde</td><td>'; 
		form_hidden("MAX_FILE_SIZE", $max_profile_image_size);
		form_file("picturepath");
		echo '<br/>(Maksimal størrelse er ' . ($max_profile_image_size/1000) . ' kilobytes. Bildet skaleres ned til maks 400 pikslers bredde og 600 pikslers høyde hvis det er større enn disse verdiene)</td></tr>';
	
	echo '<tr><td colspan=2>Ymse visvas<br/>'; form_textarea("description",$description,30,10); echo '<br/>(hvis det er noe mer vi bør vite om deg)<br/><br/></td></tr>';		
	echo '<tr><td colspan=2>'; form_submit("Button", "Send informasjon over usikret, avlyttet linje"); echo '</td></tr>';
	echo '<tr><td colspan=2>'; form_submit("cancelreg", "Avbryt registrering"); echo '</td></tr>';
	form_hidden("m_c", "module_register_user");
	form_hidden("registration", "registration_going_on");
	form_end();		
}