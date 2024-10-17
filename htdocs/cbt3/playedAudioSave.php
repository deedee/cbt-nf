<?php

	require_once('koneksi_db.php');
	
	$noPes = $_POST['np'];
	$ts = $_POST['theSound'];

	if(isset($_POST) && $_POST['op']=='saveAudio') {
		$simpanAudio = "UPDATE absensiharitespeserta SET playedAudio = '$ts' WHERE nomorPeserta = '$noPes' LIMIT 1";
		mysqli_query($con,$simpanAudio);
	}

?>