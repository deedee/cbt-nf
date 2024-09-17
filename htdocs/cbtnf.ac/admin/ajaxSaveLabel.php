<?php
	require_once('../koneksi_db.php');

	$lbl1 = $_POST['dataLbl1'];
	$lbl2 = $_POST['dataLbl2'];
	$lengthLbl1 = $_POST['nLbl1'];
	$lengthLbl2 = $_POST['nLbl2'];

	mysqli_query($con,"UPDATE datasystem SET labelno1='$lbl1', labelno2='$lbl2', nlabel1='$lengthLbl1', nlabel2='$lengthLbl2' WHERE id=1");
?>