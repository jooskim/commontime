<?php
session_start();
require("lightopenid/openid.php");
require_once("cookie.php");
require_once("config.php");
require_once("functions.php");

if(isset($_GET['keepSignedIn'])){ 
	$_SESSION['keepSignedIn'] = $_GET['keepSignedIn'];
}

if($_GET['loginType'] == 1){ // OpenID Login
		try{
		$openid = new LightOpenID("http://localhost:8888/ct");
	
		if(!$openid->mode) {
			if(isset($_GET['loginType'])) {
				$openid->identity = 'https://www.google.com/accounts/o8/id';
				$openid->required = array('contact/email', 'namePerson/first', 'namePerson/last');
				$openid->optional = array('namePerson/friendly');
				header('Location: ' . $openid->authUrl());
				return;
			}
		} else {
			if($openid->mode == 'cancel') {
				$_SESSION['error'] = "You have canceled authentication.";
				header("Location: index.php");
			} else if ( ! $openid->validate() ) {
				$_SESSION['error'] = "You were not logged in by Google.";
				header("Location: index.php");
			} else {
				$password = $openid->identity;
				$attributes = $openid->getAttributes();

				$firstName = isset($attributes['namePerson/first']) ? $attributes['namePerson/first'] : false;
				$lastName = isset($attributes['namePerson/last']) ? $attributes['namePerson/last'] : false;
				$userEmail = isset($attributes['contact/email']) ? $attributes['contact/email'] : false;
				$doLogin = true;

			}
		}
	} catch(ErrorException $e) {
		$errormsg = $e->getMessage();
	}

	if(isset($doLogin) && $doLogin === true){
		dbconnect();

		// put into the database
		$userEmail = mysql_real_escape_string($userEmail);
		$firstName = mysql_real_escape_string($firstName);
		$lastName = mysql_real_escape_string($lastName);
		$userPw = $password;

		$query = "SELECT * FROM CT_User WHERE userPw = '$userPw';";
		$result = mysql_query($query, $connect);
		if(!$result){
			$_SESSION['error'] = "DB transaction error!";
			header("Location: index.php");
		}else{
			$data = mysql_fetch_array($result);
			if(mysql_num_rows($result) > 0){
				// update a record when there exists the same account
				if($data['firstName'] != $firstName || $data['lastName'] != $lastName || $data['userEmail'] != $userEmail){
					$query = "UPDATE CT_User SET userEmail = '$userEmail', firstName = '$firstName', lastName = '$lastName', lastAccess = NOW(), emailSHA = SHA1('$userEmail') WHERE id = $data[0];";
				}else{
					$query = "UPDATE CT_User SET lastAccess = NOW() WHERE id = $data[0];";
				}
				$result = mysql_query($query, $connect);
				if(!$result){
					$_SESSION['error'] = "DB update error!";
					header("Location: index.php");
				}else{
					$primaryId = $data['id'];
				}
			}else{
				// insert a record when there doesn't exist the same account
				$query = "INSERT INTO CT_User (userEmail, userPw, firstName, lastName, joinDate, lastAccess, emailSHA) VALUES('$userEmail', '$userPw', '$firstName', '$lastName', NOW(), NOW(), SHA1('$userEmail'));";
				$result = mysql_query($query, $connect);
				if(!$result){
					$_SESSION['error'] = "DB insert error!";
					header("Location: index.php");
				}else{
					$primaryId = mysql_insert_id($connect);
				}
			}
			$level = $data['level'];
			$redFlag = $data['redFlag'];
			$joinDate = $data['joinDate'];
			$lastAccess = $data['lastAccess'];
			$avatarPic = $data['avatarPic'];
		}
		
		dbclose();
		
		if(isset($_SESSION['keepSignedIn']) && $_SESSION['keepSignedIn'] == 1){
			$guid = md5($userPw);
			$encCookie = create_secure_cookie($primaryId, $guid);
			setcookie($CFG->cookiename, $encCookie, time() + 86400 * 30);
		}
		
		$_SESSION['primaryId'] = $primaryId;
		$_SESSION['userIdentity'] = md5($userPw); // used for authorization
		$_SESSION['firstName'] = $firstName;
		$_SESSION['lastName'] = $lastName;
		$_SESSION['userEmail'] = $userEmail;
		$_SESSION['level'] = $level;
		//$_SESSION['redFlag'] = $redFlag;
		//$_SESSION['joinDate'] = $joinDate;
		//$_SESSION['lastAccess'] = $lastAccess;
		//$_SESSION['avatarPic'] = $avatarPic;
		
		unset($_SESSION['keepSignedIn']);
		$_SESSION['success'] = "Hello ".$firstName."!";
		header("Location: index.php");
		
	}else{
		$_SESSION['error'] = "Internal error: OpenID verification information is corrupt!";
		header("Location: index.php");
	}
} else if($_GET['loginType'] == 2){
	if(isset($_SESSION['newId'])){
		$newId = mysql_real_escape_string($_SESSION['newId']);
		$newPw = mysql_real_escape_string($_SESSION['userPw']);
		unset($_SESSION['newId']);
		unset($_SESSION['Pw']);
		
		dbconnect();
		$query = "SELECT * FROM CT_User WHERE id = $newId AND UserPw = '$newPw';";
		
		$result = mysql_query($query, $connect);
		if(!$result){
			$_SESSION['error'] = "Internal error: account does not exist!";
			header("Location: index.php");
		}else{
			$data = mysql_fetch_array($result);
			$_SESSION['primaryId'] = $data['id'];
			$_SESSION['firstName'] = $data['firstName'];
			$_SESSION['lastName'] = $data['lastName'];
			$_SESSION['userEmail'] = $data['userEmail'];
			$_SESSION['level'] = $data['level'];
			
			// update the last access time
			$query = "UPDATE CT_User SET lastAccess = NOW() WHERE id = ".$data['id'].";";
			$result = mysql_query($query, $connect);
			if(!$result){
				$_SESSION['error'] = "DB update (field: lastAccess) error!";
			}else{
				$_SESSION['success'] = 'Hello '.$data['firstName'].'!';
			}
		}
		dbclose();
		header("Location: index.php");
	}else{
		if(!$_SESSION['inputEmail'] || !$_SESSION['inputPw']){
			$_SESSION['error'] = "Internal error: credential data was not properly sent!";
			header("Location: index.php");
		}else{
			$inputEmail = mysql_real_escape_string($_SESSION['inputEmail']);
			$inputPw = mysql_real_escape_string($_SESSION['inputPw']);
			$keepSignedIn = mysql_real_escape_string($_GET['keepSignedIn']);
			unset($_SESSION['inputEmail']);
			unset($_SESSION['inputPw']);
			
			dbconnect();
			$query = "SELECT * FROM CT_User WHERE userEmail = '$inputEmail';";
			$result = mysql_query($query, $connect);
			if(!$result){
				$_SESSION['error'] = "DB transaction error!";
				header("Location: index.php");
			}else{
				if(mysql_num_rows($result) < 1){
					//ERROR MESSAGE
					$_SESSION['error'] = "Account does not exist!";
					header("Location: index.php");
				}else{
					$data = mysql_fetch_array($result);
					if($data['userPw'] != $inputPw){
						$_SESSION['error'] = "Incorrect password!";
						header("Location: index.php");
						dbclose();
					}else{
						$_SESSION['primaryId'] = $data['id'];
						$_SESSION['firstName'] = $data['firstName'];
						$_SESSION['lastName'] = $data['lastName'];
						$_SESSION['userEmail'] = $data['userEmail'];
						$_SESSION['level'] = $data['level'];
						
						
						if(isset($keepSignedIn) && $keepSignedIn == 1){
							$guid = $data['userPw'];
							$encCookie = create_secure_cookie($data['id'], $guid);
							setcookie($CFG->cookiename, $encCookie, time() + 86400 * 30);
						}
						
						// update the last access time
						$query = "UPDATE CT_User SET lastAccess = NOW() WHERE id = ".$data['id'].";";
						$result = mysql_query($query, $connect);
						if(!$result){
							$_SESSION['error'] = "DB update (field: lastAccess) error!";
						}else{
							$_SESSION['success'] = "Hello ".$data['firstName']."!";
						}
						dbclose();
						header("Location: index.php");
					}
				}
			}
		}
	}
} else {
	die("Login type error!");
}
?>