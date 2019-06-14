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
                                <textarea name="category[{{ $category->id }}]" cols="30" rows="2" class="form-control">{{ $category->references }}</textarea>
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