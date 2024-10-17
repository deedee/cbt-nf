<?php

	require_once('koneksi_db.php');

	if (!isset($_POST['st'])) {
		echo ('<script type="text/javascript">');
		echo ('window.location="index.php";');
		echo ('</script>');
		exit;
	}

	else if ($_POST['st'] == "SAct") {
		//bersih2 dulu klo ada timer peserta yg udah finish tapi gak kereset jadi 0
		$cekFinishHari = "SELECT nomorInduk, nomorPeserta, hariKe1, hariKe2, hariKe3, hariKe4, hariKe5, hariKe6, hariKe7, hariKe8, hariKe9, hariKe10 FROM absensiharitespeserta";
		$finishHari = mysqli_query($con, $cekFinishHari);

		while ($dFinish = mysqli_fetch_array($finishHari)) {
			$noInd = $dFinish['nomorInduk'];
			$noPes = $dFinish['nomorPeserta'];

			//loop utk hariKe1~10
			for ($f=1; $f<=10 ; $f++) {
				$hariCek = $dFinish['hariKe'.$f];
				if ($hariCek == '1') {
					$bersih2runTime = "UPDATE aruntimer SET runTime$f='0' WHERE nomorInduk='$noInd' AND nomorPeserta='$noPes' LIMIT 1";
					mysqli_query($con, $bersih2runTime);
				}
			}
		}

		$shiftPilih = $_POST['nS'];

		mysqli_query($con, "UPDATE absensiharitespeserta SET TOFinish=0, finishIt=0, msgx_n=0, msgx='' LIMIT 1");

		//cek dulu ada berapa shift yang tersedia, kalo lebih dari satu baru diaktifin
		$berapaShift = mysqli_query($con,"SELECT hariKe FROM shifttes");
		$adaBerapaShift = mysqli_num_rows($berapaShift);

		if ($adaBerapaShift>1) {
			mysqli_query($con,"UPDATE shifttes SET aktifFlag = 0");
			mysqli_query($con,"UPDATE shifttes SET aktifFlag = 1 where no = $shiftPilih");

			$ambilShift = mysqli_query($con, "SELECT jamMulai, mntMulai, shift, aktifFlag, bolehTelat FROM shifttes WHERE aktifFlag = 1");
			$dapatShift = mysqli_fetch_array($ambilShift);
			if ($dapatShift['bolehTelat']==0)
			{ $cTelat = ''; } else { $cTelat = ' &#9749;'; }
			$waktuShift = $dapatShift['jamMulai'].":".$dapatShift['mntMulai'].' (grup '.$dapatShift['shift'].')'.$cTelat;

			echo $waktuShift;
		}
	}

	else if ($_POST['st'] == "TOAct") {	
		$TOpilih = $_POST['IDbs'];
		$aksi = $_POST['tipeA'];

		//set atau unset TO yg dicek dari tabel naskahsoal
		if ($aksi == 'aktif')
		{ $setTO = "UPDATE naskahsoal SET statusAktif = 1 WHERE id=$TOpilih"; }
		else if ($aksi == 'nonaktif')
		{ $setTO = "UPDATE naskahsoal SET statusAktif = 0 WHERE id=$TOpilih"; }

		mysqli_query($con,$setTO);

		$setaktif = mysqli_query($con, "SELECT id FROM naskahsoal WHERE kodeSoal!='' AND statusAktif=1");
		$totAktif = mysqli_num_rows($setaktif);

		echo $totAktif;
	}

	else if ($_POST['st'] == "pantauSiswa") {
		//Hitung IPA yg Log
		/*
		$ipa = mysqli_query($con, "SELECT id FROM aruntimer WHERE jurusan='IPA' AND (runTime1>0 OR runTime2>0 OR runTime3>0 OR runTime4>0 OR runTime5>0 OR runTime6>0 OR runTime7>0 OR runTime8>0 OR runTime9>0 OR runTime10>0)");
		$totLogIPA = mysqli_num_rows($ipa);
		//Hitung IPS yg Log
		$ips = mysqli_query($con, "SELECT id FROM aruntimer WHERE jurusan='IPS' AND (runTime1>0 OR runTime2>0 OR runTime3>0 OR runTime4>0 OR runTime5>0 OR runTime6>0 OR runTime7>0 OR runTime8>0 OR runTime9>0 OR runTime10>0)");
		$totLogIPS = mysqli_num_rows($ips);
		//Hitung IPC yg Log
		$ipc = mysqli_query($con, "SELECT id FROM aruntimer WHERE jurusan='IPC' AND (runTime1>0 OR runTime2>0 OR runTime3>0 OR runTime4>0 OR runTime5>0 OR runTime6>0 OR runTime7>0 OR runTime8>0 OR runTime9>0 OR runTime10>0)");
		$totLogIPC = mysqli_num_rows($ipc);
		*/

		$ipa = mysqli_query($con, "SELECT id FROM absensiharitespeserta WHERE jurusan='IPA' AND loginFlag=1");
		$totLogIPA = mysqli_num_rows($ipa);

		$ips = mysqli_query($con, "SELECT id FROM absensiharitespeserta WHERE jurusan='IPS' AND loginFlag=1");
		$totLogIPS = mysqli_num_rows($ips);

		$ipc = mysqli_query($con, "SELECT id FROM absensiharitespeserta WHERE jurusan='IPC' AND loginFlag=1");
		$totLogIPC = mysqli_num_rows($ipc);

		/* RETURN VALUE */
		$hasilCari = array();
		//$hasilCari[0]=$totSiswa;
		$hasilCari[0]=$totLogIPA;
		$hasilCari[1]=$totLogIPS;
		$hasilCari[2]=$totLogIPC;

		echo json_encode($hasilCari);
	}

?>