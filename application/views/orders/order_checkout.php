<div class="row">
					<div class="col-md-6">
            <?php echo form_open('orders/store_order_fpx', array()) ?>
						<div class="billing-details">
							<div class="section-title">
								<h3 class="primary-color">Shipping Details</h3>
							</div>

							<div class="form-group">
                <label class="control-label" style="color:#F7F9F9;">Name</label>
                <input type="text" name="ship_name" class="form-control" value="<?php echo $ship['ship_name'] ?>">
							</div>

							<div class="form-group">
                <label class="control-label" style="color:#F7F9F9;">Address</label>
                <textarea name="ship_address" class="form-control"><?php echo $ship['ship_address'] ?></textarea>
							</div>
							<div class="form-group">
                <label class="control-label" style="color:#F7F9F9;">Postcode</label>
                <input type="text" name="ship_postcode" class="form-control" value="<?php echo $ship['ship_postcode'] ?>">
							</div>
							<div class="form-group">
                <label class="control-label" style="color:#F7F9F9;">Area</label>
                <input type="text" name="ship_area" class="form-control" value="<?php echo $ship['ship_area'] ?>">
							</div>
							<div class="form-group">
                <label class="control-label" style="color:#F7F9F9;">State</label>
                <?php echo form_dropdown('state_id', $state, $ship_state['state_id'], array('id'=>'state_id','class'=>'form-control','required'=>'required','onchange'=>'calc_func()')) ?>
							</div>

							<div class="form-group">

                <label class="control-label" style="color:#F7F9F9;">Phone</label>
                <input type="text" name="ship_phone" class="form-control" value="<?php echo $ship['ship_phone'] ?>">
							</div>
						</div>

					</div>

					<div class="col-md-6">
            <div class="payments-methods">
							<div class="section-title">
								<h4 class="primary-color">Payments Methods</h4>
							</div>
							<!--<div class="input-checkbox">-->
							<!--	<input type="radio" name="payments" id="payments-1" value="Cash Deposit" required>-->
							<!--	<label for="payments-1">ATM/Cash Deposit</label>-->
							<!--	<div class="caption">-->
							<!--	<p>Make your payment directly into our bank account <b>(Maybank : 562302620067 - SEVEN SEASONS PRESERVATION SDN BHD)</b>. Please send us the payment's receipt by go to "My Orders" in your account. Your order will not be shipped until the funds have cleared in our account.-->
							<!--			<p>-->
							<!--	</div>-->
							<!--</div>-->
							<div class="input-checkbox">
								<input type="radio" name="payments" id="payments-2" value="Online Banking" required checked>
								<label for="payments-2" style="color:#F7F9F9;">Online Banking</label>
								<div class="caption primary-color">
									<p>Make your payment directly using payment gateway toyyidPay. Your order will not be shipped until the funds have cleared in our account.
										<p>
								</div>
							</div>

						</div>
          </div>

					<div class="col-md-12 table-responsive">
            <div class="order-summary clearfix">
              <div class="section-title">
                <h3 class="primary-color">MY CART</h3>
              </div>
              <table class="table">
                <thead>
                  <tr>
                    <th style="color:#F7F9F9;">Product</th>
                    <th class="text-center" style="color:#F7F9F9;">Price</th>
                    <th class="text-center" style="color:#F7F9F9;">Quantity</th>
                    <th class="text-center" style="color:#F7F9F9;">Total</th>
                    <th class="text-right"></th>
                  </tr>
                </thead>
                <tbody>

									<?php
									$data=[];
			            // $this->db->where('seller_status=1 and product_status=1 and stock>0');
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
                  $total_all=0.0;$total_sub=0.0;
									$total_weight=0.0;
                  //print_r($this->cart->contents());
                  foreach ($this->cart->contents() as $items){
                    echo form_hidden($i.'[rowid]', $items['rowid']);
                    ?>
                    <tr>
                      <td class="details">
                        <a href="#" class="primary-color"><?php echo $items['name']; ?></a>
                        <ul style="color:#F7F9F9;">
                          <li><span>Code: <?php echo $items['item_code']; ?></span></li>
						  						<li><span>Weight: <?php echo $items['weight']; ?> g</span></li>
				  		  					<li><span>Shop : <?php echo $items['shop_name']; ?></span></li>
                          <!-- <li><span>Color: Camelot</span></li> -->
                        </ul>
                      </td>
                      <td class="price text-center"><strong class="primary-color"><?php echo 'RM '.$this->cart->format_number($items['price']); ?></strong></td>
                      <td class="qty text-center">
                        <!-- <button type="button" id="<?php echo $items['rowid']; ?>" class="calc_decs"><<</button>
                        <input type="text" name="qty" class="text-center" min="1" value="<?php echo $items['qty']; ?>" id="input_qty" onchange="upd_val()">
                        <button type="button" id="<?php echo $items['rowid']; ?>" class="calc_inc">>></button> -->
                        <strong class="primary-color"><?php echo $items['qty']; ?></strong>
                      </td>
                      <td class="total text-center"><strong class="primary-color">
                        <?php echo 'RM '.$this->cart->format_number($items['subtotal']); ?>
                        <!-- <div id='subtotal'></div> -->
                      </strong></td>
                      <!-- <td class="text-right"><?php echo anchor('customer/clear_item_cart/'.$items['rowid'], '<span class="fa fa-trash"></span>', array('class'=>'btn btn-danger')); ?></td> -->
                    </tr>

                    <?php
                    $i++;
                    $total_sub=$total_sub+$items['subtotal'];
										$total_weight=$total_weight+($items['weight']*$items['qty']);
                    echo form_hidden('item_id[]', $items['id']);
                    echo form_hidden('item_name[]', $items['name']);
                    echo form_hidden('item_price[]', $items['price']);
                    echo form_hidden('item_qty[]', $items['qty']);
                    echo form_hidden('item_subtotal[]', $items['subtotal']);
										echo form_hidden('item_referrel[]', $items['referrel']);
										echo form_hidden('seller_id[]', $items['seller_id']);
										echo form_hidden('modal[]', $items['modal']);
										echo form_hidden('unit_price[]', $items['unit_price']);
										echo form_hidden('tax_price[]', $items['tax_price']);
										echo form_hidden('ship_price[]', $items['ship_price']);
										echo form_hidden('seller_price[]', $items['seller_price']);
                }


								if(!empty($this->cart->contents())):
								foreach ($this->cart->contents() as $i){
									$arr_bound_seller[]=$i['seller_id'];
								}
								$arr_seller=array_unique($arr_bound_seller);
								endif;

								if(!empty($arr_seller)):
								foreach ($arr_seller as $k) {
									$sub_weight_seller=0.0;
									foreach ($this->cart->contents() as $i){
										if($i['seller_id']==$k)
										{
											$sub_weight_seller=$sub_weight_seller+($i['weight']*$i['qty']);
										}
									}
									$arr_weight_seller[]=array('seller_id'=>$k,'weight_seller'=>$sub_weight_seller);
								}
							endif;

								$arr_shipping;
								if(!empty($arr_weight_seller)):
								foreach ($arr_weight_seller as $ws) {
									$sub_shipping=0;
									if(!empty($cost)){
										foreach ($cost as $c) {
											if($ws['seller_id']==$c['seller_id'] && $ship_state['state_area']==$c['area'])
											{
												if($ws['weight_seller'] >= $c['weightInitial_set'] && $ws['weight_seller'] <= $c['weightFinal_set']){
													$sub_shipping=$c['shipcost_set'];
													//echo $c['shipcost_set'];
												}
											}
										}
									}else{
										$sub_shipping=0;
									}
									$arr_shipping[]=array('seller_id'=>$ws['seller_id'],'weight_seller'=>$ws['weight_seller'],'sub_shipping'=>$sub_shipping);
									echo form_hidden('sub_seller_id[]', $ws['seller_id']);
									echo form_hidden('sub_weight_seller[]', $ws['weight_seller']);
									echo form_hidden('sub_shipping[]', $sub_shipping);
								}
							endif;

								$total_shipping=0;
								if(!empty($arr_shipping)):
								foreach ($arr_shipping as $key) {
									$total_shipping=$total_shipping+$key['sub_shipping'];
								}
							endif;
								$total_all = 0;
								$total_all = $total_sub+$total_shipping;
								$total_all_fee = $total_sub+$total_shipping+1;
								// print_r($arr_weight_seller);echo '<br>';
								// print_r($cost);echo '<br>';
								// print_r($arr_shipping);
								// echo $total_shipping;
                   ?>
                </tbody>
                <tfoot>
                   <tr>
                    <th class="empty" colspan="2"></th>
                    <th style="color:#F7F9F9;">SUB-TOTAL</th>
                    <th colspan="2" class="text-center primary-color">RM <?php echo number_format($total_sub,2, '.', '') ?></th>
                  </tr>
                  <tr>
                    <th class="empty" colspan="2"></th>
                    <th style="color:#F7F9F9;">TOTAL SHIPPING</th>
                    <td colspan="2" class="text-center"><input type="text" name="total_shipping1" id="total_shipping1" value="RM <?php echo $total_shipping ?>" readonly class="text-center"></td>
                  </tr>
									<tr>
                    <th class="empty" colspan="2"></th>
                    <th style="color:#F7F9F9;">ONLINE TRANSACTION FEE</th>
                    <td colspan="2" class="text-center"><input type="text" value="RM 1.00" readonly class="text-center"></td>
                  </tr>
                  <tr>
                    <th class="empty" colspan="2"></th>
                    <th class="total" style="color:#F7F9F9;">TOTAL</th>
                    <th colspan="2" class="total text-center">
											<input type="text" name="total_all1" id="total_all1" value="RM <?php echo number_format($total_all_fee,2, '.', '') ?>" readonly class="text-center">
										</th>
                  </tr>
                </tfoot>
              </table>
							<!-- <span class="text-danger">P/S : Premium fee, SST tax and Shipping are calculated based on total gold's weight.</span> -->
              <div class="pull-right">
                <?php echo form_hidden('total_weight', $total_weight); ?>
								<!-- <input type="hidden" name="weightcost_id" id="weightcost_id"> -->
								<input type="hidden" name="total_all" id="total_all" value="<?php echo number_format($total_all,2, '.', '') ?>">
								<input type="hidden" name="total_shipping" id="total_shipping" value="<?php echo $total_shipping ?>">
								<?php echo form_hidden('user_id', $user_profile['id']); ?>
								<?php echo form_hidden('email', $user_profile['email']); ?>
								<?php echo form_hidden('phone', $user_profile['phone']); ?>
								<?php echo form_hidden('full_name', $user_profile['full_name']); ?>
                <button class="btn btn-green-no" type="submit" name="btnPlaceOrder">Place Order</button>
              </div>
							<div class="pull-left">
                <span class="text-danger">Note : Please make sure your shipping details is correct.</span>
              </div>
                <?php echo form_close() ?>
            </div>
          </div>


    </div>
