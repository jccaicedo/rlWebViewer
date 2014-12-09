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
//$MEM_DIR = "/home/caicedo/data/rl/V3LayerModel_long/testMem/";
//$MEM_DIR = "/home/caicedo/data/rl/fullExp01/testMem/";
$MEM_DIR = "/home/caicedo/data/rl/categories01/bicycle/testMem/";
//$MEM_DIR = "/home/caicedo/data/rl/fullGreedy/testMem/";

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
}

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
$replayData = loadReplayData($img, $MEM_DIR);
$episodes = listMemoryEpisodes($MEM_DIR);
?>

<table id="mainTable" border="1" width="100%"><tr><td align="center" width="50%">
<div class="Canvas" id="imageDiv"><img id="displayImage" src=""><canvas id="canvas"></canvas></div>
</td>
<td align="center">
Environment Viewer<br>
<table width="100%" border="1">
<?php
# Column-major indexing of positions
$posIds = array(0,3,6,1,4,7,2,5,8);
for($s = 0; $s < $S; $s++){
  if($s % 4 == 0 && $s < 9) { echo "</td></tr><tr>"; }
  echo "<td align='center' style='font-size:12px'>Scale ".(9-$s)."<br>";
  echo "<table border='1'>";
  for($h = 0; $h < $H; $h++){
    echo "<tr>";
    for($v = 0; $v < $V; $v++) {
      $pid = $posIds[$h + 3*$v];
      echo '<td id="cell_'.$s.'_'.$pid.'" width="30px" style="font-size:14px">'.$pid.'</td>'; 
    }
    echo "</tr>";
  }
  echo "</table></td>";
}
?>
</table>
</td>
</tr></table>
<table width="100%" border="1">
  <tr>
    <td width="33%">
      <b>Environment Info</b>
      <!-- <a href="showBoxes.php">Initialize</a> !--> <hr>
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
          <input type="button" value="UP" onclick="action('1UP')"/> 
          <input type="button" value="DOWN" onclick="action('1DOWN')"/> <hr>
          <input type="button" value="STAY" onclick="action('STAY')"/>
        </td>
        <td align="center">Go To<br>
            <table>
              <tr>
                  <td align="center"><input type="button" value="FRONT" onclick="action('FRONT')"/></td>
              </tr>
              <tr><td align="center"><input type="button" value="LEFT" onclick="action('LEFT')"/>
                  <input type="button" value="RIGHT" onclick="action('RIGHT')"/></td>
              </tr>
              <tr>
                  <td align="center"><input type="button" value="BACK" onclick="action('BACK')"/></td>
              </tr>
            </table>
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
      Scale: <span id="scale"></span><br>
      Location: <span id="location"></span><br>
      Boxes: <span id="batch"></span><br>
      <font color="blue"><span id="feedback"></span></font>
      <font color="white">.</font>
    </td>
</table>
<?php
  for($i = 0; $i < count($episodes); $i++) {
    echo '<a href="replay.php?img='.$episodes[$i].'">'.$episodes[$i].'</a> ';
    if($i%12==0) echo "<br>";
  }
?>
<script>
var replay = <?php echo $replayData?>;
var totalBoxes = <?php echo $numBoxes?>;
var visitedBoxes = 0;
var S = <?php echo$S?>, H = <?php echo$H?>, V = <?php echo$V?>;
var batch = 3;
var size = 0; //S/2; // Set to zero to visualize greedy
var horizontal = 0;
var vertical = 0;
var actionChosen = 0;
var env = <?php echo $env?>;
var vis = <?php echo $vis?>;
var gtb = <?php echo $gtb?>;
var hits = 0;
var actionCounter = 0;
var locations = ['A','B','C','D','E','F','G','H','I'];
var actionList = ['1UP','1DOWN','FRONT','BACK','LEFT','RIGHT','STAY'];
var replayInstant = 0;
var replayJSInterval = -1;

function startReplay() {
  replayJSInterval = setInterval( function() { replayAction() }, 100 );
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
  var s = size;
  var h = horizontal;
  var v = vertical;
  if(a == '1UP') { s = Math.max(s-1,0); }
  else if(a == '1DOWN') { s = Math.min(s+1,9); }
  else if(a == 'FRONT') { v = Math.max(v-1,0); }
  else if(a == 'BACK') { v = Math.min(v+1,2); }
  else if(a == 'LEFT') { h = Math.max(h-1,0); }
  else if(a == 'RIGHT') { h = Math.min(h+1,2); }
  else if(a == 'STAY') { }
  selectBoxes(s,h,v,a);
}

function drawGroundTruths(){
  for(i = 0; i < gtb.length; i++) {
    showBox(gtb[i].b,['rgba(0, 0, 0, 0.0)','rgba(255, 0, 0, 1.0)'],true); //'rgba(255, 255, 255, 1.0)'
  }
}

function selectBoxes(s,h,v,a) {
  var draw = Array();
  var ini = vis[s][h][v];
  var end = Math.min(ini+batch, env[s][h][v].length);
  for(i = ini; i < end; i++) {
    draw.push(env[s][h][v][i].b);
  }
  vis[s][h][v] = end;
  document.getElementById('cell_'+s+'_'+(h+3*v)).style.backgroundColor = 'green';

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
    //document.getElementById('row_'+s).style.backgroundColor = '';
    for(h = 0; h < H; h++) {
      for(v = 0; v < V; v++) {
        document.getElementById('cell_'+s+'_'+(h+3*v)).innerHTML = vis[s][h][v];
        if(vis[s][h][v] >= env[s][h][v].length) {
          document.getElementById('cell_'+s+'_'+(h+3*v)).style.backgroundColor = '#CCCCCC';
        } else {
          document.getElementById('cell_'+s+'_'+(h+3*v)).style.backgroundColor = '';
        }
        visitedBoxes += vis[s][h][v];
      }
    }
  }
  document.getElementById('cell_'+size+'_'+(horizontal+3*vertical)).style.backgroundColor = 'green';
  document.getElementById('location').innerHTML = locations[horizontal + 3*vertical] + ' ('+horizontal+','+vertical+')';
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

changeImage('<?php echo$img?>', drawGroundTruths);
startReplay();
</script>
