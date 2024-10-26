<?php
session_start();

if (isset($_COOKIE['PHPSESSID'])) {
	session_destroy();
	setcookie('PHPSESSID', '', time() - 3600);
	header("Location: index.php");
	exit();
} else {
	header("Location: index.php");
	exit();
}
?>
