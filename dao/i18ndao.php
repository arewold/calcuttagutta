<?php
/*
 * Created on 03.des.2005
 *
 */
 function daoGetString($key, $default, $language = "") {
 	// fetch default-language from cookie/user-preferences?
 	// Could merge these two, using e.g. $language = $default_language in the
 	// function-definition, perhaps? (giving one function, defaulting to that, but
 	// if provided in the function-call overriding it)
 	
 	return $default;
 }
 
 //function daoGetString($key, $default, $language) {
 // return $default;
 //}
?>
