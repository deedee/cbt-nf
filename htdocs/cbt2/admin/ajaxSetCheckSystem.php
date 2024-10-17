<?php

	require_once('../koneksi_db.php');

	$currC = $_POST['op'];			//current control

	if (isset($_POST['setNow']))
	{ $nSet = $_POST['setNow']; } else { $nSet = ""; }	//nowSet

	if ($currC == 'setShowSearch') {
		if ($nSet == "checked")
		{ mysqli_query($con,"UPDATE datasystem SET searchNama = 1  WHERE id = 1"); }
		else
		{ mysqli_query($con,"UPDATE datasystem SET searchNama = 0  WHERE id = 1"); }
	}

	else if ($currC == 'setInp2pw') {
		if ($nSet == "checked")
		{ mysqli_query($con,"UPDATE datasystem SET label2password = 1  WHERE id = 1"); }
		else
		{ mysqli_query($con,"UPDATE datasystem SET label2password = 0  WHERE id = 1"); }
	}

	else if ($currC == 'setInpHP') {
		if ($nSet == "checked")
		{ mysqli_query($con,"UPDATE datasystem SET tampilHP = 1  WHERE id = 1"); }
		else
		{ mysqli_query($con,"UPDATE datasystem SET tampilHP = 0  WHERE id = 1"); }
	}

	else if ($currC == 'setInpEmail') {
		if ($nSet == "checked")
		{ mysqli_query($con,"UPDATE datasystem SET tampilEmail = 1  WHERE id = 1"); }
		else
		{ mysqli_query($con,"UPDATE datasystem SET tampilEmail = 0  WHERE id = 1"); }
	}

	else if ($currC == 'setInpDoB') {
		if ($nSet == "checked")
		{ mysqli_query($con,"UPDATE datasystem SET tampilDoB = 1  WHERE id = 1"); }
		else
		{ mysqli_query($con,"UPDATE datasystem SET tampilDoB = 0  WHERE id = 1"); }
	}

	else if ($currC == 'setPilJur') {
		if ($nSet == "checked")
		{ mysqli_query($con,"UPDATE datasystem SET tampilPilJur = 1  WHERE id = 1"); }
		else
		{ mysqli_query($con,"UPDATE datasystem SET tampilPilJur = 0  WHERE id = 1"); }
	}

	else if ($currC == 'changeNOpsi')
	{ mysqli_query($con,"UPDATE datasystem SET nOpsi = $nSet  WHERE id = 1"); }

	else if ($currC == 'setErasableOpsi') {
		if ($nSet == "checked")
		{ mysqli_query($con,"UPDATE datasystem SET opsiErasable = 1  WHERE id = 1"); }
		else
		{ mysqli_query($con,"UPDATE datasystem SET opsiErasable = 0  WHERE id = 1"); }
	}

	else if ($currC == 'setShowScore') {
		if ($nSet == "checked")
		{ mysqli_query($con,"UPDATE datasystem SET tampilSkor = 1  WHERE id = 1"); }
		else
		{ mysqli_query($con,"UPDATE datasystem SET tampilSkor = 0  WHERE id = 1"); }
	}

?>