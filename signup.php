<?php
session_start();

require_once("config.php");
require_once("functions.php");

dbconnect();
$firstName = mysql_real_escape_string($_POST['new_firstName']);
$lastName = mysql_real_escape_string($_POST['new_lastName']);
$Email = mysql_real_escape_string($_POST['new_Email']);
$Pw = mysql_real_escape_string($_POST['new_Pw']);

// checks whether there is an account with the same email account

$query = "SELECT id FROM CT_User WHERE userEmail = '$Email';";
$result = mysql_query($query, $connect);
if(!$result){
	$_SESSION['error'] = "DB transaaction error!";
	header("Location: index.php");
}else{
	if(mysql_num_rows($result) > 0){
		$_SESSION['error'] = "There is already an account with the same email account!";
		header("Location: index.php");
	}else{
		$query = "INSERT INTO CT_User (userEmail, userPw, firstName, lastName, joinDate, emailSHA) VALUES('$Email', MD5('$Pw'), '$firstName', '$lastName', NOW(), SHA1('$Email'));";
		if(!($result = mysql_query($query, $connect))){
			$_SESSION['error'] = "DB insert error!";
			header("Location: index.php");
		}else{
			$_SESSION['newId'] = mysql_insert_id($connect);
			$_SESSION['userPw'] = md5($Pw);
			header("Location: login.php?loginType=2&keepSignedIn=0");
		}
	}
}
dbclose();
?>