<?php

/*
 * Created on 20.nov.2005
 *
 */

//TODO: implement value-checking on the set-methods here?
class User {
	var $username;
	var $password;
	var $email;
	var $firstname;
	var $lastname;
	var $webpage;
	var $birthdate;
	var $description;

	// constructor, providing defaultvalues where it's natural.
	// Uses the set-methods where available.
	function User($username, $password, $email = "", $firstname = "", $lastname = "", $webpage = "", $birthdate = "", $description = "") {
		$this->username = $username;
		setPassword($password);
		setEmail($email);
		setFirstname($firstname);
		setLastname($lastname);
		setWebpage($webpage);
		setBirthdate($birthdate);
		setDescription($description);		
	}
	
	function getUsername() {
		return $username;
	}
	
	function checkPassword($password) {
		if (isset($password)) {
			if (sha1($password) === $this->password) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	// Needed for updating and storing of User-objects.
	// Use checkPassword normally, this is supposed to be the encrypted variant.
	function getPassword() {
		return $this->password;
	}
	
	function setPassword($password) {
		if (isset($password)) {
			$this->password = sha1($password);
		}
	}
	
	function getEmail() {
		return $email;
	}
	
	function setEmail($email) {
		if (isset($email)) {
			$this->email = $email;
		}
	}
	
	function getFirstname() {
		return $firstname;
	}
	
	function setFirstname($firstname) {
		if (isset($firstname)) {
			$this->firstname = $firstname;
		}
	}
	
	function getLastname() {
		return $lastname;
	}
	
	function setLastname($lastname) {
		if (isset($lastname)) {
			$this->lastname = $lastname;
		}
	}
	
	function getName() {
		return $firstname . ' ' . $lastname;
	}
	
	function getWebpage() {
		return $webpage;
	}

	function setWebpage($webpage) {
		if (isset($webpage)) {
			$this->webpage = $webpage;
		}
	}
	
	function getBirthdate() {
		return $birthdate;
	}
	
	function setBirthdate($birthdate) {
		if (isset($birthdate)) {
			$this->birthdate = $birthdate;
		}
	}
	
	function getDescription() {
		return $description;
	}
	
	function setDescription($description) {
		if (isset($description)) {
			$this->description = $description;
		}
	}
}
?>

