<?php

	require_once('koneksi_db.php');
	
	$haKe = $_POST['rd'];
	$noInd = $_POST['ni'];
	$noPes = $_POST['np'];
	$laNu = $_POST['nl'];
	$saTi = $_POST['ti'];

	date_default_timezone_set('Asia/Jakarta'); 		//set timezone

	$nThn = date("Y")*10000;
	$nBln = date("m")*100;
	$nTgl = date("d");
	$nKal = ($nThn+$nBln+$nTgl)*1000000;

	$nHour = date("G");
	$nMin = date("i");
	$nSec = date("s");
	$cTime = $nKal+$nHour*3600 + $nMin*60 + $nSec;

	mysqli_query($con, "UPDATE aruntimer SET runTime$haKe = $saTi WHERE nomorInduk='$noInd' AND nomorPeserta='$noPes' LIMIT 1");
	mysqli_query($con, "UPDATE absensiharitespeserta SET loginKey=0, curTimer=$cTime, lastNum$haKe=$laNu WHERE nomorInduk='$noInd' AND nomorPeserta='$noPes' LIMIT 1");

?>