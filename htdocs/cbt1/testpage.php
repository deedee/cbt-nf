<?php

	//hide All Errors:
	error_reporting(0);
	ini_set('display_errors', 0);

	//show All Errors:
	// error_reporting(E_ALL);
	// ini_set('display_errors', 1);

	session_start();


	if (isset($_SESSION['currentNIN']) && isset($_SESSION['currentNOP'])) {
	  	$nomorInduk = $_SESSION['currentNIN'];
		$nomorPeserta = $_SESSION['currentNOP'];
		$pilihanSet = $_SESSION['currentPilSet'];
	}
	else if (isset($_POST['noID']) && isset($_POST['noPeserta'])) {
		$nomorInduk = $_POST['noID'];
		$nomorPeserta = $_POST['noPeserta'];
		$pilihanSet = $_POST['pilSet'];
	}
	else {
		header("Location: index.php");
		exit;
	}

	$setPil = explode("-", $pilihanSet);
	$kodeTO = $setPil[0];
	$hariKe = $setPil[1];


	require_once('koneksi_db.php');

	//ambil setting sistem
	$cekDataSistem = mysqli_query($con, "SELECT nOpsi, opsiErasable, id FROM datasystem WHERE id = '1'");
	$hasilDataSistem = mysqli_fetch_array($cekDataSistem);
	$opsiN = $hasilDataSistem['nOpsi'];
	$erasableOpsi = $hasilDataSistem['opsiErasable'];

	//------------------------------------------------------------------------------------------------------------------------------------------

	date_default_timezone_set('Asia/Jakarta'); 		//set timezone

	$nThn = date("Y")*10000;
	$nBln = date("m")*100;
	$nTgl = date("d");
	$nKal = ($nThn+$nBln+$nTgl)*1000000;

	$numHour = date("G");
	$numMin = date("i");
	$numSec = date("s");
	$numericTime = $numHour*3600 + $numMin*60 + $numSec;
	$cTime = $nKal+$numericTime;

	$hari = array("Ahad", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
	$bulan = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "Nopember", "Desember");
	$tgl_ = date("d"); $bln_ = date("n"); $hr_ = date("w"); $thn_ = date("Y");
	$hariIni = "$hari[$hr_], $tgl_ $bulan[$bln_] $thn_";

	$adaError = false;

	//cari shift TO aktif di tabel shifttes
	$cekShiftAktif = mysqli_query($con, "SELECT shift, jamMulai, mntMulai, akhirSesi, startLoginShift, endLoginShift, bolehTelat FROM shifttes WHERE aktifFlag = '1'");
	$hasilCekShift = mysqli_fetch_array($cekShiftAktif);

	$shiftAktif = $hasilCekShift['shift'];
	$jamMulai = $hasilCekShift['jamMulai'];
	$mntMulai = $hasilCekShift['mntMulai'];
	$fTelat =  $hasilCekShift['bolehTelat'];

	$awalSesiTO = ($jamMulai*3600) + ($mntMulai*60);
	$akhirSesiTO = $awalSesiTO + ($hasilCekShift['akhirSesi']*60);
	$awalLogin = $awalSesiTO - ($hasilCekShift['startLoginShift']*60);
	$akhirLogin = $awalSesiTO + ($hasilCekShift['endLoginShift']*60);

	$lastNumView = 1;
	$itemNumTot = 0;
	$BST = '';

	if ($hariKe!=0) {
		//cek Peserta ...
		$lihatAbsen = "SELECT piljur1, piljur2, petaSoal, nama, kelas, jurusan, nomorHP, alamatEmail, tglLahir, shiftTes, acakSoal$hariKe, lastNum$hariKe, tmpAnswer$hariKe, ragu$hariKe FROM absensiharitespeserta WHERE nomorInduk='$nomorInduk' AND nomorPeserta='$nomorPeserta' LIMIT 1";
		$cekAbsen = mysqli_query($con, $lihatAbsen);
		$hasilCekAbsen = mysqli_fetch_array($cekAbsen);

		if (!$hasilCekAbsen)
		{ $adaError = true; }

		else {
			$acakanSoal = $hasilCekAbsen['acakSoal'.$hariKe];
			$lastNumView = $hasilCekAbsen['lastNum'.$hariKe];
			$adaJawaban = $hasilCekAbsen['tmpAnswer'.$hariKe];
			$masihRagu = $hasilCekAbsen['ragu'.$hariKe];
			$kelasPeserta = $hasilCekAbsen['kelas'];
			$kodePetaSoal = $hasilCekAbsen['petaSoal'];
		}

		//pilih dan ambil soal yg sesuai dari tabel naskahsoal
		$ambilSoal = mysqli_query($con, "SELECT pathKodeSoal, bidStudiTabel, namaBidStudi, audioFiles, banyakItemNomor, kunciJawaban, durasi FROM naskahsoal WHERE kodeSoal='$kodeTO' LIMIT 1");
		$dapatSoal = mysqli_fetch_array($ambilSoal);
		if (!$dapatSoal)
		{ $adaError = true; }
		
		else {
			$PKS = $dapatSoal['pathKodeSoal'];
			$BST = strtolower($dapatSoal['bidStudiTabel']);
			$NBS = $dapatSoal['namaBidStudi'];
			$audioFl = $dapatSoal['audioFiles'];
			$itemNum = $dapatSoal['banyakItemNomor'];

			$totalin = substr_count($itemNum, "+");
			$totalin++;
			if ($totalin > 0) {
				$numPerBS = explode("+", $itemNum);
				for($iu=0; $iu<$totalin; $iu++)
				{ $itemNumTot += $numPerBS[$iu]; }
			}

			$susunanKunci = $dapatSoal['kunciJawaban'];		//digunakan utk menentukan bentuk soalnya pilgan atau essay
			$durasiTO = $dapatSoal['durasi'];

			//cek Timer ...
			$lihatTimer = "SELECT runTime$hariKe FROM aruntimer WHERE nomorInduk='$nomorInduk' AND nomorPeserta='$nomorPeserta' LIMIT 1";
			$cekTimer = mysqli_query($con, $lihatTimer);
			$hasilCekTimer = mysqli_fetch_array($cekTimer);
			if (!$hasilCekTimer)
			{ $adaError = true; }

			if ($hasilCekTimer['runTime'.$hariKe]!=0)
			{ $durasiSesi = $hasilCekTimer['runTime'.$hariKe]; }

			else {

				if ($numericTime < $akhirSesiTO) {
					if ($fTelat==0)
					{ $durasiSesi = $durasiTO - ($numericTime - $awalSesiTO); }
					else
					{ $durasiSesi = $durasiTO; }
				}
				else { $durasiSesi = $durasiTO; }  //ini utk yg masuk sudah lewat dari batas waktu akhir TO
			}
		}

		//set loginFlag = 1 dan kembalikan loginKey = 0
		// $domainIP = $_SERVER['REMOTE_ADDR'];
		$domainIP = $_SERVER['HTTP_X_REAL_IP'];
	}

	if ($adaError) {
		echo ('<script type="text/javascript">');
		echo ('window.location="testpege.php";');
		echo ('</script>');
	}

	//set log & soal
	$setLog = "UPDATE absensiharitespeserta SET ipRemote='$domainIP', loginFlag=1, loginKey=0, kodeTO='$kodeTO', TOke=$hariKe, curTimer=$cTime, loginError=0, TOFinish=0 WHERE nomorInduk='$nomorInduk' AND nomorPeserta='$nomorPeserta' LIMIT 1";
	mysqli_query($con,$setLog);

	// get old record
	$pjur1 = $hasilCekAbsen['piljur1'];
	$pjur2 = $hasilCekAbsen['piljur2'];
	$nomorHP = $hasilCekAbsen['nomorHP'];
	$alamatEmail = $hasilCekAbsen['alamatEmail'];
	$tglLahir = $hasilCekAbsen['tglLahir'];
	
	if (isset($_POST['pj1'])) { $pjur1 = $_POST['pj1']; }
	if (isset($_POST['pj2'])) { $pjur2 = $_POST['pj2']; }
	if (isset($_POST['noHP'])) { $nomorHP = $_POST['noHP']; }
	if (isset($_POST['email'])) { $alamatEmail = $_POST['email']; }
	if (isset($_POST['getDoB'])) { $tglLahir = $_POST['getDoB']; }
	// if (isset($_POST['pj3'])) { $pjur3 = $_POST['pj3']; } else { $pjur3 = ''; }

	$stmt = mysqli_prepare($con, "UPDATE absensiharitespeserta SET nomorHP=?, alamatEmail=?, tglLahir=?, piljur1=?, piljur2=? WHERE nomorInduk='".$nomorInduk."' AND nomorPeserta='".$nomorPeserta."' LIMIT 1");
	mysqli_stmt_bind_param($stmt, "sssss", $nomorHP, $alamatEmail, $tglLahir, $pjur1, $pjur2);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);


	//Oke, Lanjut TO ...
	$_SESSION['currentNIN'] = $nomorInduk;
	$_SESSION['currentNOP'] = $nomorPeserta;
	$_SESSION['currentPilSet'] = $pilihanSet;

	$kataRagu = "Ragu-ragu";
