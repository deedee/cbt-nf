<?php

	if (isset($_POST['nIN']) && isset($_POST['nPE'])) {

		$noInduk = $_POST['nIN'];
		$noSiswa = $_POST['nPE'];
		
		$pilSet = $_POST['pSet'];
		$setPil = explode("-", $pilSet);
		$kodeTO = $setPil[0];
		$hariKe = $setPil[1];

		$JsonArray = array();
		$JsonArray[0] = '';
		$JsonArray[1] = 0;
		$JsonArray[2] = '';
		$JsonArray[3] = '';

		require_once('koneksi_db.php');

		$acakanSoal = '';
		$cekAcakan = mysqli_query($con, "SELECT acakSoal$hariKe FROM absensiharitespeserta WHERE nomorInduk='$noInduk' LIMIT 1");
		$hasilCekAcakan = mysqli_fetch_array($cekAcakan);

		$acakanSoal = $hasilCekAcakan['acakSoal'.$hariKe];
		if (is_null($acakanSoal)) { $acakanSoal = ''; }

		if ($acakanSoal=='') {
			//pilih dan ambil soal yg sesuai dari tabel naskahsoal
			$ambilSoal = mysqli_query($con, "SELECT pathKodeSoal, banyakItemNomor, susunanItemSoal FROM naskahsoal WHERE kodeSoal='$kodeTO' LIMIT 1");
			$dapatSoal = mysqli_fetch_array($ambilSoal);

			$JsonArray[0] = $dapatSoal['banyakItemNomor'];
			$JsonArray[1] = 0;
			$JsonArray[2] = $dapatSoal['susunanItemSoal'];
			$JsonArray[3] = $dapatSoal['pathKodeSoal'];

			$totalin = substr_count($JsonArray[0], "+");
			$totalin++;
			if ($totalin > 0) {
				$numPerBS = explode("+", $JsonArray[0]);
				for($iu=0; $iu<$totalin; $iu++)
				{ $JsonArray[1] += $numPerBS[$iu]; }
			}
		}
		else
		{ $JsonArray[1] = $acakanSoal; }
	}

	echo json_encode($JsonArray);

?>