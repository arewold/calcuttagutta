<?php
/*
 * Created on 14.jan.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 function getSetting($key, $default) {
 	return daoGetSetting($key, $default);
 }
 
 function getSettings($module) {
 	return daoGetSettings($module);
 }
 
 function getSettingType($key) {
 	return daoGetSettingType($key);
 }
 
 function getModules() {
 	return daoGetModules();
 }
 
 function setSetting($key, $value) {
 	// security check:
 	$test = getSetting($key, "ERROR");
 	if ($test == "ERROR" || $test == false) {
 		// key didn't exist!
 		return false;
 	} else { 	
 		return daoSetSetting($key, $value);
 	}
 }
?>
