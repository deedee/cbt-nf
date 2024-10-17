<?php
require_once('../koneksi_db.php');
date_default_timezone_set('Asia/Jakarta'); 		//set timezone
/** PHPExcel_IOFactory */
	require_once '../library/PHPExcel/Classes/PHPExcel/IOFactory.php';

	if(isset($_FILES['fileDataKodeJurXLSX']['name']))
	{
		$file_name = $_FILES['fileDataKodeJurXLSX']['name'];
		$ext = pathinfo($file_name, PATHINFO_EXTENSION);

		//Checking the file extension
		if($ext == "xlsx"){

			//kosongin dulu data2 soal lama
			//mysql_query("TRUNCATE TABLE naskahsoal");
			//mysql_query("TRUNCATE TABLE tabelhasil");


			$file_name = $_FILES['fileDataKodeJurXLSX']['tmp_name'];
			$inputFileName = $file_name;

		/**********************PHPExcel Script to Read Excel File**********************/

			// Read Excel workbook
			try {
					$inputFileType = PHPExcel_IOFactory::identify($inputFileName); //Identify the file
					$objReader = PHPExcel_IOFactory::createReader($inputFileType); //Creating the reader
					$objPHPExcel = $objReader->load($inputFileName); //Loading the file
				} catch (Exception $e) {
					die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) 
					. '": ' . $e->getMessage());
				}

			// hapus dulu kode prodi lama
			mysqli_query($con,"TRUNCATE TABLE kodejurusan");	

			// Mulai baca tabel kode jurusan
			$sheet = $objPHPExcel->getSheet(0);     //Selecting sheet 0
			$highestRow = $sheet->getHighestRow();     //Getting number of rows
			$highestColumn = $sheet->getHighestColumn();     //Getting number of columns

			//  Loop through each row of the worksheet in turn
			for ($row = 2; $row <= $highestRow; $row++)
			{

				//  Read a row of data into an array
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
 
				//  Insert row data array into your database of choice here
				$sqlnaskahsoal = "INSERT INTO kodejurusan (noid, kodeprodi, namaprodi, namaptn, ptn, pstudi, kelompok)
							   VALUES ('".$rowData[0][0]."', '".$rowData[0][1]."', '".$rowData[0][2]."', '".$rowData[0][3]."', '".$rowData[0][4]."', '".$rowData[0][5]."', '".$rowData[0][6]."')";
				mysqli_query($con,$sqlnaskahsoal);

			}
		}
	}
	
	echo '<span style="font-family:arial; font-size:17px">Alhamdulillah, daftar kode jurusan sudah diimport ke dalam database.</span>';
?>