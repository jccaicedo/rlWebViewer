<html>
<?php
include('style.html');
ini_set('memory_limit', '-1');
$dbdir="/home/caicedo/data/rcnn/";
$file = $_GET["file"];
$format = $_GET["format"];
$category = $_GET["category"];
$experiment = $_GET["exp"];
if($experiment == "") $experiment = "third";
$TOP = 5;
$allcateg = explode(" ","aeroplane bird car dog"); // diningtable dog horse motorbike person pottedplant sheep sofa train tvmonitor");
echo "<center>| ";
foreach($allcateg as $categ){
echo "<a href='regionViewer.php?category=".$categ."'>".$categ."</a> | ";
}
echo "</center>";

$subDir = "regionSearchResults/".$experiment."/".$category."_ranks/";
if($file == ""){
?>
<div class="content">
<h3>Region Classification</h3>
<b>Goal</b>: determine the relationship between an arbitrary window and a ground truth box.<br>
<b>Procedure</b>: Three relationships are defined: <i>BIG, TIGHT, INSIDE</i>. For each relationship a linear SVM classifier is trained using positive examples that satisfy the relationship.<br>
<b>Training Set</b>: To build the relationships data set, the Intersection-Over-Union and Overlap scores were employed. These are defined as follows:<br>
<center><img src="http://latex.codecogs.com/gif.latex?IoU%28A%2CB%29%20%3D%20%5Cfrac%7BA%5Ccap%20B%7D%7BA%5Ccup%20B%7D"> <font color="#FFF"> ..............</font>
<img src="http://latex.codecogs.com/gif.latex?Ov%28A%2CB%29%20%3D%20%5Cfrac%7BA%5Ccap%20B%7D%7BB%7D"></center><br>
The only difference between them is that the first is a symmetric function that measures the common area betwen two boxes, while the second is not symmetric and indicates the portion of B covered by A. With that, a
positive example for each spatial relationship is defined as:
<ul> 
  <li><b>Big</b>: if <img src="http://latex.codecogs.com/gif.latex?Ov%28win%2Cbox%29%20%3E%200.9"></li>
  <li><b>Tight</b>: if <img src="http://latex.codecogs.com/gif.latex?IoU%28win%2Cbox%29%20%5Cgeq%200.5"></li>
  <li><b>Inside</b>: if <img src="http://latex.codecogs.com/gif.latex?Ov%28box%2Cwin%29%20%3E%200.9"> and <img src="http://latex.codecogs.com/gif.latex?IoU%28win%2Cbox%29%20%3C%200.5"></i>
  <li><b>Background</b>: Otherwise.</li>
</ul>
An explicit classifier is not learned for background windows as these examples are implicitely rejected by the other three. 
The classifiers were trained on the PASCAL 2007 data set and evaluated on the test set. Qualitative results are available below. 
Click on one number in the list to view regions for a single image or change the category in the top of the page.
<center>
<?php
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
      echo "<a href='regionViewer.php?file=".$subDir.$n.".region_rank&format=rank&category=".$category."'>";
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

$list = explode("\n",file_get_contents($dbdir."/".$_GET["file"]));

$regions = array();
foreach($list as $key=>$region){
  $r = explode(" ",$region);
  $regions[$r[6]]['imgName'][] = $r[0];
  if($format == 'rank'){
    // Use precomputed rank
    $regions[$r[6]]['rank'][] = floatval($r[1]);
    //$regions['rank'][] = ($r[4]-$r[2])*($r[5]-$r[3]);
    $regions[$r[6]]['x1'][] = $r[2]; $regions[$r[6]]['y1'][] = $r[3];
    $regions[$r[6]]['x2'][] = $r[4]; $regions[$r[6]]['y2'][] = $r[5];
    $regions[$r[6]]['label'][] = floatval($r[1]) >= -0.5 ? 'green' : 'red';
  }else{
    // Compute and rank by area
    $regions['rank'][] = ($r[3]-$r[1])*($r[4]-$r[2]);
    $regions['x1'][] = $r[1]; $regions['y1'][] = $r[2];
    $regions['x2'][] = $r[3]; $regions['y2'][] = $r[4];
    $regions['label'][] = 'blue';
  }
}
?>
<h3>Regions For Test Image</h3>
<table width="100%">
  <tr>
   <td width="50%"><img src="../lsodv2/view/showImage.php?d=3&n=<?=$regions['big']['imgName'][0]?>.jpg" width="100%"></td>
   <td> 
     <ul>
      <li>The image in the left is the test image</li>
      <li>Below are 3 tables showing the top <?=$TOP?> regions in each category for this test image</li>
      <li>The three categories are: <ul> <li> <b>Big</b> regions <li><b>Tight</b> regions <li> <b>Inside</b> regions</ul></li>
      <li>Background regions are not shown</li>
      <li>Regions are framed in <font color="green">green</font> if the score is greater than 0</li>
      <li>Regions are framed in <font color="red">red</font> if the score is below zero</li>
      <li><i>Click on a region</i> to compare with ground truth boxes</li>
    </ul>
   </td>
  </tr>
</table>
<hr>
<?
$types = array("big","tight","inside");
foreach($types as $t){
  array_multisort($regions[$t]['rank'], SORT_DESC, $regions[$t]['imgName'], $regions[$t]['x1'], $regions[$t]['y1'], $regions[$t]['x2'], $regions[$t]['y2'],$regions[$t]['label']);
?>
<table border="1" width="100%">
  <tr>
    <th bgcolor='#ccc'>
      TOP <?=$TOP?> <?=strtoupper($t)?> REGIONS
    </th>
  </tr>
<?php
?>
  <tr>
    <td><center>
<?
  for($k = 0; $k < $TOP; $k++){
   $det = array('x1'=>$regions[$t]['x1'][$k], 'y1'=>$regions[$t]['y1'][$k], 'x2'=>$regions[$t]['x2'][$k], 'y2'=>$regions[$t]['y2'][$k], 'color'=>$regions[$t]['label'][$k], 'rank'=>$regions[$t]['rank'][$k]);
   $imgName = $regions[$t]['imgName'][$k];

   $box = $det['x1'].":".$det['y1'].":".$det['x2'].":".$det['y2'];
   $bw = $det['x2']-$det['x1'];
   $bh = $det['y2']-$det['y1'];
?>
      <a href="../lsodv2/edit/gtviewer.php?c=<?=$category?>&n=<?=$imgName?>.jpg&d=3&x1=<?=$det['x1']?>&y1=<?=$det['y1']?>&x2=<?=$det['x2']?>&y2=<?=$det['y2']?>&overlap=0.0" target="_blank">
      <div style="float:left; margin:.5em 10px .5em 0; overflow:hidden; position:relative; border:2px solid <?=$det['color']?>; width:<?=$bw?>px; height:<?=$bh?>px;">
        <img style="position:absolute; top:-<?=$det['y1']?>px; left:-<?=$det['x1']?>px;" src="../lsodv2/view/showImage.php?d=3&n=<?=$imgName?>.jpg" title="<?=$det['rank']?>">
      </div>
      </a>
<? } ?>
    </center></td>
  </tr>
</table>
<?
}
?>
