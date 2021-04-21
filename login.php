<?php
	include_once './includes/header.php';
	require_once './includes/include.php';

	echo "<h3>Log In</h3>";
	echo "<p><a href='./signup.php'>Or sign up here</a></p>";

	if(isset($_POST['submit'])) {
		$connect = new mysqli($hn, $un, $pw, $db);
		if ($connect->connect_error) { die($connect->connect_error); }

		$username = sanitizeSQL($connect, $_POST['username']);
		$password = sanitizeSQL($connect, $_POST['password']);
		$salt1 = "2T(3n";
		$salt2 = "#x^rA0";
		$password = hash('ripemd128',$salt1.$password.$salt2);

		$loginQuery = "SELECT user_id, first_name FROM user WHERE username = '$username' AND password = '$password'";
		$loginResults = $connect->query($loginQuery);
		if (!$loginResults) { die($connect->error); }

		if ($loginResults->num_rows == 1) {
			$row = $loginResults->fetch_assoc();
			$_SESSION['first_name'] = $row['first_name'];
			$_SESSION['user_id'] = $row['user_id'];
			header("Location: ./user.php");
		}
		else {
			echo "<p>Invalid username and/or password</p>";
		}

	}
?>

<form method='POST' action='' class='authentication'>
	<label for="username">Username</label>
	<input type="text" name="username" required>
	<label for="password">Password</label>
	<input type="password" name="password" required>
	<input type="submit" name="submit" value='Submit'>
</form>

<?php
	include_once './includes/footer.php';
?>