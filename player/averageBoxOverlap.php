<?php
include("../config.php");
include("../dbinter.php");
include("amtUtils.php");

$gt = loadGroundTruthData();
$data = getAssignmentAnnotations('boxes','TRAINING_SESSION');
//ds($data);
//ds($gt);

$cumulatedIoU = 0;
$totalBoxes = 0;
for($i = 0; $i < count($data); $i++) {
  $key = $data[$i]["imageID"]."_".$data[$i]["entity"];
  $boxA = array($data[$i]["x1"], $data[$i]["y1"], $data[$i]["x2"], $data[$i]["y2"]);
  $max = 0;
  if(is_array($gt[$key])) {
    foreach($gt[$key] as $boxB) {
      $iou = match($boxA, $boxB);
      if($iou > $max) {
        $max = $iou;
      }
    }
  }
  $cumulatedIoU += $max;
  $totalBoxes ++;
}

echo "<center>";
echo "<h3> AVERAGE IoU: ".number_format($cumulatedIoU/$totalBoxes,4)."</h3>";
echo "<h3> TOTAL TRAINING BOXES DRAWN: ".$totalBoxes."</h3>";
echo "</center>";

?>
