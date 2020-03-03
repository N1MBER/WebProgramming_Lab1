<?php
    $R = array();
    for($i = 1 ; $i <=5 ; $i++){
        if (isset($_GET['r'.$i])){
            array_push($R,$_GET['r'.$i]);
        }
    }
    foreach ($R as $jj){
        echo $jj ."<br>";
    }
?>