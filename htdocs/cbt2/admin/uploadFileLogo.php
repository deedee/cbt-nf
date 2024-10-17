<?php

	//source from http://hayageek.com/docs/jquery-upload-file.php

	session_start();

	require_once('../koneksi_db.php');
	$output_dir = "../images/";
	$pesanError = "";
	$_SESSION['uploadError'] = "";

	if(isset($_FILES["myfile"]))
	{
		$ret = array();

		$error = $_FILES["myfile"]["error"];

		$fileName = $_FILES["myfile"]["name"];

		$logoImg=$_FILES["myfile"]["tmp_name"];
		$dimFoto=getimagesize($logoImg);
		$LFoto=$dimFoto[0];
		$TFoto=$dimFoto[1];

		if ($LFoto>210 || $TFoto>210)
		{ $pesanError = "Error !! Ukuran logo melebihi 210 x 210 pixel !!"; }

		if ($pesanError=="") {
			//no error !!, upload file
			move_uploaded_file($logoImg, $output_dir.$fileName);

			$renOld = $output_dir.$fileName;
			$newName = "logo_sekolah.png";
			$renNew = $output_dir.$newName;
			rename($renOld, $renNew);

			$ret[$fileName]= $output_dir.$fileName;
			$_SESSION['uploadError'] .= "Sukses mengupload $fileName !!";
		}
		else
		{ $_SESSION['uploadError'] .= $pesanError; }

		echo json_encode($ret);
	}

?>