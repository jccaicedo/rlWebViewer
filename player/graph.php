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
  $img = "000001";
}
//$MEM_DIR = "/home/caicedo/data/rl/S_lr0.0001_upe1_disc0.7/testMem/";
//$MEM_DIR = "/home/caicedo/data/rl/random/testMem/";
//$MEM_DIR = "/home/caicedo/data/rl/greedy/testMem/";
//$MEM_DIR = "/home/caicedo/data/rl/V3LayerModel/testMem/";
$MEM_DIR = "/home/caicedo/data/rl/V3LayerModel_long/testMem/";
//$MEM_DIR = "/home/caicedo/data/rl/fullExp01/testMem/";
$MEM_DIR = "/home/caicedo/data/rl/categories01/bicycle/testMem/";

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

/*function compareArea($u, $v){
  return $u->area() == $v->area() ? 0 : ($u->area() > $v->area()) ? -1 : 1;
}

function compareX($u, $v){
  return $u->x() == $v->x() ? 0 : ($u->x() > $v->x()) ? 1 : -1;
}

function compareY($u, $v){
  return $u->y() == $v->y() ? 0 : ($u->y() > $v->y()) ? 1 : -1;
}

function loadEnvironment($img, $S, $H, $V) {
  $BOXES_DIR = "/home/caicedo/data/rcnnPascal/proposals/rcnnProposalsPerImage/";
  $contents = file_get_contents($BOXES_DIR.$img.".idx");
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
}*/

