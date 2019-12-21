@extends('layouts.app')

@section('title', 'Log List Magento')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }

        input {
            width: 100px;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Log List SKU</h2>
            <div class="pull-right">
                <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png"/></button>
            </div>

        </div>
    </div>

    @include('partials.flash_messages')

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th style="width: 2% !important;">ID</th>
                <th style="width: 5% !important;">SKU</th>
                <th style="width: 5% !important;">SKU Format</th>
                <th style="width: 5% !important;">SKU Format Ex</th>
                <th style="width: 20% !important;">Brand</th>
                <th style="width: 20% !important;">Category</th>
                <th style="width: 20% !important;">Supplier</th>
                <th>Validation</th>
                <th>Date/Time</th>
            </tr>
            <tr>
                <th style="width: 2% !important;"><input type="text" id="product_id"></th>
                <th style="width: 5% !important;"><input type="text" id="sku"></th>
                <th style="width: 5% !important;">&nbsp;</th>
                <th style="width: 5% !important;">&nbsp;</th>
                <th style="width: 20% !important;">@php $brands = \App\Brand::getAll();
                        @endphp
                        <select data-placeholder="Select brands" class="form-control select-multiple2" id="brand" multiple>
                            <optgroup label="Brands">
                                @foreach ($brands as $id => $name)
                                    <option value="{{ $name }}" {{ isset($brand) && $brand == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </optgroup>
                        </select></th>
                <th style="width: 20% !important;">{!! $category_selection !!}</th>
                <th style="width: 20% !important;">@php $suppliers = new \App\Supplier();
                        @endphp
                        <select data-placeholder="Select Supplier" class="form-control select-multiple2" id="supplier" multiple>
                            <optgroup label="Suppliers">
                                @foreach ($suppliers->select('id','supplier')->where('supplier_status_id',1)->get() as $id => $suppliers)
                                    <option value="{{ $suppliers->supplier }}" {{ isset($supplier) && $supplier == $suppliers->supplier ? 'selected' : '' }}>{{ $suppliers->supplier }}</option>
                                @endforeach
                            </optgroup>
                        </select></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody id="content_data">
            @include('logging.partials.listsku_data')
            </tbody>

            {{ $logScrappers->render() }}

        </table>
    </div>


@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    
    <script type="text/javascript">


    $(document).ready(function() {
       $(".select-multiple").multiselect();
       $(".select-multiple2").select2();
         });
    
    $(document).ready(function () {
        $('#product_id,#sku,#category').on('blur', function () {
            $.ajax({
                url: '/logging/sku-logs',
                dataType: "json",
                data: {
                    product_id: $('#product_id').val(),
                    sku: $('#sku').val(),
                    brand: $('#brand').val(),
                    category: $('#category').val()
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                console.log(data);
                $("#log-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
                alert('No response from server');
            });
        });

         $('#brand,#category,#supplier').on('change', function () {
            $.ajax({
                url: '/logging/sku-logs',
                dataType: "json",
                data: {
                    product_id: $('#product_id').val(),
                    sku: $('#sku').val(),
                    brand: $('#brand').val(),
                    category: $('#category').val(),
                    supplier : $('#supplier').val(),
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                console.log(data);
                $("#log-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
                alert('No response from server');
            });
        });
    


    });
    function refreshPage(){
        blank = '';
        $.ajax({
                url: '/logging/sku-logs',
                dataType: "json",
                data: {
                    blank : blank
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                console.log(data);
                $("#log-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
                alert('No response from server');
            });
    }


    </script>
@endsection