?>

<!DOCTYPE html>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="TO Online CBT BKB Nurul Fikri">
	<meta name="keywords" content="BKB, NF, nurul fikri, nurul, fikri, bimbel, islami, pendidikan, sekolah, cara cepat, to, to online, cbt">
	<link rel="shortcut icon" href="images/nf-favico.ico" type="image/x-icon">
	
	<title>CBT NF</title>

	<link rel="stylesheet" href="jqm/css/jquery.mobile-1.4.5.min.css" />
	<script src="jqm/js/jquery.min.js"></script>
	<script src="jqm/js/jquery.mobile-1.4.5.min.js"></script>

	<style media="screen" type="text/css">

		/* Swipe works with mouse as well but often causes text selection. */
		/* We'll deny text selecton on everything but INPUTs and TEXTAREAs. */
		#cbtpage :not(INPUT):not(TEXTAREA) {
		    -webkit-user-select: none;
		    -moz-user-select: none;
		    -ms-user-select: none;
		    -o-user-select: none;
		    user-select: none;
		}

		body :not(INPUT):not(TEXTAREA) {	/* avoiding page content selection */
		    -webkit-user-select: none;
		    -moz-user-select: none;
		    -ms-user-select: none;
		    -o-user-select: none;
		    user-select: none;
		}

		body {
			margin: 0;
	        padding: 0;
	        border: 0;			/* This removes the border around the viewport in old versions of IE */
	        width: 100%;
	        min-width: 210px;   /* Minimum width of layout - remove line if not required */
								/* The min-width property does not work in old versions of Internet Explorer */
	    }

	    /* respEl = responsive Element */
	    @media screen and (min-width: 1067px) {
	        #cetakNoSoal {
	        	visibility: hidden;
	    	}
	    }
		
		#contain {
			width: 100%; height: 100%;
			margin-right: auto;
			margin-left: auto;
			margin-top: 0px;
			overflow: hidden;
		}
		
		#resultContainer {
			background-color: #fff;
		}
	
		/* Adjust the width of the right question-number panel */
		.blokHorDiv > div {
			display: inline-block;
		}

		.blokHorDiv {
			height:46px; width:95%;
			margin-top:5px;
			margin-bottom:5px;
			text-align:left;
		}

		.inblokDiv {
			float:left;
			cursor:pointer;
			border:2px #CBCBCB;
			border-radius:5px;
			border-style:solid;
			height:40px; width:40px;
			text-align:center;
			margin-left:4px;
			margin-right:4px;
			box-shadow: 2px 2px 2px #808080;
		}

		.inblokNodiv {
			font-size:18px;
			font-weight:bold;
			border-bottom: dashed 1px;
		}

		.inblokJwbdiv {
			font-size:13px;
			font-weight:bold;
		}
	</style>

</head>

<body>
	
