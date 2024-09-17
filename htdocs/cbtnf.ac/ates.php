<?php
    
    $itemNum = "45";
    $itemNumTot = 0;

    $totalin = substr_count($itemNum, "+");
    $totalin++;
    if ($totalin > 0) {
        $numPerBS = explode("+", $itemNum);
        for($iu=0; $iu<$totalin; $iu++)
        { $itemNumTot += $numPerBS[$iu]; }
    }

    echo $itemNumTot;

?>