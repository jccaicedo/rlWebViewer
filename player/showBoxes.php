<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<script src="utilityFunctions.js"></script>';
echo '<script src="drawingFunctions.js"></script>';
echo '<script src="BoxCompare.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="annotations.css">';
if(isset($_GET["img"])) {
  $img = $_GET["img"];
} else {
  $img = "000900";//"004500";
}

class Box {
  var $a;
  var $b;
  function __construct($b){
    $this->b = array($b[0]+0,$b[1]+0,$b[2]-$b[0],$b[3]-$b[1]);
    $this->a = ($b[2]-$b[0])*($b[3]-$b[1]);
  } 
  function area(){
    return $this->a;
  }
  function x(){
    return $this->b[0];
  }
  function y(){
    return $this->b[1];
  }
}

function compareArea($u, $v){
  return $u->area() == $v->area() ? 0 : ($u->area() > $v->area()) ? -1 : 1;
}

function compareX($u, $v){
  return $u->x() == $v->x() ? 0 : ($u->x() > $v->x()) ? 1 : -1;
}

function compareY($u, $v){
  return $u->y() == $v->y() ? 0 : ($u->y() > $v->y()) ? 1 : -1;
}

function loadEnvironment($img, $S, $H, $V) {  
  $contents = file_get_contents("data/some/".$img.".idx");
  $contents = explode("\n",$contents);
  $boxes = array(); 
  foreach($contents as $r){
    if($r == "") continue;
    $p = explode(" ",$r);
    $box = array($p[1],$p[2],$p[3],$p[4]);
    $boxes[] = new Box($box);
  }
  
  $sizeBins = count($boxes)/$S;
  $numBoxes = count($boxes);
  usort($boxes, 'compareArea');
  $environment = array();
  $visited = array();
  for($s = 0; $s < $S; $s++) {
    $sizeElems = array();
    $environment[] = array();
    $visited[] = array();
    for($i = 0; $i < $sizeBins; $i++) {
      $sizeElems[] = $boxes[$sizeBins*$s + $i];
    }
    usort($sizeElems, 'compareX');
    $horizontalBins = count($sizeElems)/$H;
    for($h = 0; $h < $H; $h++) {
      $horizontalElems = array();
      $environment[$s][] = array();
      $visited[$s][] = array();
      for($j = 0; $j < $horizontalBins; $j++) {
        $horizontalElems[] = $sizeElems[$horizontalBins*$h + $j];
      }
      usort($horizontalElems, 'compareY');
      $verticalBins = count($horizontalElems)/$V;
      for($v = 0; $v < $V; $v++) {
        $environment[$s][$h][] = array();
        $visited[$s][$h][] = array();
        for ($k = 0; $k < $verticalBins; $k++) {
          $environment[$s][$h][$v][] = $horizontalElems[$verticalBins*$v + $k];
          $visited[$s][$h][$v] = 0;
        }
      }
    }
  }
  return array('env'=>$environment, 'vis'=>$visited, 'boxes'=>$numBoxes);
}

function loadGroundTruths($img) {
  $contents = file_get_contents("data/some/ground_truths.txt");
  $contents = explode("\n",$contents);
  $boxes = array();
  foreach($contents as $r){
    if($r == "") continue;
    $p = explode(" ",$r);
    if($p[0] == $img) {
      $box = array($p[1],$p[2],$p[3],$p[4]);
      $boxes[] = new Box($box);
    }
  }
  return $boxes;
}

// MAIN PHP PROCEDURE
$S = 10;
$H = 3;
$V = 3;
$data = loadEnvironment($img, $S, $H, $V);
$env = json_encode($data['env']);
$vis = json_encode($data['vis']);
$numBoxes = $data['boxes'];
$gtb = loadGroundTruths($img);
$numObjects = count($gtb);
$gtb = json_encode($gtb);
?>

<table id="mainTable" border="1" width="100%"><tr><td align="center" width="50%">
<div class="Canvas" id="imageDiv"><img id="displayImage" src=""><canvas id="canvas"></canvas></div>
</td>
<td align="center">
Environment Viewer<br>
Scale (rows) vs Location (columns)
<table width="100%" border="1">
<tr>
  <td></td><td>A</td><td>B</td><td>C</td><td>D</td>
  <td>E</td><td>F</td><td>G</td><td>H</td><td>I</td>
