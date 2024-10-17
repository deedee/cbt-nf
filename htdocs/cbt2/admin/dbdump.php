<?php

	$timeS = $_POST['tS'];

	backup_tables($timeS,'*');		//variabel2 ini dapetnya dari file koneksi_db.php yg direquire
	backup_tables($timeS,'absensiharitespeserta,aruntimer');

	//------------------------------------------------------------------------------

	$backupDir = "../backup/";
	$shortFileName = "";

	$files = array();

	foreach (glob($backupDir."*.*") as $filename)
	{
	    $shortFileName = substr($filename,10,34);
	    array_push($files,$shortFileName);
	}

	rsort($files);

	$nlength = count($files);
	for($x = 0; $x < $nlength; $x++)
	{
	    echo '<a href="#" class="btnRestoreDB" bname="'.$files[$x].'" oncontextmenu="return false;" style="font-size:15px;color:darkblue;text-decoration: none" title="klik ganda merestore, klik kanan menghapus">'.$files[$x].'</a><br>';
	}

	//------------------------------------------------------------------------------
	//------------------------------------------------------------------------------

	// fungsi backup database
	function backup_tables($stamp,$tables)
	{
		$dbhost = "db";
	    $dbuser = "nfcbt";
	    $dbpass = "adminnfcbt";
	    $dbname = "dbcbtnfac";
	    
	    $con = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);

	    // Check connection
	    if (mysqli_connect_errno())
	    {
	        echo "Failed to connect to MySQL: " . mysqli_connect_error();
	    }

		//get all of the tables
		if($tables == '*')
		{
			$backupname = 'full_'.$name;
			$tables = array();
			$result = mysqli_query($con,'SHOW TABLES');
			while($row = mysqli_fetch_row($result))
			{
				$tables[] = $row[0];
			}
		}
		else
		{
			$backupname = 'part_'.$name;
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
		
		//cycle through
		foreach($tables as $table)
		{
			$result = mysqli_query($con,'SELECT * FROM '.$table);
			$num_fields = mysqli_num_fields($result);
			
			$return.= 'DROP TABLE '.$table.';';
			$row2 = mysqli_fetch_row(mysqli_query($con,'SHOW CREATE TABLE '.$table));
			$return.= "\n\n".$row2[1].";\n\n";
			
			for ($i = 0; $i < $num_fields; $i++) 
			{
				while($row = mysqli_fetch_row($result))
				{
					$return.= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j < $num_fields; $j++) 
					{
						$row[$j] = addslashes($row[$j]);
						$row[$j] = ereg_replace("\n","\\n",$row[$j]);
						if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
						if ($j < ($num_fields-1)) { $return.= ','; }
					}
					$return.= ");\n";
				}
			}
			$return.="\n\n\n";
		}
		
		//save file
		$handle = fopen('../backup/'.$backupname.$stamp.'.sql','w+');
		fwrite($handle,$return);
		fclose($handle);
	}

?>