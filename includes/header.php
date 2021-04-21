<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8' />
		<link rel="shortcut icon" href="./images/favicon.ico" />
		<link rel='stylesheet' href='./includes/styles.css' type='text/css' />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src='./includes/jquery-3.6.0.min.js' type='text/javascript'></script>
		<title>Coffee Database</title>
	</head>

<?php session_start(); ?>

	<body>
		<header>
			<a href='./' id='hgroup'>
				<h1>The Coffee Database</h1>
				<br/>
				<h3>Transparency. Informed. Delicious.</h3>
			</a>
			<nav>
				<ul>
					<?php
						if(!isset($_SESSION['user_id'])) { echo "<li><a href='./login.php'>Log In</a></li>"; }
						else { echo "<li><a href='./logout.php'>Log Out</a></li>"; }
					?>

					<li><a href='./browse.php'>Browse</a>
						<ul>
							<li><a href='./browse.php?origin'>Country of Origin</a></li>
							<li><a href='./browse.php?roaster'>Roaster</a></li>
							<li><a href='./browse.php?species'>Species</a></li>
							<li><a href='./browse.php?roast'>Roast Profile</a></li>
						</ul>
					</li>
					<li><a href='./advancedsearch.php'>Advanced Searh</a></li>
					<?php if(isset($_SESSION['user_id'])) { echo "<li><a href='./user.php'>My Coffees</a></li>"; } ?>
					<li><a href='./about.php'>About</a></li>
					<form action='./search.php' method='GET'>
						<input type="text" name="q" placeholder="Search..." required>
						<button type="submit"><i class='fa fa-search'></i></button>
					</form>
				</ul>
			</nav>
		</header>
		<div id='content'>