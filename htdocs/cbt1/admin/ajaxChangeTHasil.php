<?php
	require_once('../koneksi_db.php');

	$ht = $_POST['thsl'];
	mysqli_query($con,"UPDATE datasystem SET tabelhasil = '$ht'  WHERE id = 1");
?>