<script>
   function my_funct(){
    var ship = document.getElementById('other_ship');
    if(ship.checked){
      document.getElementById('show_ship').style.display="block";
    }else{
      document.getElementById('show_ship').style.display="none";
    }
  }

	function calc_func(){
		var total_sub = "<?php echo $total_sub ?>";
		var state_id = document.getElementById("state_id").value;
		var state = <?php echo json_encode($temp_state) ?>;
		var count_state = <?php echo count($temp_state) ?>;
		var total_weight = "<?php echo $total_weight ?>";
		var c = <?php echo json_encode($cost) ?>;
		var count_c = <?php echo count($cost) ?>;
		var count_ws = <?php echo count($arr_weight_seller) ?>;
		var arr_ws = <?php echo json_encode($arr_weight_seller) ?>;

		var total_all=0.00;
		var arr_shipping=[];var arr_all=[];
		for(var i=0;i<count_ws;i++){
			var sub_shipping=0.0;
			for(var j=0;j<count_state;j++)
			{
				if(state_id==state[j]['state_id'])
				{
					if(c!=null){
						for(var x=0;x<count_c;x++)
						{
							if(state[j]['state_area']==c[x]['area'] && arr_ws[i]['seller_id']==c[x]['seller_id'])
							{
								if(parseFloat(arr_ws[i]['weight_seller']) >= parseFloat(c[x]['weightInitial_set']) && parseFloat(arr_ws[i]['weight_seller']) <= parseFloat(c[x]['weightFinal_set']))
								{

									sub_shipping=c[x]['shipcost_set'];
								}
							}
						}
					}else{
						sub_shipping=0;
					}
				}
			}
			arr_shipping={seller_id:arr_ws[i]['seller_id'],weight_seller:arr_ws[i]['weight_seller'],sub_shipping:sub_shipping};
			arr_all.push(arr_shipping);
		}
		//console.log(arr_all);

		total_shipping=0.0;
		for(var d=0;d<arr_all.length;d++)
		{
			total_shipping=total_shipping+parseFloat(arr_all[d]['sub_shipping']);
		}
		total_all=parseFloat(total_sub)+parseFloat(total_shipping);
		//console.log(total_all);
		document.getElementById('total_shipping1').value = 'RM '+total_shipping;
		document.getElementById('total_all1').value = 'RM '+total_all.toFixed(2);
		document.getElementById('total_shipping').value = total_shipping;
		document.getElementById('total_all').value = total_all.toFixed(2);

	}
</script>
