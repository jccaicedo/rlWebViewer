<?php
?>
<html>
  <head>
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
  <h1>Dynamic Object Detection Demo</h1>
  <h3>Instructions</h3>
  <p>Select one category from the links below. These links start a web player to reproduce the actions of an agent that localizes objects.<p>
  <p>While the player reproduces the actions, you will see several boxes drawn in the image. The following is the color convention used for these boxes:
    <ul>
    <li> Boxes with <b><font color="red">red edges</font></b> and no color filling indicate ground truth boxes. These boxes are always static.
    <li> Boxes with <b><i>white edges</i></b> are modified and adjusted by the agent. </li>
    <li> A <b><i>white box</i></b> can <b><font color="blue">light up in blue</font></b> when it is sufficiently close to an object to be considered a positive detection. </li>
    <li> When the agent labels a region with a detection flag the box becomes <b><font color="green">green if it is a positive</font></b> detection or <b><font color="red">red if it is a false</font></b> detection. </li>
    <li> All images are processed by the agent in 200 steps.</li>
    </ul>
  </p>
  <p>The player has three compartiments below the image viewer that reflect information about the episode, action values and rewards, and action feedback. 
     If you want to change the test image, just follow the links with numbers at the bottom of the page. 
     Some images are very good examples of how well the agent learned the policy and some others show interesting failure cases. </p>
  <p>The web player includes a list of a few selected examples with good localization performance, and all other test images can be found below that list.</p>

  <h3>Categories</h3>
  <ul>
    <li> <a href="boxSearchReplay.php?cat=aeroplane&img=000067" >Aeroplane</a> </li>
    <li> <a href="boxSearchReplay.php?cat=bicycle&img=000015">Bicycle</a> </li>
    <li> <a href="boxSearchReplay.php?cat=bird&img=000040">Bird</a> </li>
    <li> <a href="boxSearchReplay.php?cat=boat&img=000069">Boat</a> </li>
    <li> <a href="boxSearchReplay.php?cat=bottle&img=000144">Bottle</a> </li>
    <li> <a href="boxSearchReplay.php?cat=bus&img=000014">Bus</a> </li>
    <li> <a href="boxSearchReplay.php?cat=car&img=000004">Car</a> </li>
    <li> <a href="boxSearchReplay.php?cat=cat&img=000011">Cat</a> </li>
    <li> <a href="boxSearchReplay.php?cat=chair&img=000003">Chair</a> </li>
    <li> <a href="boxSearchReplay.php?cat=cow&img=000013">Cow</a> </li>
    <li> <a href="boxSearchReplay.php?cat=diningtable&img=000006">Dining-Table</a> </li>
    <li> <a href="boxSearchReplay.php?cat=dog&img=000001">Dog</a> </li>
    <li> <a href="boxSearchReplay.php?cat=horse&img=000010">Horse</a> </li>
    <li> <a href="boxSearchReplay.php?cat=motorbike&img=000038">Motorbike</a> </li>
    <li> <a href="boxSearchReplay.php?cat=person&img=000001">Person</a> </li>
    <li> <a href="boxSearchReplay.php?cat=pottedplant&img=000006">Potted Plant</a> </li>
    <li> <a href="boxSearchReplay.php?cat=sheep&img=000062">Sheep</a> </li>
    <li> <a href="boxSearchReplay.php?cat=sofa&img=000003">Sofa</a> </li>
    <li> <a href="boxSearchReplay.php?cat=train&img=000002">Train</a> </li>
    <li> <a href="boxSearchReplay.php?cat=tvmonitor&img=000045">TV/Monitor</a> </li>
  </ul> 
</html>
