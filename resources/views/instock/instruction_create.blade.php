<div class="col-md-12">
    <?php echo Form::hidden("product_id",$productId,["class" => "instruction-pr-id"]); ?>
    <div class="form-group">
        <label>Instruction Type:</label>
        <?php echo Form::select("instruction_type",["location" => "Change Location","dispatch" => "Product Dispatch"],null,["class" => "form-control instruction-type-select"]); ?>
    </div>
     <div class="form-group">
        <label>Instruction Message:</label>
        <?php echo Form::textArea("instruction_message",null,["class" => "form-control"]); ?>
    </div>
    <div class="form-group">
      <label>Assign To:</label>
      <?php echo Form::select("assign_to",$users,null,["class" => "form-control"]); ?>
    </div>
    <div class="form-group dispatch-instruction dis-none">
      <label>Dispatch To:</label>
      <?php echo Form::select("order_id",$order,null,["class" => "form-control"]); ?>
    </div>
    <div class="form-group">
      <label>New Location <a target="_blank" href="<?php echo url("product-location") ?>">Create new?</a></label>
      <?php echo Form::select("location_name",$locations,null,["class" => "form-control"]); ?>
    </div>
    <div class="form-group">
      <label>Courier Name <a target="_blank" href="<?php echo url("courier") ?>">Create new?</a></label>
      <?php echo Form::select("courier_name",$couriers,null,["class" => "form-control"]); ?>
    </div>
    <div class="form-group">
      <label>Courier Details</label>
      <?php echo Form::textArea("courier_details",null,["class" => "form-control"]); ?>
    </div>
    <div class="form-group">
      <label>Date</label>
      <?php echo Form::text("date_time",null,["class" => "form-control date-time-picker"]); ?>
    </div>
</div>