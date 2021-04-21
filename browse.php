<?php
	include_once './includes/header.php';
	require_once './includes/include.php';
?>

<h3>Browse Coffees</h3>
<ul>
	<li><a class='browseItems' id='origin' href='javascript:;'>by Country of Origin</a></li>
	<li><a class='browseItems' id='roaster' href='javascript:;'>by Roaster</a></li>
	<li><a class='browseItems' id='species' href='javascript:;'>by Species</a></li>
	<li><a class='browseItems' id='roast' href='javascript:;'>by Roast Profile</a></li>
</ul>
<table id='browseTable'>
</table>

<?php
	include_once './includes/footer.php';
?>

<script>
	$(function() {
		// Grab URL info to set browse type
		var path = window.location.href;
		var paramIndex = path.lastIndexOf('?');
		// If no browse type requested, set to the first option
		if (paramIndex == -1) {
			var url = "./browseResults/" + $('#content a:first').attr('id') + ".php";
			$('#browseTable').load(url);
			$('#browseTable').addClass($('#content a:first').attr('id') + 'active');
			$('#content a:first').css('text-decoration', 'underline wavy');
		}
		else {
			var pathId = path.substring(paramIndex+1);
			var url = "./browseResults/" + pathId + ".php";
			$('#browseTable').load(url);
			$('#browseTable').addClass(pathId + 'active');
			$('#'+pathId).css('text-decoration', 'underline wavy');
		}
	});

	$(".browseItems").click(function() {
		// Check the click is not on the already active page
		if ($("#browseTable").attr('class') != $(this).attr('id') + 'active') {
			var url = "./browseResults/" + $(this).attr('id') + ".php";
			var oldId = $('#browseTable').attr('class').substring(0, $('#browseTable').attr('class').indexOf('active'));
			// Empty out the current content
			$('#browseTable').empty();
			$('#browseTable').removeClass();
			// Load new page content
			$('#browseTable').load(url);
			$('#browseTable').addClass($(this).attr('id') + 'active');
			// Style the active link
			$('#'+oldId).css('text-decoration', '');
			$(this).css('text-decoration', 'underline wavy');
		}
	});
</script>