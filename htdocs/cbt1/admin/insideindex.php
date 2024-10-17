<?php

    session_start();
    $admToLog = false;
    $adm2Log = false;

    $cap = "xxx";
    $captchaTxt = $usern = $passw = "";

        if (isset($_SESSION['captchaCode']))
        { $cap = $_SESSION['captchaCode']; }

        if (isset($_POST['txtCaptcha']))
        { $captchaTxt = $_POST['txtCaptcha']; }

        if (isset($_POST['usern']))
        { $usern = $_POST['usern']; }

        if (isset($_POST['passw']))
        { $passw = $_POST['passw']; }


    if ($cap!=$captchaTxt || $usern=="" || $passw=="") {
        session_destroy();
        echo ('<script type="text/javascript">');
        echo ('window.location="index.php";');
        echo ('</script>');
        exit();
    }


    require_once('../koneksi_db.php');

    //ambil data-data variabel dari tabel datasystem
    $cekDataSistem = mysqli_query($con, "SELECT * FROM datasystem WHERE id = '1'");
    $hasilDataSistem = mysqli_fetch_array($cekDataSistem);

    $adminUN = $hasilDataSistem['unadmin'];
    $adminPW = $hasilDataSistem['pwadmin'];
    $admin2UN = $hasilDataSistem['unadmin2'];
    $admin2PW = $hasilDataSistem['pwadmin2'];

    if ($usern == $adminUN && $passw == $adminPW)
    { $admToLog = true; }
    else if ($usern == $admin2UN && $passw == $admin2PW)
    { $adm2Log = true; }


    if (!($admToLog || $adm2Log)) {
        session_destroy();
        echo ('<script type="text/javascript">');
        echo ('window.location="index.php";');
        echo ('</script>');
        exit();
    }


    $CBTversion = $hasilDataSistem['versi'];

    $mainTitle = $hasilDataSistem['bigTitle'];
    $namaSekolahFront = $hasilDataSistem['namaSekolah'];
    $pelaksanaan = $hasilDataSistem['teksPelaksanaan'];
    $uPeserta = $hasilDataSistem['uploadPeserta'];
    $showSearch = $hasilDataSistem['searchNama'];
    $teksno1 = $hasilDataSistem['labelno1'];
    $teksno2 = $hasilDataSistem['labelno2'];
    $nlbl1 = $hasilDataSistem['nlabel1'];
    $nlbl2 = $hasilDataSistem['nlabel2'];
    $showMode = 'allgrup';
    $modeSort = 'nomorUrut';

    $motoSekolahFront = $hasilDataSistem['motoSekolah'];
    $inpHP = $hasilDataSistem['tampilHP'];
    $inpEmail = $hasilDataSistem['tampilEmail'];
    $inpDoB = $hasilDataSistem['tampilDoB'];
    $pilJurTampil = $hasilDataSistem['tampilPilJur'];
    $prefixid1 = $hasilDataSistem['prefixid1'];
    $prefixid2 = $hasilDataSistem['prefixid2'];
    $inp2pw = $hasilDataSistem['label2password'];
    $opsiN = $hasilDataSistem['nOpsi'];
    $erasableOpsi = $hasilDataSistem['opsiErasable'];
    $showScore = $hasilDataSistem['tampilSkor'];
    $scB = $hasilDataSistem['sB'];
    $scS = $hasilDataSistem['sS'];
    $scK = $hasilDataSistem['sK'];
    $sekala = $hasilDataSistem['skala'];
    $hasilditabel = $hasilDataSistem['tabelhasil'];

    date_default_timezone_set('Asia/Jakarta'); 		//set timezone

    $thn_ = date("Y");

    $sPS = array();
    $c = 0;	//counter

    //ambil data admin dari tabel dataadmin
    $cekDataAdmin = mysqli_query($con,"SELECT nama, nokontak, grupwa FROM dataadmin WHERE id = '1'");
    $hasilDataAdmin = mysqli_fetch_array($cekDataAdmin);

    $adminName = $hasilDataAdmin['nama'];
    $adminNo = $hasilDataAdmin['nokontak'];
    $wagrup = $hasilDataAdmin['grupwa'];

    //lihat shift aktif
    $ambilShift = mysqli_query($con, "SELECT no, shift, jamMulai, mntMulai, bolehTelat FROM shifttes WHERE aktifFlag = 1");
    if (mysqli_num_rows($ambilShift)==0) {
        //klo krna "sesuatu" tidak ada shift aktif, set dulu shift aktifnya ke default ...
        mysqli_query($con, "UPDATE shifttes SET aktifFlag=1 WHERE no = 1");
        //ulangi lagi ambilshift
        $ambilShift = mysqli_query($con, "SELECT no, shift, jamMulai, mntMulai, bolehTelat FROM shifttes WHERE aktifFlag = 1");
    }

    $dapatShift = mysqli_fetch_array($ambilShift);
    $idShiftAktif = $dapatShift['no'];

    if ($dapatShift['bolehTelat']==0)
    { $cTelat = ''; } else { $cTelat = ' &#9749;'; }
    $shiftAktif = $dapatShift['shift'].') '.$cTelat;

    $waktuShift = $dapatShift['jamMulai'].":".$dapatShift['mntMulai'];
    if(empty($shiftAktif)) {
    	$shiftAktif=0;
    	$waktuShift = "0:0";
    }

    //Hitung total siswa
    $siswa = mysqli_query($con,"SELECT nama FROM absensiharitespeserta WHERE nama!=''");
    $totalSiswa = mysqli_num_rows($siswa);

    $sampleIDFormat = mysqli_query($con,"SELECT nomorInduk, nomorPeserta FROM absensiharitespeserta WHERE id=1");
    $sampleData =mysqli_fetch_array($sampleIDFormat);
    if ($sampleData) {
        $sampleID1 = $sampleData['nomorInduk'];
        $sampleID2 = $sampleData['nomorPeserta'];
    }
    else {
        $sampleID1 = '';
        $sampleID2 = '';  
    }

    $totSiswa = "";
    if ($totalSiswa>0)
    { $totSiswa = "<span style='color:white; background-color:grey; font-weight:bold'>&nbsp;".$totalSiswa."&nbsp;</span>"; }

    //Hitung set & paket soal yang sudah ada
    $sets = mysqli_query($con,"SELECT id FROM naskahsoal WHERE kodeSoal!=''");
    $totSet = mysqli_num_rows($sets);

    if ($totSet>0)
    { $totalSet = "<span style='color:white; background-color:grey; font-weight:bold'>&nbsp;".mysqli_num_rows($sets)."&nbsp;</span>"; }
    else
    { $totalSet = "0"; }

    $setaktif = mysqli_query($con,"SELECT id FROM naskahsoal WHERE kodeSoal!='' AND statusAktif=1");
    $totAktif = mysqli_num_rows($setaktif);

    $cN = count(glob("../images/soal/*"));
    $cN--;
    $totPaket = $cN;
    if ($totPaket>0)
    { $totPaket = "<span style='color:white; background-color:grey; font-weight:bold'>&nbsp;".$cN."&nbsp;</span>"; }

    //Hitung total kode jurusan
    $nprodi = mysqli_query($con,"SELECT kodeprodi FROM kodejurusan WHERE kodeprodi!=''");
    $totKodeJur = mysqli_num_rows($nprodi);
    if ($totKodeJur>0)
    { $totKodeJur = "<span style='color:white; background-color:#34599E; font-weight:bold'>&nbsp;".mysqli_num_rows($nprodi)."&nbsp;</span>"; }

    //Lacak media yg tersimpan dlm folder admedia
    $listMedia = "";
    $mediake_n = 0;
    foreach (glob("../admedia/*.*") as $mediaFile) {
    	$mediake_n++;
        $mediaFileName = $mediake_n.". ".substr($mediaFile,11).";   ";
        $listMedia .= $mediaFileName;
    }

    // for($x = 0; $x < $nlength; $x++)
    // { echo '<a href="#" class="btnRestoreDB" bname="'.$files[$x].'" oncontextmenu="return false;" style="font-size:15px;color:darkblue;text-decoration: none" title="klik ganda merestore, klik kanan menghapus">'.$files[$x].'</a><br>'; }

?>

<!DOCTYPE html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBT <?= substr($namaSekolahFront,0,30); ?></title>
	<link rel="shortcut icon" href="../images/nf-favico.ico" type="image/x-icon">

    <link rel="stylesheet" href="uploadfile.css" />
    <link rel="stylesheet" href="../jquery-ui-1.8.10.custom.css" />
	<link rel="stylesheet" href="../jquery.ui.theme.css" />
    <link rel="stylesheet" href="adminstyle.css" />
    <link rel="stylesheet" href="tabulator5.4/css/tabulator.css" />

	<script type="text/javascript" src="../jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="../jquery-ui-1.8.10.custom.min.js"></script>

  	<script type="text/javascript" src="../jquery.validationEngine.js"></script>
  	<script type="text/javascript" src="../jquery.validationEngine-en.js"></script>
  	<link rel="stylesheet" type="text/css" href="../validationEngine.jquery.css" />

    <!-- Dua baris di bawah ini utk menangani upload file dan harus menyertakan keduanya,
    	karena jquery.uploadfile membutuhkan jquery.form utk berjalan -->
	<script type="text/javascript" src="../jquery.form.js"></script>
	<script type="text/javascript" src="../jquery.uploadfile.min.js"></script>

    <style>
		
		#tabelDLHasil td {
			padding:10px;
			cursor:pointer;
			font-family:Tahoma, Geneva, sans-serif;
			font-size:14px;
		}
		
		#tabelDLHasil td:hover {
			background-color:#AAD4FF;
			color:#00C;
		}
		
		input[type="button"], input[type="file"], input[type="submit"] { 
			background-color:#527FC9;
			color:white; 
			font: bold 90% 'trebuchet ms',helvetica,sans-serif;
			border: 0px solid;
			border-radius:5px;
			padding: 4px 3px 3px 3px;
			cursor:pointer;
		}
		
		input[type="file"] {
			border: 1px dashed grey;
			background-color:#FEFED0;
			color:#909090;
			padding: 1px 1px 1px 1px;
		}
		
		input[type="button"]:hover, input[type="submit"]:hover {
			background-color:#CAD8EE;
			color:#527FC9;
		}

        .btnvPage {
            font-size: 17px;
            cursor: pointer;
        }

        .btnvPage, #pgNumber {
            user-select: none;
        }

        <?php
            if ($adm2Log) {
                echo "
                    #divUpDBSoal, #divUpPktSoal {
                        display: none;
                        opacity: 0.55;
                    }";
            }
        ?>

        <?php
            if ($adm2Log) {
                echo "
                    a[href='#tab-3'], a[href='#tab-4'], #tglUploadPeserta {
                        display: none;
                    }
                    #divkunciJwb, input[name='uploadItem'], #btnUploadImgSoal {
                        display: none;
                        opacity: 0.55;
                    }";
            }
        ?>
		
	</style>
    
</head>

<body>

<!-- ## form dialogAddNewShift ## -->
<div id="dialogAddNewShift" title="Tambah Shift Baru">
	<br> 
	<form>
        <center>
        <table border="0" style="margin-left:15px; border-collapse: collapse; font-family:Verdana, Geneva, sans-serif">
        	<tr>
            	<td>Shift</td> <td>&nbsp;:&nbsp;</td> <td><input name="harike" id="harike" type="text" size="3" maxlength="2" style="text-align:center"></td>
            </tr>
            <tr style="height: 3px"></tr>
            <tr>
            	<td>Grup Siswa</td> <td>&nbsp;:&nbsp;</td> <td><input name="shift" id="shift" type="text" size="3" maxlength="2" style="text-align:center"></td>
            </tr>
            <tr style="height: 3px"></tr>
            <tr>
            	<td>Jam Mulai</td> <td>&nbsp;:&nbsp;</td> <td><input name="jamnya" id="jamnya" type="text" size="3" maxlength="2" style="text-align:center"></td>
            </tr>
            <tr style="height: 3px"></tr>
            <tr>
            	<td>Menit Mulai</td> <td>&nbsp;:&nbsp;</td> <td><input name="menitnya" id="menitnya" type="text" size="3" maxlength="2" style="text-align:center"></td>
            </tr>
            <tr style="height: 3px"></tr>
            <tr>
            	<td>Durasi Login</td> <td>&nbsp;:&nbsp;</td> <td><input name="durasinya" id="durasinya" type="text" size="3" maxlength="3" style="text-align:center"></td>
            </tr>
            <tr style="height: 3px"></tr>
            <tr>
            	<td>Telat Login</td> <td>&nbsp;:&nbsp;</td>
            	<td>
	            	<select id="ketelatan" name="ketelatan">
	            			<option value="0" selected>Dikurangi</option>
	            			<option value="1">Diabaikan</option>
	            	</select>
            	</td>
            </tr>
        </table>
    
        <br><br>
        
        <input type="button" id="btnSaveNewShift" value="&nbsp;Simpan&nbsp;">
        </center>
	</form>
	<br>
</div>

<!-- ## form dialogEditShift ## -->
<div id="dialogEditShift" title="Edit Shift">
	<br> 
	<form>
        <center>
        <table border="0" style="margin-left:15px; border-collapse: collapse; font-family:Verdana, Geneva, sans-serif">
        	<tr>
            	<td>Shift</td> <td>&nbsp;:&nbsp;</td> <td><input name="ohari" id="ohari" type="text" size="3" maxlength="2" style="text-align:center"></td>
            </tr>
            <tr style="height: 3px"></tr>
            <tr>
            	<td>Grup Siswa</td> <td>&nbsp;:&nbsp;</td> <td><input name="oshift" id="oshift" type="text" size="3" maxlength="2" style="text-align:center"></td>
            </tr>
            <tr style="height: 3px"></tr>
            <tr>
            	<td>Jam Mulai</td> <td>&nbsp;:&nbsp;</td> <td><input name="ojam" id="ojam" type="text" size="3" maxlength="2" style="text-align:center"></td>
            </tr>
            <tr style="height: 3px"></tr>
            <tr>
            	<td>Menit Mulai</td> <td>&nbsp;:&nbsp;</td> <td><input name="omnt" id="omnt" type="text" size="3" maxlength="2" style="text-align:center"></td>
            </tr>
            <tr style="height: 3px"></tr>
            <tr>
            	<td>Durasi Login</td> <td>&nbsp;:&nbsp;</td> <td><input name="odur" id="odur" type="text" size="3" maxlength="3" style="text-align:center"></td>
            </tr>
            <tr style="height: 3px"></tr>
            <tr>
            	<td>Telat Login</td> <td>&nbsp;:&nbsp;</td>
            	<td>
	            	<select id="otelat" name="otelat">
	            			<option value="0" selected>Dikurangi</option>
	            			<option value="1">Diabaikan</option>
	            	</select>
            	</td>
            </tr>
        </table>
    
        <br><br>
        
        <input type="button" id="btnEditShift" value="&nbsp;Simpan&nbsp;">
        </center>
	</form>
	<br>
</div>

<!-- ## form dialogDeleteShift ## -->
<div id="dialogDeleteShift" title="Delete Shift">
	<br> 
	<form>
        <center>
        Yakin akan menghapus<br>
        <span id='dataShiftMauDihapus'> </span> <br>
        (tidak bisa dibatalkan)
        <br><br>
        
        <input type="button" id="btnDelShift" value="&nbsp;Yakin, hapus Shift ini !!&nbsp;">
        </center>
	</form>
	<br>
</div>

