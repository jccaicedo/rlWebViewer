<!DOCTYPE html>
<html>
<head>
<link href="style.css" rel="stylesheet" />
<meta charset=utf-8 />
<title>Cytoscape.js initialisation</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="http://cytoscape.github.io/cytoscape.js/api/cytoscape.js-latest/cytoscape.min.js"></script>
<script>
var cy;
$(function(){ // on dom ready

cy = cytoscape({
  container: document.getElementById('cy'),
  
  style: cytoscape.stylesheet()
    .selector('node')
      .css({
        //'content': 'data(id)',
        'width': '2',
        'height': '2'
      })
    .selector('edge')
      .css({
        'width': 0.3,
        'opacity': '0.666',
        'line-color': '#ddd',
        'curve-style': 'haystack'
      })
    .selector('.paths')
      .css({
        'width': 1.0,
        'opacity': '1.000',
        'line-color': '#C52FE6',
        'curve-style': 'haystack'
      })
    .selector('.current')
      .css({
        'width': '5',
        'height': '5',
        'background-color': '#FD6060',
        'line-color': '#FD6060',
        'target-arrow-color': '#FD6060',
        'transition-property': 'background-color, line-color, target-arrow-color',
        'transition-duration': '0.0s'
      })
    .selector('.highlighted')
      .css({
        'width': '5',
        'height': '5',
        'background-color': '#61bffc',
        'line-color': '#61bffc',
        'target-arrow-color': '#61bffc',
        'transition-property': 'background-color, line-color, target-arrow-color',
        'transition-duration': '0.0s'
      }),
  
  elements: <?php readfile('/home/caicedo/workspace/localization-agent/graph.txt'); ?>,
  
  layout: {
    name: 'preset',
    root: '#0',
    directed: false,
    padding: 10
  }
});

/*var i = 0;
var highlightNextEle = function(){
   cy.nodes('#' + i).addClass('highlighted');
   if(i < cy.nodes().length) {
     i++;
     setTimeout(highlightNextEle, 100);
   }
};
  
// kick off first highlight
highlightNextEle();*/
parent.graphReady();

}); // on dom ready
</script>
</head>
<body>
<div id="cy"></div>
</body>
</html>
