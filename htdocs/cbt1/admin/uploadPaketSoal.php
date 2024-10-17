<?php
//show All Errors:
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once('../koneksi_db.php');

//mengubah nama path kode soal supaya tdk mudah diintip
$thisPath = mysqli_query($con, "SELECT pathKodeSoal, kodeSoal FROM naskahsoal");
$cekPath = mysqli_fetch_array($thisPath);
if (empty($cekPath)) {
	$_SESSION['uploadError'] = 'Silakan mengimport DB Set Soal terlebih dahulu.';
	exit;
}

if (isset($_FILES["zip_file"]["name"])) {
	$zipname = $_FILES["zip_file"]["name"];
	$source = $_FILES["zip_file"]["tmp_name"];
	
	$name = explode(".", $zipname);
	$continue = strtolower($name[1]) == 'zip' ? true : false;
	if (!$continue) {
		$_SESSION['uploadError'] = 'Maaf, file yang Anda upload tidak berformat zip. Silakan coba lagi !!';
		exit;
	}
	else {
		//array_map('unlink', glob("../images/soal/*"));

		$source_zip = "../images/soal".$zipname;
		if (move_uploaded_file($source, $source_zip)) {
			$zip = new ZipArchive();
			$x = $zip->open($source_zip);
			if ($x === true) {
				$zip->extractTo("../images/soal/");
				$zip->close();

				unlink($source_zip);

				//mengubah nama path kode soal supaya tdk mudah ditebak pathnya
				$thisPath = mysqli_query($con, "SELECT pathKodeSoal, kodeSoal FROM naskahsoal");
				while ($dapatPath = mysqli_fetch_array($thisPath)) {
					$PKS = $dapatPath['pathKodeSoal'];
					$sourcePath = "../images/soal/".$dapatPath['kodeSoal'];

					if (file_exists($sourcePath)) {
						$destPath = "../images/soal/".$PKS;
						rename($sourcePath, $destPath);

						$oggFiles = "";
						foreach (glob($destPath."/*") as $imgFilesPath) {
							$imgFlStruc = explode("/", $imgFilesPath);
							$curFile = $imgFlStruc[4];
							$newname = $curFile;

							//hilangkan jika ada 0 di awal nama filenya
							$newname = ltrim($curFile,"0");

							if (substr($newname, -3)=="PNG") { $newname = substr($newname,0,strlen($newname)-4).".png"; }
							else if (substr($newname, -3)=="OGG") { $newname = substr($newname,0,strlen($newname)-4).".ogg"; }

							if ($newname!=$curFile) {
								$newpathname = $imgFlStruc[0]."/".$imgFlStruc[1]."/".$imgFlStruc[2]."/".$imgFlStruc[3]."/".$newname;
								rename($imgFilesPath, $newpathname);
							}

							//catat utk ekstension file .ogg
							if (substr($newname, -3)=="ogg")
							{ $oggFiles .= "#".$newname."#"; }
						}

						if ($oggFiles != "")
						{ mysqli_query($con, "UPDATE naskahsoal SET audioFiles = '$oggFiles'  WHERE pathKodeSoal = '$PKS'"); }
					}
				}
			}

			$_SESSION['uploadError'] = 'Alhamdulillah, paket soal sudah diupload';
		}
		else
		{ $_SESSION['uploadError'] = 'Sayang sekali, paket soal GAGAL diupload !!'; }
	}
}

?>
