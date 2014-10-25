function BoxCompare()
{
    // IOU score with gt to accept a query box
    this.acceptThresh = 0.95;
    // IOU score with gt that indicates a query box is co-locating with a gt
    this.isOverGTThresh = 0.3;
    // IOU score with gt that indicates the query box only needs edge adjustment
    this.adjustBoxThresh = 0.7;
    // Pixel distance between box centers that indicates two boxes are co-locating
    this.centerDistThresh = 10;
    // Intersect over area between a query box and a gt box that indicates a
    // query box needs to be shrunk (query box area used for shrink and gt box
    // area used for growing).
    this.growShrinkThresh = 0.1;
    // Set of gt boxes
    this.gtBoxes = [];
    // User feedback indicating the active query box should be expanded.
    this.growBox = "Grow";
    // User feedback indicating the active query box should be shrunk.
    this.shrinkBox = "Shrink";
    // User feedback indicating the active query box should be moved over a gt.
    this.moveOverObject = "Move";
    // User feedback indicating a new query box should be created.
    this.addBox = "Add New Box";
    // User feedback indicating the active query box only needs adjustment.
    this.adjustBox = "Adjust";
    // User feedback indicating a query box should be deleted.
    this.deleteBox = "Delete Box";
    // Key used to indicate there is a good query box for all gt boxes.
    this.acceptKey = "accept";
    // User feedback indicating a different query box should be activated.
    this.clickOnBox = "ClickOnBox";
    // Reset on each feedback request, indicates if a good query box was found
    // for the gt of the same index.
    this.goodBox = [];
    // Indicates which query box was matched to the gt box.
    this.queryMatch = []
}

function areCentersClose(queryBox,gtBox,thresh)
{
    var queryCenter = getBoxCenter(queryBox);
    var gtCenter = getBoxCenter(gtBox);
    var threshSquared = thresh*thresh;
    var centDist = Math.pow(queryCenter[0]-gtCenter[0],2)+Math.pow(queryCenter[1]-gtCenter[1],2);
    return (centDist <= threshSquared);
}

function coords(b) {
  return [b[0], b[1], b[2]+b[0], b[3]+b[1]];
  //return b;
}


function getBoxArea(box)
{
    box = coords(box);
    return ((box[2]-box[0])*(box[3]-box[1]));
}

function getBoxCenter(box)
{
    box = coords(box);
    return [(box[2]-box[0])/2,(box[3]-box[1])/2];
}

function getIntersectArea(queryBox,gtBox)
{
    queryBox = coords(queryBox);
    gtBox = coords(gtBox);
    var x1 = Math.max(queryBox[0],gtBox[0]);
    var y1 = Math.max(queryBox[1],gtBox[1]);
    var x2 = Math.min(queryBox[2],gtBox[2]);
    var y2 = Math.min(queryBox[3],gtBox[3]);
    var w = x2-x1;
    var h = y2-y1;
    if (w > 0 && h > 0)
        return (w*h);
    else
        return 0;
}

function getIOU(queryBox,gtBox)
{
    var intersectArea = getIntersectArea(queryBox,gtBox);

    if (intersectArea == 0) {
        return 0;
    }
    else {
        var queryBoxArea = getBoxArea(queryBox);
        var gtBoxArea = getBoxArea(gtBox);
        return (intersectArea/(gtBoxArea - intersectArea + queryBoxArea));
    }
}


BoxCompare.prototype.acceptBox = function(queryBox)
{
    for (var i = 0; i < this.gtBoxes.length; i++) {
        if (getIOU(queryBox,this.gtBoxes[i]) >= this.acceptThresh) {
            return i;
        }
    }

    return -1;
}

BoxCompare.prototype.setGTBoxes = function(boxes)
{
    if (boxes[0] instanceof Array) {
        this.gtBoxes = boxes;
    }
    else {
        this.gtBoxes = new Array();
        this.gtBoxes.push(boxes);
    }
}

BoxCompare.prototype.getAcceptKey = function()
{
    return this.acceptKey;
}

BoxCompare.prototype.getAcceptedBoxCnt = function()
{
    var cnt = 0;
    for (var i = 0; i < this.goodBox.length; i++) {
        if (this.goodBox[i]) {
            cnt++;
        }
    }

    return cnt;
}

BoxCompare.prototype.getFeedback = function(queryBox,activeBoxIdx)
{
    var bestIdx = -1;
    var overlap = 0;
    for (var i = 0; i < this.gtBoxes.length; i++) {
        var iou = getIOU(queryBox,this.gtBoxes[i]);
        if (iou > overlap) {
            overlap = iou;
            bestIdx = i;
        }
    }


    if (bestIdx < 0) {
        return this.moveOverObject;
    }

    if (this.goodBox[bestIdx]) {
	if (this.queryMatch[bestIdx] == activeBoxIdx) {
	    return this.clickOnBox;
	}
	else {
	    return this.moveOverObject;
	}
    }
    
    if (overlap >= this.adjustBoxThresh) {
        return this.adjustBox;
    }   
    
    var gtBox = this.gtBoxes[bestIdx];
    var intersection = getIntersectArea(queryBox,gtBox);
    var queryBoxArea = getBoxArea(queryBox);
    if ((intersection/queryBoxArea) >= this.growShrinkThresh) {
        return this.growBox;
    }
    
    var gtBoxArea = getBoxArea(gtBox);
    if ((intersection/gtBoxArea) >= this.growShrinkThresh) {
        return this.shrinkBox;
    }
    
    var isOverGt = areCentersClose(queryBox,gtBox,this.centerDistThresh);
    if (overlap < this.isOverGTThresh && !isOverGt) {
        return this.moveOverObject;
    }
    
    return this.adjustBox;
}

BoxCompare.prototype.getBoxesFeedback = function(queryBoxes,activeBoxIdx)
{
    this.goodBox = new Array(this.gtBoxes.length);
    if (queryBoxes.length == 0) {
        return this.addBox;
    }

    if (!(queryBoxes[0] instanceof Array)) {
        var temp = new Array();
        temp.push(queryBoxes);
        queryBoxes = temp;
    }

    this.queryMatch = new Array(queryBoxes.length);

    if (queryBoxes.length > this.gtBoxes.length) {
        return this.deleteBox;
    }

    for (var i = 0; i < queryBoxes.length; i++) {
        var boxIdx = this.acceptBox(queryBoxes[i]);
        if (boxIdx >= 0) {
	    this.queryMatch[boxIdx] = i;
            this.goodBox[boxIdx] = true;
        }
    }

    if (this.getAcceptedBoxCnt() == this.gtBoxes.length) {
        return this.acceptKey;
    }
    
    if (activeBoxIdx >= 0) {
        return this.getFeedback(queryBoxes[activeBoxIdx],activeBoxIdx);
    }
    
    return this.clickOnBox;
}
