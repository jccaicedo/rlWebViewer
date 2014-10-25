<?php
ini_set('memory_limit', '-1');
$dbdir="/home/caicedo/data/cnnPatches/results/";
$fparams=$_GET["fparams"];
$category=$_GET["cat"];
$evalOv = $_GET["ov"];
if($evalOv != '') $evalOv .= ".";
$basedir=explode("/",$fparams); $basedir = $basedir[0];
//aeroplane_00_0.01_0.1_0.2.out.result
$dpm = explode("\n",file_get_contents($dbdir."/".$category.$fparams.".out.".$evalOv."result"));
$dpmAP = number_format(str_replace("AP=","",$dpm[count($dpm)-1]),4);
?>
<html>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

    // Create and populate the data table.
    var data = new google.visualization.DataTable();
    data.addColumn('number', 'X');
    data.addColumn('number', 'Average Precision = <?=$dpmAP?>');

<?
  echo "var dpmTotal = ".(count($dpm)-2).";";
  for($i=0; $i<count($dpm)-1; $i+=5){
    if($dpm[$i] != ""){
      $point = explode(" ",$dpm[$i]);
      echo "data.addRow([".number_format($point[0],4).", ".number_format($point[1],4)."]);\n";
    }
  }
?>
    // Create and draw the visualization.
    var chart = new google.visualization.ScatterChart(
        document.getElementById('visualization'));


    // The select handler. Call the chart's getSelection() method
    function selectHandler() {
      var selectedItem = chart.getSelection()[0];
      if (selectedItem) {
        //var value = data.getValue(selectedItem.row, selectedItem.column);
        var value = data.getValue(selectedItem.row, 0);
        var row = selectedItem.row < dpmTotal? selectedItem.row : selectedItem.row-dpmTotal;
        document.getElementById('measures').src="measures.php?cat=<?=$category?>&fparams=<?=$fparams?>&recall="+value+"&row="+row+"&col="+selectedItem.column+"&ov=<?=$evalOv?>";
        //alert('The user selected ' + value);
      }
    }
    // Listen for the 'select' event, and call my function selectHandler() when
    // the user selects something on the chart.
    google.visualization.events.addListener(chart, 'select', selectHandler);

    chart.draw(data, {title: 'DETECTION PERFORMANCE. RECALL-PRECISION CURVE',
                      width: 600, height: 300,
                      vAxis: {title: "Precision", titleTextStyle: {color: "green"}, viewWindow: {max: 1}, baseline: 0.0},
                      hAxis: {title: "Recall", titleTextStyle: {color: "green"}, viewWindow: {max: 1}},
                      pointSize: 1}
              );

      }
    </script>
  <style>
.shadow {
  -moz-box-shadow:    3px 3px 5px 6px #ccc;
  -webkit-box-shadow: 3px 3px 5px 6px #ccc;
  box-shadow:         3px 3px 5px 6px #ccc;
}
body
{
    font-family : Arial;
}
td{ 
text-align: center; 
}
  </style>
  </head>
  <body>
    <center>
    <h3>Category: <?=strtoupper($category)?></h3>
    <br>
    <div id="visualization" style="width: 600px; height: 300px;"></div>
    <div style="border:1px dashed; border-radius:10px; width:600px; background-color: #F2F5A9" align="center">
    Click any point in the plot to view details in the table.<br>
    Solid boxes are true positives. Dashed boxes are false positives.<br>
    Hover over thumbnails to see overlap with ground-truth.<br>
    Click an image to see the predicted box in context.
    </div>
    <iframe id="measures" src="measures.php?cat=<?=$category?>&fparams=<?=$fparams?>&ov=<?=$evalOv?>" width="100%" height="250px"></iframe>
    </center>
  </body>
</html>
