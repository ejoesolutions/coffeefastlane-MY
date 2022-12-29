<div class="row">
  <div class="col-md-12">
    <?php echo form_open('',array('class'=>'clearfix')) ?>
    <div class="billing-details">
      <div class="section-title">
        <h3 class="title">My Cart</h3>
      </div>
      <table class="table">
        <thead>
          <th>Product</th>
          <th>Unit Price</th>
          <th>Quantity</th>
          <th>Total Price</th>
          <th>Action</th>
        <thead>
        <tbody>
          <?php
          $i = 1;
          print_r($this->cart->contents());
          foreach ($this->cart->contents() as $items){
            echo form_hidden($i.'[rowid]', $items['rowid']);
            ?>
            <tr>
              <td>
                <?php echo $items['name']; ?><br><i>Code</i> : <?php echo $items['item_code']; ?>
              </td>
              <td><?php echo 'RM '.$this->cart->format_number($items['price']); ?></td>
              <td>
                <!-- <button type="button" id="<?php echo $items['rowid']; ?>" class="calc_decs"><<</button>
                <input type="text" name="qty" class="text-center" min="1" value="<?php echo $items['qty']; ?>" id="input_qty" onchange="upd_val()">
                <button type="button" id="<?php echo $items['rowid']; ?>" class="calc_inc">>></button> -->
                <?php echo $items['qty']; ?>
              </td>
              <td>
                <?php echo 'RM '.$this->cart->format_number($items['subtotal']); ?>
                <!-- <div id='subtotal'></div> -->
              </td>
              <td><?php echo anchor('customer/clear_item_cart/'.$items['rowid'], '<span class="fa fa-trash"></span>', array('class'=>'btn btn-danger')); ?></td>
            </tr>

            <?php
            $i++;
        }
            ?>
        </tbody>
        <tfoot>
          <tr>
            <th class="empty" colspan="3"></th>
            <th>SUBTOTAL</th>
            <th colspan="2" class="sub-total">$97.50</th>
          </tr>
          <tr>
            <th class="empty" colspan="3"></th>
            <th>SHIPING</th>
            <td colspan="2">Free Shipping</td>
          </tr>
          <tr>
            <th class="empty" colspan="3"></th>
            <th>TOTAL</th>
            <th colspan="2" class="total">$97.50</th>
          </tr>
        </tfoot>
      </table>
      <div class="pull-right">
        <button class="primary-btn">Place Order</button>
      </div>
      </table>
    </div>
    <?php echo form_close() ?>
  </div>
</div>
<script>
  function upd_val(){
    var input = document.getElementById("input_qty").value;
    var stock = "<?php echo $p_detail['stock']?>";
    var price = "<?php echo $p_detail['price']?>";
    var subtotal=parseFloat(input)*parseFloat(price);
  }
</script>
