<?php
/*
 * Created on 14.jan.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 function showSettingsGUI() {
 	// adminpage, stop here if not logged in/right access-level
	if (!isValidAdmin()) {
		echo (getString("not_valid_admin", "Administratorside, du må logge inn for å få tilgang her"));
		return;
	}	
 	
 	if (isset($_REQUEST['module'])) {
	 	$module = $_REQUEST['module'];
 	} else {
 		$module = "";
 	}

	if (isset($_REQUEST['key'])) {
		$key = $_REQUEST['key'];
	} else {
		$key = "";
	}
	
	h3(getString("settings_header", "Innstillinger"));
	 	
 	// mulighet til å velge andre moduler:
 	showModulesDropDown($module);

	if (isset($_REQUEST['save'])) {
		if ($_REQUEST['save'] == true && $key != "") {
			// check if we got a value, if we did not, it's a unchecked checkbox.'
			// did we get a value to save?
			if (isset($_REQUEST['setting'])) {
				$value = $_REQUEST['setting'];

				// "bugfix" kind of
				if (getSettingType($key) == "boolean") {				
					// check if it is an checkbox, if so, its value is "on"
			 		if ($value == "on") {
			 			$value = "true";
			 		}
				}
			} else {
				// setting the value to false, as this probably is an unchecked checkbox.
				$value = "false";
			}

			// save
			$result = saveSetting($key, $value);

			div_open();
				// success? reset key.			
				if ($result == true) {
					// reset
					$key = "";
					echo (getString("settings_saved_setting", "Innstilling lagret!"));
				} else {
					echo (getString("settings_could_not_save_setting", "Greide ikke å lagre innstilling!"));
				}
			div_close();
		}
	}
	
	// if chosen, show the settings 	
 	if ($module) {
	 	showModuleSettings($module, $key);
 	}
 }
 
 function showModuleSettings($module, $key) {
	 	$table = getSettings($module);
 	
 	 	table_open();
 		if ($table) {
	 		tr_open(); 
	 			td_open(1); echo(getString("settings_description", "Beskrivelse")); td_close();
	 			td_open(1); echo(getString("settings_value", "Verdi")); td_close();
	 			td_open(1); echo(getString("settings_type", "Type")); td_close();
	 			td_open(1); echo(getString("settings_edit", "Endre")); td_close();
	 		tr_close();

 			while ($row = nextResultInTable($table)) {
		 		tr_open(); 
		 			td_open(1); echo($row['description']); td_close();

			 		form_start_post();
			 			if ($row['settingskey'] == $key) {
			 				showSetting($row, true);

					 		td_open(1);
						 			form_hidden("m_c", "showSettingsGUI");
						 			form_hidden("module", $module);
						 			form_hidden("key", $row['settingskey']);
						 			form_hidden("save", true);
								 	form_submit("submit", getString("settings_save_setting", "Lagre"));
					 		td_close();
					 		
			 			} else {
			 				showSetting($row, false);
	
					 		td_open(1);
						 			form_hidden("m_c", "showSettingsGUI");
						 			form_hidden("module", $module);
						 			form_hidden("key", $row['settingskey']);
								 	form_submit("submit", getString("settings_edit_setting", "Endre"));
					 		td_close();
			 			}
			 		form_end();
			 		
		 		tr_close();
 			}
 		} else {
 			// ingen settings, gi beskjed:
	 		tr_open(); 
	 			td_open(1); echo(getString("settings_could_not_find_settings_for_this_module", "Fant ingen innstillinger for denne modulen")); td_close();
	 		tr_close();
 		}
 	table_close();
 }
 
 function showModulesDropDown($module = "") {
 	$table = getModules();

	div_open();
	 	form_start_post();
		 	form_select("module");
			 	if ($table) {
			 		while ($row = nextResultInTable($table)) {
			 			if ($module == $row['module']) {
			 				form_option($row['module'], $row['module'], "true");
			 			} else {
			 				form_option($row['module'], $row['module']);
			 			}
			 		}
			 	} else {
			 		// no modules with settings available
			 		form_option("-", "");
			 	}
		 	form_select_end();
		
			// this module
		 	form_hidden("m_c", "showSettingsGUI");
		 	
		 	// button
		 	form_submit("submit", getString("settings_show_settings", "Vis innstillinger"));
	 	form_end();
 	div_close();
 }
 
 function showSetting($row, $enabled) {
	if ($row['settingstype'] == "boolean") {
		td_open(1); 
			if ($row['value'] == "true") {
				form_checkbox("setting", $enabled, true);
			} else {
				form_checkbox("setting", $enabled, false);
			}
		td_close();
		td_open(1); 
			echo(getString("settings_boolean", "Boolsk")); 
		td_close();

	} else if ($row['settingstype'] == "integer") {
		td_open(1); 
 				form_textfield("setting", $row['value'], $enabled);
		td_close();
		td_open(1); 
			echo(getString("settings_integer", "Heltall")); 
		td_close();

	} else if ($row['settingstype'] == "string") {
		td_open(1); 
 				form_textfield("setting", $row['value'], $enabled);
		td_close();
		td_open(1); 
			echo(getString("settings_string", "Tekst")); 
		td_close();

	} else {
		td_open(1); 
 			echo($row['value']);
		td_close();
		td_open(1); 
			echo(getString("settings_unknown_type", "Ukjent datatype")); 
		td_close();
	}
 }
 
 function saveSetting($key, $value) {
 	//echo ("Forsøker å lagre: " . $key . " med verdi: " . $value);
 	
 	// Fetch the datatype of the key
 	$type = getSettingType($key);
 	
 	// check which type, and if the input is valid
 	if ($type == "boolean") {
 		$accepted_input = checkValidBoolean($value);
 	} else if ($type == "integer") {
 		$accepted_input = checkValidInteger($value);
 	} else if ($type == "string") {
 		$accepted_input = checkValidString($value);
 	} else {
 		$accepted_input = false;
 	}

	// if input is valid for that key, save
	if ($accepted_input == true) {
		return setSetting($key, $value);
	} else {
		// not ok, failed/invalid
		return false;
	}
 }
 
 function checkValidBoolean($input) {
 	if ($input == "false" || $input == "true") {
 		return true;
 	}
 }
 
 function checkValidInteger($input) {
 	// a number at all?
 	if (is_numeric($input)) {
 		// is the int-value the same after converting it?
 		if (intval($input) == $input) {
 			return true;
 		} else {
 			return false;
 		}
 	} else {
 		return false;
 	}
 }
 
 function checkValidString($input) {
 	return is_string($input);
 }
?>
