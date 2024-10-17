<?php

	session_start();
		// $_SESSION['currentNIN'] = "";
		// $_SESSION['currentNOP'] = "";
	session_destroy();

	date_default_timezone_set('Asia/Jakarta'); 		//set timezone
	$dateToday = date("Y")."-".date("m")."-".date("d");

	require_once('koneksi_db.php');

	if (isset($_POST['kodSoa'])) { $soalKode = $_POST['kodSoa']; } else { $soalKode = ''; }
	if ($soalKode=='') {
		echo ('<script type="text/javascript">');
		echo ('window.location="index.php";');
		echo ('</script>');
		exit();
	}

	$iniHari = $_POST['harIn'];
	$keHari = $_POST['harK'];
	$soalPeta = $_POST['petSoa'];
	$studiBidangNama = $_POST['namBiStud'];
	$nItem = $_POST['iteNu'];
	$soalAcak = $_POST['soalAcakan'];
	$butirIsian = $_POST['butirJwb'];
	$untaiJwb = substr($butirIsian,2);
	$dataNama = $_POST['nam'];
	$dataKelas = $_POST['kela'];
	$dataNoInduk = $_POST['nomoIndu'];
	$dataNoPeserta = $_POST['nomoPesert'];
	$dataProgram = $_POST['progra'];			// jurusan siswa: IPA, IPS atau IPC

	$ketHasil = "";


	//pilih dan ambil kunci jawaban
	// $pilihSoal = "SELECT kelompok, kodeSoal, bidStudiTabel, nomorAwalPerBS, kunciJawaban FROM naskahsoal WHERE kodeSoal='$soalKode' LIMIT 1";
	// $ambilKunci = mysqli_query($con, $pilihSoal);
	// $dapatKunci = mysqli_fetch_array($ambilKunci);

	$stmt = mysqli_prepare($con, "SELECT kelompok, kodeSoal, bidStudiTabel, nomorAwalPerBS, kunciJawaban FROM naskahsoal WHERE kodeSoal=? LIMIT 1");
	mysqli_stmt_bind_param($stmt, "s", $soalKode);
	mysqli_stmt_execute($stmt);
	$ambilKunci = mysqli_stmt_get_result($stmt);
	$dapatKunci = mysqli_fetch_array($ambilKunci);
	mysqli_stmt_close($stmt);
	
	$klpSoal = $dapatKunci['kelompok'];
	$tabelBidStudi = $dapatKunci['bidStudiTabel'];
	$nBidStudi = substr_count($tabelBidStudi, "+");
	$nBidStudi++;
	$noAwalBS = $dapatKunci['nomorAwalPerBS'];
	$answerK = $dapatKunci['kunciJawaban'];
	$answerKey = str_replace("...","",$answerK);	//utk mengoreksi jawaban soal essay


	//ambil sistem scoringnya
	$pilihDataSystem = "SELECT tampilSkor, sB, sS, sK, skala FROM datasystem WHERE id=1";
	$ambilDataSystem = mysqli_query($con, $pilihDataSystem);
	$dapatDataSystem = mysqli_fetch_array($ambilDataSystem);

	$showScore = $dapatDataSystem['tampilSkor'];
	$scB = $dapatDataSystem['sB'];
	$scS = $dapatDataSystem['sS'];
	$scK = $dapatDataSystem['sK'];
	$sekala = $dapatDataSystem['skala'];

	$kunciKe = explode(",", $answerKey);
	$numberItem = count($kunciKe);			//banyak butir soal total semua bid studi jg bisa diitung dari array $kunciKe;
	$soalTeracak = explode(",", $soalAcak);

	$jwbKe = explode("|", $untaiJwb);
	$isianUrut = explode("|", $untaiJwb);

	if ($nBidStudi==1) {		//utk yang satu kode soal hanya satu bid studi
		$noJwbKosong = ""; $noJwbBenar = ""; $noJwbSalah = "";
		$jwbKosong = 0; $jwbBenar = 0; $jwbSalah = 0;

		for ($q=1; $q<=$numberItem; $q++) {
			if ($q<10) { $r = '0'.$q; } else { $r = $q; }
			if ($soalTeracak[$q-1]<10) { $s = '0'.$soalTeracak[$q-1]; } else { $s = $soalTeracak[$q-1]; }

			if ($jwbKe[$q-1] === " ") {
				$noJwbKosong = $noJwbKosong.$r."-".$s.",";
				$jwbKosong++;
			}
			else if ($jwbKe[$q-1] === $kunciKe[$soalTeracak[$q-1]-1]) {
				$noJwbBenar = $noJwbBenar.$r."-".$s.",";
				$jwbBenar++;
			}
			else {
				$noJwbSalah = $noJwbSalah.$r."-".$s.",";
				$jwbSalah++;
			}

			if ($jwbKe[$q-1]=='') { $jwbKe[$q-1] = ' '; }
			
			$isianUrut[$soalTeracak[$q-1]-1] = $jwbKe[$q-1];
		}

		$urutIsi = implode(',',$isianUrut);
		$urutIsian = str_replace(",", "", $urutIsi);

		//proses scoring
		$noJwbKosong = substr($noJwbKosong,0,strlen($noJwbKosong)-1);
		$noJwbBenar = substr($noJwbBenar,0,strlen($noJwbBenar)-1);
		$noJwbSalah = substr($noJwbSalah,0,strlen($noJwbSalah)-1);
		$nilaiMentah = ($jwbBenar*$scB)+($jwbKosong*$scK)+($jwbSalah*$scS);
		$nilaiRerata = $nilaiMentah/($numberItem*$scB);
		$skor = round($nilaiRerata*$sekala, 2);

		if ($dataProgram=='IPA' || $klpSoal=='IPA' || $dataProgram=='IPS' || $klpSoal=='IPS') {
			if ($dataProgram=='IPA' || $klpSoal=='IPA') { $dataKlp="IPA"; $namaTabelHasil = "hasilipa"; }
			else if ($dataProgram=='IPS' || $klpSoal=='IPS') { $dataKlp="IPS"; $namaTabelHasil = "hasilips"; }

			$update_record = "UPDATE absensirecord SET acakSoal$keHari='$soalAcak', tmpAnswer$keHari='$untaiJwb', ordAnswer$keHari='$urutIsian' WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta' AND jurusan='$dataKlp'";
			mysqli_query($con, $update_record);

			//ambil nama kolom BS untuk tabel hasilipa / hasilips
			$ambilDataBS = mysqli_query($con, "SELECT kolomHasil FROM tabelhasil WHERE kelompok='$dataKlp' AND bidStudiTabel='$tabelBidStudi'");
			if ($ambilDataBS) {
				$dapatDataBS = mysqli_fetch_array($ambilDataBS);
				$colBS = $dapatDataBS['kolomHasil'];

				$colKode = $colBS."_kode";
				$colJawaban = $colBS."_jawaban";
				$colJwbBenar = $colBS."_jwbBenar";
				$colJwbSalah = $colBS."_jwbSalah";
				$colJwbKosong = $colBS."_jwbKosong";
				$colNilMentah = $colBS."_mentah";
				$colScore = $colBS."_score";

				$update_nilai = "UPDATE $namaTabelHasil SET $colKode = '$soalKode',
														  $colJawaban = '$untaiJwb',
														  $colJwbBenar = '$noJwbBenar',
														  $colJwbSalah = '$noJwbSalah',
														  $colJwbKosong = '$noJwbKosong',
														  $colNilMentah = $nilaiMentah,
														  $colScore = $skor
														  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
				mysqli_query($con, $update_nilai);
			}
		}
		else {
			//runyam dikit, ini soal utk IPA dan IPS alias IPC, jadi harus disimpen di dua tabel IPA dan IPS

			$update_records = "UPDATE absensirecord SET acakSoal$keHari='$soalAcak', tmpAnswer$keHari='$untaiJwb', ordAnswer$keHari='$urutIsian' WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
			mysqli_query($con, $update_records);

			//ambil nama kolom BS untuk tabel hasilipa
			$ambilDataBS = mysqli_query($con, "SELECT kolomHasil FROM tabelhasil WHERE kelompok='IPA' AND bidStudiTabel='$tabelBidStudi'");
			if ($ambilDataBS) {
				$dapatDataBS = mysqli_fetch_array($ambilDataBS);
				$colBS = $dapatDataBS['kolomHasil'];

				$colKode = $colBS."_kode";
				$colJawaban = $colBS."_jawaban";
				$colJwbBenar = $colBS."_jwbBenar";
				$colJwbSalah = $colBS."_jwbSalah";
				$colJwbKosong = $colBS."_jwbKosong";
				$colNilMentah = $colBS."_mentah";
				$colScore = $colBS."_score";

				$update_nilaiA = "UPDATE hasilipa SET $colKode = '$soalKode',
												  $colJawaban = '$untaiJwb',
												  $colJwbBenar = '$noJwbBenar',
												  $colJwbSalah = '$noJwbSalah',
												  $colJwbKosong = '$noJwbKosong',
												  $colNilMentah = $nilaiMentah,
												  $colScore = $skor
												  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
				mysqli_query($con, $update_nilaiA);
			}

			//ambil nama kolom BS untuk tabel hasilips
			$ambilDataBS = mysqli_query($con, "SELECT kolomHasil FROM tabelhasil WHERE kelompok='IPS' AND bidStudiTabel='$tabelBidStudi'");
			if ($ambilDataBS) {
				$dapatDataBS = mysqli_fetch_array($ambilDataBS);
				$colBS = $dapatDataBS['kolomHasil'];

				$colKode = $colBS."_kode";
				$colJawaban = $colBS."_jawaban";
				$colJwbBenar = $colBS."_jwbBenar";
				$colJwbSalah = $colBS."_jwbSalah";
				$colJwbKosong = $colBS."_jwbKosong";
				$colNilMentah = $colBS."_mentah";
				$colScore = $colBS."_score";

				$update_nilaiS = "UPDATE hasilips SET $colKode = '$soalKode',
												  $colJawaban = '$untaiJwb',
												  $colJwbBenar = '$noJwbBenar',
												  $colJwbSalah = '$noJwbSalah',
												  $colJwbKosong = '$noJwbKosong',
												  $colNilMentah = $nilaiMentah,
												  $colScore = $skor
												  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
				mysqli_query($con, $update_nilaiS);
			}
		}

		$ketHasil = "<tr>
						 <td style='text-align:left'><b>$studiBidangNama ($numberItem soal)</b></td>
						 <td>$jwbBenar</td>
						 <td>$jwbSalah</td>
						 <td>$jwbKosong</td>
						 <td><b>$nilaiMentah</b></td>
						 <td><b>$skor</b></td>
					 </tr>";
	}
	else { //utk yang satu kode soal beberapa bid studi
		$namaPerBS = explode("+", $studiBidangNama);
		$perBS = explode("+", $tabelBidStudi);
		$noAwal = explode("+", $noAwalBS);
		$itemPerBS = explode("+", $nItem);

		for($o=0;$o<$nBidStudi;$o++) { 
			$noJwbKosong = ""; $noJwbBenar = ""; $noJwbSalah = "";
			$jwbKosong = 0; $jwbBenar = 0; $jwbSalah = 0;
			
			for ($q=$noAwal[$o]; $q<=$noAwal[$o]+$itemPerBS[$o]-1; $q++) {
				if ($q<10) { $r = '0'.$q; } else { $r = $q; }
				if ($soalTeracak[$q-1]<10) { $s = '0'.$soalTeracak[$q-1]; } else { $s = $soalTeracak[$q-1]; }

				if ($jwbKe[$q-1] == " ") {
					$noJwbKosong = $noJwbKosong.$r."-".$s.",";
					$jwbKosong++;
				}
				else if ($jwbKe[$q-1] == $kunciKe[$soalTeracak[$q-1]-1]) {
					$noJwbBenar = $noJwbBenar.$r."-".$s.",";
					$jwbBenar++;
				}
				else {
					$noJwbSalah = $noJwbSalah.$r."-".$s.",";
					$jwbSalah++;
				}

				if ($jwbKe[$q-1]=='') { $jwbKe[$q-1] = ' '; }
				
				$isianUrut[$soalTeracak[$q-1]-1] = $jwbKe[$q-1];
			}

			$urutIsi = implode(',',$isianUrut);
			$urutIsian = str_replace(",", "", $urutIsi);

			//proses scoring
			$noJwbKosong = substr($noJwbKosong, 0, strlen($noJwbKosong)-1);
			$noJwbBenar = substr($noJwbBenar, 0, strlen($noJwbBenar)-1);
			$noJwbSalah = substr($noJwbSalah, 0, strlen($noJwbSalah)-1);
			$nilaiMentah = ($jwbBenar*$scB)+($jwbKosong*$scK)+($jwbSalah*$scS);
			$nilaiRerata = $nilaiMentah/($itemPerBS[$o]*$scB);
			$skor = round($nilaiRerata*$sekala, 2);

			if ($dataProgram=='IPA' || $klpSoal=='IPA' || $dataProgram=='IPS' || $klpSoal=='IPS') {
				if ($dataProgram=='IPA' || $klpSoal=='IPA') { $dataKlp="IPA"; $namaTabelHasil = "hasilipa"; }
				else if ($dataProgram=='IPS' || $klpSoal=='IPS') { $dataKlp="IPS"; $namaTabelHasil = "hasilips"; }

				$update_record2 = "UPDATE absensirecord SET acakSoal$keHari='$soalAcak', tmpAnswer$keHari='$untaiJwb', ordAnswer$keHari='$urutIsian' WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta' AND jurusan='$dataKlp'";
				mysqli_query($con, $update_record2);
				
				//ambil nama kolom BS untuk tabel hasilipa / hasilips
				$ambilDataBS = mysqli_query($con, "SELECT kolomHasil FROM tabelhasil WHERE kelompok='$dataKlp' AND bidStudiTabel='$perBS[$o]'");
				if ($ambilDataBS) {
					$dapatDataBS = mysqli_fetch_array($ambilDataBS);
					$colBS = $dapatDataBS['kolomHasil'];

					$untaiJwbBS = substr($untaiJwb,2*$noAwal[$o]-2,2*$itemPerBS[$o]-1);
					$colKode = $colBS."_kode";
					$colJawaban = $colBS."_jawaban";
					$colJwbBenar = $colBS."_jwbBenar";
					$colJwbSalah = $colBS."_jwbSalah";
					$colJwbKosong = $colBS."_jwbKosong";
					$colNilMentah = $colBS."_mentah";
					$colScore = $colBS."_score";

					$update_nilai = "UPDATE $namaTabelHasil SET $colKode = '$soalKode',
															  $colJawaban = '$untaiJwb',
															  $colJwbBenar = '$noJwbBenar',
															  $colJwbSalah = '$noJwbSalah',
															  $colJwbKosong = '$noJwbKosong',
															  $colNilMentah = $nilaiMentah,
															  $colScore = $skor
															  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
					mysqli_query($con, $update_nilai);
				}
			}
			else {	
				$update_record2s = "UPDATE absensirecord SET acakSoal$keHari='$soalAcak', tmpAnswer$keHari='$untaiJwb', ordAnswer$keHari='$urutIsian' WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
				mysqli_query($con, $update_record2s);

				//ambil nama kolom BS untuk tabel hasilipa
				$ambilDataBS = mysqli_query($con, "SELECT kolomHasil FROM tabelhasil WHERE kelompok='IPA' AND bidStudiTabel='$perBS[$o]'");
				if ($ambilDataBS) {
					$dapatDataBS = mysqli_fetch_array($ambilDataBS);
					$colBS = $dapatDataBS['kolomHasil'];

					$untaiJwbBS = substr($untaiJwb,2*$noAwal[$o]-2,2*$itemPerBS[$o]-1);
					$colKode = $colBS."_kode";
					$colJawaban = $colBS."_jawaban";
					$colJwbBenar = $colBS."_jwbBenar";
					$colJwbSalah = $colBS."_jwbSalah";
					$colJwbKosong = $colBS."_jwbKosong";
					$colNilMentah = $colBS."_mentah";
					$colScore = $colBS."_score";

					$update_nilaiA = "UPDATE hasilipa SET $colKode = '$soalKode',
													  $colJawaban = '$untaiJwb',
													  $colJwbBenar = '$noJwbBenar',
													  $colJwbSalah = '$noJwbSalah',
													  $colJwbKosong = '$noJwbKosong',
													  $colNilMentah = $nilaiMentah,
													  $colScore = $skor
													  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
					mysqli_query($con, $update_nilaiA);
				}


				//ambil nama kolom BS untuk tabel hasilips
				$ambilDataBS = mysqli_query($con, "SELECT kolomHasil FROM tabelhasil WHERE kelompok='IPS' AND bidStudiTabel='$perBS[$o]'");
				if ($ambilDataBS) {
					$dapatDataBS = mysqli_fetch_array($ambilDataBS);
					$colBS = $dapatDataBS['kolomHasil'];

					$untaiJwbBS = substr($untaiJwb,2*$noAwal[$o]-2,2*$itemPerBS[$o]-1);
					$colKode = $colBS."_kode";
					$colJawaban = $colBS."_jawaban";
					$colJwbBenar = $colBS."_jwbBenar";
					$colJwbSalah = $colBS."_jwbSalah";
					$colJwbKosong = $colBS."_jwbKosong";
					$colNilMentah = $colBS."_mentah";
					$colScore = $colBS."_score";

					$update_nilaiS="UPDATE hasilips SET $colKode = '$soalKode',
													  $colJawaban = '$untaiJwb',
													  $colJwbBenar = '$noJwbBenar',
													  $colJwbSalah = '$noJwbSalah',
													  $colJwbKosong = '$noJwbKosong',
													  $colNilMentah = $nilaiMentah,
													  $colScore = $skor
													  WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta'";
					mysqli_query($con, $update_nilaiS);
				}
			}

			$ketHasil .= "<tr>
							  <td style='text-align:left'><b>$namaPerBS[$o] ($itemPerBS[$o] soal)</b></td>
							  <td>$jwbBenar</td>
							  <td>$jwbSalah</td>
							  <td>$jwbKosong</td>
							  <td><b>$nilaiMentah</b></td>
							  <td><b>$skor</b></td>
						  </tr>";
		}
	}


	//update absensiharitespeserta
	$update_absen = "UPDATE absensiharitespeserta SET ipRemote='', lastNum$keHari='1', acakSoal$keHari='', tmpAnswer$keHari='', loginFlag='0', loginKey='0', TOke='0', curTimer='0', TOFinish=$keHari, finishIt='0', msgx_n='0', msgx='', playedAudio='', ragu$keHari='', hariKe$keHari='1' WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta' LIMIT 1";
	mysqli_query($con, $update_absen);

	mysqli_query($con, "UPDATE datasystem SET lastFinish='$dateToday' WHERE id = '1'");

	//nol-in time
	$update_time="UPDATE aruntimer SET runTime$keHari=0 WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta' LIMIT 1";
	mysqli_query($con, $update_time);

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="TO Online CBT BKB Nurul Fikri">
	<meta name="keywords" content="BKB, NF, nurul fikri, nurul, fikri, bimbel, islami, pendidikan, sekolah, cara cepat, to, to online, cbt">
	<link rel="shortcut icon" href="images/nf-favico.ico" type="image/x-icon">

	<script type="text/javascript" src="fnAcakSoal.js"></script>
	<script type="text/javascript" src="jquery-1.7.2.min.js"></script>
	
	<title><?php echo $titleBarProcsxPage; ?></title>

	<style media="screen" type="text/css">
		<!--
		body {
			margin: 0;
	        padding: 0;
	        border: 0;			/* This removes the border around the viewport in old versions of IE */
	        width: 100%;
	        min-width: 500px;   /* Minimum width of layout - remove line if not required */
	    }
		
		#contain {
			width: 100%;
			margin-top: 0px;
			overflow: hidden;
		}
		
		#mainContainer {
			background-color: #fff;
		}	

		#linkBar {
			background-color: #67BE7E;
			width: 100%;
			height: 40px;
			vertical-align: middle;
			padding-top: 10px;
		}
		
		.stickyTop {
			position: fixed;
			width: 100%;
			left: 0;
			top: 0;
		}
		
		#leftSide {
			padding: 0px 0px 0px 12px;
			margin-top: 50px;
		}

		table, th, td {
		    border: 1px solid black;
		    border-collapse: collapse;
		    font-family: verdana; font-size: 13px;
		    text-align: center; padding: 7px;
		}

		th {
			background-color: #dddddd;
		}

		-->
	</style>

	<script type="text/javascript">
		function outToIndex()
		{ window.location="cls.php"; }

		setInterval("outToIndex()", 90000);

		//Cegah backward from browser
		function preventBack(){window.history.forward();}
		setTimeout("preventBack()", 0);
		window.onunload=function(){null};
	</script>
