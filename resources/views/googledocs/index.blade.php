@extends('layouts.app')
@section('favicon' , '')

@section('title', 'Google Docs')

@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    </style>

@endsection

@section('content')
    <div class="col-md-12">
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Docs</h2>
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        <form class="form-inline message-search-handler" method="get">
                                    <div class="form-group m-1">
                                        <input name="name" list="name-lists" type="text" class="form-control" placeholder="Search file" value="{{request()->get('name')}}" />
                                        <datalist id="name-lists">
                                            @foreach ($data as $key => $val )
                                                <option value="{{$val->name}}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="form-group sm-1">
                                        <input name="docid" list="docid-lists" type="text" class="form-control" placeholder="Search Url" value="{{request()->get('docid')}}" />
                                        <datalist id="docid-lists">
                                            @foreach ($data as $key => $val )
                                                <option value="{{$val->docId}}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="form-group px-2 googleDocCategory-container">
                                        <select class="w-100 js-example-basic-multiple js-states" id="googleDocCategoryFilter" multiple="multiple" name="googleDocCategory[]">
                                            @foreach ($googleDocCategory as $key => $c)
                                                <option value="{{$key}}">{{$c}}</option>
                                            @endforeach
                                        </select>
                                    </div>
				    @if(Auth::user()->isAdmin())
                                    <div class="form-group m-1">
                                        <select name="user_gmail" class="form-control" placeholder="Search User">
                                        <option value="">Search User</option>
                                            @foreach ($users as $key => $val )
                                                <option value="{{$val->gmail}}" @if(request()->get('user_gmail')==$val->gmail) selected @endif>{{$val->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
				    @endif
                                    <div class="form-group">
                                        <label for="button">&nbsp;</label>
                                        <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
                                            <img src="/images/search.png" style="cursor: default;">
                                        </button>
                                        <a href="/google-docs" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                                    </div>
                        </form>
                    </div>
                </div>
            </div>
	    @if(Auth::user()->isAdmin())
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#updatemultipleGoogleDocPermissionModal">
                    Add Permission
                  </button>   
                  <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#GoogleDocRemovePermissionModal">
                    Remove Permission for mulitiple users
                  </button>   
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#AddGoogleDocCategoryModal">
                    Create Category
                </button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#RemoveGoogleDocPermissionModal">
                    Remove Permission
                </button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createGoogleDocModal">
                    + Create Doc
                </button>
            </div>
	    @endif
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="googlefiletranslator-table">
            <thead>
            <tr>
                @if(Auth::user()->isAdmin())
                <th><input type="checkbox" name="select_all_doc" class="select_all_doc"></th>
                @endif
                <th>No</th>
                <th>File Name</th>
                <th>Category</th>
                <th>Task</th>
                <th>Created By</th>
                <th>Created At</th>
                <th>URL</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @include('googledocs.partials.list-files')
            </tbody>
        </table>
    </div>

    <div id="RemoveGoogleDocPermissionModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remove Google Doc Permission</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('google-docs.permission.remove') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group custom-select2">
                            <label>Select Remove Permission for Users
                            </label>
                            <select class="w-100 js-example-basic-multiple js-states"
                                    id="remove_permission_write"  name="remove_permission">
                                @foreach(App\User::whereNotNull('gmail')->get() as $val)
                                    <option value="{{$val->gmail}}" class="form-control">{{$val->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">remove</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div id="AddGoogleDocCategoryModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create new category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('google-docs.category.create') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group custom-select2">
                            <label>Category</label>
                            <input type="text" name="name" class="form-control">
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

    <div id="viewGoogleDocPermissionModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">View Google Doc Permission</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                        <div class="modal-body">
                            <input type="hidden" name="view_file_id" id = "view_file_id">
                            <input type="hidden" name="view_id" id = "view_id">
                            <div class="form-group custom-select2">
                                <label>Read Permission for Users</label>
                                <div class="read_permission_users">

                                </div>
                            </div>
                            <div class="form-group custom-select2">
                                <label>Write Permission for Users</label>
                                <div class="write_permission_users">

                                </div>
                            </div>
                        </div>
                </div>

            </div>
        </div>


    @include('googledocs.partials.create-doc')
    @include('googledocs.partials.update-doc-permissions')
    </div>
    @include('googledocs.partials.update-doc')

    <style>
        .select2-search--inline {
            display: contents; 
        }

        .select2-search__field:placeholder-shown {
            width: 100% !important; 
        }
        .multi-select-box .select2{
            width: 200px!important;
        }
        ul.select2-selection__rendered{
            display: block!important;
        }
        .googleDocCategory-container .select2-container{
            width: 200px!important
        }
    </style>
@endsection
@section('scripts')
<script type="text/javascript">
    $("#remove_permission_write").select2();
    $("#googleDocCategoryFilter").select2({
        multiple: true,
        placeholder: "Select Category"
    });
    $("#googleDocCategoryFilter").trigger('change');
    

$(document).on('click', '.permissionupdate', function (e) {

		$("#updateGoogleDocPermissionModal #id_label_permission_read").val("").trigger('change');
		$("#updateGoogleDocPermissionModal #id_label_permission_write").val("").trigger('change');
		
        let data_read = $(this).data('readpermission');
        let data_write = $(this).data('writepermission');
		var file_id = $(this).data('docid');
        var id = $(this).data('id');
		var permission_read = data_read.split(',');
		var permission_write = data_write.split(',');
		if(permission_read)
		{
			$("#updateGoogleDocPermissionModal #id_label_permission_read").val(permission_read).trigger('change');
		}
		if(permission_write)
		{
			$("#updateGoogleDocPermissionModal #id_label_permission_write").val(permission_write).trigger('change');
		}
		
		$('#file_id').val(file_id);
        $('#id').val(id);
	
	});

$(document).on('click', '.permissionview', function (e) {
    var id = $(this).data('id');

    $.ajax({
        type: 'POST',
        url: '{{ route('google-docs.permission.view') }}',
        data: {id: id},
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
    }).done(response => {
        if(response.code == 200) {
            var read = response.read.split(',')
            var write = response.write.split(',');
            var data_read = [];
            var data_write = [];

            $.each(read, function (index, value) {
                data_read += '<div class="py-2 px-4 m-1 d-inline-block" style="border: 1px solid #000; border-radius:4px; cursor: text;">' + value + '</div>';
            });

            $.each(write, function (index, value) {
                data_write += '<div class="py-2 px-4 m-1 d-inline-block" style="border: 1px solid #000; border-radius:4px; cursor: text;">' + value + '</div>';
            });
            console.log(data_read)

            $(".read_permission_users").html(data_read);
            $(".write_permission_users").html(data_write);

            $("#viewGoogleDocPermissionModal").modal("show");
        }else{
            toastr['error']('Oops, something went wrong', 'error');
        }
    });
});
    $(document).on('click', '.google-doc-update', function (e) {
		var action = $(this).data('action');
        $.ajax({
            type: "GET",
            url: action,
            data: {_token: "{{ csrf_token() }}"},
            dataType: "json",
            success: function (response) {
                // $('#updateGoogleDocModal input[name=doc_category]').val(response.data.category);
                $("#editGoogleDocCategory").val(response.data.category);
                $('#updateGoogleDocModal input[name=docId]').val(response.data.docId);
                $('#updateGoogleDocModal [name=type]').val(response.data.type);
                $('#updateGoogleDocModal input[name=name]').val(response.data.name);
                $('#updateGoogleDocModal input[name=id]').val(response.data.id);
            }
        });
	});
    $(document).on("change", ".update-category", function () {
        var doc_id = $(this).data("docs_id")
        var category_id = $(this).val()

        if(doc_id != "" && doc_id != null) {
            $.ajax({
                type: "post",
                url: "{{ route('google-docs.category.update') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    category_id, doc_id
                },
                success: function (response) {
                    if(response.status == true) {
                        toastr['success'](response.message, 'Note');
                    } else {
                        toastr['error'](response.message, 'Error');
                    }
                }, 
                error: function(error) {
                    toastr['error']("Something went wrong.", 'Error');
                }
            });
        }
        
    });

    $(document).ready(function() {
        $('#updatemultipleGoogleDocPermissionModal').on('submit', function(e) {
        e.preventDefault();

        var selectedCheckboxes = [];
        var fileIDs = [];

        if ($('.select_all_doc').prop('checked')) {
                $('.google_doc_check').each(function() {
                    var fileID = $(this).data('id');
                    var checkboxValue = $(this).val();

                    fileIDs.push(fileID);
                    selectedCheckboxes.push(checkboxValue);
                });
            } else {
                $('input[name="google_doc_check"]:checked').each(function() {
                    var fileID = $(this).data('id');
                    var checkboxValue = $(this).val();

                    fileIDs.push(fileID);
                    selectedCheckboxes.push(checkboxValue);
                });
            }

            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one checkbox.');
                return;
            }

        $('#add_doc_ids').val(selectedCheckboxes.join(','));

        $(this).unbind('submit').submit()
        });


    $('#GoogleDocRemovePermissionModal').on('submit', function(e) {
        e.preventDefault(); 
        var selectedCheckboxes = [];
        var fileIDs = [];

        if ($('.select_all_doc').prop('checked')) {
                $('.google_doc_check').each(function() {
                    var fileID = $(this).data('id');
                    var checkboxValue = $(this).val();

                    fileIDs.push(fileID);
                    selectedCheckboxes.push(checkboxValue);
                });
            } else {
                $('input[name="google_doc_check"]:checked').each(function() {
                    var fileID = $(this).data('id');
                    var checkboxValue = $(this).val();

                    fileIDs.push(fileID);
                    selectedCheckboxes.push(checkboxValue);
                });
            }

            if (selectedCheckboxes.length === 0) {
                alert('Please select at least one checkbox.');
                return;
            }

        $('#remove_doc_ids').val(selectedCheckboxes.join(','));

        $(this).unbind('submit').submit()
        });
        
        $('.select_all_doc').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.google_doc_check').prop('checked', isChecked);
        });
    });
    
    </script>
@endsection
