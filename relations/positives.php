<?php
if(isset($_GET["label"])){
  $label = $_GET["label"];
}else{
  $label = "1";
  $title = "POSITIVE";
}
if(isset($_GET["start"])){
  $start = $_GET["start"];
  if($start < 0) $start = 0;
}else{
  $start = 0;
}

//$sizeFile = "/home/caicedo/data/PascalVOC/filtering/bboxes/train/dog_entropy.txt";
$sizeFile = "/home/caicedo/software/ImageStreams/data/filtering/labels/dog_training_entropy.txt";
$fileContent = explode("\n",file_get_contents($sizeFile));
$names = array();
$area = array();
$entropy = array();
foreach($fileContent as $rec){
  $d = explode(" ",$rec);
  $names[] = $d[0];
  $area[] = $d[2]*$d[3]*$d[1];
  $entropy[] = $d[1];
}
array_multisort($area, SORT_ASC, $names);

//$file = "/home/caicedo/data/PascalVOC/filtering/labels/filtered_dog_train_labels.txt";
$file = "/home/caicedo/software/ImageStreams/data/filtering/labels/dog_training_labels.txt";
$examples = explode("\n",file_get_contents($file));
if($stop > count($examples)) $stop = count($examples);
$labels = array();
foreach($examples as $v){
  $rec = explode(" ",$v);
  $labels[$rec[0]] = $rec[1];
}

if($label == "1") $title = "POSITIVE";
else $title = "NEGATIVE";

$c = 0;
$content = $c." ";
for($i=$start;$c<2000 && $i < count($names) ;$i++){
  if($labels[$names[$i]] == $label){
    //$content .= "<img src='showDetection.php?d=1&n=".$names[$i]."'> ";
    $content .= "<img src='showDetection.php?d=4&n=".$names[$i]."'> ";
    $c ++;
    //echo $names[$i]." ".$labels[$names[$i]]."<br>";
    if($c % 20 == 0) $content .= "<br>".$c." ";
  }
}

?>
<h3><?=$title?> TRAINING EXAMPLES ORDER BY ENTROPY (From low to high)</h3>
<a href='positives.php?label=1'>Positives</a> || 
<a href='positives.php?label=-1'>Negatives</a><hr>
<a href='positives.php?label=<?=$label?>&start=<?=($i)?>'> NEXT >> </a><br>
<?=$content?>
<br><a href='positives.php?label=<?=$label?>&start=<?=($i)?>'> NEXT >> </a>


