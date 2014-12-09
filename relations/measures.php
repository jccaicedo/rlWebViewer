<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('memory_limit', '-1');
include_once("config.php");
$sourceDirs = array('data/rcnnResult/');
if(!isset($_GET["source"])) {
  $dbdir = $sourceDirs[0];
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
$recall = safeValue($_GET["recall"],0);
$row = safeValue($_GET["row"],0);
$col = safeValue($_GET["col"],0);
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
<?php if(!isset($_GET["hidet"])){ ?>
  <tr>
    <th>Model</th>
    <th>Prec</th>
    <th>Recall</th>
    <th>True Pos.</th>
    <th>False Pos.</th>
    <th>
      <a href="measures.php?cat=<?php echo $category?>&fparams=<?php echo $fparams?>&recall=<?php echo $prev?>">&#60;&#60; Prev</a> 
      <font color="white"> ------------------ </font>
      Detections Around this Point
      <font color="white"> ------------------ </font>
      <a href="measures.php?cat=<?php echo $category?>&fparams=<?php echo $fparams?>&recall=<?php echo $next?>">Next &#62;&#62;</a>
    </th>
  </tr>
<?php
}
foreach($results as $model=>$values){
?>
  <tr bgcolor="<?php echo $colors[$model]?>">
<?php if(!isset($_GET["hidet"])){ ?>
    <td align="center"><?php echo $titles[$model]?></td>
    <td align="center"><?php echo number_format($results[$model]["pr"][1],4)?></td>
    <td align="center"><?php echo number_format($results[$model]["pr"][0],4)?></td>
    <td align="center"><?php echo number_format($results[$model]["tp"])?></td>
    <td align="center"><?php echo number_format($results[$model]["fp"])?></td>
<?php }?>
    <td align="center">
<?php
  $i = $results[$model]["stop"];
  if($i < $maxThumbs) $i = $maxThumbs;
  for($k=$maxThumbs;$k>0 ;$k--){ 
   $det = explode(" ",$log[$model][$i-$k]);
   $imgName = $det[0];
   $box = $det[1].":".$det[2].":".$det[3].":".$det[4];
?>
      <a href="../lsodv2/edit/gtviewer.php?c=<?php echo $category?>&n=<?php echo $det[0]?>.jpg&d=3&x1=<?php echo $det[1]?>&y1=<?php echo $det[2]?>&x2=<?php echo $det[3]?>&y2=<?php echo $det[4]?>&overlap=<?php echo number_format($det[5],3)?>" target="_blank">
      <img src="showDetection.php?n=<?php echo $imgName?>.jpg&b=<?php echo $box?>&d=1&c=<?php echo $category?>" style="<?php echo getStyle($det)?>" title="overlap:<?php echo number_format($det[6],3)?>">
      </a>
<?php } ?>
    </td>
  </tr>
<?php } ?>
</table>

