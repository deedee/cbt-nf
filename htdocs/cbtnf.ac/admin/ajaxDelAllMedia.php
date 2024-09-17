<?php
	
	// get all file names
	$files = glob('../admedia/*');

	//delete all files
	foreach($files as $file)
	{ 
	  if(is_file($file))
	  unlink($file); // delete file
	}

?>