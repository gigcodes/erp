@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Link To Post To</h2>
        </div>
        <div class="col-md-12">
            <div class="container">
                
            <table class="table table-striped">
                <tr>
                    <th>S.N</th>
                    <th>Date Scrapped</th>
                    <th>Link</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Date Posted</th>
                    <th>Date To Next Post</th>
                    <th>Article Posted</th>
                   
                    
                </tr>
                @foreach($data as $key=>$item)
                    <tr>
                        <td style="width:10%">{{$key+1}}</td>
                        <td style="width:10%">{{ $item->date_scrapped }}</td>
                        <td style="width:10%">{{ str_limit($item->link, 30) }}</td>
                        <td style="width:10%">{{ $item->name }}</td>
                        <td style="width:10%"><select class="category">
                              <option>Select Category</option>
                            @foreach($category as $cat)
                            @if($item->category_id == $cat->id)
                            <option value="{{ $cat->id }}" data-list="{{ $item->id }}" selected>{{ $cat->name }}</option>
                            @endif
                            <option value="{{ $cat->id }}" data-list="{{ $item->id }}">{{ $cat->name }}</option>
                            @endforeach
                            <option value="0">Add Category</option>
                        </select></td>
                        <td style="width:10%"><div class="input-group date" data-provide="datepicker">
    <input type="text" class="form-control">
    <div class="input-group-addon">
        <span class="glyphicon glyphicon-th"></span>
    </div>
</div></td>
                        <td style="width:10%"><div class="input-group date" data-provide="datepicker">
    <input type="text" class="form-control">
    <div class="input-group-addon">
        <span class="glyphicon glyphicon-th"></span>
    </div>
</div></td>
                        <td style="width:10%"><a href="{{ $item->link}}">{{ str_limit($item->article, 30) }}</a></td>
                       
                    </tr>

                @endforeach
           
            </table>
             {{ $data->links() }}
            </div>
          <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Categroy</h4>
        </div>
        <form action="{{ route('addArticleCategory') }}" method="POST">
            @csrf
        <div class="modal-body">
          <p>Enter Category Name</p>
          <input type="text" name="name">
        </div>
       
        <div class="modal-footer">
             <button type="submit" class="btn btn-default">Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
         </form>
      </div>
      
    </div>
        </div>
    </div>
@endsection

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css">
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
@endsection

@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
   $(document).ready(function(){
    $(".category").change(function() {
        var id = $(this).find(':selected').val();
        
        if(id == 0){
            $("#myModal").modal();
        }else{
         var list_id = $(this).find(':selected').data('list');   
            $.ajax({
                url: '{{ route('updateCategoryPost') }}',
                type: 'POST',
                dataType: 'json',
                data: {'_token': '{{ csrf_token() }}','id' : id,'link_id':list_id},
            })
            .done(function(message) {
                console.log(message);
            })
            
            
        }
        
    });
   
});
</script>
<script>
   $('.datepicker').datepicker();
</script>
@endsection
