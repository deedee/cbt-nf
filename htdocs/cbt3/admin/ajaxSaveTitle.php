<?php
	require_once('../koneksi_db.php');

	$dt = $_POST['tdata'];

	mysqli_query($con,"UPDATE datasystem SET bigTitle = '$dt'  WHERE id = 1");
?>