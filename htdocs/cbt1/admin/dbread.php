<?php

	require_once('../koneksi_db.php');

	// Name of the file
	$gFile = $_POST['fsource'];
	$fileSource = '../backup/'.$gFile.'.sql';

	// Temporary variable, used to store current query
	$templine = '';

	// Read in entire file
	$lines = file($fileSource);

	// Loop through each line
	foreach ($lines as $line)
	{
		// Skip it if it's a comment
		if (substr($line, 0, 2) == '--' || $line == '')
		    continue;

		// Add this line to the current segment
		$templine .= $line;
		
		// If it has a semicolon at the end, it's the end of the query
		if (substr(trim($line), -1, 1) == ';')
		{
		    // Perform the query
		    mysqli_query($con,$templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysqli_error($con) . '<br /><br />');
		    // Reset temp variable to empty
		    $templine = '';
		}
	}

	echo $gFile;

?>