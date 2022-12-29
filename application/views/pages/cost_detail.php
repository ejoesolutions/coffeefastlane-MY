<?php

//print_r($cost);
 ?>
 <div class="form-group">
   <div class="col-md-4">
       <label class="control-label">Min Weight (g)</label>
   </div>
   <div class="col-md-8">
    <input type="text" class="form-control" name="weightInitial_set" value="<?php echo number_format($cost['weightInitial_set'],3) ?>" required>
   </div>
 </div>
 <div class="form-group">
   <div class="col-md-4">
       <label class="control-label">Max Weight (g)</label>
   </div>
   <div class="col-md-8">
       <input type="text" class="form-control" name="weightFinal_set" value="<?php echo number_format($cost['weightFinal_set'],3) ?>" required>
   </div>
 </div>
 <div class="form-group">
   <div class="col-md-4">
       <label class="control-label">Shipping cost</label>
   </div>
   <div class="col-md-8">
       <input type="text" class="form-control" name="shipcost_set" value="<?php echo number_format($cost['shipcost_set'],2) ?>" required>
   </div>
 </div>
 <div class="form-group">
   <div class="col-md-4">
       <label class="control-label">Area</label>
   </div>
   <div class="col-md-8">
       <?php echo form_dropdown('area',array('1'=>'Semenanjung Malaysia','2'=>'Sabah & Sarawak'),$cost['area'],array('class'=>'form-control')) ?>
   </div>
 </div>
 <!-- <div class="form-group">
   <div class="col-md-4">
       <label class="control-label">Premium fee</label>
   </div>
   <div class="col-md-8">
       <input type="text" class="form-control" name="premium_set" value="<?php echo $cost['premium_set'] ?>" required>
   </div>
 </div>
 <div class="form-group">
   <div class="col-md-4">
       <label class="control-label">SST tax</label>
   </div>
   <div class="col-md-8">
       <input type="text" class="form-control" name="sst_set" value="<?php echo $cost['sst_set'] ?>" required>
   </div>

 </div> -->
<?php echo form_hidden('weightcost_id',$cost['weightcost_id']) ?>
<?php echo form_hidden('seller_id',$cost['seller_id']) ?>
 </div>
