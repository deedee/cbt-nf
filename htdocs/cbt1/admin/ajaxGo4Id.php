<?php

	require_once('../koneksi_db.php');
	
	$searchNIS = $_POST['nisDT'];
	$searchNOP = $_POST['nopDT'];
	
	$JsonArray = array();

	$ambilSee = mysqli_query($con,"SELECT id, nomorInduk, nomorPeserta, petaSoal, nama, kelas, jurusan, nomorHP, alamatEmail, shiftTes, msgx_n FROM absensiharitespeserta WHERE nomorInduk='$searchNIS' AND nomorPeserta='$searchNOP' LIMIT 1");
	$dataLogin = mysqli_fetch_array($ambilSee);

	$nid = $dataLogin['id'];
	if ($nid<10) { $nid='00'.$nid;} else if ($nid<100) { $nid='0'.$nid;}
	$nam = $dataLogin['nama'];
	$nis = $dataLogin['nomorInduk'];
	$nop = $dataLogin['nomorPeserta'];
	$nps = $dataLogin['petaSoal'];
	$kls = $dataLogin['kelas'];
	$jur = $dataLogin['jurusan'];
	$noHP = $dataLogin['nomorHP'];
	$alEm = $dataLogin['alamatEmail'];
	$sts = $dataLogin['shiftTes'];
	$msg_n = $dataLogin['msgx_n'];

	$JsonArray[0] = $nid;
	$JsonArray[1] = $nps;
	$JsonArray[2] = $kls;
	$JsonArray[3] = $nam;
	$JsonArray[4] = $nis;
	$JsonArray[5] = $nop;
	$JsonArray[6] = $sts;
	$JsonArray[7] = $msg_n;
	$JsonArray[8] = $noHP;
	$JsonArray[9] = $alEm;
	$JsonArray[10] = $jur;
	
	echo json_encode($JsonArray);
	
?>