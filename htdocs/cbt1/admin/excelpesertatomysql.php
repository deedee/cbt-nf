<?php

	//hide All Errors:
	//error_reporting(0);
	//ini_set('display_errors', 0);

	//show All Errors:
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	date_default_timezone_set('Asia/Jakarta'); 		//set timezone
	$dateToday = date("Y")."-".date("m")."-".date("d");

	ini_set('max_execution_time', 180);  //in seconds, maximum execution time of each script
	require_once('../koneksi_db.php');

	require 'vendor/autoload.php';

	if (isset($_FILES['fileDataPesertaXLSX']['name'])) {
		$file_name = $_FILES['fileDataPesertaXLSX']['name'];
		$ext = pathinfo($file_name, PATHINFO_EXTENSION);

		//Checking the file extension, process if only the extension in xlsx
		if ($ext == "xlsx") {
			$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
			$reader->setReadDataOnly(TRUE);
			$spreadsheet = $reader->load($_FILES['fileDataPesertaXLSX']['tmp_name']);

			$worksheet = $spreadsheet->getSheet(0);
			$highestRow = $worksheet->getHighestRow();
			$highestColumn = $worksheet->getHighestColumn();

			//  Loop through each row of the worksheet in turn
			for ($row = 2; $row <= $highestRow; $row++) {
				$rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row);

				//  Insert row data array into absensiharitespeserta only if nomor Induk & nomor Peserta both are not empty
				if ($rowData[0][1]!='' && $rowData[0][2]!='') {
					$sqlpeserta = "INSERT INTO absensiharitespeserta (nomorInduk, nomorPeserta, petaSoal, nama, kelas, jurusan, shiftTes)
								   VALUES ('".$rowData[0][1]."', '".$rowData[0][2]."', '".$rowData[0][3]."', '".mysqli_escape_string($con, strtoupper($rowData[0][4]))."', '".mysqli_escape_string($con, strtoupper($rowData[0][5]))."', '".strtoupper($rowData[0][6])."', '".$rowData[0][7]."')";
					mysqli_query($con, $sqlpeserta);

					//	Insert row for absensirecord table //record IPA
					if (strtoupper($rowData[0][6])=="IPA" || strtoupper($rowData[0][6])=="IPC") {
						$sqlrecorda = "INSERT INTO absensirecord (nomorInduk, nomorPeserta, nama, kelas, jurusan)
									   VALUES ('".$rowData[0][1]."', '".$rowData[0][2]."', '".mysqli_escape_string($con, strtoupper($rowData[0][4]))."', '".mysqli_escape_string($con, strtoupper($rowData[0][5]))."', 'IPA')";
						mysqli_query($con, $sqlrecorda);
					}

					//	Insert row for absensirecord table //record IPS
					if (strtoupper($rowData[0][6])=="IPS" || strtoupper($rowData[0][6])=="IPC") {
						$sqlrecords = "INSERT INTO absensirecord (nomorInduk, nomorPeserta, nama, kelas, jurusan)
									   VALUES ('".$rowData[0][1]."', '".$rowData[0][2]."', '".mysqli_escape_string($con, strtoupper($rowData[0][4]))."', '".mysqli_escape_string($con, strtoupper($rowData[0][5]))."', 'IPS')";
						mysqli_query($con, $sqlrecords);
					}

					//  Insert row data array into aruntimer table
					$sqltimer = "INSERT INTO aruntimer (nomorInduk, nomorPeserta, nama, kelas, jurusan)
								 VALUES ('".$rowData[0][1]."', '".$rowData[0][2]."', '".mysqli_escape_string($con, strtoupper($rowData[0][4]))."', '".mysqli_escape_string($con, strtoupper($rowData[0][5]))."', '".strtoupper($rowData[0][6])."')";
					mysqli_query($con, $sqltimer);

					//	Insert row for hasil IPA and hasil IPS table
					if (strtoupper($rowData[0][6])=="IPA" || strtoupper($rowData[0][6])=="IPC") {
						//hasil IPA
						$hasilIPA = "INSERT INTO hasilipa (nomorInduk, nomorPeserta, nama, kelas)
									 VALUES ('".$rowData[0][1]."', '".$rowData[0][2]."', '".mysqli_escape_string($con, strtoupper($rowData[0][4]))."', '".mysqli_escape_string($con, strtoupper($rowData[0][5]))."')";
						mysqli_query($con, $hasilIPA);
					}

					if (strtoupper($rowData[0][6])=="IPS" || strtoupper($rowData[0][6])=="IPC") {
						//hasil IPS
						$hasilIPS = "INSERT INTO hasilips (nomorInduk, nomorPeserta, nama, kelas)
									 VALUES ('".$rowData[0][1]."', '".$rowData[0][2]."', '".mysqli_escape_string($con, strtoupper($rowData[0][4]))."', '".mysqli_escape_string($con, strtoupper($rowData[0][5]))."')";
						mysqli_query($con, $hasilIPS);
					}
				}
			}
		}
	}

	//rutin pemeriksa nomor ganda, baik untuk nomorInduk maupun nomorPeserta
	$cariLabel = mysqli_query($con, "SELECT labelno1, labelno2 FROM datasystem");
	$getLabel = mysqli_fetch_array($cariLabel);
	$label1 = $getLabel[0];
	$label2 = $getLabel[1];

	$dupData = '';
	$cariDuplikatI = mysqli_query($con, "SELECT nomorInduk, COUNT(nomorInduk) FROM absensiharitespeserta GROUP BY nomorInduk HAVING COUNT(nomorInduk) > 1");
	while ($indukGanda = mysqli_fetch_array($cariDuplikatI)) {
		$i = $indukGanda['nomorInduk'];
		$cI = $indukGanda[1];

		$dupData .= "<span style='font-family: Arial; margin-bottom:4px; display:inline-block;'> $label1 : <strong>$i</strong> - $cI record<br>";
	};
	
	$cariDuplikatP = mysqli_query($con, "SELECT nomorPeserta, COUNT(nomorPeserta) FROM absensiharitespeserta GROUP BY nomorPeserta HAVING COUNT(nomorPeserta) > 1");
	while ($pesertaGanda = mysqli_fetch_array($cariDuplikatP)) {
		$p = $pesertaGanda['nomorPeserta'];
		$cP = $pesertaGanda[1];

		$dupData .= "<span style='font-family: Arial; margin-bottom:4px; display:inline-block;'> $label2 : <strong>$p</strong> - $cP record<br>";
	};

	if (strlen($dupData)>0) {
		mysqli_query($con, "TRUNCATE TABLE absensiharitespeserta");
		mysqli_query($con, "TRUNCATE TABLE aruntimer");
		mysqli_query($con, "TRUNCATE TABLE hasilipa");
		mysqli_query($con, "TRUNCATE TABLE hasilips");

		echo '<strong><u>Data Ganda</u></strong><br>';
		echo $dupData."<br>";
		echo '<span style="font-family:arial; font-size:17px; color:red"><strong>Gagal mengimport daftar peserta ke dalam database !!</strong></span>';
	}
	else {
		echo '<span style="font-family:arial; font-size:17px;">Alhamdulillah, daftar peserta sudah diimport ke dalam database.</span>';
		mysqli_query($con, "UPDATE datasystem SET uploadPeserta='$dateToday', lastFinish='0000-00-00' WHERE id = '1'");
	}

?>