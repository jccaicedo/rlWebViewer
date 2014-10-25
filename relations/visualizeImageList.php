<?php
ini_set('memory_limit', '-1');
$dbdir="/home/caicedo/data/rcnn/";
$file = $_GET["file"];
$start = $_GET["start"];
$category = $_GET["category"];
$list = explode("\n",file_get_contents($dbdir."/".$_GET["file"]));
echo "<pre>";

if($start == "") $start = 0;
$maxThumbs = 1000;
$next = $start+$maxThumbs;
$prev = $start-$maxThumbs;
if($next > count($list)) $next = count($list)-$maxThumbs;
if($prev < 0) $prev = 0;
?>
<table border="1" width="100%">
  <tr>
    <th>
      <a href="visualizeImageList.php?cat=<?=$category?>&file=<?=$file?>&start=<?=$prev?>">&#60;&#60; Prev</a> 
      <font color="white"> ------------------ </font>
      Images
      <font color="white"> ------------------ </font>
      <a href="visualizeImageList.php?cat=<?=$category?>&file=<?=$file?>&start=<?=$next?>">Next &#62;&#62;</a>
    </th>
  </tr>
<?php
?>
  <tr>
    <td align="center">
<?
  for($k=0;$k<$maxThumbs;$k++){ 
   $det = explode(" ",$list[$k+$start]);
   $imgName = $det[0];
   $box = $det[1].":".$det[2].":".$det[3].":".$det[4];
?>
      <a href="../lsodv2/edit/gtviewer.php?c=<?=$category?>&n=<?=$det[0]?>.jpg&d=3&x1=<?=$det[1]?>&y1=<?=$det[2]?>&x2=<?=$det[3]?>&y2=<?=$det[4]?>&overlap=0.0" target="_blank">
      <img src="showDetection.php?n=<?=$imgName?>.jpg&b=<?=$box?>&d=1&c=<?=$category?>" title="">
      </a>
<? } ?>
    </td>
  </tr>
  <tr>
    <th>
      <a href="visualizeImageList.php?cat=<?=$category?>&file=<?=$file?>&start=<?=$prev?>">&#60;&#60; Prev</a> 
      <font color="white"> ------------------ </font>
      Images
      <font color="white"> ------------------ </font>
      <a href="visualizeImageList.php?cat=<?=$category?>&file=<?=$file?>&start=<?=$next?>">Next &#62;&#62;</a>
    </th>
  </tr>
</table>

