<?
require_once("common.php");


function make_cookies(){
	//$DEFAULT_STYLE = "css/aviscms.css";
	$DEFAULT_STYLE = "css/galapagos.css";
	if(isset($_REQUEST['chosenstyle']))
		$styleselect = $_REQUEST['chosenstyle'];
	if(isset($_REQUEST['chosenlayout']))
		$layoutselect= $_REQUEST['chosenlayout'];
	
	global $stylestatus; global $css_file;
	global $layoutstatus; global $layout;
	
	// A cookie exists
	if(isset($_COOKIE['calcuttagutta'])){
		$cookie_contents = explode("-", $_COOKIE['calcuttagutta']);

		if(isset($styleselect)){
			$new_cookie = $_REQUEST['chosenstyle'] . "-";
			$css_file = $_REQUEST['chosenstyle'];
			$stylestatus .= "Ny stil: " . $_REQUEST['chosenstyle'];
		}else{
			$stylestatus .= "Gammel stil: " . $cookie_contents[0];
			$css_file = $cookie_contents[0];
			$new_cookie = $cookie_contents[0] . "-";
		}
			
		if(isset($layoutselect)){
			$layoutstatus .= "Ny layout: " . $_REQUEST['chosenlayout'];
 			$new_cookie .= $_REQUEST['chosenlayout'];
			$layout = $_REQUEST['chosenlayout'];
		}else{
			$layoutstatus .= "Gammel layout: " . $cookie_contents[1];
			$layout = $cookie_contents[1];

			// Must handle old cookies, which say "weblog" instead of "1"
			if ($layout == "weblog"){
				$layout = "1";	
			}
			
			$new_cookie .= $layout;
		}	
		
		setcookie ("calcuttagutta",$new_cookie, time()+60*60*24*30);
	
	}else{
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
			$new_cookie .= $_REQUEST['chosenlayout'];
			$layout = $_REQUEST['chosenlayout'];
		}else{
			$new_cookie .= "1";
			//$layoutstatus .= "Default layout: " . $_REQUEST['chosenlayout'];
			$layout = "1";
		}	
		
		setcookie ("calcuttagutta",$new_cookie, time()+60*60*24*30);		
	
	}
	


}


function mod_pick_style(){
	$styles = array("Standardstil" => "css/aviscms.css", 
				"Thormod" => "http://www.stud.ntnu.no/~nordram/thorstyle.css",
				"Heimegut" => "http://www.festsiden.org/_tore/heimegut.css",
				"Blogspot" => "http://www.festsiden.org/_tore/blogspot.css",
				"Galápagos" => "css/galapagos.css");
	
	global $stylestatus, $layout, $css_file;
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
	form_submit("submitlayout", "Endre");
	form_end();
	echo "</div>";
}