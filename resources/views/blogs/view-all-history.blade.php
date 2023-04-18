@extends('layouts.app')

@section('title', 'Blog History')

@section('styles')
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('large_content')

    <div class="row">
        <div class="col-md-12">
            <div class=""><h3 class="text-center">All Blog History<h3></div>
            <hr>
            <div class="table-responsive">
            <div>
            <a class="btn btn-bg btn-primary pull-right" href="{{route('blog.index')}}">Blog List <i class="fa fa-list" aria-hidden="true"></i></a>
            </div>
           
            <div class="table-responsive">
            {{--  <div><a class="btn btn-bg btn-primary pull-right" href="{{route('blog.create')}}">Add Blog</a> </div>  --}}

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
                   <button  class="btn btn-image ml-3 refreshTable"><i class="fa fa-history" aria-hidden="true"></i></button>
                  </div>
                <table class="table-striped table-bordered table out-of-stock-products-table"
                    id="blog-history-list">
                    <thead>
                        <tr>
                            <th>Blog Id</th>
                            <th>UserName</th>
                            <th>Plaglarism</th>
                            <th>Internal Link</th>
                            <th>External Link Date</th>
                            <th>No Index</th>
                            <th>No Follow</th>
                            <th>Creeated At</th>
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
  


         $(function() {
            
            var table = $('#blog-history-list').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            ajax: {
                url: "{{ route('view-blog-all.history') }}",
                data: function (d) {
                    d.user_id = $('#userId').val()
                
                }
            },
            
                columns: [
                    {data: 'blog_id', name: 'blog_id'},
                    {data: 'userName', name: 'userName',searchable: true},
                    {data: 'plaglarism', name: 'plaglarism', orderable: true, searchable: true},
                    {data: 'internal_link', name: 'internal_link', orderable: false},
                    {data: 'external_link', name: 'external_link', orderable: false},
                    {data: 'no_index', name: 'no_index', orderable: false},
                    {data: 'no_follow', name: 'no_follow', orderable: false},
                    {data: 'created_at', name: 'created_at', orderable: false},
                ]
            });

        $('#userId').change(function(){
            table.draw();
        });
        
        $('.refreshTable').click(function(){
        
            $('#userId').val('');
           $('#blog-history-list').DataTable().ajax.reload();
        });
        
    });


            $(document).ready(function() {
                $('#blog-datetime').datetimepicker({
            format: 'YYYY-MM-DD'
            });

            });
    </script>
@endsection