</head>

<body>

<div id="contain">

		<div id="linkBar" class="stickyTop" style="font-family:Tahoma; color:white;" >
			<div style="float: right; margin-right: 20px;"> </div>
			<div style="float: left; margin-left: 20px;">
				&nbsp; &nbsp;
				<span style="color:white; text-align:center; font-size:17px; width:110px;">
				<?php echo "$iniHari"; ?></span>
			</div>
		</div>

		<div id="mainContainer">
			<div id="leftSide" style="min-width:190px">
				<div id="isiLeftSide" style="background: #fff; margin-top: 12px;">

					<center>

						<br>
						<img src="images/logoNFBIG.png" style="max-width:100%; height:auto;" alt="BKB NF" >
						<br><br><br>
						<span style="font-size:32px; font-family:'Tahoma'; color:#527FC9;">Terima kasih
						<br><?php echo $dataNama; ?>
						</span>
						<br><br><br>

						<div style="width: 100%; overflow: auto; font-size:21px; <?php if ($showScore==0) { echo 'display:  none;'; } ?>">
							TO telah selesai dengan hasil sebagai berikut :
							<br><br>

							<table style="box-shadow: 0px 0px 6px #555555">
								<tr>
									<th>Bid. Studi</th>
									<th>Benar</th>
									<th>Salah</th>
									<th>Kosong</th>
									<th>Nil. Mentah</th>
									<th>Skor</th>
								</tr>
								<?php echo $ketHasil; ?>
							</table>

						</div>

						<br><br><br>

						 <form action="testpage.php" id="formNextTest" method="post" target="_parent">
						 	<input type="hidden" id="noID" name="noID" value="<?php echo $dataNoInduk; ?>">
							<input type="hidden" id="noPeserta" name="noPeserta" value="<?php echo $dataNoPeserta; ?>">
							<input type="hidden" id="acakanSoal" val=''>

								<?php
									
									$lihPeserta = "SELECT petaSoal, jurusan, kodeTO, TOke, hariKe1, hariKe2, hariKe3, hariKe4, hariKe5, hariKe6, hariKe7, hariKe8, hariKe9, hariKe10 FROM absensiharitespeserta WHERE nomorInduk='$dataNoInduk' AND nomorPeserta='$dataNoPeserta' LIMIT 1";
									$cek_Peserta = mysqli_query($con, $lihPeserta);
									$hasilCek_Peserta = mysqli_fetch_array($cek_Peserta);

									$peSo = $hasilCek_Peserta['petaSoal'];
									$jurusan = $hasilCek_Peserta['jurusan'];
									$sedangTOkode = $hasilCek_Peserta['kodeTO'];
									$sedangTOke = $hasilCek_Peserta['TOke'];

									$hasil_Ke[1] = $hasilCek_Peserta['hariKe1'];
									$hasil_Ke[2] = $hasilCek_Peserta['hariKe2'];
									$hasil_Ke[3] = $hasilCek_Peserta['hariKe3'];
									$hasil_Ke[4] = $hasilCek_Peserta['hariKe4'];
									$hasil_Ke[5] = $hasilCek_Peserta['hariKe5'];
									$hasil_Ke[6] = $hasilCek_Peserta['hariKe6'];
									$hasil_Ke[7] = $hasilCek_Peserta['hariKe7'];
									$hasil_Ke[8] = $hasilCek_Peserta['hariKe8'];
									$hasil_Ke[9] = $hasilCek_Peserta['hariKe9'];
									$hasil_Ke[10] = $hasilCek_Peserta['hariKe10'];

									if ($sedangTOke!=0) {
										if ($jurusan == "IPA" || $jurusan == "IPS")
										{ $lihBSAktif = "SELECT namaBidStudi, banyakItemNomor, indexBidStudi, kodeSoal FROM naskahsoal WHERE (kodeSoal='$sedangTOkode' AND petaSoal='$peSo') OR (statusAktif=1 AND petaSoal='$peSo' AND (kelompok='IPAIPS' OR kelompok='$jurusan'))"; }
										else if ($jurusan == "IPC")
										{ $lihBSAktif = "SELECT namaBidStudi, banyakItemNomor, indexBidStudi, kodeSoal FROM naskahsoal WHERE (kodeSoal='$sedangTOkode' AND petaSoal='$peSo') OR (statusAktif=1 AND petaSoal='$peSo')"; }
									}
									else {
										if ($jurusan == "IPA" || $jurusan == "IPS")
										{ $lihBSAktif = "SELECT namaBidStudi, banyakItemNomor, indexBidStudi, kodeSoal FROM naskahsoal WHERE statusAktif=1 AND petaSoal='$peSo' AND (kelompok='IPAIPS' OR kelompok='$jurusan')"; }
										else if ($jurusan == "IPC")
										{ $lihBSAktif = "SELECT namaBidStudi, banyakItemNomor, indexBidStudi, kodeSoal FROM naskahsoal WHERE statusAktif=1 AND petaSoal='$peSo'"; }
									}

									$cek_BSAktif = mysqli_query($con, $lihBSAktif);
									$nou=0;		//nomor urut

									if (mysqli_num_rows($cek_BSAktif)>0) {
										$selectInput = false;

										while ($hasilCek_BS = mysqli_fetch_array($cek_BSAktif))
										{
											$nou++;
											$bsyangaktif="";
											$namabsaktif = $hasilCek_BS['namaBidStudi'];
											$jmlsoal = $hasilCek_BS['banyakItemNomor'];
											$hitBanyakBS = substr_count($jmlsoal, "+");

											if ($hitBanyakBS>0) {
												$activeObject = explode("+", $namabsaktif);
												$itemObject = explode("+", $jmlsoal);
												for ($h=0; $h<=$hitBanyakBS;$h++)
												{ $bsyangaktif = $bsyangaktif.$activeObject[$h]." (".$itemObject[$h]." soal), "; }
												$bsyangaktif = substr($bsyangaktif,0,strlen($bsyangaktif)-2); 
											}
											else
											{ $bsyangaktif = $namabsaktif." (".$jmlsoal." soal)"; }

											$forToolTip = $bsyangaktif;
											if (strlen($bsyangaktif)>60)
											{ $bsyangaktif = substr($bsyangaktif,0,57)." ..."; }

											if ($hasil_Ke[$hasilCek_BS['indexBidStudi']] == '0') {
												if (!$selectInput) {
													echo "<select name='pilSet' id='pilSet' style='width: 260px'>
																<option value='0' selected> - Pilih Bid. Studi - </option>";
													$selectInput = true;
												}
													echo "<option value='$hasilCek_BS[kodeSoal]-$hasilCek_BS[indexBidStudi]' title='$forToolTip'>$nou. $bsyangaktif</option>";
											}
										}
										
										if ($selectInput) {
											echo "</select> &nbsp;";
											echo "<input disabled type='button' id='btnNextTest' style='font-weight:bold; color:#1648AD' value='Next Test'>";
											$selectInput = false;
										}
									}
								?>
							<div id="infoText" style="margin-top: 15px; font-family: arial;"> </div>
						</form>

						<br><br><br><br>
						 
						<input type="button" value="Logout" onclick="outToIndex()" 
						style="font-size:18px; font-weight:bold; color:red; height:35px; width:110px;">

						<br><br><br><br>

					</center>

				</div>
			</div>
		</div>

