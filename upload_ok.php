<?php
session_start();

require_once("config.php");
require_once("functions.php");

$uploadedBy = $_SESSION['primaryId'];

dbconnect();
$title = mysql_real_escape_string($_POST['txtTitle']);
$composer = mysql_real_escape_string($_POST['txtComposer']);
$composeYear = mysql_real_escape_string($_POST['txtComposeYear']);
$genre = mysql_real_escape_string($_POST['txtGenre']); 
$opusNumber = mysql_real_escape_string($_POST['txtOpusNumber']);
$key = mysql_real_escape_string($_POST['txtKey']);
$language = mysql_real_escape_string($_POST['txtLanguage']);
$pieceStyle = mysql_real_escape_string($_POST['txtPieceStyle']);
$instrumentation = mysql_real_escape_string($_POST['txtInstrumentation']);
$copyright = mysql_real_escape_string($_POST['txtCopyright']);
$publishYear = mysql_real_escape_string($_POST['txtPublishYear']);
$scoreImage = mysql_real_escape_string($_POST['txtScoreImage']);
$description = mysql_real_escape_string($_POST['txtDescription']);
$tags = mysql_real_escape_string($_POST['txtTags']);


// check validation



// insert

$query = "INSERT INTO CT_Score (title, isPublic, genre, composer, composeYear, instrumentation, description, timestamp, uploadedBy, language, opusNum, key, style) VALUES ('$title', $copyright, '$genre', '$composer', $composeYear, '$instrumentation', '$description', NOW(), $uploadedBy, '$language', '$opusNum', '$key', $pieceStyle );";
if(!($result = mysql_query($query, $connect))){
	$_SESSION['error'] = "DB insert error!";
	header("Location: mypage.php");
}else{
	header("Location: mypage.php");
}
dbclose();
?>