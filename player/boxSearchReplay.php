<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/***************************************/
/***** IMAGE AND ENVIRONMENT INFO ******/
/***************************************/
if( !isset($_GET["t"]) ){ 

echo '<script src="utilityFunctions.js"></script>';
echo '<script src="drawingFunctions.js"></script>';
echo '<script src="BoxCompare.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="annotations.css">';
if(isset($_GET["img"])) {
  $img = $_GET["img"];
} else {
  die("No image has been specified");
}
if(isset($_GET["cat"])) {
  $categ = $_GET["cat"];
} else {
  die("No category has been specified");
}

$SELEX = array("aeroplane"=>array('000316','001547','002217','004824'),
               "bicycle"=>array('002239','002475','000283','004395','004637'),
               "bird"=>array('001637','001926','002316','003402','003514','004362'),
               "boat"=>array('000350','001646','001822'),
               "bottle"=>array('001631','002857'),
               "bus"=>array('000864','004431','005870'),
               "car"=>array('000240','001267','002446','002701'),
               "cat"=>array('001401','004165'),
               "chair"=>array('000940','000953','001039'),
               "cow"=>array('000273','000725','001987','002115'),
               "diningtable"=>array('000144','001674'),
               "dog"=>array('001202','002111','002160'),
               "horse"=>array('000623','002071'),
               "motorbike"=>array('000979','001134'),
               "person"=>array('000022','000038','000069','000139','000191','000240','000286'),
               "pottedplant"=>array('000575','002339'),
               "sheep"=>array('000925','000992','003366'),
               "sofa"=>array('000658'),
               "train"=>array('000674','001449','001672'),
               "tvmonitor"=>array('000059','000790','001905','002074')
              );

$MEM_DIR = "/media/T2/backupJC/agent/".$categ."/testMem/";

class Box {
  var $a;
  var $b;
  var $c;
  function __construct($b){
    $this->c = 0;
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

function loadGroundTruths($categ, $img) {
  $contents = file_get_contents("/media/T2/backupJC/lists/2007/test/".$categ."_test_bboxes.txt");
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
$gtb = loadGroundTruths($categ, $img);
$numObjects = count($gtb);
$gtb = json_encode($gtb);
$replayData = loadReplayData($img, $MEM_DIR);
$episodes = listMemoryEpisodes($MEM_DIR);
?>
<head>
  <script>
  var playing = false;
  </script>
</head>
<center>
 <a href="demo.php">Back to Home</a> <br>
 <b>Searching for: <?php echo $categ; ?> </b>
</center>
<table id="mainTable" border="1" width="100%">
<tr><th>Image</th><th>Evolution of IoU</th></tr>
<tr><td align="center" width="50%">
<div class="Canvas" id="imageDiv"><img id="displayImage" src=""><canvas id="canvas"></canvas></div>
</td>
<td align="center">
<iframe id="graphView" src="boxSearchReplay.php?t=true"></iframe>
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
          <table border="1">
            <tr>
              <td class="bcell"><input type="button" value="Up" onclick="action(0)"/></td>
              <td class="bcell"><input type="button" value="Up" onclick="action(1)"/></td>
              <td class="bcell"><input type="button" value="Up" onclick="action(2)"/></td>
              <td class="bcell"><input type="button" value="Up" onclick="action(3)"/></td>
            </tr>
            <tr>
              <td class="bcell">X-Coord</td><td class="bcell">Y-Coord</td>
              <td class="bcell">Scale</td><td class="bcell">Aspect Ratio</td>
            </tr>
            <tr>
              <td class="bcell"><input type="button" value="Down" onclick="action(4)"/></td>
              <td class="bcell"><input type="button" value="Down" onclick="action(5)"/></td>
              <td class="bcell"><input type="button" value="Down" onclick="action(6)"/></td>
              <td class="bcell"><input type="button" value="Down" onclick="action(7)"/></td>
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
      Box: <span id="box"></span><br>
      Aspect Ratio: <span id="ratio"></span><br>
      Action: <span id="action_display"></span><br>
      <font color="blue"><span id="feedback"></span></font>
      <font color="white">.</font>
    </td>
</table>
<table>
  <tr><th>Selected Examples</th></tr>
  <tr><td align="center">
<?php
  for($i = 0; $i < count($SELEX[$categ]); $i++) {
    echo '<a href="boxSearchReplay.php?cat='.$categ.'&img='.$SELEX[$categ][$i].'">'.$SELEX[$categ][$i].'</a> ';
  }
?>
  </td></tr>
  <tr><th>All Test Examples</th></tr>
  <tr><td>
<?php
  for($i = 0; $i < count($episodes); $i++) {
    echo '<a href="boxSearchReplay.php?cat='.$categ.'&img='.$episodes[$i].'">'.$episodes[$i].'</a> ';
  }
?>
  </td></tr>
</table>
<script>
var replay = <?php echo $replayData?>;
var maximumBoxes = 2000;
var visitedBoxes = 0;
var actionChosen = 0;
var currentNode = 0;
var env = null; 
var gtb = <?php echo $gtb?>;
var hits = 0;
var chosenAction = 0;
var actionCounter = 0;
var replayInstant = 0;
var replayJSInterval = -1;
var box = [0,0,0,0];
var W = 0, H = 0;
var landmarks = Array();
var speed = 100;
var actionNames = ['x-up','y-up','scale-up','ratio-up','x-down','y-down','scale-down','ratio-down','landmark','skip-region'];
var prevMaxIoU = 0;

function startReplay() {
  env = document.getElementById('graphView').contentWindow.cy;
  changeImage('<?php echo$img?>', initializeEnvironment);
}

function stopReplay() {
  if(replayJSInterval != -1) {
    clearInterval(replayJSInterval);
    replayJSInterval = -1;
  }
}

function initializeEnvironment(){
  drawGroundTruths();
  replayJSInterval = setInterval( function() { replayAction() }, speed );
  playing = true;
  var W = document.getElementById("canvas").width;
  var H = document.getElementById("canvas").height;
  env.add( {group:"nodes", data:{id:"A", w:1, h:1}, position:{x:0,y:H*0.5}} );
  env.add( {group:"nodes", data:{id:"B", w:1, h:1}, position:{x:W,y:H*0.5}} );
  env.add( {group:"nodes", data:{id:"C", w:1, h:1}, position:{x:0,y:H*0.25}} );
  env.add( {group:"nodes", data:{id:"D", w:1, h:1}, position:{x:W,y:H*0.25}} );
  env.add( {group:"nodes", data:{id:"E", w:1, h:1}, position:{x:0,y:H*0.75}} );
  env.add( {group:"nodes", data:{id:"F", w:1, h:1}, position:{x:W,y:H*0.75}} );

  env.add( {group:"edges", data:{ id:"AB", source: "A", target: "B"}} );
  env.add( {group:"edges", data:{ id:"CD", source: "C", target: "D"}} );
  env.add( {group:"edges", data:{ id:"EF", source: "E", target: "F"}} );

}

function replayAction() {
  if(replayInstant < replay.actions.length){
    b = replay.boxes[replayInstant];
    box = [b[0],b[1],b[2]-b[0],b[3]-b[1]];
    chosenAction = replay.actions[replayInstant];
    if(chosenAction == 8) {
      landmarks.push(box);
    }
    showBoxes();
    document.getElementById('actionValue').innerHTML = ' ' + replay.values[replayInstant].toFixed(2);
    document.getElementById('reward').innerHTML = ' ' + replay.rewards[replayInstant].toFixed(2);
    replayInstant += 1;
    actionCounter += 1;
    //if(chosenAction == 8) stopReplay();
  } else {
    stopReplay();
  }
}

function action(a) {
  chosenAction = a;
  actionCounter += 1;
  // Transform box
  if(a == 0){
    box[0] += 15;
  } else if(a == 1) {
    box[1] += 15;
  } else if(a == 2) {
    box[0] -= 0.1*box[2]/2;
    box[1] -= 0.1*box[3]/2;
    box[2] += 0.1*box[2];
    box[3] += 0.1*box[3];
  } else if(a == 3) {
    box[1] -= 0.1*box[3]/2;
    box[3] += 0.1*box[3];
  } else if(a == 4) {
    box[0] -= 15;
  } else if(a == 5) {
    box[1] -= 15;
  } else if(a == 6) {
    box[0] += 0.1*box[2]/2;
    box[1] += 0.1*box[3]/2;
    box[2] -= 0.1*box[2];
    box[3] -= 0.1*box[3];
  } else if(a == 7) {
    box[0] -= 0.1*box[2]/2;
    box[2] += 0.1*box[2];
  } else if(a == 8) {
    landmarks.push(box);
  }
  // Check limits
  box[0] = box[0] < 0? 0 : box[0] > W? W: box[0];
  box[1] = box[1] < 0? 0 : box[1] > H? H: box[1];
  box[2] = box[2] < 1? 1 : box[0] + box[2] > W? W-box[0]: box[2];
  box[3] = box[3] < 1? 1 : box[1] + box[3] > H? H-box[1]: box[3];
  // Redraw environment
  showBoxes();
}

function drawGroundTruths(){
  for(i = 0; i < gtb.length; i++) {
    showBox(gtb[i].b,['rgba(0, 0, 0, 0.0)','rgba(255, 0, 0, 1.0)'],true);
  }
  for(i = 0; i < landmarks.length; i++) {
    var maxIoU = 0;
    var gtMatch = -1;
    for (j = 0; j < gtb.length; j++) {
      iou = getIOU(landmarks[i],gtb[j].b);
      if (iou > maxIoU) {
        maxIoU = iou;
        gtMatch = j;
      }
    }
    if(maxIoU >= 0.5 && (gtb[gtMatch].c == 0 || gtb[gtMatch].c == i+1)) {
      showBox(landmarks[i],['rgba(0, 255, 0, 0.5)','rgba(255, 255, 255, 1.0)'],true);
      gtb[gtMatch].c = i+1;
    } else {
      showBox(landmarks[i],['rgba(255, 0, 0, 0.5)','rgba(255, 255, 255, 1.0)'],true);
    }
  }
  if (box[0]+box[1]+box[2]+box[3] == 0) {
    W = document.getElementById("canvas").width;
    H = document.getElementById("canvas").height;
    var initW = W/2, initH = H/2;
    var x = initW/2, y = initH/2;
    box = [x,y, initW, initH];
    box = [0,0,W,H];
    showBox(box,['rgba(0, 255, 255, 0.0)','rgba(255, 255, 255, 1.0)'],true);
  }
}

function showBoxes(){
  var draw = Array();
  draw.push(box);
  visitedBoxes += 1;

  var c = document.getElementById("canvas");
  var ctx = c.getContext("2d");
  ctx.clearRect(0, 0, c.width, c.height);
  var maxIoU = 0;
  var bestBox = [];
  for(i = 0; i < draw.length; i++) {
    showBox(draw[i],['rgba(0, 255, 255, 0.0)','rgba(255, 255, 255, 1.0)'],true);
    for (j = 0; j < gtb.length; j++) {
      if(gtb[j].c == 0) {
        iou = getIOU(draw[i],gtb[j].b);
        if (iou > maxIoU) {
          maxIoU = iou;
          bestBox = draw[i];
        }
      }
    }
    hits += 1;
  }
  drawGroundTruths();
  percent = 100*(visitedBoxes/maximumBoxes);
  document.getElementById('visited').innerHTML = visitedBoxes + ' ('+percent.toFixed(2)+'%)';
  document.getElementById('counter').innerHTML = actionCounter;
  document.getElementById('nBoxes').innerHTML = maximumBoxes;
  document.getElementById('box').innerHTML = '(' + box[0].toFixed(0) + ',' + box[1].toFixed(0) + ',' + box[2].toFixed(0) + ',' + box[3].toFixed(0) + ')';
  document.getElementById('ratio').innerHTML = (box[3]/box[2]).toFixed(2);
  document.getElementById('action_display').innerHTML =  actionNames[chosenAction];

  if (maxIoU >= 0.5) {
    document.getElementById('feedback').innerHTML = 'Ground truth hit: IOU='+maxIoU.toFixed(2);
    showBox(bestBox,['rgba(0, 255, 255, 0.5)','rgba(255, 255, 255, 1.0)'],true);
    document.getElementById('found').innerHTML = hits;
  } else {
    document.getElementById('feedback').innerHTML = '';
  }

  // Draw IoU
  var coordX = (document.getElementById("canvas").width/replay.boxes.length)*visitedBoxes;
  var coordY = document.getElementById("canvas").height*(1-maxIoU)/2.0;
  env.add( {group:"nodes", data:{id:"IoU"+visitedBoxes, w:5, h:5}, position:{x:coordX,y:coordY}} );
  if(chosenAction == 8 && maxIoU >= 0.5){
    env.add( {group:"nodes", data:{id:"Landmark"+visitedBoxes, w:8, h:8}, position:{x:coordX,y:coordY}} );
    env.nodes('#Landmark'+visitedBoxes).addClass('positive');
  }
  if(chosenAction == 8 && maxIoU < 0.5){
    env.add( {group:"nodes", data:{id:"Landmark"+visitedBoxes, w:8, h:8}, position:{x:coordX,y:coordY}} );
    env.nodes('#Landmark'+visitedBoxes).addClass('negative');
  }
  // Draw Delta IoU
  var deltaIoU = maxIoU - prevMaxIoU;
  var coordZ = document.getElementById("canvas").height*( (0.5 - deltaIoU)/2 + 0.5 );
  env.add( {group:"nodes", data:{id:"Delta"+visitedBoxes, w:5, h:5}, position:{x:coordX,y:coordZ}} );
  env.nodes('#Delta'+visitedBoxes).addClass('delta');
  // Connect edges
  if(visitedBoxes > 1) {
    env.add( {group:"edges", data:{ id:(visitedBoxes-1)+"_"+visitedBoxes, source: "IoU"+(visitedBoxes-1), target: "IoU"+visitedBoxes}} );
    env.add( {group:"edges", data:{ id:"DeltaE_"+visitedBoxes, source: "Delta"+(visitedBoxes-1), target: "Delta"+visitedBoxes}} );
  }
  prevMaxIoU = maxIoU;

}
</script>
</html>

<?php 
/***************************************/
/******* TRAJECTORY VISUALIZATION ******/
/***************************************/
} else if(isset($_GET["t"])){ 
?>
<!DOCTYPE html>
<html>
<head>
<link href="style.css" rel="stylesheet" />
<meta charset=utf-8 />
<title>Cytoscape.js initialisation</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="http://cytoscape.github.io/cytoscape.js/api/cytoscape.js-latest/cytoscape.min.js"></script>
<script>
var cy;
$(function(){ // on dom ready

cy = cytoscape({
  container: document.getElementById('cy'),
  
  style: cytoscape.stylesheet()
    .selector('node')
      .css({
        'content': 'data(action)',
        'text-valign': 'center',
        'shape': 'ellipse',
        'width': 'data(w)',
        'height': 'data(h)',
        'opacity': 1.0,
        'border-width': 0,
        'border-color': '#61bffc'
      })
    .selector('edge')
      .css({
        'width': 1.0,
        'opacity': '1.0',
        'line-color': '#ddd',
        'curve-style': 'haystack',
        'haystack-radius': '0'
      })
    .selector('.positive')
      .css({
        'shape': 'ellipse',
        'background-color': 'green',
        'line-color': 'green',
        'target-arrow-color': 'green',
        'transition-property': 'background-color, line-color, target-arrow-color',
        'transition-duration': '0.0s'
      })
    .selector('.negative')
      .css({
        'shape': 'ellipse',
        'background-color': 'red',
        'line-color': 'red',
        'target-arrow-color': 'red',
        'transition-property': 'background-color, line-color, target-arrow-color',
        'transition-duration': '0.0s'
      })
    .selector('.delta')
      .css({
        'shape': 'ellipse',
        'background-color': 'brown',
        'line-color': 'brown',
        'target-arrow-color': 'brown',
        'transition-property': 'background-color, line-color, target-arrow-color',
        'transition-duration': '0.0s'
      }),
  
  elements: {nodes: [], edges: []},
  
  layout: {
    name: 'preset',
    root: '#0',
    directed: false,
    padding: 0
  }
});
try{
  parent.startReplay();
} catch(err) {
  setTimeout( function (){parent.startReplay();}, 1000);
}

}); // on dom ready
</script>
</head>
<body>
<div id="cy"></div>
</body>
</html>
<?php
/***************************************/
/**** END TRAJECTORY VISUALIZATION *****/
/***************************************/
}
?>
