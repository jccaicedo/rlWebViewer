<html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include('style.html');
ini_set('memory_limit', '-1');
$dbdir="/home/caicedo/data/rcnn/";
$file = $_GET["file"];
$format = $_GET["format"];
$category = $_GET["category"];
$TOP = 5;
$allcateg = explode(" ","aeroplane bicycle bird boat bottle bus car cat chair cow diningtable dog horse motorbike person pottedplant sheep sofa train tvmonitor"); 
echo "<center>| ";
$ncat = 0;
foreach($allcateg as $categ){
  echo "<a href='regionViewerSixth.php?category=".$categ."'>".$categ."</a> | ";
  if (++ $ncat % 10 == 0) echo "<br>";
}
echo "</center>";

$subDir = "regionSearchResults/sixth/".$category."_ranks/";
if($file == ""){
?>
<div class="content">
<center>
<h3>Top Inside-Box Detections</h3>
<iframe src="measures.php?cat=<?=$category?>&fparams=_inside_0.001_region_search_fifth_0.7_2&ov=&hidet=true&source=1" height="250px" width="100%"></iframe>
<h3>Top Inside-Segmentation-Masks Detections</h3>
<iframe src="measures.php?cat=<?=$category?>&fparams=_inside_0.001_region_search_sixth_0.7_2&ov=&hidet=true&source=2" height="250px" width="100%"></iframe>
<h3>Precision Recall Curves</h3>
<img src="prSegCurves/<?=$category?>.png">

<?php
  die();
  echo "<h3>Test Images: ".$category."</h3>";
  if($category != ""){
    $categoryNames = "lists/2007/test/".$category."_test_bboxes.txt";
    $list = explode("\n",file_get_contents($dbdir."/".$categoryNames));
    function getName($r){
      $d = explode(" ",$r);
      return $d[0];
    } 
  } else {
    $list = glob($dbdir.$subDir."/*.region_rank");
    function getName($r){
      $n = end( preg_split("/\//",$r) );
      return str_replace(".region_rank","",$n);
    }
  }
  $c = 0; $l = 0;
  $prev = '';
  foreach ($list as $item) {
    $n = getName($item);
    if($n != $prev){
      echo "<a href='regionViewerFour.php?file=".$subDir.$n.".region_rank&format=rank&category=".$category."'>";
      echo $n."</a> ";
      $l++;
      if($l % 10 == 0) echo "<br>\n";
    }
    $prev = $n;
    $c++; 
  }
  echo "<br><br>\nTotal images: ".$c;
  echo "</center></div>";
  die();
}

