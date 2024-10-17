<?php

	require_once('../koneksi_db.php');
	
	$searchBy = $_POST['bDT'];
	$searchWhat = $_POST['wDT'];

	$JsonArray = array();

	$resSearch = mysqli_query($con,"SELECT jurusan FROM absensiharitespeserta WHERE $searchBy REGEXP '$searchWhat'");
	$nResSearch = mysqli_num_rows($resSearch);


	if ($nResSearch==1)
	{
		$ambilSee = mysqli_query($con,"SELECT id, nomorInduk, nomorPeserta, petaSoal, nama, kelas, jurusan, nomorHP, alamatEmail, shiftTes, msgx_n FROM absensiharitespeserta WHERE $searchBy REGEXP '$searchWhat' ORDER BY nama");
		$dataLogin = mysqli_fetch_array($ambilSee);

		$nid = $dataLogin['id'];
		$nis = $dataLogin['nomorInduk'];
		$nop = $dataLogin['nomorPeserta'];
		$nps = $dataLogin['petaSoal'];
		$nam = $dataLogin['nama'];
		$kls = $dataLogin['kelas'];
		$jur = $dataLogin['jurusan'];
		$noHP = $dataLogin['nomorHP'];
		$alEm = $dataLogin['alamatEmail'];
		$sts = $dataLogin['shiftTes'];
		$msg_n = $dataLogin['msgx_n'];

		$JsonArray[1] = $nid;
		$JsonArray[2] = $nps;
		$JsonArray[3] = $kls;
		$JsonArray[4] = $nam;
		$JsonArray[5] = $nis;
		$JsonArray[6] = $nop;
		$JsonArray[7] = $sts;
		$JsonArray[8] = $msg_n;
		$JsonArray[9] = $noHP;
		$JsonArray[10] = $alEm;
		$JsonArray[11] = $jur;
	}
	else if ($nResSearch>1)
	{
		$ix = 0;
		$foundEm = '';

		$ambilSee = mysqli_query($con,"SELECT id, nomorInduk, nomorPeserta, nama FROM absensiharitespeserta WHERE $searchBy REGEXP '$searchWhat' ORDER BY nama");
		while ($dataLogin = mysqli_fetch_array($ambilSee))
		{
			$ix++;
			$nid = $dataLogin['id'];
			if ($nid<10) { $nid='00'.$nid;} else if ($nid<100) { $nid='0'.$nid;}
			$nam = $dataLogin['nama'];
			$nis = $dataLogin['nomorInduk'];
			$nop = $dataLogin['nomorPeserta'];
			
			if ($nResSearch>3)
			{
				if ($ix/2 == floor($ix/2))
				{ $foundEm .= "<span class='listSearchRes' tagnis='$nis' tagnop='$nop' style='margin-bottom:4px; padding:3px 0 3px 0; display:inline-block; background-color:lightgray; cursor:pointer'>&nbsp; $nid. $nam [<strong>$nis - $nop</strong>] &nbsp;</span><br>"; }
				else
				{ $foundEm .= "<span class='listSearchRes' tagnis='$nis' tagnop='$nop' style='margin-bottom:4px; display:inline-block; cursor:pointer'>&nbsp; $nid. $nam [<strong>$nis - $nop</strong>] &nbsp;</span><br>"; }
			}
			else
			{
				$foundEm .= "<span class='listSearchRes' tagnis='$nis' tagnop='$nop' style='margin-bottom:4px; display:inline-block; cursor:pointer'>&nbsp; $nid. $nam [<strong>$nis - $nop</strong>] &nbsp;</span><br>";
			}
		};
		
		$JsonArray[1] = $foundEm;
	}
	
	$JsonArray[0] = $nResSearch;

	echo json_encode($JsonArray);
	
?>