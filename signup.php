<?php
	include_once './includes/header.php';
	require_once './includes/include.php';

	echo "<h3>Sign Up</h3>";

	// insert new account information into user table
	if(isset($_POST['submit'])) {
		$connect = new mysqli($hn, $un, $pw, $db);
		if ($connect->connect_error) { die($connect->connect_error); }

		$fname = sanitizeSQL($connect, $_POST['fname']);
		$lname = sanitizeSQL($connect, $_POST['lname']);
		$username = sanitizeSQL($connect, $_POST['username']);
		$password = sanitizeSQL($connect, $_POST['password']);
		$password = encryptPass($password);

		$signupQuery = "INSERT INTO user VALUES (NULL, '$fname', '$lname', '$username', '$password')";
		$signupResults = $connect->query($signupQuery);
		if (!$signupResults) { die($connect->error); }

		// create a default "favorites" list for the user
		$newUserId = $connect->insert_id;
		$defaultListQuery = "INSERT INTO list VALUES (NULL, $newUserId, 'Favorites')";
		$defaultListResults = $connect->query($defaultListQuery);
		if (!$defaultListResults) { die($connect->error); }
		
		$connect->close();
		// show success message and link to login page
		echo "<p><a href='./login.php'>Successful registration! Log in here.</a></p>";
	}
	// show option to log in if user did not just create an account
	else { echo "<p><a href='./login.php'>Or log in here</a></p>"; }
?>

<p id='formp'><span class='req'>*</span> indicates a required field</p>
<form method='POST' action='' class='authentication'>
	<label for="fname">First Name<span class='req'>*</span></label>
	<input type="text" name="fname" maxlength="32" required>
	<label for="lname">Last Name</label>
	<input type="text" name="lname" maxlength="32">
	<label for="username">Username<span class='req'>*</span></label>
	<input type="text" name="username" maxlength="32" required>
	<label for="password">Password<span class='req'>*</span></label>
	<input type="password" name="password" maxlength="32" required>
	<input type="submit" name="submit" value='Submit'>
</form>

<?php
	include_once './includes/footer.php';
?>