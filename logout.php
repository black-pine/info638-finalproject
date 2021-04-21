<?php
	include_once './includes/header.php';
	require_once './includes/include.php';

	$_SESSION = array();
	session_destroy();

	echo "<h3>Logged Out</h3>";
	echo "<p><a href='./'>Return to homepage</a></p>";	

	include_once './includes/footer.php';
?>
