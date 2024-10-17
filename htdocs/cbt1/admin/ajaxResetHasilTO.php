<?php

	require_once('../koneksi_db.php');

	$pilID = $_POST['rID'];

	if ($pilID!='') {
		//cari dulu dia IPA atau IPS, juga no pesertanya berapa
		$ceksiswanya = mysqli_query($con,"SELECT nomorInduk, nomorPeserta, jurusan FROM absensiharitespeserta WHERE id=$pilID LIMIT 1");
		$hasilceksiswanya = mysqli_fetch_array($ceksiswanya);
		$noInd = $hasilceksiswanya['nomorInduk'];
		$noPes = $hasilceksiswanya['nomorPeserta'];
		$jurPes = $hasilceksiswanya['jurusan'];

		if ($jurPes=="IPA") { $tabelhasil="hasilipa"; } elseif ($jurPes=="IPS") { $tabelhasil="hasilips"; }
	}

	if ($pilID!="") {
		if ($jurPes!='IPC') {
			mysqli_query($con,"UPDATE $tabelhasil SET BS1_kode = NULL, BS1_jawaban = '', BS1_jwbBenar = '', BS1_jwbSalah = '', BS1_jwbKosong = '', BS1_mentah = '0', BS1_score = '0.00',
					BS2_kode = NULL, BS2_jawaban = '', BS2_jwbBenar = '', BS2_jwbSalah = '', BS2_jwbKosong = '', BS2_mentah = '0', BS2_score = '0.00',
					BS3_kode = NULL, BS3_jawaban = '', BS3_jwbBenar = '', BS3_jwbSalah = '', BS3_jwbKosong = '', BS3_mentah = '0', BS3_score = '0.00',
					BS4_kode = NULL, BS4_jawaban = '', BS4_jwbBenar = '', BS4_jwbSalah = '', BS4_jwbKosong = '', BS4_mentah = '0', BS4_score = '0.00',
					BS5_kode = NULL, BS5_jawaban = '', BS5_jwbBenar = '', BS5_jwbSalah = '', BS5_jwbKosong = '', BS5_mentah = '0', BS5_score = '0.00',
					BS6_kode = NULL, BS6_jawaban = '', BS6_jwbBenar = '', BS6_jwbSalah = '', BS6_jwbKosong = '', BS6_mentah = '0', BS6_score = '0.00',
					BS7_kode = NULL, BS7_jawaban = '', BS7_jwbBenar = '', BS7_jwbSalah = '', BS7_jwbKosong = '', BS7_mentah = '0', BS7_score = '0.00',
					BS8_kode = NULL, BS8_jawaban = '', BS8_jwbBenar = '', BS8_jwbSalah = '', BS8_jwbKosong = '', BS8_mentah = '0', BS8_score = '0.00',
					BS9_kode = NULL, BS9_jawaban = '', BS9_jwbBenar = '', BS9_jwbSalah = '', BS9_jwbKosong = '', BS9_mentah = '0', BS9_score = '0.00',
					BS10_kode = NULL, BS10_jawaban = '', BS10_jwbBenar = '', BS10_jwbSalah = '', BS10_jwbKosong = '', BS10_mentah = '0', BS10_score = '0.00'
					WHERE nomorInduk='$noInd' AND nomorPeserta='$noPes'");
		}
		else {
			mysqli_query($con,"UPDATE hasilipa SET BS1_kode = NULL, BS1_jawaban = '', BS1_jwbBenar = '', BS1_jwbSalah = '', BS1_jwbKosong = '', BS1_mentah = '0', BS1_score = '0.00',
					BS2_kode = NULL, BS2_jawaban = '', BS2_jwbBenar = '', BS2_jwbSalah = '', BS2_jwbKosong = '', BS2_mentah = '0', BS2_score = '0.00',
					BS3_kode = NULL, BS3_jawaban = '', BS3_jwbBenar = '', BS3_jwbSalah = '', BS3_jwbKosong = '', BS3_mentah = '0', BS3_score = '0.00',
					BS4_kode = NULL, BS4_jawaban = '', BS4_jwbBenar = '', BS4_jwbSalah = '', BS4_jwbKosong = '', BS4_mentah = '0', BS4_score = '0.00',
					BS5_kode = NULL, BS5_jawaban = '', BS5_jwbBenar = '', BS5_jwbSalah = '', BS5_jwbKosong = '', BS5_mentah = '0', BS5_score = '0.00',
					BS6_kode = NULL, BS6_jawaban = '', BS6_jwbBenar = '', BS6_jwbSalah = '', BS6_jwbKosong = '', BS6_mentah = '0', BS6_score = '0.00',
					BS7_kode = NULL, BS7_jawaban = '', BS7_jwbBenar = '', BS7_jwbSalah = '', BS7_jwbKosong = '', BS7_mentah = '0', BS7_score = '0.00',
					BS8_kode = NULL, BS8_jawaban = '', BS8_jwbBenar = '', BS8_jwbSalah = '', BS8_jwbKosong = '', BS8_mentah = '0', BS8_score = '0.00',
					BS9_kode = NULL, BS9_jawaban = '', BS9_jwbBenar = '', BS9_jwbSalah = '', BS9_jwbKosong = '', BS9_mentah = '0', BS9_score = '0.00',
					BS10_kode = NULL, BS10_jawaban = '', BS10_jwbBenar = '', BS10_jwbSalah = '', BS10_jwbKosong = '', BS10_mentah = '0', BS10_score = '0.00'
					WHERE nomorInduk='$noInd' AND nomorPeserta='$noPes'");

			mysqli_query($con,"UPDATE hasilips SET BS1_kode = NULL, BS1_jawaban = '', BS1_jwbBenar = '', BS1_jwbSalah = '', BS1_jwbKosong = '', BS1_mentah = '0', BS1_score = '0.00',
					BS2_kode = NULL, BS2_jawaban = '', BS2_jwbBenar = '', BS2_jwbSalah = '', BS2_jwbKosong = '', BS2_mentah = '0', BS2_score = '0.00',
					BS3_kode = NULL, BS3_jawaban = '', BS3_jwbBenar = '', BS3_jwbSalah = '', BS3_jwbKosong = '', BS3_mentah = '0', BS3_score = '0.00',
					BS4_kode = NULL, BS4_jawaban = '', BS4_jwbBenar = '', BS4_jwbSalah = '', BS4_jwbKosong = '', BS4_mentah = '0', BS4_score = '0.00',
					BS5_kode = NULL, BS5_jawaban = '', BS5_jwbBenar = '', BS5_jwbSalah = '', BS5_jwbKosong = '', BS5_mentah = '0', BS5_score = '0.00',
					BS6_kode = NULL, BS6_jawaban = '', BS6_jwbBenar = '', BS6_jwbSalah = '', BS6_jwbKosong = '', BS6_mentah = '0', BS6_score = '0.00',
					BS7_kode = NULL, BS7_jawaban = '', BS7_jwbBenar = '', BS7_jwbSalah = '', BS7_jwbKosong = '', BS7_mentah = '0', BS7_score = '0.00',
					BS8_kode = NULL, BS8_jawaban = '', BS8_jwbBenar = '', BS8_jwbSalah = '', BS8_jwbKosong = '', BS8_mentah = '0', BS8_score = '0.00',
					BS9_kode = NULL, BS9_jawaban = '', BS9_jwbBenar = '', BS9_jwbSalah = '', BS9_jwbKosong = '', BS9_mentah = '0', BS9_score = '0.00',
					BS10_kode = NULL, BS10_jawaban = '', BS10_jwbBenar = '', BS10_jwbSalah = '', BS10_jwbKosong = '', BS10_mentah = '0', BS10_score = '0.00'
					WHERE nomorInduk='$noInd' AND nomorPeserta='$noPes'");
		}
	}
	else {
		mysqli_query($con,"UPDATE hasilipa SET BS1_kode = NULL, BS1_jawaban = '', BS1_jwbBenar = '', BS1_jwbSalah = '', BS1_jwbKosong = '', BS1_mentah = '0', BS1_score = '0.00',
					BS2_kode = NULL, BS2_jawaban = '', BS2_jwbBenar = '', BS2_jwbSalah = '', BS2_jwbKosong = '', BS2_mentah = '0', BS2_score = '0.00',
					BS3_kode = NULL, BS3_jawaban = '', BS3_jwbBenar = '', BS3_jwbSalah = '', BS3_jwbKosong = '', BS3_mentah = '0', BS3_score = '0.00',
					BS4_kode = NULL, BS4_jawaban = '', BS4_jwbBenar = '', BS4_jwbSalah = '', BS4_jwbKosong = '', BS4_mentah = '0', BS4_score = '0.00',
					BS5_kode = NULL, BS5_jawaban = '', BS5_jwbBenar = '', BS5_jwbSalah = '', BS5_jwbKosong = '', BS5_mentah = '0', BS5_score = '0.00',
					BS6_kode = NULL, BS6_jawaban = '', BS6_jwbBenar = '', BS6_jwbSalah = '', BS6_jwbKosong = '', BS6_mentah = '0', BS6_score = '0.00',
					BS7_kode = NULL, BS7_jawaban = '', BS7_jwbBenar = '', BS7_jwbSalah = '', BS7_jwbKosong = '', BS7_mentah = '0', BS7_score = '0.00',
					BS8_kode = NULL, BS8_jawaban = '', BS8_jwbBenar = '', BS8_jwbSalah = '', BS8_jwbKosong = '', BS8_mentah = '0', BS8_score = '0.00',
					BS9_kode = NULL, BS9_jawaban = '', BS9_jwbBenar = '', BS9_jwbSalah = '', BS9_jwbKosong = '', BS9_mentah = '0', BS9_score = '0.00',
					BS10_kode = NULL, BS10_jawaban = '', BS10_jwbBenar = '', BS10_jwbSalah = '', BS10_jwbKosong = '', BS10_mentah = '0', BS10_score = '0.00'");
		
		mysqli_query($con,"UPDATE hasilips SET BS1_kode = NULL, BS1_jawaban = '', BS1_jwbBenar = '', BS1_jwbSalah = '', BS1_jwbKosong = '', BS1_mentah = '0', BS1_score = '0.00',
					BS2_kode = NULL, BS2_jawaban = '', BS2_jwbBenar = '', BS2_jwbSalah = '', BS2_jwbKosong = '', BS2_mentah = '0', BS2_score = '0.00',
					BS3_kode = NULL, BS3_jawaban = '', BS3_jwbBenar = '', BS3_jwbSalah = '', BS3_jwbKosong = '', BS3_mentah = '0', BS3_score = '0.00',
					BS4_kode = NULL, BS4_jawaban = '', BS4_jwbBenar = '', BS4_jwbSalah = '', BS4_jwbKosong = '', BS4_mentah = '0', BS4_score = '0.00',
					BS5_kode = NULL, BS5_jawaban = '', BS5_jwbBenar = '', BS5_jwbSalah = '', BS5_jwbKosong = '', BS5_mentah = '0', BS5_score = '0.00',
					BS6_kode = NULL, BS6_jawaban = '', BS6_jwbBenar = '', BS6_jwbSalah = '', BS6_jwbKosong = '', BS6_mentah = '0', BS6_score = '0.00',
					BS7_kode = NULL, BS7_jawaban = '', BS7_jwbBenar = '', BS7_jwbSalah = '', BS7_jwbKosong = '', BS7_mentah = '0', BS7_score = '0.00',
					BS8_kode = NULL, BS8_jawaban = '', BS8_jwbBenar = '', BS8_jwbSalah = '', BS8_jwbKosong = '', BS8_mentah = '0', BS8_score = '0.00',
					BS9_kode = NULL, BS9_jawaban = '', BS9_jwbBenar = '', BS9_jwbSalah = '', BS9_jwbKosong = '', BS9_mentah = '0', BS9_score = '0.00',
					BS10_kode = NULL, BS10_jawaban = '', BS10_jwbBenar = '', BS10_jwbSalah = '', BS10_jwbKosong = '', BS10_mentah = '0', BS10_score = '0.00'");
	}

?>