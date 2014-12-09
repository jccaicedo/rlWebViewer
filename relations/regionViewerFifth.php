<html>
<?php
include('style.html');
ini_set('memory_limit', '-1');
$file = $_GET["file"];
$format = $_GET["format"];
$category = $_GET["category"];
$experiment = $_GET["exp"];
if($experiment == "") $experiment = "fifth";
$TOP = 5;
$allcateg = explode(" ","aeroplane bicycle bird boat bottle bus car cat chair cow diningtable dog horse motorbike person pottedplant sheep sofa train tvmonitor"); 
echo "<center>| ";
$ncat = 0;
foreach($allcateg as $categ){
  echo "<a href='regionViewerFifth.php?category=".$categ."'>".$categ."</a> | ";
  if (++ $ncat % 10 == 0) echo "<br>";
}
echo "</center>";

$subDir = "regionSearchResults/".$experiment."/".$category."_ranks/";
if($file == ""){
?>
<div class="content">
<center>
<h3>Top Big Detections</h3
/aeroplane_big_det.out.result>
<iframe src="measures.php?cat=<?php echo $category?>&fparams=_big_det&ov=&hidet=true&source=0" height="250px" width="100%"></iframe>
<h3>Top Tight Detections</h3>
<iframe src="measures.php?cat=<?php echo $category?>&fparams=_tight_det&ov=&hidet=true&source=0" height="250px" width="100%"></iframe>
<h3>Top Inside Detections</h3>
<iframe src="measures.php?cat=<?php echo $category?>&fparams=_inside_det&ov=&hidet=true&source=0" height="250px" width="100%"></iframe>
<h3>Precision Recall Curves</h3>
<img src="prCurves_seventh/<?php echo $category?>.png">

<?php } ?>
