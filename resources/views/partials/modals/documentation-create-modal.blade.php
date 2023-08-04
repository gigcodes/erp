<div id="documentaddModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title">Store a Document</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Document Users:</strong>
                        <?php echo Form::select("user_id",['' => ''],null,["class" => "form-control globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.updatedby_users'), 'data-placeholder' => 'users']); ?>
                        @if ($errors->has('user_id'))
                        <div class="alert alert-danger">{{$errors->first('user_id')}}</div>
                    @endif
                    </div>

                    <div class="form-group">
                        <strong>Document Type:</strong>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>

                        @if ($errors->has('name'))
                            <div class="alert alert-danger">{{$errors->first('name')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Document category:</strong>
                        <?php echo Form::select("category_id",['' => ''],null,["class" => "form-control globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.documentCategory'), 'data-placeholder' => 'Choose a Category']); ?>

                        @if ($errors->has('category'))
                            <div class="alert alert-danger">{{$errors->first('category')}}</div>
                        @endif
                    </div>
                    <input type="hidden" name="status" value="1">
                    <div class="form-group">
                        <strong>File:</strong>
                        <input type="file" name="file[]" class="form-control" value="" multiple required>

                        @if ($errors->has('file'))
                            <div class="alert alert-danger">{{$errors->first('file')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Version:</strong>
                        <input type="text" name="version" class="form-control" value="1" required>

                        @if ($errors->has('version'))
                            <div class="alert alert-danger">{{$errors->first('version')}}</div>
                        @endif
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Upload</button>
                </div>
            </form>
        </div>

    </div>
</div>
