<div class="page-header" style="margin:0">
  <h1 class="primary-color"><?= $title ?></h1>
</div>
<span style="font-size:110%">
  <?php foreach ($info as $key) {
    echo $key['shipping'];
  } ?>
</span>