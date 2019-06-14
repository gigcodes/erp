@extends('layouts.app')

@section('large_content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Categories Map (References)</h2>
        </div>
        <div class="col-md-12">
            <form method="post" action="{{ action('CategoryController@saveReferences') }}">
                @csrf
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Category Name</th>
                        <th>References</th>
                    </tr>

                    @foreach($categories as $category)
                        <tr>
                            <td>
                                {{ $category->title }}
                            </td>
                            <td>
                                <select name="category[{{ $category->id }}][]" cols="30" rows="2" class="form-control" multiple>
                                    @php $options = explode(',', $category->references) @endphp
                                    @if(count($options)>0)
                                        @foreach($options as $option)
                                            @if(strlen($option) > 1)
                                                <option selected value="{{$option}}">{{$option}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" class="text-right">
                            <button class="btn btn-default">Save</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>

    <script>
        $("select").select2({
            tags: true
        });
    </script>
@endsection