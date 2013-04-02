<?php
session_start();
require_once("cookie.php");

delete_secure_cookie();
session_unset();
header("Location: index.php");
?>