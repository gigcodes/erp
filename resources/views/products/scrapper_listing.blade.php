@extends('layouts.app')

@section('favicon' , 'attributeedit.png')
@section('title', 'Approved Product Listing - ERP Sololuxury')

@section('title', 'Product Listing')

@section('styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">

    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropme@latest/dist/cropme.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet"/>
    
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    
    <style>
        .quick-edit-color {
            transition: 1s ease-in-out;
        }
        span.multiselect-native-select {
            display: none;
            width: 100%;
        }
        .thumbnail-pic {
            position: relative;
            display: inline-block;
        }
        .thumbnail-pic:hover .thumbnail-edit {
            display: block;
        }
        .thumbnail-edit {
            padding-top: 12px;
            padding-right: 7px;
            position: absolute;
            left: 0;
            top: 0;
            display: none;
        }
        .thumbnail-edit a {
            color: #FF0000;
        }
        .thumbnail-pic {
            position: relative;
            padding-top: 10px;
            display: inline-block;
        }
        .notify-badge {
            position: absolute;
            top: 10px;
            text-align: center;
            border-radius: 30px 30px 30px 30px;
            color: white;
            padding: 5px 10px;
            font-size: 10px;
        }
        .notify-red-badge {
            background: red;
        }
        .notify-green-badge {
            background: green;
        }
        .cropme-container {
            margin-left: 35px !important;
            top: 0px !important;
            width: 300px !important;
            height: 300px !important;
            display: inline-block  !important;
            vertical-align: middle !important;
        }

        .cropme-slider {
            margin-top : 0px !important;
            transform: translate3d(550px, 155px, 0px) rotate(-90deg) !important;
            transform-origin:unset !important;
        }
        .product_filter .row > div:not(:first-child):not(:last-child) {
            padding-left: 10px;
            padding-right: 10px;
        }
        .product_filter .row > div:first-child {
            padding-right: 10px;
        }
        .product_filter .row > div:last-child {
            padding-left: 10px;
        }
        /* Select2 changes */
        .select2-container .select2-selection--single {
            height: 34px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px;
            right: 5px;
        }
        .select2-container--default .select2-selection--single, .select2-container--default .select2-selection--multiple {
            border: 1px solid #ccc;
        }
        .select2-container .select2-selection--multiple {
            min-height: 34px;
        }
        .select2-selection select2-selection--multiple {
            padding: 0 5px;
        }
        .select2-container .select2-search--inline .select2-search__field {
            padding: 0 5px;
        }
        td.action > div, td.action > button {
            margin-top: 8px;
        }
        .lmeasurement-container, .dmeasurement-container, .hmeasurement-container {
            display: block;
            margin-bottom: 10px;
        }
        .quick-name {
            display: block;
            text-overflow: ellipsis;
            overflow: hidden;
            width: 90px;
            height: 1.2em;
            white-space: nowrap;
        }
        .quick-description {
            display: block;
            text-overflow: ellipsis;
            overflow: hidden;
            width: 100%;
            max-width: 140px;
            height: 1.2em;
            white-space: nowrap;
        }
        td {
            padding:3px !important;
        }

        .quick-edit-category ,.quick-edit-composition-select, .quick-edit-color,.post-remark, .approved_by {
            height: 26px;
            padding: 2px 12px;
            font-size: 12px; 
        }
        .lmeasurement-container input {
           height: 26px;
            padding: 2px 12px;
            font-size: 12px;  
        }

        .infinite-scroll-data .badge {
            display: inline-block;
            min-width: 5px;
            padding: 0px 4px;
        }
        .quick-edit-category ,.quick-edit-composition-select, .quick-edit-color,.post-remark, .approved_by {
            height: 26px;
            padding: 2px 12px;
            font-size: 12px; 
        }
        .lmeasurement-container input {
           height: 26px;
            padding: 2px 12px;
            font-size: 12px;  
        }
        .infinite-scroll-data .badge {
            display: inline-block;
            min-width: 5px;
            padding: 0px 4px;
        }
        .toggle.btn{
            margin:0px;
        }
        
        input[type=checkbox] {
            height: 12px;
        }

        .carousel {
            margin: 10px;
        }
    </style>
@endsection

@section('large_content')
    <div class="row">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">Scrapper Product Images ({{ $products_count }}) </h2>
        </div>
    </div>
   
    @include('partials.flash_messages')

    <div class="row">
        <div class="col-md-12">
            <div class="infinite-scroll table-responsive mt-5 infinite-scroll-data">
                @include("products.scrapper_listing_image_ajax")
            </div>
            <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
        </div>
    </div>  

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cropme@latest/dist/cropme.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

<script type="text/javascript">       
    
var productIds = [
    @foreach ( $products as $product )
    {{ $product->id }},
    @endforeach
];

var page = 1;
var isLoadingProducts;
$(document).ready(function () {
    
    $(window).scroll(function() {
        if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
            loadMoreProducts();
        }
    });

    function loadMoreProducts() {
        if (isLoadingProducts)
            return;
        isLoadingProducts = true;
        if(!$('.pagination li.active + li a').attr('href'))
        return;

        var $loader = $('.infinite-scroll-products-loader');
        $.ajax({
            url: $('.pagination li.active + li a').attr('href'),
            type: 'GET',
            beforeSend: function() {
                $loader.show();
                $('ul.pagination').remove();
            }
        })
        .done(function(data) {
            if('' === data.trim())
                return;

            $loader.hide();

            $('.infinite-scroll-data').append(data);

            isLoadingProducts = false;
        })
        .fail(function(jqXHR, ajaxOptions, thrownError) {
            console.error('something went wrong');

            isLoadingProducts = false;
        });
    }
    $('.dropify').dropify();
    // $(".select-multiple").multiselect();
    $(".select-multiple").select2({
        minimumResultsForSearch: -1,
        width: '100%'
    });
    $("body").tooltip({selector: '[data-toggle=tooltip]'});
});
</script>
@endsection
