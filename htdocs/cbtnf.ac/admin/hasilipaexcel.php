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

	$tempExcel = \PhpOffice\PhpSpreadsheet\IOFactory::load("templateExcel/TemplateHasilIPA.xlsx");

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

	$tempExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 12, $labelno1)
								  ->setCellValueByColumnAndRow(3, 12, $labelno2);


	//cari ganti nama bidang studi IPA BS->
	$BSname = array();
	for ($i=1; $i < 11; $i++) { 
		$BSname['BS'.$i]='';
	}

	$dapatBS = mysqli_query($con, "SELECT * FROM tabelhasil WHERE kelompok='IPA'");
	while ($dataBS = mysqli_fetch_array($dapatBS))
	{
		$indexBS = $dataBS['kolomHasil'];
		$nBS = $dataBS['namaBidStudi'];
		$BSname[$indexBS]=$nBS;
	}

	$tempExcel->getActiveSheet()->setCellValueByColumnAndRow(12, 12, $BSname['BS1'])
								  ->setCellValueByColumnAndRow(20, 12, $BSname['BS2'])
								  ->setCellValueByColumnAndRow(28, 12, $BSname['BS3'])
								  ->setCellValueByColumnAndRow(36, 12, $BSname['BS4'])
								  ->setCellValueByColumnAndRow(44, 12, $BSname['BS5'])
								  ->setCellValueByColumnAndRow(52, 12, $BSname['BS6'])
								  ->setCellValueByColumnAndRow(60, 12, $BSname['BS7'])
								  ->setCellValueByColumnAndRow(68, 12, $BSname['BS8'])
								  ->setCellValueByColumnAndRow(76, 12, $BSname['BS9'])
								  ->setCellValueByColumnAndRow(84, 12, $BSname['BS10']);

	$dapatSiswa = mysqli_query($con, "SELECT * FROM hasilipa");

	$no = 0;
	$nRow = 13;

	while ($dataSiswa = mysqli_fetch_array($dapatSiswa))
	{
		$no++;
		$nRow++;
		$noU = $no;
		$nI = $dataSiswa['nomorInduk'];
		$nP = $dataSiswa['nomorPeserta'];
		$noI = " ".$nI;
		$noP = " ".$nP;
		$nm = $dataSiswa['nama'];
		$kls = $dataSiswa['kelas'];

		//cari nohp di absensiharitespeserta
		$carihp = "SELECT piljur1, piljur2, piljur3, nomorHP, alamatEmail, tglLahir FROM absensiharitespeserta WHERE nomorInduk='$nI' AND nomorPeserta='$nP' LIMIT 1";
		$dapathp = mysqli_fetch_array(mysqli_query($con, $carihp));

		if ($dapathp) {
			$pj1 = $dapathp['piljur1'];
			$pj2 = $dapathp['piljur2'];
			$pj3 = $dapathp['piljur3'];

			$noHP = $dapathp['nomorHP'];
			$email = $dapathp['alamatEmail'];
			$dob = $dapathp['tglLahir'];
		}

		$namaBS1 = $BSname['BS1'];
		$m1 = $dataSiswa['BS1_mentah'];
		$sc1 = $dataSiswa['BS1_score'];
		$b1 = $dataSiswa['BS1_jwbBenar'];
		$s1 = $dataSiswa['BS1_jwbSalah'];
		$k1 = $dataSiswa['BS1_jwbKosong'];
		$jb1 = round((strlen($b1)+1)/6,0);
		$js1 = round((strlen($s1)+1)/6,0);
		$jk1 = round((strlen($k1)+1)/6,0);

		$namaBS2 = $BSname['BS2'];
		$m2 = $dataSiswa['BS2_mentah'];
		$sc2 = $dataSiswa['BS2_score'];
		$b2 = $dataSiswa['BS2_jwbBenar'];
		$s2 = $dataSiswa['BS2_jwbSalah'];
		$k2 = $dataSiswa['BS2_jwbKosong'];
		$jb2 = round((strlen($b2)+1)/6,0);
		$js2 = round((strlen($s2)+1)/6,0);
		$jk2 = round((strlen($k2)+1)/6,0);

		$namaBS3 = $BSname['BS3'];
		$m3 = $dataSiswa['BS3_mentah'];
		$sc3 = $dataSiswa['BS3_score'];
		$b3 = $dataSiswa['BS3_jwbBenar'];
		$s3 = $dataSiswa['BS3_jwbSalah'];
		$k3 = $dataSiswa['BS3_jwbKosong'];
		$jb3 = round((strlen($b3)+1)/6,0);
		$js3 = round((strlen($s3)+1)/6,0);
		$jk3 = round((strlen($k3)+1)/6,0);

		$namaBS4 = $BSname['BS4'];
		$m4 = $dataSiswa['BS4_mentah'];
		$sc4 = $dataSiswa['BS4_score'];
		$b4 = $dataSiswa['BS4_jwbBenar'];
		$s4 = $dataSiswa['BS4_jwbSalah'];
		$k4 = $dataSiswa['BS4_jwbKosong'];
		$jb4 = round((strlen($b4)+1)/6,0);
		$js4 = round((strlen($s4)+1)/6,0);
		$jk4 = round((strlen($k4)+1)/6,0);

		$namaBS5 = $BSname['BS5'];
		$m5 = $dataSiswa['BS5_mentah'];
		$sc5 = $dataSiswa['BS5_score'];
		$b5 = $dataSiswa['BS5_jwbBenar'];
		$s5 = $dataSiswa['BS5_jwbSalah'];
		$k5 = $dataSiswa['BS5_jwbKosong'];
		$jb5 = round((strlen($b5)+1)/6,0);
		$js5 = round((strlen($s5)+1)/6,0);
		$jk5 = round((strlen($k5)+1)/6,0);

		$namaBS6 = $BSname['BS6'];
		$m6 = $dataSiswa['BS6_mentah'];
		$sc6 = $dataSiswa['BS6_score'];
		$b6 = $dataSiswa['BS6_jwbBenar'];
		$s6 = $dataSiswa['BS6_jwbSalah'];
		$k6 = $dataSiswa['BS6_jwbKosong'];
		$jb6 = round((strlen($b6)+1)/6,0);
		$js6 = round((strlen($s6)+1)/6,0);
		$jk6 = round((strlen($k6)+1)/6,0);

		$namaBS7 = $BSname['BS7'];
		$m7 = $dataSiswa['BS7_mentah'];
		$sc7 = $dataSiswa['BS7_score'];
		$b7 = $dataSiswa['BS7_jwbBenar'];
		$s7 = $dataSiswa['BS7_jwbSalah'];
		$k7 = $dataSiswa['BS7_jwbKosong'];
		$jb7 = round((strlen($b7)+1)/6,0);
		$js7 = round((strlen($s7)+1)/6,0);
		$jk7 = round((strlen($k7)+1)/6,0);

		$namaBS8 = $BSname['BS8'];
		$m8 = $dataSiswa['BS8_mentah'];
		$sc8 = $dataSiswa['BS8_score'];
		$b8 = $dataSiswa['BS8_jwbBenar'];
		$s8 = $dataSiswa['BS8_jwbSalah'];
		$k8 = $dataSiswa['BS8_jwbKosong'];
		$jb8 = round((strlen($b8)+1)/6,0);
		$js8 = round((strlen($s8)+1)/6,0);
		$jk8 = round((strlen($k8)+1)/6,0);

		$namaBS9 = $BSname['BS9'];
		$m9 = $dataSiswa['BS9_mentah'];
		$sc9 = $dataSiswa['BS9_score'];
		$b9 = $dataSiswa['BS9_jwbBenar'];
		$s9 = $dataSiswa['BS9_jwbSalah'];
		$k9 = $dataSiswa['BS9_jwbKosong'];
		$jb9 = round((strlen($b9)+1)/6,0);
		$js9 = round((strlen($s9)+1)/6,0);
		$jk9 = round((strlen($k9)+1)/6,0);

		$namaBS10 = $BSname['BS10'];
		$m10 = $dataSiswa['BS10_mentah'];
		$sc10 = $dataSiswa['BS10_score'];
		$b10 = $dataSiswa['BS10_jwbBenar'];
		$s10 = $dataSiswa['BS10_jwbSalah'];
		$k10 = $dataSiswa['BS10_jwbKosong'];
		$jb10 = round((strlen($b10)+1)/6,0);
		$js10 = round((strlen($s10)+1)/6,0);
		$jk10 = round((strlen($k10)+1)/6,0);

		$tempExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $nRow, $noU)
									  ->setCellValueByColumnAndRow(2, $nRow, $noI)
									  ->setCellValueByColumnAndRow(3, $nRow, $noP)
									  ->setCellValueByColumnAndRow(4, $nRow, $nm)
									  ->setCellValueByColumnAndRow(5, $nRow, $kls)
									  ->setCellValueByColumnAndRow(6, $nRow, $noHP)
									  ->setCellValueByColumnAndRow(7, $nRow, $email)
									  ->setCellValueByColumnAndRow(8, $nRow, $dob)

									  ->setCellValueByColumnAndRow(9, $nRow, $pj1)
									  ->setCellValueByColumnAndRow(10, $nRow, $pj2)
									  ->setCellValueByColumnAndRow(11, $nRow, $pj3)

									  ->setCellValueByColumnAndRow(12, $nRow, $jb1)
									  ->setCellValueByColumnAndRow(13, $nRow, $js1)
									  ->setCellValueByColumnAndRow(14, $nRow, $jk1)
									  ->setCellValueByColumnAndRow(15, $nRow, $m1)
									  ->setCellValueByColumnAndRow(16, $nRow, $sc1)
									  ->setCellValueByColumnAndRow(17, $nRow, $b1)
									  ->setCellValueByColumnAndRow(18, $nRow, $s1)
									  ->setCellValueByColumnAndRow(19, $nRow, $k1)

									  ->setCellValueByColumnAndRow(20, $nRow, $jb2)
									  ->setCellValueByColumnAndRow(21, $nRow, $js2)
									  ->setCellValueByColumnAndRow(22, $nRow, $jk2)
									  ->setCellValueByColumnAndRow(23, $nRow, $m2)
									  ->setCellValueByColumnAndRow(24, $nRow, $sc2)
									  ->setCellValueByColumnAndRow(25, $nRow, $b2)
									  ->setCellValueByColumnAndRow(26, $nRow, $s2)
									  ->setCellValueByColumnAndRow(27, $nRow, $k2)

									  ->setCellValueByColumnAndRow(28, $nRow, $jb3)
									  ->setCellValueByColumnAndRow(29, $nRow, $js3)
									  ->setCellValueByColumnAndRow(30, $nRow, $jk3)
									  ->setCellValueByColumnAndRow(31, $nRow, $m3)
									  ->setCellValueByColumnAndRow(32, $nRow, $sc3)
									  ->setCellValueByColumnAndRow(33, $nRow, $b3)
									  ->setCellValueByColumnAndRow(34, $nRow, $s3)
									  ->setCellValueByColumnAndRow(35, $nRow, $k3)

									  ->setCellValueByColumnAndRow(36, $nRow, $jb4)
									  ->setCellValueByColumnAndRow(37, $nRow, $js4)
									  ->setCellValueByColumnAndRow(38, $nRow, $jk4)
									  ->setCellValueByColumnAndRow(39, $nRow, $m4)
									  ->setCellValueByColumnAndRow(40, $nRow, $sc4)
									  ->setCellValueByColumnAndRow(41, $nRow, $b4)
									  ->setCellValueByColumnAndRow(42, $nRow, $s4)
									  ->setCellValueByColumnAndRow(43, $nRow, $k4)

									  ->setCellValueByColumnAndRow(44, $nRow, $jb5)
									  ->setCellValueByColumnAndRow(45, $nRow, $js5)
									  ->setCellValueByColumnAndRow(46, $nRow, $jk5)
									  ->setCellValueByColumnAndRow(47, $nRow, $m5)
									  ->setCellValueByColumnAndRow(48, $nRow, $sc5)
									  ->setCellValueByColumnAndRow(49, $nRow, $b5)
									  ->setCellValueByColumnAndRow(50, $nRow, $s5)
									  ->setCellValueByColumnAndRow(51, $nRow, $k5)

									  ->setCellValueByColumnAndRow(52, $nRow, $jb6)
									  ->setCellValueByColumnAndRow(53, $nRow, $js6)
									  ->setCellValueByColumnAndRow(54, $nRow, $jk6)
									  ->setCellValueByColumnAndRow(55, $nRow, $m6)
									  ->setCellValueByColumnAndRow(56, $nRow, $sc6)
									  ->setCellValueByColumnAndRow(57, $nRow, $b6)
									  ->setCellValueByColumnAndRow(58, $nRow, $s6)
									  ->setCellValueByColumnAndRow(59, $nRow, $k6)

									  ->setCellValueByColumnAndRow(60, $nRow, $jb7)
									  ->setCellValueByColumnAndRow(61, $nRow, $js7)
									  ->setCellValueByColumnAndRow(62, $nRow, $jk7)
									  ->setCellValueByColumnAndRow(63, $nRow, $m7)
									  ->setCellValueByColumnAndRow(64, $nRow, $sc7)
									  ->setCellValueByColumnAndRow(65, $nRow, $b7)
									  ->setCellValueByColumnAndRow(66, $nRow, $s7)
									  ->setCellValueByColumnAndRow(67, $nRow, $k7)

									  ->setCellValueByColumnAndRow(68, $nRow, $jb8)
									  ->setCellValueByColumnAndRow(69, $nRow, $js8)
									  ->setCellValueByColumnAndRow(70, $nRow, $jk8)
									  ->setCellValueByColumnAndRow(71, $nRow, $m8)
									  ->setCellValueByColumnAndRow(72, $nRow, $sc8)
									  ->setCellValueByColumnAndRow(73, $nRow, $b8)
									  ->setCellValueByColumnAndRow(74, $nRow, $s8)
									  ->setCellValueByColumnAndRow(75, $nRow, $k8)

									  ->setCellValueByColumnAndRow(76, $nRow, $jb9)
									  ->setCellValueByColumnAndRow(77, $nRow, $js9)
									  ->setCellValueByColumnAndRow(78, $nRow, $jk9)
									  ->setCellValueByColumnAndRow(79, $nRow, $m9)
									  ->setCellValueByColumnAndRow(80, $nRow, $sc9)
									  ->setCellValueByColumnAndRow(81, $nRow, $b9)
									  ->setCellValueByColumnAndRow(82, $nRow, $s9)
									  ->setCellValueByColumnAndRow(83, $nRow, $k9)

									  ->setCellValueByColumnAndRow(84, $nRow, $jb10)
									  ->setCellValueByColumnAndRow(85, $nRow, $js10)
									  ->setCellValueByColumnAndRow(86, $nRow, $jk10)
									  ->setCellValueByColumnAndRow(87, $nRow, $m10)
									  ->setCellValueByColumnAndRow(88, $nRow, $sc10)
									  ->setCellValueByColumnAndRow(89, $nRow, $b10)
									  ->setCellValueByColumnAndRow(90, $nRow, $s10)
									  ->setCellValueByColumnAndRow(91, $nRow, $k10);
	}

	$tempExcel->getActiveSheet()->getStyle('A'.'14'.':CM'.$nRow)->applyFromArray($BStyle);

	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Hasil TO CBT - IPA.xlsx"');
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