<?php
	require_once('../koneksi_db.php');

	$pesNo = $_POST['npPesertanya'];
	$hrID = $_POST['idTO'];
	$TOyangKe = substr($hrID, -1);
	if ($TOyangKe==0) { $TOyangKe=10; }

	mysqli_query($con,"UPDATE absensiharitespeserta SET acakSoal$TOyangKe='', lastNum$TOyangKe='1', tmpAnswer$TOyangKe='', ragu$TOyangKe='' WHERE nomorPeserta='$pesNo'");
?>