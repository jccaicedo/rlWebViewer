<?php
ini_set('memory_limit', '-1');
$dbdir="/home/caicedo/data/rcnn/";
$features = "GPUCategoriesPascal07";
$category = $_GET["category"];
$model = $_GET["model"];

echo "<pre>";
$images = explode("\n",file_get_contents($dbdir.$features."/".$category."0.idx"));
$labels = explode("\n",file_get_contents($dbdir."/models/".$model));

if (count($images) == count($labels)){
  $total = count($images)-1;
  $data = array();
  for($i = 0; $i < $total; $i++){
    $s = explode(" ",$labels[$i]);
    $r = explode(" ",$images[$i]);
    $data[$s[0]]["name"][] = $r[0];
    $data[$s[0]]["x1"][] = $r[1];
    $data[$s[0]]["y1"][] = $r[2];
    $data[$s[0]]["x2"][] = $r[3];
    $data[$s[0]]["y2"][] = $r[4];
    $data[$s[0]]["score"][] = $s[1];
  }
}else{
die();
}

?>
<table border="1" width="100%">
  <tr>
    <th>Cluster</th>
    <th>
      Positive Examples
    </th>
  </tr>
<?php
foreach($data as $cluster=>$images){
  array_multisort($images["score"],SORT_DESC,$images["name"],$images["x1"],$images["y1"],$images["x2"],$images["y2"]);

?>
  <tr>
    <td align="center">Cluster:<?=$cluster?> <br> Examples:<?=count($images["name"])?></td>
    <td align="center"><br>
<?
  for($i = 0; $i < count($images["name"]); $i++){
   $imgName = $images["name"][$i];
   $b = array("x1"=>$images["x1"][$i],"y1"=>$images["y1"][$i],"x2"=>$images["x2"][$i],"y2"=>$images["y2"][$i],"score"=>$images["score"][$i]);
   $box = $b["x1"].":".$b["y1"].":".$b["x2"].":".$b["y2"];
?>
      <a href="../lsodv2/edit/gtviewer.php?c=<?=$category?>&n=<?=$imgName?>.jpg&d=3&x1=<?=$b["x1"]?>&y1=<?=$b["y1"]?>&x2=<?=$b["x2"]?>&y2=<?=$b["y2"]?>&overlap=1.0" target="_blank">
      <img src="showDetection.php?n=<?=$imgName?>.jpg&b=<?=$box?>&d=1&c=<?=$category?>" style="" height="50" width="50" title="<?=$b["score"]?>"> <!--height="50" width="50"  -->
      </a>
<? } ?>
    </td>
  </tr>
<? } ?>
</table>

