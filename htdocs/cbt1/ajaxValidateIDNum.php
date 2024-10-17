<?php

	require_once('koneksi_db.php');

	/* RECEIVE VALUE */
	$validateValue = $_REQUEST['fieldValue'];
	$validateId = $_REQUEST['fieldId'];
	$validateError = "This username is already taken";
	$validateSuccess = "This username is available";

	$kolom="SELECT * FROM absensiharitespeserta WHERE nomorInduk='$validateValue' LIMIT 1";
	$ambilkolom=mysqli_query($con,$kolom);
	$isi_kolom=mysqli_fetch_array($ambilkolom);

	/* RETURN VALUE */
	$arrayToJs = array();
	$arrayToJs[0] = $validateId;

	if($isi_kolom == 0) {	// validate??
		$arrayToJs[1] = false;	// RETURN false
		echo json_encode($arrayToJs);	// RETURN ARRAY WITH Error
	}
	else {
		//for($x=0;$x<1000000;$x++){
		//if($x == 990000){
			$arrayToJs[1] = true; //// RETURN true
			echo json_encode($arrayToJs);	// RETURN ARRAY WITH Success
		//}
		//}
	}

?>