<div data-role="page" id="cbtpage" class="ui-responsive-panel" data-title="CBT NF">

	<div data-role="popup" id="displayErrorDisconnect" data-overlay-theme="b" data-theme="a" style="min-width:305px;">
	    <div data-role="header" data-theme="a">
	    	<h2>Access Error</h2>
	    	<a href="#" data-icon="alert" data-iconpos="notext">alertError</a>
	    </div>
	    <center>
	    <br>
	    	<img src="images/disconnected.png">
	        <br><br>
	        <span style="color:red"><b>Akses file gagal !!</b><br/span>
        <br><br>
    	</center>
	</div>
	<!-- popup disconnected or file access error -->

	<div data-role="popup" id="dialogChat" data-dismissible="false" data-overlay-theme="a" data-theme="a" style="min-width:305px;">
	    <div data-role="header" data-theme="a">
	    	<h2>Info Box</h2> <a data-icon="comment" data-iconpos="notext">infoBox</a>
	    </div>
	    <center>
	    <br>
    		<input type="hidden" id="noInd" value="<?= $nomorInduk; ?>" style="display: none">
	        <input type="hidden" id="noPes" value="<?= $nomorPeserta; ?>" style="display: none">
	        <div id="isiPesanChat"></div>
	        <br>
	        <a id="OkeKak" class="ui-btn ui-mini ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">Ok</a>
    	<br><br>
    	</center>
	</div>
	<!-- popup dialog chatBox -->

	<div data-role="popup" id="popupDlgFinish" data-overlay-theme="b" data-theme="a" style="max-width:400px;">
	    <div data-role="header" data-theme="a">
	    	<h2>
	    		<?php
					if ($BST=="eng")
					{ echo "Finish TO"; }
					else
					{ echo "Selesai TO"; }
				?>
	    	</h2>
	    	<a href="#" data-icon="check" data-iconpos="notext">sureFinish</a>
	    </div>
	    <div role="main" class="ui-content">
	        <h4 class="ui-title">Yakin akan selesai ujian ?</h4>
	    <p>
	    	<span id="konfirmSoal"></span>
            <span id="adaSoalBelumDijawab"></span>
            <span id="adaSoalMasihRagu"></span>
	    </p>
	    <p>Nilai akan langsung dihitung dan ditampilkan.</p>
	    <br>
	    <label><input name="confirmFinish" type="checkbox" data-mini="true" data-theme="a"> Ya, saya yakin untuk selesai ujian </label><br>
	        <a href="#" onClick="goAudio()" class="ui-btn ui-mini ui-corner-all ui-shadow ui-btn-inline ui-btn-b" data-rel="back">
	        	<?php
					if ($BST=="eng")
					{ echo "Cancel"; }
					else
					{ echo "Batal"; }
				?>
	        </a>
	        <a href="#" onClick="cekFinishTO()" id="okeFinish" class="ui-btn ui-mini ui-corner-all ui-shadow ui-btn-inline ui-btn-b ui-disabled">
	        	<?php
					if ($BST=="eng")
					{ echo "Finish !!"; }
					else
					{ echo "Selesai !!"; }
				?>
	        </a>
	    </div>
	</div>
	<!-- popup dialog finish -->

	<!-- HEADER starts here -->
    <div data-role="header" style="background-color:#527fc9">
        <h1 style="color:white">CBT NF -  
        		<a href="#" id="clock" style="text-decoration:none; cursor:default; color:white"></a>
        </h1>
        <a href="#infoTipeSoal" data-icon="bars" data-iconpos="notext">Petunjuk</a>
        <a href="#nomorSoal" data-icon="grid" data-iconpos="notext">Pilihan Nomor</a>
    </div>
    <!-- invisible time counter -->
    <input id="tmpClock" style="display:none;" type="text" readonly> </input>
    <!-- HEADER ends here -->

    <div role="main" class="ui-content jqm-content jqm-fullwidth" style="margin-top:-15px">
    	<center>

    	<div class="ui-grid-d" style="background-color: #f5f5f5; width:100vw; margin-left: -15px; margin-bottom: 8px;">
			<?php
		    if ($lastNumView==1)
		    { echo '<div class="ui-block-a" style="visibility:hidden">'; }
		    else
		    { echo '<div class="ui-block-a">'; }
		    ?>
		    <a id="kurang" style="float:right; margin-top:13px" data-ajax="false" class="ui-btn ui-mini ui-btn-inline ui-icon-carat-l ui-btn-icon-left ui-alt-icon">&nbsp;</a>
			</div>

		    <?php
			    if ($lastNumView==$itemNumTot)
			    { echo '<div class="ui-block-b" style="visibility:hidden">'; }
			    else
			    { echo '<div class="ui-block-b">'; }
		    ?>
		    <a id="tambah" style="float:left; margin-top:13px" data-ajax="false" class="ui-btn ui-mini ui-btn-inline ui-icon-carat-r ui-btn-icon-right ui-alt-icon">&nbsp;</a>
		    </div>

		    <div class="ui-block-c">
		    	<span id="cetakNoSoal" style="display:block; width:26px; height:23px; border-radius:3px; font-family:'Arial'; font-size:13px; text-align:center; color:white; background-color:black; padding-top:4px; transform: translateY(17px);"></span>
		    </div>
		    <div class="ui-block-d"> </div>
		    
		    <div class="ui-block-e" style="width: 10%">
		    	<select id="noSoal">
					<?php
						for ($x=1; $x<=$itemNumTot; $x++) {
						   if ($x==$lastNumView)
						   { echo ('<option id="soal'.$x.'" value="'.$x.'" selected>'.$x.'. [ ]</option>'); }
						   else
						   { echo ('<option id="soal'.$x.'" value="'.$x.'">'.$x.'. [ ]</option>'); }
						}
					?>
				</select>
		    </div>
		</div>
		<!-- /grid-c -->

	        <div id="viewQuestion" style="width: 100%; height: calc(100vh - 170px); overflow: auto;">
				<audio id="audioPlace">
					<source src="" type="audio/ogg">
					Your browser does not support the audio element.
				</audio>
				<br>

				<img id="portView" src="" oncontextmenu="return false" style="visibility:hidden;max-width:100%;height:auto;" />

				<br><br>

				<div id="pilihanJawabanNo">
					<b><legend>
						<?php
							if ($BST=="eng")
							{ echo "Answer :"; }
							else
							{ echo "Jawaban :"; }
						?>
					</legend></b><br>
				    <div class="ui-grid-d" id="jwbEssay" style="display: none">
					    <div class="ui-block-a"> </div>
					    <div class="ui-block-b"> </div>
					    <div class="ui-block-c">
					    	<!-- <textarea name="kolomIsian" id="kolomIsian" title="isi dengan jawaban akhir saja, tanpa jalan / langkah penyelesaian"></textarea> -->
					    	<textarea name="kolomIsian" id="kolomIsian" title="Silakan menjawab tanpa menggunakan tanda koma &ldquo;,&rdquo; pada kalimat jawaban. Ganti tanda koma dengan titik koma."></textarea>
					    	<span style="font-size: 15px">Silakan menjawab tanpa menggunakan tanda koma &ldquo;,&rdquo; pada kalimat jawaban. Ganti tanda koma dengan titik koma. &ldquo; ; &rdquo;</span><br>
					    </div>
					    <div class="ui-block-d"> </div>
					    <div class="ui-block-e"> </div>
					</div>

				    <fieldset id='pilgan' data-role="controlgroup" data-mini="true" data-type="horizontal" style="display: none;">
				        <input name="pil" id="pilA" value="A" type="radio">
				        <label for="pilA">A</label>
				        
				        <input name="pil" id="pilB" value="B" type="radio">
				        <label for="pilB">B</label>
				        
				        <?php
				        	if($opsiN>2) {
				        		echo '<input name="pil" id="pilC" value="C" type="radio">';
				        		echo '<label for="pilC">C</label>';
				        	}

				        	if($opsiN>3) {
				        		echo '<input name="pil" id="pilD" value="D" type="radio">';
				        		echo '<label for="pilD">D</label>';
				        	}

				        	if($opsiN>4) {
				        		echo '<input name="pil" id="pilE" value="E" type="radio">';
				        		echo '<label for="pilE">E</label>';
				        	}
				        ?>
				    </fieldset>

				    
				    <div class="ui-grid-d" data-mini="true">
					    <div class="ui-block-a"> </div>
					    <div class="ui-block-b"> </div>
					    <div class="ui-block-c">
					    	<label style="width:61px">
					    		<input name="ragu" id="ragu" type="checkbox" style="display:none" data-mini="true" data-theme="a">
					    		<?php
									if ($BST=="eng")
									{ echo "Uncertain"; }
									else
									{ echo "Ragu"; }
								?>
					    	</label></div>
					    <div class="ui-block-d"> </div>
					    <div class="ui-block-e"> </div>
					</div>
					
				</div>

				<br><br><br>
			</div>

		</center>
		
    </div>
    <!-- /content -->

    <div data-role="panel" data-display="reveal" data-theme="b" id="infoTipeSoal">
		<a href="#" data-rel="Close" data-ajax="false" class="ui-btn ui-mini ui-corner-all ui-btn-inline ui-icon-delete ui-btn-icon-left">Close</a>
    	<u><h4>Petunjuk Menjawab</h4></u>

	    	<div style="height:calc(77vh); overflow:auto;">

		        <p><b>Tipe Uraian</b><br>
		        	Isilah dengan jawaban singkat, tanpa menuliskan jalannya
		        </p>

		        <p><b>Tipe A</b><br>
		        	Pilih <u>satu</u> jawaban yang paling tepat
		        </p>
		        
		        <p><b>Tipe B<br>Pernyataan &amp; Alasan</b><br>
		        	A : jika pernyataan dan alasan benar dan menunjukkan hubungan sebab-akibat<br>
		        	B : jika pernyataan dan alasan benar tapi tidak menunjukkan hubungan sebab-akibat<br>
		        	C : jika pernyataan benar dan alasan salah<br>
		        	D : jika pernyataan salah dan alasan benar<br>
		        	E : jika pernyataan dan alasan keduanya salah
		        </p>

		        <p><b>Tipe C</b><br>
		        	A : jika (1), (2), dan (3) benar<br>
		        	B : jika (1) dan (3) benar<br>
		        	C : jika (2) dan (4) benar<br>
		        	D : jika hanya (4) yang benar<br>
		        	E : jika semuanya benar
		        </p>

        	</div>

    </div>
    <!-- /panel -->

    <div data-role="panel" data-position="right" data-display="push" data-theme="a" id="nomorSoal">
    	<a href="#" data-rel="Close" data-ajax="false" class="ui-btn ui-mini ui-corner-all ui-btn-inline ui-icon-delete ui-btn-icon-right">Close</a>
    	<p style="font-size:13px"><b><?= $hasilCekAbsen['nama']; ?><br>
    	<?= $hasilCekAbsen['kelas']; ?><!-- - <?= $hasilCekAbsen['nomorPeserta']; ?> --></b></p>

    	<form name="siswa" id="siswa" method="POST" target="_parent" data-ajax="false" action="procsx.php">
	    	<input type="hidden" name="harIn" value="<?= $hariIni; ?>">
			<input type="hidden" name="harK" value="<?= $hariKe; ?>">
			<input type="hidden" name="kodSoa" value="<?= $kodeTO; ?>">
			<input type="hidden" name="petSoa" value="<?= $kodePetaSoal; ?>">
			<input type="hidden" name="namBiStud" value="<?= $NBS; ?>">
			<input type="hidden" name="iteNu" value="<?= $itemNum; ?>">
            <input type="hidden" id="soalAcakan" name="soalAcakan" value="">
			<input type="hidden" id="butirJwb" name="butirJwb">
			  
			<input type="hidden" name="nam" value="<?= $hasilCekAbsen['nama']; ?>">
			<input type="hidden" name="kela" value="<?= $hasilCekAbsen['kelas']; ?>">
			<input type="hidden" name="progra" value="<?= $hasilCekAbsen['jurusan']; ?>">
			<input type="hidden" name="nomoIndu" value="<?= $nomorInduk; ?>">
			<input type="hidden" name="nomoPesert" value="<?= $nomorPeserta; ?>">
		</form>

        <div id="colAnswer" style="height:calc(100vh - 160px); overflow:auto;">
					
			<?php
				$itemNo = $itemNumTot;
				for ($x=1; $x<=ceil($itemNo/4); $x++) {
				   if ($x!=ceil($itemNo/4)) {
						echo ('<div class="blokHorDiv">');
							for ($y=1; $y<=4; $y++) {
								$noBlok = ($x-1)*4 + $y;
								if ($noBlok < 10)
								{ $txtnoBlok = "0".$noBlok; }
								else
								{ $txtnoBlok = $noBlok; }
								echo ('<div class="inblokDiv" id="blok'.$noBlok.'" style="background-color:white; color:black;" onmousedown="$.gantiBlok('.$noBlok.');">');
									echo ('<div id="no'.$noBlok.'" class="inblokNodiv">&nbsp;'.$txtnoBlok.'&nbsp;</div>');
									echo ('<div id="jwb'.$noBlok.'" class="inblokJwbdiv">&nbsp;</div>');
								echo ('</div>');
							}
						echo ('</div>');
				   }
				   else {
					   echo ('<div class="blokHorDiv">');
							$z = $itemNo - (ceil($itemNo/4)-1)*4;
							for ($zu=1; $zu<=$z; $zu++) {
								$noBlok = ($x-1)*4 + $zu;
								if ($noBlok < 10)
								{ $txtnoBlok = "0".$noBlok; }
								else
								{ $txtnoBlok = $noBlok; }
								echo ('<div class="inblokDiv" id="blok'.$noBlok.'" style="background-color:white; color:black;" onmousedown="$.gantiBlok('.$noBlok.');">');
									echo ('<div id="no'.$noBlok.'" class="inblokNodiv">&nbsp;'.$txtnoBlok.'&nbsp;</div>');
									echo ('<div id="jwb'.$noBlok.'" class="inblokJwbdiv">&nbsp;</div>');
								echo ('</div>');
							}
					   echo ('</div>');
				   }
				}
			?>
			<br>
			<a href="#" id="finishBtn" data-rel="popup" class="ui-btn ui-mini ui-corner-all ui-shadow ui-btn-b" data-transition="pop"
		   			style="background-color:mediumseagreen; width: 172px">
		   			<b>
		   				<?php
							if ($BST=="eng")
							{ echo "Finish"; }
							else
							{ echo "Selesai"; }
						?>
		   			</b></a>
		   	<br>

		</div>
    </div>
    <!-- /panel -->

