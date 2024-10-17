<?php
	require_once('../koneksi_db.php');

	$noS = $_POST['idShiftNow'];
	
	$JsonArray = array();

	$lihatShift = mysqli_query($con,"SELECT hariKe,shift,jamMulai,mntMulai,akhirSesi,bolehTelat FROM shifttes WHERE no=$noS");
	while ($getShift = mysqli_fetch_array($lihatShift))
	{
		$JsonArray[0] = $getShift['hariKe'];
		$JsonArray[1] = $getShift['shift'];
		$JsonArray[2] = $getShift['jamMulai'];
		$JsonArray[3] = $getShift['mntMulai'];
		$JsonArray[4] = $getShift['akhirSesi'];
		$JsonArray[5] = $getShift['bolehTelat'];
		
		echo json_encode($JsonArray);
	}
	
?>