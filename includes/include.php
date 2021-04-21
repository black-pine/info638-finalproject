<?php
	$hn = 'localhost';
	$db = 'rmatsumo';
	$un = 'coffee_site';
	$pw = 'coffee_access';

	$browseSelect = "SELECT coffee.coffee_name, farm.farm_name, roaster.roaster_name, farm.origin, blend.coffee_id, blend.type, bean.varietal, bean.species, bean.processing, coffee.roast_profile";
	$searchSelect = "SELECT coffee.coffee_name, farm.farm_name, roaster.roaster_name, farm.origin, farm.region, blend.coffee_id, blend.type, bean.varietal, bean.species, bean.altitude, bean.harvest, bean.processing, bean.q_score, coffee.roast_profile, coffee.tasting_notes, roaster.country, roaster.city, roaster.state";
	$tableJoin = "FROM farm JOIN bean ON farm.farm_id = bean.farm_id JOIN blend ON bean.bean_id = blend.bean_id JOIN coffee ON blend.coffee_id = coffee.coffee_id JOIN roaster ON coffee.roaster_id = roaster.roaster_id";

	function sanitizeString($var) {
		$var = stripslashes($var);
		$var = strip_tags($var);
		$var = htmlentities($var);
		return $var;
	}
	function sanitizeSQL($connection, $var) {
		$var = sanitizeString($var);
		$var = $connection->real_escape_string($var);
		return $var;
	}
	function printArr($arr) {
		foreach ($arr as $k => $val) {
			echo $val;
			if ($k != array_key_last($arr)) { echo ', '; }
		}
	}
	function printArrBr($arr) {
		foreach ($arr as $k => $val) {
			echo $val;
			if ($k != array_key_last($arr)) { echo '<br/><br/>'; }
		}
	}

	function valRange($arr) {
		if (min($arr) == max($arr)) { return min($arr); }
		else { return min($arr).' - '.max($arr); }
	}
?>
