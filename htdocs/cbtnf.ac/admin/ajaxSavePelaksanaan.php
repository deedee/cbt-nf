<?php
	require_once('../koneksi_db.php');

	$dl = $_POST['dataLak'];

	if ($dl!="")
	{ mysqli_query($con,"UPDATE datasystem SET teksPelaksanaan = '$dl'  WHERE id = 1"); }
?>