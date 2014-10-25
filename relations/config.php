<?php
include_once("common.php");
date_default_timezone_set('America/Chicago');
ini_set('memory_limit', '4096M');
global $CFG;
$CFG = new stdClass();
$CFG->dbdir = "/home/caicedo/data/Flickr/";

$CFG->sys = "/home/caicedo/software/ImageStreams/controller/";
$CFG->remoteHost = "taub.campuscluster.illinois.edu";
$CFG->remoteDB = "/projects/VisionLanguage/caicedo/data/Flickr/";

$CFG->categoriesFile = $CFG->sys."files/categories.txt";

?>