</tr>
<?php
for($s = 0; $s < $S; $s++){
  echo "<tr id='row_".$s."'><td>".(9-$s)."</td>";
  for($v = 0; $v < $V; $v++) {
    for($h = 0; $h < $H; $h++){
      echo '<td id="cell_'.$s.'_'.$h.'_'.$v.'"></td>'; //'<a href="showBoxes.php?s='.$s.'&h='.$h.'&v='.$v.'&a=a">'.count($environment[$s][$h][$v]).'</a></td>';
    }
  }
  echo "</tr>";
}
?>
</table>
</td>
</tr></table>
<table width="100%" border="1">
  <tr>
    <td width="33%">
      <b>Environment Info</b>
      <a href="showBoxes.php">Initialize</a> <hr>
      Total Number of boxes: <span id="nBoxes"><?php echo $numBoxes?></span><br>
      Visited boxes: <span id="visited">0</span><br>
      Ground truth objects: <span id="objects"><?php echo$numObjects?></span><br>
      Good candidates found: <span id="found">0</span>
    </td>
    <td width="34%">
      <table align="center">
        <tr><th colspan="3">ACTION CONTROLS</th></tr>
        <tr>
        <td align="center">
          <input type="button" value="Explore 2 Scales Up" onclick="action('2UP')"/> <br>
          <input type="button" value="Explore 1 Scales Up" onclick="action('1UP')"/> <hr>
          <input type="button" value="Explore 1 Scales Down" onclick="action('1DOWN')"/> <br>
          <input type="button" value="Explore 2 Scales Down" onclick="action('2DOWN')"/>
        </td>
        <td align="center">Go To<br>
            <table>
              <tr><td><input type="button" value="A" onclick="action('A')"/></td>
                  <td><input type="button" value="B" onclick="action('B')"/></td>
                  <td><input type="button" value="C" onclick="action('C')"/></td>
              </tr>
              <tr><td><input type="button" value="D" onclick="action('D')"/></td>
                  <td><input type="button" value="E" onclick="action('E')"/></td>
                  <td><input type="button" value="F" onclick="action('F')"/></td>
              </tr>
              <tr><td><input type="button" value="G" onclick="action('G')"/></td>
                  <td><input type="button" value="H" onclick="action('H')"/></td>
                  <td><input type="button" value="I" onclick="action('I')"/></td>
              </tr>
            </table>
        </td>
        <td align="center">
        </tr>
      </table>
    </td>
    <td>
      <b>Action Feedback</b> (t=<span id="counter">0</span>)<hr>
      Scale: <span id="scale"></span><br>
      Location: <span id="location"></span><br>
      Boxes: <span id="batch"></span><br>
      <font color="blue"><span id="feedback"></span></font>
      <font color="white">.</font>
    </td>
</table>
<script>
var totalBoxes = <?php echo$numBoxes?>;
var visitedBoxes = 0;
var S = <?php echo$S?>, H = <?php echo$H?>, V = <?php echo$V?>;
var batch = H*V;
var size = 0;
var horizontal = 0;
var vertical = 0;
var actionChosen = 0;
var env = <?php echo$env?>;
var vis = <?php echo$vis?>;
var gtb = <?php echo$gtb?>;
var hits = 0;
var actionCounter = 0;
var locations = ['A','B','C','D','E','F','G','H','I'];

function action(a) {
  var s = size;
  var h = horizontal;
  var v = vertical;
  if(a == '2UP') { s = Math.max(s-2,0); }
  else if(a == '1UP') { s = Math.max(s-1,0); }
  else if(a == 'A') { h = 0; v = 0; }
  else if(a == 'B') { h = 1; v = 0; }
  else if(a == 'C') { h = 2; v = 0; }
  else if(a == 'D') { h = 0; v = 1; }
  else if(a == 'E') { h = 1; v = 1; }
  else if(a == 'F') { h = 2; v = 1; }
  else if(a == 'G') { h = 0; v = 2; }
  else if(a == 'H') { h = 1; v = 2; }
  else if(a == 'I') { h = 2; v = 2; }
  else if(a == '1DOWN') { s = Math.min(s+1,9); }
  else if(a == '2DOWN') { s = Math.min(s+2,9); }
  selectBoxes(s,h,v,a);
}

