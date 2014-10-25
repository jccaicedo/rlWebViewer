function changeImage(im,dims) 
{
    document.getElementById("displayImage").src = "";
    document.getElementById("displayImage").src = "showImg.php?name=" + im + ".jpg";
    document.getElementById("canvas").width = dims[0];
    document.getElementById("canvas").height = dims[1];
}

function displayErrorMsg(msg)
{ 
    document.getElementById("UserMsg").innerHTML = msg;

    var width = document.getElementById("msgDiv").clientWidth;
    if (width > 200) {
	document.getElementById("UserMsg").style.fontSize = 18;
    }
    else {
	document.getElementById("UserMsg").style.fontSize = 16;
    }
}

function gatherResults()
{
    document.getElementById('pageResults').value = '';
    var results = new Array();
    for (var i = 0; i < responses.length; i++) {
	results.push( { 'img':questions[i].image, 'phrase':questions[i].span, 'response':responses[i],'coms':workerComments[i]} );
    }
    document.getElementById('pageResults').value = JSON.stringify(results);
    return true;
}

function updateExplanation()
{
    document.getElementById("explanationText").style.display = "none";
    document.getElementById("explanationText").style.visibility = "invisible";
    document.getElementById("explainLabel").style.display = "none";
    document.getElementById("explainLabel").style.visibility = "invisible";
}

function getTrainingMsg()
{
    var msg = '';
    if (questions[queryIdx].expected instanceof Array && responses[queryIdx] instanceof Array) {
	// some IOU stuffs
	iou = getIOU(responses[queryIdx], questions[queryIdx].expected);
        if (iou < 0.8 && iou >= 0.6) msg = "Your box is not tight enough.";
        else if (iou < 0.6) msg = "Your box is not covering the target object well.";
    }
    else if (questions[queryIdx].expected instanceof Array && typeof(responses[queryIdx]) == "string") {
        msg = "You need to draw a box for phrases like this.";
    }
    else if (typeof(questions[queryIdx].expected) == "string" && typeof(responses[queryIdx]) == "string")
        msg = questions[queryIdx].explanation;
    else if (typeof(questions[queryIdx].expected) == "string")
	msg = questions[queryIdx].explanation;
    else
	msg = "Phrase can be seen in the image.";
    return msg;
}

function giveTrainFeedback(isAnswerCorrect, message)
{
    document.getElementById("explanationText").style.display = "block";
    document.getElementById("explanationText").style.visibility = "visible";
    document.getElementById("explainLabel").style.display = "initial";
    document.getElementById("explainLabel").style.visibility = "visible";
    document.getElementById("explanationText").className = isAnswerCorrect;
    document.getElementById("explainLabel").innerHTML = message;
    if (isTraining) {
	document.getElementById("explanationText").innerHTML = getTrainingMsg() + " " + questions[queryIdx].explanation;
	drawExampleBoxes();
    }
    else
	document.getElementById("explanationText").innerHTML = questions[queryIdx].explanation;

    if (isAnswerCorrect)
	document.getElementById("explainLabel").style.color = "green";
    else
	document.getElementById("explainLabel").style.color = "red";
    displayErrorMsg('');
}

function isResponseCorrect()
{
    if (questions[queryIdx].expected instanceof Array && responses[queryIdx] instanceof Array) {
        iou = getIOU(responses[queryIdx], questions[queryIdx].expected);
        return iou >= 0.8;
    }
    else if (typeof(questions[queryIdx].expected) == "string" && typeof(responses[queryIdx]) == "string")
	return responses[queryIdx] == questions[queryIdx].expected;
    return false;
}

function updateTrainingButtonStatus()
{
    if (isTraining) {
	var isQuestionAnswered = responses[queryIdx] != undefined;
	var isBox = false;
	document.getElementById("done").style.display = "block";
	if (isQuestionAnswered)
	    isBox = responses[queryIdx] instanceof Array;
	$('#save').prop('disabled',!isQuestionAnswered);
	$('#done').prop('disabled',isQuestionAnswered);
	$('#newBox').prop('disabled',isQuestionAnswered);
	$('#delBox').prop('disabled',isQuestionAnswered);
	$('#NoBox').prop('disabled',isQuestionAnswered);
    }
}

function giveTrainingFeedback()
{
    if (validInput()) {
	saveNoBoxResponseAndComments();
	var isCorrect = true;
	var msg = "Comments";
	if (isTraining) {
	    msg = "That's Correct!";
	    isCorrect = isResponseCorrect();
	    if (!isCorrect)
		msg = "Sorry that's incorrect.";
	}
	giveTrainFeedback(isCorrect,msg);
	var isQuestionAnswered = responses[queryIdx] != undefined;
	$('#NoBox').attr('disabled',isQuestionAnswered);
	$('#delBox').attr('disabled',isQuestionAnswered);
	$('#newBox').prop('disabled',isQuestionAnswered);
	updateTrainingButtonStatus();
    }
}

