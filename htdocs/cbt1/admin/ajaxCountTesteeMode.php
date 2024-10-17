<?php
	
	require_once('../koneksi_db.php');

	//lihat shift aktif
	$ambilShift = mysqli_query($con, "SELECT shift, aktifFlag FROM shifttes WHERE aktifFlag = 1");
	$dapatShift = mysqli_fetch_array($ambilShift);
	$shiftKe = $dapatShift['shift'];

	//tampilan peserta tes
	$seeWho = $_POST['tampilanPeserta'];

	if ($seeWho == 'login') {
		$cekLogin = "SELECT id FROM absensiharitespeserta WHERE loginFlag = 1"; }

	else if ($seeWho == 'acgrup')
	{ $cekLogin = "SELECT id FROM absensiharitespeserta WHERE shiftTes = '$shiftKe'"; }

	else if ($seeWho == 'allgrup')
	{ $cekLogin = "SELECT id FROM absensiharitespeserta"; }

	$hasilCek = mysqli_query($con, $cekLogin);
	$totalSiswa = mysqli_num_rows($hasilCek);

	echo $totalSiswa;

?>