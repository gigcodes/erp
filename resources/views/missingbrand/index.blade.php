@extends('layouts.app')



@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('large_content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{ $title }} ({{ $missingBrands->count() }})</h2>
            <div class="pull-left">
<!--                 <form class="form-inline" action="{{ route('missing-brands.index') }}" method="GET">
                    <div class="form-group">
                        <input name="term" type="text" class="form-control"
                               value="{{ isset($term) ? $term : '' }}"
                               placeholder="Search">
                    </div>
                    

                    <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
                </form> -->
            </div>

        </div>
    </div>

    @include('partials.flash_messages')

   <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Brand</th>
                <th>Supplier</th>
                <th>Created At</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($missingBrands as $missingBrand)
                <tr>
                    <td>{{ $missingBrand->id }}</td>
                    <td>{{ $missingBrand->name }}</td>
                    <td>{{ $missingBrand->supplier }}</td>
                    <td>{{ $missingBrand->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
            {!! $missingBrands->render() !!}
        </table>
    </div>





@endsection


