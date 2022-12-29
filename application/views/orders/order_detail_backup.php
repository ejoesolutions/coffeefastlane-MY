<div class="portlet box blue">
  <div class="portlet-title">
    <div class="caption">
      <?php echo $title ?>
    </div>
  </div>
  <div class="portlet-body">
    <?php //print_r($orders) ?>
    <table class="table table-bordered" id="" width="100%">
      <thead>
        <tr>
          <th colspan="1">Order #<?php echo $orders['order_id'] ?> details</th>
          <td colspan="2">
            <?php
            if($orders['payment_type']=='Online Banking'){
              ?><b>Payment</b> via <?php echo $orders['payment_type'].' [ '.$orders['reference_note'].' ]';
            }else{
              ?><b>Payment</b> via <?php echo $orders['payment_type'];

              if(!empty($orders['att_file'])){
                ?> [ <a class="" data-toggle="modal" href="#viewPayReceipt"><b>View Receipt</b></a> ]<?php
              }
            }
            ?>
          </td>
        </tr>
        <tr>
          <th width="25%">General</th>
          <th width="30%">Billing</th>
          <th width="30%">Shipping</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Order created :<br><?php echo date('d/m/Y @ H:i:s',strtotime($orders['created_date'])) ?></td>
          <td rowspan="2">
            <?php
            echo $orders['bill_name'].'<br>'.$orders['bill_address'];
             ?>
             <br><br>Email : <?php echo $orders['bill_email']; ?>
             <br>Phone : <?php echo $orders['bill_phone']; ?>
          </td>
          <td rowspan="2">
            <?php
            echo $orders['ship_name'].'<br>'.$orders['ship_address'].'<br>'.$orders['ship_phone'];
             ?>
            <?php
              if(!empty($track)){
                ?>
                <br><br>Tracking No : <?php echo $track['tracking_code']; ?>
                <br>Courier : <?php echo $track['courier_name']; ?>
                <?php
              }
             ?>
          </td>
        </tr>
        <tr>
          <?php
          // foreach ($items as $key) {
            //$result=array_unique($items);
            print_r($order_status);
          //}

           ?>
          <td>Status :<br>
            <?php
            $stat=array(array('0'=>'1','1'=>'On Hold'),array('0'=>'2','1'=>'Processing'),array('0'=>'3','1'=>'Shipping out'),array('0'=>'4','1'=>'Completed'));
            //print_r($stat);
             ?>
             <?php echo form_open('orders/updateStatus'); ?>
            <select name="status" class="form-control" id="status" onchange="set_productsiri()">
              <?php
              for($i=0;$i<count($stat);$i++){
                if($orders['status']==$stat[$i][0] && $orders['status']==1){
                  echo '<option value="1" selected>On Hold</option>';
                }
                else if($orders['status']==$stat[$i][0] && $orders['status']==2){
                  echo '<option value="2" selected>Processing</option>';
                }
                else if($orders['status']==$stat[$i][0] && $orders['status']==3){
                  echo '<option value="3" selected>Shipping Out</option>';
                }
                else if($orders['status']==$stat[$i][0] && $orders['status']==4){
                  echo '<option value="4" selected>Completed</option>';
                }
                else{
                  echo '<option value="'.$stat[$i][0].'">'.$stat[$i][1].'</option>';
                }
              }

               ?>
            </select><br>
            <?php echo form_hidden('order_id',$orders['order_id']) ?>
            <?php echo form_hidden('owner_id',$orders['created_by']) ?>
            <?php
            if(!empty($items)){
              foreach ($items as $row) {
                  echo form_hidden('product_id[]',$row['product_id']);
                  echo form_hidden('qty[]',$row['qty']);
                  echo form_hidden('seller_id[]', $row['seller_id']);
              }
            }
             ?>

            <input type="submit" name="submitStatus" value="Update" id="submitStatus" style="display:none" class="btn btn-primary">
            <?php echo form_close(); ?>
          </td>

        </tr>
      </tbody>
    </table>

    <?php
    // open section status 2
    if(empty($siri)){
      ?>
      <div id="show_siriproduct" style="display:none">
        <table class="table table-bordered" id="" width="100%">
          <?php echo form_open('orders/store_siriproduct') ?>
          <tr>
            <th>No.</th>
            <th>Product</th>
            <th>Product Code</th>
            <th>Siri No.</th>
          </tr>
          <?php echo form_hidden('order_id', $orders['order_id']) ?>
          <?php echo form_hidden('owner_id', $orders['created_by']) ?>
          <?php if ($items){ ?>
                  <?php
                  $n=1;
                  $p=0;
                  //print_r($items);
                  foreach ($items as $item){ ?>
                    <?php
                      for($i=0;$i<$item['qty'];$i++)
                      {

                        ?>
                        <tr>
                          <td><?php echo $n++; ?></td>
                          <td><?php echo $item['product_name'] ?></td>
                          <td><?php echo $item['item_code'] ?></td>
                          <?php
                          if(!isset($_POST['submitStatus2'])){

                              ?>
                              <td><input type="text" name="no_siri[]" id="no_siri" class="form-control uppercase" required></td>
                              <?php


                          }else{
                            //print_r($item['product_type']);

                               ?>
                               <td><input type="text" name="no_siri[]" id="no_siri" class="form-control uppercase" value="<?php echo $no_siri[$p++]; ?>" required></td>
                               <?php

                          }
                          ?>
                          <!-- <td><input type="text" name="no_siri[]" id="no_siri" class="form-control"></td> -->

                          <!-- <td><?php echo form_input('no_siri[]', NULL, array('class'=>'form-control')) ?></td> -->
                          <?php echo form_hidden('siri_item[]', $item['product_id']) ?>
                          <?php //echo form_hidden('type[]', $item['product_type']) ?>
                          <?php //echo form_input('code[]', $item['item_code']) ?>
                          <?php //echo form_hidden('product_id[]',$row['product_id']);
                           ?>
                          <!-- <?php print_r($order); ?> -->
                        </tr>
                        <?php
                      //}
                    }
                    echo form_hidden('product_id[]',$item['product_id']);
                    echo form_hidden('qty[]',$item['qty']);
                    echo form_hidden('seller_id[]', $item['seller_id']);
                    //endif;

                  }
                  if(isset($_POST['submitStatus2'])){
                    echo "<script type='text/javascript'>alert('$error_msg');</script>";
                  }
                }
             ?>
             <tr>
               <td colspan="4" class="text-right"><input type="submit" name="submitStatus2" value="Update" class="btn btn-primary" ></td>
             </tr>
             <?php echo form_close(); ?>
        </table>

      </div>
      <?php
    }
    ?>
    <!-- close section status 2 -->

    <!-- open section status 3 -->
    <?php if(empty($track)){ ?>
    <div id="show_tracking" style="display:none">
      <table class="table table-bordered" id="" width="100%">
        <?php echo form_open('orders/store_tracking') ?>
        <tr>
          <th>No.</th>
          <th>Tracking Code</th>
          <th>Courier Name</th>
        </tr>
        <tr>
          <td><?php echo '1'; ?></td>
          <td><input type="text" name="no_tracking" id="no_tracking" class="form-control" required></td>
          <td>
            <input type="text" name="courier" id="courier" class="form-control" list="list-courier" required>
            <datalist id="list-courier">
              <?php
              foreach ($list_courier as $key) {
                  ?><option value="<?php echo $key['courier_name'] ?>"><?php echo $key['courier_name'] ?></option><?php
              }
               ?>
            </datalist>
          </td>
        </tr>
        <tr>
          <td colspan="3" class="text-right"><input type="submit" name="submitStatus3" id="submitStatus3" value="Update" class="btn btn-primary" ></td>
        </tr>
        <?php echo form_hidden('order_id', $orders['order_id']) ?>
        <?php echo form_hidden('owner_id', $orders['created_by']) ?>
         <?php echo form_close(); ?>
      </table>
    </div>
    <?php } ?>
    <!-- close section status 3 -->

    <table class="table table-bordered" id="" width="100%">
      <thead>
        <tr>
          <th>Item</th>
          <?php if(!empty($siri)){ ?>
          <th class="text-center">Siri No.</th>
        <?php } ?>
          <th class="text-center">Cost</th>
          <th class="text-center">Qty</th>
          <th class="text-center">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php
        //print_r($items);
        if(!empty($items)){
          foreach ($items as $key) {
            ?>
            <tr>
              <td><?php echo $key['product_name'].'&nbsp;[ '.$key['item_code'].' ] - '.$key['weight'].' g' ?><br>Seller : <?php echo $key['shop_name'] ?></td>

              <?php if(!empty($siri)){ ?>

              <td>
                <?php
                          $s=0;
                          //print_r($pid);
                          foreach ($pid as $key1) {
                            $i=$key1['total'];
                            foreach ($siri as $value_siri) {
                              if($key1['product_id']==$value_siri['product_id'] && $value_siri['product_id']==$key['product_id']){
                                echo $value_siri['serial_no'];
                                $s++;
                                if($i-1!=0){
                                  echo ' , ';
                                  $i--;
                                }
                                if($s>2){
                                    echo "<br>";$s=0;
                                }
                              }
                            }
                        }
                        ?>
              </td>
            <?php } ?>
              <td class="text-center"><?php echo 'RM '.number_format($key['ordered_price'],2) ?></td>
              <td class="text-center"><?php echo $key['qty'] ?></td>
              <td class="text-center"><?php echo 'RM '.number_format($key['subtotal'],2) ?></td>
            </tr>
            <?php
          }
        }
        ?>

        <tr>
          <!-- <td colspan="4" class="text-right"><b>Net Total</b></td>
          <td colspan="4" class="text-center"><b><?php echo 'RM '.number_format($orders['total_all'],2) ?></b></td> -->
          <?php if(!empty($siri)){
            echo '<td colspan="4" class="text-right"><b>Net Total</b></td><td colspan="4" class="text-center"><b>RM '.number_format($orders['total_all'],2).'</b></td>';
          }else{
              echo '<td colspan="3" class="text-right"><b>Net Total</b></td><td colspan="3" class="text-center"><b>RM '.number_format($orders['total_all'],2).'</b></td>';
          } ?>
        </tr>
      </tbody>
    </table>
    <?php
      if(!empty($siri)){
        echo anchor('orders/print_order/'.$orders['order_id'], '<i class="fa fa-print"></i>Print', array('class'=>'btn btn-success','target'=>'_blank'));
      }
    ?>
  </div>
