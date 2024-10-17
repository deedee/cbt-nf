<?php
	if ($_POST[st]!="cKey") {
		echo ('<script type="text/javascript">');
		echo ('window.location="index.php";');
		echo ('</script>');
		exit;
	}
	require_once('../koneksi_db.php');
	
	$kodeSoalPath = $_POST[pKodeSoal];
	$isiKunci = $_POST[kunciBaru];
	
	mysqli_query($con,"UPDATE naskahsoal SET kunciJawaban='$isiKunci' WHERE pathKodeSoal='$kodeSoalPath'");
?>