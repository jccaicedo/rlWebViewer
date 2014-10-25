function addNewBox()
{
    responses[queryIdx] = [10,10,100,100];
    $('#newBox').attr('disabled',true);
    $('#delBox').attr('disabled',false);
    drawBoxes();
    newBoxEditions = 0;
}

function getBoxColor(idx)
{
    var boxColor = ['rgba(0, 255, 255, 0.25)','rgba(0, 0, 255, 0.25)', 'rgba(255, 0, 255, 0.25)', 'rgba(0, 128, 0, 0.25)', 'rgba(0, 255, 0, 0.25)', 'rgba(128, 0, 0, 0.25)', 'rgba(0, 0, 128, 0.25)', 'rgba(128, 128, 0, 0.25)', 'rgba(128, 0, 128, 0.25)', 'rgba(255, 0, 0, 0.25)', 'rgba(192, 192, 192, 0.25)', 'rgba(0, 128, 128, 0.25)', 'rgba(255, 255, 0, 0.25)'];
    var borderColor = ['rgba(0, 255, 255, 0.35)','rgba(0, 0, 255, 0.35)', 'rgba(255, 0, 255, 0.35)', 'rgba(0, 128, 0, 0.35)', 'rgba(0, 255, 0, 0.35)', 'rgba(128, 0, 0, 0.35)', 'rgba(0, 0, 128, 0.35)', 'rgba(128, 128, 0, 0.35)', 'rgba(128, 0, 128, 0.35)', 'rgba(255, 0, 0, 0.35)', 'rgba(192, 192, 192, 0.35)', 'rgba(0, 128, 128, 0.35)', 'rgba(255, 255, 0, 0.35)'];
    var colorIdx = idx % boxColor.length;
    return [boxColor[colorIdx],borderColor[colorIdx]];
}

function changeMouseCursor(xy)
{
    var left = isValidDragSide(xy[0],0,xy[1],1,3);
    var up = isValidDragSide(xy[1],1,xy[0],0,2);
    var right= isValidDragSide(xy[0],2,xy[1],1,3);
    var down = isValidDragSide(xy[1],3,xy[0],0,2);
    var isOverBox = isUserBoxSelected(xy);
    updateCursor(left,right,up,down,isOverBox);
} 

function deleteBox()
{
    responses[queryIdx] = undefined;
    $('#newBox').attr('disabled',false);
    $('#delBox').attr('disabled',true);
    newBoxEditions = 10;
    drawBoxes();
}

function drawExampleBoxes()
{
    var exampleAnswer = questions[queryIdx].expected;
    if (exampleAnswer instanceof Array) {
	var offset = questions[queryIdx].boxes.length + 1;
	showBox(exampleAnswer,getBoxColor(offset),false);
    }
}

function drawBoxes()
{
    var c = document.getElementById("canvas");
    var ctx = c.getContext("2d");
    ctx.clearRect(0, 0, c.width, c.height);
    var boxes = questions[queryIdx].boxes;
    for (var i = 0; i < boxes.length; i++)
	showBox(boxes[i],getBoxColor(i+1),false);
    if (responses[queryIdx] instanceof Array) {
	var minSize = 8;
	if (responses[queryIdx][2] < minSize)
	    responses[queryIdx][2] = minSize;
	if (responses[queryIdx][3] < minSize)
	    responses[queryIdx][3] = minSize;
	showBox(responses[queryIdx],getBoxColor(0),true);

    }
}

function getXYFromEvent(evt)
{
    var x = new Number();
    var y = new Number();
    var canvas = document.getElementById("canvas");
    /*if (evt.x != undefined && evt.y != undefined) {
	x = evt.x;
        y = evt.y;
    }
    else // Firefox method to get the position
    {*/
        x = evt.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
        y = evt.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    //}
    
    x -= canvas.offsetLeft;
    y -= canvas.offsetTop;
    //document.getElementById('capt0').innerHTML = x+','+y + ' ' + evt.clientX + '::' + evt.clientY;
    return [x,y];
}

function isCloseEnough(pt,sidePos)
{
    var offset = 4;
    return pt >= (sidePos-offset) && pt <= (sidePos+offset);
}

function isUserBoxSelected(xy)
{
    if (responses[queryIdx] instanceof Array) {
        if (isTraining && $('#done').prop('disabled') == true) {
          return false;
        }
	var box = responses[queryIdx];
	if (box[0] <= xy[0] && (box[0]+box[2]) >= xy[0]) {
	    return box[1] <= xy[1] && (box[1]+box[3]) >= xy[1];
	}  
    }
    
    return false;
}