<!-- ## form dialogDownloadHasil ## -->
<div id="dialogDownloadHasil" title="Download Hasil CBT">
	<br>
        <form action="hasilmentahall.php" enctype="multipart/form-data" id="formAll" target="_parent"> </form>
        <form action="hasilipaexcel.php" enctype="multipart/form-data" id="formIPA" target="_parent"> </form>
        <form action="hasilipsexcel.php" enctype="multipart/form-data" id="formIPS" target="_parent"> </form>
        
        <span id="dALL" style="cursor:pointer;font-weight:bold;font-size:14px;color:blue" title="download hasil mentah total (untuk TO SBMPTN, diproses oleh TI Polda)">Hasil Mentah Total (.xlsx)</span> <br>
        <span id="dIPA" style="cursor:pointer;font-weight:bold;font-size:14px;color:blue" title="download hasil IPA">Hasil IPA (.xlsx)</span> <br>
        <span id="dIPS" style="cursor:pointer;font-weight:bold;font-size:14px;color:blue" title="download hasil IPS">Hasil IPS (.xlsx)</span> <br>
		<br>
        
        <span style="font-weight:bold;font-size:14px;">Per Bidang Studi</span><br>
        <table id="tabelDLHasil" border="1px" bordercolor="#AAAAAA" style="padding:5px; border-collapse:collapse; box-shadow:2px 2px 2px #707070; margin-top:3px;">
        	<tr>
            	<td class="txtdlmateri" nmtabelDB="hasilipa" nmBS="BS1">BS1 IPA</td>
            	<td class="txtdlmateri" nmtabelDB="hasilipa" nmBS="BS2">BS2 IPA</td>
            	<td class="txtdlmateri" nmtabelDB="hasilipa" nmBS="BS3">BS3 IPA</td>
            	<td class="txtdlmateri" nmtabelDB="hasilipa" nmBS="BS4">BS4 IPA</td>
            	<td class="txtdlmateri" nmtabelDB="hasilipa" nmBS="BS5">BS5 IPA</td>
            	<td class="txtdlmateri" nmtabelDB="hasilipa" nmBS="BS6">BS6 IPA</td>
            	<td class="txtdlmateri" nmtabelDB="hasilipa" nmBS="BS7">BS7 IPA</td>
            	<td class="txtdlmateri" nmtabelDB="hasilipa" nmBS="BS8">BS8 IPA</td>
            	<td class="txtdlmateri" nmtabelDB="hasilipa" nmBS="BS9">BS9 IPA</td>
            	<td class="txtdlmateri" nmtabelDB="hasilipa" nmBS="BS10">BS10 IPA</td>
            </tr>
            <tr>	
                <td class="txtdlmateri" nmtabelDB="hasilips" nmBS="BS1">BS1 IPS</td>
                <td class="txtdlmateri" nmtabelDB="hasilips" nmBS="BS2">BS2 IPS</td>
            	<td class="txtdlmateri" nmtabelDB="hasilips" nmBS="BS3">BS3 IPS</td>
            	<td class="txtdlmateri" nmtabelDB="hasilips" nmBS="BS4">BS4 IPS</td>
            	<td class="txtdlmateri" nmtabelDB="hasilips" nmBS="BS5">BS5 IPS</td>
            	<td class="txtdlmateri" nmtabelDB="hasilips" nmBS="BS6">BS6 IPS</td>
            	<td class="txtdlmateri" nmtabelDB="hasilips" nmBS="BS7">BS7 IPS</td>
            	<td class="txtdlmateri" nmtabelDB="hasilips" nmBS="BS8">BS8 IPS</td>
            	<td class="txtdlmateri" nmtabelDB="hasilips" nmBS="BS9">BS9 IPS</td>
            	<td class="txtdlmateri" nmtabelDB="hasilips" nmBS="BS10">BS10 IPS</td>
            </tr>
        </table>

        <form action="hasilperbs.php" method="get" enctype="multipart/form-data" id="formPerMateri" target="_parent">
            <input name="tabelnya" id="tabelnya" type="hidden" value="">
            <input name="bidstudinya" id="bidstudinya" type="hidden" value="">
        </form>
	<br>
</div>

<!-- ## form dialogBackup ## -->
<div id="dialogBackup" title="Recovery">
	<br>
        <center>
            <strong><span style="font-size:15px;" id="labelRestorePoint"></span></strong>
            <br><br>
            <div id="listBackup" style="height: 120px; overflow: auto" oncontextmenu="return false"> </div>
        	<hr style="color:lightgrey; margin-top: 15px">
        	<br>
        	<input type="button" id="btnBackupNow" value="&nbsp;Backup Now&nbsp;" style="background-color: green">&nbsp;&nbsp;&nbsp;
    	    <input type="button" id="btnClearRestorePoint" value="&nbsp;Clear All Backups&nbsp;" style="background-color: #ff3333" title="klik ganda untuk menghapus semua backup point">
            <br>
        </center>
	<br>
</div>

<!-- ## form dialogRestore ## -->
<div id="dialogRestore" title="Restore" oncontextmenu="return false">
	<br> 
        <center>
        Anda akan merestore database ke backup point <br>
        <span id="namaRestoreP" style="font-weight:bold; color:red; padding:2px 2px 2px 2px; margin:7px 0px 2px 0px;"></span>
        <br><br>
        
        <input type="button" id="yesRestore" bHereR="" value="&nbsp;Ya, Restore database !!&nbsp;">
        </center>
	<br>
</div>

<!-- ## form dialogDeleteBackup ## -->
<div id="dialogDeleteBackup" title="Delete Backup" oncontextmenu="return false">
	<br> 
        <center>
        Anda akan menghapus backup <br>
        <span id="namaBackup" style="font-weight:bold; color:red; padding:2px 2px 2px 2px; margin:7px 0px 2px 0px;"></span>
        <br><br>
        
        <input type="button" id="yesDelBackup" bHere="" value="&nbsp;Ya, hapus backup ini !!&nbsp;">
        </center>
	<br>
</div>

<!-- ## form dialogDeleteAllBackup ## -->
<div id="dialogDeleteAllBackup" title="Delete All Backup" oncontextmenu="return false">
	<br> 
        <center>
        Anda akan menghapus semua backup point ? <br>
        <br><br>
        
        <input type="button" style="background-color:red;" id="yesDelAllBackup" value="&nbsp;Ya, hapus semua backup point !!&nbsp;" title="klik ganda">
        </center>
	<br>
</div>

<!-- ## form dialogAddPeserta ## -->
<div id="dialogAddPeserta" title="Tambah Peserta">
	<form id="formAddPeserta">
        <br>
        <center>
        <table id="tabelAddPeserta" border="0" style="margin-left:15px; border-collapse: collapse; font-family: arial; font-size: 15px">
        	<tr><td><?= $teksno1; ?></td> <td>&nbsp;:&nbsp;</td> <td>
        		<input name="dnisn" id="dnisn" class="validate[required]" data-prompt-position="topLeft:2" title="harus diisi" placeholder="<?= $nlbl1; ?> char. <?= $sampleID1; ?>" type="text" size="15" maxlength="<?= $nlbl1; ?>" style="padding-left:5px"> <span id="rcNoInd"> </span> </td></tr>
            <tr><td style="height: 4px"></td></tr>

            <tr><td><?= $teksno2; ?></td> <td>&nbsp;:&nbsp;</td> <td>
            	<input name="dnopes" id="dnopes" class="validate[required]" data-prompt-position="topLeft:2" title="harus diisi" placeholder="<?= $nlbl2; ?> char. <?= $sampleID2; ?>" type="text" size="15" maxlength="<?= $nlbl2; ?>" style="padding-left:5px"> <span id="rcNoPes"> </span> </td></tr>
            <tr><td style="height: 4px"></td></tr>

            <tr><td>Nama</td> <td>&nbsp;:&nbsp;</td> <td><input name="dnama" id="dnama" class="validate[required, minSize[3]]" data-prompt-position="topLeft:2" title="harus diisi" type="text" size="30" maxlength="40" style="padding-left:5px; text-transform: uppercase;"></td></tr>
            <tr><td style="height: 4px"></td></tr>

            <tr><td>Kelas / Sekolah</td> <td>&nbsp;:&nbsp;</td> <td><input name="dkelas" id="dkelas" type="text" size="20" style="padding-left:5px"></td></tr>
            <tr><td style="height: 4px"></td></tr>

            <tr><td>Jurusan TO</td> <td>&nbsp;:&nbsp;</td>
            	<td>
	            	<select name="djurusan" id="djurusan">
						<option val="A" selected>IPA</option>
						<option val="S">IPS</option>
						<option val="C">IPC</option>
					</select>
            	</td>
            </tr>
            <tr><td style="height: 4px"></td></tr>

            <tr><td>Paket Soal</td> <td>&nbsp;:&nbsp;</td> <td><input name="dpakso" id="dpakso" class="validate[required, custom[onlyNumberSp]]" data-prompt-position="topLeft:2" title="harus diisi, paket soal untuk siswa ini : 1, 2, dst..." type="text" size="3" style="text-align: center"></td></tr>
            <tr><td style="height: 4px"></td></tr>

            <tr><td>Grup Tes</td> <td>&nbsp;:&nbsp;</td> <td><input name="dgrup" id="dgrup" class="validate[required, custom[onlyNumberSp]]" data-prompt-position="topLeft:2" title="harus diisi, 1 digit, grup shift siswa ini" type="text" size="3" maxlength="3" style="text-align: center"></td></tr>
            <tr><td style="height: 4px"></td></tr>
            <tr><td style="height: 4px"></td></tr>

        </table>

        <br><br>

        <input type="submit" id="btnSavePeserta" value="&nbsp;Tambah Peserta&nbsp;">
        </center>
	</form>
	<br>
</div>

<!-- ## form dialogLogKeyAll ## -->
<div id="dialogLogKeyAll" title="LogKey All">
	<br>
	<b>Pilih group yang akan di-logkey</b><br><br>
	<form>
        <center>
        <input type="button" class="btnDlgCekPeserta" id="btnSetLogKeyThisGroup" value="&nbsp; Grup Shift Aktif &nbsp;">&nbsp;
        <input type="button" class="btnDlgCekPeserta" id="btnSetLogKeyAll" value="&nbsp; Semua Grup &nbsp;">
        </center>
	</form>
</div>

<!-- ## form dialogFinishAll ## -->
<div id="dialogFinishAll" title="Finish All">
	<br>
	<center>
		<select id="kSoal" name="kSoal">
                <option value="0" selected> Pilih Kode Soal &nbsp;&nbsp; </option>
                <?php
                    $cekKdSoal = mysqli_query($con,"SELECT DISTINCT kodeSoal, singkatanBS FROM naskahsoal");
                    while ($getKdSoal = mysqli_fetch_array($cekKdSoal))
                    {
                        $noKode = $getKdSoal['kodeSoal'];
                        $bidStudi = $getKdSoal['singkatanBS'];
                        {
                        	echo ('<option value="'.$noKode.'" >'.$noKode.' : '.$bidStudi.'</option>');
                        }
                    }
                ?>
        </select>
        <br><br>
        <input name="rJur" id="rJurA" type="radio" value="IPA"> <label for="rJurA">IPA</label> &nbsp;&nbsp;&nbsp;
        <input name="rJur" id="rJurS" type="radio" value="IPS"> <label for="rJurS">IPS</label>
        <br><br>
        <input type="button" id="btnYesFinishAll" value="&nbsp; Finishkan &nbsp;">
    </center>

</div>

<!-- ## form dialogSetTimer ## -->
<div id="dialogSetTimer" title="Set Timer">
	<br>
    <center>
    <b>Isi dengan format h:m:s</b><br>
    atau +/-h:m:s<br><br>
        <input type="text" id="newTime" maxlength="8" size="12" style="text-align:center" placeholder="h:m:s"><br><br>
        <input type="button" id="btnSetNewTimer" value="&nbsp; Set Peserta Ini &nbsp;" style="margin-bottom:4px"><br><input type="button" id="btnSetNewTimerAll" value="&nbsp; Set Semua Peserta &nbsp;">
    </center>
	<br>
</div>

<!-- ## form dialogSearchGo ## -->
<div id="dialogSearchGo" title="Search Box">
	<div style="margin-bottom: 15px;">
		<select id="cariData" style="padding:2px; padding-bottom:3px">
      		<option value="nama" selected="">Nama</option>
      		<option value="nomorInduk" id='teksDataLog1'><?= $teksno1; ?></option>
      		<option value="nomorPeserta" id='teksDataLog2'><?= $teksno2; ?></option>
      	</select>

      	<input type="text" id="searchFor" size="25" style="padding:2px; padding-bottom:3px;">
      	<input type="button" id="GoSearch" value="Go" style="display: none">
	</div>

    <div id="searchResult1" style="font-family: arial; overflow: auto">

    </div>
</div>

<!-- ## form dialogCekPeserta ## -->
<div id="dialogCekPeserta" title="Control Peserta">

	<input type="hidden" id="idPesertaIni">
	<div style="font-family: arial">
        <span id="noUrutID"></span>. 
		<span style="font-weight: bold; text-transform: uppercase;" id="teksNamaSiswa"></span><br>
    	<span id="textID1"><?= $teksno1; ?></span> : <b><span id="dataNISiswa"></span></b> - 
        <span id="textID2"><?= $teksno2; ?></span> : <b><span id="dataNPSiswa"></span></b><br>
        Paket Soal : <b>[<span id="petaSoalx"></span>]</b> - Kelas : <b><span id="klsPesertaIni"></span></b>
        <div style="height: 7px"></div>
        No. HP : <b><span id="dataPhoneSiswa"></span></b><br>
        Email : <b><span id="dataEmailSiswa"></span></b><br><br>
	</div>

    <center>
        <!-- elemen tersembunyi, yg class nya disediakan oleh jquery ui, utk menjebak focus pertama
        yg defaultnya adalah element input text pertama -->
        <span class="ui-helper-hidden-accessible"><input type="text"/></span>
        <!-- ====================================================== -->

        <input type="text" id="inputDataBaru" align="middle" size="45" style="text-align:center; color:grey; margin-bottom:7px; font-family: Arial; font-size: 16px; padding:3px;"
          title="isi data baru di sini, baru klik tombol edit di bawah"><br>

        <input type="button" class="btnDlgCekPeserta" id="btnSaveNamaBaru" style="background-color:#0C6" value="&nbsp;Edit Nama&nbsp;">&nbsp;
        <input type="button" class="btnDlgCekPeserta" id="btnSaveNewID1" style="background-color:#0C6" value="&nbsp;Edit <?= $teksno1; ?>&nbsp;">&nbsp;
        <input type="button" class="btnDlgCekPeserta" id="btnSaveNewID2" style="background-color:#0C6" value="&nbsp;Edit <?= $teksno2; ?>&nbsp;"><br>
        <div style="height:5px"></div>
        <input type="button" class="btnDlgCekPeserta" id="btnGantiNoHP" style="background-color:#0C6" value="&nbsp; Edit No. HP&nbsp; ">&nbsp;
        <input type="button" class="btnDlgCekPeserta" id="btnGantiEmail" style="background-color:#0C6" value="&nbsp; Edit Email&nbsp; ">
        <div style="height:5px"></div>

        Grup : <input type="text" id="dataShiftSiswa" align="middle" size="4" style="text-align:center; margin-bottom:7px;">
        <input type="button" class="btnDlgCekPeserta" id="btnGantiShift" style="background-color:#0C6" value="&nbsp; Edit Grup&nbsp; ">
        &emsp;
        <input type="button" id="btnGantiIPAIPS" title="" style="background-color:#0C6" value="&nbsp; IPA/S&nbsp; ">
        <br><br>

        <div style="color:#527FC9">
	        <input type="button" class="btnDlgCekPeserta" id="btnSetLogKey" value="&nbsp; LogKey 1&nbsp; " title="LogKey peserta ini"> &bull;
	        <input type="button" class="btnDlgCekPeserta" id="btnResetBS" value="&nbsp; Pilihan Soal&nbsp; ">
	        <input type="text" id="indBS" style="text-align:center" maxlength="2" size="2" title="isi pilihan soal untuk peserta ini, lalu klik Pilihan Soal">
	        <br><br>

	        <input type="button" id="btnFinishIt" style="background-color:#f00; margin-top:3px" title="Gunakan dengan bijak (eksekusi Double Click) !!"
	         value="&nbsp; Finishkan !!&nbsp; "> &nbsp;
	        <input type="button" id="btnCallNewTimer" style="background-color:#FF7F00" value="&nbsp; Set Timer&nbsp; "> &nbsp;
	        <input type="button" id="btnPeringatan" style="background-color:#CC3" value="&nbsp; Pesan &nbsp;"> &nbsp;
	        <input type="button" id="btnStatusTO" style="background-color:#C3C;margin-bottom:5px" value="&nbsp; Status TO &nbsp;">
    	</div>
    </center>
	<br>
</div>

<!-- ## form dialogPindahJur ## -->
<div id="dialogPindahJur" title="Pindah Jurusan">
	<br>
        <center>
        yakin akan memindahkan jurusan peserta ini ?<br>
        (Semua progres & hasil bidang studi <span id="txtOldJur"></span> nya akan dihapus)

        <br><br><br>
    	<input type="button" id="btnChangeJur" value="&nbsp;Pindahkan&nbsp;" style="background-color: green" title="klik ganda untuk memindahkan jurusan">&nbsp;&nbsp;&nbsp;
	    <input type="button" id="btnCancelChangeJur" value="&nbsp;Batalkan&nbsp;" style="background-color: #ff3333">
        <br>
        </center>
	<br>
</div>

<!-- ## form dialogKirimPesan ## -->
<div id="dialogKirimPesan" title="Pesan ke Peserta">
	<br>
	<span style="font-family: arial; font-size: 13px; color: #808080;">Gunakan <strong>+vid:filevideo</strong> untuk menampilkan video dan <strong>+img:fileimage</strong> untuk menampilkan gambar. Format video : mp4, format gambar : <i>umum</i>.</span>
    <span style="font-family: arial; font-weight: bold; font-size: 12px; color: red;">Jangan menampilkan video pada saat ujian yang mengandung <i>listening section</i> !!</span><br><br>
    Pesan ke-<span id="xWarn"></span> untuk peserta ini<br><br>
    <center>
    	<input type="hidden" id="idPesertaWarn">
        <textarea id="isiPesan" cols="40" rows="7" style="text-align:center; margin-bottom:7px; padding:3px 6px 6px 6px;" placeholder="contoh +vid:Jingle NF 1st.mp4"></textarea><br>
        <br>
        <input type="button" class="kirimkanPesan" id="btnPesanKirim" style="background-color:#CC3" value="&nbsp; Kirim ke peserta ini&nbsp; ">
        <input type="button" class="kirimkanPesan" id="btnPesanKirimAll" style="background-color:#CC3" value="&nbsp; Kirim ke semua&nbsp; ">
        <!-- <input type="button" class="kirimkanPesan" id="btnHapusPesan" style="background-color:#FF0000" value="&nbsp; Hapus Pesan&nbsp; "> -->
        <br>
    </center>
	<br>
