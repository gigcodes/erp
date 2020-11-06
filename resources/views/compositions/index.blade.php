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
        <h2 class="page-heading">Compositions</h2>
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
            <input type="checkbox" name="no_ref" class="form-control" id="no_ref" @if(request('no_ref') == 1) checked="checked" @endif value="1"/> No Ref
          </div>
          <button type="submit" class="btn btn-default ml-2 small-field-btn"><i class="fa fa-search"></i></button>
        </form>
    </div>
    <div class="col-md-12 mt-5">
        <table class="table table-bordered">
            <tr>
                <th>SN</th>
                <th>Composition</th>
                <th>Erp Composition</th>
                <th>Action</th>
            </tr>
            @foreach($compositions as $key=>$composition)
                <tr>
                    <td>{{ $composition->id }}</td>
                    <td>{{ $composition->name }}</td>
                    <td>
                        <form action="{{ route('compositions.update', $composition->id) }}" method="POST">
                            {{ method_field('PUT') }}
                            {{ csrf_field() }}
                            <div class="form-group small-field">
                                <input class="form-control col-md-9" data-id="{{$composition->id}}" type="text" name="replace_with" value="{{ $composition->replace_with }}">
                                <button class="btn btn-secondary ml-2 small-field-btn">
                                    <i class="fa fa-save" type="submit"></i>
                                </button>
                            </div>
                        </form>
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
         var cbt = $(".add-compositions");
             cbt.on('click',function() {

             });
    </script>
@endsection
@endsection