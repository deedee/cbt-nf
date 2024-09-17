<?php
	require_once('koneksi_db.php');
	
	//$diKls = $_POST['k'];
	//$lok = substr($diKls,0,3);
	
	$seeAdm = mysqli_query ($con,"SELECT * FROM dataadmin WHERE nokontak != ''");
	$getAdm = mysqli_fetch_array($seeAdm);
	
	if (!$getAdm)
	{ echo " "; }
	else {
		$dataAdm = "<b>Ada Kendala ?</b> Hub. ".$getAdm['nama']." ".$getAdm['nokontak']." (wa)";
		echo $dataAdm;
	}
?>