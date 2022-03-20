<?php
	require "config.php";
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<script>
function myFunction(elem) {
	elem.querySelector("i").className = "fas fa-spinner fa-spin";
}
</script>
<?php
	$colors = array(
		"google" => "dd4a38",
		"facebook" => "3b5a9a",
		"instagram" => "d63080",
		"email" => "2ecc71",
	);

	foreach($config as $key => $val){
		$icon = 'fab fa-'.$key;
		if($key == "email")
			$icon = "fas fa-envelope";
		echo '<a onclick="myFunction(this)" href="'.ucwords($key).'.php?reffer='.$_SERVER["HTTP_REFERER"].'" class="login-button" style="background:#'.$colors[$key].';"><i class="'.$icon.'"></i> Join us on '.ucwords($key).'</a>';
	}
?>