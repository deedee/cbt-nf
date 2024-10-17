<!DOCTYPE HTML>

<HEAD>

</HEAD>

<BODY style="background-color:#EAEAEA">

<center>

<?php
error_reporting(E_ERROR | E_PARSE);
require_once('../../koneksi_db.php');


function deleteDir($dirPath)
{
    if (! is_dir($dirPath))
    {
        if (substr($dirPath, strlen($dirPath)-9, 9) != 'index.php')
        { unlink($dirPath); }
    }

    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/')
    { $dirPath .= '/'; }

    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file)
    {
        if (is_dir($file))
        { deleteDir($file); }
        else
        { unlink($file); }
    }
    rmdir($dirPath);
}


$yesDel = $_POST['yesDelete'];


if ($yesDel=='delConfirm')
{
	//kosongin soal
	array_map('deleteDir', glob("../../images/soal/*"));
    mysqli_query($con,"TRUNCATE TABLE naskahsoal");
	mysqli_query($con,"TRUNCATE TABLE tabelhasil");

	echo '
			<div style="width:430px; height:120px; background-color:green; border:5px green solid; border-radius:9px; margin-top:70px">
		
				<br>
				<span style="font-size:30px; font-family:Tahoma; color:springgreen">
					Semua soal telah dihapus ! <br><br>
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
					Anda akan menghapus semua soal. <br>
					Lanjutkan ?
				</span>
				<br><br>
				<img src="../../images/icon-alert-small.png">
				<br><br>
				<form action="#" method="post" target="_parent">
					<input type="hidden" name="yesDelete" value="delConfirm">
					<input type="submit" value="Ya, hapus semua soal">
				</form> 

			</div>
		';
}
?>

</center>
</BODY>


</HTML>