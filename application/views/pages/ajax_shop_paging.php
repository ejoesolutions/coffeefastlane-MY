<?php if(!empty($pList)){
  foreach ($pList as $row) {
    if($row->stock>0 && $row->seller_status==1 && $row->product_status==1){
      $info = pathinfo( $row->image_file );
      $no_extension =  basename( $row->image_file, '.'.$info['extension'] );
      $thumb_image = $no_extension.'_thumb.'.$info['extension'];
    ?>
    <div class="col-md-2 col-sm-6 col-xs-6 c-padding" style="margin:0">
      <!-- <a href="<?php echo base_url('catalog/vendor_product_detail') ?>/<?php echo $seller['shop_name'] ?>/<?php echo $seller['seller_id'] ?>/<?php echo $row->product_id; ?>"> -->
      <a href="<?php echo base_url('') ?><?php echo $seller['shop_url'] ?>/p/<?php echo $seller['seller_id'] ?>/<?php echo $row->product_id; ?>">
      <div class="content product product-single custom-banner" style="margin-top:10px;margin-bottom:0">
          <div class="product-thumb" style="margin:0;">
            <div class="content-overlay"></div>
            <!-- <div class="quarter-circle-bottom-left" style="font-size:80%;text-align:center;"><?php if($row->weight>=1000){ $n_weight=$row->weight/1000; echo $n_weight.' kg';}else{ echo number_format($row->weight,0).'g';} ?></div> -->
            <?php
            if($row->discount!=null){
              ?><div class="discount-label red"> <span>-<?php echo $row->discount.'%' ?></span> </div><?php
            }
            if($row->top_product!=null){
              ?><div class="discount-label-2 yellow"> <span>TOP!</span> </div><?php
            }
            if($row->new_product!=null)
            {
              ?><div class="discount-label-3 black"> <span>NEW!</span> </div><?php
            }
            ?>
            <!-- <img style="height: 100%; width: 100%; object-fit: contain" class="content-image" src="<?php echo base_url().'images/thumbnail/'.$thumb_image; ?>" alt=""> -->
            <div class="center" style="height: 182px;">
              <img src="<?php echo base_url().'images/thumbnail/'.$thumb_image; ?>" alt=" " style="width: 255%;max-height: 100%;" />
            </div>
            <div class="content-details fadeIn-bottom">
              <div class="bottom d-flex align-items-center justify-content-center">
                <?php $nuprice=0;$price_after_tax=0;$clean_price=0;$price_1=0; ?>
                <p style="color:black">
                <?php
                if ($row->shop_image) {
                  $image = $row->shop_image;
                  $image_properties = array(
                    'src'   => base_url().'logo_vendor/small/'.$image,
                    'alt'   => $row->shop_name,
                    'class' => '',
                    'width' => '30',
                    'height'=> '30',
                    'style'=>'border:0',
                    'title' => $row->shop_name,
                  );
                  echo img($image_properties);

                } else {

                    $image_properties = array(
                    //  'src'   => 'https://dummyimage.com/75x75/d6c7d6/9b9dad.jpg&text=No+Image',
                      'src'   => base_url().'logo_vendor/DummyShop.png',
                      'alt'   => 'No image',
                      'class' => '',
                      'width' => '30',
                      'height'=> '30',
                      'style'=>'border:0',
                      'title' => 'No image',
                    );
                    echo img($image_properties);
                  }

                  ?>
                  <br><?= $row->shop_name ?><br>
                  CODE : <?php echo $row->item_code; ?>
                  <!-- PRICE : RM <?php echo number_format($row->add_cost,2);?><br> -->
                </p>
              </div>
            </div>
          </div>
          <div class="product-body" align="left" style="height:107px;margin-top:0.1px">
            <p class="over-flow" style="color:black;margin-top:0;margin-bottom:3px;line-height: 1;">
              <a class="primary-color" href="<?php echo base_url('') ?><?php echo $seller['shop_url'] ?>/p/<?php echo $seller['seller_id'] ?>/<?php echo $row->product_id; ?>"><?php echo $row->product_name ?></a>
            </p>
            <h4 class="product-price">
              <?php
                if($row->discount!=null)
                {
                  $clean_price=$row->add_cost-($row->add_cost*($row->discount/100));
                  echo '<span style="text-decoration: line-through;color:#AEB6BF;margin-right:2px;font-size:84%"><span style="font-size:81%">RM</span>'.number_format($row->add_cost,2).'</span>';

                  echo '<span style="font-size:81%">RM</span>'.number_format($clean_price,2);
                }else{
                  echo '<span style="font-size:81%">RM</span>'.number_format($row->add_cost,2);
                }
                ?>
            </h4>
            <p style="margin:0;line-height: 1.2;">
              <span class="text-danger" style="font-size:82%">In Stock : <?php echo $row->stock ?></span>
              <!-- <br> -->
            </p>
            <span style="color:#AEB6BF;font-size:84%"><?= $row->shop_city ?>, <?= $row->shop_state ?></span>

              
          </div>
        </div>
      </a>
    </div>

    <?php
  }
}

}else{
  echo "<p class='text-center'>No products yet</p>";
}
?>
