<?php
header('Content-Type: application/json');

include_once("../config.php");
include_once("pageCommon.php");
include_once("../dbinter.php");

function prepareOutput($CFG,$image,$span,$explanation,$answer,$boxes)
{
  $sentenceList = file($CFG->dbdir.'/sentences/'.$image.'.txt', FILE_IGNORE_NEW_LINES);
  $imgDetails = exec("identify ".$CFG->dbdir."/images/".$image.'.jpg');
  $data = explode(" ",$imgDetails);
  $dim = explode("x",$data[2]);
  $imDims = array(intval($dim[0]),intval($dim[1]));
  $data = array("image" => $image, "span" => $span,
		"imDims" => $imDims, "sentenceList" => $sentenceList,
		"explanation" => $explanation, "expected" => $answer,
		"boxes" => $boxes);
  return($data);
}

function getExplanations()
{
  return array("The dog can be seen so it should receieve a bounding box that is as tight as possible around all observable parts.","Since the hands cannot be easily separated they receieve only one box.","The dog's hair can be observed in the image it receives a bounding box.","With multiple instances draw boxes only for instances that don't already have a box.","Since the hats cannot be easily separated they receive only one box.","The lake is observed in the image so it receives a box.","The girls can be easily seperated into separate regions, so they each receive their own bounding box.");
}

function getExamples($CFG)
{
  $imageList = array(115275821,126824259,356929855,128404184,109608573,1157855775,7725133284);
  $spanList = array('Ablackandwhitedog_19','herhands_8','goldenhair_13','Twolittleboys_8','arowofcolorfulhats_12','alake_9','Fourwomen_10');
  $boxList = array([],[],[],[[77,89,62,167],[362,113,52,142]],[],[],[[301,98,69,191],[272,273,150,99],[112,161,112,173]]);
  $expectedList = array([163,99,153,253],[103,257,119,82],[152,142,227,80],'nobox',[0,81,291,293],[72,202,426,136],[112,112,57,160]);
  $explanationList = getExplanations();
  $data = array(count($imageList));
  for ($i = 0; $i < count($imageList); $i++)
    $data[$i] = prepareOutput($CFG,$imageList[$i],$spanList[$i],$explanationList[$i],$expectedList[$i],$boxList[$i]);
  return $data;
}


/**********************************************
**           MAIN PROCEDURE                 **
**********************************************/

$data = array();
if (isset($_GET["q"])) {
  $questionID = $_GET["q"];
  $q = getDrawQuestions($questionID);
  foreach($q as $question) {
    $image = $question['imageID'];
    $span = $question['entity'];
    $boxes = $question['boxes'];
    array_push($data,prepareOutput($CFG,$image,$span,'','',$boxes));
  }
}
else 
  $data = getExamples($CFG);

echo json_encode($data);

?>
