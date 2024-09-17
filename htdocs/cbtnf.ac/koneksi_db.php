<?php
    
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

    //=========================================================================================================================================
    
    $titleBarLoginPage = "NF-CBT Login";		//tampilan di bag title bar browser, pd halaman login
    $titleBarTestPage = "NF-CBT Test";			//tampilan di bag title bar browser pd halaman index testpage
    $titleBarProcsxPage = "NF-CBT Result";	    //tampilan di bag title bar browser pd halaman hasil akhir setelah siswa mengklik Finish

?>