<?php

    //Lacal media yg tersimpan dlm folder admedia
    $listMedia = "";
    $mediake_n = 0;
    foreach (glob("../admedia/*.*") as $mediaFile) {
    	$mediake_n++;
        $mediaFileName = $mediake_n.". ".substr($mediaFile,11).";   ";
        $listMedia .= $mediaFileName;
    }

    echo $listMedia;

?>