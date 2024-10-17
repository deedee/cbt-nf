<?php
//file ini dipanggil oleh file index.php (file login peserta, utk memvalidasi & mengidentifikasi peserta yg akan login)

	if (!isset($_POST['nIN']) || !isset($_POST['nPE'])) {
		echo ('<script type="text/javascript">');
		echo ('window.location="index.php";');
		echo ('</script>');
		exit;
	}

	$noInduk = $_POST['nIN'];
	$noSiswa = $_POST['nPE'];

	session_start();
	require_once('koneksi_db.php');

	$hasil_Ke = array();
	$JsonArray = array();

	$id1P = $id2P = '';
	//cek dulu validitas id Login 1 dan 2
	$cekdt1Peserta = mysqli_query($con, "SELECT id FROM absensiharitespeserta WHERE nomorInduk = '$noInduk' LIMIT 1");
	if ($cekdt1Peserta) {
		$val1Peserta = mysqli_fetch_array($cekdt1Peserta);
		$id1P = $val1Peserta['id'];
	}

	$cekdt2Peserta = mysqli_query($con, "SELECT id FROM absensiharitespeserta WHERE nomorPeserta = '$noSiswa' LIMIT 1");
	if ($cekdt2Peserta) {
		$val2Peserta = mysqli_fetch_array($cekdt2Peserta);
		$id2P = $val2Peserta['id'];
	}


	if ($id1P=='' || $id2P=='')					//error tipe 0, ada data yg salah di id1 atau id2
	{ $JsonArray[0] = 0; }
	else if ($id1P!='' && $id2P!='' && ($id1P != $id2P))	//error tipe 1, id1 dan id2 tdk cocok
	{ $JsonArray[0] = 1; }
	else
	{
		$JsonArray[0] = 2;

		//cari peserta tersebut
		$lihPeserta = "SELECT piljur1, piljur2, piljur3, petaSoal, nama, kelas, jurusan, nomorHP, alamatEmail, tglLahir, shiftTes, loginFlag, loginKey, kodeTO, TOke, hariKe1, hariKe2, hariKe3, hariKe4, hariKe5, hariKe6, hariKe7, hariKe8, hariKe9, hariKe10 FROM absensiharitespeserta WHERE nomorInduk='$noInduk' AND nomorPeserta='$noSiswa' LIMIT 1";
		$cek_Peserta = mysqli_query($con, $lihPeserta);
		$hasilCek_Peserta = mysqli_fetch_array($cek_Peserta);

		//cari timer peserta tersebut
		$lihTime = "SELECT runTime1, runTime2, runTime3, runTime4, runTime5, runTime6, runTime7, runTime8, runTime9, runTime10 FROM aruntimer WHERE nomorInduk='$noInduk' AND nomorPeserta='$noSiswa' LIMIT 1";
		$cek_Time = mysqli_query($con, $lihTime);
		$hasilCek_Time = mysqli_fetch_array($cek_Time);

		$pJur1 = $hasilCek_Peserta['piljur1'];
		$pJur2 = $hasilCek_Peserta['piljur2'];
		$pJur3 = $hasilCek_Peserta['piljur3'];

		$peSo = $hasilCek_Peserta['petaSoal'];
		$nama_ = $hasilCek_Peserta['nama'];
		$kls = $hasilCek_Peserta['kelas'];
		$jurusan_ = $hasilCek_Peserta['jurusan'];
		$HPno = $hasilCek_Peserta['nomorHP'];
		$EmailAddr = $hasilCek_Peserta['alamatEmail'];
		$DoB = $hasilCek_Peserta['tglLahir'];

		$shiftnya = $hasilCek_Peserta['shiftTes'];
		$sedangTOkode = $hasilCek_Peserta['kodeTO'];
		$sedangTOke = $hasilCek_Peserta['TOke'];

		if ($sedangTOke!=0) { $rT = $hasilCek_Time['runTime'.$sedangTOke]; } else { $rT = 0; }

		$flagLogin = $hasilCek_Peserta['loginFlag'];
		$kLogin = $hasilCek_Peserta['loginKey'];

		if (isset($_SESSION['currentNIN']) && $_SESSION['currentNIN']!='' && isset($_SESSION['currentNOP']) && $_SESSION['currentNOP']!='') {	//apakah sudah pernah masuk halaman tes
			if ($_SESSION['currentNIN'] == $noInduk || $_SESSION['currentNOP'] == $noSiswa)
			{ $orgUser = true; }
			else
			{ $orgUser = false; }
		}
		else {
			if ($kLogin==1)
			{ $orgUser = true; }
			else
			{ $orgUser = false; }
		}

		if ($orgUser)
		{ $keyLogin = 1; }
		else
		{ $keyLogin = 0; }

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

	 	$JsonArray[1] = $nama_;
		$JsonArray[2] = $kls;
		$JsonArray[3] = $jurusan_;
		$JsonArray[4] = $rT;
		$JsonArray[5] = $keyLogin;
		$JsonArray[7] = $HPno;
		$JsonArray[8] = $EmailAddr;
		$JsonArray[9] = $DoB;

		$JsonArray[10] = $pJur1;
		$JsonArray[11] = $pJur2;
		$JsonArray[12] = $pJur3;

		$JsonArray[13] = $shiftnya;
		$JsonArray[14] = $flagLogin;

		//lihat kalo2 dia udah ikut bid studi tertentu maka yang muncul adalah bid studi tersebut
		if ($sedangTOke!=0) {
			if ($jurusan_ == "IPA" || $jurusan_ == "IPS")
			{ $lihBSAktif = "SELECT namaBidStudi, banyakItemNomor, indexBidStudi, kodeSoal FROM naskahsoal WHERE (kodeSoal='$sedangTOkode' AND petaSoal='$peSo') OR (statusAktif=1 AND petaSoal='$peSo' AND (kelompok='IPAIPS' OR kelompok='$jurusan_'))"; }
			else if ($jurusan_ == "IPC")
			{ $lihBSAktif = "SELECT namaBidStudi, banyakItemNomor, indexBidStudi, kodeSoal FROM naskahsoal WHERE (kodeSoal='$sedangTOkode' AND petaSoal='$peSo') OR (statusAktif=1 AND petaSoal='$peSo')"; }
		}
		else {
			if ($jurusan_ == "IPA" || $jurusan_ == "IPS")
			{ $lihBSAktif = "SELECT namaBidStudi, banyakItemNomor, indexBidStudi, kodeSoal FROM naskahsoal WHERE statusAktif=1 AND petaSoal='$peSo' AND (kelompok='IPAIPS' OR kelompok='$jurusan_')"; }
			else if ($jurusan_ == "IPC")
			{ $lihBSAktif = "SELECT namaBidStudi, banyakItemNomor, indexBidStudi, kodeSoal FROM naskahsoal WHERE statusAktif=1 AND petaSoal='$peSo'"; }
		}

		$cek_BSAktif = mysqli_query($con, $lihBSAktif);
		$nou=0;		//nomor urut
		$JsonArray[6] = "";

		if (mysqli_num_rows($cek_BSAktif) > 0) {
			while ($hasilCek_BS = mysqli_fetch_array($cek_BSAktif)) {
				$nou++;
				$bsyangaktif="";
				$namabsaktif = $hasilCek_BS['namaBidStudi'];
				$jmlsoal = $hasilCek_BS['banyakItemNomor'];
				$hitBanyakBS = substr_count($jmlsoal, "+");

				if ($hitBanyakBS > 0) {
					$activeObject = explode("+",$namabsaktif);
					$itemObject = explode("+",$jmlsoal);
					for ($h=0; $h<=$hitBanyakBS;$h++)
					{ $bsyangaktif = $bsyangaktif.$activeObject[$h]." (".$itemObject[$h]." soal), "; }
					$bsyangaktif = substr($bsyangaktif,0,strlen($bsyangaktif)-2); 
				}
				else
				{ $bsyangaktif = $namabsaktif." (".$jmlsoal." soal)"; }

				$forToolTip = $bsyangaktif;
				if (strlen($bsyangaktif) > 60)
				{ $bsyangaktif = substr($bsyangaktif,0,57)." ..."; }

				if ($hasil_Ke[$hasilCek_BS['indexBidStudi']] == '0')
				{ $JsonArray[6] .= "<option value='$hasilCek_BS[kodeSoal]-$hasilCek_BS[indexBidStudi]' title='$forToolTip'>$nou. $bsyangaktif</option>"; }
			}
		}
	}

	echo json_encode($JsonArray);

?>