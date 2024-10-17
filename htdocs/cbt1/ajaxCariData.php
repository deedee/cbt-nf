<?php

	require_once('koneksi_db.php');
	
	if (isset($_POST['kData']))
	{ $findWhat = $_POST['kData']; } else { $findWhat = 'xxx'; }
	$foundData = '';

		$keySearch = mysqli_query($con,"SELECT nomorInduk, nomorPeserta, nama, kelas FROM absensiharitespeserta WHERE nama REGEXP '$findWhat' AND (loginKey=1 OR loginFlag=0) ORDER BY nama ASC");
		if ($keySearch) {
			while ($hasilCariData = mysqli_fetch_array($keySearch))
			{
				$i = $hasilCariData['nomorInduk'];
				$p = $hasilCariData['nomorPeserta'];
				$n = $hasilCariData['nama'];
				$k = $hasilCariData['kelas'];

				$foundData .= "<span style='font-family:Arial; margin-bottom:4px; display:inline-block;'> <strong>$n</strong> $k</span><br>[$i - $p] &nbsp;&nbsp; <input type='button' value=' Pilih' class='hasilData' tagData='$i#$p#$n' style='cursor:pointer'><br><br>";
			};
		}
		else {
			$foundData .= "";
		}
	
	echo $foundData;
	
?>