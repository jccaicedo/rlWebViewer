<?php
ini_set('memory_limit', '-1');
$dbdir="/home/caicedo/data/rcnn/";
$category = $_GET["category"];
$model = $_GET["result"];

echo "<pre>";
$images = explode("\n",file_get_contents($dbdir."/results/".$model.".result.missed"));

  $total = count($images)-1;
  $data = array();
  for($i = 0; $i < $total; $i++){
    $r = explode(" ",$images[$i]);
    $data["name"][] = $r[0];
    $data["x1"][] = $r[1];
    $data["y1"][] = $r[2];
    $data["x2"][] = $r[3];
    $data["y2"][] = $r[4];
  }
$images = $data;

?>
<table border="1" width="100%">
  <tr>
    <th>
      Missed Examples :<?=count($images["name"])?>
    </th>
  </tr>
<?php
?>
  <tr>
    <td align="center"><br>
<?
  for($i = 0; $i < count($images["name"]); $i++){
   $imgName = $images["name"][$i];
   $b = array("x1"=>$images["x1"][$i],"y1"=>$images["y1"][$i],"x2"=>$images["x2"][$i],"y2"=>$images["y2"][$i]);//,"score"=>$images["score"][$i]);
   $box = $b["x1"].":".$b["y1"].":".$b["x2"].":".$b["y2"];
?>
      <a href="../lsodv2/edit/gtviewer.php?c=<?=$category?>&n=<?=$imgName?>.jpg&d=3&x1=<?=$b["x1"]?>&y1=<?=$b["y1"]?>&x2=<?=$b["x2"]?>&y2=<?=$b["y2"]?>&overlap=1.0" target="_blank">
      <img src="showDetection.php?n=<?=$imgName?>.jpg&b=<?=$box?>&d=1&c=<?=$category?>" style="" height="50" width="50">
      </a>
<? } ?>
    </td>
  </tr>
<? //} ?>
</table>

