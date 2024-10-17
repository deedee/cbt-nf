<?php

require_once('../koneksi_db.php');
$datalogin = mysqli_query($con,"SELECT labelno1, labelno2, nlabel1, nlabel2, unadmin, pwadmin FROM datasystem WHERE id=1");
$inidatanya =mysqli_fetch_array($datalogin);

$lblID1 = $inidatanya['labelno1'];
$lblID2 = $inidatanya['labelno2'];
$nlbl1 = $inidatanya['nlabel1'];
$nlbl2 = $inidatanya['nlabel2'];

$adminUN = $inidatanya['unadmin'].'2';
$adminPW = $inidatanya['pwadmin'].'2';

$sampleIDFormat = mysqli_query($con,"SELECT nomorInduk, nomorPeserta FROM absensiharitespeserta WHERE id=1");
$sampleData =mysqli_fetch_array($sampleIDFormat);
$sampleID1 = $sampleData['nomorInduk'];
$sampleID2 = $sampleData['nomorPeserta'];

if ($_POST['usern']!=$adminUN || $_POST['passw']!=$adminPW || $_POST['fromForm']!="inForm2")
{
	echo ('<script type="text/javascript">');
	echo ('window.location="../admin2";');
	echo ('</script>');
}

?>

<!DOCTYPE html>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../images/nf-favico.ico" type="image/x-icon">
	<link rel="stylesheet" href="adminstyle.css" />

    <script type="text/javascript" src="../jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="../jquery-ui-1.8.10.custom.min.js"></script>
    <!-- penggunaan ui touch punch di bawah ini memungkinkan perangkat mobile dpt melakukan
    		drag pd elemen div, tapi membuat pemilihan elemen input tdk bisa di pilih -->
    <!-- <script type="text/javascript" src="../jquery.ui.touch-punch.min.js"></script> -->

	<link rel="stylesheet" type="text/css" href="../jquery-ui-1.8.10.custom.css" />
	<link rel="stylesheet" type="text/css" href="../jquery.ui.theme.css" />

	<style type="text/css">
	<!--

		body
		{
		    margin: auto;
		    padding: 5px 5px 5px 5px;
		    background-color: ivory;
		    font-family: Arial;
			font-size:19px;
			font-weight:bold;
			color:#006699;
		}

		#inputNama
		{
			border-radius: 4px;
			border-style: dotted;
			border-color:#069;
			border-width:3px;
			border-left: 0px;
			border-top: 0px;
			border-right: 0px;
			font-size:15px;
			font-weight:bold;
			font-family: Arial;
			background-color: whitesmoke;
			color:#006699;
			text-align:left;
			padding: 6px 10px;
			margin: 8px 0;
			box-sizing: border-box;
		}

		#resDiv
		{
				text-transform: uppercase;
				font-size:14px;
				font-family: Arial;
				text-align:left;
				margin-top: 10px;
				box-sizing: border-box;
		}

		#popup {
			display:none;
			background-color:#f0f0f0;
			position:absolute;
			padding:7px 10px 0px 10px;
			border:2px solid silver;
			border-radius:3px;
			top: 115px;
			left: 20px;
			z-index: 200;
			box-shadow: 2px 2px 3px #999;
	    }

	    #tblDaftarOTS input {
	    	padding-left: 4px;
	    }

	-->
	</style>

</head>

<body>

<!-- ## form popup image preview ## -->
<div id="popup">
    <span style="font-family:Arial; font-size: 17px; background-color: #006699; color: white; width: 100%;padding: 1px 3px 1px 3px;"> Data Peserta </span>
    <br><br>
    <center>
    	<table id="tblDaftarOTS" style="font-size: 14px; text-align: left">
    		<tr>
    			<th scope="row"><?php echo $lblID1; ?></th>
    			<td><input type="text" name="iID1" id="iID1" maxlength="<?php echo $nlbl1; ?>" placeholder="<?php echo $nlbl1; ?> char. <?php echo $sampleID1; ?>"></td>
    		</tr>
    		<tr>
    			<th scope="row"><?php echo $lblID2; ?></th>
    			<td><input type="text" name="iID2" id="iID2" maxlength="<?php echo $nlbl2; ?>" placeholder="<?php echo $nlbl2; ?> char. <?php echo $sampleID2; ?>"></td>
    		</tr>
    		<tr>
    			<th scope="row">Nama</th>
    			<td><input type="text" name="iNama" id="iNama"></td>
    		</tr>
    		<tr>
    			<th scope="row">Kelas / Sekolah</th>
    			<td><input type="text" name="iKelas" id="iKelas"></td>
    		</tr>
    		<tr>
    			<th scope="row">Jurusan</th>
    			<td>
    				<select name="iJurusan" id="iJurusan">
    					<option val="" selected>Pilihan dlm TO</option>
    					<option val="A">IPA</option>
    					<option val="S">IPS</option>
    					<option val="C">IPC</option>
    				</select>
    			</td>
    		</tr>
    		<tr>
    			<th scope="row">Paket Soal</th>
    			<td><input type="text" size="2" style="text-align: center" name="iPaket" id="iPaket"></td>
    		</tr>
    		<tr>
    			<th scope="row">Grup Tes</th>
    			<td><input type="text" size="2" style="text-align: center" name="iGrup" id="iGrup"></td>
    		</tr>
    	</table>
    	<br><br>
        <button style="color:navy" id="btnDaftarOTS">Daftar</button>&nbsp;&nbsp;<button style="color: red; font-weight: bold" id="btnClosePopup">&#10006;</button>
        <br><br>
    </center>
