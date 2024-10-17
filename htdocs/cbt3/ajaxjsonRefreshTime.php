<?php

	require_once('koneksi_db.php');

	$JsonArray = array();

	$lihatShift = mysqli_query($con,"SELECT shift, jamMulai, mntMulai, akhirSesi, startLoginShift FROM shifttes WHERE aktifFlag=1 LIMIT 1");
	$getShft = mysqli_fetch_array($lihatShift);

	$JsonArray[0] = $getShft['shift'];
	$JsonArray[1] = $getShft['jamMulai'];
	$JsonArray[2] = $getShft['mntMulai'];
	$JsonArray[3] = $getShft['akhirSesi'];
	$JsonArray[4] = $getShft['startLoginShift'];

	echo json_encode($JsonArray);

?>