<?php

    $backupDir = "../backup/";
    $shortFileName = "";

    $files = array();

    foreach (glob($backupDir."*.*") as $filename) {
        $shortFileName = substr($filename,10,34);
        array_push($files,$shortFileName);
    }

    rsort($files);

    $nlength = count($files);

    for($x = 0; $x < $nlength; $x++) {
        echo '<a href="#" class="btnRestoreDB" bname="'.$files[$x].'" oncontextmenu="return false;" style="font-size:15px;color:darkblue;text-decoration: none" title="klik ganda merestore, klik kanan menghapus">'.$files[$x].'</a><br>';
    }

?>