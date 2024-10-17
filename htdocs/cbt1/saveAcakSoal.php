<?php
	require_once('koneksi_db.php');
	
	$haKe = $_POST['hk'];
	$noInd = $_POST['ni'];
	$noPes = $_POST['np'];
	$suSo = $_POST['ss'];
	
	//simpan acakan soal
	$simpanAcakanSoal = "UPDATE absensiharitespeserta SET acakSoal$haKe = '$suSo' WHERE nomorInduk='$noInd' AND nomorPeserta = '$noPes' LIMIT 1";
	mysqli_query($con,$simpanAcakanSoal);

	// tes baca balik utk menjamin berhasil di save
	$isiAcakan = "";
	$cekAcakan = mysqli_query($con, "SELECT acakSoal$haKe FROM absensiharitespeserta WHERE nomorInduk = '$noInd' AND nomorPeserta = '$noPes' LIMIT 1");
	$getAcakan = mysqli_fetch_array($cekAcakan);
	$isiAcakan = $getAcakan['acakSoal'.$haKe];

	echo $isiAcakan;
?>