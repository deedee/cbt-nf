<?php

	require_once('../koneksi_db.php');

	if ($_FILES["media_file"]["name"])
	{
		$filename = $_FILES["media_file"]["name"];
		$source = $_FILES["media_file"]["tmp_name"];
		
		$target_path = "../admedia/".$filename;
		move_uploaded_file($source, $target_path);
	}

?>
