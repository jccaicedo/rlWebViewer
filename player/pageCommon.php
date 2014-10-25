<?php

function makePage()
{
  echo '<table id="mainTable"><tr><td align="center">';
  makeImage();
  echo '</td><td><div id="imageMarginDiv"></div></td><td valign="top">';
  
  makePromptAndCanvas("#FF00FF");
  makeMainUserControls();
  
    echo '<a id="addComments" href="#comments" onclick="showCommentBox()">Add Comments (Optional)</a><br><br>';
    echo '<label for="comments" id="commentsLabel" style="font-size:12pt;display:none;">Comment on this task (Optional):</label>';
    echo '<textarea id="comments" rows="7" cols="60" style="font-size:10pt;display:none;"></textarea>';
    echo '</td></tr></table>';
  echo '</td></tr></table>';
}

function makePromptAndCanvas($queryWordColor)
{
  echo '<div id="promptDiv" class="right">';
  makeCaption();
  echo '<p id="promptText" align="center">'; //class="queryTag" 
  echo '<text class="query">Draw a bounding box for: </text><br>';
  echo '<text class="query" id="queryWord" style="color:',$queryWordColor,'"></text>';
  echo '<table align="center"><tr><td>';
  echo '<input type="button" id="newBox" value="Add Box" onclick="addNewBox()" class="tool">';
  echo '</td><td>';
  echo '</td><td>';
  echo '<input type="button" id="delBox" value="Delete Box" onclick="deleteBox()" class="tool">';
  echo '</td></tr><tr><td colspan="3" align="center">';
  echo '<input type="checkbox" id="NoBox" name="NoBox"><label><b>Check here</b> if the phrase can\'t be observed in the image or if every instance already has a bounding box</label>';
  echo '</td></tr><tr><td>';
  echo '<input type="button" id="prev" value="Prev" onclick="prevExample()" class="tool">';
  echo '</td><td>';
  echo '<input type="button" id="done" value="Done" onclick="giveTrainingFeedback();" class="tool" style="display:none;">';
  echo '</td><td>';
  echo '<input type="button" id="save" value="Next" onclick="nextExample();" class="tool">';
  echo '</td></tr></table></p>';
  echo '</div>';

}

function makeImage()
{
  echo '<div class="Canvas" id="imageDiv"><img id="displayImage" src=""><canvas id="canvas"></canvas></div>';
}

function makeCaption()
{
  echo '<p id="captionText">';
  $maxNSentences = 5;
  echo "<i>Image Caption</i>: ";
  for($i = 0; $i < $maxNSentences; $i++){
    echo '</text><text id="sent',$i,'" class="sm"></text>';
  }
  echo '</p>';
}

function makeMainUserControls()
{
  echo '<div id="userControlsDiv" class="right"><table width="100%"><tr><td align="left" width="100%">';
  echo '<div id="msgDiv"><text id="UserMsg" style="color:#FF3030;font-size:18pt"></text></div>';
  echo '</td></tr><tr><td align="left" width="100%">';
  echo '<b></text><text id="progress" style="font-size:20pt;"></text> annotations.</b>';
  echo '</td></tr></table></div>';

}

?>
