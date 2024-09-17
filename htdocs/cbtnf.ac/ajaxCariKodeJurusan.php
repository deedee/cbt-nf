<?php

	require_once('koneksi_db.php');

	$findWhat = $_POST['tF'];
	$cl1 = $_POST['clue1'];
	$cl2 = $_POST['clue2'];
	$cl3 = $_POST['clue3'];
	$cl4 = $_POST['clue4'];		// input kode pil prodi

	$foundPTNProdi = '';
	if ($findWhat=='ptn') {
		// $ptnSearch = mysqli_query($con, "SELECT noid FROM kodejurusan WHERE kelompok REGEXP '$cl1' AND namaprodi REGEXP '$cl2' AND (namaptn REGEXP '$cl3' OR ptn REGEXP '$cl3') LIMIT 1");
		// if (mysqli_fetch_array($ptnSearch)!=NULL) {
		$ix=0;
		$findPTN = mysqli_query($con, "SELECT DISTINCT namaptn, ptn FROM kodejurusan WHERE kelompok REGEXP '$cl1' AND namaprodi REGEXP '$cl2' AND (namaptn REGEXP '$cl3' OR ptn REGEXP '$cl3')");
		if ($findPTN) {
			while ($daftarPTN = mysqli_fetch_array($findPTN)) {
				$ix++;
				if ($ix<10) { $ix='00'.$ix;} else if ($ix<100) { $ix='0'.$ix;}
				$nam = $daftarPTN['namaptn'].' ('.$daftarPTN['ptn'].')';
				$nma = $daftarPTN['namaptn'];
				
				$foundPTNProdi .= "<tr><td style='vertical-align: top'><b>$ix.</b> &nbsp;</td> <td style='vertical-align: top'><span class='listSearchPTN' tagnm='$nma' style='margin-bottom:12px; cursor: pointer;'>$nam &nbsp;&nbsp;</span></td></tr>";
			}
		}
		else
		{ $foundPTNProdi .= "<tr><td style='vertical-align: top; color: red;'><b>00.</b> &nbsp;</td> <td style='vertical-align: top'><span style='margin-bottom:12px; color: red;'>Data tidak ditemukan !!&nbsp;&nbsp;</span></td></tr>"; }
	}
	else if ($findWhat=='prodi') {
		// jika terdapat tanda kurung harus dihilangkan dulu, membuat REGEXP jd error, krna dianggap pola regular expression
		if (strpos($cl4, "(")>0)
		{ $cl4 = substr($cl4,0,strpos($cl4, "(")); }

		// $jurSearch = mysqli_query($con, "SELECT noid FROM kodejurusan WHERE kelompok REGEXP '$cl1' AND namaprodi REGEXP '$cl2' AND namaptn='$cl3' AND namaprodi REGEXP '$cl4'");
		// if (mysqli_fetch_array($jurSearch)!=NULL) {
		$ix=0;
		$findJur = mysqli_query($con, "SELECT DISTINCT namaprodi, kodeprodi FROM kodejurusan WHERE kelompok REGEXP '$cl1' AND namaprodi REGEXP '$cl2' AND namaptn='$cl3' AND namaprodi REGEXP '$cl4'");
		if ($findJur) {
			while ($daftarJur = mysqli_fetch_array($findJur)) {
				$ix++;
				if ($ix<10) { $ix='00'.$ix;} else if ($ix<100) { $ix='0'.$ix;}
				$nam = $daftarJur['namaprodi'];
				$kod = $daftarJur['kodeprodi'];
				
				$foundPTNProdi .= "<tr><td style='vertical-align: top'><b>$ix.</b> &nbsp;</td> <td style='vertical-align: top'><span class='listSearchJur' tagnm='$nam' tagkd='$kod' style='margin-bottom: 12px; cursor: pointer;'>$nam &nbsp;&nbsp;</span></td></tr>";
			}
		}
		else
		{ $foundPTNProdi .= "<tr><td style='vertical-align: top; color: red;'><b>00.</b> &nbsp;</td> <td style='vertical-align: top'><span style='margin-bottom: 12px; color: red;'>Data tidak ditemukan !! &nbsp;&nbsp;</span></td></tr>"; }
	}
	
	echo $foundPTNProdi;
	
?>