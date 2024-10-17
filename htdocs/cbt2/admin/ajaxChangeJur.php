<?php
	require_once('../koneksi_db.php');

	$NIID = $_POST['NITO'];
	$NPID = $_POST['NPTO'];

	//ambil beberapa data lama
	$cekNowJur = mysqli_query($con, "SELECT jurusan FROM absensiharitespeserta WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID' LIMIT 1");
	$getJur = mysqli_fetch_array($cekNowJur);
	$jurlama = $getJur['jurusan'];

	if ($jurlama=='IPA') {
		$jurbaru = "IPS";
		$tblHasilLama = "hasilipa";
		$tblHasilBaru = "hasilips";
	}
	else if ($jurlama=='IPS') {
		$jurbaru = "IPA";
		$tblHasilLama = "hasilips";
		$tblHasilBaru = "hasilipa";
	}

	//insert record baru di tabel hasil baru dan copy bbrp data dari tabel hasil lama
	$insertTblHasil = "INSERT INTO $tblHasilBaru (nomorInduk, nomorPeserta, nama, kelas, piljur1, piljur2, piljur3)
							  SELECT nomorInduk, nomorPeserta, nama, kelas, piljur1, piljur2, piljur3
							  FROM $tblHasilLama WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID' LIMIT 1";
	mysqli_query($con, $insertTblHasil);

	//copy data hasil dari tabel lama yg perlu dipindahkan, yaitu utk bid studi IPAIPS, kalau ada
	$cekHasilIPAIPS = mysqli_query($con, "SELECT bidStudiTabel FROM naskahsoal WHERE kelompok='IPAIPS'");
	while ($getBSTbl = mysqli_fetch_array($cekHasilIPAIPS)) {
	 	$bsTabel = $getBSTbl['bidStudiTabel'];

	 	$perBS = explode("+", $bsTabel);
	 	foreach ($perBS as $bs) {
		    $ckHasil1 = mysqli_query($con, "SELECT kolomHasil FROM tabelhasil WHERE kelompok='$jurlama' AND bidStudiTabel='$bs'");
		    $getckHasil1 = mysqli_fetch_array($ckHasil1);
		    $getKH1 = $getckHasil1['kolomHasil'];

		    $getKH1_kode = $getKH1.'_kode';
		    $getKH1_jawaban = $getKH1.'_jawaban';
		    $getKH1_jwbBenar = $getKH1.'_jwbBenar';
		    $getKH1_jwbSalah = $getKH1.'_jwbSalah';
		    $getKH1_jwbKosong = $getKH1.'_jwbKosong';
		    $getKH1_mentah = $getKH1.'_mentah';
		    $getKH1_score = $getKH1.'_score';


		    $ckHasil2 = mysqli_query($con, "SELECT kolomHasil FROM tabelhasil WHERE kelompok='$jurbaru' AND bidStudiTabel='$bs'");
		    $getckHasil2 = mysqli_fetch_array($ckHasil2);
		    $getKH2 = $getckHasil1['kolomHasil'];

		    $getKH2_kode = $getKH2.'_kode';
		    $getKH2_jawaban = $getKH2.'_jawaban';
		    $getKH2_jwbBenar = $getKH2.'_jwbBenar';
		    $getKH2_jwbSalah = $getKH2.'_jwbSalah';
		    $getKH2_jwbKosong = $getKH2.'_jwbKosong';
		    $getKH2_mentah = $getKH2.'_mentah';
		    $getKH2_score = $getKH2.'_score';

		    //read write tabel hasil lama ke yg baru
		    $ckBS1 = mysqli_query($con, "SELECT $getKH1_kode, $getKH1_jawaban, $getKH1_jwbBenar, $getKH1_jwbSalah, $getKH1_jwbKosong, $getKH1_mentah, $getKH1_score FROM $tblHasilLama WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID' LIMIT 1");
		    $getckBS1 = mysqli_fetch_array($ckBS1);

		    $kolKo1 = $getckBS1[$getKH1.'_kode'];
		    if(!is_null($kolKo1)) {
			    $kolJa1 = $getckBS1[$getKH1.'_jawaban'];
			    $kolJB1 = $getckBS1[$getKH1.'_jwbBenar'];
			    $kolJS1 = $getckBS1[$getKH1.'_jwbSalah'];
			    $kolJK1 = $getckBS1[$getKH1.'_jwbKosong'];
			    $kolMe1 = $getckBS1[$getKH1.'_mentah'];
			    $kolSc1 = $getckBS1[$getKH1.'_score'];

			    $copyNilai = "UPDATE $tblHasilBaru SET $getKH2_kode = $kolKo1,
												  $getKH2_jawaban = '$kolJa1',
												  $getKH2_jwbBenar = '$kolJB1',
												  $getKH2_jwbSalah = '$kolJS1',
												  $getKH2_jwbKosong = '$kolJK1',
												  $getKH2_mentah = $kolMe1,
												  $getKH2_score = $kolSc1
												  WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'";
				mysqli_query($con, $copyNilai);
			}
		}
	}

	//cek hasil jurusan lama
	$cekIndJurLama = mysqli_query($con, "SELECT indexBidStudi FROM naskahsoal WHERE kelompok='$jurlama'");
	while ($indexLama = mysqli_fetch_array($cekIndJurLama)) {
		$indxLama = $indexLama['indexBidStudi'];

		//bersihkan absensiharitespeserta
		$cleanAbsensi = "UPDATE absensiharitespeserta SET acakSoal$indxLama = '',
												  lastNum$indxLama = 1,
												  tmpAnswer$indxLama = '',
												  ragu$indxLama = '',
												  hariKe$indxLama = 0
												  WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'";
		mysqli_query($con, $cleanAbsensi);

		//bersihkan index absensirecord
		$cleanAbsensiRec = "UPDATE absensirecord SET acakSoal$indxLama = '', tmpAnswer$indxLama = '', ordAnswer$indxLama = '' WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'";
		mysqli_query($con, $cleanAbsensiRec);

		//bersihkan index aruntimer
		$cleanRunTimer = "UPDATE aruntimer SET runTime$indxLama = 0 WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'";
		mysqli_query($con, $cleanRunTimer);
	}

	//ganti jurusan dan bersih2 di tabel absensiharitespeserta, absensirecord, dan aruntimer
	mysqli_query($con, "UPDATE absensiharitespeserta SET jurusan='$jurbaru', loginFlag=0, loginKey=0, kodeTO='', TOke=0, setTimer=0, curTimer=0, TOFinish=0 WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'");
	mysqli_query($con, "UPDATE absensirecord SET jurusan='$jurbaru' WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'");
	mysqli_query($con, "UPDATE aruntimer SET jurusan='$jurbaru' WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'");

	//hapus record di tabel hasil lama
	mysqli_query($con, "DELETE FROM $tblHasilLama WHERE nomorInduk='$NIID' AND nomorPeserta='$NPID'");
?>