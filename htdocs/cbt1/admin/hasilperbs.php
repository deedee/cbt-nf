<?php
	//pentunjuk mendownload hasil per bid studi secara manual
	//tabelnya=hasilipa utk IPA atau hasilnya=hasilips utk IPS
	//bidstudinya=BS1 , dan sterusnya
	//contoh : hasilperbs.php?tabelnya=hasilipa&bidstudinya=BS1

	// hide All Errors:
	// error_reporting(0);
	// ini_set('display_errors', 0);

	// show All Errors:
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	ini_set('max_execution_time', 1800);  //in seconds
	require_once('../koneksi_db.php');

	require 'vendor/autoload.php';

	$tempExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load("templateExcel/TemplateHasil.xlsx");

	$BStyle = array(
	  'borders' => array(
		'right' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
		),
		'left' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
		),
		'vertical' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
		),
		'horizontal' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED
		),
		'top' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
		),
		'bottom' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
		)
	  )
	);

	$EndStyle = array(
	  'borders' => array(
		'bottom' => array(
		  'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
		)
	  )
	);

	$namatabel = $_GET['tabelnya'];
	$namabs = $_GET['bidstudinya'];
	if ($namatabel=="hasilipa") { $dataProgram="IPA"; } else if ($namatabel=="hasilips") { $dataProgram="IPS"; }

	$cariBS = mysqli_query($con,"SELECT kolomHasil, namaBidStudi FROM tabelhasil WHERE kelompok='$dataProgram' AND kolomHasil='$namabs'");
	$dapatBS = mysqli_fetch_array($cariBS);
	$BS = $dapatBS['kolomHasil'];
	$NBS = $dapatBS['namaBidStudi'];

	$cariSek = mysqli_query($con,"SELECT namaSekolah FROM datasystem");
	$dapatSek = mysqli_fetch_array($cariSek);
	$Sek = $dapatSek['namaSekolah'];

	
	function AColfromNum($colNum)	{	//mengubah nomor kolom ke bentuk abjad A, B, dst...
		return ($colNum < 26 ? chr(65+$colNum) : chr(65+floor($colNum/26)-1) . chr(65+ ($colNum % 26)));
	}
	
	function fillCell($colN, $rowN, $res)
	{
		global $tempExcel;

		if ($res == '1')
		{
			$tempExcel
				->getActiveSheet()
				->getStyle($colN.$rowN.':'.$colN.$rowN)
				->getFill()
				->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				->getStartColor()
				->setRGB('2AFF7F');
		}
		else if ($res == '0')
		{
			$tempExcel
				->getActiveSheet()
				->getStyle($colN.$rowN.':'.$colN.$rowN)
				->getFill()
				->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				->getStartColor()
				->setRGB('FB8383');
		}
		else if ($res == ' ')
		{
			$tempExcel
				->getActiveSheet()
				->getStyle($colN.$rowN.':'.$colN.$rowN)
				->getFill()
				->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				->getStartColor()
				->setRGB('BFBFBF');
		}
	}

	//ambil penamaan nomor login ssuai setting pd System
	$dtnoLogin = mysqli_query($con,"SELECT labelno1, labelno2 FROM datasystem");
	$getnoLogin = mysqli_fetch_array($dtnoLogin);
	$labelno1 = strtoupper($getnoLogin['labelno1']);
	$labelno2 = strtoupper($getnoLogin['labelno2']);

	$tempExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 12, $labelno1)
								  ->setCellValueByColumnAndRow(3, 12, $labelno2);


	$ketBS = "Bidang Studi : ".$NBS;
	if ($namatabel=='hasilipa')
		{ $klp = "Jurusan : IPA"; }
		else if ($namatabel=='hasilips')
		{ $klp = "Jurusan : IPS"; }
	$tempExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 8, $ketBS)
								  ->setCellValueByColumnAndRow(1, 9, $klp." - ".$Sek);


	$no = 0;
	$nRow = 12;

	//$cariSiswa = "SELECT * FROM $namatabel LIMIT 380, 380"; = offset 500 (dilewatkan 500 data pertama), banyaknya yg diambil 100 record : 501, 502, 503, ... 600
	//$cariSiswa = "SELECT * FROM $namatabel LIMIT 100 OFFSET 500"; = offset 500 (dilewatkan 500 data pertama), banyaknya yg diambil 100 record : 501, 502, 503, ... 600
	//$cariSiswa = "SELECT * FROM $namatabel LIMIT 400, 212";
	$cariSiswa = "SELECT * FROM $namatabel";
	$dapatSiswa = mysqli_query($con, $cariSiswa);

	while ($dataSiswa = mysqli_fetch_array($dapatSiswa))
	{
		$no++;
		$nRow++;
		$nRow++;
		
		$noU = $no;
		$noI = " ".$dataSiswa['nomorInduk'];
		$noP = " ".$dataSiswa['nomorPeserta'];
		$nm = $dataSiswa['nama'];
		$kls = $dataSiswa['kelas'];
		$jawab = $dataSiswa[$namabs.'_jawaban'];
		$mentah = $dataSiswa[$namabs.'_mentah'];
		$sc = $dataSiswa[$namabs.'_score'];

		$tempExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $nRow, $noU)
									  ->setCellValueByColumnAndRow(1, $nRow+1, $noU)
									  ->setCellValueByColumnAndRow(2, $nRow, $noI)
									  ->setCellValueByColumnAndRow(3, $nRow, $noP)
									  ->setCellValueByColumnAndRow(4, $nRow, $nm)
									  ->setCellValueByColumnAndRow(5, $nRow, $kls)
									  ->setCellValueByColumnAndRow(6, $nRow, $mentah)
									  ->setCellValueByColumnAndRow(7, $nRow, $sc);
		$jwbn = explode("|", $jawab);
		
		$b = $dataSiswa[$namabs.'_jwbBenar'];
		if ($b!='')
		{
			$b_exp = explode(",", $b);
			foreach($b_exp as $ygbenar)
			{
				$jBenar = explode("-", $ygbenar);

				$nobenar = ltrim($jBenar[1],'0');
				$isianb = ltrim($jBenar[0],'0');

				$tempExcel->getActiveSheet()->setCellValueByColumnAndRow(7+$nobenar, $nRow, '1')
											  ->setCellValueByColumnAndRow(7+$nobenar, $nRow+1, $isianb.". ".$jwbn[$isianb-1]);
				fillCell(AColfromNum(6+$nobenar), $nRow, '1');
				fillCell(AColfromNum(6+$nobenar), $nRow+1, '1');
			}
		}

		$s = $dataSiswa[$namabs.'_jwbSalah'];
		if ($s!='')
			{
			$s_exp = explode(",", $s);
			foreach($s_exp as $ygsalah)
			{
				$jSalah = explode("-", $ygsalah);

				$nosalah = ltrim($jSalah[1],'0');
				$isians = ltrim($jSalah[0],'0');
				$tempExcel->getActiveSheet()->setCellValueByColumnAndRow(7+$nosalah, $nRow, '0')
											  ->setCellValueByColumnAndRow(7+$nosalah, $nRow+1, $isians.". ".$jwbn[$isians-1]);
				fillCell(AColfromNum(6+$nosalah), $nRow, '0');
				fillCell(AColfromNum(6+$nosalah), $nRow+1, '0');
			}
		}

		$k = $dataSiswa[$namabs.'_jwbKosong'];
		if ($k!='')
		{
			$k_exp = explode(",", $k);
			foreach($k_exp as $ygkosong)
			{
				$jKosong = explode("-", $ygkosong);

				$nokosong = ltrim($jKosong[1],'0');
				$isiank = ltrim($jKosong[0],'0');
				$tempExcel->getActiveSheet()->setCellValueByColumnAndRow(7+$nokosong, $nRow, ' ')
											  ->setCellValueByColumnAndRow(7+$nokosong, $nRow+1, $isiank.". ".' ');
				fillCell(AColfromNum(6+$nokosong), $nRow, ' ');
				fillCell(AColfromNum(6+$nokosong), $nRow+1, ' ');
			}
		}
	}

	$nRow++;
	$tempExcel->getActiveSheet()->getStyle('A'.'14'.':'.AColfromNum(106).$nRow)->applyFromArray($BStyle);

	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Hasil '.$dataProgram.' - '.$NBS.'.xlsx"');
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