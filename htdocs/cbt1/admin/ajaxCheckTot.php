<?php

	require_once('../koneksi_db.php');

	//Hitung total siswa
	$nSiswa = mysqli_query($con,"SELECT nama FROM absensiharitespeserta WHERE nama!=''");
	$totSiswa = mysqli_num_rows($nSiswa);

	//Hitung total set
	$nSet = mysqli_query($con,"SELECT kodeSoal FROM naskahsoal WHERE kodeSoal!=''");
	$totSet = mysqli_num_rows($nSet);

	//Hitung total paket
	$cF = count(glob("../images/soal/*"));
	$cF--;
	$totPaket = $cF;

	/* RETURN VALUE */
	$hasilTot = array();
	
	$hasilTot[0]=$totSiswa;
	$hasilTot[1]=$totSet;
	$hasilTot[2]=$totPaket;

	echo json_encode($hasilTot);

?>