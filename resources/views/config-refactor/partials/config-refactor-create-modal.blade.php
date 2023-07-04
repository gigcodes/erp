<div id="config-refactor-create" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="<?php echo route('config-refactor.store'); ?>" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Create Config Refactor</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                        <?php echo csrf_field(); ?>
                        <div class="form-group normal-subject">
                            <label for="name">Section Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label for="type">Section Type<span class="text-danger">*</span></label>
                            <?php echo Form::select("type", ['ND' => 'Non Default', 'DE' => 'Default'], null, ["class" => "form-control type globalSelect2", "style" => "width:100%;", 'data-placeholder' => 'Select section type']); ?>
                        </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary save-config-refactor-window">Save</butto>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>