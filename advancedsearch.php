<?php
	include_once './includes/header.php';
	require_once './includes/include.php';
?>
	<h3>Advanced Search</h3><br/>

	<form action='./search.php' method='POST'>
		<div class='formcol'>
			<label for="coffee_coffee_name">Coffee Name</label>
			<input type="text" name="coffee_coffee_name">
			<label for="farm_farm_name">Farm Name</label>
			<input type="text" name="farm_farm_name">
			<label for="roaster_roaster_name">Roaster Name</label>
			<input type="text" name="roaster_roaster_name">
			<label for="farm_origin">Country of Origin</label>
			<input type="text" name="farm_origin">
			<label for="farm_region">Region</label>
			<input type="text" name="farm_region">
			<label for="blend_type">Blend / Single Origin</label>
			<input type="text" name="blend_type">
		</div>
		<div class='formcol'>
			<label for="bean_varietal">Varietal</label>
			<input type="text" name="bean_varietal">
			<label for="bean_species">Species</label>
			<input type="text" name="bean_species">
			<label for="bean_altitude">Altitude</label>
			<input type="text" name="bean_altitude">
			<label for="bean_harvest">Harvest Date</label>
			<input type="text" name="bean_harvest">
			<label for="bean_processing">Processing Method</label>
			<input type="text" name="bean_processing">
			<label for="bean_q_score">Q Score</label>
			<input type="text" name="bean_q_score">
		</div>
		<div class='formcol'>
			<label for="coffee_roast_profile">Roast Profile</label>
			<input type="text" name="coffee_roast_profile">
			<label for="coffee_tasting_notes">Tasting Notes</label>
			<input type="text" name="coffee_tasting_notes">
			<label for="roaster_country">Roaster Country</label>
			<input type="text" name="roaster_country">
			<label for="roaster_city">Roaster City</label>
			<input type="text" name="roaster_city">
			<label for="roaster_state">Roaster State</label>
			<input type="text" name="roaster_state" maxlength="3">
			<button type="submit"><i class='fa fa-search'> Search</i></button>
		</div>
	</form>
<?php
	include_once './includes/footer.php';
?>

