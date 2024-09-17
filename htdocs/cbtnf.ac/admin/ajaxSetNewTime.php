<?php

	require_once('../koneksi_db.php');

	$nt = $_POST['ticks'];
	$nopeserta = $_POST['vNP'];
	$idBtn = $_POST['idB'];

	$timePart = explode(":", $nt);

	$ho = $timePart[0];
	$cekhosign = substr($ho,0,1);

	$mm = $timePart[1];
	$ss = $timePart[2];

	//periksa dulu di depan $hh apakah ada tanda +/-
	if ($cekhosign=='+' || $cekhosign=='-')
	{ $hh = substr($ho,1); } else { $hh = $ho; }

	$countTimer = $hh*3600 + $mm*60 + $ss;

	//cari tahu skrg sdg TO ke berapa
	$ambilTOke = mysqli_query($con,"SELECT TOke FROM absensiharitespeserta WHERE nomorPeserta = '$nopeserta' LIMIT 1");
	$dapatTOke = mysqli_fetch_array($ambilTOke);
	$TOyangke = $dapatTOke['TOke'];

	//intip dulu timernya berapa skrg ?
	$ambilRunTime = mysqli_query($con,"SELECT runTime$TOyangke FROM aruntimer WHERE nomorPeserta = '$nopeserta' LIMIT 1");
	$dapatRunTime = mysqli_fetch_array($ambilRunTime);
	$RTnya = $dapatRunTime['runTime'.$TOyangke];

	if ($cekhosign=='+')	//kalo ada tanda + di depannya berarti yg dimau tambahan waktu,
	{ $newCountTimer = $RTnya+$countTimer; }
	else if ($cekhosign=='-')	//kalo ada tanda - di depannya berarti yg dimau pengurangan waktu,
	{ $newCountTimer = $RTnya-$countTimer;	}
	else
	{ $newCountTimer = $countTimer; }

	if ($idBtn == 'btnSetNewTimer')
	{ mysqli_query($con, "UPDATE absensiharitespeserta SET setTimer = $newCountTimer  WHERE nomorPeserta = '$nopeserta'"); }
	else if ($idBtn == 'btnSetNewTimerAll')
	{ mysqli_query($con, "UPDATE absensiharitespeserta SET setTimer = $newCountTimer  WHERE loginFlag = '1'"); }

?>