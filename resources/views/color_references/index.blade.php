@extends('layouts.app')
@section('title')
    Color References
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Color References</h2>
        </div>
        <div class="col-md-12">
            <div class="col-md-4 mt-5 float-right">
                {!! Form::open(["class" => "form-inline" , "route" => 'color-reference.index',"method" => "GET"]) !!}    
                  <div class="form-group">
                    <input type="text" name="keyword" class="form-control" id="keyword" placeholder="Enter keyword" value="{{ old('keyword') ? old('keyword') : request('keyword') }}"/>
                  </div>
                  <div class="form-group ml-2">
                    <input type="checkbox" name="no_ref" class="form-control" id="no_ref" @if(request('no_ref') == 1) checked="checked" @endif value="1"/> No Ref
                  </div>
                  <button type="submit" class="btn btn-default ml-2 small-field-btn"><i class="fa fa-search"></i></button>
                </form>
            </div>
        </div>    
        <div class="col-md-12 mt-5">
            <form action="{{ action('ColorReferenceController@store') }}" method="post">
                @csrf
                <table class="table table-striped">
                    <tr>
                        <th>SN</th>
                        <th>Color</th>
                        <th>Erp Colo Name</th>
                        <th>Color Name</th>
                    </tr>
                    @foreach($colors as $key=>$color)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td style="background-color: #{{$color->color_code}}">{{ $color->color_code }}</td>
                            <td>
                                <select class="form-control" name="colors[{{$color->id}}]" id="color_{{$color->id}}">
                                    <option value="">Select Color</option>
                                    @foreach((new \App\Colors())->all() as $col)
                                        <option {{ $col==$color->erp_name ? 'selected' : '' }} value="{{ $col }}">{{ $col }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{{ $color->color_name }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="3">
                            <button class="btn btn-secondary">SAVE</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
@endsection