function isValidDragSide(pt,side,otherPt,limitA,limitB)
{
    if (responses[queryIdx] instanceof Array) {
        if (isTraining && $('#done').prop('disabled') == true) {
          return false;
        }
	var sidePos = responses[queryIdx][side];
	if (side > 1) {
	    sidePos += responses[queryIdx][side-2];
	}
	limitA = responses[queryIdx][limitA];
	limitB = responses[queryIdx][limitB] + limitA;
	if (isCloseEnough(pt,sidePos) && limitA <= otherPt && otherPt <= limitB)
	    return true;
    }
    return false;
}

function onMouseDown(evt)
{
    var xy = getXYFromEvent(evt);
    drawLeft = isValidDragSide(xy[0],0,xy[1],1,3);
    drawUp = isValidDragSide(xy[1],1,xy[0],0,2);
    drawRight = isValidDragSide(xy[0],2,xy[1],1,3);
    drawDown = isValidDragSide(xy[1],3,xy[0],0,2);
    if (!drawLeft && !drawUp && !drawRight && !drawDown) {
	moveBox = isUserBoxSelected(xy);
	startX = xy[0];
	startY = xy[1];
    }
    
    updateCursor(drawLeft,drawRight,drawUp,drawDown,moveBox);
    drawBoxes();
}

function onMouseMove(evt)
{
    var xy = getXYFromEvent(evt);
    if (drawLeft || drawRight || drawUp || drawDown) {
        newBoxEditions += 1;
	var box = responses[queryIdx];
	var minVal = 5;
	if (drawLeft) {
	    var newW = box[2]+box[0]-xy[0];
	    if (newW > minVal) {
		box[2] = newW;
		box[0] = xy[0];
	    }
	}
	if (drawRight) {
	    if (xy[0] > box[0]) {
		box[2] = xy[0]-box[0];
	    }
	}
	if (drawUp) {
	    var newH = box[3]+box[1]-xy[1];
	    if (newH > minVal) {
		box[3] = newH;
		box[1] = xy[1];
	    }
	}
	if (drawDown) {
	    if (xy[1] > box[1]) {
		box[3] = xy[1]-box[1];
	    }
	}
	if (box[3] < minVal) {
	    box[3] = minVal;
	}
	if (box[2] < minVal) {
	    box[2] = minVal;
	}
	
	drawBoxes();
    }
    else if (moveBox) {
	var box = responses[queryIdx];
	box[0] += xy[0]-startX;
	box[1] += xy[1]-startY;
	startX = xy[0];
	startY = xy[1];
	drawBoxes();
    }
    
    changeMouseCursor(xy);
}

function onMouseUp(evt)
{
    drawLeft = false;
    drawRight = false;
    drawUp = false;
    drawDown = false;
    moveBox = false;
    updateCursor(drawLeft,drawRight,drawUp,drawDown,moveBox);
}

function showBox(box,colors,isUserBox)
{
    var c = document.getElementById("canvas");
    var ctx = c.getContext("2d");
    if (isUserBox) {
	ctx.fillStyle = colors[0];
	ctx.fillRect(box[0],box[1],box[2],box[3]);
    }
    if (isUserBox) {
        ctx.strokeStyle = colors[1]; //'rgba(255, 255, 255, 1.0)';
	ctx.lineWidth = 2;
    }
    else {
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.75)';
        ctx.lineWidth = 2;
    }
    ctx.strokeRect(box[0],box[1],box[2],box[3]);
}

function updateCursor(left,right,up,down,isOverBox)
{
    if (left && up) {
	document.getElementById("canvas").style.cursor="nw-resize";
    }
    else if (left && down) {
	document.getElementById("canvas").style.cursor="sw-resize";
    }
    else if (right && up) {
	document.getElementById("canvas").style.cursor="ne-resize";
    }
    else if (right && down) {
	document.getElementById("canvas").style.cursor="se-resize";
    }
    else if (left) {
	document.getElementById("canvas").style.cursor="w-resize";
    }
    else if (right) {
	document.getElementById("canvas").style.cursor="e-resize";
    }
    else if (up) {
	document.getElementById("canvas").style.cursor="n-resize";
    }
    else if (down) {
	document.getElementById("canvas").style.cursor="s-resize";
    }
    else {
	if (isOverBox) {
	    document.getElementById("canvas").style.cursor="move";
	}
	else {
	    document.getElementById("canvas").style.cursor="auto";
	}
    }
}
