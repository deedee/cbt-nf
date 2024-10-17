<?php
	require_once('../koneksi_db.php');
	
	$itschange = $_POST['op'];

	if ($itschange=='chPaket') {
		$kodeSoalPath = $_POST['pKodeSoal'];
		$paketNew = $_POST['newPaket'];
		
		mysqli_query($con,"UPDATE naskahsoal SET petaSoal='$paketNew' WHERE pathKodeSoal='$kodeSoalPath'");
	}
	else if ($itschange=='chDuration') {
		$kodeSoalPath = $_POST['pKodeSoal'];
		$timeDuration = $_POST['newDuration'];
		
		mysqli_query($con,"UPDATE naskahsoal SET durasi='$timeDuration' WHERE pathKodeSoal='$kodeSoalPath'");
	}

?>