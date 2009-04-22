<?
require_once("common.php");


function make_cookies(){
	global $stylestatus; global $css_file;
	global $layoutstatus; global $layout;
	global $languageChoice;
	
	$DEFAULT_STYLE = "css/galapagos.css";
	if(isset($_REQUEST['chosenstyle']))
		$styleselect = $_REQUEST['chosenstyle'];
	if(isset($_REQUEST['chosenlayout']))
		$layoutselect= $_REQUEST['chosenlayout'];
	if(isset($_REQUEST['languageChoice']))
		$languageChoice = $_REQUEST['languageChoice'];
		

	// A cookie exists
	if(isset($_COOKIE['calcuttagutta'])){
		$cookie_contents = explode("-", $_COOKIE['calcuttagutta']);
		
		if(isset($styleselect)){
			$new_cookie = $_REQUEST['chosenstyle'] . "-";
			$css_file = $_REQUEST['chosenstyle'];
			addDebug("Ny stil: " . $_REQUEST['chosenstyle']);
		}else{
			addDebug("Gammel stil: " . $cookie_contents[0]);
			$css_file = $cookie_contents[0];
			$new_cookie = $cookie_contents[0] . "-";
		}
			
		if(isset($layoutselect)){
			$layoutstatus .= "Ny layout: " . $_REQUEST['chosenlayout'];
			addDebug("Ny layout: " . $_REQUEST['chosenlayout']);
 			$new_cookie .= $_REQUEST['chosenlayout'] . "-";
			$layout = $_REQUEST['chosenlayout'];
		}else{
			$layoutstatus .= "Gammel layout: " . $cookie_contents[1] . " hele cookie: ";
			addDebug("Gammel layout: " . $cookie_contents[1] . " hele cookie: ");
			foreach($cookie_contents as $innhold){
				$layoutstatus .= $innhold;
			}
			$layout = $cookie_contents[1];

			// Must handle old cookies, which say "weblog" instead of "1"
			if ($layout == "weblog"){
				$layout = "1";	
			}			
			$new_cookie .= $layout . "-";
		}	
		
		//	Check for language preference
		if(isset($_REQUEST['languageChoice'])){
			$layoutstatus .= "--had cookie, had choice in request--";
			addDebug("--had cookie, had choice in request--");
			// If a new is specified - use that one and add it to cookie
			$languageChoice = $_REQUEST['languageChoice'];
		} else {
			// No specified - try to get from cookie
			$languageChoice = $cookie_contents[2];
			
			
			if($languageChoice == null){
				$layoutstatus .= "--had cookie, had no language set--";
				addDebug("--had cookie, had no language set--");
				// Set blank if cookie didn't have any goodies
				$languageChoice = "-1";
			} else {
				$layoutstatus .= "--had cookie, had language set in it to " . $languageChoice . "--";
				addDebug("--had cookie, had language set in it to " . $languageChoice . "--");
			}		
		}
		
		addDebug("Adding languageChoice " . $languageChoice . " to cookie");
		$new_cookie .= $languageChoice . "-";
		
		
		addDebug("Old cookie updated contents: " . $new_cookie);
		setcookie ("calcuttagutta",$new_cookie, time()+60*60*24*30);
	
	}else{
		// There's no cookie, create one
		if(isset($styleselect)){
			$stylestatus .= "Ny stil og cookie: " . $_REQUEST['chosenstyle'];
			$new_cookie = $_REQUEST['chosenstyle'] . "-";
			$css_file = $_REQUEST['chosenstyle'];
		}else{
			$new_cookie = $DEFAULT_STYLE . "-";
			//$stylestatus .= "Default stil: " . $_REQUEST['chosenstyle'];
			$css_file = $DEFAULT_STYLE;	
		}
			
		if(isset($layoutselect)){
			$layoutstatus .= "Ny layout og cookie: " . $_REQUEST['chosenlayout'];
			$new_cookie .= $_REQUEST['chosenlayout'] . "-";
			$layout = $_REQUEST['chosenlayout'];
		}else{
			$new_cookie .= "1" . "-";
			//$layoutstatus .= "Default layout: " . $_REQUEST['chosenlayout'];
			$layout = "1";
		}	
		
		if(isset($_REQUEST['languageChoice'])){
			$languageChoice = isset($_REQUEST['languageChoice']);
			$new_cookie .= $_REQUEST['languageChoice'];
		} else {
			$languageChoice = '-1';
			$new_cookie .= '-1';
		}

		addDebug("Brand new cookie contents: " . $new_cookie);
		setcookie ("calcuttagutta",$new_cookie, time()+60*60*24*30);		
	
	}
	
	

}


function mod_pick_style(){
	$styles = array("Standardstil" => "css/aviscms.css", 
				"Thormod" => "http://www.stud.ntnu.no/~nordram/thorstyle.css",
				"Heimegut" => "http://www.festsiden.org/_tore/heimegut.css",
				"Blogspot" => "http://www.festsiden.org/_tore/blogspot.css",
				"Galápagos" => "css/galapagos.css");
	
	global $stylestatus, $layout, $css_file, $languageChoice;
	echo '<div class="options"><div class="optionsheader">Stilvelger</div>';
	form_start_get();
	
	echo '<select name="chosenstyle">';
	foreach ($styles as $stylename => $stylefile){
		if($stylefile == $css_file){
			echo '<option value="' . $stylefile . '" selected="selected">' . $stylename . '</option>';	
		}else{
			echo '<option value="' . $stylefile. '">' . $stylename . '</option>';		
		}		
	}
	echo "</select>";
	form_hidden("module_right_2", "mod_pick_style");
	form_hidden("styleselect", "styleselect");
	echo "<br/>";echo "<br/>";
	
	echo '<div class="optionsheader">Velg layout</div>';
		?>
		<select name="chosenlayout">
		<?
			
				echo '<option value="1">En kolonne</option>';
				echo '<option value="2">To kolonner</option>';
				echo '<option value="3">Tre kolonner</option>';
				echo '<option value="4">Fire kolonne</option>';
			
		?>
		</select>
	<?
	form_hidden("redirect", "true");
	form_hidden("module_right_2", "mod_pick_style");
	form_hidden("layoutselect", "layoutselect");
	
	echo "<br/>";echo "<br/>";
	echo '<div class="optionsheader">Velg språk</div>';
	
	$arrayWithAllLanguageIds = getAllLanguageIds();
	$arrayWithAllLanguageNames = getAllLanguageNames();
	
	// Add a choice for viewing articles in all languages
	array_unshift($arrayWithAllLanguageIds, "-1");
	array_unshift($arrayWithAllLanguageNames, "Alle språk");
	debug("fra cookie..:" . $languageChoice . "!");
	debug("fra cookie..:" . $_REQUEST['languageChoice'] . "!");
	form_dropdown("languageChoice", $arrayWithAllLanguageIds, $arrayWithAllLanguageNames, $languageChoice + 1);
	
	echo "<br/>";echo "<br/>";
	form_submit("submitlayout", "Endre");
	form_end();
	echo "</div>";
}