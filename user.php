<?php
	include_once './includes/header.php';
	require_once './includes/include.php';
	
	if (isset($_SESSION['user_id'])) {
		$connect = new mysqli($hn, $un, $pw, $db);
		if ($connect->connect_error) { die($connect->connect_error); }
		
		echo "<div id='changelists'>";
		// Add list query & form
		if(isset($_POST['add_submit']) && isset($_POST['add']) && !empty($_POST['add'])) {
			$addQuery = "INSERT INTO list VALUES (NULL, ".$_SESSION['user_id'].", '".sanitizeSQL($connect, $_POST['add'])."')";
			$addResults = $connect->query($addQuery);
			if (!$addResults) { die($connect->error); }
		}
		echo "<form method='POST' action=''><label for='add'>Add a new list: </label><input type='text' name='add' placeholder='list name' maxlength='50' required><button type='submit' name='add_submit'>Add</button></form>";

		// Remove list query & form
		if(isset($_POST['remove_submit']) && isset($_POST['remove']) && !empty($_POST['remove'])) {
			$removeQuery = "DELETE FROM list WHERE list_id = ".$_POST['remove'];
			$removeResults = $connect->query($removeQuery);
			if (!$removeResults) { die($connect->error); }
		}
		echo "<form method='POST' action=''><label for='remove'>Remove a list:</label><select name='remove'>";
		$listQuery = "SELECT list_id, list_name FROM list WHERE user_id = ".$_SESSION['user_id'];
		$listResults = $connect->query($listQuery);
		if (!$listResults) { die($connect->error); }
		while ($row = $listResults->fetch_assoc()) {
			echo "<option value='".$row['list_id']."'>".$row['list_name']."</option>";
		}
		echo "</select><button type='submit' name='remove_submit'>Remove</button></form>";
		$listResults->close();

		// Delete coffee item from form submit
		if(isset($_POST['delete']) && isset($_POST['coffee_id']) && !empty($_POST['coffee_id']) && isset($_POST['list_id']) && !empty($_POST['list_id'])) {
			$deleteQuery = "DELETE FROM favorite WHERE list_id = ".$_POST['list_id']." AND coffee_id = ".$_POST['coffee_id'];
			$deleteResults = $connect->query($deleteQuery);
			if (!$deleteResults) { die($connect->error); }
		}

		// Show user favs by list
		echo "</div><div id='userlists'><h2>Welcome ".$_SESSION['first_name']."!</h2>";
		$favoritesQuery = "SELECT DISTINCT coffee.*, roaster.roaster_name, blend.type, list.list_name, list.list_id FROM coffee JOIN roaster ON coffee.roaster_id = roaster.roaster_id JOIN blend ON blend.coffee_id = coffee.coffee_id JOIN favorite ON favorite.coffee_id = coffee.coffee_id RIGHT JOIN list ON list.list_id = favorite.list_id WHERE list.user_id = ".$_SESSION['user_id']." ORDER BY list.list_name";
		$favoritesResults = $connect->query($favoritesQuery);
		if (!$favoritesResults) { die($connect->error); }

		// Check for invalid number of results
		if ($favoritesResults->num_rows == 0) {
			echo "<p>You have no saved coffees!</p>";
		}
		else {
			$list = 0;
			while ($fav = $favoritesResults->fetch_assoc()) {
				if ($list != $fav['list_id']) {
					if ($list != 0) { echo "</ul>"; }
					echo "<h3>".$fav['list_name']."</h3><ul class='favs'>";
					$list = $fav['list_id'];
				}
				if (!$fav['coffee_id']) { echo "<p>There are no coffees in this list!</p>"; }
				else {
					echo "<li><a href='./coffee.php?id=".$fav['coffee_id']."'><img src='./images/".$fav['img_url']."'><p class='favtext'><b>".$fav['coffee_name']."</b><br/>".$fav['roaster_name']."<br/><span class='flavornotes'>".$fav['tasting_notes']."</span></p></a><form action='' method='POST'><input type='hidden' name='coffee_id' value='".$fav['coffee_id']."'><input type='hidden' name='list_id' value='".$fav['list_id']."'><button type='submit' name='delete'>Delete</button></form></li>";
				}
			}
			echo "</ul></div>";
		}
		$favoritesResults->close();
		$connect->close();
	}

	else {
		header("Location: ./login.php");
	}

	include_once './includes/footer.php';
?>