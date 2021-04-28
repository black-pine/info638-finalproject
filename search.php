<?php
	include_once './includes/header.php';
	require_once './includes/include.php';

	echo "<h3>Search Results</h3>";
	$connect = new mysqli($hn, $un, $pw, $db);
	if ($connect->connect_error) { die($connect->connect_error); }
	$searchClause = "";

	// if general search from the search bar
	if (isset($_GET['q']) && !empty($_GET['q'])) {
		$searchReq = 'Search query: <i>'.sanitizeString($_GET['q']).'</i>';
		$safeSearch = sanitizeSQL($connect, $_GET['q']);

		// build search clause comparing all SELECTed columns against the $safeSearch term
		$searchAttrs = explode(', ', substr($searchSelect, 7));
		foreach ($searchAttrs as $k => $col) {
			$searchClause .= "$col LIKE '%$safeSearch%'";
			if ($k != array_key_last($searchAttrs)) { $searchClause .= ' OR '; }
		}
	}
	// if advanced search (or from item page)
	else if (count($_POST) != 0) {
		$searchReq = "";
		foreach ($_POST as $col => $val) {
			if (isset($val) && !empty($val)) {
				// build query WHERE clause
				if (!empty($searchClause)) { $searchClause .= ' AND '; }
				$attr = substr($col, 0, strpos($col, '_'));
				$attr .= '.'.substr($col, strpos($col, '_')+1);
				$safeVal = sanitizeSQL($connect, $val);
				$searchClause .= "$attr LIKE '%$safeVal%'";

				// switch-case to build search query display
				switch ($col) {
					case "coffee_coffee_name":
						$searchReq .= "Coffee Name: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "farm_farm_name":
						$searchReq .= "Farm Name: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "roaster_roaster_name":
						$searchReq .= "Roaster Name: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "farm_origin":
						$searchReq .= "Country of Origin: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "farm_region":
						$searchReq .= "Region: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "blend_type":
						$searchReq .= "Blend / Single Origin: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "bean_varietal":
						$searchReq .= "Varietal: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "bean_species":
						$searchReq .= "Species: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "bean_altitude":
						$searchReq .= "Altitude: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "bean_harvest":
						$searchReq .= "Harvest Date: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "bean_processing":
						$searchReq .= "Processing Method: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "bean_q_score":
						$searchReq .= "Q Score: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "coffee_roast_profile":
						$searchReq .= "Roast Profile: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "coffee_tasting_notes":
						$searchReq .= "Tasting Notes: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "roaster_country":
						$searchReq .= "Roaster Country: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "roaster_city":
						$searchReq .= "Roaster City: <i>".sanitizeString($val)."</i><br/>";
						break;
					case "roaster_state":
						$searchReq .= "Roaster State: <i>".sanitizeString($val)."</i><br/>";
						break;
				}
			}
		}
	}

	// empty search returns all database items
	if (empty($searchClause)) {
		$searchReq = "No search terms entered!";
		$searchClause = "1=1";
	}

	echo "<p>$searchReq</p>";
	// query to pull coffee items that match the search term(s)
	$searchQuery = "$searchSelect $tableJoin WHERE coffee.coffee_id IN (SELECT coffee.coffee_id $tableJoin WHERE $searchClause) ORDER BY coffee.coffee_name, blend.coffee_id";
	$searchResults = $connect->query($searchQuery);
	if (!$searchResults) { die($connect->error); }

	// if no results are returned (exit search)
	if (!$searchResults->num_rows) {
		echo "<p>NO RESULTS</p>";
		$connect->close();
		include_once './includes/footer.php';
		exit;
	}
?>
	<div id='searchTable'>
		<table>
			<thead>
				<tr>
					<th class='coffee'>Coffee Name</th>
					<th class='tasting'>Tasting Notes</th>
					<th class='farm'>Farm Name</th>
					<th class='origin'>Origin</th>
					<th class='varietal species'>Varietal (Species)</th>
					<th class='processing'>Processing</th>
					<th class='altitude'>Altitude (masl)</th>
					<th class='harvest'>Harvest</th>
					<th class='qscore'>Q Score</th>
					<th class='roaster'>Roaster Name</th>
					<th class='roasterloc'>Roaster Location</th>
					<th class='blend'>Blend Type</th>
					<th class='roast'>Roast Profile</th>
				</tr>
			</thead>
			<tbody>
