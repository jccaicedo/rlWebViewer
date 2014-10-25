$(window).resize(checkSize);

function checkSize()
{
    var w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    width = w.innerWidth || e.clientWidth || g.clientWidth,
    height = w.innerHeight|| e.clientHeight|| g.clientHeight;
    var maxWidth = 1024;
    var minWidth = 300;
    var maxHeight = 1024;
    var minHeight = 250;
    if (width < minWidth) {
	width = minWidth;
    }
    if (height < minHeight) {
	height = minHeight;
    }
    if (width > maxWidth) {
	width = maxWidth;
    }
    if (height > maxHeight) {
	height = maxHeight;
    }
    
    var minPromptW = 200;
    var minPromptInstructH = 300;
    /*if (imDims.length != 0) {
        if ((minPromptInstructH+imDims[queryIdx][1]) > (height + height*0.1)) {
	    height = minPromptInstructH+imDims[queryIdx][1];
	    height = height + height*0.1;
        }
	if ((minPromptW+imDims[queryIdx][0]) > (width + width*0.03)) {
	    width = minPromptW+imDims[queryIdx][0];
	    width = width + width*0.03;
        }
    }*/

    //alert(width + ' ' + height);
    //updateWindowWidth(width);
    //updateWindowHeight(height);
    //updateAllFontSizes();
}

function updateAllFontSizes()
{
    var containerId = "captionDiv";
    var textIds = ["captionText"];
    var elementIds = ['text.sm'];
    var initSizes = [20];
    var margin = 0;
    //updateTextContainer(containerId,textIds,elementIds,initSizes,margin);
    containerId = "instructionsDiv";
    textIds = ["instructionsTable"];
    elementIds = ["#guideTag",'text.title','p.guide','p.falseGuide','p.trueGuide'];
    initSizes = [22,24,22,20,20];
    updateTextContainer(containerId,textIds,elementIds,initSizes,margin);
    containerId = "promptDiv";
    textIds = ["promptText"];
    elementIds = ['text.query'];
    initSizes = [20];
    updateTextContainer(containerId,textIds,elementIds,initSizes,margin);
    
    if (document.getElementById("explanationDiv")) {
	containerId = "instructionsDiv";
	textIds = ["instructionsTable"];
	elementIds = ["#explanationText"];
	initSizes = [20];
	marginSize = 25;
	updateTextContainer(containerId,textIds,elementIds,initSizes,margin);
    }
}

function getCurrentTextSize(textIds,margin)
{
    for (var i = 0; i < textIds.length; i++) {
	var h = document.getElementById(textIds[i]).offsetHeight;
	if (!isNaN(h)) {
	    margin += h;
	}
    }
    return margin;
}

function updateTextContainer(containerId,textIds,elementIds,initSizes,margin)
{
    var width = document.getElementById(containerId).clientWidth;
    var height = document.getElementById(containerId).clientHeight;
    for (var i = 0; i < elementIds.length; i++) {
	$(elementIds[i]).css('fontSize',initSizes[i]);
    }
    while (getCurrentTextSize(textIds,margin) >= height) {
	for (var i = 0; i < elementIds.length; i++) {
	    var fontSize = $(elementIds[i]).css('fontSize');
	    if (fontSize != undefined) {
		fontSize = parseInt(fontSize);
		fontSize--;
		$(elementIds[i]).css('fontSize',fontSize);
	    }
	}
    }
}

function updateUserControlsWidths(controlWidth)
{
    var currVals = document.getElementById("prev").offsetWidth +
        document.getElementById("save").offsetWidth + 0;
    //document.getElementById("msgDiv").style.width = controlWidth - currVals;
}

function updateWindowHeight(height)
{
    document.getElementById("pageDiv").style.height = height;
    
    var instructionsPortion = 0.20;
    var questionPortion = (1 - instructionsPortion)*height;
    document.getElementById("instructionsDiv").style.height = instructionsPortion*height;
    
    document.getElementById("imageDiv").style.height = 500;
    var captionPortion = 0.0;
    var promptPortion = 0.75;
    var userControlsPortion = 1 - captionPortion - promptPortion;
    
    if (document.getElementById("explanationDiv")) {
	var explainationPortion = 0.25;
	userControlsPortion -= explainationPortion;
	promptPortion -= 10;
	captionPortion -= 5;
	document.getElementById("explanationDiv").style.height = questionPortion*explainationPortion;
    }
    
    //updateHeight("captionDiv",questionPortion*captionPortion,150);
    updateHeight("promptDiv",questionPortion*(promptPortion),350);
    updateHeight("userControlsDiv",questionPortion*userControlsPortion,75);
}

function updateHeight(id,height,maxHeight)
{
    if (height > maxHeight) {
        height = maxHeight;
    }
    document.getElementById(id).style.height = height;
}

function updateWindowWidth(width)
{
    document.getElementById("pageDiv").style.width = width;
    document.getElementById("instructionsDiv").style.width = width;
    document.getElementById("mainTable").style.width = width;
    
    var marginPortion = 0.025;
    var imgWidth = 500;
    var promptPortion = (1 - marginPortion)*width - imgWidth - 20;
    document.getElementById("imageDiv").style.width = imgWidth;
    document.getElementById("imageMarginDiv").style.width = marginPortion*width;
    if (document.getElementById("explanationDiv")) {
	document.getElementById("explanationDiv").style.width = promptPortion;
    }
    //document.getElementById("captionDiv").style.width = promptPortion;
    document.getElementById("promptDiv").style.width = promptPortion;
    document.getElementById("userControlsDiv").style.width = promptPortion;
    
    updateUserControlsWidths(promptPortion);
}
