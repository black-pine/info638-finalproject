<?php
	include_once './includes/header.php';
	require_once './includes/include.php';
	
	if (isset($_GET['id'])) {
		$connect = new mysqli($hn, $un, $pw, $db);
		if ($connect->connect_error) { die($connect->connect_error); }
		$itemQuery = "SELECT * $tableJoin WHERE coffee.coffee_id = ".sanitizeSQL($connect, $_GET['id'])." ORDER BY farm.farm_id";
		$itemResults = $connect->query($itemQuery);
		if (!$itemResults) { die($connect->error); }

		// Check for invalid number of results
		if ($itemResults->num_rows == 0) {
			echo "There is no coffee with id ".sanitizeString($_GET['id']);
		}
		else {
			$coffeeData = array('id' => 0);
			while ($row = $itemResults->fetch_assoc()) {
				if ($coffeeData['id'] == 0) {
					// initialize $coffeeData
					$coffeeData['id'] = $row['coffee_id'];
					$coffeeData['coffee'] = $row['coffee_name'];
					$coffeeData['roast'] = $row['roast_profile'];
					$coffeeData['tasting'] = $row['tasting_notes'];
					$coffeeData['price'] = $row['price'];
					$coffeeData['retail'] = $row['bag_size'];
					$coffeeData['purchase'] = $row['purchase_url'];
					$coffeeData['img'] = $row['img_url'];
					$coffeeData['species'] = $row['species'];
					$coffeeData['blend'] = $row['type'];
					$coffeeData['roaster'] = $row['roaster_name'];
					$coffeeData['roasterloc'] = $row['city'];
					if ($row['state']) { $coffeeData['roasterloc'] .= ', '.$row['state']; }
					$coffeeData['roasterloc'] .= ", ".$row['country'];
					$coffeeData['farms'] = array();
				}
				if (!array_key_exists($row['farm_id'], $coffeeData['farms'])) {
					$coffeeData['farms'][$row['farm_id']] = array(
						'name' => $row['farm_name'],
						'origin' => $row['origin'],
						'region' => $row['region'],
						'altitude' => array(),
						'harvest' => array(),
						'varietal' => array(),
						'processing' => array(),
						'qscore' => array()
					);
				}
				array_push($coffeeData['farms'][$row['farm_id']]['altitude'], $row['altitude']);
				array_push($coffeeData['farms'][$row['farm_id']]['varietal'], $row['varietal']);
				array_push($coffeeData['farms'][$row['farm_id']]['processing'], $row['processing']);
				if ($row['harvest']) { array_push($coffeeData['farms'][$row['farm_id']]['harvest'], strtotime($row['harvest'])); }
				if ($row['q_score']) { array_push($coffeeData['farms'][$row['farm_id']]['qscore'], $row['q_score']); }
			}

			// Print out gathered data about the single coffee product
			echo "<div id='coffeeHeader'><h1>".$coffeeData['coffee']."</h1>";
			echo "<p><i>".$coffeeData['roast']." roast | ".$coffeeData['blend']."<br/>".$coffeeData['tasting']."</i></p></div>";
			echo "<img src='./images/".$coffeeData['img']."'/>";
			echo "<div id='roasterfarmers'>";

			foreach ($coffeeData['farms'] as $farm) {
				echo "<div class='farm'><h3><a href='javascript:;' onclick='searchLink(\"farm_farm_name\",this)'>".$farm['name']."</a></h3>";
				echo "<p><span class='label'>Origin: </span>".$farm['region'].", ".$farm['origin']."</p>";
				echo "<p><span class='label'>Altitude: </span>".valRange($farm['altitude'])." masl</p>";
				echo "<p><span class='label'>Processing Method: </span>";
				printArr(array_unique($farm['processing']));
				echo "</p>";

				if (!empty($farm['qscore'])) {
					$farm['qscore'] = valRange($farm['qscore']);
					echo "<p><span class='label'>Q Score: </span>".$farm['qscore']."</p>";
				}
				if (!empty($farm['harvest'])) {
					$farm['harvest'] = valRange($farm['harvest']);
					$harvestTemp = explode(' - ', $farm['harvest']);
					foreach ($harvestTemp as $k => $t) { $harvestTemp[$k] = date('F Y', $t); }
					$farm['harvest'] = implode(' - ', $harvestTemp);
					echo "<p><span class='label'>Harvest Date(s): </span>".$farm['harvest']."</p>";
				}
				echo "</div>";
			}
			echo "<hr><h3><a href='javascript:;' onclick='searchLink(\"roaster_roaster_name\",this)'>".$coffeeData['roaster']."</a></h3>";
			echo "<p>".$coffeeData['roasterloc']."</p><p><a href='".$coffeeData['purchase']."'>Purchase a ".$coffeeData['retail'].'g bag from the roaster for $'.$coffeeData['price']."</a></p></div>";

			// User box to add coffee to list
			echo "<div id='coffeeadd'>";
			if(isset($_SESSION['user_id'])) {
				$successmsg = "";
				if(isset($_POST['submit']) && isset($_POST['list']) && !empty($_POST['list'])) {
					$addQuery = "INSERT INTO favorite VALUES (NULL, ".$_POST['list'].", ".sanitizeSQL($connect, $_GET['id']).")";
					$addResults = $connect->query($addQuery);
					if (!$addResults) { die($connect->error); }
					$successmsg = "<p><i>Successfully added to your list</i></p>";
				}

				echo "<b>Hello ".$_SESSION['first_name']."!</b><form method='POST' action=''><label for='list'>Save this coffee to a list</label><select name='list'>";
				$listQuery = "SELECT list_id, list_name FROM list WHERE user_id = ".$_SESSION['user_id'];
				$listResults = $connect->query($listQuery);
				if (!$listResults) { die($connect->error); }
				while ($row = $listResults->fetch_assoc()) {
					echo "<option value='".$row['list_id']."'>".$row['list_name']."</option>";
				}
				echo "</select><button type='submit' name='submit'>Add</button></form>$successmsg";
			}
			else {
				echo "<a href='./login.php'>Sign in to save this coffee!</a>";
			}
			echo "</div>";

		}
	}

	$itemResults->close();
	$connect->close();
	include_once './includes/footer.php';
?>

<script>
	function searchLink(attr,elem) {
		var form = $('<form></form>');
		form.attr("method", "post");
		form.attr("action", './search.php');

		var part = $('<input></input>');
		part.attr("type", "hidden");
		part.attr("name", attr); //ENTER SEARCH OVER ITEM
		part.attr("value", $(elem).text()); //ENTER SEARCH TEXT
		form.append(part);

        $(form).appendTo('body').submit();
	}
</script>