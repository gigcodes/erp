@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Color References</h2>
        </div>
        <div class="col-md-12">
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