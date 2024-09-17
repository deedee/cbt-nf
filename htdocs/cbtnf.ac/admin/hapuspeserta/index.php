<!DOCTYPE HTML>

<HEAD>

</HEAD>

<BODY style="background-color:#EAEAEA">

<CENTER>

<?php
	require_once('../../koneksi_db.php');

	if (isset($_POST["submit"]))
	{
		//kosongin data peserta
		mysqli_query($con,"TRUNCATE TABLE absensiharitespeserta");
		mysqli_query($con,"TRUNCATE TABLE absensirecord");
		mysqli_query($con,"TRUNCATE TABLE aruntimer");
		mysqli_query($con,"TRUNCATE TABLE hasilipa");
		mysqli_query($con,"TRUNCATE TABLE hasilips");

		echo '
				<div style="width:430px; height:120px; background-color:green; border:5px green solid; border-radius:9px; margin-top:70px">
			
					<br>
					<span style="font-size:30px; font-family:Tahoma; color:springgreen">
						Semua peserta CBT telah dihapus ! <br><br>
					</span>
					<br><br>
					
					<br><br>

				</div>
			';
	}
	else
	{
		echo '
				<div style="width:430px; height:250px; background-color:white; border:5px red solid; border-radius:9px; margin-top:70px">
					
					<br>
					<span style="font-size:20px; font-family:Tahoma; color:red">
						Anda akan menghapus semua peserta.<br>
						Lanjutkan ?
					</span>
					<br><br>
					<img src="../../images/icon-deleteuser-small.png">
					<br><br>
					<form action="#" method="post" target="_parent">
						<input type="submit" name="submit" value="Ya, hapus semua peserta">
					</form> 

				</div>
			';
	}
?>

</CENTER>
</BODY>


</HTML>