<?php
session_start();

require_once("config.php");
require_once("functions.php");

$uploadedBy = $_SESSION['primaryId'];


// save uploaded file
// $scoreImage = mysql_real_escape_string($_FILES['txtScoreImage']['name']);
 $target = "assets/scores/"; 
 $target = $target . basename( $_FILES['txtScoreImage']['name'] ) ; 
 $ok=1; 
 if(move_uploaded_file($_FILES['txtScoreImage']['tmp_name'], $target)) 
 {
 echo "The file ". basename( $_FILES['txtScoreImage']['name']). " has been uploaded<br>";
 } 
 else {
 echo "Sorry, there was a problem uploading your file.<br>";
 }


dbconnect();
$title = mysql_real_escape_string($_POST['txtTitle']);
$composer = mysql_real_escape_string($_POST['txtComposer']);
$composeYear = mysql_real_escape_string($_POST['txtComposeYear']);
$opusNumber = mysql_real_escape_string($_POST['txtOpusNumber']);
$key = mysql_real_escape_string($_POST['txtKey']);
$language = mysql_real_escape_string($_POST['txtLanguage']);
$pieceStyle = mysql_real_escape_string($_POST['txtPieceStyle']);
$copyright = mysql_real_escape_string($_POST['txtCopyright']);
$publishYear = mysql_real_escape_string($_POST['txtPublishYear']);
$description = mysql_real_escape_string($_POST['txtDescription']);

$genre = mysql_real_escape_string($_POST['txtGenre']); 
$instrumentation = mysql_real_escape_string($_POST['txtInstrumentation']);
$tags = mysql_real_escape_string($_POST['txtTags']);

// check validation
echo 'title:'. $title."<br>";
echo 'composer:'. $composer."<br>";
echo 'composeYear:'. $composeYear."<br>";
echo 'genre:'. $genre."<br>";
echo 'opusNumber:'. $opusNumber."<br>";
echo 'key:'. $key."<br>";
echo 'language:'. $language."<br>";
echo 'pieceStyle:'. $pieceStyle."<br>";
echo 'instrumentation:'. $instrumentation."<br>";
echo 'copyright:'. $copyright."<br>";
echo 'publishYear:'. $publishYear."<br>";
echo 'description:'. $description."<br>";
echo 'target:'. $target."<br>";
echo 'tags:'. $tags."<br>";


// check validation





// insert

$query = "INSERT INTO CT_Score (title, isPublic, composer, composeYear, publishYear, description, downloadLink, timestamp, uploadedBy, language, opusNum, `key`) VALUES ('$title', $copyright, '$composer', $composeYear, $publishYear, '$description', '$target', NOW(), $uploadedBy, '$language', '$opusNumber', '$key');";
echo "<br><br>".$query."<br><br>";
if(!($result = mysql_query($query, $connect))){
	//$_SESSION['error'] = "DB insert error!";
	echo "DB insert error!<br>";
//	header("Location: mypage.php");
}else{
//	header("Location: mypage.php");
	echo "DB insert success!<br>";
	$ct_score_ref_id = mysql_insert_id();
	echo $ct_score_ref_id."<br>";

	// Genre
	$genre_split = explode(",",$genre);
	for ( $i = 0; $i < sizeof($genre_split); $i++ )
	{
		$query = "INSERT INTO ct_genre (refScore, genre) VALUES ($ct_score_ref_id, '".trim($genre_split[$i])."');";
		mysql_query($query, $connect);
	}

	// Instrumentation
	$instrumentation_split = explode(",",$instrumentation);
	for ( $i = 0; $i < sizeof($instrumentation_split); $i++ )
	{
		$query = "INSERT INTO ct_instrumentation (refScore, instrumentation) VALUES ($ct_score_ref_id, '".trim($instrumentation_split[$i])."');";
		mysql_query($query, $connect);
	}

	// Piece Style
	$style_split = explode(",",$pieceStyle);
	for ( $i = 0; $i < sizeof($style_split); $i++ )
	{
		$query = "INSERT INTO ct_style (refScore, style) VALUES ($ct_score_ref_id, '".trim($style_split[$i])."');";
		mysql_query($query, $connect);
	}

	// Tags
	$tags_split = explode(",",$tags);
	for ( $i = 0; $i < sizeof($tags_split); $i++ )
	{
		$query = "INSERT INTO ct_ScoreTag (refScore, tagBy, tag) VALUES ($ct_score_ref_id, $uploadedBy, '".trim($tags_split[$i])."');";
		mysql_query($query, $connect);
	}
}
dbclose();
?>