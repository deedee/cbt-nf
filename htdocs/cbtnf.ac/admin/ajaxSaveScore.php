<?php
	require_once('../koneksi_db.php');

	$b = $_POST['sBenar'];
	$s = $_POST['sSalah'];
	$k = $_POST['sKosong'];
	$l = $_POST['sSkala'];

	mysqli_query($con,"UPDATE datasystem SET sB=$b, sS=$s, sK=$k, skala=$l WHERE id = 1");
?>