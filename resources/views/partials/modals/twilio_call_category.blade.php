<div id="twilio_category_add" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      
        <div class="modal-header">
          <h4 class="modal-title">Add Category</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
         <form action="<?php echo route('twilio.create.category'); ?>" method="post">
        <div class="modal-body">
         
            {{csrf_field()}}
            <div class="form-group">
            <?php echo Form::text("categoryfield",null,["class" => "form-control categoryfield", "placeholder" => "Enter Your Category"]); ?>
          </div>

          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button class="btn btn-secondary">Create Category</button>
        </div>
      </form>
    </div>

  </div>
</div>
