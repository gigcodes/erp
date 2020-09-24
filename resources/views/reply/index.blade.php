@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Quick Replies List</h2>
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#categoryModal">Create Category</button>
                <a class="btn btn-secondary" href="{{ route('reply.create') }}">+</a>
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
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Model</th>
            <th>Category</th>
            <th width="200px">Action</th>
        </tr>
        @foreach ($replies as $key => $reply)
            <tr>
                <td id="reply_id">{{ $reply->id }}</td>
                <td id="reply_text">{{ $reply->reply }}</td>
                <td id="reply_model">{{ $reply->model }}</td>
                <td id="reply_category_name">{{ $reply->category->name }}</td>
                <td>
                    <a class="btn btn-image" href="{{ route('reply.edit',$reply->id) }}"><img src="/images/edit.png" /></a>
                    <a class="btn intent-edit" data-toggle="modal" data-target="#auto-reply-popup">
                      <span>Add Popup</span>
                    </a>
                    {!! Form::open(['method' => 'DELETE','route' => ['reply.destroy',$reply->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
    </table>
    </div>

    {!! $replies->links() !!}


@include('partials.modals.auto-reply')

<script type="text/javascript">
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

</script>
@endsection