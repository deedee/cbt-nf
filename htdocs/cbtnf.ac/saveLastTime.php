<?php

	require_once('koneksi_db.php');

	date_default_timezone_set('Asia/Jakarta'); 		//set timezone

	$nThn = date("Y")*10000;
	$nBln = date("m")*100;
	$nTgl = date("d");
	$nKal = ($nThn+$nBln+$nTgl)*1000000;

	$nHour = date("G");
	$nMin = date("i");
	$nSec = date("s");
	$cTime = $nKal+$nHour*3600 + $nMin*60 + $nSec;		//untuk memindai mana peserta yg dicurigai keluar dari halaman test, tapi gak login2 lagi

	$JsonArray = array();

	$haKe = $_POST['rd'];
	$noInd = $_POST['ni'];
	$noPes = $_POST['np'];
	$saTi = $_POST['ti'];

	$cekAbsen = mysqli_query($con, "SELECT msgx_n, msgx, finishIt, setTimer, curTimer, hariKe$haKe FROM absensiharitespeserta WHERE nomorInduk='$noInd' AND nomorPeserta='$noPes' LIMIT 1");
	$hasilCekAbsen = mysqli_fetch_array($cekAbsen);

	if (!is_null($hasilCekAbsen)) {
		//simpan hanya jika si peserta belum selesai ...
		if ($hasilCekAbsen['hariKe'.$haKe] == 0) {
			mysqli_query($con, "UPDATE aruntimer SET runTime$haKe = $saTi WHERE nomorInduk='$noInd' AND nomorPeserta='$noPes' LIMIT 1");
			mysqli_query($con, "UPDATE absensiharitespeserta SET loginKey=0, curTimer = $cTime WHERE nomorInduk='$noInd' AND nomorPeserta='$noPes' LIMIT 1");
		}

		$jmlmsgx_n = $hasilCekAbsen['msgx_n'];

		$JsonArray[0] = $hasilCekAbsen['msgx'];
		$JsonArray[1] = $hasilCekAbsen['finishIt'];
		$JsonArray[2] = $hasilCekAbsen['setTimer'];

		if ($JsonArray[0]!='') {
			$jmlmsgx_n++;
			mysqli_query($con,"UPDATE absensiharitespeserta SET msgx_n=$jmlmsgx_n, msgx='' WHERE nomorInduk = '$noInd' AND nomorPeserta = '$noPes' LIMIT 1");
		}

		if ($JsonArray[2]>0) {
			mysqli_query($con,"UPDATE absensiharitespeserta SET setTimer=0  WHERE nomorInduk='$noInd' AND nomorPeserta='$noPes' LIMIT 1");
		}
	}

	echo json_encode($JsonArray);

?>