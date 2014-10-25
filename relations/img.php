<?
//include("../model/config.php");
$imgsDir = array('/home/caicedo/data/allimgs/','/home/caicedo/data/rcnn/testMasks/');
$d = $_GET['d'];
header("Content-type: image/jpg");
$name = $_GET["n"];
if($name == ""){
  readfile("icons/white_pixels.png");
}else{
  readfile($imgsDir[$d].$name);
}
?>
