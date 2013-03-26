<?php
require_once("functions.php");
// getPartJSON({table name}, {fields in string}, {filter})
echo(getPartJSON("CT_Score", "title, composer", "1"));
	
?>