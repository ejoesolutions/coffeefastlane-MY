
<style>
.vl {
  border-left: 1px solid #B2BABB;
  height: 100px;
}
</style>

<?php 

$date1 = date('Y-m-d', strtotime($seller['seller_created']));
$date2 = date('Y-m-d');

$diff = abs(strtotime($date2) - strtotime($date1));

$years = floor($diff / (365*60*60*24));
$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

?>

<div class="media">
  <a class="pull-left" href="#">
    <img class="media-object" src="<?= ($seller['shop_image']) ? base_url().'logo_vendor/'.$seller['shop_image'] : base_url().'logo_vendor/DummyShop.png' ?>" alt="..." width="140px">
  </a>
  <div class="media-body">
    <div class="col-md-5">
      <h3 class="media-heading">
        <?=  $seller['shop_name'] ?>
      </h3>
      <div class="btn-group">
        <a href="https://wa.me/6<?= $seller['phone'] ?>" class="btn btn-success"><i class="icon-bubbles"></i> Whatsapp</a>
        <a href="<?php echo base_url('') ?><?php echo $seller['shop_url'] ?>" class="btn btn-default"><i class="icon-bag"></i> View Shop</a>
      </div>
    </div>
    <div class="col-md-1">
      <div class="vl"></div>
    </div>
    <div class="col-md-3 h5">
      <h4>Products : <span class="text-choco"><?= ($pList) ? count($pList): 0 ?></span></h4>
      <h4>Contact : <span class="text-choco"><?= $seller['phone'] ?></span></h4>
      <!-- <h4 class="media-heading"> -->
        <!-- <?=  $seller['address'] ?>
        <?=  $seller['postcode'] ?>
        <?=  $seller['town_area'] ?>
        <?=  $seller['state'] ?> -->
      <!-- </h4> -->
    </div>
    <div class="col-md-3 h5">
      <h4>Joined : <span class="text-choco"><?= ($years) ? $years.' years' : '' ?> <?= ($months) ? $months.' months' : '' ?> <?= (!$months && !$years) ? $days.' days' : '' ?> ago</span></h4>
    </div>
  </div>
</div>

