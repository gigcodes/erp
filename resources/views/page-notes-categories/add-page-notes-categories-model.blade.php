<div id="pageNotesCategoriesModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('notesCreate') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h4 class="modal-title">Create Notes</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- short_note -->
                        <div class="col-md-12 col-lg-12 @if($errors->has('short_note')) has-danger @elseif(count($errors->all())>0) has-success @endif">
                            <div class="form-group">
                                <div class="form-group">
                                    <strong>Title:</strong>
                                    <input type="text" name="title" class="form-control" value="" required>
                                </div>
                                <div class="form-group">
                                    <strong>Note URL:</strong>
                                    <input type="text" name="url" class="form-control" value="" required>
                                </div>
                                <div class="form-group">
                                    <strong>Category ID:</strong>
                                    <select name="category_id" id="" class="form-control" required>
                                        <option selected disabled>Select</option>
                                        @if (isset($category) && count($category))
                                            @foreach ($category as $key => $item)
                                                <option value="{{$key}}">{{$item}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group">
                                    <strong>Note:</strong>
                                    <textarea name="note" id="create_note" cols="30" rows="10" class="form-control"></textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Add</button>
                </div>
            </form>
        </div>

    </div>
</div>