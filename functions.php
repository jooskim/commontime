<?php
require_once("connect.php");

/* Variables */
$connect;
$json_container = new ArrayObject(array());

/* Functions */

function dbconnect()
{
	global $conn, $connect;
	$connect = mysql_connect($conn[0], $conn[1], $conn[2]);
	if(!$connect)
	{
		die("MySQL connection error!");
	}
	else
	{
		if(!mysql_select_db("Commontime"))
		{
			die("DB connection error!");
		}
	}
}

function dbclose()
{
	global $connect;
	if(!$connect)
	{
		die("There is no database connection!");
	}
	else
	{
		mysql_close($connect);
	}
}

function getAllJSON($table)
{
	global $json_container, $connect;
	$return = '';
	
	dbconnect();
	$query = "SELECT * FROM ".mysql_real_escape_string($table).";";
	$result = mysql_query($query, $connect);
	$data = @mysql_fetch_array($result);
	
	if($result === false)
	{
		return 'There is no such a table';
	}
	else
	{
		$json_container->append($data);
		$return = json_encode($json_container);
	}
	dbclose();
	
	return $return;
}

function getPartJSON($table, $fields, $filter)
{
	global $json_container, $connect;
	$return = '';
	
	dbconnect();
	$query = "SELECT ".mysql_real_escape_string($fields)." FROM ".mysql_real_escape_string($table)." WHERE ".mysql_real_escape_string($filter).";";
	$result = mysql_query($query, $connect);
	if($result === false)
	{
		return 'There is no such a table';
	}
	else
	{
		if(mysql_num_rows($result) <= 1)
		{
			$data = @mysql_fetch_array($result);
			$json_container->append($data);
			$return = json_encode($json_container);
		}
		else
		{
			while($data = @mysql_fetch_array($result))
			{
				$json_container->append($data);
			}
			$return = json_encode($json_container);
		}
	}
	dbclose();
	
	return $return;
}

function getSQLFromID($type, $param) {
	if($type == "firstName"){
		return "SELECT firstName FROM CT_User WHERE id = ".mysql_real_escape_string($param).";";
	}else if($type == "lastName"){
		return "SELECT lastName FROM CT_User WHERE id = ".mysql_real_escape_string($param).";";
	}else if($type == "redFlag"){
		return "SELECT redFlag FROM CT_User WHERE id = ".mysql_real_escape_string($param).";";
	}else if($type == "joinDate"){
		return "SELECT joinDate FROM CT_User WHERE id = ".mysql_real_escape_string($param).";";
	}else if($type == "flagHistory"){
		return "SELECT * FROM CT_FlagHistory WHERE refScore = ".mysql_real_escape_string($param)." ORDER BY timestamp DESC;";
	}else if($type == "genre"){
		return "SELECT * FROM CT_Genre WHERE refScore = ".mysql_real_escape_string($param).";";
	}else if($type == "instrumentation"){
		return "SELECT * FROM CT_Instrumentation WHERE refScore = ".mysql_real_escape_string($param).";";
	}else if($type == "comment"){
		return "SELECT * FROM CT_ScoreComment WHERE refScore = ".mysql_real_escape_string($param)." ORDER BY timestamp DESC;";
	}else if($type == "style"){
		return "SELECT style FROM CT_Style WHERE id = ".mysql_real_escape_string($param).";";
	}else if($type == "tag"){
		return "SELECT tag FROM CT_ScoreTag WHERE refScore = ".mysql_real_escape_string($param).";";
	}
}

function idToValue($val, $table, $condition){
	global $connect;
	dbconnect();
	$query = "SELECT ".mysql_real_escape_string($val)." FROM ".mysql_real_escape_string($table)." WHERE id = ".mysql_real_escape_string($condition).";";
	$result = mysql_query($query, $connect);
	$data = @mysql_fetch_array($result);
	dbclose();
	return $data;
}
?>