function loadGroundTruths($img) {
  $contents = file_get_contents("/home/caicedo/data/cnnPatches/lists/2007/all_objects_test.txt");
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

function loadReplayData($img, $memDir) {
  $contents = file_get_contents($memDir.$img.".txt");
  return $contents;
}

function listMemoryEpisodes($memDir) {
  $episodes = scandir($memDir);
  foreach($episodes as $k=>$v) {
    $episodes[$k] = str_replace('.txt','',$v);
    if($v == '.' || $v == '..') {
      $episodes[$k] = '';
    }
  }
  return($episodes);
}

// MAIN PHP PROCEDURE
$gtb = loadGroundTruths($img);
$numObjects = count($gtb);
$gtb = json_encode($gtb);
$replayData = loadReplayData($img, $MEM_DIR);
//$episodes = listMemoryEpisodes($MEM_DIR);
?>

<table id="mainTable" border="1" width="100%">
<tr><th>Image</th><th>Environment</th></tr>
<tr><td align="center" width="50%">
<div class="Canvas" id="imageDiv"><img id="displayImage" src=""><canvas id="canvas"></canvas></div>
</td>
<td align="center">
<iframe id="graphView" src="cyViewer.php"></iframe>
</td>
</tr></table>
<table width="100%" border="1">
  <tr>
    <td width="33%">
      <b>Environment Info</b>
      <!-- <a href="showBoxes.php">Initialize</a> !--> <hr>
      Total Number of boxes: <span id="nBoxes">0</span><br>
      Visited boxes: <span id="visited">0</span><br>
      Ground truth objects: <span id="objects"><?php echo$numObjects?></span><br>
      Good candidates found: <span id="found">0</span>
    </td>
    <td width="34%">
      <table align="center">
        <tr><th colspan="2">ACTION CONTROLS</th></tr>
        <tr>
        <td align="center" colspan="2">
          <input type="button" value="0" onclick="action(0)"/> 
          <input type="button" value="1" onclick="action(1)"/> 
          <input type="button" value="2" onclick="action(2)"/>
          <input type="button" value="3" onclick="action(3)"/> 
          <input type="button" value="4" onclick="action(4)"/> <hr>
          <input type="button" value="5" onclick="action(5)"/>
          <input type="button" value="6" onclick="action(6)"/> 
          <input type="button" value="7" onclick="action(7)"/> 
          <input type="button" value="8" onclick="action(8)"/>
          <input type="button" value="9" onclick="action(9)"/> 
        </td>
        </tr>
        <tr>
          <td align="center">Action Value: <br><span id="actionValue"></span></td>
          <td align="center">Reward: <br><span id="reward"></span></td>
        </tr>
      </table>
    </td>
    <td>
      <b>Action Feedback</b> (t=<span id="counter">0</span>)<hr>
      Location: <span id="location"></span><br>
      Connections To: <span id="connections"></span><br>
      New Boxes: <span id="batch"></span><br>
      <font color="blue"><span id="feedback"></span></font>
      <font color="white">.</font>
    </td>
</table>
<?php
/*  for($i = 0; $i < count($episodes); $i++) {
    echo '<a href="replay.php?img='.$episodes[$i].'">'.$episodes[$i].'</a> ';
    if($i%12==0) echo "<br>";
  }*/
?>
<script>
var replay = <?php echo $replayData?>;
var totalBoxes = 0;
var visitedBoxes = 0;
var batch = 10;
var actionChosen = 0;
var currentNode = 0;
var env = null; 
var gtb = <?php echo $gtb?>;
var hits = 0;
var actionCounter = 0;
var replayInstant = 0;
var replayJSInterval = -1;

function graphReady() {
  env = document.getElementById('graphView').contentWindow.cy;
  totalBoxes = env.nodes().length;
  document.getElementById('nBoxes').innerHTML = totalBoxes;
}

function startReplay() {
  //replayJSInterval = setInterval( function() { replayAction() }, 100 );
}

function stopReplay() {
  if(replayJSInterval != -1) {
    clearInterval(replayJSInterval);
    replayJSInterval = -1;
  }
}

function replayAction() {
  if(replayInstant < replay.actions.length){
    action( actionList[ replay.actions[replayInstant] ] );
    document.getElementById('actionValue').innerHTML = ' ' + replay.values[replayInstant].toFixed(2);
    document.getElementById('reward').innerHTML = ' ' + replay.rewards[replayInstant].toFixed(2);
    replayInstant += 1;
  } else {
    stopReplay();
  }
}

function action(a) {
  env.elements('edge[source = "'+currentNode+'"]').removeClass('paths');
  env.nodes('#' + currentNode).removeClass('current');
  env.nodes('#' + currentNode).addClass('highlighted');
  elems = env.elements('edge[source = "'+currentNode+'"]');
  edges = elems.edges();
  currentNode = edges[a].data('target');
  showBoxes();
  env.nodes('#' + currentNode).removeClass('highlighted');
  env.nodes('#' + currentNode).addClass('current');
  env.elements('edge[source = "'+currentNode+'"]').addClass('paths');
}

function drawGroundTruths(){
  for(i = 0; i < gtb.length; i++) {
    showBox(gtb[i].b,['rgba(0, 0, 0, 0.0)','rgba(255, 0, 0, 1.0)'],true); //'rgba(255, 255, 255, 1.0)'
  }
}

function showBoxes(){
  elems = env.elements('edge[source = "'+currentNode+'"]');
  edges = elems.edges();
  var draw = Array();
  var connStr = '';
  var newBoxes = 0;
  for(i = 0; i < edges.length; i++) {
    nodeId = edges[i].data('target');
    node = env.nodes('#'+nodeId);
    node.addClass('highlighted');
    draw.push(node);
    connStr += ' ' + i + ':' + nodeId;
    if(node.data('visited') == undefined) {
      node.data('visited',true);
      visitedBoxes += 1;
      newBoxes += 1;
    }
  }

  var c = document.getElementById("canvas");
  var ctx = c.getContext("2d");
  ctx.clearRect(0, 0, c.width, c.height);  
  var maxIoU = 0;
  var bestBox = [];
  for(i = 0; i < draw.length; i++) {
    showBox(draw[i].data('box'),['rgba(0, 255, 255, 0.0)','rgba(255, 255, 255, 1.0)'],true);
    for (j = 0; j < gtb.length; j++) {
      iou = getIOU(draw[i].data('box'),gtb[j].b);
      if (iou > maxIoU) {
        maxIoU = iou;
        bestBox = draw[i].data('box');
      }
    }
    if(draw[i].data('evaluated') == undefined) {
      draw[i].data('evaluated','true');
      hits += 1;
    }
  }
  drawGroundTruths();
  document.getElementById('connections').innerHTML = connStr;
  document.getElementById('location').innerHTML = 'node ' + currentNode;
  percent = 100*(visitedBoxes/totalBoxes);
  document.getElementById('visited').innerHTML = visitedBoxes + ' ('+percent.toFixed(2)+'%)';
  document.getElementById('batch').innerHTML = newBoxes;
  document.getElementById('counter').innerHTML = actionCounter;
  if (maxIoU > 0.5) {
    document.getElementById('feedback').innerHTML = 'Ground truth hit: IOU='+maxIoU.toFixed(2);
    showBox(bestBox,['rgba(0, 255, 255, 0.5)','rgba(255, 255, 255, 1.0)'],true);
    //hits += 1;
    document.getElementById('found').innerHTML = hits;
  } else {
    document.getElementById('feedback').innerHTML = '';
  }
}

changeImage('<?php echo$img?>', drawGroundTruths);
startReplay();
</script>
