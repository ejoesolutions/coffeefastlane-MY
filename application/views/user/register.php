<div class="portlet box blue">
  <div class="portlet-title">
    <div class="caption">
      <?php echo $title ?>
    </div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-12 margin-top-20">
        <?php echo form_open() ?>

          <div class="form-group">
            <label class="control-label">USER'S CATEGORY</label>
            <?php echo form_dropdown('user_group', array(''=>'CHOOSE USER','1'=>'ADMIN', '3'=>'STAF'), set_value('user_group'), array('class'=>'form-control','required'=>'required')) ?>
          </div>
          <div class="form-group">
            <label class="control-label">FULL NAME</label>
            <?php echo form_input($full_name); ?>
            <?php echo form_error('full_name', '<p class="text-danger">', '</p>'); ?>
          </div>
          <?php if($identity_column!=='email') { ?>
            <div class="form-group">
              <label class="control-label">USERNAME</label>
              <?php echo form_input($identity); ?>
              <?php echo form_error('identity','<p class="text-danger">', '</p>'); ?>
            </div>
          <?php } ?>
          <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label">PHONE NO.</label>
            <?php echo form_input($phone); ?>
            <?php echo form_error('phone', '<p class="text-danger">', '</p>'); ?>
          </div>
          <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label">ADDRESS</label>
            <?php echo form_textarea($address); ?>
            <?php echo form_error('address', '<p class="text-danger">', '</p>'); ?>
          </div>
          <div class="form-group">
            <label class="control-label">POSTCODE</label>
            <?php echo form_input($postcode) ?>
            <?php echo form_error('postcode', '<p class="text-danger">', '</p>'); ?>
          </div>
          <div class="form-group">
            <label class="control-label">AREA</label>
            <?php echo form_input($town_area) ?>
            <?php echo form_error('town_area', '<p class="text-danger">', '</p>'); ?>
          </div>
          <div class="form-group">
            <label class="control-label">STATE</label>
            <?php echo form_dropdown($state_id,$state) ?>
            <?php echo form_error('state_id', '<p class="text-danger">', '</p>'); ?>
          </div>
          <hr>
          <!-- <p class="hint"> Maklumat Pengguna Sistem </p> -->
          <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label">EMAIL</label>
            <?php echo form_input($email); ?>
            <?php echo form_error('email', '<p class="text-danger">', '</p>'); ?>
          </div>

          <div class="form-group">
            <label class="control-label">PASSWORD</label>
            <?php echo form_input($password); ?>
            <?php echo form_error('password','<p class="text-danger">', '</p>'); ?>
          </div>
          <div class="form-group">
            <label class="control-label">PASSWORD CONFIRMATION</label>
            <?php echo form_input($password_confirm); ?>
            <?php echo form_error('password_confirm','<p class="text-danger">', '</p>'); ?>
          </div>
          <hr>

          <div class="form-group">
            <label class="control-label">STATUS</label><br>
            <input type="radio" name="active" value="1" checked="checked">Active<br>
            <!-- <input type="radio" name="active" value="0">Tak Aktif -->
          </div>
        <!-- <div class="form-group">
          <input type="radio" name="type" value="1" checked="checked">Admin Wakalah<br>
          <input type="radio" name="type" value="2">Staff Wakalah
        </div> -->


          <!-- <div class="form-group margin-top-20 margin-bottom-20">
            <label class="mt-checkbox mt-checkbox-outline">
              <input type="checkbox" name="tnc" /> I agree to the
              <a href="javascript:;">Terms of Service </a> &
              <a href="javascript:;">Privacy Policy </a>
              <span></span>
            </label>
            <div id="register_tnc_error"> </div>
          </div> -->
          <div class="form-actions">
            <button type="submit" id="register-submit-btn" class="btn btn-primary">Register</button>
          </div>

        <?php echo form_close() ?>

      </div>
    </div>
  </div>
</div>
