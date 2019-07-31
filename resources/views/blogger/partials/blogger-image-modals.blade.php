<div id="bloggerImageModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content" style="width: 800px;left: -25%;">
            <div class="modal-header">
                <h4 class="modal-title">Blogger Images</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="blogger_images row">

                </div>
                {!! Form::open(['route'=>'blogger.image.upload','files'=>'true','method'=>'POST']) !!}
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <button type="submit" class="btn btn-sm btn-secondary">Add</button>
                {!! Form::close() !!}
            </div>
            <button id="show-form"><i class="fa fa-plus"></i></button>
            <div id="add_payment" style="display: none">
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-secondary add-cost-button">Add</button>
                </div>
            </div>
        </div>

    </div>
</div>