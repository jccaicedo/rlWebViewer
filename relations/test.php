<!DOCTYPE HTML>
<html>
  <head>
    <style>
      body {
        margin: 0px;
        padding: 0px;
      }
    </style>
  </head>
  <body>
    <div id="container"></div>
    <script src="http://d3lp1msu2r81bx.cloudfront.net/kjs/js/lib/kinetic-v5.0.1.min.js"></script>
    <script defer="defer">
      function addNode(obj, layer) {
        var node = new Kinetic.Circle({
          x: obj.x,
          y: obj.y,
          radius: 2,
          fill: obj.color,
          id: obj.id,
          transformsEnabled: 'position'
        });

        layer.add(node);
      }
      var stage = new Kinetic.Stage({
        container: 'container',
        width: 800,
        height: 600
      });

      var tooltipLayer = new Kinetic.Layer();
      var dragLayer = new Kinetic.Layer();
      
      var tooltip = new Kinetic.Label({
        opacity: 0.75,
        visible: false,
        listening: false
      });
      
      tooltip.add(new Kinetic.Tag({
        fill: 'black',
        pointerDirection: 'down',
        pointerWidth: 10,
        pointerHeight: 10,
        lineJoin: 'round',
        shadowColor: 'black',
        shadowBlur: 10,
        shadowOffset: {x:10, y:10},
        shadowOpacity: 0.2
      }));
      
      tooltip.add(new Kinetic.Text({
        text: '',
        fontFamily: 'Calibri',
        fontSize: 18,
        padding: 5,
        fill: 'white'
      }));
      
      tooltipLayer.add(tooltip);
      
      // build data
      var data = [];
      var width = stage.width();
      var height = stage.height();
      var colors = ['red', 'orange', 'cyan', 'green', 'blue', 'purple'];
      for(var n = 0; n < 20000; n++) {
        var x = Math.random() * width;
        var y = Math.random() * height; //height + (Math.random() * 200) - 100 + (height / width) * -1 * x;
        data.push({
          x: x,
          y: y,
          id: n,
          color: colors[Math.round(Math.random() * 5)]
        });
      }

      // render data
      var nodeCount = 0;
      var layer = new Kinetic.Layer();
      for(var n = 0; n < data.length; n++) {
        addNode(data[n], layer);
        nodeCount++;
        if(nodeCount >= 1000) {
          nodeCount = 0;
          stage.add(layer);
          layer = new Kinetic.Layer();
        }
      }

      stage.add(dragLayer);
      stage.add(tooltipLayer);

      stage.on('mouseover mousemove dragmove', function(evt) {
        var node = evt.targetNode;
        if (node) {
          // update tooltip
          var mousePos = node.getStage().getPointerPosition();
          tooltip.position({x:mousePos.x, y:mousePos.y - 5});
          tooltip.getText().text("node: " + node.id() + ", color: " + node.fill());
          tooltip.show();
          tooltipLayer.batchDraw();
        }
      }); 

      stage.on('mouseout', function(evt) {
        tooltip.hide();
        tooltipLayer.draw();
      });
      
      var startLayer;
      
      stage.on('mousedown', function(evt) {
        var shape = evt.targetNode;
        if (shape) {
          startLayer = shape.getLayer();
          shape.moveTo(dragLayer);
          startLayer.draw();
          // manually trigger drag and drop
          shape.startDrag();
        }
      });
      
      stage.on('mouseup', function(evt) {
        var shape = evt.targetNode;
        if (shape) {
          shape.moveTo(startLayer);
          dragLayer.draw();
          startLayer.draw();
        }
      });

      stage.on('click', function(evt) {
        var shape = evt.targetNode;
        if (shape) {
          alert('Hi'+shape.id());
          //shape.moveTo(startLayer);
          //dragLayer.draw();
          //startLayer.draw();
        }
      });


    </script>
  </body>
</html>