function prevQuestion()
{
    if (queryIdx > 0) {
	if (areBothOptionsSelected())
	    return false;
	saveNoBoxResponseAndComments();
	queryIdx--;
	updateNextPrevButtons();
	return true;
    }
    
    displayErrorMsg("At first question");
    return false; 
}

function prevExample()
{
    if (prevQuestion()) {
	if (responses[queryIdx] != undefined)
	    giveTrainingFeedback();
	else
	    updateExplanation();
	$('#save').attr('disabled',false);
	updateTrainingButtonStatus();
    }
}

function saveNoBoxResponseAndComments()
{
    if (document.getElementById('comments'))
	workerComments[queryIdx] = document.getElementById('comments').value;
    if ($('#NoBox').prop('checked'))
        responses[queryIdx] = 'nobox';
}

function areBothOptionsSelected()
{
    if ($('#NoBox').prop('checked') && responses[queryIdx] instanceof Array) {
	displayErrorMsg("Please delete the drawn box or unselect the 'no box' checkbox");
	return true;
    }

    return false;
}

function validInput()
{
    if (areBothOptionsSelected()) {
        return false;
    }
    
    if (!$('#NoBox').prop('checked') && responses[queryIdx] == undefined) {
	displayErrorMsg("You must make a selection to continue");
	return false;
    }
    
    if (newBoxEditions <= 1 && !$('#NoBox').prop('checked')) {
        displayErrorMsg("Please move and resize the new box to cover an object");
        return false;
    }

    return true;
}

function nextQuestion()
{
    if (validInput()) {
        saveNoBoxResponseAndComments();
        queryIdx++;
        if (queryIdx == questions.length)
	    $("#mainForm").submit();
        else {
	    updateNextPrevButtons();
	    updateTrainingButtonStatus();
	}

	return true;
    } 

    return false;
}

function nextExample()
{
    if (nextQuestion() && queryIdx < questions.length) {
	if (responses[queryIdx] == undefined)
	    updateExplanation();
	else
	    giveTrainingFeedback();

	var atLastExample = !isTraining && (queryIdx+1) == questions.length;
	$('#save').attr('disabled',atLastExample);
	//updateTrainingButtonStatus();
    }
    return true;
}

function updateNextPrevButtons()
{
    var sentenceList = questions[queryIdx].sentenceList;
    for (var i = 0; i < sentenceList.length; i++)
	document.getElementById("sent"+i).innerHTML = sentenceList[i];
    displayErrorMsg('');
    if (document.getElementById('comments'))
	document.getElementById('comments').value = workerComments[queryIdx];
    $('#NoBox').prop('checked',responses[queryIdx] == 'nobox');
    changeImage(questions[queryIdx].image,questions[queryIdx].imDims);
    drawBoxes();
    var isBoxDrawn = responses[queryIdx] instanceof Array;
    $('#newBox').attr('disabled',isBoxDrawn);
    $('#delBox').attr('disabled',!isBoxDrawn);
    if (isBoxDrawn)
	newBoxEditions = 2;
    else
	newBoxEditions = 0;
    updateSpanColors();
    updateProgress();
}

function initAnnotationArray()
{
    for (var i = 0; i < responses.length; i++) {
	responses[i] = undefined;
	workerComments.push('');
    }
}

function copyResponses()
{
    for (var i = 0; i < questions.length; i++)
	responses[i] = questions[i].expected;
}

function updateSpanColors()
{
    // Highlight associated text in sentences
    var spanArray = document.getElementsByTagName('span');
    var number_spans = spanArray.length ;
    for( var i = 0; i < number_spans ; i++ ){
	spanArray[i].style.color = "black";
	spanArray[i].parentNode.style.display = "none";
    }
    var spanList = questions[queryIdx].span;
    var redSpan = document.getElementById(spanList);
    redSpan.parentNode.style.display = "block";
    redSpan.style.color = "#FF00FF";
    document.getElementById("queryWord").innerHTML = "\""+redSpan.innerHTML+"\"";
}

function updateProgress()
{
    document.getElementById("progress").innerHTML = (queryIdx+1)+" of "+questions.length;
    if (queryIdx == questions.length-1)
	document.getElementById("save").value = "Submit";
    else
	document.getElementById("save").value = "Next";
}
