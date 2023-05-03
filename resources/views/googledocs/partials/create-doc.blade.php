<div id="createGoogleDocModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Google Doc</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('google-docs.create') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <strong>Document type:</strong>

                        <select class="form-control" name="type" required>
                            <option value="spreadsheet">Spreadsheet</option>
                            <option value="doc">Doc</option>
                            <option value="ppt">Ppt</option>
                            <option value="xps">Xps</option>
                            <option value="txt">Txt</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="doc_name" value="{{ old('doc_name') }}" class="form-control input-sm" placeholder="Document Name" required>

                        @if ($errors->has('doc_name'))
                            <div class="alert alert-danger">{{$errors->first('doc_name')}}</div>
                        @endif
                    </div>

                    {{-- <input type="text" name="doc_category" value="{{ old('doc_category') }}" class="form-control input-sm" placeholder="Document Category" required> --}}
                    {{-- <div class="form-group">
                        <strong>Category:</strong>
                        <select name="doc_category" class="form-control" id="doc-category" required>
                            <option>Select Category</option>
                            @if (isset($googleDocCategory) && count($googleDocCategory) > 0)
                                @foreach ($googleDocCategory as $key => $category)
                                    <option value="{{$key}}">{{$category}}</option>
                                @endforeach
                            @endif
                        </select>
                        @if ($errors->has('doc_category'))
                            <div class="alert alert-danger">{{$errors->first('doc_category')}}</div>
                        @endif
                    </div> --}}

                    <div class="form-group">
                        <strong>Existing Doc Id:</strong>
                        <input type="text" name="existing_doc_id" value="{{ old('existing_doc_id') }}" class="form-control input-sm" placeholder="Existing Document ID">

                        @if ($errors->has('existing_doc_id'))
                            <div class="alert alert-danger">{{$errors->first('existing_doc_id')}}</div>
                        @endif
                    </div>
                    <div class="form-group custom-select2">
                        <label>Read Permission for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_multiple" multiple="multiple" name="read[]">
                                @foreach($users as $val)
                                <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
                    </div>
                    <div class="form-group custom-select2">
                        <label>Write Permission for Users
                        </label>
                        <select class="w-100 js-example-basic-multiple js-states"
                                id="id_label_multiple_write" multiple="multiple" name="write[]">
                                @foreach($users as $val)
                                <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Create</button>
                </div>
            </form>
        </div>

    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$("#id_label_multiple").select2();
$("#id_label_multiple_write").select2();
</script>
