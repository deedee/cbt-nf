<?php

	$prosesFlag = $_POST['prosesF'];
	$oldImg = $_POST['fotoDir'];
	$newImg = $_POST['fotoDirTmp'];
	
	if ($prosesFlag=='1')
	{
		copy($newImg, $oldImg);
		
		$files = glob("../images/tmpSoalDir/*"); //get all file names
		foreach($files as $file)
		{
			if(is_file($file))
			unlink($file); //delete file
		}
		$teks = "<h2>Selesai input image soal !!</h2>";
	}
	else
	{
		//image Dir
		$toDir = $_POST['kodeFolder'];
		
		if ($_FILES['uploadItem']['name'] && $toDir != 0)
		{
			$lokFoto = $_FILES['uploadItem']['tmp_name'];
			$namaFoto = $_FILES['uploadItem']['name'];
			
			$dirFoto = "../images/soal/$toDir/$namaFoto";
			$dirFotoTmp = "../images/tmpSoalDir/$namaFoto";
			
			move_uploaded_file($lokFoto,$dirFotoTmp);
		}
		else
		{
			exit;
		}
	}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

    <head>

    </head>
    
    <body>
    	<?php
        	if ($teks!='')
			{
				echo $teks."<br><br>";
			}
		?>
        <h2>Image soal lama :</h2>
    	<?php echo ('<img src="'.$dirFoto.'" alt="Exist Item">'); ?>
        
    	<h2>Image soal baru :</h2>
    	<?php echo ('<img src="'.$dirFotoTmp.'" alt="Uploaded Item">'); ?>
        
        <form name="selfForm" id="selfForm" method="post" enctype="multipart/form-data">
			<input name="fotoDir" type="hidden" value="<?php echo $dirFoto; ?>">
            <input name="fotoDirTmp" type="hidden" value="<?php echo $dirFotoTmp; ?>">
            <input name="prosesF" type="hidden" value="1">
            <input type="submit" value="&nbsp; Upload Image Soal&nbsp; ">
		</form>
    </body>

</html>