<div class="page-header" style="margin:0">
  <h1 class="primary-color"><?= $title ?></h1>
</div>
<br>
<span style="font-size:110%;color:#FFF;">
  <?php foreach ($info as $key) {
    echo $key['about_us'];
  } ?>
</span>