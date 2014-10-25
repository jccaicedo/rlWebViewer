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
  Reproducing experiments of <a href="http://arxiv.org/abs/1311.2524">Girshick et al. (2013)</a> on Pascal 2007, using CNN features from layer 6.<br>
  The main difference in performance is most likely due to differences in feature computation. <br>
  They use GPUs and report a top-1 error of 42.9 on the validation set of ImageNet2012.<br>
  We use CPUs and get a top-1 error of 45.8 on the validation set of ImageNet2012.
  <br><br>
  <table border='1' width='600px'>
    <tr>
      <th>Class</th><th>Our Result</th><th>Indep. Comp.</th><th>Clusters</th><th>LatentSVM</th><th>Components</th><th>Reported Result</th><th>DPM</th>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=aeroplane&fparams=_10_0.001_gs_0.4_0.2_3">Aeroplane</a> </td>
      <td>0.5503</td><td>0.5425</td><td><a target="_blank" href="clusters.php?category=aeroplane&model=aeroplane_0.001_subcZ_0.3_4.txt.subcategories">C</a></td>
      <td>0.5567</td><td><a target="_blank" href="clusters.php?category=aeroplane&model=aeroplane_10_0.001_gs_0.4_0.2_5.txt.2.latentLabels">5</a></td>
      <td>0.561</td><td>0.332</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=bicycle&fparams=_10_0.001_gs_0.3_0.2_2">Bicycle</a> </td>
      <td>0.5944</td><td>0.5919</td><td><a target="_blank" href="clusters.php?category=bicycle&model=bicycle_0.001_subcZ_0.4_4.txt.subcategories">C</a></td>
      <td>0.6092</td><td><a target="_blank" href="clusters.php?category=bicycle&model=bicycle_10_0.001_gs_0.4_0.2_5.txt.1.latentLabels">5</a></td>
      <td>0.588</td><td>0.603</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=bird&fparams=_10_0.001_gs_0.3_0.2_1">Bird</a> </td>
      <td>0.3187</td><td>0.3403</td><td><a target="_blank" href="clusters.php?category=bird&model=bird_0.001_subcZ_0.4_3.txt.subcategories">C</a></td>
      <td>0.3510</td><td><a target="_blank" href="clusters.php?category=bird&model=bird_10_0.001_gs_0.4_0.2_5.txt.2.latentLabels">5</a></td>
      <td>0.344</td><td>0.230</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=boat&fparams=_10_0.001_gs_0.3_0.3_1">Boat</a> </td>
      <td>0.3044</td><td>0.3019</td><td><a target="_blank" href="clusters.php?category=boat&model=boat_0.001_subcZ_0.4_4.txt.subcategories">C</a></td>
      <td>0.3081</td><td><a target="_blank" href="clusters.php?category=boat&model=boat_10_0.001_gs_0.4_0.3_4.txt.1.latentLabels">4</a></td>
      <td>0.296</td><td>0.161</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=bottle&fparams=_10_0.001_gs_0.4_0.3_1">Bottle</a> </td>
      <td>0.2149</td><td>0.2275</td><td><a target="_blank" href="clusters.php?category=bottle&model=bottle_0.001_subcZ_0.3_3.txt.subcategories">C</a></td>
      <td>0.2210</td><td><a target="_blank" href="clusters.php?category=bottle&model=bottle_10_0.001_gs_0.4_0.3_4.txt.3.latentLabels">4</a></td>
      <td>0.226</td><td>0.273</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=bus&fparams=_10_0.001_gs_0.3_0.3_3">Bus</a> </td>
      <td>0.5028</td><td>0.4700</td><td><a target="_blank" href="clusters.php?category=bus&model=bus_0.001_subcZ_0.4_4.txt.subcategories">C</a></td>
      <td>0.5285</td><td><a target="_blank" href="clusters.php?category=bus&model=bus_10_0.001_gs_0.3_0.2_5.txt.3.latentLabels">5</a></td>
      <td>0.504</td><td>0.543</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=car&fparams=_10_0.001_gs_0.3_0.2_2">Car</a> </td>
      <td>0.5183</td><td>0.5163</td><td><a target="_blank" href="clusters.php?category=car&model=car_0.001_subcZ_0.4_3.txt.subcategories">C</a></td>
      <td>0.5490</td><td><a target="_blank" href="clusters.php?category=car&model=car_10_0.001_gs_0.4_0.2_5.txt.3.latentLabels">5</a></td>
      <td>0.580</td><td>0.582</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=cat&fparams=_10_0.001_gs_0.3_0.3_2">Cat</a> </td>
      <td>0.4830</td><td>0.4712</td><td><a target="_blank" href="clusters.php?category=cat&model=cat_0.001_subcZ_0.4_3.txt.subcategories">C</a></td>
      <td>0.4952</td><td><a target="_blank" href="clusters.php?category=cat&model=cat_10_0.001_gs_0.5_0.3_5.txt.3.latentLabels">5</a></td>
      <td>0.525</td><td>0.230</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=chair&fparams=_10_0.001_gs_0.3_0.3_1">Chair</a> </td>
      <td>0.2081</td><td>0.2323</td><td><a target="_blank" href="clusters.php?category=chair&model=chair_0.001_subcZ_0.3_4.txt.subcategories">C</a></td>
      <td>0.2241</td><td><a target="_blank" href="clusters.php?category=chair&model=chair_10_0.001_gs_0.4_0.3_4.txt.1.latentLabels">4</a></td>
      <td>0.183</td><td>0.200</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=cow&fparams=_10_0.001_gs_0.4_0.2_1">Cow</a> </td>
      <td>0.4553</td><td>0.4257</td><td><a target="_blank" href="clusters.php?category=cow&model=cow_0.001_subcZ_0.4_4.txt.subcategories">C</a></td>
      <td>0.4597</td><td><a target="_blank" href="clusters.php?category=cow&model=cow_10_0.001_gs_0.3_0.2_3.txt.1.latentLabels">3</a></td>
      <td>0.401</td><td>0.241</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=diningtable&fparams=_10_0.001_gs_0.3_0.2_3">Table</a> </td>
      <td>0.3670</td><td>0.3964</td><td><a target="_blank" href="clusters.php?category=diningtable&model=diningtable_0.001_subcZ_0.3_3.txt.subcategories">C</a></td>
      <td>0.4286</td><td><a target="_blank" href="clusters.php?category=diningtable&model=diningtable_10_0.001_gs_0.4_0.2_3.txt.2.latentLabels">3</a></td>
      <td>0.413</td><td>0.267</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=dog&fparams=_10_0.001_gs_0.3_0.3_1">Dog</a> </td>
      <td>0.4414</td><td>0.4170</td><td><a target="_blank" href="clusters.php?category=dog&model=dog_0.001_subcZ_0.3_4.txt.subcategories">C</a></td>
      <td>0.4634</td><td><a target="_blank" href="clusters.php?category=dog&model=dog_10_0.001_gs_0.4_0.3_2.txt.1.latentLabels">2</a></td>
      <td>0.468</td><td>0.127</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=horse&fparams=_10_0.001_gs_0.3_0.3_3">Horse</a> </td>
      <td>0.4823</td><td>0.4752</td><td><a target="_blank" href="clusters.php?category=horse&model=horse_0.001_subcZ_0.3_4.txt.subcategories">C</a></td>
      <td>0.5234</td><td><a target="_blank" href="clusters.php?category=horse&model=horse_10_0.001_gs_0.4_0.2_2.txt.2.latentLabels">2</a></td>
      <td>0.495</td><td>0.581</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=motorbike&fparams=_10_0.001_gs_0.5_0.3_2">Motorbike</a> </td>
      <td>0.5366</td><td>0.4917</td><td><a target="_blank" href="clusters.php?category=motorbike&model=motorbike_0.001_subcZ_0.4_3.txt.subcategories">C</a></td>
      <td>0.5570</td><td><a target="_blank" href="clusters.php?category=motorbike&model=motorbike_10_0.001_gs_0.3_0.3_3.txt.1.latentLabels">3</a></td>
      <td>0.535</td><td>0.482</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=person&fparams=_10_0.01_gs_0.4_0.3_3">Person</a> </td>
      <td>0.3674</td><td>0.3414</td><td><a target="_blank" href="clusters.php?category=person&model=person_0.001_subcZ_0.4_3.txt.subcategories">C</a></td>
      <td>0.3335</td><td><a target="_blank" href="clusters.php?category=person&model=person_10_0.001_gs_0.4_5.txt.1.latentLabels">5</a></td>
      <td>0.397</td><td>0.432</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=pottedplant&fparams=_10_0.001_gs_0.3_0.3_1">PottedPlant</a> </td>
      <td>0.1894</td><td>0.2079</td><td><a target="_blank" href="clusters.php?category=pottedplant&model=pottedplant_0.001_subcZ_0.4_4.txt.subcategories">C</a></td>
      <td>0.2162</td><td><a target="_blank" href="clusters.php?category=pottedplant&model=pottedplant_10_0.001_gs_0.3_4.txt.3.latentLabels">4</a></td>
      <td>0.230</td><td>0.120</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=sheep&fparams=_10_0.001_gs_0.5_0.3_1">Sheep</a> </td>
      <td>0.4715</td><td>0.4437</td><td><a target="_blank" href="clusters.php?category=sheep&model=sheep_0.001_subcZ_0.4_4.txt.subcategories">C</a></td>
      <td>0.4593</td><td><a target="_blank" href="clusters.php?category=sheep&model=sheep_10_0.001_gs_0.4_4.txt.2.latentLabels">4</a></td>
      <td>0.464</td><td>0.211</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=sofa&fparams=_10_0.001_gs_0.4_0.3_3">Sofa</a> </td>
      <td>0.3820</td><td>0.3708</td><td><a target="_blank" href="clusters.php?category=sofa&model=sofa_0.001_subcZ_0.3_4.txt.subcategories">C</a></td>
      <td>0.3798</td><td><a target="_blank" href="clusters.php?category=sofa&model=sofa_10_0.001_gs_0.4_5.txt.1.latentLabels">5</a></td>
      <td>0.364</td><td>0.361</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=train&fparams=_10_0.001_gs_0.4_0.3_3">Train</a> </td>
      <td>0.5300</td><td>0.4860</td><td><a target="_blank" href="clusters.php?category=train&model=train_0.001_subcZ_0.3_3.txt.subcategories">C</a></td>
      <td>0.5399</td><td><a target="_blank" href="clusters.php?category=train&model=train_10_0.001_gs_0.3_4.txt.1.latentLabels">4</a></td>
      <td>0.508</td><td>0.460</td>
    </tr>
    <tr>
      <td> <a target="_blank" href="main.php?cat=tvmonitor&fparams=_10_0.001_gs_0.4_0.2_1">TvMonitor</a> </td>
      <td>0.5499</td><td>0.5405</td><td><a target="_blank" href="clusters.php?category=tvmonitor&model=tvmonitor_0.001_subcZ_0.4_4.txt.subcategories">C</a></td>
      <td>0.5750</td><td><a target="_blank" href="clusters.php?category=tvmonitor&model=tvmonitor_10_0.001_gs_0.4_0.2_3.txt.2.latentLabels">3</a></td>
      <td>0.590</td><td>0.435</td>
    </tr>
    <tr>
      <th> MAP </th>
      <th>0.4234</th><th>0.4145</th><th>.</th>
      <th>0.4336</th><th>.</th><th>0.433</th><th>0.343</th>
    </tr>
  </table>
</html>
