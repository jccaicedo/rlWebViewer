<?php
#include_once("config.php");
$imageName = $_GET["name"];
$dir = "/home/caicedo/data/pascalImgs/";
$content = file_get_contents($dir.$imageName);
header('Content-Type:jpeg');
echo $content;
?>
