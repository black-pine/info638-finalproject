<?php
	require_once '../includes/include.php';
	$connect = new mysqli($hn, $un, $pw, $db);
	if ($connect->connect_error) { die($connect->connect_error); }
?>
<tr>
	<td>
		<ul class='browseToggles'>
			<?php
				$navItem = array();
				$navQuery = "SELECT DISTINCT farm.origin FROM farm JOIN bean ON farm.farm_id = bean.farm_id JOIN blend ON bean.bean_id = blend.bean_id ORDER BY farm.origin";
				$navResults = $connect->query($navQuery);
				if (!$navResults) { die($connect->error); }
				while ($row = $navResults->fetch_assoc()) {
					array_push($navItem, $row['origin']);
					echo "<li class='originNav'>".$row['origin']."</li>";
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
				<col style='width:18%'>
				<col style='width:5%'>
				<col style='width:15%'>
				<col style='width:11%'>
				<col style='width:7%'>
				<col style='width:8%'>
			</colgroup>
			<thead>
				<tr>
					<th class='country'>Origin</th>
					<th class='coffee'>Coffee Name</th>
					<th class='roaster'>Roaster Name</th>
					<th class='farm'>Farm Name</th>
					<th class='species'>Species</th>
					<th class='varietals'>Varietal</th>
					<th class='processing'>Processing</th>
					<th class='blend'>Blend Type</th>
					<th class='roast'>Roast Profile</th>
				</tr>
			</thead>

				<?php
					foreach ($navItem as $originCountry) {
						$originCountryId = str_replace(' ', '', $originCountry);
						echo "<tbody id='$originCountryId' hidden>";
						$originQuery = "$browseSelect $tableJoin WHERE farm.origin = '$originCountry' ORDER BY coffee.coffee_id";
						$originResults = $connect->query($originQuery);
						if (!$originResults) die($connect->error);
						$coffeeData = array('id' => 0);
						while ($row = $originResults->fetch_assoc()) {
							if ($coffeeData['id'] != $row['coffee_id']) {
								// if not the first $row entry then print the existing $coffeeData before overwriting
								if ($coffeeData['id'] != 0) { printOriginRow($coffeeData); }
								// initialize $coffeeData
								$coffeeData['id'] = $row['coffee_id'];
								$coffeeData['coffee'] = $row['coffee_name'];
								$coffeeData['farm'] = array($row['farm_name']);
								$coffeeData['roaster'] = $row['roaster_name'];
								$coffeeData['country'] = $row['origin'];
								$coffeeData['varietal'] = array($row['varietal']);
								$coffeeData['species'] = $row['species'];
								$coffeeData['blend'] = $row['type'];
								$coffeeData['processing'] = array($row['processing']);
								$coffeeData['roast'] = $row['roast_profile'];
							}
							// if additional bean entry for same coffee
							else {
								array_push($coffeeData['farm'], $row['farm_name']);
								array_push($coffeeData['varietal'], $row['varietal']);
								array_push($coffeeData['processing'], $row['processing']);
							}
						}
						// print last $coffeeData / $row item
						printOriginRow($coffeeData);

						$originResults->close();
						echo "</tbody>";
					}
				?>
				
		</table>
	</td>
</tr>

<?php
	$connect->close();

	function printOriginRow($coffeeData) {
		echo "<tr href='".$coffeeData['id']."'><td class='country'>".$coffeeData['country']."</td><td class='coffee'>".$coffeeData['coffee']."</td><td class='roaster'>".$coffeeData['roaster']."</td><td class='farm'>";
		printArrBr(array_unique($coffeeData['farm']));
		echo "</td><td class='species'>".$coffeeData['species']."</td><td class='varietals'>";
		printArr(array_unique($coffeeData['varietal']));
		echo "</td><td class='processing'>";
		printArr(array_unique($coffeeData['processing']));
		echo "</td><td class='blend'>".$coffeeData['blend']."</td><td class='roast'>".$coffeeData['roast']."</td></tr>";
	}
?>

<script>
	$(function() {
		$("tbody[hidden]:first").toggle();
		$(".browseToggles li:first").css('background-color', '#9C958F');
	});
	
	$(".originNav").click(function() {
		var tbodyId = $(this).text().replace(/ /g, "");
		$('#'+tbodyId).toggle();
		if ($(this).css('background-color') == 'rgb(156, 149, 143)') {
			$(this).css('background-color', '#CED4D7');
		}
		else {
			$(this).css('background-color', '#9C958F');
		}

		$('tbody[style="display: table-row-group;"] tr:even').css('background-color', '#FFFFFF');
		$('tbody[style="display: table-row-group;"] tr:odd').css('background-color', '#E8EEF1');

		$('tbody[style="display: table-row-group;"] tr:even').hover(function() { $(this).css('background-color', '#CFC8C2'); }, 
			function() { $(this).css('background-color', '#FFFFFF'); });
		$('tbody[style="display: table-row-group;"] tr:odd').hover(function() { $(this).css('background-color', '#CFC8C2'); }, 
			function() { $(this).css('background-color', '#E8EEF1'); });
	});

	$(".tableHolder tbody tr").click(function() {
		window.location.href = './coffee.php?id=' + $(this).attr('href');
	});
</script>