</div>

<!-- Modal view pay slip -->
<div class="modal fade bs-modal-xl" id="viewPayReceipt" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">View Payment Receipt</h4>
      </div>
      <div class="modal-body">
        <?php echo form_open('', array('class'=>'form-horizontal')); ?>
        <div class="form-group">
          <div class="col-md-4">
              <label class="control-label">Bank Name</label>
          </div>
          <div class="col-md-8">
              <input type="text" class="form-control" name="bank_name" value="<?php echo $orders['reference_note'] ?>" readonly>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-4">
              <label class="control-label">Receipt Image</label>
          </div>
          <div class="col-md-8">
            <?php
            $extension = pathinfo($orders['att_file'], PATHINFO_EXTENSION);
            if($extension=='pdf'){
              echo anchor('orders/download/'.$orders['att_file'], 'Download Here', array(''));
            }else{
              echo '<img src="'.base_url('/payment_receipt/').$orders['att_file'].'" width="300px" height="350px">';
            }
            //echo '<img src="'.base_url('/payment_receipt/').$orders['att_file'].'" width="300px" height="350px">';
            ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>

      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>

<script>
function set_productsiri(){
    var select_status=$('#status').val();
    var status="<?php echo $orders['status'] ?>";

    if(status=='1'){
      $('#show_tracking').hide();
      //document.getElementById("submitStatus").style.visibility="hidden";
    }
    if(status=='1' && select_status=='2'){
      $('#show_siriproduct').show();
      $('#show_tracking').hide();
    }
    if(status=='1' && (select_status=='1' || select_status=='3' || select_status=='4')){
      $('#show_siriproduct').hide();
      $('#show_tracking').hide();
    }
    if(status=='2' && select_status=='3'){
      $('#show_tracking').show();
    }
    if(status=='2' && (select_status=='1' || select_status=='2' || select_status=='4')){
      $('#show_tracking').hide();
    }
    if(status=='3' && select_status=='4'){
      $('#submitStatus').show();
    }
    if(status=='3' && (select_status=='1' || select_status=='2' || select_status=='3')){
      $('#submitStatus').hide();
    }

    //return select_cara;
  }
</script>
