<?php

	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	require_once('koneksi_db.php');

	date_default_timezone_set('Asia/Jakarta'); 		//set timezone

	$numThn = date("Y")*10000;
	$numBln = date("m")*100;
	$numTgl = date("d");
	$numKal = ($numThn+$numBln+$numTgl)*1000000;

	$numHour = date("G");
	$numMin = date("i");
	$numSec = date("s");
	$nowTimer = $numKal+$numHour*3600 + $numMin*60 + $numSec;

	//lihat shift aktif
	$ambilShift = mysqli_query($con, "SELECT shift, aktifFlag FROM shifttes WHERE aktifFlag = 1");
	$dapatShift = mysqli_fetch_array($ambilShift);
	$shiftKe = $dapatShift['shift'];

	//tampilan peserta tes
	$seeWho = $_POST['tampilanPeserta'];
	$seeMode = $_POST['sortMode'];
	$vwTestee = $_POST['vT'];
	$pgViewNow = $_POST['vPN'];
	$offSet = ($pgViewNow-1)*$vwTestee;


	if ($seeMode == 'nomorUrut') { $orderby = 'id'; }
	else if ($seeMode == 'namaUrut') { $orderby = 'nama'; }

	if ($seeWho == 'login') {
		$cekLoginID = "SELECT id FROM absensiharitespeserta WHERE loginFlag = 1 ORDER BY loginError DESC, $orderby ASC LIMIT $offSet, $vwTestee";
	}
	else if ($seeWho == 'acgrup') {			//acgrup = active grup
		$cekLoginID = "SELECT id FROM absensiharitespeserta WHERE shiftTes = '$shiftKe' ORDER BY loginError DESC, $orderby ASC LIMIT $offSet, $vwTestee";
	}
	else if ($seeWho == 'allgrup') {
		$cekLoginID = "SELECT id FROM absensiharitespeserta ORDER BY loginError DESC, $orderby ASC LIMIT $offSet, $vwTestee";
	}

	// lama waktu (dlm detik) seorang peserta dianggap keluar / tdk lagi membuka halaman tes
	// variabel idle ini juga ada di file index.php
	$idle = 75;

	$hasilCekID = mysqli_query($con, $cekLoginID);
	if ($hasilCekID) {
		while ($dataCekID = mysqli_fetch_array($hasilCekID)) {
			$dataID = $dataCekID['id'];
			if ($seeWho == 'login') {
				$cekLogin = mysqli_query($con, "SELECT ipRemote, nomorInduk, nomorPeserta, petaSoal, nama, kelas, jurusan, nomorHP, alamatEmail, shiftTes, loginFlag, loginKey, kodeTO, TOke, curTimer, loginError, TOFinish, msgx_n FROM absensiharitespeserta WHERE id='$dataID' LIMIT 1");
			}
			else if ($seeWho == 'acgrup') {			//acgrup = active grup
				$cekLogin = mysqli_query($con, "SELECT ipRemote, nomorInduk, nomorPeserta, petaSoal, nama, kelas, jurusan, nomorHP, alamatEmail, shiftTes, loginFlag, loginKey, kodeTO, TOke, curTimer, loginError, TOFinish, msgx_n FROM absensiharitespeserta WHERE id='$dataID' LIMIT 1");
			}
			else if ($seeWho == 'allgrup') {
				$cekLogin = mysqli_query($con, "SELECT ipRemote, nomorInduk, nomorPeserta, petaSoal, nama, kelas, jurusan, nomorHP, alamatEmail, shiftTes, loginFlag, loginKey, kodeTO, TOke, curTimer, loginError, TOFinish, msgx_n FROM absensiharitespeserta WHERE id='$dataID' LIMIT 1");
			}


			$dataLogin = mysqli_fetch_array($cekLogin);
			$uto = 0;
			$tempo = 0;
			$proses = 0;
			$progress = '';
			$tko = '';
			$wrno = '#B0B0B0';

			$nis = $dataLogin['nomorInduk'];
			$nop = $dataLogin['nomorPeserta'];
			$sekian = $dataLogin['TOke'];
			$TOsudah = $dataLogin['TOFinish'];
			$ipr = $dataLogin['ipRemote'];
			$nps = $dataLogin['petaSoal'];
			$nam = $dataLogin['nama'];
			$kls = $dataLogin['kelas'];
			$sts = $dataLogin['shiftTes'];
			$lky = $dataLogin['loginKey'];
			$kto = $dataLogin['kodeTO'];
			$jur = $dataLogin['jurusan'];		//maksudnya IPA, IPS, atau IPC

			$nhp = $dataLogin['nomorHP'];
			if ($nhp!='') { $nomhp = ' +'.$nhp; } else { $nomhp=''; }

			$aem = $dataLogin['alamatEmail'];
			$msg_n = $dataLogin['msgx_n'];
			$logF = $dataLogin['loginFlag'];

			$logE = $dataLogin['loginError'];
			
			$tTimer = $dataLogin['curTimer'];

			if ($dataID<10)
			{ $dataID = '000'.$dataID; }
			else if ($dataID<100)
			{ $dataID = '00'.$dataID; }
			else if ($dataID<1000)
			{ $dataID = '0'.$dataID; }
			
			if ($sekian>0) {
				$iData = mysqli_query($con, "SELECT acakSoal$sekian, lastNum$sekian, tmpAnswer$sekian, hariKe$sekian FROM absensiharitespeserta WHERE nomorInduk='$nis' AND nomorPeserta='$nop' LIMIT 1");
				$indexedData = mysqli_fetch_array($iData);

				$acSoal = $indexedData['acakSoal'.$sekian];
				$totnJwb = substr_count($acSoal, ",")+1;
				$lNum = $indexedData['lastNum'.$sekian];
				$jwb = $indexedData['tmpAnswer'.$sekian];
				$uto = $indexedData['hariKe'.$sekian];

				if ($uto == 0 && $TOsudah == 0) {
					//jika belum atau sedang login ujian
					$tko = $kto.' ';
				}

				if ($logF == 1) {
					$cekTime = mysqli_query($con, "SELECT runTime$sekian FROM aruntimer WHERE nomorInduk='$nis' AND nomorPeserta='$nop' LIMIT 1");
					$dataTime = mysqli_fetch_array($cekTime);
					$tempo = $dataTime['runTime'.$sekian];
					$hms = gmdate("H:i:s", $tempo);

					$dTimer = $nowTimer-$tTimer;

					if ($dTimer>$idle) {		//penghitung lama tdk masuk halaman tes, lebih dari waktu idle, siswanya ke mana ??
						$fText='&nbsp; &nbsp;<span style="color:black">&#8986; !?</span>';
						// sekalian set logkeynya biar bs langsung login lg - auto idle-logkey
						mysqli_query($con, "UPDATE absensiharitespeserta SET loginKey='1' WHERE nomorInduk='$nis' AND nomorPeserta='$nop' LIMIT 1");
					}
					else
					{ $fText=''; }

					$wrno = '#419F62';
					$proses = $totnJwb - substr_count($jwb," ") + 1;
					if ($proses > $totnJwb) { $proses = 0; }
					$progress = "<b> [ $proses/$totnJwb ] @ $hms"." &#10151; ".$lNum." ".$fText."</b>";

					if ($proses == $totnJwb)
					{ $wrno = 'orange'; }
				}
			}
			else if ($TOsudah!='0') {
				$hasilBS = mysqli_query($con, "SELECT DISTINCT bidStudiTabel FROM naskahsoal WHERE (kelompok = '$jur' OR kelompok = 'IPAIPS') AND indexBidStudi = $TOsudah");
				$dataBS = mysqli_fetch_array($hasilBS);
				if ($dataBS) {
					$dapatBS = str_replace("+", ", ", $dataBS['bidStudiTabel']);
					$progress = "&#10004; <b>$dapatBS</b>";
					$wrno = 'blue'; 
				}
			}

			$kar = '';
			if ($logE == 1) { $kar = 'x? '; $wrno = '#FF0000';}
			if ($lky == '1') { $kar = '# '; }
			
			echo "<tr>
					  <td width='470'><span title='klik kanan untuk langsung me-logKey peserta ini' class='nmPesertaTes' tagnoid='$dataID' tagps='$nps' tagkls='$kls' tagjur='$jur' tagshift='$sts' tagni='$nis' tagnp='$nop' tagnohp='$nomhp' tagem='$aem' tagmx='$msg_n' style='cursor:pointer; font-size:13px; color:$wrno;' oncontextmenu='return false;'>
					  $dataID. $kar<b>$jur</b> $ipr [$nps] <b>$nam</b> ($nis  $nop<b><i>$nomhp</i></b>), grup:$sts </span></td>
					  <td width='170'><span style='font-size:13px; color:$wrno;'; >$tko$progress</span></td>
				  </tr>
				  
				  <tr> <td colspan='2'> <hr style='border-top: 1px dashed #808080; border-bottom: 0px; margin:4px 25px 4px 0px; '> </td> </tr>";
		}
	}

?>