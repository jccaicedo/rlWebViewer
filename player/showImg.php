<?php
include_once("../config.php");
$imageName = $_GET["name"];
$content = file_get_contents($CFG->imageSource.$imageName);
header('Content-Type:jpeg');
echo $content;
?>
