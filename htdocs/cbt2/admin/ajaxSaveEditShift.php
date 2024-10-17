<?php

	require_once('../koneksi_db.php');

	$changeID = $_POST['idChange'];
	$hr = $_POST['hari'];
	$shift_ = $_POST['shiftnya'];
	$jamnya = $_POST['jam'];
	$menitnya = $_POST['mnt'];
	$durasinya = $_POST['dur'];
	$endDur = $durasinya - 1;
	$telatnya = $_POST['tel'];

	//edit shift
	mysqli_query($con,"UPDATE shifttes SET hariKe=$hr, shift='$shift_', jamMulai=$jamnya, mntMulai=$menitnya, akhirSesi=$durasinya, bolehTelat=$telatnya, endLoginShift=$endDur  WHERE no=$changeID");

	$lihatShift = mysqli_query($con,"SELECT no, hariKe, aktifFlag, shift, jamMulai, mntMulai, bolehTelat FROM shifttes");
	while ($getShift = mysqli_fetch_array($lihatShift)) {
		$noKini = $getShift['no'];
		$hariShift = $getShift['hariKe'];
		$flagAktif = $getShift['aktifFlag'];
		$waktunyaShift = $getShift['jamMulai'].":".$getShift['mntMulai'];
		$grup = $getShift['shift'];

		$telatShift = $getShift['bolehTelat'];
		if ($telatShift==0)
		{ $lTelat=''; } else { $lTelat=' &#9749;'; }

		if ($flagAktif == 1)
		{ echo ('<option value="'.$noKini.'" selected>Shift ' .$hariShift.' - '.$waktunyaShift.' (grup '.$grup.')'.$lTelat.'</option>'); }
		else
		{ echo ('<option value="'.$noKini.'">Shift ' .$hariShift.' - '.$waktunyaShift.' (grup '.$grup.')'.$lTelat.'</option>'); }
	}

?>