</div>

<!-- ## form dialogStatus ## -->
<div id="dialogStatusTO" title="Status TO">
	<br>
    	<b>TO 01</b> : <span id='ac_Soal1' title=''></span> | <span id='tmp_Answer1' title=''></span> | <a href='#' style="text-decoration: none; color:red; font-size: 14px; font-family: arial" id='haKe_1' class="soalhake" toNop='' title='klik 2 kali untuk mereset soal TO 1'></a> | <input type="button" id='ha_Ke1' style="background-color: red; height: 21px" class="linkhake" forNop='' title='klik 2 kali untuk men-set/reset TO 1 => 1 sudah TO , 0 belum TO'></button><br><br>
    	<b>TO 02</b> : <span id='ac_Soal2' title=''></span> | <span id='tmp_Answer2' title=''></span> | <a href='#' style="text-decoration: none; color:red; font-size: 14px; font-family: arial" id='haKe_2' class="soalhake" toNop='' title='klik 2 kali untuk mereset soal TO 2'></a> | <input type="button" id='ha_Ke2' style="background-color: red; height: 21px" class="linkhake" forNop='' title='klik 2 kali untuk men-set/reset TO 2 => 1 sudah TO , 0 belum TO'></button><br><br>
    	<b>TO 03</b> : <span id='ac_Soal3' title=''></span> | <span id='tmp_Answer3' title=''></span> | <a href='#' style="text-decoration: none; color:red; font-size: 14px; font-family: arial" id='haKe_3' class="soalhake" toNop='' title='klik 2 kali untuk mereset soal TO 3'></a> | <input type="button" id='ha_Ke3' style="background-color: red; height: 21px" class="linkhake" forNop='' title='klik 2 kali untuk men-set/reset TO 3 => 1 sudah TO , 0 belum TO'></button><br><br>
    	<b>TO 04</b> : <span id='ac_Soal4' title=''></span> | <span id='tmp_Answer4' title=''></span> | <a href='#' style="text-decoration: none; color:red; font-size: 14px; font-family: arial" id='haKe_4' class="soalhake" toNop='' title='klik 2 kali untuk mereset soal TO 4'></a> | <input type="button" id='ha_Ke4' style="background-color: red; height: 21px" class="linkhake" forNop='' title='klik 2 kali untuk men-set/reset TO 4 => 1 sudah TO , 0 belum TO'></button><br><br>
    	<b>TO 05</b> : <span id='ac_Soal5' title=''></span> | <span id='tmp_Answer5' title=''></span> | <a href='#' style="text-decoration: none; color:red; font-size: 14px; font-family: arial" id='haKe_5' class="soalhake" toNop='' title='klik 2 kali untuk mereset soal TO 5'></a> | <input type="button" id='ha_Ke5' style="background-color: red; height: 21px" class="linkhake" forNop='' title='klik 2 kali untuk men-set/reset TO 5 => 1 sudah TO , 0 belum TO'></button><br><br>
    	<b>TO 06</b> : <span id='ac_Soal6' title=''></span> | <span id='tmp_Answer6' title=''></span> | <a href='#' style="text-decoration: none; color:red; font-size: 14px; font-family: arial" id='haKe_6' class="soalhake" toNop='' title='klik 2 kali untuk mereset soal TO 6'></a> | <input type="button" id='ha_Ke6' style="background-color: red; height: 21px" class="linkhake" forNop='' title='klik 2 kali untuk men-set/reset TO 6 => 1 sudah TO , 0 belum TO'></button><br><br>
    	<b>TO 07</b> : <span id='ac_Soal7' title=''></span> | <span id='tmp_Answer7' title=''></span> | <a href='#' style="text-decoration: none; color:red; font-size: 14px; font-family: arial" id='haKe_7' class="soalhake" toNop='' title='klik 2 kali untuk mereset soal TO 7'></a> | <input type="button" id='ha_Ke7' style="background-color: red; height: 21px" class="linkhake" forNop='' title='klik 2 kali untuk men-set/reset TO 7 => 1 sudah TO , 0 belum TO'></button><br><br>
    	<b>TO 08</b> : <span id='ac_Soal8' title=''></span> | <span id='tmp_Answer8' title=''></span> | <a href='#' style="text-decoration: none; color:red; font-size: 14px; font-family: arial" id='haKe_8' class="soalhake" toNop='' title='klik 2 kali untuk mereset soal TO 8'></a> | <input type="button" id='ha_Ke8' style="background-color: red; height: 21px" class="linkhake" forNop='' title='klik 2 kali untuk men-set/reset TO 8 => 1 sudah TO , 0 belum TO'></button><br><br>
    	<b>TO 09</b> : <span id='ac_Soal9' title=''></span> | <span id='tmp_Answer9' title=''></span> | <a href='#' style="text-decoration: none; color:red; font-size: 14px; font-family: arial" id='haKe_9' class="soalhake" toNop='' title='klik 2 kali untuk mereset soal TO 9'></a> | <input type="button" id='ha_Ke9' style="background-color: red; height: 21px" class="linkhake" forNop='' title='klik 2 kali untuk men-set/reset TO 9 => 1 sudah TO , 0 belum TO'></button><br><br>
    	<b>TO 10</b> : <span id='ac_Soal10' title=''></span> | <span id='tmp_Answer10' title=''></span> | <a href='#' style="text-decoration: none; color:red; font-size: 14px; font-family: arial" id='haKe_10' class="soalhake" toNop='' title='klik 2 kali untuk mereset soal TO 10'></a> | <input type="button" id='ha_Ke10' style="background-color: red; height: 21px" class="linkhake" forNop='' title='klik 2 kali untuk men-set/reset TO 10 => 1 sudah TO , 0 belum TO'></button>
	<br><br>
</div>

<!-- ## form dialogInputItem ## -->
<div id="dialogInputItem" title="Cek Item Soal">
	<br>
    <div >
    	<form action="inputitemsoal.php" method="post" enctype="multipart/form-data" target="_blank">
            <select id="kodeFolder" name="kodeFolder">
                <option value="0" selected> Pilih Bid. Studi &nbsp;&nbsp; </option>
                <?php
                    $lihatKodeFolderSoal = mysqli_query($con,"SELECT DISTINCT pathKodeSoal, kodeSoal, petaSoal, bidStudiTabel, kunciJawaban, durasi FROM naskahsoal");
                    while ($getFolderSoal = mysqli_fetch_array($lihatKodeFolderSoal))
                    {
                        $pathKS = $getFolderSoal['pathKodeSoal'];
                        $noKode = $getFolderSoal['kodeSoal'];
                        $petaS = $getFolderSoal['petaSoal'];
                        $bidStudi = $getFolderSoal['bidStudiTabel'];
                        $kunJwb = $getFolderSoal['kunciJawaban'];
                        $durSoal = $getFolderSoal['durasi'];

                        $itemNomor = substr_count($kunJwb, ",");
                        $itemNomor++;
                        {
                        	echo ('<option value="'.$pathKS.'" pktSoal="'.$petaS.'" nItem="'.$itemNomor.'" kkey="'.$kunJwb.'" bsDur="'.$durSoal.'">'.$bidStudi.' ('.$noKode.') </option>');
                        }
                    }
                ?>
            </select> &nbsp;&nbsp;&nbsp;&nbsp;
            Paket Soal : <input type="text" id="soalPaket" size="3" style="text-align: center;"> &nbsp;&nbsp; <input type="button" id="chPaketSoal" value="&nbsp; set paket &nbsp;">
            <br><br>
            
            <div id="divkunciJwb">
            Kunci Jawaban : <textarea id="keyIsi" name="keyIsi" rows="2" cols="100" value="" style="padding-left:5px; color:blue; vertical-align:top"></textarea>
            </div>
            <br><br>
            
            Durasi : <input type="text" id="bsDurasi" style="text-align:right; width:50px; padding:0px 3px 2px 0px; border-style: line;"> detik &nbsp;&nbsp; <input type="button" id="setNewDur" value="&nbsp; set durasi &nbsp;">
            <br><br>
            <div>
            	<input type="button" class="navThumb" id="minusThumb" value="&nbsp; << &nbsp;">
            	<input id="noImg" style="text-align:center; width:90px; padding:2px 2px 2px 2px; border: transparent;">
            	<input type="button" class="navThumb" id="plusThumb" value="&nbsp; >> &nbsp;">
            </div>
            <br><br>
            <img id="imgPreview" src="">
            <br><br>
            <input type="file" name="uploadItem" value="" accept="image/png">
            <input type="submit" value="&nbsp;<< Ganti Soal&nbsp;" id="btnUploadImgSoal">
        </form>
    </div>
	<br>
</div>

<!-- ## form dialogResetTO ## -->
<div id="dialogResetTO" title="Reset TO">
	<br> 
	<form>
        <center>
        Yakin akan mereset TO ??<br>
        Semua status proses akan dikembalikan ke awal.<br>
        <b><span style="color:red">TIDAK BISA DI-<i>UNDO</i> !!</span></b>
        <br><br>
        ID Peserta yang akan direset :
        <input name="iddiReset" id="iddiReset" type="text" size="3" maxlength="4" style="text-align:center" title="isikan ID peserta yg akan direset, kosongkan utk mereset semua peserta"><br><br>
        <input type="button" id="btnResetTOYes" value="&nbsp;Ya... Reset TO !!&nbsp;">
        </center>
	</form>
	<br>
</div>

<!-- ## form dialogResetHasilTO ## -->
<div id="dialogResetHasilTO" title="Reset Hasil TO">
	<br> 
	<form>
        <center>
        Yakin akan mereset Hasil TO ??<br>
        Hasil TO yang sudah diperoleh akan dikosongkan.<br><b><span style="color:red">TIDAK BISA DI-<i>UNDO</i> !!</span></b>
        <br><br>
        ID Peserta yang akan direset :
        <input name="iddiHReset" id="iddiHReset" type="text" size="3" maxlength="4" style="text-align:center" title="isikan ID peserta yg akan dihapus hasil TOnya, kosongkan utk menghapus hasil TO semua peserta"><br><br>
        <input type="button" id="btnResetHasilTOYes" value="&nbsp;Ya... Reset Hasil TO !!&nbsp;">
        </center>
	</form>
	<br>
</div>

<!-- ## form dialogChangeKey ## -->
<div id="dialogChangeKey" title="Edit Kunci Jawaban">
	<br> 
	<form>
        <center>
        Ubah kunci jawaban ??<br><br>
        <input type="button" class="btnKeyChange" id="btnYesChangeKey" value="&nbsp; Ya &nbsp;">&nbsp;
        <input type="button" class="btnKeyChange" id="btnNoChangeKey" value="&nbsp; Batal &nbsp;">
        </center>
	</form>
	<br>
</div>

<!-- ## form dialogDeleteAllMedia ## -->
<div id="dialogDeleteAllMedia" title="Delete All Media" oncontextmenu="return false">
	<br> 
        <center>
        Anda akan menghapus semua file Media ? <br>
        <br><br>
        
        <input type="button" style="background-color:red;" id="yesDelAllMedia" value="&nbsp;Ya, hapus semua file media !!&nbsp;" title="klik ganda">
        </center>
	<br>
</div>

