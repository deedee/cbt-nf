<?php
	require_once('../koneksi_db.php');

	if ($_POST['op']!="nmSekolah" || $_POST['op']!="mtSekolah") {
		echo ('<script type="text/javascript">');
		echo ('window.location="index.php";');
		echo ('</script>');
	}

	$ds = $_POST['datasek'];

	if ($_POST['op']=="nmSekolah")
	{ mysqli_query($con,"UPDATE datasystem SET namaSekolah = '$ds'  WHERE id = 1"); }
	else if ($_POST['op']=="mtSekolah")
	{ mysqli_query($con,"UPDATE datasystem SET motoSekolah = '$ds'  WHERE id = 1"); }
?>