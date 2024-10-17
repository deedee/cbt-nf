<?php
	
	require_once('../koneksi_db.php');

	$NoInd = $_POST['no1'];
	$NoPes = $_POST['no2'];
	$logRelog = $_POST['logType'];

	if ($logRelog==1)
	{
		mysqli_query($con,"UPDATE absensiharitespeserta SET loginKey='1', loginError = '0' WHERE nomorInduk='$NoInd' AND nomorPeserta='$NoPes'");
	}
	else if ($logRelog==2)
	{
		mysqli_query($con,"UPDATE absensiharitespeserta SET loginKey='0' WHERE nomorInduk='$NoInd' AND nomorPeserta='$NoPes'");
	}

?>