<div class="row">
    <hr>
    <?php 
    if ($this->uri->segment(2) && $this->uri->segment(3)) {
      $this->load->view('product/vendor_product_detail');
      echo "<hr>";
    }else { ?>
    <div class="section-title">
      <h3 class="title">Products by <?php echo $seller['shop_name'] ?></h3>
      <?php $baseW=4.25; ?>
    </div>
    <?php } ?>


    <?php 
    if ($this->uri->segment(2) && $this->uri->segment(3)) {
      echo "<h4 class='title'>More Products by ".$seller['shop_name']."</h4>";
    } ?>
  </div>
  <!-- section title -->
  <?php
    $limit =36;
    if (isset($_GET["page"])) {
      $pn  = $_GET["page"];
    }
    else {
      $pn=1;
    };

    $start_from = ($pn-1) * $limit;
    //$data=[];
    $this->db->LIMIT($limit,$start_from);
    $this->db->where('vu_products_list.seller_status=1 and vu_products_list.product_status=1 and vu_products_list.stock>0');
    if($this->uri->segment(1)!=''){
      $this->db->where('vu_products_list.shop_url',$this->uri->segment(1));
      $this->db->or_where('vu_products_list.seller_id',1);
      $this->db->or_where('vu_products_list.cfl',1);
    }
    $this->db->select('
      vu_products_list.*,
      ci_seller.shop_city,
    ');
    $this->db->order_by('vu_products_list.seller_id desc,vu_products_list.product_id desc');
    $this->db->join('ci_seller', 'ci_seller.seller_id = vu_products_list.seller_id', 'left');
    
    $this->query = $this->db->get('vu_products_list');
    if ($this->query->num_rows() > 0) {
      foreach ($this->query->result() as $row) {
        $data[] = $row;
      }
      //return $data;
    }
    //print_r($data);

    if(!empty($data)){
      foreach ($data as $row) {
        if($row->stock>0 && $row->seller_status==1 && $row->product_status==1){
          $info = pathinfo( $row->image_file );
          $no_extension =  basename( $row->image_file, '.'.$info['extension'] );
          $thumb_image = $no_extension.'_thumb.'.$info['extension'];
        ?>
        <div class="col-md-2 col-sm-6 col-xs-6 c-padding">
          <!-- <a href="<?php echo base_url('catalog/vendor_product_detail') ?>/<?php echo $seller['shop_name'] ?>/<?php echo $seller['seller_id'] ?>/<?php echo $row->product_id; ?>"> -->
          <a href="<?php echo base_url('') ?><?php echo $seller['shop_url'] ?>/p/<?php echo $seller['seller_id'] ?>/<?php echo $row->product_id; ?>">
          <div class="content product product-single custom-banner">
              <div class="product-thumb">

                <div class="content-overlay"></div>
                <div class="quarter-circle-bottom-left" style="font-size:80%;text-align:center;"><?php if($row->weight>=1000){ $n_weight=$row->weight/1000; echo $n_weight.' kg';}else{ echo number_format($row->weight,0).'g';} ?></div>
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
                <img style="border:1px solid #e7e4e4;padding-left:1px;padding-right:1px;padding-top:1px;padding-bottom:1px;" class="content-image img-fluid d-block mx-auto" src="<?php echo base_url().'images/thumbnail/'.$thumb_image; ?>" alt="">
                <div class="content-details fadeIn-bottom">
                 <div class="bottom d-flex align-items-center justify-content-center">
                   <?php
                   $nuprice=0;$price_after_tax=0;$clean_price=0;$price_1=0;

                   ?>
                   <p style="color:black">

                    <?php
                    if ($row->shop_image) {
                      $image = $row->shop_image;
                      $image_properties = array(
                        'src'   => base_url().'logo_vendor/'.$image,
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
                     CODE : <?php echo $row->item_code; ?><br>
                     PRICE : RM <?php echo number_format($row->add_cost,2);  ?><br>
                   </p>
                 </div>
                </div>
              </div>
              <div class="product-body" align="right" style="height:190px">
                <h4 class="product-price">
                  <?php
                    if($row->discount!=null)
                    {
                      $clean_price=$row->add_cost-($row->add_cost*($row->discount/100));
                      echo "RM ".number_format($clean_price,2);
                    }else{
                      echo "RM ".number_format($row->add_cost,2);
                    }

                   ?>
                </h4>

                <p class="product-name" style="color:black;">
                  <a href="<?php echo base_url('') ?><?php echo $seller['shop_url'] ?>/p/<?php echo $seller['seller_id'] ?>/<?php echo $row->product_id; ?>"><?php echo $row->product_name ?></a>
                  <?php //echo "<br>".$row->category_type; ?><br>
                  <span class="text-danger">In Stock : <?php echo $row->stock ?></span>
                  <br>
                  <small class="text-secondary"><?php echo $row->shop_city ?></small>
                </p>
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

</div>
<ul class="pagination">
  <?php
    //$total_records = count($products);
    $total_records = 0;
    if(!empty($pList)){
      foreach ($pList as $key) {
        if($key['seller_status']==1 && $key['product_status']==1 && $key['stock']>0){
          $total_records++;
        }
      }
    }
    // Number of pages required.
    $total_pages = ceil($total_records / $limit);
    $pagLink = "";
    for ($i=1; $i<=$total_pages; $i++) {
      if ($i==$pn) {
        $pagLink .= "<li class='active'><a href='?page=".$i."'>".$i."</a></li>";
      }
      else  {
        $pagLink .= "<li><a href='?page=".$i."'>".$i."</a></li>";
      }
    };
    echo $pagLink;
  ?>
</ul>