</div>

<script type="text/javascript">

	$(window).load(function() {
		
		$("#pilSet").change(function() {
			pilSetVal = $(this).val();
			$("#acakanSoal").val('');
			$("#btnNextTest").prop('disabled', true);

			if (pilSetVal!="0") {
				$("#btnNextTest").prop('disabled', false);
				adaNoID = $("#noID").val();
                adaNoPeserta = $("#noPeserta").val();
                // cek keberadaan acakan soalnya di tabel absensiharitespeserta
                $.ajax({
                    type: "post",
                    url: "ajaxjsonCekAcakanSoal.php",
                    data: {nIN:adaNoID, nPE:adaNoPeserta, pSet:pilSetVal},
                    cache: false,
                    dataType: "json",
                    success: function(cekSoal) {
                        itemPerBS = cekSoal[0];
                        totS = cekSoal[1];
                        fSoal = cekSoal[2];
                        pathSoal = cekSoal[3];
                        if (itemPerBS!='') {
                        	acakNoSoal = acakinSoal(itemPerBS, totS, fSoal);
                        	perImg = acakNoSoal.split(",");
	                        dfile = "images/soal/"+pathSoal+"/"+perImg[0]+".png";
	                        imageObject = new Image();
	                        imageObject.src = dfile;
                        }
                        else
                        { acakNoSoal = cekSoal[1]; }

                    	$("#acakanSoal").val(acakNoSoal);
                    }
                });
			}
		});

		$("#btnNextTest").mousedown(function() {
			$("#infoText").html("Menyiapkan soal ... " + "&nbsp;<img src='images/open-book-ani.gif' style='transform:translateY(2px);' border='0' />");

			pilSetVal = $("#pilSet").val();
            setPil = pilSetVal.split("-");
            idxH = setPil[1];
            adaNoID = $("#noID").val();
            adaNoPeserta = $("#noPeserta").val();
            soalAcak = $("#acakanSoal").val();

            if (soalAcak!='') {
                $.ajax({
                    type: "post",
                    url: "saveAcakSoal.php",
                    data: {hk:idxH, ni:adaNoID, np:adaNoPeserta, ss:soalAcak},
                    cache: false,
                    success: function(cekAcak) {
                        if (cekAcak!="")
                        { $("#formNextTest").submit(); }
                        else
                        { $("#infoText").html(""); }
                    }
                });
            }
            else {   // ternyata acakanSoalnya kosong -> acak dulu
                $.ajax({
                    type: "post",
                    url: "ajaxjsonCekAcakanSoal.php",
                    data: {nIN:adaNoID, nPE:adaNoPeserta, pSet:pilSetVal},
                    cache: false,
                    dataType: "json",
                    success: function(cekSoal) {
                        itemPerBS = cekSoal[0];
                        totS = cekSoal[1];
                        fSoal = cekSoal[2];
                        pathSoal = cekSoal[3];
                        if (itemPerBS!='') {
                        	acakNoSoal = acakinSoal(itemPerBS, totS, fSoal);
                        	perImg = acakNoSoal.split(",");
	                        dfile = "images/soal/"+pathSoal+"/"+perImg[0]+".png";
	                        imageObject = new Image();
	                        imageObject.src = dfile;
                        }
                        else
                        { acakNoSoal = cekSoal[1]; }

                    	$("#acakanSoal").val(acakNoSoal);
                        $("#infoText").html("");
                    }
                });
            }
        });

	});

	if ( window.history.replaceState )
	{ window.history.replaceState( null, null, window.location.href ); }

</script>

</body>
</html>