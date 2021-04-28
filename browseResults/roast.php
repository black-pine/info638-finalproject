<?php
	require_once '../includes/include.php';
	$connect = new mysqli($hn, $un, $pw, $db);
	if ($connect->connect_error) { die($connect->connect_error); }
?>

<tr>
	<td>
		<!-- query database for all roast profiles, print each result as a navigation list item, & save each result to an array -->
		<ul class='browseToggles'>
			<?php
				$navItem = array();
				$navQuery = "SELECT DISTINCT coffee.roast_profile FROM coffee";
				$navResults = $connect->query($navQuery);
				if (!$navResults) { die($connect->error); }
				while ($row = $navResults->fetch_assoc()) {
					array_push($navItem, ucfirst($row['roast_profile']));
					echo "<li class='roastNav'>".ucfirst($row['roast_profile'])."</li>";
				}
				$navResults->close();
			?>
		</ul>
	</td>

	<td class='tableHolder'>
		<table>
			<colgroup>
				<col style='width:8%'>
				<col style='width:16%'>
				<col style='width:13%'>
				<col style='width:17%'>
				<col style='width:8%'>
				<col style='width:5%'>
				<col style='width:15%'>
				<col style='width:12%'>
				<col style='width:7%'>
			</colgroup>
			<thead>
				<tr>
					<th class='roast'>Roast Profile</th>
					<th class='coffee'>Coffee Name</th>
					<th class='roaster'>Roaster Name</th>
					<th class='farm'>Farm Name</th>
					<th class='country'>Origin</th>
					<th class='species'>Species</th>
					<th class='varietals'>Varietal</th>
					<th class='processing'>Processing</th>
					<th class='blend'>Blend Type</th>
				</tr>
			</thead>

				<?php
					// for each item from the navigation (saved in an array), create a <tbody> element with relevant items as rows
					foreach ($navItem as $roastType) {
						$roastTypeId = str_replace(' ', '', $roastType);
						echo "<tbody id='$roastTypeId' hidden>";
						$roastQuery = "$browseSelect $tableJoin WHERE coffee.roast_profile = '$roastType' ORDER BY coffee.coffee_id";
						$roastResults = $connect->query($roastQuery);
						if (!$roastResults) { die($connect->error); }
						$coffeeData = array('id' => 0);
						while ($row = $roastResults->fetch_assoc()) {
							// initialize information for a coffee item
							if ($coffeeData['id'] != $row['coffee_id']) {
								// if not the first $row entry then print the existing $coffeeData before overwriting
								if ($coffeeData['id'] != 0) { printRoastRow($coffeeData); }
								// initialize $coffeeData
								$coffeeData['id'] = $row['coffee_id'];
								$coffeeData['coffee'] = $row['coffee_name'];
								$coffeeData['farm'] = array($row['farm_name']);
								$coffeeData['roaster'] = $row['roaster_name'];
								$coffeeData['country'] = array($row['origin']);
								$coffeeData['varietal'] = array($row['varietal']);
								$coffeeData['species'] = $row['species'];
								$coffeeData['blend'] = $row['type'];
								$coffeeData['processing'] = array($row['processing']);
								$coffeeData['roast'] = $row['roast_profile'];
							}
							// if additional bean entry for same coffee
							else {
								array_push($coffeeData['farm'], $row['farm_name']);
								array_push($coffeeData['country'], $row['origin']);
								array_push($coffeeData['varietal'], $row['varietal']);
								array_push($coffeeData['processing'], $row['processing']);
							}
						}
						// print last $coffeeData / $row item
						printRoastRow($coffeeData);

						$roastResults->close();
						echo "</tbody>";
					}
				?>
				
		</table>
	</td>
</tr>

<?php
	$connect->close();

	function printRoastRow($coffeeData) {
		echo "<tr href='".$coffeeData['id']."'><td class='roast'>".$coffeeData['roast']."</td><td class='coffee'>".$coffeeData['coffee']."</td><td class='roaster'>".$coffeeData['roaster']."</td><td class='farm'>";
		printArrBr(array_unique($coffeeData['farm']));
		echo "</td><td class='country'>";
		printArrBr(array_unique($coffeeData['country']));
		echo "</td><td class='species'>".$coffeeData['species']."</td><td class='varietals'>";
		printArr(array_unique($coffeeData['varietal']));
		echo "</td><td class='processing'>";
		printArr(array_unique($coffeeData['processing']));
		echo "</td><td class='blend'>".$coffeeData['blend']."</td></tr>";
	}
?>

<script>
	// default to showing the first <tbody> / navigation item
	$(function() {
		$("tbody[hidden]:first").toggle();
		$(".browseToggles li:first").css('background-color', '#9C958F');
	});

	$(".roastNav").click(function() {
		// toggle to show/hide the <tbody> element relating to the clicked on navigation item
		var tbodyId = $(this).text().replace(/ /g, "");
		$('#'+tbodyId).toggle();
		// toggle navigation item background color
		$(this).css('background-color') == 'rgb(156, 149, 143)' ? $(this).css('background-color', '#CED4D7') : $(this).css('background-color', '#9C958F');
		// shade every other row based on all currently visible rows
		$('tbody[style="display: table-row-group;"] tr:even').css('background-color', '#FFFFFF');
		$('tbody[style="display: table-row-group;"] tr:odd').css('background-color', '#E8EEF1');
		// hover effect to change background color (for both shaded & non-shaded rows)
		$('tbody[style="display: table-row-group;"] tr:even').hover(function() { $(this).css('background-color', '#CFC8C2'); }, function() { $(this).css('background-color', '#FFFFFF'); });
		$('tbody[style="display: table-row-group;"] tr:odd').hover(function() { $(this).css('background-color', '#CFC8C2'); }, function() { $(this).css('background-color', '#E8EEF1'); });
	});

	// make each row linkable to the coffee item page
	$(".tableHolder tbody tr").click(function() {
		window.location.href = './coffee.php?id=' + $(this).attr('href');
	});
</script>