<!-- /\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/ -->

	<div id="header" style="font-family: arial narrow">
		<a href="../index.php" target="_blank" style="color: white; text-decoration: none;">CBT</a> - <?= $namaSekolahFront; ?> <span id="inHe"></span>
        <br>
		BKB NURUL FIKRI
	</div>

	<div id="content">
		<div id="sidebar">

				Login Siswa IPA : <b><span id="logIPA"></span></b>
				<div style="height: 3px;"></div>
				Login Siswa IPS : <b><span id="logIPS"></span></b>
				<div style="height: 3px;"></div>
                Login Siswa IPC : <b><span id="logIPC"></span></b>
                <div style="height: 3px;"></div>
				Login Total : <b><span id="logTot"></span></b>
				<br><br>
                <span id="allVP" style="display: none;"></span>
                <hr width="90%">
                <br>

                <b>Shift Aktif <span id="ShiftygAktif"> <?= $waktuShift.' (grup '.$shiftAktif; ?> </b> </span> 
				<div style="height: 4px;"></div>
				<select id="noShift" style="margin-top:3px; margin-bottom:4px; padding:2px; padding-bottom:3px; width:188px">
						<?php
							$lihatShift = mysqli_query($con,"SELECT no, hariKe, shift, jamMulai, mntMulai, bolehTelat FROM shifttes");
							while ($getShift = mysqli_fetch_array($lihatShift))
							{
								$noKini = $getShift['no'];
								$hariShift = $getShift['hariKe'];
								$grup = $getShift['shift'];
								$waktunyaShift = $getShift['jamMulai'].":".$getShift['mntMulai'];
								$telat = $getShift['bolehTelat'];

								if ($telat == 0)
								{ $fTelat = ''; }
								else if ($telat == 1)
								{ $fTelat = ' &#9749;'; }

								if ($noKini == $idShiftAktif)
								{ echo ('<option value="'.$noKini.'" selected>Shift ' .$hariShift.' - '.$waktunyaShift.' (grup '.$grup.')'.$fTelat.'</option>'); }
								else
								{ echo ('<option value="'.$noKini.'">Shift ' .$hariShift.' - '.$waktunyaShift.' (grup '.$grup.')'.$fTelat.'</option>'); }
							}
						?>
				</select>
                <span id="pengaktifShift" style="font-weight: bold; font-size: 16px; cursor: pointer; display: inline-block; transform: translateY(2px);" title="aktifkan shift">⏱</span>
                <div style="height: 4px;"></div>
                <input id="pengaddShift" type="button" value="&nbsp;add&nbsp;" title="tambah shift">
                <input id="pengeditShift" type="button" value="&nbsp;edit&nbsp;" title="sunting shift">
                <input id="pendeleteShift" type="button" value="&nbsp;delete&nbsp;" title="hapus shift">
				<br><br><br>
				
				<a href="#"><img id="aPilihanSoal" src="images/book-icon-01s.png" style="margin-bottom: 5px;" title="Pilihan Soal"></a>
					<div id="roundBadge" style="display:inline-block; border-radius:50%; width: 16px; height: 15px; transform: translate(-12px, -29px);
					  border: 1px solid white; color:white; text-align:center; font-size: 11px; padding-top:1px">x</div>
				<a href="#"><img id="aHasilBS" src="images/table-icon.png" style="margin-bottom: 5px;" title="Tabel Hasil"></a></br>

				<div id="listPilihanSoal" style="display: none">
					<?php 
						//lihat TO aktif
						$pilihTO = "SELECT id, statusAktif, pathKodeSoal, kodeSoal, petaSoal, indexBidStudi, kelompok, bidStudiTabel, singkatanBS, kunciJawaban, durasi FROM naskahsoal ORDER BY id ASC"; 
						$ambilTO = mysqli_query($con,$pilihTO);

						while ($dapatTO = mysqli_fetch_array($ambilTO)) {
							$c++;
							$sPS[$c] = $dapatTO['statusAktif'];
							$iD = $dapatTO['id'];
							if ($iD<10) { $iD = '0'.$iD; }
							$sA = $dapatTO['statusAktif'];
							if ($sA == 1) { $beCheck = 'checked'; } else { $beCheck = ''; }
							$pKS = $dapatTO['pathKodeSoal'];
							$kS = $dapatTO['kodeSoal'];
							$pS = $dapatTO['petaSoal'];
							$iBS = $dapatTO['indexBidStudi'];
							$kL = $dapatTO['kelompok'];
							$bST = $dapatTO['bidStudiTabel'];
							$sBS = $dapatTO['singkatanBS'];
							$kJ = $dapatTO['kunciJawaban'];
							$dS = $dapatTO['durasi'];
							$bIN = substr_count($kJ, ",");
							$bIN++;

							//hitung dulu ada berapa banyak indexBidStudi dalam satu kelompok jurusan yg sama, karena 
							//"bisa" berpotensi error
							$cekIndexPerJur = "SELECT indexBidStudi, kelompok FROM naskahsoal WHERE indexBidStudi='$iBS' AND (kelompok='$kL' OR kelompok='IPAIPS')"; 
							$getIndexPerJur = mysqli_query($con, $cekIndexPerJur);
							$nIndexPerJur = mysqli_num_rows($getIndexPerJur);
							if ($nIndexPerJur>1)
							{ $colIndex =  "red"; }
							else
							{ $colIndex =  "black"; }
							
							echo '<label for="cek'.$iD.'">'.$iD.'</label>. <input class="cBS" id="cek'.$iD.'" tagID="'.$iD.'" type="checkbox" '.$beCheck.'>
									 <a title="klik untuk mengecek item soalnya" href="#" class="namedir" tagpS="'.$pS.'" tagbin="'.$bIN.'" tagkey="'.$kJ.'" tagdur="'.$dS.'" tagdn="'.$pKS.'">
									 	[<span id="pPaket'.$pKS.'" title="Paket : '.$pS.' , Index : '.$iBS.'" style="color:'.$colIndex.'">'.$pS.':<b>'.$iBS.'</b></span>] '.$kS.' '.$sBS.' - '.$kL.'&nbsp; </a></br>';
						}
					?>
					<br>
				</div>
				
				<div id="listHasilBS" style="display: none; font-size: 12px;">
					<?php 
						//lihat TO & sinkronisasi tabel hasil
						$pilihDataNaskah = "SELECT kelompok, bidStudiTabel FROM naskahsoal"; 
						$ambilDNaskah = mysqli_query($con,$pilihDataNaskah);

						while ($dapatDNaskah = mysqli_fetch_array($ambilDNaskah)) {
							$dKL = $dapatDNaskah['kelompok'];
							$dBST = $dapatDNaskah['bidStudiTabel'];
														
							echo '<span>* <b>'.$dKL.'</b></span></br>';

							$perBST = explode("+",$dBST);
							foreach($perBST as $cBST) {
								if ($dKL=='IPAIPS')
								{ $pilihDataTabel = "SELECT kolomHasil, kelompok, bidStudiTabel, namaBidStudi FROM tabelhasil WHERE bidStudiTabel='$cBST'"; }
								else if ($dKL=='IPA')
								{ $pilihDataTabel = "SELECT kolomHasil, kelompok, bidStudiTabel, namaBidStudi FROM tabelhasil WHERE bidStudiTabel='$cBST' AND kelompok='IPA'"; }
								else if ($dKL=='IPS')
								{ $pilihDataTabel = "SELECT kolomHasil, kelompok, bidStudiTabel, namaBidStudi FROM tabelhasil WHERE bidStudiTabel='$cBST' AND kelompok='IPS'"; }
								$ambilDTabel = mysqli_query($con,$pilihDataTabel);
								$adaYa = mysqli_num_rows($ambilDTabel);

								if ($adaYa>0) {
									while ($dapatTblHasil = mysqli_fetch_array($ambilDTabel))
									{ echo '&nbsp;&nbsp; <span title="'.$dapatTblHasil['namaBidStudi'].'">'.$dapatTblHasil['bidStudiTabel'].'</span> : <span style="color:green;">'.$dapatTblHasil['kolomHasil'].' '.$dapatTblHasil['kelompok'].'</span><br>'; }
								}
								else
								{ echo '&emsp;'.$cBST.' : <span style="color:red;font-weight:bold">X</span><br>'; }
							}
							echo '<div style="height:4px"></div>';
						}
					?>
				</div>

				<br><br>
				<hr width="90%"> <br>
                <input type="button" value="&nbsp;Download Hasil CBT&nbsp;" id="btnDLHasil"> <br> <br>

                <input type="button" value="&nbsp;Recovery&nbsp;" id="btnBackupCek" style="display: none; background-color: orange;" oncontextmenu="return false" title="klik kiri menampilkan dialog recovery, klik kanan untuk langsung melakukan backup">
                <span id="lastBackup" style="font-size: 12px"></span>

                <br><br>
                <span style="font-size:10px; font-weight:normal; color:white; position:absolute; top:10px; right:10px" title="Copyright BKB Nurul Fikri">Copyright &copy; <?= $thn_; ?> BKB NURUL FIKRI</span>
                <span style="font-size:10px; font-weight:normal; color:white; position:absolute; top:24px; right:10px">CBTNF Ver. <?= $CBTversion; ?></span>
                <span id="tglUploadPeserta" style="font-size:10px; font-weight:normal; color:white; position:absolute; top:38px; right:10px">upload peserta : <?= $uPeserta; ?></span>

		</div>


		<div id="main-content" style="overflow:hidden;">

			<audio id="errSound">
				<source src="LogErrAlert.ogg" type="audio/ogg">
				Your browser does not support the audio element.
			</audio>

            <div id="menuTab">
              <ul>
                <li><a href="#tab-1" tagAAct="1" class="notTabH"><img src="images/activity.png" width="9" height="11">&nbsp;<b>Aktivitas CBT</b></a></li>
                <li><a href="#tab-2" id="tabH"><img src="images/hasil.png" width="9" height="11">&nbsp;<b>Hasil</b></a></li>
                <li><a href="#tab-3" tagAAct="0" class="notTabH"><img src="images/datasystem.png" width="11" height="11">&nbsp;<b>Data Sumber</b></a></li>
                <li><a href="#tab-4" tagAAct="0" class="notTabH"><img src="images/system.png" width="11" height="11">&nbsp;<b>Sistem</b></a></li>
                
                <div id="divSee" style="float:right;margin-top:7px; margin-right:12px; font-family:arial; font-weight:normal">
                	Tampilan Peserta :
                	<select id="modeTampilan" style="padding:2px; padding-bottom:3px">
						<option value="allgrup">semua</option>
                        <option value="acgrup">grup aktif</option>
                        <option value="login">yang login</option>
					</select>
                </div>
              </ul>
              
              <div id="tab-1">
              	<div style="height:calc(100vh - 195px); overflow:auto;">
	              	<img id="lampErrorSign" style="float:left" src="../images/GreenLogErrorSign.png" align="top" height="25">
	              	&nbsp;
	              	<input type="button" id="btnAddPeserta" value="&nbsp; ✚ Peserta&nbsp; " style="background-color:orange">
	              	<input type="button" id="btnCallLogKeyAll" value="&nbsp; LogKey All&nbsp; " title="LogKey semua peserta">
	              	<input type="button" class="btnDlgCekPeserta" id="btnReSetLogKeyAll" value="&nbsp; UnLogKey All&nbsp; " title="UnLogKey semua peserta">
	              	<input type="button" id="btnCallFinishAll" value="&nbsp; Finish All&nbsp; " title="finishkan semua peserta" style="background-color:#f00;">
	              	
	              	<input type="button" id="letsSearch" value=" Cari " style="float:right">

	              	<br>
	              		&nbsp;
	              		<span style='font-size: 13px; font-family: arial'>&nbsp;urutkan : </span>
	              		<a style='font-size:13px;font-family:arial;' href="#" class='sortedBy' valTag='nomorUrut'>no urut</a> &bull; 
	              		<a style='font-size:13px;font-family:arial;' href="#" class='sortedBy' valTag='namaUrut'>nama</a>
	              	<br>
						<div id="divAktivitas" style="height:calc(100vh - 257px); overflow:auto; margin-top:7px; border: 1px solid white; border-left-color: #c7cace; border-top-color: #c7cace; background-color: #e7f0f7; border-radius: 2px; padding: 5px">
	                       <table id="hasilPantauan" style="width:100%; font-family:Arial, Helvetica, sans-serif"></table>
	                    </div>
	            </div>
                <div style="transform: translateY(8px);">
                    <center>
                        <span class="btnvPage" id="vPage1">&#9198;</span>&nbsp;
                        <span class="btnvPage" id="vPagePrev">&#9194;</span>
                        &ensp;
                        <span id="pgNumber" style="font-size: 15px;"></span>
                        &ensp;
                        <span class="btnvPage" id="vPageNext">&#9193;</span>&nbsp;
                        <span class="btnvPage" id="vPageEnd">&#9197;</span>

                    </center>
                </div>
              </div>

              <div id="tab-2">
               	    <div style="height:calc(100vh - 173px); overflow: auto;">

	               	    <div id="tabel_hasil" style="font:arial"> </div>

	                    <script type="text/javascript" src="tabulator5.4/js/tabulator.js"></script>
						<script type="text/javascript">
							var valFormatter = function(cell, formatterParams) {
                                field = cell.getField();
                                value = cell.getValue();
                                rowN = cell.getTable().getRowPosition(cell.getRow());

                                if (rowN%2==1) { clrBack = "#b5ffe4"; } else { clrBack = "aquamarine"; }
                                jwb = "";
                                
                                switch(field) {
                                    case "bs1":
                                        jwb = cell.getData().bs1_j;
                                        break;
                                    case "bs2":
                                        jwb = cell.getData().bs2_j;
                                        break;
                                    case "bs3":
                                        jwb = cell.getData().bs3_j;
                                        break;
                                    case "bs4":
                                        jwb = cell.getData().bs4_j;
                                        break;
                                    case "bs5":
                                        jwb = cell.getData().bs5_j;
                                        break;
                                    case "bs6":
                                        jwb = cell.getData().bs6_j;
                                        break;
                                    case "bs7":
                                        jwb = cell.getData().bs7_j;
                                        break;
                                    case "bs8":
                                        jwb = cell.getData().bs8_j;
                                        break;
                                    case "bs9":
                                        jwb = cell.getData().bs9_j;
                                        break;
                                    case "bs10":
                                        jwb = cell.getData().bs10_j;
                                }

                                if (jwb!="" && jwb!=null) {
							     	cell.getElement().style.backgroundColor = clrBack;
							     	cell.getElement().style.color = "mediumblue";
							    }
							    return value;
							};

							var tableRes = new Tabulator("#tabel_hasil",
								{
								 	// set height of table (in CSS or here), this enables the Virtual DOM and improves render speed dramatically (can be any valid css height value)
								 	//data:tabledata, //assign data to table
								 	height:"calc(100vh - 190px)",
								 	layout:"fitColumns", //fit columns to width of table (optional)
								 	tooltipsHeader:true,
								 	sortBy:"nama",
								 	sortDir:"asc",
								 	ajaxURL:"ajaxLoadTabHasil.php",
                                    columns:[ 
											 	{title:"No.", width:55, align:"center", formatter:"rownum", frozen:true},
												{title:"Nama", tooltip:true, field:"nama", width:130, headerFilter:true, frozen:true},
                                                {title:"Kelas", tooltip:true, field:"kelas", width:70, align:"center", headerFilter:true, frozen:true},
												{title:"Jurusan", field:"jur", width:40, align:"center", headerFilter:"list", headerFilterParams:{valuesLookup:true, clearable:true}},
												{field:"bs1_j", visible:false},
                                                {title:"BS1", field:"bs1", align:"center", sorter:"number", formatter:valFormatter},
												{field:"bs2_j", visible:false},
                                                {title:"BS2", field:"bs2", align:"center", sorter:"number", formatter:valFormatter},
												{field:"bs3_j", visible:false},
                                                {title:"BS3", field:"bs3", align:"center", sorter:"number", formatter:valFormatter},
												{field:"bs4_j", visible:false},
                                                {title:"BS4", field:"bs4", align:"center", sorter:"number", formatter:valFormatter},
												{field:"bs5_j", visible:false},
                                                {title:"BS5", field:"bs5", align:"center", sorter:"number", formatter:valFormatter},
												{field:"bs6_j", visible:false},
                                                {title:"BS6", field:"bs6", align:"center", sorter:"number", formatter:valFormatter},
												{field:"bs7_j", visible:false},
                                                {title:"BS7", field:"bs7", align:"center", sorter:"number", formatter:valFormatter},
												{field:"bs8_j", visible:false},
                                                {title:"BS8", field:"bs8", align:"center", sorter:"number", formatter:valFormatter},
												{field:"bs9_j", visible:false},
                                                {title:"BS9", field:"bs9", align:"center", sorter:"number", formatter:valFormatter},
												{field:"bs10_j", visible:false},
                                                {title:"BS10", field:"bs10", align:"center", sorter:"number", formatter:valFormatter},
												{title:"Total", field:"totalsc", width:70, align:"center", sorter:"number", formatter:valFormatter},
												{title:"noPes", field:"id", visible:false}
										 	]
								});

                                //create Tabulator object
                                tableRes.on("cellDblClick", function(e, cell) {
                                    nmKol = cell.getField();
                                    dtVal = cell.getValue();
                                    
                                    if (nmKol=='nama') {
                                        $("#letsSearch").trigger('click');
                                        $("#searchFor")
                                        .val(dtVal)
                                        .trigger('keyup');
                                    }
                                });
						</script>
                    </div>
              </div>
              
              <div id="tab-3">
              		<div style="height:calc(100vh - 173px); overflow:auto">
              			<br>
                        
                        <div style="float:right; margin-right:30px; padding:3px 3px 3px 3px;">
                            <b>Logo Sekolah (.png) </b> <br> ukuran maks. ideal 210 x 210 <b>:</b><br>
                            <img id="imgLogo" src="../images/logo_sekolah.png?<?= rand(); ?>" alt="Logo Sekolah"
                             style="box-shadow:2px 2px 3px grey; background:url(images/trans_bg.png); padding:3px 3px 3px 3px; margin-top:3px; border: 2px solid white; border-radius:6px;">
                             </br></br>
                             <div id="uploadDiv" style="margin-top:5px;"></div>
                             <input type="button" id="btnStartUpload" value="&nbsp;Upload file logo&nbsp;">
                        </div>
                        
                        <b>Judul TO</b>
						<div style="height: 4px;"></div>
                        <input id="titleTO" name="titleTO" type="text" value="<?= $mainTitle; ?>" 
                        style="width:425px; padding:3px 3px 3px 3px; background-color:#E0E0E0; border-radius:3px;">
                        <div style="height: 4px;"></div>
                        <input style="margin-bottom:8px; margin-top: 2px" id="btnJudulSimpan" name="judulSimpan" type="button" value="&nbsp;Simpan Judul&nbsp;">
                        
						<div style="height: 30px;"></div>
                        
                        <b>Nama Sekolah</b>
						<div style="height: 4px;"></div>
                        <input id="schoolName" name="schoolName" type="text" value="<?= $namaSekolahFront; ?>" 
                        style="width:425px; padding:3px 3px 3px 3px; background-color:#E0E0E0; border-radius:3px;">
                        <div style="height: 4px;"></div>
                        <input style="margin-bottom:8px; margin-top: 2px" id="btnNamaSekolahSimpan" type="button" value="&nbsp;Simpan Nama Sekolah&nbsp;">
                        
						<div style="height: 30px;"></div>
                        
                        <b>Teks Pelaksanaan</b>
						<div style="height: 4px;"></div>
                        <input id="teksLaksana" name="teksLaksana" type="text" value="<?= $pelaksanaan; ?>" 
                        style="width:425px; padding:3px 3px 3px 3px; background-color:#E0E0E0; border-radius:3px;">
                        <div style="height: 4px;"></div>
                        <input style="margin-bottom:8px; margin-top: 2px" id="btnPelaksanaanSimpan" type="button" value="&nbsp;Simpan Teks&nbsp;">
                        
						<div style="height: 30px;"></div>
                        
                        <b>Motto Sekolah</b>
						<div style="height: 4px;"></div>
                        <textarea id="schoolMotto" name="schoolMotto" cols="47" rows="3"
                         style="width:425px; padding:3px 3px 3px 3px; background-color::#E0E0E0; border-radius:3px;"><?= $motoSekolahFront; ?></textarea>
                         <div style="height: 4px;"></div>
                        <input style="margin-bottom:8px; margin-top: 2px" id="btnMottoSekolahSimpan" type="button" value="&nbsp;Simpan Motto Sekolah&nbsp;" style="transform: translate(0px, -5px);">
                        
						<div style="height: 60px;"></div>

                        <a href="templateExcel/[SAMPEL] DB Peserta.xlsx" target="_blank" style="font-weight: bold; text-decoration: none; cursor: pointer">DB Peserta (.xlsx)</a>
                        <hr style="border-top: 1px dotted black; border-bottom: 0px; width: 43%; margin: 2px 0 4px 0">
                        <span style="font-family:Arial; font-size:15px;">Jml. Peserta : <span id="txtTotSiswa"><?= $totSiswa; ?></span> </span>
                        <form enctype="multipart/form-data" action="excelpesertatomysql.php" method="post" target="_blank" style="margin-top: 4px;">
                            <input type="file" value="" id="fileDataPesertaXLSX" name="fileDataPesertaXLSX" accept=".xlsx" required>
                            <input type="submit" value="&nbsp;<< Isi DB Peserta&nbsp;" id="btnImportPeserta2DB" style="margin-bottom: 5px">
                            </br>
                        </form>
                        
						<div style="height: 30px;"></div>

                        <div id="divUpDBSoal">
                        <b>DB Set Soal (.xlsx)</b>
                        <hr style="border-top: 1px dotted black; border-bottom: 0px; width: 43%; margin: 2px 0 4px 0">
                        <span style="font-family:Arial; font-size:15px;">Set Soal : <span id="txtTotSet"><?= $totalSet; ?></span> </span>
                        <form enctype="multipart/form-data" action="excelsoaltomysql.php" method="post" target="_blank" style="margin-top: 4px;">
                            <input type="file" value="" id="fileDataSetSoalXLSX" name="fileDataSetSoalXLSX" accept=".xlsx" required>
                            <input type="submit" value="&nbsp;<< Isi DB Set Soal&nbsp;" id="btnImportSoal2DB" style="margin-bottom: 5px">
                            </br>
                        </form>
                        </div>
                        
						<div style="height: 30px;"></div>
                        
                        <div id="divUpPktSoal">
                        <b>Paket Soal (.zip)</b><br>
                        <span style="font-family: arial; font-size: 11px; color: #808080;">upload setelah mengisi DB Set Soal !</span>
                        <hr style="border-top: 1px dotted black; border-bottom: 0px; width: 43%; margin: 2px 0 4px 0">
                        <span style="font-family:Arial; font-size:15px;">Paket Soal :  <span id="txtTotPaket"><?= $totPaket; ?></span> </span>
                        <br>
                        	<div id="uploadDivPaket" style="margin-top:5px;"></div>
                            <input type="button" id="btnStartUploadPaket" value="&nbsp;Upload Paket Soal&nbsp;" style="transform: translateY(-12px); margin-bottom: 3px;"> &nbsp;
                            </br>
                        </div>

						<div style="height: 30px;"></div>

                        <b>Database Jurusan (.xlsx)</b>
                        <hr style="border-top: 1px dotted black; border-bottom: 0px; width: 43%; margin: 2px 0 4px 0">
                        <span style="font-family:Arial; font-size:15px;">Jml. Kode Jurusan :  <?= $totKodeJur; ?> </span>
                        <form enctype="multipart/form-data" action="excelproditomysql.php" method="post" target="_blank" style="margin-top: 4px;">
                            <input type="file" value="" id="fileDataKodeJurXLSX" name="fileDataKodeJurXLSX" accept=".xlsx" required>
                            <input type="submit" value="&nbsp;<< Upload DB Jurusan&nbsp;" id="btnUploadDBKodeJur" style="margin-bottom: 5px">
                            </br>                            
                        </form>
                        <br><br>

                        <span id="btnResetHasilTO" style="cursor:pointer; background-color:#EC5244; font-family:Tahoma; font-size:9px;
												   color:white; border-radius:4px; padding: 2px 3px 2px 3px; float:right; margin-right:35px; bottom:5px;"
		                					 	   title="Klik ganda untuk mengosongkan database hasil TO. Hati-hati, resiko tanggung sendiri !!">R-Hasil TO</span>
		                <span id="btnResetTO" style="cursor:pointer; background-color:#EC5244; font-family:Tahoma; font-size:9px;
											  color:white; border-radius:4px; padding: 2px 3px 2px 3px; float:right; margin-right:10px; bottom:5px;"
		                					  title="Klik ganda untuk mereset status semua TO. Hati-hati, resiko tanggung sendiri  !!">R-TO</span>
		                <br><br>
                        <hr width="98%"><br><br>
                        <b>Media</b>
						<div style="height: 5px;"></div>
                        <textarea id="mediaList" name="mediaList" cols="47" rows="2"
                         style="width:350px; padding:3px 3px 3px 3px; background-color:#E0E0E0; border-radius:3px;"> </textarea>
                         </br>
                        	<div id="uploadMedia" style="margin-top:5px;"></div>
                            <input type="button" id="btnUploadMedia" value="&nbsp;Upload Media&nbsp;" style="margin-bottom: 3px">&nbsp;&nbsp;&nbsp;
                            <input type="button" id="btnClearAllMedia" value="&nbsp;Delete All Media&nbsp;" style="background-color: #ff3333" title="klik ganda untuk menghapus semua media">
                        </br><br>


                    </div>
              </div>
              
              <div id="tab-4">
               	<div style="height:calc(100vh - 173px); overflow:auto">
                   <br>
                   
				   <table>
					   <tr>
						   <td><b>Nama Admin</b> &nbsp; </td>
						   <td><input type="text" id="adminName" size="30" style="padding-left:3px" value="<?= $adminName; ?>"></td>
					   </tr>
					   <tr>
						   <td><b>No. WA Admin</b> &nbsp; </td>
						   <td><input type="text" id="adminNumber" size="30" style="padding-left:3px" placeholder="hanya angka: 081234567890" value="<?= $adminNo; ?>" title="hanya angka: 081234567890"></td>
					   </tr>
					   <tr>
						   <td><b>Link Grup WA</b> &nbsp; </td>
						   <td><input type="text" id="grupWA" size="70" style="padding-left:3px" value="<?= $wagrup; ?>"></td>
					   </tr>
					   <tr style="height: 33px;">
						   <td></td>
						   <td><input type="button" id="btnSaveName" value="&nbsp; Simpan Admin & Link WA &nbsp;"></td>
					   </tr>
				   </table>
                   
                   
                   <div style="height: 30px;"></div>
				   
				   <table>
				   		<tr style="height: 30px">
                    		<td><input name="showSearch" id="showSearch" type="checkbox" title="centang untuk menampilkan search nama pada halaman login peserta" <?php if ($showSearch == 1) { echo 'checked'; } ?>>&nbsp; <label for="showSearch"><b>Search Nama</b></label> </td>
                    		<td></td>
                    		<td></td>
                    		<td></td>
                    	</tr>
						<tr>
							<td><b>Label Login 1</b> &nbsp; </td>
							<td><input type="text" id="no1" size="20" maxlength="20" style="padding-left:3px" value="<?= $teksno1; ?>"></td>
							<td><input type="text" id="nlab1" size="3" maxlength="3" style="text-align:center" value="<?= $nlbl1; ?>"> digit</td>
							<td></td>
						</tr>
						<tr>
							<td><b>Label Login 2</b> &nbsp; </td>
							<td><input type="text" id="no2" size="20" maxlength="20" style="padding-left:3px" value="<?= $teksno2; ?>"></td>
							<td><input type="text" id="nlab2" size="3" maxlength="3" style="text-align:center" value="<?= $nlbl2; ?>"> digit</td>
							<td style="padding-top: 4px;"> &nbsp; &nbsp; <input name="input2pw" id="input2pw" type="checkbox" <?php if ($inp2pw == '1') { echo 'checked'; } ?>> <label for="input2pw">Tipe Password</label></td>
						</tr>
						<tr style="height: 33px;">
							<td></td>
							<td><input type="button" id="btnSaveLabel" value="&nbsp; Simpan Label &nbsp;"></td>
							<td></td>
							<td></td>
						</tr>
					</table>
                   
					<div style="height: 25px;"></div>

				   <table>
					   <tr>
						   <td><b>Prefix <span id="txtlbl1"><?= $teksno1; ?></span> &nbsp; </b></td>
						   <td><input type="text" id="prefixID1" maxlength="20" size="11" style="text-align:center;" placeholder="12345678xx" value="<?= $prefixid1; ?>"></td>
					   </tr>
					   <tr>
						   <td><b>Prefix <span id="txtlbl2"><?= $teksno2; ?></span> &nbsp; </b></td>
						   <td><input type="text" id="prefixID2" maxlength="20" size="11" style="text-align:center;" placeholder="12345678xx" value="<?= $prefixid2; ?>"></td>
					   </tr>
					   <tr style="height: 33px;">
						   <td></td>
						   <td><input type="button" id="btnSavePrefix" value="&nbsp; Simpan Prefix &nbsp;"></td>
					   </tr>
				   </table>
                   
                   <div style="height: 25px;"></div>

				   <b>Opsi Item Login</b> &nbsp;&nbsp;&nbsp;&nbsp;

                   <input name="inputHP" id="inputHP" type="checkbox" <?php if ($inpHP == 1) { echo 'checked'; } ?> >&nbsp;
                   		<label for="inputHP" title="menampilkan isian no hp pada halaman login">No. HP</label>
                   &nbsp;&nbsp;&nbsp;

                   <input name="inputEmail" id="inputEmail" type="checkbox" <?php if ($inpEmail == 1) { echo 'checked'; } ?> >&nbsp;
                   		<label for="inputEmail" title="menampilkan isian alamat email pada halaman login">Email</label>
                   &nbsp;&nbsp;&nbsp;
                   
                   <input name="inputDoB" id="inputDoB" type="checkbox" <?php if ($inpDoB == 1) { echo 'checked'; } ?> >&nbsp;
                   		<label for="inputDoB" title="menampilkan isian tgl lahir pada halaman login">Tgl. Lahir</label>
                   &nbsp;&nbsp;&nbsp;
                   
                   <input name="pilJurOpt" id="pilJurOpt" type="checkbox" <?php if ($pilJurTampil == 1) { echo 'checked'; } ?> >&nbsp;
                   		<label for="pilJurOpt" title="menampilkan isian kode jurusan pada halaman login">Kode Jurusan</label>
                   <br><br><br><br>
                   
                   <b>Jumlah Pilihan Jwb</b> &nbsp;&nbsp;&nbsp;&nbsp;
                   <input id="opsi2" name="nOpsi" type="radio" value="2" <?php if ($opsiN == 2) { echo 'checked'; } ?>> <label for="opsi2">2</label> &nbsp;&nbsp;&nbsp;
                   <input id="opsi3" name="nOpsi" type="radio" value="3" <?php if ($opsiN == 3) { echo 'checked'; } ?>> <label for="opsi3">3</label> &nbsp;&nbsp;&nbsp;
                   <input id="opsi4" name="nOpsi" type="radio" value="4" <?php if ($opsiN == 4) { echo 'checked'; } ?>> <label for="opsi4">4</label> &nbsp;&nbsp;&nbsp;
                   <input id="opsi5" name="nOpsi" type="radio" value="5" <?php if ($opsiN == 5) { echo 'checked'; } ?>> <label for="opsi5">5</label>
                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;::&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                   
                   <input name="erasableOpt" id="erasableOpt" type="checkbox" <?php if ($erasableOpsi == 1) { echo 'checked'; } ?> >&nbsp; 
                   <label for="erasableOpt"><b>Jawaban bisa dihapus</b></label>
                   
                   <br>

	                   <table>
	                    	<tr style="height: 30px">
	                    		<td><input name="showScore" id="showScore" type="checkbox" title="centang untuk menampilkan skor akhir pada halaman finish peserta" <?php if ($showScore == 1) { echo 'checked'; } ?>>&nbsp; <label for="showScore"><b>Scoring</b></label> </td>
	                    		<td></td>
	                    		<td></td>
	                    	</tr>
	                    	<tr>
	                    		<td>Benar</td>
	                    		<td></td>
	                    		<td> <input type="text" size="2" maxlength="2" id="sBenar" style="text-align:center" value="<?= $scB; ?>"> </td>
	                    	</tr>
	                    	<tr>
	                    		<td>Salah</td>
	                    		<td></td>
	                    		<td> <input type="text" size="2" maxlength="2" id="sSalah" style="text-align:center" value="<?= $scS; ?>"> </td>
	                    	</tr>
	                    	<tr>
	                    		<td>Kosong</td>
	                    		<td></td>
	                    		<td> <input type="text" size="2" maxlength="2" id="sKosong" style="text-align:center" value="<?= $scK; ?>"> </td>
	                    	</tr>
	                   		<tr>
	                   			<td>Skala</td>
	                   			<td></td>
	                   			<td> <input type="text" size="3" maxlength="3" id="sSkala" style="text-align:center" value="<?= $sekala; ?>"> </td>
	                   		</tr>
	                   		<tr style="height: 33px;">
	                   			<td> </td>
	                    		<td> </td>
	                    		<td> <input type="button" id="btnSaveScore" value="&nbsp; Simpan Scoring &nbsp;" style="margin-top:2px"> </td>
							</tr>
	                   </table>

					   <div style="height: 20px;"></div>

					   <table>
					   		<tr>
	                   			<td><b>Nilai Tabel Hasil</b></td>
	                   			<td> &nbsp; &nbsp; </td>
	                   			<td style="padding-top: 2px;">
	                   				<input name="tHasil" id="tHM" type="radio" value="mentah" <?php if ($hasilditabel == "mentah") { echo 'checked'; } ?> > <label for="tHM">mentah</label> &nbsp;&nbsp;&nbsp;
                   					<input name="tHasil" id="tHS" type="radio" value="score" <?php if ($hasilditabel == "score") { echo 'checked'; } ?> > <label for="tHS">score</label>
	                   			</td>
	                   		</tr>
					   </table>
	                   
                </div>
              </div>

            </div>
		</div>
	</div>


