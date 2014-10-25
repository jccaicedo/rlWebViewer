<?php
include_once("../lsodv2/model/config.php");
header("Content-type: image/jpg");
$name = safeValue($_GET["n"],"");
$dir = safeValue($_GET["d"],1);
$bbox = safeValue($_GET["b"],"");
if($name == "" || $name == "/"){
  readfile("icons/white_pixels.png");
}else{
  $sourceDirs = array(1=>$CFG->auxDataDir."/allimgs/");
  $tmpRoot = "tmp/";
  $srcRoot = $sourceDirs[$dir];
  $relativePath = explode("/",$name);
  $bbox = explode(":",$bbox);
  $w = $bbox[2]-$bbox[0];
  $h = $bbox[3]-$bbox[1];
  $tmpName = array_pop($relativePath);
  $tmpName = str_replace(".jpg","_".implode("_",$bbox).".jpg",$tmpName);
  if(!file_exists($tmpRoot.$tmpName)){
    exec("convert ".$srcRoot.$name." -crop ".$w."x".$h."+".$bbox[0]."+".$bbox[1]."! png: | convert - -resize 50x50 ".$tmpRoot.$tmpName);
    //exec("convert ".$srcRoot.$name." -resize 50x50 ".$tmpRoot.$tmpName);
  }
  readfile($tmpRoot.$tmpName);
}

?>
