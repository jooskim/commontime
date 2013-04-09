<?php
session_start();
require_once("functions.php");

$arr2JSON = new ArrayObject(array());

if(!isset($_REQUEST['mode'])){
	$_SESSION['error'] = "Retrieve mode not specified!";
	header("Location: index.php");
}else{
	$mode = $_REQUEST['mode'];
	dbconnect();
	
	// clumsy code. needs touch-up
	$querySnippet = "SELECT COUNT(*) FROM CT_";

	$query["scoreNum"] = "SELECT DISTINCT id FROM CT_Score;";
	$query["genreNum"] = "SELECT DISTINCT genre FROM CT_Genre;";
	$query["composerNum"] = "SELECT DISTINCT composer FROM CT_Score WHERE `composer` IS NOT NULL;";
	$query["composeYearNum"] = "SELECT DISTINCT composeYear FROM CT_Score WHERE `composeYear` IS NOT NULL;";
	$query["publishYearNum"] = "SELECT DISTINCT publishYear FROM CT_Score WHERE `publishYear` IS NOT NULL;";
	$query["instrumentationNum"] = "SELECT DISTINCT instrumentation FROM CT_Instrumentation WHERE `instrumentation` IS NOT NULL;";
	
	$num["scoreNum"] = mysql_num_rows(mysql_query($query["scoreNum"], $connect));
	$num["genreNum"] = mysql_num_rows(mysql_query($query["genreNum"], $connect));
	$num["composerNum"] = mysql_num_rows(mysql_query($query["composerNum"], $connect));
	$num["composeYearNum"] = mysql_num_rows(mysql_query($query["composeYearNum"], $connect));
	$num["publishYearNum"] = mysql_num_rows(mysql_query($query["publishYearNum"], $connect));
	$num["instrumentationNum"] = mysql_num_rows(mysql_query($query["instrumentationNum"], $connect));

	dbclose();
	
	if($mode == 0){ // without search keywords
		$arr2JSON->append($num);
		echo(json_encode($arr2JSON));
	}
	
	if($mode == 1){
		dbconnect();
		$srchType = $_REQUEST['srchType'];
		$keyword = $_REQUEST['keyword'];
		if($srchType != 'tag'){
			if($srchType == 'genre'){
				$query = "SELECT ".mysql_real_escape_string($srchType)." FROM CT_Genre WHERE $srchType LIKE '%".mysql_real_escape_string($keyword)."%';";
			}else{
				$query = "SELECT ".mysql_real_escape_string($srchType)." FROM CT_Score WHERE $srchType LIKE '%".mysql_real_escape_string($keyword)."%';";
			}
		}else{
			$query = "SELECT ".mysql_real_escape_string($srchType)." FROM CT_ScoreTag WHERE tag LIKE '%".mysql_real_escape_string($keyword)."%';";
		}
		$result = mysql_query($query, $connect);
		while($data = mysql_fetch_array($result)){
			$arr2JSON->append($data);
		}
		dbclose();
		echo(json_encode($arr2JSON));
	}
	
	if($mode == 2){
		dbconnect();
		$msgTo = $_REQUEST['msgTo'];
		$msgBy = $_REQUEST['msgBy'];
		$msg = $_REQUEST['msgContent'];
		$query = "INSERT INTO CT_ScoreComment (refScore, commentBy, comment, timestamp) VALUES(".mysql_real_escape_string($msgTo).", ".mysql_real_escape_string($msgBy).", '".mysql_real_escape_string($msg)."', UNIX_TIMESTAMP());";
		$result = mysql_query($query, $connect);
		if(!$result){
			$query2 = "SELECT * FROM CT_ScoreComment WHERE refScore = ".mysql_real_escape_string($msgTo)." ORDER BY timestamp DESC;";
			$result2 = mysql_query($query2, $connect);
			while($data2 = mysql_fetch_array($result2)){
				$arr2JSON->append($data2);
			}
			dbclose();
			echo(json_encode($arr2JSON));
		}
	}
	
	if($mode == 3){
		dbconnect();
		$flagTo = $_REQUEST['flagTo'];
		$flagBy = $_REQUEST['flagBy'];
		$flagDesc = $_REQUEST['flagDesc'];
		$query = "INSERT INTO CT_FlagHistory (refScore, flagBy, description, timestamp) VALUES(".mysql_real_escape_string($flagTo).", ".mysql_real_escape_string($flagBy).", '".mysql_real_escape_string($flagDesc)."', UNIX_TIMESTAMP());";
		$query2 = "UPDATE CT_Score SET isFlagged = 1 WHERE id = ".mysql_real_escape_string($flagTo).";";
		$result = mysql_query($query, $connect);
		if($result){
			$result2 = mysql_query($query2, $connect);
		}
		dbclose();
		echo('done');
	}
}
?>