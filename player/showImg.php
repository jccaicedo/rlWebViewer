<?php
#include_once("config.php");
$imageName = $_GET["name"];
$dir = "/home/juanc/backup/pascalImgs/";
$content = file_get_contents($dir.$imageName);
header('Content-Type:jpeg');
echo $content;
?>
