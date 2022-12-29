<div class="row">

	<div class="col-md-6">
		<div class="">
			<div class="section-title">
				<h3 class="primary-color">Already a Member? Login</h3>
			</div>
			<?php echo form_open('member/login',array('class'=>'clearfix')) ?>
			<div class="form-group">
				<label class="control-label" style="color:#F7F9F9;">Username</label>
				<input class="form-control" type="text" name="identity" placeholder="Username/Email" required autocomplete="off">
			</div>
			<div class="form-group">
				<label class="control-label" style="color:#F7F9F9;">Password</label>
				<input class="form-control" type="password" name="password" placeholder="Password" required autocomplete="new-password">
			</div>
			<div class="form-group">
				<table width="100%">
					<tr>
						<!--<td><input type="submit" class="btn btn-warning" name="submit" value="Login"></td>-->
						<td><button type="submit" class="btn btn-green-no" name="submit">Login</button></td>
						<!--<td align="right">Forgot Password? <a href="<?php echo base_url('customer/reset') ?>"> <b>Reset Here</b></a></td>-->
						<td align="right" style="color:#F7F9F9;">Forgot Password? <a class="primary-color" data-toggle="modal" href="#forgotPswd"><b>Reset Here<b></a></td>
					</tr>
				</table>
			</div>
			<?php echo form_close() ?>
	</div>

	<div class="modal fade bs-modal-xl" id="forgotPswd" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
					<h4 class="modal-title">Reset Password</h4>
				</div>
				<div class="modal-body">
					<p>Enter your username/registered email below to reset your password. </p>
					<?php echo form_open('user/forgot_password',array('class'=>'form-horizontal')) ?>
					<div class="form-group">
						<div class="col-md-4">
								<label class="control-label">Username/Email</label>
						</div>
						<div class="col-md-8">
								<input type="text" class="form-control" name="identity" required>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<?php echo form_hidden('user_group',2); ?>
					<button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-success">Submit</button>
				</div>
				<?php echo form_close() ?>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>

</div>

	<div class="col-md-6">
		<?php echo form_open('',array('class'=>'clearfix')) ?>
		<div class="billing-details">
			<div class="section-title">
				<h3 class="primary-color">Create Account</h3>
			</div>
			<div class="form-group">
				<?php echo form_input($full_name); ?>
				<?php echo form_error('full_name', '<p class="text-danger">', '</p>'); ?>
			</div>
			<?php if($identity_column!=='email') { ?>
				<div class="form-group">
					<?php echo form_input($identity); ?>
					<?php echo form_error('identity','<p class="text-danger">', '</p>'); ?>
				</div>
			<?php } ?>
			<div class="form-group">
				<?php echo form_input($email); ?>
				<?php echo form_error('email', '<p class="text-danger">', '</p>'); ?>
			</div>
			<div class="form-group">
				<!-- <input class="input" type="text" name="address" placeholder="Address"> -->
				<?php echo form_textarea($address); ?>
				<?php echo form_error('address', '<p class="text-danger">', '</p>'); ?>
			</div>
			<div class="form-group">
				<?php echo form_input($postcode); ?>
				<?php echo form_error('postcode', '<p class="text-danger">', '</p>'); ?>
			</div>
			<div class="form-group">
					<?php echo form_input($town_area); ?>
					<?php echo form_error('town_area', '<p class="text-danger">', '</p>'); ?>
			</div>
			<div class="form-group">
					<?php echo form_dropdown('state_id', $state, set_value('state_id'), array('class'=>'form-control','required'=>'required')) ?>
			</div>
			<div class="form-group">
				<?php echo form_input($phone); ?>
				<?php echo form_error('phone', '<p class="text-danger">', '</p>'); ?>
			</div>
			<div class="form-group">
				<?php echo form_input($refer_member); ?>
				<?php echo form_error('refer_member', '<p class="text-danger">', '</p>'); ?>
				<!-- <input type="text" name="refer_member" class="form-control" value="<?= ($this->uri->segment(3)) ? ($this->uri->segment(3)) : '' ?>" placeholder="Insert Referrel Number If Any"> -->
			</div>
			<div class="form-group">
			<?php echo form_input($password); ?>
			<?php echo form_error('password','<p class="text-danger">', '</p>'); ?>
		</div>
		<div class="form-group">
			<?php echo form_input($password_confirm); ?>
			<?php echo form_error('password_confirm','<p class="text-danger">', '</p>'); ?>
		</div>
			<div class="form-actions">
				<input type="hidden" name="user_group" value="2">
				<input type="hidden" name="active" value="1">
				<button type="submit" id="register-submit-btn" class="btn btn-green-no">Register</button>
			</div>
		</div>
		<?php echo form_close() ?>
	</div>


</div>
