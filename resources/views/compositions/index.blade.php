@extends('layouts.app')
@section('title')
    Compositions 
@endsection
@section('content')
<style type="text/css">
    .form-inline label {
        display: inline-block;
    }
    .form-control {
        height: 25px !important;
    }
    .small-field { 
        margin-bottom: 0px;
     }
     .small-field-btn {
        padding: 0px 13px;
     }   
</style>
<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">Compositions ({{$compositions->total()}})</h2>
    </div>
    <div class="col-md-8 mt-5">
        {!! Form::open(["class" => "form-inline" , "route" => 'compositions.store',"method" => "POST"]) !!}    
          <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" value="{{ old('name') ? old('name') : request('name') }}"/>
          </div>
          <div class="form-group ml-2">
            <label for="replace_with">Erp Name:</label>
            <input type="text" name="replace_with" class="form-control" placeholder="Enter Erp Name" value="{{ old('replace_with') ? old('replace_with') : request('replace_with') }}" id="replace_with">
          </div>
          <button type="submit" class="btn btn-default ml-2 small-field-btn">Submit</button>
        </form>
    </div>
    <div class="col-md-4 mt-5">
        {!! Form::open(["class" => "form-inline" , "route" => 'compositions.index',"method" => "GET"]) !!}    
          <div class="form-group">
            <input type="text" name="keyword" class="form-control" id="name" placeholder="Enter keyword" value="{{ old('keyword') ? old('keyword') : request('keyword') }}"/>
          </div>
          <div class="form-group ml-2">
            <input type="checkbox" name="with_ref" class="form-control" id="with_ref" @if(request('with_ref') == 1) checked="checked" @endif value="1"/> With Ref
          </div>
          <button type="submit" class="btn btn-default ml-2 small-field-btn"><i class="fa fa-search"></i></button>
        </form>
    </div>
    <div class="col-md-12 mt-5">
        <table class="table table-bordered">
            <tr>
                <th width="10%">SN</th>
                <th width="30%">Composition</th>
                <th width="40%">Erp Composition</th>
                <th width="20%">Action</th>
            </tr>
            @foreach($compositions as $key=>$composition)
                <tr>
                    <td>{{ $composition->id }}</td>
                    <td>{{ $composition->name }}</td>
                    <td>
                        <div class="form-group small-field">
                            <?php echo Form::select(
                                'replace_with', 
                                $listcompostions , 
                                $composition->replace_with, 
                                ["class" => "form-control change-list-compostion select2", 'data-id' => $composition->id, 'style' => 'width:400px']
                            ); ?>
                        </div>
                    </td>
                    <td>
                        <form action="{{ route('compositions.destroy', $composition->id) }}" method="POST">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button class="btn btn-secondary small-field-btn" onclick="return confirm('Are you sure you want to delete ?')">
                                <i class="fa fa-trash" type="submit"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
        {{ $compositions->appends(request()->except('page'))->links() }}
    </div>
</div>
@section('scripts')
    <script type="text/javascript">
            $(".select2").select2({"tags" : true});
            $(document).on("change",".change-list-compostion",function() {
                var $this = $(this);
                $.ajax({
                    type: 'PUT',
                    url: '/compositions/'+$this.data("id"),
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                    data: {
                        _token: "{{ csrf_token() }}",
                        replace_with: $this.val(),
                    },
                    dataType: "json"
                }).done(function (response) {
                    $("#loading-image").hide();
                    if (response.code == 200) {
                        $this.remove();
                        toastr['success'](response.message, 'success');
                    }
                }).fail(function (response) {
                    $("#loading-image").hide();
                    console.log("Sorry, something went wrong");
                });
            });
    </script>
@endsection
@endsection