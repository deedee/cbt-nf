<?php
	require_once('../koneksi_db.php');

	$bID = $_POST['idBtn'];
	$idnya = $_POST['toID'];
	$isiPesan = $_POST['txtIsi'];

	if ($bID == 'btnPesanKirim')
	{
		mysqli_query($con,"UPDATE absensiharitespeserta SET msgx = '".mysqli_escape_string($con,nl2br($isiPesan))."' WHERE id=$idnya");
	}
	else if ($bID == 'btnPesanKirimAll')
	{
		mysqli_query($con,"UPDATE absensiharitespeserta SET msgx = '".mysqli_escape_string($con,nl2br($isiPesan))."' WHERE loginFlag=1");
	}
?>