</div>

<script type="text/javascript">

	/*
	$(document).on("pagecreate", "#cbtpage", function() {
		$(document).on("swipeleft swiperight", "#cbtpage", function(e) {
	        // We check if there is no open panel on the page because otherwise
	        // a swipe to close the left panel would also open the right panel (and v.v.).
	        // We do this by checking the data that the framework stores on the page element (panel: open).
	        if ( $(".ui-page-active").jqmData("panel") !== "open") {
	            if (e.type === "swiperight")
	            { $("#infoTipeSoal").panel("open"); }
	        }
	        else {
	        	if (e.type === "swiperight")
	            { $("#nomorSoal").panel("close"); }
	        }
	    });
	});
	*/

    //buat object counter berdasarkan durasi soal
    var xCount = <?= $durasiSesi+1; ?>;
	var xDown = 1;
	var durasiTO = <?= $durasiTO; ?>;
	var TOCountDown = 0;

	var lastView = <?= $lastNumView; ?>;
	var noLalu = <?= $lastNumView; ?>;
	var nomor = <?= $lastNumView; ?>;
	var iNTot = <?= $itemNumTot; ?>;

	var tipeSoal = "";
	// var tipeSoal untuk menentukan binding keydown dan keypress
	// kalo tipe soalnya essay keydown & keypress ga usah dilakukan
	// nilainya pilgan dan essay

	var opsiN = <?= $opsiN; ?>;
	var opsiErase = <?= $erasableOpsi; ?>;

	var audioSet = 0;

	function goAudio() {
		var idAudio = document.getElementById("audioPlace");
		idAudio.play();
	}

	function cekFinishTO() {
		xDown = 0;
		document.getElementById("siswa").submit();
	}
	
    //fungsi displayCountDown yang dieksekusi tiap 1000ms = 1 dtk
	function displayCountDown() {
		//kurangi xCount
		xCount = xCount - xDown;
		
		var idAudio = document.getElementById("audioPlace");
		
		if (xCount > durasiTO) {
			//audio distop dulu
			idAudio.pause();
			idAudio.autoplay = "false";
			
			document.getElementById("portView").style.visibility = "hidden";
			document.getElementById("pilihanJawabanNo").style.visibility = "hidden";
			document.getElementById("colAnswer").style.visibility = "hidden";
			
			$("#kurang").prop("disabled", true).addClass("ui-disabled");
			document.getElementById("noSoal").disabled = true;
			$("#tambah").prop("disabled", true).addClass("ui-disabled");
			
			$("#finishBtn").prop("disabled", true).addClass("ui-disabled");
			
			if (xCount % 8 == 0)
			{ document.getElementById("cetakNoSoal").innerHTML = "<span style='font-family:tahoma'>&neArr;</span>"; }
			else if (xCount % 8 == 1)
			{ document.getElementById("cetakNoSoal").innerHTML = "<span style='font-family:tahoma'>&uArr;</span>"; }
			else if (xCount % 8 == 2)
			{ document.getElementById("cetakNoSoal").innerHTML = "<span style='font-family:tahoma'>&nwArr;</span>"; }
			else if (xCount % 8 == 3)
			{ document.getElementById("cetakNoSoal").innerHTML = "<span style='font-family:tahoma'>&lArr;</span>"; }
			else if (xCount % 8 == 4)
			{ document.getElementById("cetakNoSoal").innerHTML = "<span style='font-family:tahoma'>&swArr;</span>"; }
			else if (xCount % 8 == 5)
			{ document.getElementById("cetakNoSoal").innerHTML = "<span style='font-family:tahoma'>&dArr;</span>"; }
			else if (xCount % 8 == 6)
			{ document.getElementById("cetakNoSoal").innerHTML = "<span style='font-family:tahoma'>&seArr;</span>"; }
			else { document.getElementById("cetakNoSoal").innerHTML = "<span style='font-family:tahoma'>&rArr;</span>"; }
			
			//ambil jam
			  var jam = Math.floor((xCount-durasiTO)/3600);
			  var j = jam.toString();
			  var sisajam = (xCount-durasiTO) % 3600;
			//ambil menit
			  var menit = Math.floor(sisajam/60);
			  var m = menit.toString();
			  var detik = sisajam % 60;
			  var d = detik.toString();
		}
		else {
			if(audioSet==0) {
				//audionya boleh dimulai
				audioSet=1;
				idAudio.autoplay = "true";
				idAudio.play();
			
				document.getElementById("portView").style.visibility = "visible";
				document.getElementById("pilihanJawabanNo").style.visibility = "visible";
				document.getElementById("colAnswer").style.visibility = "visible";
				
				$("#kurang").removeClass("ui-disabled");
				document.getElementById("noSoal").disabled = false;
				$("#tambah").removeClass("ui-disabled");
				
				$("#finishBtn").removeClass("ui-disabled");

				document.getElementById("cetakNoSoal").innerHTML = noLalu;
				TOCountDown = 1;
			}
			
			//ambil jam
			  var jam = Math.floor(xCount/3600);
			  var j = jam.toString();
			  var sisajam = xCount % 3600;
			//ambil menit
			  var menit = Math.floor(sisajam/60);
			  var m = menit.toString();
			  var detik = sisajam % 60;
			  var d = detik.toString();
		}
        
		var showCount = (j.length==1?"0"+j:j)+":"+(m.length==1?"0"+m:m)+":"+(d.length==1?"0"+d:d);

        document.getElementById("clock").innerHTML = showCount;
		document.getElementById("tmpClock").value = xCount;

		//kalo TO sudah sampe 00:00:00 nilai segera dihitung untuk ditampilkan, catat bhw siswa tsb sudah ikut TO utk hari itu, selesai !!
		if (showCount == "00:00:00" && TOCountDown == 1)
		{ cekFinishTO(); }
    }

	//Cegah backward from browser
	  function preventBack() {window.history.forward();}
	  setTimeout("preventBack()", 0);
	  window.onunload=function() {null};	

	  setInterval("displayCountDown()", 1000);


	$(window).load(function() {

		//warna blok yg sedang terpilih saat itu
		var warnaBorderPilih = "#DAFEE1";
		var warnaBlokPilih = "#67BE7E";
		var warnaTeksPilih = "white";

		//warna blok yg sudah dijawab
		var warnaBorderJawab = "#BCCEEA";
		var warnaBlokJawab = "#527FC9";
		var warnaTeksJawab = "white";

		//warna blok yg belum dijawab
		var warnaBorderKosong = "#CBCBCB";
		var warnaBlokKosong = "white";
		var warnaTeksKosong = "black";

		//warna blok yg ditandai ragu2, tdk sedang dipilih
		var warnaBorderRagu = "#F1F36B";
		var warnaBlokRagu = "#DEE75F";
		var warnaTeksRagu = "black";
		
		var ext = ".png";
		var aext = ".ogg"; //audio file extension
		var sndAudio = "<?= $audioFl; ?>";	//pencatat record sound yg sudah diputar
		var preloadSoal = [];
		var jawabanSoal = [];
		var nomorRagu = [];
		var jwbNo;

		var minNomor = 1;
		var sKunci = '<?= $susunanKunci; ?>';
		var acakNoSoal = '<?= $acakanSoal; ?>';
		var reachable;		//variable penentu image suatu no soal dpt diakses oleh client atau tidak

		var chooseAnswer = false;	//variabel penanda baru memilih jawaban
		
		var urutanSoal = acakNoSoal.split(",");
		var urutanKunci = sKunci.split(",");

		$("#soalAcakan").val(acakNoSoal);
		
		var ds = '<?= "images/soal/$PKS/"; ?>';


		//////////// menampilkan soal pertama ///////////////////////////
		/////////////////////////////////////////////////////////////////

		//tampilkan dulu animasi gif loading
		$("#portView").attr("src", "images/loadingWaves.gif");
		//siapkan nama soal dan sound untuk soal pertama, kalau ada
		af = urutanSoal[lastView-1]+ext;
		uf = urutanSoal[lastView-1]+aext;

		$.ajax({
			type: "post",
			url: "cekandload.php",
			async: false,
			data: {caf:ds+af, cuf:ds+uf},
			cache: false,
			success: function(resCek) {
				if (resCek == 0) //file tdk ditemukan
				{ $("#displayErrorDisconnect").popup("open"); }
				else {
					if (resCek == 1) //hanya ada file image
					{ $("#portView").attr("src", ds+af); }
					else {	//ada file image dan audio
						$("#portView").attr("src", ds+af);
						$("#audioPlace").attr("src", ds+uf);
					}
				}
				$("#noSoal").val(lastView).change();
				document.getElementById("cetakNoSoal").innerHTML = lastView;
			}
		});

		//sesuaikan tampilan jawaban dengan tipe soal, uraian ataukah pilgan
		//dengan melihat kunci jwbnnya, essay diawali dengan titik 3 di depan jawabannya
		if (urutanKunci[urutanSoal[lastView-1]-1].length == 1) {
			tipeSoal = "pilgan";
			$("#jwbEssay").hide();
			$("#pilgan").show();
		}
		else {
			tipeSoal = "essay";
			$("#jwbEssay").show();
			$("#pilgan").hide();
		}

		// cek jawaban ----------------------------------
		var sudahJawab = "<?= $adaJawaban; ?>";
		if (sudahJawab == "") {
			for (j=0; j<=iNTot; j++)
			{ jawabanSoal[j] = " "; }
		}
		else {
			jawabanSoal = sudahJawab.split("|");

			for (i=1; i<=iNTot; i++) {
				if (jawabanSoal[i]!=" ") {
					var option = $('<option></option>').text(i+". ["+jawabanSoal[i]+"]");
					$("#soal"+i).empty().append(option);

					$("#blok"+i).css("borderColor",warnaBorderJawab);
					$("#blok"+i).css("backgroundColor",warnaBlokJawab);
					$("#blok"+i).css("color",warnaTeksJawab);

					$("#jwb"+i).text(jawabanSoal[i].charAt(0));
					$("#blok"+i).prop("title",jawabanSoal[i]);

					//tandai (checked=true) radio button yg tampil klo merupakan soal yang saat ini dilihat
					//dg syarat tipe kuncinya adalah soal pilgan
					if (i==lastView) {
						if (urutanKunci[urutanSoal[i-1]-1].length == 1) {
							if (jawabanSoal[lastView] == "A")
							   { $("#pilA").prop("checked",true).checkboxradio("refresh"); }
							else if (jawabanSoal[lastView] == "B") 
							   { $("#pilB").prop("checked",true).checkboxradio("refresh"); }
							else if (jawabanSoal[lastView] == "C") 
							   { $("#pilC").prop("checked",true).checkboxradio("refresh"); }
							else if (jawabanSoal[lastView] == "D") 
							   { $("#pilD").prop("checked",true).checkboxradio("refresh"); }
							else if (jawabanSoal[lastView] == "E") 
							   { $("#pilE").prop("checked",true).checkboxradio("refresh"); }
						}
						else {
							$("#kolomIsian").val(jawabanSoal[lastView]);
							$("#kolomIsian").focus();
						}
						chooseAnswer = true;
						$("#noSoal").val(lastView).change();
					}
				}
			}
		}
		
		// cek ragu -------------------------------------
		var adaRagu = "<?= $masihRagu; ?>";		
		if (adaRagu == "") {
			for (l=0; l<=iNTot; l++)
			{ nomorRagu[l] = " "; }
		}
		else {
			nomorRagu = adaRagu.split(",");

			for (k=1; k<=iNTot; k++) {
				if (nomorRagu[k]!=" ") {
					$("#blok"+k).css("borderColor",warnaBorderRagu);
					$("#blok"+k).css("backgroundColor",warnaBlokRagu);
					$("#blok"+k).css("color",warnaTeksRagu);
					if (k==lastView)
					{ $("#ragu").prop("checked",true).checkboxradio("refresh"); }
				}
			}
		}

		$("#blok"+lastView).css("borderColor",warnaBorderPilih);
		$("#blok"+lastView).css("backgroundColor",warnaBlokPilih);
		$("#blok"+lastView).css("color",warnaTeksPilih);

		if ($("#blok"+lastView).position().top + $("#blok"+lastView).height() > $("#colAnswer").position().top + $("#colAnswer").height()) {
			$("#colAnswer").animate({ scrollTop: $("#colAnswer").scrollTop() + 
			$("#blok"+lastView).position().top - $("#colAnswer").position().top - $("#colAnswer").height() + $("#blok"+lastView).height()*3}, 650);
		}

		$("#butirJwb").val(jawabanSoal.join('|'));

		runChecking();

		/////////////////////////////////////////////////////////////////
		/////////////////////////////////////////////////////////////////

		////////// preloading image soal berikutnya //////////
		var imageObject = new Image();
        imageObject.src = ds+urutanSoal[lastView]+ext;

        if(lastView<iNTot)
        { preloadSoal[lastView+1] = "1"; } //soal selanjutnya sudah dipreload

		(function($) {
			//fungsi untuk mengganti gambar soal

			// ############## ganti soal ################# //
			$.gantiSoal = function(soalKe) {
				
				af = urutanSoal[soalKe-1]+ext;
				uf = urutanSoal[soalKe-1]+aext;
			    
			    $("#cetakNoSoal").html(soalKe);
			    $("#portView").attr("src", ds+af);
				$("#audioPlace").attr("src", "");
				
				cekUf = "#" + uf + "#";
				adakahAu = sndAudio.indexOf(cekUf);
				if (adakahAu >- 1) { $("#audioPlace").attr("src", ds+uf); }
				
				document.getElementById("kurang").style.visibility = "visible";
				document.getElementById("tambah").style.visibility = "visible";
				if (soalKe == 1) 
				{ document.getElementById("kurang").style.visibility = "hidden"; }
				else if (soalKe == iNTot)
				{ document.getElementById("tambah").style.visibility = "hidden"; }

				jwbNo = jawabanSoal[soalKe];

				//tampilkan jawaban yg sudah diisi sesuai dengan bentuk soalnya pilgan atau essay
				if (urutanKunci[urutanSoal[soalKe-1]-1].length == 1) {
					tipeSoal = "pilgan";
					$("#jwbEssay").hide();
					$("#pilgan").show();

					//tampilkan pilihan jawaban no tersebut
					if (jwbNo == "A")
					{ $("#pilA").prop("checked",true).checkboxradio("refresh"); } else { $("#pilA").prop("checked",false).checkboxradio("refresh"); }
				    if (jwbNo == "B") 
					{ $("#pilB").prop("checked",true).checkboxradio("refresh"); } else { $("#pilB").prop("checked",false).checkboxradio("refresh"); }
				    if (jwbNo == "C") 
					{ $("#pilC").prop("checked",true).checkboxradio("refresh"); } else { $("#pilC").prop("checked",false).checkboxradio("refresh"); }
				    if (jwbNo == "D") 
					{ $("#pilD").prop("checked",true).checkboxradio("refresh"); } else { $("#pilD").prop("checked",false).checkboxradio("refresh"); }
				    if (jwbNo == "E") 
					{ $("#pilE").prop("checked",true).checkboxradio("refresh"); } else { $("#pilE").prop("checked",false).checkboxradio("refresh"); }
				}
				else {
					tipeSoal = "essay";
					$("#jwbEssay").show();
					$("#pilgan").hide();

					//tampilkan uraian jawaban no tersebut
					if (jwbNo==' ') { jwbNo = ''; }
					$("#kolomIsian").val(jwbNo);
					$("#kolomIsian").focus();
				}

				if (nomorRagu[soalKe]!=" ")
				{ $("#ragu").prop("checked",true).checkboxradio("refresh"); }
				else
				{ $("#ragu").prop("checked",false).checkboxradio("refresh"); }

				//preloader
				if (soalKe != iNTot) {
					var nextImg = ++soalKe;

					if (preloadSoal[nextImg] != "1") {
						imageObject = new Image();
						imageObject.src = ds+urutanSoal[nextImg-1]+ext;
						preloadSoal[nextImg] = reachable;
					}
				}
				
				if (jawabanSoal[noLalu]!=" ") {
					$("#blok"+noLalu).css("borderColor",warnaBorderJawab);
					$("#blok"+noLalu).css("backgroundColor",warnaBlokJawab);
					$("#blok"+noLalu).css("color",warnaTeksJawab);
				}
				else {
					$("#blok"+noLalu).css("borderColor",warnaBorderKosong);
					$("#blok"+noLalu).css("backgroundColor",warnaBlokKosong);
					$("#blok"+noLalu).css("color",warnaTeksKosong);
				}

				if (nomorRagu[noLalu]!=" ") {
					$("#blok"+noLalu).css("borderColor",warnaBorderRagu);
					$("#blok"+noLalu).css("backgroundColor",warnaBlokRagu);
					$("#blok"+noLalu).css("color",warnaTeksRagu);
				}

				//update no skrg jadi noLalu
				noLalu=$("#noSoal").val();
				$("#blok"+noLalu).css("borderColor",warnaBorderPilih);
				$("#blok"+noLalu).css("backgroundColor",warnaBlokPilih);
				$("#blok"+noLalu).css("color",warnaTeksPilih);

				if ($("#blok"+noLalu).position().top + $("#blok"+noLalu).height() > $("#colAnswer").position().top + $("#colAnswer").height()) {
					$("#colAnswer").animate({ scrollTop: $("#colAnswer").scrollTop() + 
					$("#blok"+noLalu).position().top - $("#colAnswer").position().top - $("#colAnswer").height() + $("#blok"+noLalu).height()*3}, 450);
				}
				else if ($("#blok"+noLalu).position().top < $("#colAnswer").position().top) {
					$("#colAnswer").animate({ scrollTop: $("#colAnswer").scrollTop() - 
					$("#colAnswer").position().top + $("#blok"+noLalu).position().top - $("#blok"+noLalu).height()*3}, 650);
				}
				$("#noSoal").blur();
				$("#portView").focus();

				$.ajax({
					type: "post",
					url: "saveLastNum.php",
					data: {rd:"<?= $hariKe; ?>", ni:"<?= $nomorInduk; ?>", np:"<?= $nomorPeserta; ?>", nl:noLalu, ti:xCount},
					cache: false,
					success: function() {}
				});				
			};
			// ################################################ //

			// ############## milih blok soal ################# //			
			$.gantiBlok = function(soalKe) {
				//fungsi untuk mengganti soal jika dipilih dari blok soal
				if (soalKe!=noLalu) {
					nomor = soalKe;
					$("#noSoal").val(nomor).change();

					$.gantiSoal(nomor);
					if ($("#blok"+nomor).position().top + $("#blok"+nomor).height() > $("#colAnswer").position().top + $("#colAnswer").height()) {
						$("#colAnswer").animate({ scrollTop: $("#colAnswer").scrollTop() + 
						$("#blok"+nomor).position().top - $("#colAnswer").position().top - $("#colAnswer").height() + $("#blok"+nomor).height()*3}, 450);
					}
					else if( $("#blok"+nomor).position().top < $("#colAnswer").position().top) {
						$("#colAnswer").animate({ scrollTop: $("#colAnswer").scrollTop() - 
						$("#blok"+nomor).height()*3}, 650);
					}
				}
			};
			// ################################################ //

		}(jQuery));

		function ngisiJawaban() {
			isian = $("#kolomIsian").val();

			if (isian == null || isian == '') {
				isian = " ";
				$("#jwb"+nomor).html("&nbsp;");
			}
			else {
				$("#jwb"+nomor).html(isian.charAt(0));
				$("#blok"+nomor).prop("title",isian);
			}

			var opsi = $('<option></option>').text(nomor+". ["+isian+"]");
			$("#soal"+nomor).empty().append(opsi);

			jawabanSoal[nomor] = isian;			
			$("#butirJwb").val(jawabanSoal.join('|'));

			uj = jawabanSoal.join('|');
			$.ajax({
				type: "post",
				url: "tempAnswerSave.php",
				data: {rd:"<?= $hariKe; ?>", ni:"<?= $nomorInduk; ?>", np:"<?= $nomorPeserta; ?>", ta:uj, ti:xCount},
				cache: false,
				success: function()
				{}
			});

			//$("#noSoal").val(nomor).change();
		};
		
		function milihJawaban() {	
			jwbNo = $("input[name='pil']:checked").val();
			if (jwbNo == null || jwbNo == '') {
				jwbNo = " ";
				$("#jwb"+nomor).html("&nbsp;");
			}
			else {
				$("#jwb"+nomor).html(jwbNo.charAt(0));
				$("#blok"+nomor).prop("title", jwbNo);
			}
			
			var option = $('<option></option>').text(nomor+". ["+jwbNo+"]");
			$("#soal"+nomor).empty().append(option);

			jawabanSoal[nomor] = jwbNo;			
			$("#butirJwb").val(jawabanSoal.join('|'));

			chooseAnswer = true;
		    $("#noSoal").val(nomor).change();

			//////////////////////////////////////////////////////////////////////////////////////
			
			uj = jawabanSoal.join('|');
			$.ajax({
				type: "post",
				url: "tempAnswerSave.php",
				data: {rd:"<?= $hariKe; ?>", ni:"<?= $nomorInduk; ?>", np:"<?= $nomorPeserta; ?>", ta:uj, ti:xCount},
				cache: false,
				success: function() {}
			});
			
			$("#portView").focus();
			$("#pilA").blur();
			$("#pilB").blur();
			$("#pilC").blur();
			$("#pilD").blur();
			$("#pilE").blur();							
	    };
		
		function gantiRagu() {	
			raguNo = $("input[name='ragu']:checked").val();

			if (raguNo=="on")
			{ rNum = "1"; } else { rNum = " "; }
			
			nomorRagu[nomor] = rNum;
			nr = nomorRagu.toString();

			$.ajax({
				type: "post",
				url: "raguAnswerSave.php",
				data: {rd:"<?= $hariKe; ?>", ni:"<?= $nomorInduk; ?>", np:"<?= $nomorPeserta; ?>", jx:nr},
				cache: false,
				success: function() {}
			});
			
			$("#portView").focus();
			$("#ragu").blur();
	    };

// ======================================== dialogform control =========================================

		// ............................................................
			$("#OkeKak").click(function () {	
				indNo = $("#noInd").val();
				pesNo = $("#noPes").val();
				xDown = 1;	//klo sudah diklik Oke, kembali jalankan counter

				btimer = setInterval(runChecking, 60000);	//kembalikan lagi btimer aktif utk pesan2 berikutnya dari admin
				document.getElementById("adVideo").pause();
			});
		
		// -------------------------------------------------------------------

// =========================================================================================================
			
		/*
		$("#audioPlace").on('ended', function() {
			clipSnd = "#" + $("#noSoal").val() + ".ogg#";
			adaAu = sndAudio.indexOf(clipSnd);
			if(adaAu<0)
			{ sndAudio = sndAudio.concat(clipSnd); }
		});
		*/

		$("#kurang").mousedown(function () {
			nomor = $("#noSoal").val();
			if (nomor!=0) {	
				if (nomor == minNomor) {nomor = minNomor + 1}
				if (nomor > minNomor) {	
					nomor--;
					$("#noSoal").val(nomor).change();
					$.gantiSoal(nomor);
				}
			}	
		});

		$("#tambah").mousedown(function () {
			nomor = $("#noSoal").val();
				if (nomor == iNTot) {nomor = iNTot - 1}
				if (nomor < iNTot) {	
					nomor++;
					$("#noSoal").val(nomor).change();
					$.gantiSoal(nomor);
				}
		});

		$("#noSoal").change(function() {
			if (!chooseAnswer) {
				nomor = $("#noSoal").val();
				$.gantiSoal(nomor);
			}

			else
			{ chooseAnswer = false; }
		});

		$("input[name='pil']").click(function() {
			diPilih = $("input[name='pil']:checked").val();
			if (diPilih == jwbNo) {
				if (opsiErase == 1)
				{ $("#pil"+diPilih).prop("checked", false); }
			}
			milihJawaban();
		});

		$("#kolomIsian").keyup(function() { ngisiJawaban(); });

		$("input[name='pil']").change(function() { milihJawaban(); });

		$("input[name='ragu']").change(function() { gantiRagu(); });

		$("#ragu").click(function()
		{ $("#ragu").trigger('change'); });

	    $("input[name='confirmFinish']").change(function() {
			diYakini = $(this).is(":checked");
			if (!diYakini)
			{ $("#okeFinish").prop("disabled", true).addClass("ui-disabled"); }

			else
			{ $("#okeFinish").removeClass("ui-disabled"); }
		});
			
		$("#finishBtn").click(function() {
			nI = "<?= $nomorInduk; ?>";
			nP = "<?= $nomorPeserta; ?>";
			hK = "<?= $hariKe; ?>";

			$.ajax({
				type: "post",
				url: "ajaxloadJwbAndRagu.php",
				data: {nID:nI, nPE:nP, hKe:hK},
				cache: false,
				dataType: "json",
				success: function(jsonData) {
					jwbnya = jsonData[0];
					ragunya = jsonData[1];

					$("#konfirmSoal").html("");
					$("#adaSoalBelumDijawab").html("");
					$("#adaSoalMasihRagu").html("");

					if (jwbnya=='' || !jwbnya)
					{ banyakSoalBelumDijawab = iNTot; }
					else {
						var banyakSoalBelumDijawab = jwbnya.toString().split(/ /g).length;
						banyakSoalBelumDijawab -= 2;
					}

					if (ragunya=='' || !ragunya)
					{ banyakSoalMasihRagu = 0; }
					else {
						var banyakSoalTidakRagu = ragunya.toString().split(/ /g).length;
						banyakSoalMasihRagu = iNTot + 2 - banyakSoalTidakRagu;
					}
					
					if (banyakSoalBelumDijawab!=0 || banyakSoalMasihRagu!=0) {
						$("#konfirmSoal").html("Masih terdapat ");
						if (banyakSoalBelumDijawab > 0 && banyakSoalMasihRagu == 0) {
							$("#adaSoalBelumDijawab").html("<b>" + banyakSoalBelumDijawab + " dari " + iNTot + "</b> soal belum dijawab.<br><br>");
							$("#adaSoalMasihRagu").html("");
						}
						else if (banyakSoalBelumDijawab == 0 && banyakSoalMasihRagu > 0) {
							$("#adaSoalBelumDijawab").html("");
							$("#adaSoalMasihRagu").html("<b>" + banyakSoalMasihRagu + " soal</b> ragu-ragu.<br><br>");
						}
						else {
							$("#adaSoalBelumDijawab").html("<b>" + banyakSoalBelumDijawab + " dari " + iNTot + "</b> soal belum dijawab dan ");
							$("#adaSoalMasihRagu").html("<b>" + banyakSoalMasihRagu + " soal</b> ragu-ragu.<br><br>");
						}
					}
					
					var cF = $("input[name='confirmFinish']");
			        cF.prop("checked", false).checkboxradio("refresh");
			        cF.trigger('change');
					$("#popupDlgFinish").popup("open");

					$("#audioPlace").trigger('pause');
				}
			});
		});

		btimer = setInterval(runChecking, 60000);

		function runChecking() {
			if (xCount < durasiTO) {
				//simpan waktu sementara
				$.ajax({
					type: "post",
					url: "saveLastTime.php",
					data: {rd:"<?= $hariKe; ?>", ni:"<?= $nomorInduk; ?>", np:"<?= $nomorPeserta; ?>", ti:xCount},
					dataType: "json",
					cache: false,
					success: function(getR) {
						var isiWarn = getR[0];
						var finishKan = getR[1];
						var newTime = getR[2];

						if (!isiWarn){ isiWarn=''; }
						if (isiWarn!='') {
							$("#OkeKak").show();

							$("#noInd").val('<?= $nomorInduk; ?>');
							$("#noPes").val('<?= $nomorPeserta; ?>');

							if (isiWarn.indexOf("#") >= 0) {
								partPesan = isiWarn.split("#");

								pesan = partPesan[0];
								labelOk = partPesan[1];
							}
							else {
								pesan = isiWarn;
								labelOk = "OK";
							}

							//memeriksa apakah ada reserved word utk menampilkan video atau gambar pada pesan, sbg media iklan
							rVid = "+vid:";		//reserved word utk menampilkan video
							rImg = "+img:";		//reserved word utk menampilkan gambar

							cekRc = pesan.substr(0,5);
							if (cekRc==rVid) {
								clearInterval(btimer);	//ini biar videonya bisa jalan terus sampai selesai

								fVideo = pesan.substr(5);	//ini nama file videonya
								pesanUtama = '&nbsp;&nbsp;&nbsp;Timer dihentikan sesaat untuk video berikut ini&nbsp;&nbsp;&nbsp;<br> <video style="max-width:calc(0.9 * 100vw); height:100%" controls autoplay id="adVideo"> <source src="admedia/'+fVideo+'"> </video>';
							}
							else if (cekRc==rImg) {
								fImage = pesan.substr(5);	//ini nama file imagenya
								pesanUtama = '&nbsp;&nbsp;&nbsp;Timer dihentikan sesaat untuk image berikut ini&nbsp;&nbsp;&nbsp;<br> <img style="max-width:calc(0.9 * 100vw); height:100%" src="admedia/'+fImage+'">';
							}
							else
							{ pesanUtama = pesan; }

							$("#isiPesanChat").html("   "+pesanUtama+"   ");
							$("#OkeKak").text(" "+labelOk+" ");

							$("#dialogChat").popup("open");

							//hentikan sementara timer
							xDown = 0;
						}

						if (newTime>0)
						{ xCount = newTime; }
						
						if (finishKan!='0')
						{ cekFinishTO(); }
					}
				});
			}
		}

		// Attach the event keydown to some keys
		$(document).keydown(function(k) {

			// keycode 37 left arrow, 39 right arrow, 38 up arrow, 40 down arrow
			if (k.keyCode == 37) {
				if (tipeSoal=="pilgan")
				{$("#kurang").trigger('mousedown');}
			}

			else if (k.keyCode == 39) {
				if (tipeSoal=="pilgan")
				{$("#tambah").trigger('mousedown');}
			}

			else if (k.keyCode == 38) {
				if (tipeSoal=="pilgan") {
					if(Number(noLalu) > 4) {
						nomor -= 4;
						var pindahKe = Number(noLalu)-4;
						$("#noSoal").val(pindahKe).change();
						$.gantiSoal(pindahKe);
					}
				}
			}
		
			else if (k.keyCode == 40) {
				if (tipeSoal=="pilgan") {
					if(Number(noLalu) < iNTot-3) {
						nomor += 4;
						var pindahKe = Number(noLalu)+4;
						$("#noSoal").val(pindahKe).change();
						$.gantiSoal(pindahKe);
					}
				}
			}

			/*
			if (k.keyCode == 35) {
				nomor = iNTot;
				var pindahKe = iNTot;
				$("#noSoal").val(pindahKe).change();
				$.gantiSoal(pindahKe);
			}

			else if (k.keyCode == 36) {
				nomor = 1;
				var pindahKe = 1;
				$("#noSoal").val(pindahKe).change();
				$.gantiSoal(pindahKe);
			}
			*/

			/*
			if (k.charCode == 82 || k.charCode == 114) {
				diRagu = $("#ragu").is(":checked");
				$("#ragu").prop('checked', !diRagu).checkboxradio("refresh");
				$("input[name='ragu']").trigger("change");
			}
			*/
			
			else if (k.keyCode == 65) {
				if (tipeSoal=="pilgan") {
					if (!$("#pilA").is(":checked")) {
						$("#pilA").prop("checked", true);
						$("#pilA").trigger('click');
					}
					else {
						if (opsiErase == 1) {
							$("#pilA").prop("checked", false).checkboxradio("refresh");
						}
					}
				}
			}
			
			else if (k.keyCode == 66) {
				if (tipeSoal=="pilgan") {
					if (!$("#pilB").is(":checked")) {
						$("#pilB").prop("checked", true);
						$("#pilB").trigger('click');
					}
					else {
						if (opsiErase == 1)
						{ $("#pilB").prop("checked", false).checkboxradio("refresh"); }
					}
				}
			}

			else if (k.keyCode == 67) {
				if (tipeSoal=="pilgan") {
					if (opsiN>2) {
						if (!$("#pilC").is(":checked")) {
							$("#pilC").prop("checked", true);
							$("#pilC").trigger('click');
						}
						else {
							if (opsiErase == 1)
							{ $("#pilC").prop("checked", false).checkboxradio("refresh"); }
						}
					}
				}
			}

			else if (k.keyCode == 68) {
				if (tipeSoal=="pilgan") {
					if (opsiN>3) { 
						if (!$("#pilD").is(":checked")) {
							$("#pilD").prop("checked", true);
							$("#pilD").trigger('click');
						}
						else {
							if (opsiErase == 1)
							{ $("#pilD").prop("checked", false).checkboxradio("refresh"); }
						}
					}
				}
			}

			else if (k.keyCode == 69) {
				if (tipeSoal=="pilgan") {
					if (opsiN>4) { 
						if (!$("#pilE").is(":checked")) {
							$("#pilE").prop("checked", true);
							$("#pilE").trigger('click');
						}
						else {
							if (opsiErase == 1)
							{ $("#pilE").prop("checked", false).checkboxradio("refresh"); }
						}
					}
				}
			}

			/*
			else if (k.charCode == 70 || k.charCode == 102)
			{ $("#finishBtn").trigger('click'); }
			*/

			if (k.keyCode >= 64 && k.keyCode <= 69) {
				if (tipeSoal=="pilgan")
				{ $("input[name='pil']").trigger('change'); }
			}

		});
		//
	});
</script>
</body>
</html>