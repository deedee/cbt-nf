<?php

	// hide All Errors:
	// error_reporting(0);
	// ini_set('display_errors', 0);

	// show All Errors:
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	ini_set('max_execution_time', 1800);  //in seconds
	require_once('../koneksi_db.php');

	require 'vendor/autoload.php';

	$tempExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load("templateExcel/TemplateHasilMentah.xlsx");

	$BStyle = array(
	  'borders' => array(
		'right' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
		),
		'left' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
		),
		'vertical' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
		),
		'horizontal' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
		),
		'bottom' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
		)
	  )
	);
	
	$EndStyle = array(
	  'borders' => array(
		'bottom' => array(
		  'borderStyle' =>\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
		)
	  )
	);

	//ambil penamaan nomor login ssuai setting pd System
	$dtnoLogin = mysqli_query($con, "SELECT labelno1, labelno2 FROM datasystem");
	$getnoLogin = mysqli_fetch_array($dtnoLogin);
	$labelno1 = strtoupper($getnoLogin['labelno1']);
	$labelno2 = strtoupper($getnoLogin['labelno2']);

	foreach (array("IPS", "IPA") as $klpJur) {
		$tempExcel->getSheetByName($klpJur)->setCellValueByColumnAndRow(2, 12, $labelno1)
									  ->setCellValueByColumnAndRow(3, 12, $labelno2);

		$colNmBS = 12;
		$cariNamaBS = mysqli_query($con, "SELECT kolomHasil, bidStudiTabel, namaBidStudi FROM tabelhasil WHERE kelompok='".$klpJur."' ORDER BY kolomHasil");
		while ($getNamaBS = mysqli_fetch_array($cariNamaBS)) {
			$colNmBS++;
			$tempExcel->getSheetByName($klpJur)->setCellValueByColumnAndRow($colNmBS, 12, $getNamaBS['namaBidStudi'].' ('.$getNamaBS['bidStudiTabel'].')');
		}

		$nRow = 13;
		$dapatSiswa = mysqli_query($con, "SELECT nomorInduk, nomorPeserta, piljur1, piljur2, piljur3, nama, kelas, nomorHP, alamatEmail, tglLahir FROM absensiharitespeserta WHERE jurusan='".$klpJur."' OR jurusan='IPC'");

		while ($dataSiswa = mysqli_fetch_array($dapatSiswa)) {
			$nI = $dataSiswa['nomorInduk'];
			$nP = $dataSiswa['nomorPeserta'];
			$noI = " ".$nI;
			$noP = " ".$nP;

			$nma = $dataSiswa['nama'];
			$kls = $dataSiswa['kelas'];
			$nHP = $dataSiswa['nomorHP'];
			$ema = $dataSiswa['alamatEmail'];
			$dob = $dataSiswa['tglLahir'];
			$pj1 = $dataSiswa['piljur1'];
			$pj2 = $dataSiswa['piljur2'];
			$pj3 = $dataSiswa['piljur3'];

			$cariContents = mysqli_query($con, "SELECT jurusan, ordAnswer1, ordAnswer2, ordAnswer3, ordAnswer4, ordAnswer5, ordAnswer6, ordAnswer7, ordAnswer8, ordAnswer9, ordAnswer10 FROM absensirecord WHERE nomorInduk='$nI' AND nomorPeserta='$nP' AND jurusan='".$klpJur."'");

			while ($dapatContents = mysqli_fetch_array($cariContents)) {
				$nRow++;
				$noU = $nRow-13;

				$klp = $dapatContents['jurusan'];

				$ord1 = $dapatContents['ordAnswer1'];
				$ord2 = $dapatContents['ordAnswer2'];
				$ord3 = $dapatContents['ordAnswer3'];
				$ord4 = $dapatContents['ordAnswer4'];
				$ord5 = $dapatContents['ordAnswer5'];
				$ord6 = $dapatContents['ordAnswer6'];
				$ord7 = $dapatContents['ordAnswer7'];
				$ord8 = $dapatContents['ordAnswer8'];
				$ord9 = $dapatContents['ordAnswer9'];
				$ord10 = $dapatContents['ordAnswer10'];

				$tempExcel->getSheetByName($klpJur)->setCellValueByColumnAndRow(1, $nRow, $noU)
											  ->setCellValueByColumnAndRow(2, $nRow, $noI)
											  ->setCellValueByColumnAndRow(3, $nRow, $noP)
											  ->setCellValueByColumnAndRow(4, $nRow, $nma)
											  ->setCellValueByColumnAndRow(5, $nRow, $kls)
											  ->setCellValueByColumnAndRow(6, $nRow, $klp)
											  ->setCellValueByColumnAndRow(7, $nRow, $nHP)
											  ->setCellValueByColumnAndRow(8, $nRow, $ema)
											  ->setCellValueByColumnAndRow(9, $nRow, $dob)
											  ->setCellValueByColumnAndRow(10, $nRow, $pj1)
											  ->setCellValueByColumnAndRow(11, $nRow, $pj2)
											  ->setCellValueByColumnAndRow(12, $nRow, $pj3)

											  ->setCellValueByColumnAndRow(13, $nRow, $ord1)
											  ->setCellValueByColumnAndRow(14, $nRow, $ord2)
											  ->setCellValueByColumnAndRow(15, $nRow, $ord3)
											  ->setCellValueByColumnAndRow(16, $nRow, $ord4)
											  ->setCellValueByColumnAndRow(17, $nRow, $ord5)
											  ->setCellValueByColumnAndRow(18, $nRow, $ord6)
											  ->setCellValueByColumnAndRow(19, $nRow, $ord7)
											  ->setCellValueByColumnAndRow(20, $nRow, $ord8)
											  ->setCellValueByColumnAndRow(21, $nRow, $ord9)
											  ->setCellValueByColumnAndRow(22, $nRow, $ord10);
			}
		}

		$tempExcel->getSheetByName($klpJur)->getStyle('A'.'14'.':V'.$nRow)->applyFromArray($BStyle);
	}


	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Hasil Mentah TO CBT.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($tempExcel);
	$writer->save('php://output');

?>