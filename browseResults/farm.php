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
				$navQuery = "SELECT DISTINCT farm.farm_name FROM farm JOIN bean ON farm.farm_id = bean.farm_id JOIN blend ON bean.bean_id = blend.bean_id ORDER BY farm.farm_name";
				$navResults = $connect->query($navQuery);
				if (!$navResults) { die($connect->error); }
				while ($row = $navResults->fetch_assoc()) {
					array_push($navItem, $row['farm_name']);
					echo "<li class='farmNav'>".$row['farm_name']."</li>";
				}
				$navResults->close();
			?>
		</ul>
	</td>

	<td class='tableHolder'>
		<table>
			<colgroup>
				<col style='width:16%'>
				<col style='width:16%'>
				<col style='width:13%'>
				<col style='width:8%'>
				<col style='width:5%'>
				<col style='width:16%'>
				<col style='width:7%'>
				<col style='width:12%'>
				<col style='width:8%'>
			</colgroup>
			<thead>
				<tr>
					<th class='farm'>Farm Name</th>
					<th class='coffee'>Coffee Name</th>
					<th class='roaster'>Roaster Name</th>
					<th class='country'>Origin</th>
					<th class='species'>Species</th>
					<th class='varietal'>Varietal</th>
					<th class='altitude'>Altitude (masl)</th>
					<th class='processing'>Processing</th>
					<th class='roast'>Roast Profile</th>
				</tr>
			</thead>

				<?php
					foreach ($navItem as $farmName) {
						$farmNameId = str_replace(' ', '', $farmName);
						echo "<tbody id='$farmNameId' hidden>";
						$farmQuery = "$browseSelect $tableJoin WHERE farm.farm_name = '$farmName' ORDER BY coffee.coffee_id";
						$farmResults = $connect->query($farmQuery);
						if (!$farmResults) die($connect->error);
						$coffeeData = array('id' => 0);
						while ($row = $farmResults->fetch_assoc()) {
							if ($coffeeData['id'] != $row['coffee_id']) {
								// if not the first $row entry then print the existing $coffeeData before overwriting
								if ($coffeeData['id'] != 0) {
									printFarmRow($coffeeData);
								}
								// initialize $coffeeData
								$coffeeData['id'] = $row['coffee_id'];
								$coffeeData['coffee'] = $row['coffee_name'];
								$coffeeData['farm'] = $row['farm_name'];
								$coffeeData['roaster'] = $row['roaster_name'];
								$coffeeData['country'] = $row['origin'];
								$coffeeData['varietal'] = array($row['varietal']);
								$coffeeData['species'] = $row['species'];
								$coffeeData['altitude'] = array($row['altitude']);
								$coffeeData['processing'] = array($row['processing']);
								$coffeeData['roast'] = $row['roast_profile'];
							}
							// if additional bean entry for same coffee
							else {
								array_push($coffeeData['varietal'], $row['varietal']);
								array_push($coffeeData['processing'], $row['processing']);
								array_push($coffeeData['altitude'], $row['altitude']);
							}
						}
						// print last $coffeeData / $row item
						printFarmRow($coffeeData);

						$farmResults->close();
						echo "</tbody>";
					}
				?>
				
		</table>
	</td>
</tr>

<?php
	$connect->close();

	function printFarmRow($coffeeData) {
		echo "<tr href='".$coffeeData['id']."'><td class='farm'>".$coffeeData['farm']."</td><td class='coffee'>".$coffeeData['coffee']."</td><td class='roaster'>".$coffeeData['roaster']."</td><td class='country'>".$coffeeData['country']."</td><td class='species'>".$coffeeData['species']."</td><td class='varietal'>";
		printArr(array_unique($coffeeData['varietal']));
		echo "</td><td class='altitude'>".valRange($coffeeData['altitude'])."</td><td class='processing'>";
		printArr(array_unique($coffeeData['processing']));
		echo "</td><td class='roast'>".$coffeeData['roast']."</td></tr>";
	}
?>

<script>
	$(function() {
		$("tbody[hidden]:first").toggle();
		$(".browseToggles li:first").css('background-color', '#9C958F');
	});
	
	$(".farmNav").click(function() {
		var tbodyId = $(this).text().replace(/ /g, "");
		$('#'+tbodyId).toggle();
		if ($(this).css('background-color') == 'rgb(156, 149, 143)') {
			$(this).css('background-color', '#CED4D7');
		}
		else {
			$(this).css('background-color', '#9C958F');
		}
	});

	$(".tableHolder tbody tr").click(function() {
		window.location.href = './coffee.php?id=' + $(this).attr('href');
	});
</script>