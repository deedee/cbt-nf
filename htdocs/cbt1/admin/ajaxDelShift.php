<?php
	
	require_once('../koneksi_db.php');

	$delID = $_POST['idDel'];

	//cek dulu apakah yg akan dihapus sedang aktif, kalau sedang aktif nggak bisa dihapus
	$actifShift = mysqli_query($con,"SELECT * FROM shifttes WHERE aktifFlag = 1");
	$getActifShift = mysqli_fetch_array($actifShift);
	$noActifShift = $getActifShift['no'];

	//boleh delete shift jika bukan active shift
	if ($delID != $noActifShift)
	{	mysqli_query($con,"DELETE FROM shifttes WHERE no = '$delID'"); }

	//periksa apakah tinggal satu record ?, kalau iya kembalikan automatic index ke 2, biar rapi aja
	$adaRecordkah = mysqli_num_rows(mysqli_query($con,"SELECT * FROM shifttes"));
	if ($adaRecordkah == 1)
	{
		//reset AutoIncrement kembali ke 2, biar rapi
		mysqli_query($con,"ALTER TABLE shifttes auto_increment = 2");
	}

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