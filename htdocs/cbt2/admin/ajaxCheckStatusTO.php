<?php
	
	require_once('../koneksi_db.php');
	
	$noI = $_POST['noinduknya'];
	$noP = $_POST['nopesertanya'];
	
	$arrayStatus = array();
	
	$lihatStatusTO = mysqli_query($con,"SELECT acakSoal1, tmpAnswer1, hariKe1,
												acakSoal2, tmpAnswer2, hariKe2,
												acakSoal3, tmpAnswer3, hariKe3,
												acakSoal4, tmpAnswer4, hariKe4,
												acakSoal5, tmpAnswer5, hariKe5,
												acakSoal6, tmpAnswer6, hariKe6,
												acakSoal7, tmpAnswer7, hariKe7,
												acakSoal8, tmpAnswer8, hariKe8,
												acakSoal9, tmpAnswer9, hariKe9,
												acakSoal10, tmpAnswer10, hariKe10 FROM absensiharitespeserta WHERE nomorInduk='$noI' AND nomorPeserta='$noP' LIMIT 1");

	$getStatusTO = mysqli_fetch_array($lihatStatusTO);
	
	$arrayStatus[0] = $getStatusTO['acakSoal1'];
	$arrayStatus[1] = $getStatusTO['tmpAnswer1'];
	$arrayStatus[2] = $getStatusTO['hariKe1'];
	$arrayStatus[3] = $getStatusTO['acakSoal2'];
	$arrayStatus[4] = $getStatusTO['tmpAnswer2'];
	$arrayStatus[5] = $getStatusTO['hariKe2'];
	$arrayStatus[6] = $getStatusTO['acakSoal3'];
	$arrayStatus[7] = $getStatusTO['tmpAnswer3'];
	$arrayStatus[8] = $getStatusTO['hariKe3'];
	$arrayStatus[9] = $getStatusTO['acakSoal4'];
	$arrayStatus[10] = $getStatusTO['tmpAnswer4'];
	$arrayStatus[11] = $getStatusTO['hariKe4'];
	$arrayStatus[12] = $getStatusTO['acakSoal5'];
	$arrayStatus[13] = $getStatusTO['tmpAnswer5'];
	$arrayStatus[14] = $getStatusTO['hariKe5'];
	$arrayStatus[15] = $getStatusTO['acakSoal6'];
	$arrayStatus[16] = $getStatusTO['tmpAnswer6'];
	$arrayStatus[17] = $getStatusTO['hariKe6'];
	$arrayStatus[18] = $getStatusTO['acakSoal7'];
	$arrayStatus[19] = $getStatusTO['tmpAnswer7'];
	$arrayStatus[20] = $getStatusTO['hariKe7'];
	$arrayStatus[21] = $getStatusTO['acakSoal8'];
	$arrayStatus[22] = $getStatusTO['tmpAnswer8'];
	$arrayStatus[23] = $getStatusTO['hariKe8'];
	$arrayStatus[24] = $getStatusTO['acakSoal9'];
	$arrayStatus[25] = $getStatusTO['tmpAnswer9'];
	$arrayStatus[26] = $getStatusTO['hariKe9'];
	$arrayStatus[27] = $getStatusTO['acakSoal10'];
	$arrayStatus[28] = $getStatusTO['tmpAnswer10'];
	$arrayStatus[29] = $getStatusTO['hariKe10'];
	$arrayStatus[30] = $noP;

	echo json_encode($arrayStatus);
	
?>