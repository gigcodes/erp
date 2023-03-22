<!-- Modal -->
<div id="quick_notes_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Notes</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <!-- data-url="{{ route('notesCreate') }}" -->
                <div class="form-group">
                    <strong>Title:</strong>
                    <input type="text" name="page_note_title" id="page_note_title" value="{{ old('page_note_title') }}" class="form-control input-sm" placeholder="PageNote Title">
                    @if ($errors->has('page_note_title'))
                        <div class="alert alert-danger">{{$errors->first('page_note_title')}}</div>
                    @endif
                </div>
                <div class="form-group">
                    <strong>Category list:</strong>

                    <select class="form-control" name="category_name" id="category_name" required>
                        @if(!empty($category))
                            @foreach($category as $key => $val)
                                <option value="{{$key}}" class="form-control">{{$val}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <strong>Note:</strong>
                    <textarea id="editor-notes-content"  class="editor-notes-content" name="instruction"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn_save_notes btn btn-secondary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
