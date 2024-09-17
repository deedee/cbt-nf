<?php

	require_once('../koneksi_db.php');

	$cekhasiltabel = mysqli_query($con,"SELECT tabelhasil FROM datasystem WHERE id = '1'");
	$hasiltampil = mysqli_fetch_array($cekhasiltabel);
	$hdt = $hasiltampil['tabelhasil'];

	$data = array();
	$nourut = 0;

	//search Hasil IPA
	$daftarHasil = mysqli_query($con, "SELECT * FROM hasilipa");
	while ($dataHasil = mysqli_fetch_array($daftarHasil)) {
		$nourut++;
		if ($hdt=='mentah') {
			$tSc = $dataHasil['BS1_mentah']+$dataHasil['BS2_mentah']+$dataHasil['BS3_mentah']+$dataHasil['BS4_mentah']+$dataHasil['BS5_mentah']+$dataHasil['BS6_mentah']+$dataHasil['BS7_mentah']+$dataHasil['BS8_mentah']+$dataHasil['BS9_mentah']+$dataHasil['BS10_mentah'];
			$totmentah = number_format((float)$tSc, 2, '.', ''); 
			array_push($data, array('nama'=>$dataHasil['nama'],
									'kelas'=>$dataHasil['kelas'],
									'jur'=>'IPA',
									'bs1_j'=>$dataHasil['BS1_jawaban'],
									'bs1'=>$dataHasil['BS1_mentah'],
									'bs2_j'=>$dataHasil['BS2_jawaban'],
									'bs2'=>$dataHasil['BS2_mentah'],
									'bs3_j'=>$dataHasil['BS3_jawaban'],
									'bs3'=>$dataHasil['BS3_mentah'],
									'bs4_j'=>$dataHasil['BS4_jawaban'],
									'bs4'=>$dataHasil['BS4_mentah'],
									'bs5_j'=>$dataHasil['BS5_jawaban'],
									'bs5'=>$dataHasil['BS5_mentah'],
									'bs6_j'=>$dataHasil['BS6_jawaban'],
									'bs6'=>$dataHasil['BS6_mentah'],
									'bs7_j'=>$dataHasil['BS7_jawaban'],
									'bs7'=>$dataHasil['BS7_mentah'],
									'bs8_j'=>$dataHasil['BS8_jawaban'],
									'bs8'=>$dataHasil['BS8_mentah'],
									'bs9_j'=>$dataHasil['BS9_jawaban'],
									'bs9'=>$dataHasil['BS9_mentah'],
									'bs10_j'=>$dataHasil['BS10_jawaban'],
									'bs10'=>$dataHasil['BS10_mentah'],
									'totalsc'=>$totmentah,
									'id'=>$dataHasil['nomorPeserta']));
		}
		else if ($hdt=='score') {
			$tSc = $dataHasil['BS1_score']+$dataHasil['BS2_score']+$dataHasil['BS3_score']+$dataHasil['BS4_score']+$dataHasil['BS5_score']+$dataHasil['BS6_score']+$dataHasil['BS7_score']+$dataHasil['BS8_score']+$dataHasil['BS9_score']+$dataHasil['BS10_score'];
			$totscore = number_format((float)$tSc, 2, '.', ''); 
			array_push($data, array('nama'=>$dataHasil['nama'],
									'kelas'=>$dataHasil['kelas'],
									'jur'=>'IPA',
									'bs1_j'=>$dataHasil['BS1_jawaban'],
									'bs1'=>$dataHasil['BS1_score'],
									'bs2_j'=>$dataHasil['BS2_jawaban'],
									'bs2'=>$dataHasil['BS2_score'],
									'bs3_j'=>$dataHasil['BS3_jawaban'],
									'bs3'=>$dataHasil['BS3_score'],
									'bs4_j'=>$dataHasil['BS4_jawaban'],
									'bs4'=>$dataHasil['BS4_score'],
									'bs5_j'=>$dataHasil['BS5_jawaban'],
									'bs5'=>$dataHasil['BS5_score'],
									'bs6_j'=>$dataHasil['BS6_jawaban'],
									'bs6'=>$dataHasil['BS6_score'],
									'bs7_j'=>$dataHasil['BS7_jawaban'],
									'bs7'=>$dataHasil['BS7_score'],
									'bs8_j'=>$dataHasil['BS8_jawaban'],
									'bs8'=>$dataHasil['BS8_score'],
									'bs9_j'=>$dataHasil['BS9_jawaban'],
									'bs9'=>$dataHasil['BS9_score'],
									'bs10_j'=>$dataHasil['BS10_jawaban'],
									'bs10'=>$dataHasil['BS10_score'],
									'totalsc'=>$totscore,
									'id'=>$dataHasil['nomorPeserta']));
		}
	}

	//search Hasil IPS
	$daftarHasil = mysqli_query($con, "SELECT * FROM hasilips");
	while ($dataHasil = mysqli_fetch_array($daftarHasil)) {
		$nourut++;
		if ($hdt=='mentah') {
			$tSc = $dataHasil['BS1_mentah']+$dataHasil['BS2_mentah']+$dataHasil['BS3_mentah']+$dataHasil['BS4_mentah']+$dataHasil['BS5_mentah']+$dataHasil['BS6_mentah']+$dataHasil['BS7_mentah']+$dataHasil['BS8_mentah']+$dataHasil['BS9_mentah']+$dataHasil['BS10_mentah'];
			$totmentah = number_format((float)$tSc, 2, '.', ''); 
			array_push($data, array('nama'=>$dataHasil['nama'],
									'kelas'=>$dataHasil['kelas'],
									'jur'=>'IPS',
									'bs1_j'=>$dataHasil['BS1_jawaban'],
									'bs1'=>$dataHasil['BS1_mentah'],
									'bs2_j'=>$dataHasil['BS2_jawaban'],
									'bs2'=>$dataHasil['BS2_mentah'],
									'bs3_j'=>$dataHasil['BS3_jawaban'],
									'bs3'=>$dataHasil['BS3_mentah'],
									'bs4_j'=>$dataHasil['BS4_jawaban'],
									'bs4'=>$dataHasil['BS4_mentah'],
									'bs5_j'=>$dataHasil['BS5_jawaban'],
									'bs5'=>$dataHasil['BS5_mentah'],
									'bs6_j'=>$dataHasil['BS6_jawaban'],
									'bs6'=>$dataHasil['BS6_mentah'],
									'bs7_j'=>$dataHasil['BS7_jawaban'],
									'bs7'=>$dataHasil['BS7_mentah'],
									'bs8_j'=>$dataHasil['BS8_jawaban'],
									'bs8'=>$dataHasil['BS8_mentah'],
									'bs9_j'=>$dataHasil['BS9_jawaban'],
									'bs9'=>$dataHasil['BS9_mentah'],
									'bs10_j'=>$dataHasil['BS10_jawaban'],
									'bs10'=>$dataHasil['BS10_mentah'],
									'totalsc'=>$totmentah,
									'id'=>$dataHasil['nomorPeserta']));
		}
		else if ($hdt=='score') {
			$tSc = $dataHasil['BS1_score']+$dataHasil['BS2_score']+$dataHasil['BS3_score']+$dataHasil['BS4_score']+$dataHasil['BS5_score']+$dataHasil['BS6_score']+$dataHasil['BS7_score']+$dataHasil['BS8_score']+$dataHasil['BS9_score']+$dataHasil['BS10_score'];
			$totscore = number_format((float)$tSc, 2, '.', ''); 
			array_push($data, array('nama'=>$dataHasil['nama'],
									'kelas'=>$dataHasil['kelas'],
									'jur'=>'IPS',
									'bs1_j'=>$dataHasil['BS1_jawaban'],
									'bs1'=>$dataHasil['BS1_score'],
									'bs2_j'=>$dataHasil['BS2_jawaban'],
									'bs2'=>$dataHasil['BS2_score'],
									'bs3_j'=>$dataHasil['BS3_jawaban'],
									'bs3'=>$dataHasil['BS3_score'],
									'bs4_j'=>$dataHasil['BS4_jawaban'],
									'bs4'=>$dataHasil['BS4_score'],
									'bs5_j'=>$dataHasil['BS5_jawaban'],
									'bs5'=>$dataHasil['BS5_score'],
									'bs6_j'=>$dataHasil['BS6_jawaban'],
									'bs6'=>$dataHasil['BS6_score'],
									'bs7_j'=>$dataHasil['BS7_jawaban'],
									'bs7'=>$dataHasil['BS7_score'],
									'bs8_j'=>$dataHasil['BS8_jawaban'],
									'bs8'=>$dataHasil['BS8_score'],
									'bs9_j'=>$dataHasil['BS9_jawaban'],
									'bs9'=>$dataHasil['BS9_score'],
									'bs10_j'=>$dataHasil['BS10_jawaban'],
									'bs10'=>$dataHasil['BS10_score'],
									'totalsc'=>$totscore,
									'id'=>$dataHasil['nomorPeserta']));
		}
	}

	//return JSON formatted data
	echo(json_encode($data));

?>