<?php
	$fa = $_POST['caf'];	//file image crop
	$fu = $_POST['cuf'];	//file audio
	
	if (file_exists($fu) && file_exists($fa))
		{ echo '2'; }
	else if (file_exists($fa))
		{ echo '1'; }
	else { echo '0'; }
?>