<?php

	//hide All Errors:
	//error_reporting(0);
	//ini_set('display_errors', 0);

	//show All Errors:
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	ini_set('max_execution_time', 180);  //in seconds, maximum execution time of each script
	require_once('../koneksi_db.php');

	require 'vendor/autoload.php';

	if(isset($_FILES['fileDataSetSoalXLSX']['name'])) {
		$file_name = $_FILES['fileDataSetSoalXLSX']['name'];
		$ext = pathinfo($file_name, PATHINFO_EXTENSION);

		//Checking the file extension
		if($ext == "xlsx"){

			//kosongin dulu data2 soal lama
			//mysql_query("TRUNCATE TABLE naskahsoal");
			//mysql_query("TRUNCATE TABLE tabelhasil");

			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
			$reader->setReadDataOnly(TRUE);
			$spreadsheet = $reader->load($_FILES['fileDataSetSoalXLSX']['tmp_name']);

			$worksheet = $spreadsheet->getSheet(0);     //Selecting sheet 1
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();

			//Loop through each row of the worksheet in turn
			for ($row = 2; $row <= $highestRow; $row++) {
				//Read a row of data into an array
				$rowData = $worksheet->rangeToArray('B' . $row . ':' . $highestColumn . $row);

				$randSeed = mt_rand(1000000,9999999);
				$randPath = $randSeed.$rowData[0][2];

				//Insert row data array into your database of choice here
				$sqlnaskahsoal = "INSERT INTO naskahsoal (kodeSoal, pathKodeSoal, petaSoal, indexBidStudi, kelompok, bidStudiTabel, namaBidStudi, singkatanBS, nomorAwalPerBS, banyakItemNomor, susunanItemSoal, kunciJawaban, durasi)
							   VALUES ('".$rowData[0][2]."', '".$randPath."', '".$rowData[0][1]."', '".$rowData[0][3]."', '".$rowData[0][0]."', '".$rowData[0][5]."', '".$rowData[0][6]."', '".$rowData[0][4]."', '".$rowData[0][7]."', '".$rowData[0][8]."', '".$rowData[0][9]."', '".$rowData[0][10]."', '".$rowData[0][11]."')";
				mysqli_query($con, $sqlnaskahsoal);
			}

			//bersihkan klo ada baris yg kosong di database naskahsoal
			mysqli_query($con, "DELETE FROM naskahsoal WHERE kodeSoal = ''");

			//Hitung total naskah soal
			$soal = mysqli_query($con, "SELECT id FROM naskahsoal");
			$totSoal = mysqli_num_rows($soal);
			$totSoal++;
			mysqli_query($con,"ALTER TABLE naskahsoal auto_increment = $totSoal");


			//baca tabel hasil
			$worksheet = $spreadsheet->getSheet(1);     //Selecting sheet 2
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();

			//Loop through each row of the worksheet in turn
			for ($row = 2; $row <= $highestRow; $row++) {
				//Read a row of data into an array
				$rowData = $worksheet->rangeToArray('B' . $row . ':' . $highestColumn . $row);

				//Insert row data array into your database of choice here
				$sqltabelhasil = "INSERT INTO tabelhasil (kolomHasil, kelompok, bidStudiTabel, namaBidStudi)
							   VALUES ('".$rowData[0][1]."', '".$rowData[0][0]."', '".$rowData[0][2]."', '".$rowData[0][3]."')";
				mysqli_query($con,$sqltabelhasil);
			}

			//bersihkan klo ada baris yg kosong di database naskahsoal
			mysqli_query($con, "DELETE FROM tabelhasil WHERE kolomhasil = ''" );
		}
	}
	
	echo '<span style="font-family:arial; font-size:17px">Alhamdulillah, set soal sudah diimport ke dalam database.</span>';
?>