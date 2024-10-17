<?php

	require_once('../koneksi_db.php');

	date_default_timezone_set('Asia/Jakarta'); 		//set timezone

	$numThn = date("Y")*10000;
	$numBln = date("m")*100;
	$numTgl = date("d");
	$numKal = ($numThn+$numBln+$numTgl)*1000000;

	$numHour = date("G");
	$numMin = date("i");
	$numSec = date("s");
	$nowTimer = $numKal+$numHour*3600 + $numMin*60 + $numSec;

	$dicari = $_POST['cariIni'];
	if ($dicari=="a" || $dicari=="A")		//all
	{
		$cari = mysqli_query($con,"SELECT nomorInduk, nomorPeserta, nama, loginFlag, loginKey, curTimer, loginError FROM absensiharitespeserta");
		$txtSum = "Jml. Total : ";
	}
	else if ($dicari=="q" || $dicari=="Q")	//login
	{
		$cari = mysqli_query($con,"SELECT nomorInduk, nomorPeserta, nama, loginFlag, loginKey, curTimer, loginError FROM absensiharitespeserta WHERE loginFlag=1");
		$txtSum = "Jml. Login : ";
	}
	else if ($dicari=="z" || $dicari=="Z")	//login Error
	{
		$cari = mysqli_query($con,"SELECT nomorInduk, nomorPeserta, nama, loginFlag, loginKey, curTimer, loginError FROM absensiharitespeserta WHERE loginError=1");
		$txtSum = "Jml. Error : ";
	}
	else
	{
		$cari = mysqli_query($con,"SELECT nomorInduk, nomorPeserta, nama, loginFlag, loginKey, curTimer, loginError FROM absensiharitespeserta WHERE nama REGEXP '$dicari'");
		$txtSum = "Matched : ";
	}

	$idle = 30;	//lama waktu (dlm detik) seorang peserta dianggap keluar / tdk lagi membuka halaman tes

	$numCari = mysqli_num_rows($cari);
	echo "<span style='font-size: 12px'>".$txtSum.$numCari."</span><br><br>";

	while ($dptSiswa = mysqli_fetch_array($cari))
	{
		$nIn = $dptSiswa['nomorInduk'];
		$nPe = $dptSiswa['nomorPeserta'];
		$nam = $dptSiswa['nama'];

		$logFl = $dptSiswa['loginFlag'];
		$logKy = $dptSiswa['loginKey'];
		$logEr = $dptSiswa['loginError'];

		$tTimer = $dptSiswa['curTimer'];
		$dTimer = $nowTimer-$tTimer;

		if($dTimer>$idle)		//penghitung lama tdk masuk halaman tes, lebih dari waktu idle, siswanya ke mana ??
		{ $fText='&nbsp;&nbsp;<span style="color:black">&#8986; !?</span>'; }
		else
		{ $fText=''; }

		if ($logFl=='0')		//belum login sama sekali
		{
			echo "<span style='display:block; margin-bottom:7px; color:darkgrey; cursor:pointer' class='itemNama' nI='$nIn' nP='$nPe'>$nam [$nIn - $nPe]</span>";
		}
		else if ($logKy=='1')
		{
			echo "<span style='display:block; margin-bottom:7px; color:dodgerblue; cursor:pointer' class='itemNama' nI='$nIn' nP='$nPe'>$nam [$nIn - $nPe]$fText</span>";
		}
		else if ($logEr=='1')
		{
			echo "<span style='display:block; margin-bottom:7px; color:red; cursor:pointer' class='itemNama' nI='$nIn' nP='$nPe'>$nam [$nIn - $nPe]$fText</span>";
		}
		else if ($fText!='')
		{
			echo "<span style='display:block; margin-bottom:7px; color:orchid; cursor:pointer' class='itemNama' nI='$nIn' nP='$nPe'>$nam [$nIn - $nPe]$fText</span>";
		}
		else
		{
			echo "<span style='display:block; margin-bottom:7px; color:limegreen; cursor:pointer' class='itemNama' nI='$nIn' nP='$nPe'>$nam [$nIn - $nPe]$fText</span>";
		}
	}

?>
