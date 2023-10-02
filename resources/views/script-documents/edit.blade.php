<div id="scriptdocumentEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="edit-h3">Edit Script Document</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route'=> ['script-documents.update' ]  ]) !!}

                <div class="form-group {{ $errors->has('file') ? 'has-error' : '' }}">
                    <label> File </label>
                    <input class="form-control id" name="id" type="hidden">
                    <input class="form-control" id="file_update" name="file" type="text">
                    <span class="text-danger">{{ $errors->first('file') }}</span>
                </div>

                <div class="form-group {{ $errors->has('usage_parameter') ? 'has-error' : '' }}">
                    <label> Usage Parameter </label>
                    <input class="form-control" id="usage_parameter_update" name="usage_parameter" type="text">
                    <span class="text-danger">{{ $errors->first('usage_parameter') }}</span>
                </div>

                <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                    <label> Category</label>                
                    <input class="form-control" id="category_update" name="category" type="text">
                    <span class="text-danger">{{ $errors->first('category') }}</span>
                </div>

                <div class="form-group {{ $errors->has('comments') ? 'has-error' : '' }}">
                    <label> Comments</label>                
                    <textarea class="form-control" id="comments_update" name="comments"></textarea>
                    <span class="text-danger">{{ $errors->first('comments') }}</span>
                </div>

                <div class="form-group {{ $errors->has('author') ? 'has-error' : '' }}">
                    <label> Author</label>                
                    <input class="form-control" id="author_update" name="author" type="text">
                    <span class="text-danger">{{ $errors->first('author') }}</span>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-secondary btn-update-bug">Store</button>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.btn-update-bug', function() {
	$('.text-danger').html('');
	if($('#file_update').val() == '') {
        $('#file_update').next().text("Please enter the file");
        return false;
    }
    
    if($('#usage_parameter_update').val() == '') {
        $('#usage_parameter_update').next().text("Please enter the usage parameter");
        return false;
    }

    if($('#category_update').val() == '') {
        $('#category_update').next().text("Please enter the category");
        return false;
    }

    if($('#comments_update').val() == '') {
        $('#comments_update').next().text("Please enter the comments");
        return false;
    }

    if($('#author_update').val() == '') {
        $('#author_update').next().text("Please enter the author");
        return false;
    }

	return true;
});
</script>