<div id="colorCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">

            {!! Form::open(['route' => 'magento_module_types.store', 'method' => 'POST', 'class' => 'form mb-15', 'enctype' => 'multipart/form-data']) !!}
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Add {{ $title }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <strong>Module Category :</strong>
                    {!! Form::text('module_type', null, ['placeholder' => 'Module Type', 'id' => 'module_type', 'class' => 'form-control', 'required' => 'required']) !!}
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Add</button>
            </div>
            {!! Form::close() !!}
        </div>

    </div>
</div>

<div id="colorEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <form action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4 class="modal-title">Update Store Color</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Module Category :</strong>
                            {!! Form::text('module_type', null, ['placeholder' => 'Module Type', 'id' => 'module_type', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('store_color'))
                                <div class="alert alert-danger">{{ $errors->first('store_color') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Update</button>
                </div>
            </form>
        </div>

    </div>
</div>
