<?php
/*
 * Created on 03.des.2005
 *
 */
 function daoGetSetting($key, $default) {
 	$query = "SELECT value FROM settings WHERE settingskey = '" . $key . "'";
 	$result = getScalar($query);
 	
 	if ($result) {
 		return $result;
 	}
 	else {
	 	return $default;
 	}
 }
 
 function daoGetSettingType($key) {
 	$query = "SELECT settingstype FROM settings WHERE settingskey = '" . $key . "'";
 	return getScalar($query);
 }
 
 function daoGetSettings($module) {
 	if ($module) {
 		$query = "SELECT settingskey, value, settingstype, description, module FROM settings WHERE module = '" . $module . "'";
 	} else {
 		$query = "SELECT settingskey, value, settingstype, description, module FROM settings";
 	}
 	
 	return getTable($query);
 }
 
 function daoGetModules() {
 	$query = "SELECT DISTINCT module FROM settings";
 	return getTable($query);
 }
 
 function daoSetSetting($key, $value) {
 	$query = "UPDATE settings SET value = '" . $value . "' WHERE settingskey = '" . $key . "'";
 	return update($query);
 }
?>
