<?php

	require_once('../koneksi_db.php');
	
	$searchNI = $_POST['iNI'];
	$searchNP = $_POST['iNP'];
	
	$ambilNama = mysqli_query($con,"SELECT nama FROM absensiharitespeserta WHERE nomorInduk='$searchNI' AND nomorPeserta='$searchNP' LIMIT 1");
	$dataNama = mysqli_fetch_array($ambilNama);

	$nam = $dataNama['nama'];
	
	echo $nam;
	
?>