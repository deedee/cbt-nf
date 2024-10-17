<?php

	session_start();
	require_once('koneksi_db.php');

	date_default_timezone_set('Asia/Jakarta'); 		//set timezone
	$DateTime = date('Y, m, d, H, i, s, 0');

	$noInd = $noPes = '';
	if (isset($_SESSION['currentNIN'])) { $noInd = $_SESSION['currentNIN']; }
	if (isset($_SESSION['currentNOP'])) { $noPes = $_SESSION['currentNOP']; }

	mysqli_query($con,"UPDATE absensiharitespeserta SET loginError=1 WHERE nomorPeserta='$noPes' LIMIT 1");

	//ambil data admin dari tabel dataadmin
	$cekDataAdmin = mysqli_query($con,"SELECT * FROM dataadmin WHERE id = '1'");
	$hasilDataAdmin = mysqli_fetch_array($cekDataAdmin);
	$adminName = $hasilDataAdmin['nama'];
	$adminNo = $hasilDataAdmin['nokontak'];
	$linkThisGrup = $hasilDataAdmin['grupwa'];;

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="TO Online CBT BKB Nurul Fikri">
	<meta name="keywords" content="BKB, NF, nurul fikri, nurul, fikri, bimbel, islami, pendidikan, sekolah, cara cepat, to, to online, cbt">
	<link rel="shortcut icon" href="images/nf-favico.ico" type="image/x-icon">
	<script type="text/javascript" src="jquery-1.7.2.min.js"></script>
	
	<title>Access Error</title>

	<style media="screen" type="text/css">
		<!--
		body {
			margin: 0;
	        padding: 0;
	        border: 0;			/* This removes the border around the viewport in old versions of IE */
	        width: 100%;
	        min-width: 210px;   /* Minimum width of layout - remove line if not required */
								/* The min-width property does not work in old versions of Internet Explorer */
	    }
		
		#contain {
			width: 100%; height: 100%;
			margin-right: auto;
			margin-left: auto;
			margin-top: 0px;
			overflow: hidden;
		}
		
		#mainContainer {
			background-color: #fff;
		}	

		#linkBar {
			background-color: #EE2B2E;
			width: 100%;
			height: 40px;
			vertical-align: middle;
			padding-top: 10px;
		}
		
		.stickyTop {
			position: fixed;
			width: 100%;
			left: 0;
			top: 0;
		}
		
		#leftSide {
			padding: 0px 0px 0px 12px;
			min-width: 210px;
			width: 100%;
			height: 100%;
			float: left;
			margin-top: 50px;
		}

		-->
	</style>
	
</head>

<body>
<div id="contain">
		
		<div id="linkBar" class="stickyTop" style="font-family:Tahoma; color:white;" >	</div>

		<div id="mainContainer">

	  	  <audio id="errSound" autoplay>
				<source src="admin/LogErrAlert.ogg" type="audio/ogg">
				Your browser does not support the audio element.
		  </audio>

				<center> 
					<br><br><br>

					<div style="width: 100%; height: 490px; overflow: auto;">
						<br><br>
						<img style="max-width:100%; height:auto" src="images/redcircle_login_error.png" alt="ACCESS ERROR">
						<br><br>
						<span style="font-size:40px; font-family:'Courier'; color:#EE2B2E;" ><b>Access Error</b></span>
						<br><br><br><br><br><br><br><br>
						<form id="errorTrapper" action="#" method="post" target="_parent">
							<input type="image" id="open_slot" src="images/open_slot_off.png" alt="relogin">
						</form>
						<br><br>
						<span id="textRelogin" style="font-size:22px; font-family:'Arial'; color:green; display: none">Klik icon di atas<br>untuk login kembali</span>
					</div>
				</center>

				<!-- text helper, di pojok kanan bawah -->
				<div id="txtAdmin" style="right:10px; bottom:10px; position:absolute; font-family:'Tahoma'; font-size:15px; border-radius: 4px;
						 padding: 3px 6px 3px 6px; background-color:#EEEEEE; border:2px solid #D0D0D0;">
						 <?php
						 	if($adminNo!='')
						 	{
						 		echo "Ada kendala ? hubungi $adminName di <span style='color:blue'>$adminNo</span><br>";
						 	}
							if($linkThisGrup!='')
							{
								echo "Klik <a href='$linkThisGrup' style='color:green; text-decoration:none' target='_blank'>link ini untuk join grup WA admin server</a>";
							}
						 ?>
				</div>
    
      </div>
    
</div>

<script type="text/javascript">

		$(window).load(function() {
			
			var ni1 = '<?php echo $noInd; ?>';
			var np2 = '<?php echo $noPes; ?>';

			$(function() {
				var ctimer = setInterval(cekKeyLog, 3000);
				function cekKeyLog()
				{
					//memeriksa apakah admin sudah melogKey
					$.ajax({
						type: "post",
						url: "ajaxCekKeyLog.php",
						data: {n1:ni1, n2:np2},
						cache: false,
						success: function(responKey){
							if (responKey=='oklogkey')
							{
								$("#open_slot").attr("src", "images/open_slot_on.png");
								$("#textRelogin").css('display', 'block');
								$("#dni").val(ni1);
								$("#dnp").val(np2);
								$("#errorTrapper").attr('action', 'index.php');

								$(document).prop('title', 'NF-CBT Re-login');
							}
						}
					});
				}
			});
		});

	</script>
</body>
</html>