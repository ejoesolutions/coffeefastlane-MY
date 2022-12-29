<div class="row">
		<div class="col-md-2">
			<?php $this->load->view('includes/side_menu_cust') ?>
		</div>
		
		<div class="col-md-10">
			<h4 class="primary-color">My Addresses</h4>
				<!-- <a class="btn btn-success" data-toggle="modal" href="#addAddress"> Add New Address </a> -->
			<!-- <button type="button" class="btn btn-success">+ Add New Address</button> -->
			<hr>
			<table class="" border=0  width="100%">
					<tr>
						<td colspan="2" class="primary-color">Shipping Address</td>
					</tr>
						<tr>
							<td class="primary-color">Name</td>
							<td style="color:#F7F9F9;"><b><?php echo $ship['ship_name'] ?><b></td>
						</tr>
						<tr>
							<td class="primary-color">Phone</td>
							<td style="color:#F7F9F9;"><?php echo $ship['ship_phone'] ?></td>
						</tr>
						<tr>
							<td class="primary-color">Address</td>
							<td style="color:#F7F9F9;"><?php echo $ship['ship_address'].'<br>'.$ship['ship_postcode'].' '.$ship['ship_area'].'<br>'.$ship['state'] ?></td></td>
						</tr>
						<tr>
							<td><a class="" data-toggle="modal" href="#editShipAddress"><button class="btn btn-primary">Edit</button></a></td>
						</tr>
			</table>


            <!-- Modal section -->

            <!-- edit address -->
						<div class="modal fade bs-modal-xl" id="editShipAddress" tabindex="-1" role="dialog" aria-hidden="true">
	            <div class="modal-dialog modal-xl">
	              <div class="modal-content">
	                <div class="modal-header">
	                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	                  <h4 class="modal-title">Edit Shipping Address</h4>
	                </div>
	                <div class="modal-body">
	                  <?php echo form_open('member/update_shipAddress',array('class'=>'form-horizontal')) ?>
	                  <div class="form-group">
	                    <div class="col-md-4">
	                        <label class="control-label">Name</label>
	                    </div>
	                    <div class="col-md-8">
	                        <input type="text" class="form-control" name="ship_name" value="<?php echo $ship['ship_name'] ?>" required>
	                    </div>
	                  </div>
	                  <div class="form-group">
	                    <div class="col-md-4">
	                        <label class="control-label">Phone</label>
	                    </div>
	                    <div class="col-md-8">
	                        <input type="text" class="form-control" name="ship_phone" value="<?php echo $ship['ship_phone'] ?>" required>
	                    </div>
	                  </div>
	                  <div class="form-group">
	                    <div class="col-md-4">
	                        <label class="control-label">Address</label>
	                    </div>
	                    <div class="col-md-8">
	                        <textarea name="ship_address" class="form-control" required value="<?php echo $ship['ship_address'] ?>"><?php echo $ship['ship_address'] ?></textarea>
	                    </div>
	                  </div>
	                  <div class="form-group">
	                    <div class="col-md-4">
	                        <label class="control-label">Postcode</label>
	                    </div>
	                    <div class="col-md-8">
	                        <input type="text" class="form-control" name="ship_postcode" value="<?php echo $ship['ship_postcode'] ?>" required>
	                    </div>
	                  </div>
										<div class="form-group">
	                    <div class="col-md-4">
	                        <label class="control-label">Area</label>
	                    </div>
	                    <div class="col-md-8">
	                        <input type="text" class="form-control" name="ship_area" value="<?php echo $ship['ship_area'] ?>" required>
	                    </div>
	                  </div>
										<div class="form-group">
	                    <div class="col-md-4">
	                        <label class="control-label">State</label>
	                    </div>
	                    <div class="col-md-8">
	                        <?php echo form_dropdown('ship_state',$state,$ship['ship_state'],array('class'=>'form-control','required'=>'required')) ?>
	                    </div>
	                  </div>

	                </div>
	                <div class="modal-footer">
	                  <?php echo form_hidden('shipping_id',$ship['shipping_id']) ?>
	                  <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
	                  <button type="submit" class="btn btn-success">Save</button>
	                </div>
	                <?php echo form_close() ?>
	              </div>
	              <!-- /.modal-content -->
	            </div>
	            <!-- /.modal-dialog -->
	          </div>
					</div>
</div>
