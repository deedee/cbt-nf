<?php
	require_once('../koneksi_db.php');

	$dPref1 = $_POST['datapref1'];
	$dPref2 = $_POST['datapref2'];
	mysqli_query($con, "UPDATE datasystem SET prefixid1 = '$dPref1', prefixid2 = '$dPref2' WHERE id = 1");
?>