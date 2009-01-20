<?php
/*
 * Created on 20.nov.2005
 *
 * Contains functions for retrieving and saving article data
 */
 
 // Store a new user
 function daoCreateUser( $user ){
 	if (isset($user)) {
 		if (is_a($user, 'User')) {
 			$insert = "INSERT INTO user (username, password, email, firstname, lastname, webpage, birthdate, description) ";
 			$values = "VALUES (" .
 				$user->getUsername() . ", " .
 				$user->getPassword() . ", " .
 				$user->getEmail() . ", " .
 				$user->getFirstname() . ", " .
 				$user->getLastname() . ", " .
 				$user->getWebpage() . ", " .
 				$user->getBirthdate() . ", " .
 				$user->getDescription() .
				")";
			$query = $insert . $values;
			
			//TODO: test, and remove echo, errorhandling.
			$result = insert($query);
			echo $result;
 		} else {
 			//TODO: errorhandling
 		}
 	} else {
 		//TODO: errorhandling
 	}
 }
 
 // Update a user
 function daoUpdateUser( $user ){
 	if (isset($user)) {
 		// is_a: deprecated as of 5.0, instanceof introduced.
 		if (is_a($user, 'User')) {
 			//TODO: escape data, again, should mysqldao do that?
 			$update = "UPDATE user ";
 			$set = "SET password = " . $user->getPassword() .  
				" email = " . $user->getEmail() .
 				" firstname = " . $user->getFirstname() .
 				" lastname = " . $user->getLastname() .
 				" webpage = " . $user->getWebpage() .
 				" birthdate = " . $user->getBirthdate() .
 				" description = " . $user->getDescription() .
 				" ";
 			$where = "WHERE username = " . $user->getUsername();
 			
 			$query = $update . $set . $where;
 			
 			//TODO: test this, remove echo, errorhandling.
 			$result = update($query);
 			echo $result;
 		} else {
 			//TODO: errorhandling, not a User-object, probably a better way to check this.
 		}
 	} else {
 		//TODO: errorhandling
 	}
 }
 
 // Delete a user
 function daoDeleteUser( $user ){
 	// Should probably be marked as deleted rather than actually deleted?
 	// Especially if we enforce relations in the database, which we really should.
 }
 
 // Retrieve info about user
 function daoGetUser( $username ){
 	if (isset($username)) {
 		// something like this? necessary, mysql_dao could possibly do this?
 		$username = addSlashes($username);
 		
 		$select = "SELECT username, password, email, firstname, lastname, webpage, birthdate, description ";
 		$from = "FROM user ";
 		$where = "WHERE username = " . $username;
 		$query = $select . $from . $where;
 		
 		$userinfo = getArray($query);
 		
	 	return new User($userinfo['username'], $userinfo['password'], $userinfo['email'], $userinfo['firstname'], $userinfo['lastname'], $userinfo['webpage'], $userinfo['birthdate'], $userinfo['description']);
 	} else {
 		//TODO: Error msg, no user.
 	}
 }
 
?>
