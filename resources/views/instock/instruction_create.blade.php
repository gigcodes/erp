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
      <label>Location</label>
      <?php echo Form::text("location_name",null,["class" => "form-control"]); ?>
    </div>
    <div class="form-group">
      <label>Courier Name</label>
      <?php echo Form::text("courier_name",null,["class" => "form-control"]); ?>
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