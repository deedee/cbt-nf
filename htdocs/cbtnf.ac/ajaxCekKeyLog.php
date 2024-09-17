<?php
	require_once('koneksi_db.php');
	
	$noP1 = $_POST['n1'];
	$noP2 = $_POST['n2'];
	
	$JsonArray = array();

	$lihatKeyLog = mysqli_query($con, "SELECT loginKey FROM absensiharitespeserta WHERE (nomorPeserta='$noP1' OR nomorPeserta='$noP2') AND loginKey = '1'");
	while ($getLogKey = mysqli_fetch_array($lihatKeyLog))
	{ echo "oklogkey"; }
?>