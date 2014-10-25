<?php
if(isset($_GET["feature"])){
  $feature = $_GET["feature"];
}else{
  $feature = "all";
}
if(isset($_GET["start"])){
  $start = $_GET["start"];
  if($start < 0) $start = 0;
}else{
  $start = 0;
}
if(isset($_GET["exp"])){
  $experiment = $_GET["exp"];
}else{
  $experiment = "2px3py";
}

$file = "/home/caicedo/software/ImageStreams/data/filtering/results/".$experiment."/".$feature."_ranking.txt";
if(file_exists($file)){
  $fileContent = explode("\n",file_get_contents($file));
  $names = array();
  $area = array();
  $entropy = array();
  foreach($fileContent as $rec){
    $d = explode(" ",$rec);
    $names[] = $d[0];
    $scores[] = $d[1];
    $labels[] = $d[2];
  }
  array_multisort($scores, SORT_DESC, $names, $labels);

  $title = strtoupper($feature);

  $c = $start;
  $end = $c+2000;
  $content = "<tr><td>$c</td>";
  for($i=$start;$i<$end && $i < count($names) ;$i++){
    if($labels[$i] == 1) $color = "blue";
    else $color = "red";
      $content .= "<td bgcolor='$color'><img src='showDetection.php?d=4&n=".$names[$i]."'></td>";
      $c ++;
      if($c % 20 == 0) $content .= "</tr><tr><td>$c</td> ";
  }
}else{
  $content = "<tr><td>No available for this experiment</td></tr>";
}

?>
<h3>RANKING BY <?=$title?> (From top-left to bottom-right)</h3>
Blue frames are true positives. Red frames are false positives<br>
<a href='ranking.php?exp=<?=$experiment?>&feature=all'>All</a> || 
<a href='ranking.php?exp=<?=$experiment?>&feature=rgbhist'>RGBHist</a> ||
<a href='ranking.php?exp=<?=$experiment?>&feature=colorsift'>ColorSIFT SPM</a> ||
<a href='ranking.php?exp=<?=$experiment?>&feature=hog'>HoG SPM</a> ||
<a href='ranking.php?exp=<?=$experiment?>&feature=siftspm'>Regular SIFT SPM</a> <br>
<a href='ranking.php?exp=<?=$experiment?>&feature=<?=$feature?>&start=<?=($i)?>'> NEXT >> </a><br>
<table>
<?=$content?>
</table>
<br><a href='ranking.php?exp=<?=$experiment?>&feature=<?=$feature?>&start=<?=($i)?>'> NEXT >> </a>


