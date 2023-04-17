@extends('layouts.app')

@section('title', 'Blog Listing')

@section('styles')
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" 
     href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('large_content')

    <div class="row">
        <div class="col-md-12">
            <div class=""><h3 class="text-center">Blog Listing<h3></div>
            <hr>
            <div class="table-responsive">
            <div><a class="btn btn-bg btn-primary pull-right" href="{{route('blog.create')}}">Add Blog</a> </div>

                {{--  <div class="form-group col-md-3 pd-3">
                  <input style="width:100%;" name="term" type="text" class="form-control"
                         value="{{ isset($term) ? $term : '' }}"
                         placeholder="Search">
                </div>  --}}
                 <div class="form-group col-md-2 pd-3 status-select-cls select-multiple-checkbox">
                  <select class="form-control" name="user_id" id="userId">
                    <option value="">Select User</option>
                      @foreach ($users as  $value)
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                      @endforeach
                  </select>
                </div>

                <div class="form-group col-md-1 pd-3">
                  <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" /></button>
                  </div>
                
                {{--  <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label><strong>Status :</strong></label>
                        <select id='status' class="form-control" style="width: 200px">
                            <option value="">--Select Status--</option>
                            <option value="1">Active</option>
                            <option value="0">Deactive</option>
                        </select>
                    </div>
                </div>
            </div>  --}}
                <table class="table-striped table-bordered table out-of-stock-products-table"
                    id="blog_listing">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>userName</th>
                            <th>Idea</th>
                            <th>Keyword</th>
                            <th>Publish Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script type="text/javascript">
    @if(Session::has('message'))
   toastr.options =
  {
  	"closeButton" : true,
  	"progressBar" : true
  }
  		toastr.success("{{ session('message') }}");
  @endif

 @if(Session::has('error'))
  toastr.options =
  {
  	"closeButton" : true,
  	"progressBar" : true
  }
  		toastr.error("{{ session('error') }}");
  @endif

        $(function() {
           
        var table = $('#blog_listing').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('blog.index') }}",
            data: function (d) {
                d.user_id = $('#userId').val()
               
            }
        },
        
        columns: [
            {data: 'id', name: 'id'},
            {data: 'userName', name: 'userName',searchable: true},
            {data: 'idea', name: 'idea', orderable: true, searchable: true},
            {data: 'keyword', name: 'keyword'},
            {data: 'publish_blog_date', name: 'publish_blog_date'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

        $('#userId').change(function(){
            table.draw();
        });
        
        });

        $(document).on("click",".delete-blog",function(e){
          e.preventDefault();
          var id = $(this).data('blog-id');
          
          var x = window.confirm("Are you sure, you want to delete ?");
          if(!x) {
            return;
          }
          
          $.ajax({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "delete/"+id,
              type: "DELETE",
              data: {id : id}
            }).done(function(response) {
                
              toastr['success'](response.message);
           $('#blog_listing').DataTable().ajax.reload();
            }).fail(function(errObj) {
            });
        });
    </script>
@endsection
