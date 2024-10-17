<?php
	require_once('../koneksi_db.php');

	$idnya = $_POST['toID'];

	mysqli_query($con,"UPDATE absensiharitespeserta SET finishIt = 1 WHERE id = $idnya");
?>