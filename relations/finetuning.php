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
  <h3>Regions+CNN Detector</h3>
  Experiments with Finetuning.<br>
  The pretrained network of Caffe was trained for 70K more iterations using as inputs positive and negative windows from Pascal 2012.
  <br><br>
  <table border='1' width='600px'>
    <tr>
      <th>Class</th><th>No Fine-tuning</th><th>With Fine-tuning</th><th>Reported Result</th><th>DPM</th>
    </tr>
    <tr>
      <td> Aeroplane </td>
      <td>0.5503</td>
      <td>0.5364</td>
      <td>0.635</td>
      <td>0.332</td>
    </tr>
    <tr>
      <td> Bicycle </td>
      <td>0.5944</td>
      <td>0.6420</td>
      <td>0.660</td>
      <td>0.603</td>
    </tr>
    <tr>
      <td> Bird </td>
      <td>0.3187</td>
      <td>0.4272</td>
      <td>0.479</td>
      <td>0.230</td>
    </tr>
    <tr>
      <td> Boat </td>
      <td>0.3044</td>
      <td>0.3121</td>
      <td>0.377</td>
      <td>0.161</td>
    </tr>
    <tr>
      <td> Bottle </td>
      <td>0.2149</td>
      <td>0.2918</td>
      <td>0.299</td>
      <td>0.273</td>
    </tr>
    <tr>
      <td> Bus </td>
      <td>0.5028</td>
      <td>0.6086</td>
      <td>0.625</td>
      <td>0.543</td>
    </tr>
    <tr>
      <td> Car </td>
      <td>0.5183</td>
      <td>0.6304</td>
      <td>0.702</td>
      <td>0.582</td>
    </tr>
    <tr>
      <td> Cat </td>
      <td>0.4830</td>
      <td>0.4900</td>
      <td>0.602</td>
      <td>0.230</td>
    </tr>
    <tr>
      <td> Chair </td>
      <td>0.2081</td>
      <td>0.2660</td>
      <td>0.320</td>
      <td>0.200</td>
    </tr>
    <tr>
      <td> Cow </td>
      <td>0.4553</td>
      <td>0.5084</td>
      <td>0.579</td>
      <td>0.241</td>
    </tr>
    <tr>
      <td> Table </td>
      <td>0.3670</td>
      <td>0.4771</td>
      <td>0.470</td>
      <td>0.267</td>
    </tr>
    <tr>
      <td> Dog </td>
      <td>0.4414</td>
      <td>0.4694</td>
      <td>0.535</td>
      <td>0.127</td>
    </tr>
    <tr>
      <td> Horse </td>
      <td>0.4823</td>
      <td>0.5034</td>
      <td>0.601</td>
      <td>0.581</td>
    </tr>
    <tr>
      <td> Motorbike </td>
      <td>0.5366</td>
      <td>0.5873</td>
      <td>0.642</td>
      <td>0.482</td>
    </tr>
    <tr>
      <td> Person </td>
      <td>0.3674</td>
      <td>0.4250</td>
      <td>0.522</td>
      <td>0.432</td>
    </tr>
    <tr>
      <td> PottedPlant </td>
      <td>0.1894</td>
      <td>0.2758</td>
      <td>0.313</td>
      <td>0.120</td>
    </tr>
    <tr>
      <td> Sheep </td>
      <td>0.4715</td>
      <td>0.5118</td>
      <td>0.550</td>
      <td>0.211</td>
    </tr>
    <tr>
      <td> Sofa </td>
      <td>0.3820</td>
      <td>0.3721</td>
      <td>0.500</td>
      <td>0.361</td>
    </tr>
    <tr>
      <td> Train </td>
      <td>0.5300</td>
      <td>0.5411</td>
      <td>0.577</td>
      <td>0.460</td>
    </tr>
    <tr>
      <td> TvMonitor </td>
      <td>0.5499</td>
      <td>0.6138</td>
      <td>0.630</td>
      <td>0.435</td>
    </tr>
    <tr>
      <th> MAP </th>
      <th>0.4234</th>
      <th>0.4744</th>
      <th>0.531</th>
      <th>0.343</th>
    </tr>
  </table>
</html>