function drawGroundTruths(){
  for(i = 0; i < gtb.length; i++) {
    showBox(gtb[i].b,['rgba(0, 0, 0, 0.0)','rgba(255, 0, 0, 1.0)'],true); //'rgba(255, 255, 255, 1.0)'
  }
}

function selectBoxes(s,h,v,a) {
  var draw = Array();
  if(a[0] != "1" && a[0] != "2") {
    var ini = vis[s][h][v];
    var end = Math.min(ini+batch, env[s][h][v].length);
    for(i = ini; i < end; i++) {
      draw.push(env[s][h][v][i].b);
    }
    vis[s][h][v] = end;
    document.getElementById('cell_'+s+'_'+h+'_'+v).style.backgroundColor = 'green';
  } else {
    for(i = 0; i < V; i++) {
      for(j = 0; j < H; j++){
        next = Math.min(vis[s][j][i], env[s][j][i].length);
        if (next < env[s][j][i].length) {
          draw.push(env[s][j][i][next].b);
          vis[s][j][i] = next+1;
        }
      }
    }
    document.getElementById('row_'+s).style.backgroundColor = 'yellow';
  }
  var c = document.getElementById("canvas");
  var ctx = c.getContext("2d");
  ctx.clearRect(0, 0, c.width, c.height);  
  var maxIoU = 0;
  var bestBox = [];
  for(i = 0; i < draw.length; i++) {
    showBox(draw[i],['rgba(0, 255, 255, 0.0)','rgba(255, 255, 255, 1.0)'],true);
    for (j = 0; j < gtb.length; j++) {
      iou = getIOU(draw[i],gtb[j].b);
      if (iou > maxIoU) {
        maxIoU = iou;
        bestBox = draw[i];
      }
    }
  }
  drawGroundTruths();
  size = s;
  horizontal = h;
  vertical = v;
  actionChosen = a;
  visitedBoxes = 0;
  actionCounter += 1;
  for(s = 0; s < S; s++) {
    document.getElementById('row_'+s).style.backgroundColor = '';
    for(h = 0; h < H; h++) {
      for(v = 0; v < V; v++) {
        document.getElementById('cell_'+s+'_'+h+'_'+v).innerHTML = vis[s][h][v];
        if(vis[s][h][v] >= env[s][h][v].length) {
          document.getElementById('cell_'+s+'_'+h+'_'+v).style.backgroundColor = '#CCCCCC';
        } else {
          document.getElementById('cell_'+s+'_'+h+'_'+v).style.backgroundColor = '';
        }
        visitedBoxes += vis[s][h][v];
      }
    }
  }
  if(actionChosen[0] != "1" && actionChosen[0] != "2") {
    document.getElementById('cell_'+size+'_'+horizontal+'_'+vertical).style.backgroundColor = 'green';
    document.getElementById('location').innerHTML = locations[horizontal + 3*vertical];
  } else {
    document.getElementById('row_'+size).style.backgroundColor = 'yellow';
    document.getElementById('location').innerHTML = 'Exploring all';
  }
  percent = 100*(visitedBoxes/totalBoxes);
  document.getElementById('visited').innerHTML = visitedBoxes + ' ('+percent.toFixed(2)+'%)';
  document.getElementById('scale').innerHTML = 9 - size;
  document.getElementById('batch').innerHTML = draw.length;
  document.getElementById('counter').innerHTML = actionCounter;
  if (maxIoU > 0.5) {
    document.getElementById('feedback').innerHTML = 'Ground truth hit: IOU='+maxIoU.toFixed(2);
    showBox(bestBox,['rgba(0, 255, 255, 0.5)','rgba(255, 255, 255, 1.0)'],true);
    hits += 1;
    document.getElementById('found').innerHTML = hits;
  } else {
    document.getElementById('feedback').innerHTML = '';
  }
}

changeImage('data/some/<?php echo$img?>',Array(500,375));
drawGroundTruths();
</script>