<script type="text/javascript">

    let totalSoal = '<?= $totSet; ?>';
    let totalSoalAktif = '<?= $totAktif; ?>';

    if (totalSoal==0)
    { totalSoalAktif='✕'; }

    $("#roundBadge").html(totalSoalAktif);

    if(totalSoalAktif==0 || totalSoalAktif=='✕')
    { $("#roundBadge").css("background-color","crimson"); }
    else
    { $("#roundBadge").css("background-color","lightseagreen"); }

    let modeShow = '<?= $showMode; ?>';
    $("#modeTampilan").val(modeShow);

    $(".sortedBy").css({"text-decoration": "none"});
    let modeSort = '<?= $modeSort; ?>';

    if (modeSort=='nomorUrut')
    { $('[valTag="nomorUrut"]').css("text-decoration", "underline"); }
    else if (modeSort=='namaUrut')
    { $('[valTag="namaUrut"]').css({"text-decoration": "underline"}); }


    let seeActivity = true;
    let progresss;
    let nomorBS;
    let nomorShift = <?= $idShiftAktif; ?>;
    let hasilSeen = 0;
    let dMX = 0;        //banyaknya pesan peringatan ke peserta
    let nThumb = 0;
    let nowThumb = 0;
    let dntag = '';

    let cTotSiswa = 0;
    let cTotSet = 0;
    let cTotPaket = 0;

    let totDaftarSiswa = <?= $totalSiswa; ?>;
    let vwbleTestee = 0;
    let viewPageNow = 1;
    let allviewPage = 0;


	function tempAlert(msg,duration,kiri,atas) {
			el = document.createElement("div");
			lf = kiri; tp = atas; 
			el.setAttribute("style", "position:absolute; left:"+lf+"px; top:"+tp+"px; background-color:white; padding:2px; border: 1px #567fc5 solid; font-size: 14px; text-align:center;");
			el.innerHTML = msg;
			setTimeout(function(){
			el.parentNode.removeChild(el);
		}, duration);
		document.body.appendChild(el);
	}

    function cekaktivitas() {
        if (seeActivity) {
            $.ajax({
                type: "post",
                url: "../cekAktifitas.php",
                data: {tampilanPeserta: modeShow, sortMode: modeSort, vT: vwbleTestee, vPN: viewPageNow},
                cache: false,
                success: function(dipantau) {
                    $("#hasilPantauan").html(dipantau);
                    if ($("#hasilPantauan").html().indexOf('FF0000') > -1) {
                        $("#errSound").trigger('play');
                        $("#lampErrorSign").attr("src", "../images/RedLogErrorSign.png");
                    }
                    else
                    { $("#lampErrorSign").attr("src", "../images/GreenLogErrorSign.png"); }
                }
            });
        }
    }

    function updatetotlogin() {
        $.ajax({
            type: "post",
            url: "../activate.php",
            data: {st:"pantauSiswa"},
            dataType: "json",
            cache: false,
            success: function(output) {
                var totalIPALog = output[0];
                var totalIPSLog = output[1];
                var totalIPCLog = output[2];
                var totalLog = totalIPALog+totalIPSLog+totalIPCLog;
                
                $("#logIPA").text(totalIPALog);
                $("#logIPS").text(totalIPSLog);
                $("#logIPC").text(totalIPCLog);
                $("#logTot").text(totalLog);
            }
        });
    }

    function showLog() {
        if (modeShow=="login") {
            countallViewPage();
            allviewPage = parseInt($("#allVP").text());
            if (viewPageNow>allviewPage) { viewPageNow=allviewPage; }
            changePgNumber();
        }
        cekaktivitas();
        updatetotlogin();

        if (cTotSiswa==0 || cTotSet==0 || cTotPaket==0) {
            $.ajax({
                type: "post",
                url: "ajaxCheckTot.php",
                dataType: "json",
                cache: false,
                success: function(cTot)
                {
                    var cTotSiswa = cTot[0];
                    var cTotSet = cTot[1];
                    var cTotPaket = cTot[2];

                    if (cTotSiswa>0)
                    { $("#txtTotSiswa").html("<span style='color:white; background-color:#34599E; font-weight:bold'>&nbsp;"+cTotSiswa+"&nbsp;</span>"); }
                    if (cTotSet>0)
                    { $("#txtTotSet").html("<span style='color:white; background-color:#34599E; font-weight:bold'>&nbsp;"+cTotSet+"&nbsp;</span>"); }
                    if (cTotPaket>0)
                    { $("#txtTotPaket").html("<span style='color:white; background-color:#34599E; font-weight:bold'>&nbsp;"+cTotPaket+"&nbsp;</span>"); }
                }
            });
        }
    };

    function countRowView() {
        // a = divAkt.scrollTop();
        // b = divAkt.prop("scrollHeight") - divAkt.innerHeight();
        // c = a/b;
        divAkt = $("#divAktivitas");
        vwbleTestee = Math.ceil((divAkt.innerHeight()-30)/29);
    }

    function countallViewPage() {
        $.ajax({
            type: "post",
            url: "ajaxCountTesteeMode.php",
            data: {tampilanPeserta: modeShow},
            async: false,
            cache: false,
            success: function(nAllTestee)
            {
                allviewPage = Math.ceil(nAllTestee/vwbleTestee);
                if (allviewPage==0) { allviewPage++; }
                $("#allVP").text(allviewPage);
            }
        });
    }

    function changePgNumber() {
        $("#pgNumber").text(viewPageNow+"/"+allviewPage);
    }

    function recountPage() {
        countRowView();
        viewPageNow = 1;
        countallViewPage();
        allviewPage = parseInt($("#allVP").text());
        changePgNumber();
        cekaktivitas();
    }

    $("#menuTab").tabs();

    // source https://bensmann.no/jquery-resize-end-event/
    resizeTimer = false;
    $(window)
    .on('resize', function(e) {
        if( !resizeTimer )
        { $(window).trigger('resizestart'); }

        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            resizeTimer = false;
            $(window).trigger('resizeend');
        }, 400);
    })
    .on('resizestart', function()
    { /* do something; */ })
    .on('resizeend', function() {
        recountPage();
    })
    .load(function() {

        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
            /*
                // https://stackoverflow.com/questions/2481350/how-can-i-get-the-scrollbar-position-with-javascript

                // check below out for more about slickgrid
                // https://github.com/6pac/SlickGrid/releases
                // https://github.com/6pac/SlickGrid/blob/master/examples/example-optimizing-dataview.html
                // http://6pac.github.io/SlickGrid/examples/example-optimizing-dataview.html
            */

            // $("#divAktivitas").on('scroll', function() {
            //     countRowView();
            // });
        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

            recountPage();

            updatetotlogin();

            var timer = setInterval(showLog, 10000);

        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

			$("#dialogResetTO").dialog ({
				autoOpen:false, modal:true, resizable:false, width:400, show:{effect: 'shake', duration: 75}, hide:'explode'
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogResetHasilTO").dialog ({
				autoOpen:false, modal:true, resizable:false, width:400, show:{effect: 'shake', duration: 75}, hide:'explode'
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogAddNewShift").dialog ({
				autoOpen:false, modal:false, resizable:false, width:250
			});
					$("#dialogAddNewShift").on("dialogopen", function() {
						$("#harike").val('');
						$("#shift").val('');
						$("#jamnya").val('');
						$("#menitnya").val('');
						$("#durasinya").val('');
					});
			//.........................................................................
			$("#btnSaveNewShift").click(function() {
				$.ajax({
					type: "post",
					url: "ajaxAddNewShift.php",
					data: {hari:$("#harike").val(), shiftnya:$("#shift").val(), jam:$("#jamnya").val(), mnt:$("#menitnya").val(), dur:$("#durasinya").val(), tel:$("#ketelatan").val()},
					cache: false,
					success: function(refreshShift)
					{ $("#noShift").html(refreshShift); }
				});

				$("#dialogAddNewShift").dialog("close");
				tempAlert(' Shift baru sudah ditambahkan ', 2300, 15, 227);
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogEditShift").dialog ({
				autoOpen:false, modal:false, resizable:false, width:250
			});
					$("#dialogEditShift").on("dialogopen", function() {
						$("#ohari").val('');
						$("#oshift").val('');
						$("#ojam").val('');
						$("#omnt").val('');
						$("#odur").val('');
						
						$.ajax({
    						type: "post",
    						url: "ajaxCheckShift.php",
    						data: {idShiftNow:$("#noShift").val()},
    						cache: false,
    						dataType: "json",
    						success: function(getShift)
    							{
    								$("#ohari").val(getShift[0]);
    								$("#oshift").val(getShift[1]);
    								$("#ojam").val(getShift[2]);
    								$("#omnt").val(getShift[3]);
    								$("#odur").val(getShift[4]);
    								$("#otelat").val(getShift[5]);
    							}
						});
					});
			//.........................................................................
			$("#btnEditShift").click(function() {
				$.ajax({
					type: "post",
					url: "ajaxSaveEditShift.php",
                    data: {idChange:$("#noShift").val(), hari:$("#ohari").val(), shiftnya:$("#oshift").val(), jam:$("#ojam").val(), mnt:$("#omnt").val(), dur:$("#odur").val(), tel:$("#otelat").val()},
					cache: false,
					success: function(refreshShift)
					{
                        $("#noShift").html(refreshShift);
                        nomorShift = $("#noShift").val();
                        $("#pengaktifShift").click();
                    }
				});

				$("#dialogEditShift").dialog("close");
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogDeleteShift").dialog ({
				autoOpen:false, modal:false, resizable:false, width:300
			});
						$("#dialogDeleteShift").on("dialogopen", function() {
							$.ajax({
							type: "post",
							url: "ajaxCheckShift.php",
							data: {idShiftNow:$("#noShift").val()},
							cache: false,
							dataType: "json",
							success: function(getShift)
								{
									hhari = getShift[0];
									sshift = getShift[1];
									jjam = getShift[2];
									mmnt = getShift[3];
									
									$("#dataShiftMauDihapus").html('Shift '+hhari+', grup '+sshift+', jam '+jjam+':'+mmnt+' ??');
								}
							});
						});
			//.........................................................................
			$("#btnDelShift").click(function() {
				$.ajax({
					type: "post",
					url: "ajaxDelShift.php",
					data: {idDel:$("#noShift").val()},
					cache: false,
					success: function(shiftDel)
						{ $("#noShift").html(shiftDel); }
					});
				$("#dialogDeleteShift").dialog("close");
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#aPilihanSoal").mousedown(function() {
				$(this).css("transform", "translate(.5px,.5px)");

                if($("#roundBadge").html()!='✕')
                { $("#listPilihanSoal").toggle(270); }
			});

			$("#aPilihanSoal").mouseup(function()
			{ $(this).css("transform", "translate(-.5px,-.5px)"); });
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#aHasilBS").mousedown(function() {
				$(this).css("transform", "translate(.5px,.5px)");
				$("#listHasilBS").toggle(270);
			});

			$("#aHasilBS").mouseup(function()
			{ $(this).css("transform", "translate(-.5px,-.5px)"); });
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogDownloadHasil").dialog ({
				autoOpen:false, modal:false, resizable:false, width:590
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogBackup").dialog ({
				autoOpen:false, modal:false, resizable:false, width:410, height:280
			});

			$("#dialogRestore").dialog ({
				autoOpen:false, modal:false, resizable:false, width:350, height:150
			});

			$("#dialogDeleteBackup").dialog ({
				autoOpen:false, modal:false, resizable:false, width:350, height:150
			});

			$("#dialogDeleteAllBackup").dialog ({
				autoOpen:false, modal:false, resizable:false, width:350, height:150
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

			$("#dialogAddPeserta").dialog ({
				autoOpen:false, modal:false, resizable:false, 'width':'auto', show:'slide', hide:'blind'
			});

			$("#formAddPeserta").validationEngine('attach',
		    {
			  onValidationComplete: function(form, status) {
			    if(status!=false) {
				    pesNISN = $("#dnisn").val();
					pesNP = $("#dnopes").val();
					pesNama = $("#dnama").val();
					pesKls = $("#dkelas").val();
					pesJur = $("#djurusan").val();
					pesPaket = $("#dpakso").val();
					pesGrup = $("#dgrup").val();
			
					$.ajax({
						type: "post",
						url: "ajaxInsertNewPeserta.php",
						data: {pNISN:pesNISN, pNP:pesNP, pNama:pesNama, pKls:pesKls, pJur:pesJur, pPaket:pesPaket, pGrup:pesGrup},
						async: false,
						cache: false,
						success: function(cekNo)
						{
							$("#rcNoInd").html("");
							$("#rcNoPes").html("");

							if (cekNo==1)
							{ $("#rcNoInd").html("<span style='color:red'>sudah ada !</span>"); }

							else if (cekNo==2)
							{ $("#rcNoPes").html("<span style='color:red'>sudah ada!</span>"); }

							else if (cekNo==3) {
								$("#rcNoInd").html("<span style='color:red'>sudah ada!</span>");
								$("#rcNoPes").html("<span style='color:red'>sudah ada!</span>");
							}
						}
					});
				}
				else
				{ alert('Pastikan kembali data peserta baru sudah diisi dengan benar dan lengkap !!'); }
			  }  
			});
			
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$(".sortedBy").mousedown(function() {
				$(".sortedBy").css({"text-decoration": "none"});
				modeSort = $(this).attr('valTag');

				if (modeSort=='nomorUrut')
				{ $('[valTag="nomorUrut"]').css("text-decoration", "underline"); }

				else if (modeSort=='namaUrut')
				{ $('[valTag="namaUrut"]').css({"text-decoration": "underline"}); }

                cekaktivitas();
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogCekPeserta").dialog ({
				autoOpen:false, modal:false, resizable:false, 'width':'auto', 'height':'auto'
			});
			//.........................................................................
					$(".btnDlgCekPeserta").click(function() {
						idBtn = $(this).attr('id');
						utkNID = $("#idPesertaIni").val();
						utkNM = $("#inputDataBaru").val();
						utkNI = $("#dataNISiswa").text();
						lenNI = $("#nlab1").val();
						utkNP = $("#dataNPSiswa").text();
						lenNP = $("#nlab2").val();
						utkShift = $("#dataShiftSiswa").val();
						isiBS = $("#indBS").val();

						$.ajax({
								type: "post",
								url: "ajaxSetResetPeserta.php",
								data: {op:idBtn, NIDTO:utkNID, NMTO:utkNM, NITO:utkNI, lNITO:lenNI, NPTO:utkNP, lNPTO:lenNP, SHTO:utkShift, BSTO:isiBS},
								async: false,
								cache: false,
								success: function(msgProc)
								{
									if (idBtn=="btnSetLogKeyThisGroup" || idBtn=="btnSetLogKeyAll" || idBtn=="btnReSetLogKeyAll") {
                                        cekaktivitas();
                                    }

                                    else if (idBtn=="btnSaveNamaBaru" && $("#inputDataBaru").val()!='') {
										$("#teksNamaSiswa").text($("#inputDataBaru").val());
										$("#inputDataBaru").val('');
									}

									else if (idBtn=="btnGantiNoHP") {
										if(utkNM!="")
										{ $("#dataPhoneSiswa").text("+"+utkNM); } else
										{ $("#dataPhoneSiswa").text(""); }
										$("#inputDataBaru").val('');
									}
									
                                    else if (idBtn=="btnGantiEmail") {
										$("#dataEmailSiswa").text(utkNM);
										$("#inputDataBaru").val('');
									}

									else if (idBtn=="btnGantiShift")
									{ alert("Diubah menjadi grup "+utkShift); }

									else if (idBtn=="btnResetBS") {
										$("#indBS").css("background-color", "#FFFFAA");
										$("#indBS").css("color", "#FF0000");
										$("#indBS").css("font-weight", "bold");
									}

									if (msgProc=='NI OK') {
										$("#dataNISiswa").text(utkNM);
										$("#dialogCekPeserta").dialog('close');
									}

									else if (msgProc=='NP OK') {
										$("#dataNPSiswa").text(utkNM);
										$("#dialogCekPeserta").dialog('close');
									}
								}
						});
					});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogPindahJur").dialog ({
				autoOpen:false, modal:false, resizable:false, width:430, height:170
			});
			//.........................................................................
					$("#btnGantiIPAIPS").click(function() {
						if($(this).val()=="  >> IPA  ") {
							$("#txtOldJur").text("IPS");
							$("#btnChangeJur").val("  Pindahkan ke IPA  ");
						}
						else {
							$("#txtOldJur").text("IPA");
							$("#btnChangeJur").val("  Pindahkan ke IPS  ");
						}
						$("#dialogPindahJur").dialog("open");
					});
			//.........................................................................
					$("#btnChangeJur").dblclick(function() {
						//panggil script pemindah jurusan
						keNI = $("#dataNISiswa").text();
						keNP = $("#dataNPSiswa").text();
                        thisVal = $(this).val();

						$.ajax({
								type: "post",
								url: "ajaxChangeJur.php",
								data: {NITO:keNI, NPTO:keNP},
								cache: false,
								success: function() {
                                    if (thisVal.includes("IPA"))
                                    { $("#btnGantiIPAIPS").val("  >> IPS  "); }
                                    else
                                    { $("#btnGantiIPAIPS").val("  >> IPA  "); }
                                    cekaktivitas();
                                }
						});

						$("#dialogPindahJur").dialog("close");
					});
			//.........................................................................
					$("#btnCancelChangeJur").click(function()
					{ $("#dialogPindahJur").dialog("close"); });
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogLogKeyAll").dialog ({
				autoOpen:false, modal:false, resizable:false, width:300, hide:'fade'
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogFinishAll").dialog ({
				autoOpen:false, modal:false, resizable:false, 'width':'auto'
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogSearchGo").dialog ({
				autoOpen:false, modal:false, resizable:true, 'width':'auto', show:{effect: 'scale', duration: 170}, hide:{effect: 'clip', duration: 150}
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogSetTimer").dialog ({
				autoOpen:false, modal:false, resizable:false, width: 300
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogInputItem").dialog ({
				autoOpen:false, modal:true, resizable: false, 'width': 'auto'
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogChangeKey").dialog ({
				autoOpen:false, modal:false, resizable:false, width:200
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogKirimPesan").dialog ({
				autoOpen:false, modal:false, resizable:false, width:430
			});
			//.........................................................................
			$("#btnPeringatan").click(function()
			{
				$("#isiPesan").val('');
				$("#dialogKirimPesan").dialog("open");
			});
			//.........................................................................
			$(".kirimkanPesan").click(function() {
				btnID = $(this).attr('id');
				$.ajax({
					type: "post",
					url: "ajaxSendMessg.php",
					data: {idBtn:btnID, toID:$("#idPesertaWarn").val(), txtIsi:$("#isiPesan").val()},
					cache: false,
					success: function()
					{ dMX++; }
				});
				
				$("#dialogKirimPesan").dialog("close");
			});
			//.........................................................................
			$("#btnFinishIt").dblclick(function() {
				$.ajax({
					type: "post",
					url: "ajaxSendFinishIt.php",
					data: {toID:$("#idPesertaWarn").val()},
					cache: false,
					success: function()
					{}
				});
				$("#dialogKirimPesan").dialog("close");
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$("#dialogStatusTO").dialog ({
				autoOpen:false, modal:false, resizable:false, width:470
			});
			//.........................................................................
			$("#btnStatusTO").click(function() {
				noinduk = $("#dataNISiswa").text();
				nopeserta = $("#dataNPSiswa").text();

				$.ajax({
					type: "post",
					url: "ajaxCheckStatusTO.php",
					data: {noinduknya:noinduk, nopesertanya:nopeserta},
					cache: false,
					dataType: "json",
					success: function(getStat)
						{
							for (i=1; i<=10; i++)
							{
								$("#ac_Soal"+i).text('');
								$("#ac_Soal"+i).prop('title', '');

								$("#tmp_Answer"+i).text('');
								$("#tmp_Answer"+i).prop('title', '');
								
								$("#ha_Ke"+i).val('');

								//acakSoal
								j = 3*i-3;
								if (!getStat[j]) { getStat[j]=''; }
								$("#ac_Soal"+i).text(getStat[j].substr(0,14)+'...');
								$("#ac_Soal"+i).prop('title', getStat[j]);

								//tmpAnswer
								k = 3*i-2;
								if (!getStat[k]) { getStat[k]=''; }
								$("#tmp_Answer"+i).text(getStat[k].substr(2,14)+'...');
								$("#tmp_Answer"+i).prop('title', getStat[k].substr(2));

								//btnReset soal tesHariKe-n
								if (i<10) { iTxt = "0"+i; } else { iTxt = i; }
								$("#haKe_"+i).text('Clear Soal TO-'+iTxt);

								//flag tesHariKe-n
								l = 3*i-1;
								$("#ha_Ke"+i).val(getStat[l]);
							}
							
							$(".soalhake").prop('toNop',getStat[30]);
							$(".linkhake").prop('forNop',getStat[30]);
							$("#dialogStatusTO").dialog("open");
						}
				});
			});
			//.........................................................................
			$(".linkhake").dblclick(function() {
				theNop = $(this).prop('forNop');
				theId = $(this).prop('id');
				statNow = $(this).val();

				$.ajax({
					type: "post",
					url: "ajaxSetResetTOPerOrg.php",
					data: {npPesertanya:theNop, idTO:theId, nowStat:statNow},
					cache: false,
					success: function(hasilKond)
						{
							$("#"+theId).val(hasilKond);
						}
				});
			});
			//.........................................................................
			$(".soalhake").dblclick(function() {
				theNop = $(this).prop('toNop');
				theId = $(this).prop('id');

				$.ajax({
					type: "post",
					url: "ajaxResetSoalTOPerOrg.php",
					data: {npPesertanya:theNop, idTO:theId},
					cache: false,
					success: function()
						{
							$("#ac_Soal"+theId.slice(-1)).text('...');
							$("#tmp_Answer"+theId.slice(-1)).text('...');
						}
				});
			});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

			$("#dialogDeleteAllMedia").dialog ({
				autoOpen:false, modal:false, resizable:false, width:350, height:150
			});

		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
    		$("#tabH").click(function() {
    			$("#divSee").hide();
    			if (hasilSeen==0) {
                    tableRes.clearSort();
    				tableRes.replaceData();
    				tableRes.redraw();
    				hasilSeen = 1;
    			}
                seeActivity = false;
    		});
    		
    		$(".notTabH").click(function() {
    			hasilSeen=0;
    			var tagAA = $(this).attr('tagAAct');
    			if (tagAA=="1") {
                    $("#divSee").show();
                    seeActivity = true;
                }
                else {
                    $("#divSee").hide();
                    seeActivity = false;
                }
    		});
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

            $(".btnvPage").mousedown(function() {
                changePage = false;
                theID = this.id;
                allviewPage = parseInt($("#allVP").text());

                if (theID=="vPage1") {
                    viewPageNow = 1;
                    changePage = true;
                }
                else if (theID=="vPagePrev") {
                    if (viewPageNow==1)
                    { viewPageNow = allviewPage+1; }
                    viewPageNow--;
                    changePage = true;
                }
                else if (theID=="vPageNext") {
                    if (viewPageNow == allviewPage)
                    { viewPageNow = 0; }
                    viewPageNow++;
                    changePage = true;
                }
                else if (theID=="vPageEnd") {
                    viewPageNow = allviewPage;
                    changePage = true;
                }

                if (changePage) {
                    cekaktivitas();
                    changePgNumber();
                }
            });

        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
		// $("#peserta").hide();

		$("#btnResetTOYes").dblclick(function() {
			var resIDpeserta = $("#iddiReset").val();
			$.ajax({
				type: "post",
				url: "ajaxResetTO.php",
				data: {rID:resIDpeserta},
				cache: false,
				success: function(adaID)
				{
					if (adaID=='') {
						$("#ShiftygAktif").html('0:0 (grup 0)');
						$("#noShift").val(0).change();
					}

					$("#dialogResetTO").dialog("close");
				}
			});
		});

		$("#btnResetHasilTOYes").dblclick(function() {
			var resIDpeserta = $("#iddiHReset").val();
			$.ajax({
				type: "post",
				url: "ajaxResetHasilTO.php",
				data: {rID:resIDpeserta},
				cache: false,
				success: function()
				{ $("#dialogResetHasilTO").dialog("close"); }
			});
			
		});

		$("#Btn2Log").click(function() {
			if ($(this).text()=="Cek 2 Log") {
				$("#peserta").slideDown(160);
				$(this).text("Close");
			}
			else {
				$("#peserta").slideUp(160);
				$("#noPes").val("");
				$("#statusPeserta").html("");
				$(this).text("Cek 2 Log");
			}
		});
		
		$("#noShift").change(function()
        { nomorShift = $("#noShift").val(); });

		$("#noBS").change(function()
        { nomorBS = $("#noBS").val(); });
		
		$("#btnResetTO").dblclick(function() {
			$("#iddiReset").val("");
			$("#dialogResetTO").dialog("open");
		});

		$("#btnResetHasilTO").dblclick(function() {
			$("#iddiHReset").val("");
			$("#dialogResetHasilTO").dialog("open");
		});
		
		$("#pengaktifShift").click(function() {
			$.ajax({
				type: "post",
				url: "../activate.php",
				data: {st:"SAct", nS:nomorShift},
				cache: false,
				success: function(ubahShift)
                { $("#ShiftygAktif").html(ubahShift); }
			});
		});
		
		$("#pengaddShift").click(function() 
		{ $("#dialogAddNewShift").dialog("open"); });
		
		$("#pengeditShift").click(function() {
			if ($("#noShift").val()!='1')
			{ $("#dialogEditShift").dialog("open"); }
		});

		$("#pendeleteShift").click(function() {
			if ($("#noShift").val()!='1') {
				$("#dialogDeleteShift")
				.dialog("open")
				.dialog({
					position: {my: "left top", at: "left bottom", of: "#noShift"}
				});
			}
		});

		$("#BtnClearCheat").click(function() {
			$.ajax({
				type: "post",
				url: "../activate.php",
				data: {st:"clearCheat"},
				cache: false,
				success: function()
				{
					$("#Btn2Log").click();
				}
			});
		});
		
		$(".cBS").click(function() {
			var bsID = $(this).attr('tagID');
			var cek = $(this).is(":checked");

			if (cek) {
				//klo aktif
				$.ajax({
					type: "post",
					url: "../activate.php",
					data: {st:"TOAct", IDbs:bsID, tipeA:'aktif'},
					cache: false,
					success: function(totSoalAktif)
					{
						$("#roundBadge").text(totSoalAktif);
						if(totSoalAktif==0)
						{ $("#roundBadge").css("background-color","crimson"); }
						else
						{ $("#roundBadge").css("background-color","lightseagreen"); }
					}
				});
			}
			else {
				//klo gak aktif
				$.ajax({
					type: "post",
					url: "../activate.php",
					data: {st:"TOAct", IDbs:bsID, tipeA:'nonaktif'},
					cache: false,
					success: function(totSoalAktif)
					{
						$("#roundBadge").text(totSoalAktif);
						if(totSoalAktif==0)
						{ $("#roundBadge").css("background-color","crimson"); }
						else
						{ $("#roundBadge").css("background-color","lightseagreen"); }
					}
				});
			}

			
		});
		
		$("#btnDLHasil").click(function() 
		{ $("#dialogDownloadHasil").dialog("open"); });

		$("#btnBackupCek").click(function() {
			$.ajax({
				type: "post",
				url: "ajaxListBackup.php",
				cache: false,
				success: function(listBackup)
					{
						if (listBackup=='') {
							$("#labelRestorePoint").text('Tidak ada backup point');
							$("#btnClearRestorePoint").hide();
						}
						else {
							$("#labelRestorePoint").text('Pilih backup point untuk merestore database :');
							$("#btnClearRestorePoint").show();
						}
						
						$("#listBackup").html('');
						$("#listBackup").html(listBackup);

						$("#dialogBackup").dialog("open");
					}
			});
		});

		$("#btnBackupCek").mousedown(function(e) {
			if (e.which == 3)	//right click
			{ $("#btnBackupNow").trigger('click'); }
		});

		$(document).on("dblclick", ".btnRestoreDB", function() {
			rfile = $(this).attr('bname');
			$("#namaRestoreP").text(rfile);
			$("#yesRestore").attr('bHereR',rfile);
			$("#dialogRestore").dialog('open');
		});

		$("#yesRestore").click(function() {
			rpf = $(this).attr('bHereR');
			$.ajax({
				type: "post",
				url: "dbread.php",
				data:{fsource:rpf},
				cache: false,
				success: function(theFile)
				{
					attime = rpf.substr(0,1)+':'+rpf.substr(15,19);
					$("#lastBackup").text('Restored to '+attime);

					$("#dialogRestore").dialog("close");
				}
			});
		});

		$(document).on("mousedown", ".btnRestoreDB", function(e) {
			if (e.which == 3) {	//right click
				bfile = $(this).attr('bname');
				$("#namaBackup").text(bfile);
				$("#yesDelBackup").attr('bHere',bfile);

				$("#dialogDeleteBackup").dialog('open');
			}
		});

		$("#yesDelBackup").click(function() {
			buf = $(this).attr('bHere');
				
			$.ajax({
				type: "post",
				url: "ajaxDelFBackup.php",
				data: {dFile:buf},
				cache: false,
				success: function(listNow)
				{
					$("#dialogDeleteBackup").dialog('close');
					$("#listBackup").html(listNow);

					if (listNow=='') {
						$("#labelRestorePoint").text('Tidak ada backup point');
						$("#btnClearRestorePoint").hide();
					}
					else {
						$("#labelRestorePoint").text('Pilih backup point untuk merestore database :');
						$("#btnClearRestorePoint").show();
					}
				}
			});
		});

		$("#btnBackupNow").click(function() {
			var dt = new Date();
			
			thn = dt.getFullYear();
			bln = dt.getMonth(); bln++; if (bln<10) { bln = '0'+bln; }
			tgl = dt.getDate(); if (tgl<10) { tgl = '0'+tgl; }
			jam = dt.getHours(); if (jam<10) { jam = '0'+jam; }
			mnt = dt.getMinutes(); if (mnt<10) { mnt = '0'+mnt; }
			dtk = dt.getSeconds(); if (dtk<10) { dtk = '0'+dtk; }

			timeStamp = '@'+thn+'.'+bln+'.'+tgl+'@'+jam+'.'+mnt+'.'+dtk;

			$.ajax({
				type: "post",
				url: "dbdump.php",
				data: {tS:timeStamp},
				async: false,
				cache: false,
				success: function(dbRes)
				{
					resDump = dbRes;

					if (resDump!='') {
						buTime = jam+':'+mnt+':'+dtk; //backuptime;
						$("#lastBackup").text('Last backup @ '+buTime);

						$("#listBackup").html(dbRes);

						if (dbRes=='') {
							$("#labelRestorePoint").text('Tidak ada backup point');
							$("#btnClearRestorePoint").hide();
						}
						else {
							$("#labelRestorePoint").text('Pilih backup point untuk merestore database :');
							$("#btnClearRestorePoint").show();
						}
					}
				}
			});	
		});

		$("#btnClearRestorePoint").dblclick(function()
		{ $("#dialogDeleteAllBackup").dialog("open"); });

		$("#yesDelAllBackup").dblclick(function() {
			$.ajax({
				type: "post",
				url: "ajaxDelAllFBackup.php",
				cache: false,
				success: function()
				{
					$("#dialogDeleteAllBackup").dialog("close");
					$("#listBackup").html('');
					$("#dialogBackup").dialog("close");
				}
			});
		});

		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

		$("#modeTampilan").change(function() {
            modeShow = $(this).val();
            viewPageNow = 1;
            countallViewPage();
            allviewPage = parseInt($("#allVP").text());
            changePgNumber();
            cekaktivitas();
        });

		$("#btnAddPeserta").click(function() {
			$("#dnisn").val(''); $("#rcNoInd").html("");
			$("#dnopes").val(''); $("#rcNoPes").html("");
			
			$("#dnama").val('');
			$("#dkelas").val('');
			$("#djurusan").val('');
			$("#dpakso").val('1');
			$("#dgrup").val('1');
			$("#prodi1").val('');
			$("#prodi2").val('');
			$("#prodi3").val('');
			$("#dialogAddPeserta").dialog("open");
		});

		$("#btnCallLogKeyAll").click(function() 
		{ $("#dialogLogKeyAll").dialog("open"); });

		$("#btnCallFinishAll").click(function() {
			$("input[name='rJur'").removeAttr('checked');
			$("#dialogFinishAll").dialog("open");
		});

		$("#btnYesFinishAll").click(function() {
			kodeTO=$("#kSoal option:selected").val();
			jurS=$("input[name='rJur']:checked").val();
			
			if(kodeTO!='0' && $("input[name='rJur'").is(":checked")) {
				$.ajax({
					type: "post",
					url: "ajaxGetResult.php",
					data: {kto:kodeTO, kjur:jurS},
					cache: false,
					success: function(resFin)
					{
						alert ('Finishing '+kodeTO+' sebanyak '+resFin+' data');
					}
				});
			}
		});

		///// GoSearch semua data hasil pencarian klo ada yg mmnuhi kriteria pencarian yg sama /////
		$("#letsSearch").click(function() {
			$("#searchResult1").html('');
			$("#searchFor").val('');
			$("#dialogSearchGo").dialog("open");
		});

		$("#searchFor").keyup(function() {
			if ($(this).val().length > 2)
			{ $("#GoSearch").trigger('click'); }
			else
			{ $("#searchResult1").html(""); }
		});

		$("#GoSearch").click(function() {
			byData = $("#cariData").val();
			whatData = $("#searchFor").val();
			if (byData=="" || whatData==""){ return; }

			$.ajax({
			type: "post",
			url: "ajaxGoSearch.php",
			data: {bDT:byData, wDT:whatData},
			cache: false,
			dataType: "json",
			success: function(getWho)
				{
					var dSR = getWho[0];
					
					if (dSR < 2) {
						var dID = getWho[1];
                        if (dID<10) { dID = '00'+dID; } else if (dID<100) { dID = '0'+dID; }
						var dPS = getWho[2];
						var dKLS = getWho[3];
						var dNM = getWho[4];
						var dNI = getWho[5];
						var dNP = getWho[6];
						var dShift = getWho[7];
						var dMX = getWho[8];
						var dHP = getWho[9];
						var dEm = getWho[10];
						var dJur = getWho[11];
						dMX++;

						if (dNI==null)
						{ $("#searchResult1").html(""); }
						else {
							$("#noUrutID").text(dID);
                            $("#teksNamaSiswa").text(dNM);
							$("#inputDataBaru").val('');
							$("#idPesertaIni").val(dID);
							$("#petaSoalx").text(dPS);
							$("#klsPesertaIni").text(dKLS);
							$("#dataShiftSiswa").val(dShift);
							$("#dataNISiswa").text(dNI);
							$("#dataNPSiswa").text(dNP);
							$("#dataPhoneSiswa").text(dHP);
							$("#dataEmailSiswa").text(dEm);
							$("#idPesertaWarn").val(dID);
							$("#xWarn").text(dMX);
							$("#indBS").val('');
							$("#indBS").css("background-color", "white");
							$("#indBS").css("color", "black");
							$("#indBS").css("font-weight", "normal");
							if (dJur=='IPC')
							{ $("#btnGantiIPAIPS").css('display','none'); }
							else {
								$("#btnGantiIPAIPS").css('display','inline');
								if (dJur=="IPA") {
									$("#btnGantiIPAIPS").val('  >> IPS  ');
									$("#btnGantiIPAIPS").prop('title','Pindah ke IPS');
								}
								else if (dJur=="IPS") {
									$("#btnGantiIPAIPS").val('  >> IPA  ');
									$("#btnGantiIPAIPS").prop('title','Pindah ke IPA');
								}
							}

							$("#dialogSearchGo").dialog("close");
							$("#dialogCekPeserta").dialog("open");
						}
					}
					else
					{ $("#searchResult1").html(getWho[1]); }
				}
			});
		});

		$(document).on("click", ".listSearchRes", function() {
			nisData = $(this).attr('tagnis');
			nopData = $(this).attr('tagnop');

			$.ajax({
			type: "post",
			url: "ajaxGo4Id.php",
			data: {nisDT:nisData, nopDT:nopData},
			cache: false,
			dataType: "json",
			success: function(getWho)
				{
					var dID = getWho[0];
					var dPS = getWho[1];
					var dKLS = getWho[2];
					var dNM = getWho[3];
					var dNI = getWho[4];
					var dNP = getWho[5];
					var dShift = getWho[6];
					var dMX = getWho[7];
					var dHP = getWho[8];
					var dEm = getWho[9];
					var dJur = getWho[10];
					dMX++;

					if (dNI==null) {
						alert("Data tidak ditemukan !!");
						return;
					}
					else {
						$("#noUrutID").text(dID);
                        $("#teksNamaSiswa").text(dNM);
						$("#inputDataBaru").val('');
						$("#idPesertaIni").val(dID);
						$("#petaSoalx").text(dPS);
						$("#klsPesertaIni").text(dKLS);
						$("#dataShiftSiswa").val(dShift);
						$("#dataNISiswa").text(dNI);
						$("#dataNPSiswa").text(dNP);
						$("#dataPhoneSiswa").text(dHP);
						$("#dataEmailSiswa").text(dEm);
						$("#idPesertaWarn").val(dID);
						$("#xWarn").text(dMX);
						$("#indBS").val('');
						$("#indBS").css("background-color", "white");
						$("#indBS").css("color", "black");
						$("#indBS").css("font-weight", "normal");
						if (dJur=='IPC')
						{ $("#btnGantiIPAIPS").css('display','none'); }
						else {
							$("#btnGantiIPAIPS").css('display','inline');
							if (dJur=="IPA") {
								$("#btnGantiIPAIPS").val('  >> IPS  ');
								$("#btnGantiIPAIPS").prop('title','Pindah ke IPS');
							}
							else if (dJur=="IPS") {
								$("#btnGantiIPAIPS").val('  >> IPA  ');
								$("#btnGantiIPAIPS").prop('title','Pindah ke IPA');
							}
						}

						$("#dialogSearchGo").dialog("close");
						$("#dialogCekPeserta").dialog("open");
					}
				}
			});
		});

		$("#btnCallNewTimer").click(function() {
			$("#newTime").val('');
			$("#dialogSetTimer").dialog("open");
		});

		$("#btnSetNewTimer, #btnSetNewTimerAll").click(function() {
			nTimer = $("#newTime").val();
			idBtn = $(this).prop('id');
			
			if (nTimer!='') {
				$.ajax({
					type: "post",
					url: "ajaxSetNewTime.php",
					data: {ticks:nTimer, vNP:$("#dataNPSiswa").text(), idB:idBtn},
					cache: false,
					success: function()
					{}
				});
			}
			$("#dialogSetTimer").dialog("close");
		});

		$("#btnSetLogKeyAll, #btnSetLogKeyThisGroup").click(function() 
		{ $("#dialogLogKeyAll").dialog("close"); });

		$(document).on("click", ".nmPesertaTes", function() {
			
			var dNM;
			var dNI = $(this).attr('tagni');
			var dNP = $(this).attr('tagnp');

			$.ajax({
				type: "post",
				url: "ajaxJustGetName.php",
				data: {iNI:dNI, iNP:dNP},
				async: false,
				cache: false,
				success: function(hslName)
				{ dNM = hslName; }
			});

			var dID = $(this).attr('tagnoid');
			var dPS = $(this).attr('tagps');
			var dKLS = $(this).attr('tagkls');
			var dJrs = $(this).attr('tagjur');
			var dShift = $(this).attr('tagshift');
			var dHP = $(this).attr('tagnohp');
			var dEm = $(this).attr('tagem');
			var dMX = $(this).attr('tagmx');
			dMX++;

			$("#noUrutID").text(dID);
            $("#teksNamaSiswa").text(dNM);
			$("#inputDataBaru").val('');
			$("#idPesertaIni").val(dID);
			$("#petaSoalx").text(dPS);
			$("#klsPesertaIni").text(dKLS);
			$("#dataShiftSiswa").val(dShift);
			$("#dataNISiswa").text(dNI);
			$("#dataNPSiswa").text(dNP);
			$("#dataPhoneSiswa").text(dHP);
			$("#dataEmailSiswa").text(dEm);
			$("#idPesertaWarn").val(dID);
			$("#xWarn").text(dMX);
			$("#indBS").val('');
			$("#indBS").css("background-color", "white");
			$("#indBS").css("color", "black");
			$("#indBS").css("font-weight", "normal");
			if (dJrs=='IPC')
			{ $("#btnGantiIPAIPS").css('display','none'); }
			else
			{
				$("#btnGantiIPAIPS").css('display','inline');
				if (dJrs=="IPA") {
					$("#btnGantiIPAIPS").val('  >> IPS  ');
					$("#btnGantiIPAIPS").prop('title','Pindah ke IPS');
				}
				else if (dJrs=="IPS") {
					$("#btnGantiIPAIPS").val('  >> IPA  ');
					$("#btnGantiIPAIPS").prop('title','Pindah ke IPA');
				}
			}

			$("#dialogCekPeserta").dialog("open");
		});

		$(document).on("mousedown", ".nmPesertaTes", function(e) {
			if (e.which == 3) {	//jika right click maka logkey peserta ini
				utkNID = $(this).attr('tagnoid');
				$.ajax({
						type: "post",
						url: "ajaxSetResetPeserta.php",
						data: {op:'btnSetLogKey', NIDTO:utkNID},
						async: false,
						cache: false,
						success: function()
						{}
					});
                cekaktivitas();
			}
		});
		
		$(".txtdlmateri").click(function() {
			var tabelnm = $(this).attr('nmtabelDB');
			var bsnm = $(this).attr('nmBS');
			
			$("#tabelnya").val(tabelnm);
			$("#bidstudinya").val(bsnm);
			
			$("#formPerMateri").submit();
		});

		$("#dALL").click(function()
		{ $("#formAll").submit(); });

		$("#dIPA").click(function()
		{ $("#formIPA").submit(); });
		
		$("#dIPS").click(function() 
		{ $("#formIPS").submit(); });

		$("#btnJudulSimpan").click(function() {
			$.ajax({
				type: "post",
				url: "ajaxSaveTitle.php",
				data: {tdata:$("#titleTO").val()},
				cache: false,
				success: function()
					{ }
				});
		});
		
		$("#btnNamaSekolahSimpan").click(function() {
			$.ajax({
				type: "post",
				url: "ajaxSaveSekolah.php",
				data: {op:"nmSekolah", datasek:$("#schoolName").val()},
				cache: false,
				success: function()
					{ }
				});
		});

		$("#btnPelaksanaanSimpan").click(function() {
			$.ajax({
				type: "post",
				url: "ajaxSavePelaksanaan.php",
				data: {dataLak:$("#teksLaksana").val()},
				cache: false,
				success: function()
					{ }
				});
		});

		$("#btnMottoSekolahSimpan").click(function() {
			$.ajax({
				type: "post",
				url: "ajaxSaveSekolah.php",
				data: {op:"mtSekolah", datasek:$("#schoolMotto").val()},
				cache: false,
				success: function()
					{ }
				});
		});

        $("#kodeFolder").change(function() {
			petanya = $("#kodeFolder option:selected").attr("pktSoal");
			kuncinya = $("#kodeFolder option:selected").attr("kkey");
			durasinya = $("#kodeFolder option:selected").attr("bsDur");
			nThumb = $("#kodeFolder option:selected").attr("nItem");
			if (nThumb.indexOf('+') > -1) {
			  nSoal = nThumb.split("+");
			  nThumb = parseFloat(nSoal[0])+parseFloat(nSoal[1]);
			}

			$("#soalPaket").val(petanya);
			$("#keyIsi").val(kuncinya);
			$("#bsDurasi").val(durasinya);
			
			nowThumb=1;
			kunciKini = $("#keyIsi").val().substr(2*nowThumb-2,1);
			$("#noImg").val(nowThumb+". "+kunciKini);

			dntag=$("#kodeFolder option:selected").val();
			$("#imgPreview").attr("src", "../images/soal/"+dntag+"/1.png");
			
		});

		$("#chPaketSoal").click(function() {
			$.ajax({
				type: "post",
				url: "ajaxChangePaketOrDurationBS.php",
				data: {op:'chPaket', pKodeSoal:dntag, newPaket:$("#soalPaket").val()},
				cache: false,
				success: function()
				{
					$("#kodeFolder option:selected").attr('pktSoal',$("#soalPaket").val());
					$("#pPaket"+dntag).text($("#soalPaket").val());
					$(".namedir[tagdn='"+dntag+"']").attr('tagpS',$("#soalPaket").val());
				}
			});
		});

		$("#keyIsi").on("input", function()
		{ $("#dialogChangeKey").dialog("open"); });

		$(".btnKeyChange").click(function() {
			if ($(this).attr("id")=="btnYesChangeKey") {
				$.ajax({
					type: "post",
					url: "ajaxChangeKey.php",
					data: {st:"cKey", pKodeSoal:dntag, kunciBaru:$("#keyIsi").val()},
					cache: false,
					success: function()
					{
						$(".namedir[tagdn='"+dntag+"']").attr("tagkey",$("#keyIsi").val());
						$("#kodeFolder option:selected").attr("kkey",$("#keyIsi").val());
					}
				});
			}
			$("#dialogChangeKey").dialog("close");
		});

		$("#setNewDur").click(function() {
			$.ajax({
				type: "post",
				url: "ajaxChangePaketOrDurationBS.php",
				data: {op:'chDuration', pKodeSoal:dntag, newDuration:$("#bsDurasi").val()},
				cache: false,
				success: function()
				{
					$("#kodeFolder option:selected").attr('bsDur',$("#bsDurasi").val());
				}
			});
		});

		$(".navThumb").mousedown(function() {
			cekid = $(this).attr("id");
			if (cekid == "minusThumb") {
				if (nowThumb==1) { nowThumb=parseFloat(nThumb)+1; }
				nowThumb -= 1;
				$("#imgPreview").attr("src", "../images/soal/"+dntag+"/"+nowThumb+".png");
			}
			else {
				if (nowThumb==nThumb) { nowThumb=0; }
				nowThumb += 1;
				$("#imgPreview").attr("src", "../images/soal/"+dntag+"/"+nowThumb+".png");
			}
			kunciK = $("#keyIsi").val().split(",");
			kunciKini = kunciK[parseFloat(nowThumb)-1];
			$("#noImg").val(nowThumb+". "+kunciKini);
		});

		$("#btnSaveName").click(function() {
			$.ajax({
				type: "post",
				url: "ajaxSaveName.php",
				data: {dataName:$("#adminName").val(), dataNumber:$("#adminNumber").val(), datagwa:$("#grupWA").val()},
				cache: false,
				success: function()
				{ }
			});
		});

		$("#mediaList").val('<?= $listMedia; ?>');

		$("#btnClearAllMedia").dblclick(function()
		{ $("#dialogDeleteAllMedia").dialog("open"); });

		$("#yesDelAllMedia").dblclick(function() {
			$.ajax({
				type: "post",
				url: "ajaxDelAllMedia.php",
				cache: false,
				success: function()
				{
					$("#mediaList").val('');
					$("#dialogDeleteAllMedia").dialog("close");
				}
			});
		});

		//::::::::::::::::::::::: file uploader ::::::::::::::::::::::::::::::::::
		//sumber : hayageek.com/docs/jquery-upload-file.php

		//uploader logo sekolah
		var settingUploadLogo = {
			url: "uploadFileLogo.php",
			method: "POST",
			allowedTypes: "png",
			fileName: "myfile",
			maxFileCount: 1,
			uploadStr: "Pilih file logo...",
			dragDropStr: "<span><b>atau drag drop ke sini</b></span>",
			extErrorStr: " tidak bertipe ",
			maxFileCountErrorStr: " dibatalkan. Maks banyak file sekali upload : ",
			showPreview: true,
			previewHeight: "32%", previewWidth: "32%",
			autoSubmit: false, showCancel: false,
			multiple: true,
			afterUploadAll:function()
			{
				$.ajax({
					type: "post",
					url: "ajaxGetErrUpload.php",
					cache: false,
					success: function(theErr)
						{
							uploadObj.reset();
							if(theErr!="") {
								d = new Date();
								$("#imgLogo").attr("src", "../images/logo_sekolah.png?"+d.getTime());
							}
						}
				});
			}
		};
		
		var uploadObj = $("#uploadDiv").uploadFile(settingUploadLogo);

		//-------------------------------------------------------------
			$("#btnStartUpload").click(function()
            { uploadObj.startUpload(); });

		//-------------------------------------------------------------
		//-------------------------------------------------------------

		//uploader paket soal
		var settingUploadPaket = {
			url: "uploadPaketSoal.php",
			method: "POST",
			allowedTypes: "zip",
			fileName: "zip_file",
			maxFileCount: 1,
			uploadStr: "Pilih paket soal...",
			dragDropStr: "<span><b>atau drag drop ke sini</b></span>",
			extErrorStr: " tidak bertipe ",
			maxFileCountErrorStr: " dibatalkan. Maks banyak file sekali upload : ",
			showPreview: true,
			autoSubmit: false, showCancel: false,
			multiple: false,
			afterUploadAll:function()
			{
				$.ajax({
					type: "post",
					url: "ajaxGetErrUpload.php",
					cache: false,
					success: function(theErr)
						{
							uploadObjPaket.reset();
							if(theErr!="")
							{ alert(theErr); }
						}
				});
			}
		};
		
		var uploadObjPaket = $("#uploadDivPaket").uploadFile(settingUploadPaket);

		//-------------------------------------------------------------
			$("#btnStartUploadPaket").click(function() {
				uploadObjPaket.startUpload();
			});
		//-------------------------------------------------------------
		//-------------------------------------------------------------

		//uploader paket soal
		var settingUploadMedia = {
			url: "uploadMediaFile.php",
			method: "POST",
			fileName: "media_file",
			maxFileCount: 5,
			uploadStr: "Pilih media...",
			dragDropStr: "<span><b>atau drag drop ke sini</b></span>",
			extErrorStr: " tidak bertipe ",
			maxFileCountErrorStr: " dibatalkan. Maks banyak file sekali upload : ",
			showPreview: true,
			autoSubmit: false, showCancel: false,
			multiple: true,
			afterUploadAll:function()
			{
				uploadObjMedia.reset();

				$.ajax({
					type: "post",
					url: "ajaxListMedia.php",
					cache: false,
					success: function(medias)
						{ $("#mediaList").val(medias); }
				});
			}
		};
		
		var uploadObjMedia = $("#uploadMedia").uploadFile(settingUploadMedia);

		//-------------------------------------------------------------
			$("#btnUploadMedia").click(function()
            { uploadObjMedia.startUpload(); });
		//-------------------------------------------------------------

		//########################################################################


		$("input[name='showSearch']").change(function() {
			thisSetting = $(this).attr('checked')
			$.ajax({
				type: "post",
				url: "ajaxSetCheckSystem.php",
				data: {op:"setShowSearch", setNow:thisSetting},
				cache: false,
				success: function()
				{}
			});
		});

		$("#no1").on("input", function() {
			lTxtSortLbl1 = $(this).val().toLowerCase();
		    $("#txtsortlbl1").text(lTxtSortLbl1);
		    $("#txtlbl1").text($(this).val());
		    $("#textID1").text($(this).val());
		    $("#btnSaveNewID1").val(" Ganti "+$(this).val()+" ");
		});

		$("#no2").on("input", function() {
		    $("#txtlbl2").text($(this).val());
		    $("#textID2").text($(this).val());
		    $("#btnSaveNewID2").val(" Ganti "+$(this).val()+" ");
		});

		$("input[name='input2pw']").change(function() {
			thisSetting = $(this).attr('checked');
			$.ajax({
				type: "post",
				url: "ajaxSetCheckSystem.php",
				data: {op:"setInp2pw", setNow:thisSetting},
				cache: false,
				success: function()
				{}
			});
		});

		$("#btnSaveLabel").click(function() {
			$.ajax({
				type: "post",
				url: "ajaxSaveLabel.php",
				data: {dataLbl1:$("#no1").val(), dataLbl2:$("#no2").val(), nLbl1:$("#nlab1").val(), nLbl2:$("#nlab2").val()},
				cache: false,
				success: function()
				{
					$("#teksDataLog1").text($("#no1").val());
					$("#teksDataLog2").text($("#no2").val());
				}
			});
		});

		$("#btnSavePrefix").click(function() {
			$.ajax({
				type: "post",
				url: "ajaxSavePrefix.php",
				data: {datapref1:$("#prefixID1").val(), datapref2:$("#prefixID2").val()},
				cache: false,
				success: function()
				{}
			});
		});

		$("input[name='inputHP']").change(function() {
			thisSetting = $(this).attr('checked');
			$.ajax({
				type: "post",
				url: "ajaxSetCheckSystem.php",
				data: {op:"setInpHP", setNow:thisSetting},
				cache: false,
				success: function()
				{}
			});
		});

		$("input[name='inputEmail']").change(function() {
			thisSetting = $(this).attr('checked');
			$.ajax({
				type: "post",
				url: "ajaxSetCheckSystem.php",
				data: {op:"setInpEmail", setNow:thisSetting},
				cache: false,
				success: function()
				{}
			});
		});

		$("input[name='inputDoB']").change(function() {
			thisSetting = $(this).attr('checked');
			$.ajax({
				type: "post",
				url: "ajaxSetCheckSystem.php",
				data: {op:"setInpDoB", setNow:thisSetting},
				cache: false,
				success: function()
				{}
			});
		});

		$("input[name='pilJurOpt']").change(function() {
			thisSetting = $(this).attr('checked');
			$.ajax({
				type: "post",
				url: "ajaxSetCheckSystem.php",
				data: {op:"setPilJur", setNow:thisSetting},
				cache: false,
				success: function()
				{}
			});
		});
		
		$("input[name='nOpsi']").change(function() {
			$.ajax({
				type: "post",
				url: "ajaxSetCheckSystem.php",
				data: {op:"changeNOpsi", setNow:$(this).val()},
				cache: false,
				success: function()
				{}
			});
		});
		
		$("input[name='erasableOpt']").change(function() {
			thisSetting = $(this).attr('checked')
			$.ajax({
				type: "post",
				url: "ajaxSetCheckSystem.php",
				data: {op:"setErasableOpsi", setNow:thisSetting},
				cache: false,
				success: function()
				{}
			});
		});

		$("input[name='showScore']").change(function() {
			thisSetting = $(this).attr('checked')
			$.ajax({
				type: "post",
				url: "ajaxSetCheckSystem.php",
				data: {op:"setShowScore", setNow:thisSetting},
				cache: false,
				success: function()
				{}
			});
		});
		
		$("#btnSaveScore").click(function() {
			$.ajax({
				type: "post",
				url: "ajaxSaveScore.php",
				data: {sBenar:$("#sBenar").val(), sSalah:$("#sSalah").val(), sKosong:$("#sKosong").val(), sSkala:$("#sSkala").val()},
				cache: false,
				success: function()
				{}
			});
		});

		$("input[name='tHasil']").change(function() {
			$.ajax({
				type: "post",
				url: "ajaxChangeTHasil.php",
				data: {thsl:$(this).val()},
				cache: false,
				success: function()
				{}
			});
		});

		$(document).on("click", ".namedir", function() {
			pstag = $(this).attr('tagpS');
			dntag = $(this).attr('tagdn');
			nThumb = $(this).attr('tagbin');
			keytag = $(this).attr('tagkey');
			keydur = $(this).attr('tagdur');

			$("#soalPaket").val(pstag);
			$("#kodeFolder").val(dntag);
			$("#keyIsi").val(keytag);
			$("#bsDurasi").val(keydur);

			nowThumb = 1;
			kunciKini = $("#keyIsi").val().substr(2*nowThumb-2,1);
			$("#noImg").val(nowThumb+". "+kunciKini);
			$("#imgPreview").attr("src", "../images/soal/"+dntag+"/1.png");

			$("#dialogInputItem").dialog("open");
		});
		
		$("#btnUploadImgSoal").click(function()
		{ $("#dialogInputItem").dialog("close"); });

	});
</script>

</body>

</html>