<?php
	$coffeeData = array('id' => 0);
	while ($row = $searchResults->fetch_assoc()) {
		if ($coffeeData['id'] != $row['coffee_id']) {
		// if not the first $row entry then print the existing $coffeeData before overwriting
			if ($coffeeData['id'] != 0) { printSearchRow($coffeeData); }
			// initialize $coffeeData
			$coffeeData['id'] = $row['coffee_id'];
			$coffeeData['coffee'] = $row['coffee_name'];
			$coffeeData['roaster'] = $row['roaster_name'];
			$coffeeData['roast'] = $row['roast_profile'];
			$coffeeData['species'] = $row['species'];
			$coffeeData['blend'] = $row['type'];
			$coffeeData['tasting'] = $row['tasting_notes'];
			$coffeeData['farm'] = array($row['farm_name']);
			$coffeeData['origin'] = array('<i>'.$row['region'].'</i> <span class="origin">('.$row['origin'].')</span>');
			$coffeeData['varietal'] = array($row['varietal']);
			$coffeeData['altitude'] = array($row['altitude']);
			$coffeeData['processing'] = array($row['processing']);
			$coffeeData['roasterloc'] = $row['city'];
			if ($row['state']) { $coffeeData['roasterloc'] .= ', '.$row['state']; }
			$coffeeData['roasterloc'] .= ", ".$row['country'];
			$coffeeData['qscore'] = array();
			if ($row['q_score']) { array_push($coffeeData['qscore'], $row['q_score']); }
			$coffeeData['harvest'] = array();
			if ($row['harvest']) { array_push($coffeeData['harvest'], (int)substr($row['harvest'], 0, 4)); }
		}
		// if additional bean entry for the same coffee
		else {
			array_push($coffeeData['farm'], $row['farm_name']);
			array_push($coffeeData['origin'], '<i>'.$row['region'].'</i> <span class="origin">('.$row['origin'].')</span>');
			array_push($coffeeData['varietal'], $row['varietal']);
			array_push($coffeeData['processing'], $row['processing']);
			array_push($coffeeData['altitude'], $row['altitude']);
			if ($row['q_score']) { array_push($coffeeData['qscore'], $row['q_score']); }
			if ($row['harvest']) { array_push($coffeeData['harvest'], (int)substr($row['harvest'], 0, 4)); }
		}
	}
	// print final row of coffee data
	printSearchRow($coffeeData);
	echo "</tbody></table></div>";

	$searchResults->close();
	$connect->close();

	include_once './includes/footer.php';


	function printSearchRow($coffeeData) {
		// create ranges for items with multiple values 
		if (!empty($coffeeData['qscore'])) { $coffeeData['qscore'] = valRange($coffeeData['qscore']); }
		else { $coffeeData['qscore'] = ''; }
		if (!empty($coffeeData['harvest'])) { $coffeeData['harvest'] = valRange($coffeeData['harvest']); }
		else { $coffeeData['harvest'] = ''; }

		echo "<tr href='".$coffeeData['id']."'><td class='coffee'>".$coffeeData['coffee']."</td><td class='tasting'>".$coffeeData['tasting']."</td><td class='farm'>";
		printArrBr(array_unique($coffeeData['farm']));
		echo "</td><td class='region'>";
		printArrBr(array_unique($coffeeData['origin']));
		echo "</td><td class='varietal'><i>";
		printArr(array_unique($coffeeData['varietal']));
		echo "</i><br/><span class='species'>(".$coffeeData['species'].")</span></td><td class='processing'>";
		printArr(array_unique($coffeeData['processing']));
		echo "</td><td class='altitude'>".valRange($coffeeData['altitude'])."</td><td class='harvest'>".$coffeeData['harvest']."</td><td class='qscore'>".$coffeeData['qscore']."</td><td class='roaster'>".$coffeeData['roaster']."</td><td class='roasterloc'>".$coffeeData['roasterloc']."</td><td class='blend'>".$coffeeData['blend']."</td><td class='roast'>".$coffeeData['roast']."</td>";
	}
?>

<script>
	// make each row linkable to the coffee item page
	$("#searchTable tbody tr").click(function() {
		window.location.href = './coffee.php?id=' + $(this).attr('href');
	});
</script>