@extends('layouts.app')
@section('title', 'Magento push status')

@section('large_content')
    @include('partials.flash_messages')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2 class="page-heading">Magento push status ({{ $productsCount }})</h2>
            <div class="infinite-scroll table-responsive mt-5 infinite-scroll-data">
                @include("products.magento_conditions_check.list")
            </div>
            <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
        </div>
    </div>
@endsection
