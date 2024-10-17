<?php
	require_once('../koneksi_db.php');

	$hr = $_POST['hari'];
	$shift = $_POST['shiftnya'];
	$jamnya = $_POST['jam'];
	$menitnya = $_POST['mnt'];
	$durasinya = $_POST['dur'];
	$endDur = $durasinya - 1;
	$telatnya = $_POST['tel'];

	//tambah shift baru
	$shiftBaru = "INSERT INTO shifttes (hariKe, aktifFlag, shift, jamMulai, mntMulai, akhirSesi, startLoginShift, endLoginShift,bolehTelat)
				  VALUES ($hr, 0, '$shift', $jamnya, $menitnya, $durasinya, 5, $endDur,$telatnya)";
	$tambahshiftBaru = mysqli_query($con,$shiftBaru);
	
	$lihatShift = mysqli_query($con,"SELECT * FROM shifttes");
	while ($getShift = mysqli_fetch_array($lihatShift))
	{
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