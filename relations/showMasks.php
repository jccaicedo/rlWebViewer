<?php
$dir = '/home/caicedo/data/rcnn/testMasks/*.png';
$files = glob($dir);
print '<pre>';
foreach($files as $f){
  $data = explode('/',$f);
  print '<img src="img.php?d=1&n='.$data[count($data)-1].'"><br>';
}
?>
