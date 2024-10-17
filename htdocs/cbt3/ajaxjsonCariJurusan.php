<?php

	require_once('koneksi_db.php');
	$adaKode = $_POST['kA'];

	$JsonArray = array();

	$lihatProdi = mysqli_query($con,"SELECT kelompok, namaptn, namaprodi FROM kodejurusan WHERE kodeprodi REGEXP '$adaKode' LIMIT 1");
	if ($lihatProdi) {
		$dptProdi = mysqli_fetch_array($lihatProdi);
		$JsonArray[0] = $dptProdi['kelompok'];
		$JsonArray[1] = $dptProdi['namaptn'];
		$JsonArray[2] = $dptProdi['namaprodi'];
	}

	echo json_encode($JsonArray);

?>