<?php
$dbdir = "/home/caicedo/data/PascalVOC/filtering/predictions/dog/";
if($_GET["kernel"] == ""){
  $contents = file_get_contents($dbdir."dog_linear.txt");
  $kernel = "";
}else{
  $contents = file_get_contents($dbdir."dog_bhattacharyya.txt");
  $kernel = "b";
}
echo "<h3>VALIDATION EXAMPLES</h3>";
$data = explode("\n",$contents);
$images = array();
$scores = array();
$labels = array();
foreach($data as $k=>$v){
  $parts = explode(" ",$v);
  $images[] = $parts[0];
  $scores[] = $parts[1];
  $labels[] = $parts[2];
}
if(!isset($_GET["o"])) $o = "top";
else $o = $_GET["o"];
if($o == "top"){
  array_multisort($scores,SORT_DESC,$images,$labels);
}else{
  array_multisort($scores,SORT_ASC,$images,$labels);
}
$c = 0;
for($i = 0; $i < count($images); $i++){
  if($labels[$i] > 0){
    echo "<img src='showDetection.php?n=".$images[$i]."' title='".$scores[$i]."'>";
    $c++;
    if($c % 20 == 0) echo "<br> ";
  }

}
?>
