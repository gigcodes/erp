@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Quick Replies List</h2>
        <div class="pull-left">
            <div class="row">
                <div class="col-md-12 ml-5">            
                    <form action="{{ route('reply.replyList') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-6 pd-sm">
                                {{ Form::select("store_website_id", ["" => "-- Select Website --"] + \App\StoreWebsite::pluck('website','id')->toArray(),request('store_website_id'),["class" => "form-control"]) }}
                            </div>
                            <div class="col-md-5 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                            </div>
                            

                            <div class="col-md-1 pd-sm">
                                 <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                            </div>
                        </div>
                    </form>
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

<div class="tab-content ">
    <!-- Pending task div start -->
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;"> 
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="2%" style="display:block;">ID</th>
                            <th width="2%">Store website</th>
                            <th width="25%">Category</th>
                            <th width="10%">Reply</th>
                            <th width="5%">Model</th>
                            <th width="15%">Updated On</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($replies as $key => $reply)
                            <tr>
                                <td id="reply_id">{{ $reply->id }}</td>
                                <td id="reply-store-website">{{ $reply->website }}</td>
                                <td id="reply_category_name">{{ $reply->parentList() }} > {{ $reply->category_name }}</td>
                                <td style="cursor:pointer;" id="reply_text" class="change-reply-text" data-id="{{ $reply->id }}" data-message="{{ $reply->reply }}">{{ $reply->reply }}</td>
                                <td id="reply_model">{{ $reply->model }}</td>
                                <td id="reply_model">{{ $reply->created_at }}</td>
                                <td id="reply_action"><i onclick="return confirm('Are you sure you want to delete this record?')" class="fa fa-trash fa-trash-bin-record" data-id="{{ $reply->reply_cat_id }}"></i></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                    {!! $replies->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reply-update-form-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
             <form method="POST" action="{{route('reply.replyUpdate')}}" id="reply-update-form" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Update reply</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="id" id="reply-update-model-text-id">
                            <textarea id="reply-update-model-text-reply" class="form-control" name="reply"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="create-camp-btn" class="btn btn-secondary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

$(document).on("click",".fa-trash-bin-record",function() {
    var $this = $(this);
    $.ajax({
        url: "{{ url('reply-list/delete') }}",
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          id: $this.data("id")
        },
        beforeSend: function() {
            $("#loading-image").show();
        }
      }).done( function(response) {
            $("#loading-image").hide();
            if(response.code == 200) {
                toastr["success"](response.message);
                location.reload();
            }else{
               toastr["error"]('Record is unable to delete!');
            }
      }).fail(function(errObj) {
            $("#loading-image").hide();
      });
});

$(document).on("click",".change-reply-text",function(e) {
    e.preventDefault();
    var $this = $(this);
    $("#reply-update-model-text-id").val($this.data("id"));
    $("#reply-update-model-text-reply").val($this.data("message"));
    $("#reply-update-form-modal").modal("show");
});


</script>
@endsection