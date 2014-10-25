<?php
ini_set('memory_limit', '-1');
$sourceDirs = array('/home/caicedo/data/cnnPatches/results/','/home/caicedo/data/rcnn/regionSearchResults/fifth/',
                    '/home/caicedo/data/rcnn/regionSearchResults/sixth/','/home/caicedo/data/rcnn/regionSearchResults/fifth/additionalEval/');
if(!isset($_GET["source"])) {
  $dbdir="/home/caicedo/data/cnnPatches/results/";
} else {
  $dbdir = $sourceDirs[$_GET["source"]];
}
$fparams = $_GET["fparams"];
$category = $_GET["cat"];
$evalOv = $_GET["ov"];
$log = array();
$curve = array();
$log["system"] = explode("\n",file_get_contents($dbdir.$category.$fparams.".out.".$evalOv."result.log"));
$curve["system"] = explode("\n",file_get_contents($dbdir.$category.$fparams.".out.".$evalOv."result"));
echo "<pre>";
//print_r($log);
$colors = array("system"=>"#3399FF","filter"=>"#FF9999","combined"=>"#FFCC00");
$titles = array("system"=>"RCNN","filter"=>"Second Stage Classifier","combined"=>"Combined");

function getStyle($det){
  if($det[7] == "1")
    return "border:3px solid black";
  else
    return "border:3px dotted black";
}

$maxThumbs = 100;
$recall = $_GET["recall"];
$row = $_GET["row"];
$col = $_GET["col"];
$results = array();
foreach($curve as $model=>$values){
  $results[$model] = array("tp"=>0,"fp"=>0,"pr"=>0);

  for($i=0; $i<count($values); $i++){
    $ld = explode(" ",$log[$model][$i]);
    if($ld[7] == "1")
      $results[$model]["tp"] += 1;
    else
      $results[$model]["fp"] += 1;
    $results[$model]["pr"] = explode(" ",$values[$i]);
    if($results[$model]["pr"][0] >= $recall) {
      break;
    }
  }
  $results[$model]["stop"] = $i;
  $results[$model]["img"][] = $ld[0];
  if($i+$maxThumbs < count($values)){
    $next = $values[$i+$maxThumbs];
  }else{
    $next = $values[count($values)-1];
  }
  if($i-$maxThumbs >= 0){
    $prev = $values[$i-$maxThumbs];
  }else{
    $prev = $values[0];
  }
}
$next = explode(" ",$next);
$next = number_format($next[0],4);
$prev = explode(" ",$prev);
$prev = number_format($prev[0],4);

?>
<table border="1" width="100%">
<? if(!isset($_GET["hidet"])){ ?>
  <tr>
    <th>Model</th>
    <th>Prec</th>
    <th>Recall</th>
    <th>True Pos.</th>
    <th>False Pos.</th>
    <th>
      <a href="measures.php?cat=<?=$category?>&fparams=<?=$fparams?>&recall=<?=$prev?>">&#60;&#60; Prev</a> 
      <font color="white"> ------------------ </font>
      Detections Around this Point
      <font color="white"> ------------------ </font>
      <a href="measures.php?cat=<?=$category?>&fparams=<?=$fparams?>&recall=<?=$next?>">Next &#62;&#62;</a>
    </th>
  </tr>
<?php
}
foreach($results as $model=>$values){
?>
  <tr bgcolor="<?=$colors[$model]?>">
<? if(!isset($_GET["hidet"])){ ?>
    <td align="center"><?=$titles[$model]?></td>
    <td align="center"><?=number_format($results[$model]["pr"][1],4)?></td>
    <td align="center"><?=number_format($results[$model]["pr"][0],4)?></td>
    <td align="center"><?=number_format($results[$model]["tp"])?></td>
    <td align="center"><?=number_format($results[$model]["fp"])?></td>
<?}?>
    <td align="center">
<?
  $i = $results[$model]["stop"];
  if($i < $maxThumbs) $i = $maxThumbs;
  for($k=$maxThumbs;$k>0 ;$k--){ 
   $det = explode(" ",$log[$model][$i-$k]);
   $imgName = $det[0];
   $box = $det[1].":".$det[2].":".$det[3].":".$det[4];
?>
      <a href="../lsodv2/edit/gtviewer.php?c=<?=$category?>&n=<?=$det[0]?>.jpg&d=3&x1=<?=$det[1]?>&y1=<?=$det[2]?>&x2=<?=$det[3]?>&y2=<?=$det[4]?>&overlap=<?=number_format($det[5],3)?>" target="_blank">
      <img src="showDetection.php?n=<?=$imgName?>.jpg&b=<?=$box?>&d=1&c=<?=$category?>" style="<?=getStyle($det)?>" title="overlap:<?=number_format($det[6],3)?>">
      </a>
<? } ?>
    </td>
  </tr>
<? } ?>
</table>

