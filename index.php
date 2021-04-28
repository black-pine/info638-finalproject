<?php
	include_once './includes/header.php';
	require_once './includes/include.php';
	$connect = new mysqli($hn, $un, $pw, $db);
	if ($connect->connect_error) { die($connect->connect_error); }

	echo "<h2>Explore Coffee</h2>";
	echo "<div id='featured'>";
	echo "<p>Discover a random coffee:</p>";
	// select a random coffee to be "featured"
	$randQuery = "SELECT * FROM coffee ORDER BY RAND() LIMIT 1";
	$randResults = $connect->query($randQuery);
	if (!$randResults) { die($connect->error); }
	$randCoffee = $randResults->fetch_assoc();
	echo "<a href='./coffee.php?id=".$randCoffee['coffee_id']."'><h3>".$randCoffee['coffee_name']."</h3><img src='./images/".$randCoffee['img_url']."'></a>";
	$randResults->close();

	echo "</div>";
?>

<div id='home'>
	<p><img src='./images/faviconbean.png'><a href='./browse.php?roast'>Looking for light roast coffees?</a></p>
	<p><img src='./images/faviconbean.png'><a href='./browse.php?origin'>Brazilian coffees?</a></p>
	<p><img src='./images/faviconbean.png'><a href='./search.php?q=chocolate'>Chocolatey coffees?</a></p>
	<p><img src='./images/faviconbean.png'><a href='./browse.php?species'>Specialty robustas?</a></p>
<?php
	// link to the coffee item with the max q_score value (if no coffees have a q_score, skip this link)
	$ratingQuery = "SELECT coffee.coffee_id FROM coffee JOIN blend ON coffee.coffee_id = blend.coffee_id JOIN bean ON blend.blend_id = bean.bean_id ORDER BY bean.q_score DESC LIMIT 1";
	$ratingResults = $connect->query($ratingQuery);
	if (!$ratingResults) { die($connect->error); }
	if ($ratingResults->num_rows == 0) { echo "<br/>"; }
	else {
		$topRated = $ratingResults->fetch_assoc();
		echo "<p><img src='./images/faviconbean.png'><a href='./coffee.php?id=".$topRated['coffee_id']."'>The top rated coffee?</a></p><br/>";
	}
	$ratingResults->close();
	$connect->close();
?>
	<p><a href='./advancedsearch.php'>Or search to find exactly what you are looking for.</a><img src='./images/faviconbean.png'></p>
</div>

<?php
	include_once './includes/footer.php';
?>
