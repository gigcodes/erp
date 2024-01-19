@extends('layouts.app')

@section('content')
<div class="row ">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Quick Replies List</h2>
        <div class="pull-left">
            <div class="row m-4">
                <div class="col-md-4">            
                    <form action="{{ route('reply.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-6 p-0">
                                <select class="form-control" name="category_id" id="category_id">
                                    <option value="">Select Category</option>
                                    @foreach($reply_categories as $cat)
                                        <option {{request()->get('category_id')==$cat->id ? 'selected' : ''}} value="{{$cat->id}}">{{$cat->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-5 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                            </div>

                            <div class="col-md-1 pd-sm">
                                 <button type="submit" class="btn btn-image search mt-0" onclick="document.getElementById('download').value = 1;">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-8">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#categoryModal">Create Category</button>
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#categorySubModal">Create Sub Category</button>
                        <a class="btn btn-secondary" href="{{ route('reply.create') }}">+</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="row ml-4 mr-4">
    <div class="col-md-12">
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="panel mt-3 panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" href="#collapse1">Category Assignments</a>
                    </h4>
                </div>
                <div id="collapse1" class="panel-collapse collapse">
                    <div class="panel-body">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>Model</th>
                                <th>Category</th>
                            </tr>
                            
                            <tr>
                                <td>Users</td>
                                <td>
                                    <select class="form-control update-default-category" name="users_category" data-id="users">
                                        <option value="">None</option>
                                        @foreach($reply_categories as $cat)
                                        <option value="{{$cat->id }}" {{$cat->default_for=='users' ? 'selected': ''}}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Customers</td>
                                <td>
                                    <select class="form-control update-default-category" name="customers_category" data-id="customers">
                                        <option value="">None</option>
                                        @foreach($reply_categories as $cat)
                                        <option value="{{$cat->id }}" {{$cat->default_for=='customers' ? 'selected': ''}}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Vendors</td>
                                <td>
                                    <select class="form-control update-default-category" name="vendors_category" data-id="vendors">
                                        <option value="">None</option>
                                        @foreach($reply_categories as $cat)
                                        <option value="{{$cat->id }}" {{$cat->default_for=='vendors' ? 'selected': ''}}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                           
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tab-content ">
    <!-- Pending task div start -->
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;"> 
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="10%">Category</th>
                            <th width="50%">Name</th>
                            <th width="10%">Model</th>
                            <th width="20%">Action</th>
                        </tr>
                        @foreach ($replies as $key => $reply)
                            <tr>
                                <td id="reply_id">{{ $reply->id }}</td>
                                <td id="reply_category_name">{{ ($reply->category) ?  $reply->category->name : '-' }}</td>
                                <td id="reply_text">{{ $reply->reply }}</td>
                                <td id="reply_model">{{ $reply->model }}</td>
                                <td>
                                    <a class="btn btn-image" href="{{ route('reply.edit',$reply->id) }}"><img src="/images/edit.png" /></a>
                                    <a class="btn intent-edit" data-toggle="modal" data-target="#auto-reply-popup">
                                      <span>Add Popup</span>
                                    </a>
                                    {!! Form::open(['method' => 'DELETE','route' => ['reply.destroy',$reply->id],'style'=>'display:inline']) !!}
                                    <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                                    {!! Form::close() !!}

                                    <button type="button"  class="btn btn-image btn-copy-reply" data-id="{{ $reply->reply }} ">
                                      <i class="fa fa-clone" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                    {!! $replies->links() !!}
            </div>
        </div>
    </div>
</div>                        

<div id="categoryModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Category</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('reply.category.store') }}" method="POST" enctype="multipart/form-data" id="approvalReplyForm">
                @csrf

                <div class="modal-body">

                    <div class="form-group">
                        <strong>Category Name:</strong>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @if ($errors->has('name'))
                            <div class="alert alert-danger">{{$errors->first('name')}}</div>
                        @endif
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

<div id="categorySubModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Sub Category</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="{{ route('reply.subcategory.store') }}" method="POST" enctype="multipart/form-data" id="approvalReplyForm">
                @csrf

                <div class="modal-body">

                    <div class="form-group">
                        <strong>Category:</strong>
                        <select class="form-control" name="parent_id" id="parent_id">
                            <option value="">Select Category</option>
                            @foreach($reply_main_categories as $cat)
                                <option value="{{$cat->id}}">{{$cat->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <strong>Sub Category Name:</strong>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @if ($errors->has('name'))
                            <div class="alert alert-danger">{{$errors->first('name')}}</div>
                        @endif
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


@include('partials.modals.auto-reply')

<script type="text/javascript">
$( document ).ready(function() {
    $(document).on("change",".update-default-category",function() {
        var model=$(this).attr('data-id');
        var cat_id=$(this).val();
        console.log(model);
        console.log(cat_id);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "post",
            url: '/reply/category/setDefault',
            data: {
                "model": model,
                "cat_id": cat_id,
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    toastr['success'](response.message);
                } else {
                    
                    toastr['error'](response.message);
                }
            },
            error: function() {
                toastr['error']('Something might be wrong please try again later!');
            }
        });

    });
});

$(document).on("click",".intent-edit",function() {
    var reply_model = $(this).closest("tr").children('#reply_model').text();
    var reply_text = $(this).closest("tr").children('#reply_text').text();
    var reply_category_name = $(this).closest("tr").children('#reply_category_name').text();
    var reply_id = $(this).closest("tr").children('#reply_id').text();
    $('#reply_id_edit').val(reply_id);
    $('#intentValues').val(reply_text);
    $('#intentReply').val(reply_text);
    $('#intentModel').val(reply_model);
    $("#intentCategory option").each(function() {
        if($(this).text() == reply_category_name) {
            $(this).attr('selected', 'selected');
        }
    });
});

var searchForIntent = function(ele) {
    var intentBox = ele.find(".search-intent");
    if (intentBox.length > 0) {
        intentBox.select2({
            placeholder: "Enter intent name or create new one",
            width: "100%",
            tags: true,
            allowClear: true,
            ajax: {
                url: '/chatbot/question/search',
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data.items
                    };
                }
            }
        }).on("change.select2", function() {
            var $this = $(this);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "post",
                url: '/chatbot/question/submit',
                data: {
                    "name": $this.val(),
                    "question": $("#dialog-save-response-form").find(".question-insert").val(),
                    "category_id" : $("#dialog-save-response-form").find(".search-category").val()
                },
                dataType: "json",
                success: function(response) {
                    if (response.code != 200) {
                        toastr['error']('Can not store intent please review or use diffrent name!');
                    } else {
                        toastr['success']('Success!');
                    }
                },
                error: function() {
                    toastr['error']('Can not store intent name please review!');
                }
            });
        });
    }
};
searchForIntent($("#auto-reply-popup"));

$(document).on("click",".btn-copy-reply",function() {
    var password = $(this).data('id');
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(password).select();
    document.execCommand("copy");
    $temp.remove();
    alert("Copied!");
});
</script>
@endsection