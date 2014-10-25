<?php
$dir='/home/caicedo/data/cnnPatches/results/';
echo "<pre>";

foreach (glob($dir."/*.out") as $filename) {
    $n = end( preg_split("/\//",$filename) );
    $category = strstr($n,'_',true);
    $params = str_replace(".out","",strstr($n,'_'));
    echo "<a href='main.php?cat=".$category."&fparams=".$params."'>View</a> ".$category." :: ".$params."\n";
}

?>
