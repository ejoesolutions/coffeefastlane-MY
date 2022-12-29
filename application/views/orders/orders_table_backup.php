<div class="portlet box blue">
  <div class="portlet-title">
    <div class="caption">
      <?php echo $title ?>
    </div>
  </div>
  <div class="portlet-body">
    <?php//print_r($order_status) ?>
    <table class="table table-bordered" id="sample_3">
      <thead>
        <tr>
          <th width="50px" class="text-center">No</th>
          <th>Order</th>
          <th class="text-center">Date</th>
          <th>Ship to</th>
          <th class="text-center">Total</th>
          <th class="text-center">Status</th>
          <!-- <th class="text-center">#</th> -->
        </tr>
      </thead>
      <tbody>
        <?php if ($orders):
          $n=1;
          ?>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td class="text-center"><?php echo $n++; ?></td><!--echo $order->order_id-->
              <td class="text-left"><?php echo '#'.$order['order_id'].'&nbsp by '.$order['full_name']; ?></td>
              <td class="text-center"><?php echo date("d/m/Y",strtotime($order['created_date'])); ?></td>
              <!-- <td>
                <ol style="list-style-type: circle;">
                  <?php foreach ($items as $item): ?>
                    <?php if ($item->order_id == $order->order_id): ?>
                      <li><?php echo $item->product_name.' x '.$item->qty.' - '.number_format($item->ordered_price,2).'(-'.number_format($item->discount_by_kgt,0).'%)'; ?></li>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </ol>
              </td> -->
              <td class="text-left"><?php echo $order['ship_name'].'<br>'.$order['ship_address'].'<br>'.$order['ship_phone']; ?></td>
              <td class="text-center">
                <?php
                if($user_profile['user_group']==0 || $user_profile['user_group']==1)
                {
                   echo 'RM '.number_format($order['total_all'],2);
                }else{
                  if(!empty($items)){
                    $new_sub=0;
                    foreach ($items as $val) {
                      if($val['seller_id']==$shop['seller_id'] && $val['order_id']==$order['order_id'])
                      {
                        $new_sub=$new_sub+$val['subtotal'];
                      }
                    }
                    echo 'RM '.number_format($new_sub,2);
                  }
                }
                //if($shop['seller_id']==$order['seller_id'])
                 // echo 'RM '.number_format($order['total_all'],2); ?>
              </td>
              <td class="text-center">
                <?php
                if($user_profile['user_group']==0 || $user_profile['user_group']==1)
                {
                  foreach ($order_status as $key) {
                    if($order['order_id']==$key['order_id'])
                    {

                      echo $key['shop_name'].'<br>[ ';
                      if($key['order_status']==0){
                        //print_r($orders);
                        // if($orders[0]['reference_note']==''){
                        //   date_default_timezone_set("Asia/Kuala_Lumpur");
                        //   $date_now=date('Y-m-d H:i:s');
                        //   //$diff_date=strtotime(date('Y-m-d H:i:s'))-strtotime($key['transaction_record']);
                        //   // echo $diff_date;
                        //   if($date_now > $key['next_transaction']){
                        //     $this->db->where('order_status_id',$key['order_status_id']);
                        //     $this->db->update('ci_order_status',array('order_status'=>6));
                        //
                        //     $this->db->where('order_id',$key['order_id']);
                        //     $this->db->update('ci_orders',array('expired'=>1));
                        //
                        //     $this->db->insert('ci_transaction',array('transaction_status'=>6,'transaction_record'=>date("Y-m-d H:i:s"),'order_status_id'=>$key['order_status_id']));
                        //   }
                        // }
                        //echo 'Waiting Pay Receipt';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Waiting Pay Receipt');
                      }
                      if($key['order_status']==1){
                        //echo 'On Hold';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'On Hold');
                      }
                      if($key['order_status']==2){
                        //echo 'Processing';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Processing');
                      }
                      if($key['order_status']==3){
                        //echo 'Shipping Out';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Shipping Out');
                      }
                      if($key['order_status']==4){
                        //echo 'Completed';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Completed');
                      }
                      if($key['order_status']==5){
                        //echo 'Completed';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Verify & Notify');
                      }
                      if($key['order_status']==6){
                        //echo 'Completed';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Cancel');
                      }
                      echo ' ]<br>';
                    }
                  }
                }else{
                  foreach ($order_status as $key) {
                    if($order['order_id']==$key['order_id'] && $shop['seller_id']==$key['seller_id'])
                    {
                      echo $key['shop_name'].'<br>[ ';
                      if($key['order_status']==0){
                        //echo 'Waiting Pay Receipt';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Waiting Pay Receipt');
                      }
                      if($key['order_status']==1){
                        //echo 'On Hold';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'On Hold');
                      }
                      if($key['order_status']==2){
                        //echo 'Processing';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Processing');
                      }
                      if($key['order_status']==3){
                        //echo 'Shipping Out';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Shipping Out');
                      }
                      if($key['order_status']==4){
                        //echo 'Completed';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Completed');
                      }
                      if($key['order_status']==5){
                        //echo 'Completed';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Verify & Notify');
                      }
                      if($key['order_status']==6){
                        //echo 'Completed';
                        echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'Cancel');
                      }
                      echo ' ]<br>';
                    }
                  }
                }

                 ?>
              </td>
              <!-- <th class="text-center">
                <?php
                  foreach ($order_status as $key) {
                    if($order['order_id']==$key['order_id'])
                    {
                      echo anchor('orders/detail/'.$order['order_id'].'/'.$key['seller_id'], 'View '.$key['shop_name']);
                      echo '<br>';
                    }
                  }
                   ?>
              </th> -->
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
