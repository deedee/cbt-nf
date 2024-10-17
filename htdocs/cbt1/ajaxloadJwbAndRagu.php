<?php

	require_once('koneksi_db.php');
	
	$noI = $_POST['nID'];
	$noP = $_POST['nPE'];
	$haK = $_POST['hKe'];
	
	$JsonArray = array();

	$lihatJdR = mysqli_query($con,"SELECT tmpAnswer$haK, ragu$haK FROM absensiharitespeserta WHERE nomorInduk = '$noI' AND nomorPeserta = '$noP' LIMIT 1");
	while ($getJdR = mysqli_fetch_array($lihatJdR)) {
		$JsonArray[0] = $getJdR['tmpAnswer'.$haK];
		$JsonArray[1] = $getJdR['ragu'.$haK];
		
		echo json_encode($JsonArray);
	}
	
?>