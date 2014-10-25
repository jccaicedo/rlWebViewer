<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once("pageCommon.php");

$actionURL = "index.php?detections=1";

echo '<html><head><script src="../jquery-1.8.1.min.js"></script>';
echo '<script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>';
echo '<script src="../common.js"></script>';
echo '<script src="resizingFunctions.js"></script>';
echo '<script src="utilityFunctions.js"></script>';
echo '<script src="drawingFunctions.js"></script>';
echo '<script src="BoxCompare.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="annotations.css">';
echo '<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">';
echo '<script> $(function() { $( document ).tooltip(); }); </script>';
echo '</head><body onload="checkSize()"><center>'; // style="overflow: hidden;"

makeAnnotationPage($actionURL);

echo '</center><script type="text/javascript">';
echo 'drawLeft = false;';
echo 'drawRight = false;';
echo 'drawUp = false;';
echo 'drawDown = false;';
echo 'moveBox = false;';
echo 'newBoxEditions = 0;';
echo 'startX = 0;';
echo 'startY = 0;';
echo 'queryIdx = 1;';
echo 'isTraining = false;';
echo 'workerComments = new Array();';
echo 'questions = ',remoteServerCall($CFG,"drawPhraseBoxes/readNextEntity.php"),';';
echo 'responses = new Array(questions.length);';
echo 'initAnnotationArray();copyResponses();prevExample();'; 
echo '</script>';
echo '</body></html>';


function makeAnnotationPage($actionURL)
{
  echo '<form action="',$actionURL,'" method="post" id="mainForm" onsubmit="return gatherResults();">';
  echo '<input type="hidden" name="pageResults" id="pageResults" value="">';
  echo '<input type="hidden" name="coms" id="coms" value="">';

  echo '<div id="pageDiv" class="main">';
  makePage();
  echo '</div></form>';
}

?>
