<?php
	require_once('../koneksi_db.php');

	$namaA = $_POST['dataName'];
	$nomorA = $_POST['dataNumber'];
	$gWA = $_POST['datagwa'];

	mysqli_query($con,"UPDATE dataadmin SET nama='$namaA', nokontak='$nomorA', grupwa='$gWA' WHERE id=1");
?>