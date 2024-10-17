<?php

	require_once('../koneksi_db.php');

	if (isset($_POST['NIDTO']))
	{ $IDID = $_POST['NIDTO']; }
	
	if (isset($_POST['NMTO']))
	{ $NMID = $_POST['NMTO']; }	//isi data baru

	if (isset($_POST['NITO']))
	{ $NIID = $_POST['NITO']; }
	if (isset($_POST['lNITO']))
	{ $LNI = $_POST['lNITO']; }	//panjang label ID1

	if (isset($_POST['NPTO']))
	{ $NPID = $_POST['NPTO']; }
	if (isset($_POST['lNPTO']))
	{ $LNP = $_POST['lNPTO']; }	//panjang label ID2

	if (isset($_POST['BSTO']))
	{ $BSID = $_POST['BSTO']; }
	if (isset($_POST['SHTO']))
	{ $SHID = $_POST['SHTO']; }

	$endMsg='';

	//set / reset
	if ($_POST['op']=="btnSaveNamaBaru") {
		if ($NMID!='') {
			mysqli_query($con,"UPDATE absensiharitespeserta SET nama='".mysqli_escape_string($con,strtoupper($NMID))."' WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'");
			mysqli_query($con,"UPDATE absensirecord SET nama='".mysqli_escape_string($con,strtoupper($NMID))."' WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'");
			mysqli_query($con,"UPDATE aruntimer SET nama='".mysqli_escape_string($con,strtoupper($NMID))."' WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'");
			mysqli_query($con,"UPDATE hasilipa SET nama='".mysqli_escape_string($con,strtoupper($NMID))."' WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'");
			mysqli_query($con,"UPDATE hasilips SET nama='".mysqli_escape_string($con,strtoupper($NMID))."' WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'");
		}
	}

	else if ($_POST['op']=="btnSaveNewID1") {
		if ($NMID!='') {
			//cek panjang karakter ID1
			$lenID1 = strlen($NMID);
			if ($lenID1 == $LNI) {
				//cek dulu ada gak ID1 yg sama
				$adaNI = mysqli_query($con, "SELECT id FROM absensiharitespeserta WHERE nomorInduk='$NMID' LIMIT 1");
				$totAdaNI = mysqli_num_rows($adaNI);
				if ($totAdaNI==0) {
					mysqli_query($con,"UPDATE absensiharitespeserta SET nomorInduk='$NMID' WHERE nomorPeserta='$NPID'");
					mysqli_query($con,"UPDATE absensirecord SET nomorInduk='$NMID' WHERE nomorPeserta='$NPID'");
					mysqli_query($con,"UPDATE aruntimer SET nomorInduk='$NMID' WHERE nomorPeserta='$NPID'");
					mysqli_query($con,"UPDATE hasilipa SET nomorInduk='$NMID' WHERE nomorPeserta='$NPID'");
					mysqli_query($con,"UPDATE hasilips SET nomorInduk='$NMID' WHERE nomorPeserta='$NPID'");

					$endMsg='NI OK';
				}
			}

		}
	}

	else if ($_POST['op']=="btnSaveNewID2") {
		if ($NMID!='') {
			//cek panjang karakter ID1
			$lenID2 = strlen($NMID);
			if ($lenID2 == $LNP) {
				//cek dulu ada gak ID2 yg sama
				$adaNP = mysqli_query($con, "SELECT id FROM absensiharitespeserta WHERE nomorPeserta='$NMID' LIMIT 1");
				$totAdaNP = mysqli_num_rows($adaNP);
				if ($totAdaNP==0) {
					mysqli_query($con,"UPDATE absensiharitespeserta SET nomorPeserta='$NMID' WHERE nomorInduk='$NIID'");
					mysqli_query($con,"UPDATE absensirecord SET nomorPeserta='$NMID' WHERE nomorInduk='$NIID'");
					mysqli_query($con,"UPDATE aruntimer SET nomorPeserta='$NMID' WHERE nomorInduk='$NIID'");
					mysqli_query($con,"UPDATE hasilipa SET nomorPeserta='$NMID' WHERE nomorInduk='$NIID'");
					mysqli_query($con,"UPDATE hasilips SET nomorPeserta='$NMID' WHERE nomorInduk='$NIID'");

					$endMsg='NP OK';
				}

			}
		}
	}

	else if ($_POST['op']=="btnGantiShift")
	{ mysqli_query($con,"UPDATE absensiharitespeserta SET shiftTes='$SHID' WHERE id='$IDID'"); }

	else if ($_POST['op']=="btnGantiNoHP")
	{ mysqli_query($con,"UPDATE absensiharitespeserta SET nomorHP='$NMID' WHERE id='$IDID'"); }

	else if ($_POST['op']=="btnGantiEmail")
	{ mysqli_query($con,"UPDATE absensiharitespeserta SET alamatEmail='$NMID' WHERE id='$IDID'"); }

	else if ($_POST['op']=="btnSetLogKey") {
		$cekLogkey = mysqli_query($con, "SELECT loginKey FROM absensiharitespeserta WHERE id='$IDID' LIMIT 1");
		$getKey = mysqli_fetch_array($cekLogkey);
		$logKeyAktif = $getKey['loginKey'];
		if ($logKeyAktif==0)
		{ mysqli_query($con,"UPDATE absensiharitespeserta SET loginKey='1', loginError='0'  WHERE id='$IDID'"); }
		else
		{ mysqli_query($con,"UPDATE absensiharitespeserta SET loginKey='0'  WHERE id='$IDID'"); }
	}

	else if ($_POST['op']=="btnSetLogKeyThisGroup" || $_POST['op']=="btnSetLogKeyAll") {
		date_default_timezone_set('Asia/Jakarta'); 		//set timezone
		$idle = 30; //lama waktu (dlm detik) seorang peserta dianggap keluar / tdk lagi membuka halaman tes, var ini ada juga di script cekAktifitas.php, harus disamakan

		$numThn = date("Y")*10000;
		$numBln = date("m")*100;
		$numTgl = date("d");
		$numKal = ($numThn+$numBln+$numTgl)*1000000;

		$numHour = date("G");
		$numMin = date("i");
		$numSec = date("s");
		$nowTimer = $numKal + $numHour*3600 + $numMin*60 + $numSec;
		$bTimer = $nowTimer - $idle;		//bTimer = barrier timer

		if ($_POST['op']=="btnSetLogKeyThisGroup") {
			$cekShift = mysqli_query($con,"SELECT shift FROM shifttes WHERE aktifFlag='1'");
			$getShift = mysqli_fetch_array($cekShift);
			$shiftAktif = $getShift['shift'];
			if ($shiftAktif!='0')
			{ mysqli_query($con, "UPDATE absensiharitespeserta SET loginKey='1', loginError='0' WHERE shiftTes='$shiftAktif' AND curTimer<=$bTimer"); }
		}
		else
		{ mysqli_query($con, "UPDATE absensiharitespeserta SET loginKey='1', loginError='0' WHERE loginFlag=0 OR curTimer<=$bTimer"); }
	}

	else if ($_POST['op']=="btnReSetLogKeyAll")
	{ mysqli_query($con,"UPDATE absensiharitespeserta SET loginKey='0'"); }

	else if ($_POST['op']=="btnResetBS") {
		//cari peserta tersebut
		$lihKodeTO = "SELECT kodeSoal, indexBidStudi FROM naskahsoal WHERE id='$BSID'";
		$cek_KodeTO = mysqli_query($con,$lihKodeTO);
		$hasilCek_KodeTO = mysqli_fetch_array($cek_KodeTO);
		$TOcode = $hasilCek_KodeTO['kodeSoal'];
		$TOid = $hasilCek_KodeTO['indexBidStudi'];
		mysqli_query($con,"UPDATE absensiharitespeserta SET loginKey=1, kodeTO='$TOcode', TOke='$TOid' WHERE id='$IDID'");
		if ($TOcode=='') { mysqli_query($con,"UPDATE absensiharitespeserta SET kodeTO='', TOke=0 WHERE id='$IDID'"); }
	}

	echo $endMsg;

?>