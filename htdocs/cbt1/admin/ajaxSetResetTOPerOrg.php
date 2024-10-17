<?php
	
	require_once('../koneksi_db.php');

	$pesNo = $_POST['npPesertanya'];
	$hrID = $_POST['idTO'];
	$TOyangKe = substr($hrID, -1);
	if ($TOyangKe==0) { $TOyangKe=10; }
	$kondTO = $_POST['nowStat'];

	if ($kondTO=='1') {
		mysqli_query($con, "UPDATE absensiharitespeserta SET hariKe$TOyangKe='0' WHERE nomorPeserta='$pesNo'");
		$kondTONow = '0';
	}
	else {
		mysqli_query($con, "UPDATE absensiharitespeserta SET hariKe$TOyangKe='1' WHERE nomorPeserta='$pesNo'");
		$kondTONow = '1';
	}

	mysqli_query($con, "UPDATE aruntimer SET runTime$TOyangKe='0' WHERE nomorPeserta='$pesNo'");

	echo $kondTONow;
?>