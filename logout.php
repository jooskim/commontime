<?php
session_start();
require_once("cookie.php");

delete_secure_cookie();
session_unset();

$_SESSION['success'] = "Successfully logged out";
header("Location: index.php");
?>