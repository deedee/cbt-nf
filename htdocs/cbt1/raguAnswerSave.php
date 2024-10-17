<?php

	require_once('koneksi_db.php');
	
	$haKe = $_POST['rd'];
	$noInd = $_POST['ni'];
	$noPes = $_POST['np'];
	$jaRa = $_POST['jx'];

	$simpanJwbRagu = "UPDATE absensiharitespeserta SET ragu$haKe='$jaRa' WHERE nomorInduk='$noInd' AND nomorPeserta='$noPes' LIMIT 1";
	mysqli_query($con,$simpanJwbRagu);
	
?>