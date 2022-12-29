<div class="row">
  <div class="col-md-12">
    <div class="order-summary clearfix">
      <div class="section-title">
        <h3 class="primary-color">MY CART</h3>
      </div>
      <div class="table-responsive">
        <table class=" table">
          <thead>
            <tr>
              <th class="primary-color">Product</th>
              <th class="text-center primary-color">Price</th>
              <th class="text-center primary-color">Quantity</th>
              <th class="text-center primary-color">Total</th>
              <th class="text-right primary-color"></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $data=[];
            $this->db->order_by('product_id','asc');
            $this->query = $this->db->get('vu_products_list');
            if ($this->query->num_rows() > 0) {
              foreach ($this->query->result_array() as $row) {
                $data[] = $row;
              }
              //return $data;
            }

            foreach ($data as $key) {
              foreach ($this->cart->contents() as $key2){
                if($key['product_id']==$key2['id'] && ($key['product_status']==0 || $key['stock']==0))
                {
                  $this->cart->remove($key2['rowid']);
                }
              }
            }

            $i = 1;
            $total_all=0.0;
            // print_r($this->cart->contents());
            foreach ($this->cart->contents() as $items){
              echo form_hidden($i.'[rowid]', $items['rowid']);
              ?>
              <tr>
                <td class="details">
                  <a href="#" class="primary-color"><?php echo $items['name']; ?></a>
                  <ul style="color:#F7F9F9;">
                    <li><span>Code : <?php echo $items['item_code']; ?></span></li>
                    <li><span>Weight : <?php echo $items['weight']; ?> g</span></li>
                    <li><span>Shop : <?php echo $items['shop_name']; ?></span></li>
                    <!-- <li><span>Color: Camelot</span></li> -->
                  </ul>
                </td>
                <td class="price text-center"><strong class="primary-color"><?php echo 'RM '.$this->cart->format_number($items['price']); ?></strong></td>
                <td class="qty text-center">
                  <?php
                  echo form_open('cart/update_cart');
                  foreach ($data as $key ) {
                   if($key['product_id']==$items['id'])
                   {
                     ?><input type="number" name="qty" id="qty" class="text-center" min="1"  value="<?php echo $items['qty']; ?>" max="<?php echo $key['stock'] ?>"><?php
                   }
                  }
                  echo form_hidden('itemID', $items['id']);
                  echo form_hidden('row_id', $items['rowid']);
                  ?><button class="btn btn-warning" type="submit" id="btn-update"><span class="fa fa-refresh"></span></button><?php
                  echo form_close();
                   ?>
                  <!-- <button type="button" id="<?php echo $items['rowid']; ?>" class="calc_decs"><<</button>
                  <input type="text" name="qty" class="text-center" min="1" value="<?php echo $items['qty']; ?>" id="input_qty" onchange="upd_val()">
                  <button type="button" id="<?php echo $items['rowid']; ?>" class="calc_inc">>></button> -->
                  <!-- <strong><?php echo $items['qty']; ?></strong> -->
                </td>
                <td class="total text-center"><strong class="primary-color">
                  <?php echo 'RM '.$this->cart->format_number($items['subtotal']); ?>
                  <!-- <div id='subtotal'></div> -->
                </strong></td>
                <td class="text-right"><?php echo anchor('member/clear_item_cart/'.$items['rowid'], '<span class="fa fa-trash"></span>', array('class'=>'btn btn-danger')); ?></td>
              </tr>

              <?php
              $i++;
              $total_all=$total_all+$items['subtotal'];
              echo form_hidden('item_id[]', $items['id']);
              echo form_hidden('item_name[]', $items['name']);
              echo form_hidden('item_price[]', $items['price']);
              echo form_hidden('item_qty[]', $items['qty']);
              echo form_hidden('item_subtotal[]', $items['subtotal']);
              echo form_hidden('item_referrel[]', $items['referrel']);
          } ?>
          </tbody>
          <tfoot>
            <tr>
              <th class="empty" colspan="2"></th>
              <th class="primary-color">TOTAL</th>
              <th colspan="2" class="total primary-color text-center">RM <?php echo number_format($total_all,2, '.', '') ?></th>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="pull-right" style="margin-top:10px">
        <?php if(!empty($this->cart->contents())): ?>
          <a href="<?php echo base_url('orders/checkout') ?>"><button class="btn btn-green">Checkout</button></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>


<script>
  function upd_val(){
    var input = document.getElementById("input_qty").value;
    var stock = "<?php echo $p_detail['stock']?>";
    var price = "<?php echo $p_detail['price']?>";
    var subtotal=parseFloat(input)*parseFloat(price);

    // document.querySelector('')
  }
</script>