</div>

Cari Peserta <br>
<input type="text" id="inputNama" size="28" placeholder="q : login, a : semua, z : error" autofocus>&nbsp;
<button id="btnClearNama" style="color: red; font-weight: bold; font-size: 21px; transform: translateY(4px);">&#10006;</button><br>
	 <button id="btnOTS" style="font-size: 11px;">✚&nbsp; Peserta</button> &nbsp;
	<a href="#" id="aLegend" style="font-size: 11px; font-weight: bold">Status Warna</a>

<div id="resDiv">

</div>

<script type="text/javascript">

	$(window).load(function()
	{
		lbl1 = '<?php echo $lblID1; ?>';
		lbl2 = '<?php echo $lblID2; ?>';

		lbl1Len = '<?php echo $nlbl1; ?>';
		lbl2Len = '<?php echo $nlbl2; ?>';

		$(function()
	    {
	        var utimer = setInterval(updating, 1500);

	        function updating() 
    		{ $("#inputNama").trigger('keyup'); }
	    });

		$("#inputNama").keyup(function()
		{
			carian = $(this).val();

			if (carian.length>2 || carian=="q" || carian=="Q" || carian=="a" || carian=="A" || carian=="z" || carian=="Z")
			{
				$.ajax({
					type: "post",
					url: "cariPeserta.php",
					data: {cariIni:carian},
					cache: false,
					success: function(hasilCari)
					{
						$("#resDiv").html(hasilCari);
					}
				});
			}
			else
			{ $("#resDiv").html(''); }
		});

		$("#btnClearNama").click(function()
		{
			$("#resDiv").html('');
			$("#inputNama").val('');
			$("#inputNama").focus();
		});

		$("#aLegend").click(function()
		{ alert("Status warna\n▔▔▔▔▔▔▔\n● Abu-abu : belum login\n● Biru : di-logKey\n● Merah : ada error\n● Ungu : idle > 30 sec.\n● Hijau : lancar"); });

		$("#btnOTS").click(function()
		{
			$("#tblDaftarOTS input[type='text']").not("#iID1, #iID2").val('');
			$("#iPaket").val('1');
			$("#iGrup").val('1');
			$("#popup").css('display','block');
			//$("#popup").draggable();
			$("#iID1").focus();
		});

		$("#btnDaftarOTS").click(function()
		{
			nID1 = $("#iID1").val().length;
			nID2 = $("#iID2").val().length;
			ID1 = $("#iID1").val();
			ID2 = $("#iID2").val();
			dNama = $("#iNama").val();
			dKelas = $("#iKelas").val();
			dJur = $("#iJurusan").find(":selected").text();
			dPaket = $("#iPaket").val();
			dGrup = $("#iGrup").val();

			if (nID1<lbl1Len)
			{ alert('Cek '+lbl1); }
			else if (nID2<lbl2Len)
			{ alert('Cek '+lbl2); }
			else if (dNama=='')
			{ alert('Lengkapi nama'); }
			else if (dKelas=='')
			{ alert('Lengkapi kelas / sekolah'); }
			else if (dJur!='IPA' && dJur!='IPS' && dJur!='IPC')
			{ alert('Lengkapi jurusan'); }
			else if (dPaket=='' || dPaket<1)
			{ alert('Paket tes harus diisi'); }
			else if (dGrup=='' || dGrup<1)
			{ alert('Grup tes harus diisi'); }
			else
			{
				$.ajax({
					type: "post",
					url: "../admin/ajaxInsertNewPeserta.php",
					data: {pNISN:ID1, pNP:ID2, pNama:dNama, pKls:dKelas, pJur:dJur, pPaket:dPaket, pGrup:dGrup, prd1:'', prd2:'', prd3:''},
					async: false,
					cache: false,
					success: function(cekNo)
					{
						if (cekNo==0)
						{
							alert ("Pendaftaran Berhasil !!");

							$("#tblDaftarOTS input[type='text']").not("#iID1, #iID2, #iPaket, #iGrup").val('');
							//$("#popup").draggable();
							$("#iID1").focus();
						}
						else
						{
							if (cekNo==1)
							{
								alert(lbl1+' ada yang sama !!');
							}
							else if (cekNo==2)
							{
								alert(lbl2+' ada yang sama !!');
							}
							else if (cekNo==3)
							{
								alert(lbl1+' & '+lbl2+' ada yang sama !!');
							}
						}
					}
				});
			}
		});

		$("#btnClosePopup").click(function()
		{ $("#popup").css('display','none'); });

		$(document).on("click", ".itemNama", function()
		{
			noInd = $(this).attr('nI');
			noPes = $(this).attr('nP');

			$.ajax({
					type: "post",
					url: "logKeyIt.php",
					data: {logType:1, no1:noInd, no2:noPes},
					cache: false,
					success: function()
					{}
				});
		});

		$(document).on("dblclick", ".itemNama", function(e)
		{
			e.preventDefault();		//menghilangkan selection yg merupakan default dari event doubleclick thd suatu kata

			noInd = $(this).attr('nI');
			noPes = $(this).attr('nP');

			$.ajax({
					type: "post",
					url: "logKeyIt.php",
					data: {logType:2, no1:noInd, no2:noPes},
					cache: false,
					success: function()
					{}
				});
		});

	});

</script>

</body>

</html>