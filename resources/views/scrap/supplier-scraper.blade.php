@extends('layouts.app')

@section('title', 'Generic Supplier Scraper')

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
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Generic Supplier Scraper (<span id="count">{{ $scrapers->total() }}</span>)</h2>
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
                <th>Id</th>
                <th style="width: 5% !important;">Scraper name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Run Gap</th>
                <th>Time Out</th>
                <th>Starting URL</th>
                <th>Designer URL Selector</th>
                <th>Product URL Selector</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody id="content_data">
            @include('scrap.partials.supplier-scraper-data')
            </tbody>

            {{ $scrapers->render() }}

        </table>
        {{ $scrapers->render() }}
    </div>

@include('scrap.partials.edit-supplier-scraper-modal')
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
        $('#sku,#category').on('blur', function () {
            $.ajax({
                url: '/logging/sku-logs',
                dataType: "json",
                data: {
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
                $("#count").text(data.totalFailed);
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
                 $("#nulti").show();
                console.log(data);
                $("#count").text(data.totalFailed);
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
                $("#nulti").show();
                console.log(data);
                $("#count").text(data.totalFailed);
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

    function addTask(supplier , category , sku , brand) {
        $('#taskModal').modal('show');
        $('#task_subject').val(supplier +' '+category+' '+sku);
        $('#references').val(supplier+''+category+''+brand);
    }

    $(".checkbox").change(function() {
    if(this.checked) {
        validate = 1;
        $.ajax({
                url: '/logging/sku-logs',
                dataType: "json",
                data: {
                    sku: $('#sku').val(),
                    brand: $('#brand').val(),
                    category: $('#category').val(),
                    supplier : $('#supplier').val(),
                    validate : validate,
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                $("#nulti").show();
                console.log(data);
                $("#count").text(data.totalFailed);
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
        
    }else{
        validate = 2;
        $.ajax({
                url: '/logging/sku-logs',
                dataType: "json",
                data: {
                    sku: $('#sku').val(),
                    brand: $('#brand').val(),
                    category: $('#category').val(),
                    supplier : $('#supplier').val(),
                    validate : validate,
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                $("#nulti").show();
                console.log(data);
                $("#count").text(data.totalFailed);
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
    });

    function sendMulti(){
        brand =  $('#brand').val();
        category = $('#category').val();
        supplier = $('#supplier').val();
        if(brand == ''){
            alert('Please Select Brand');
        }
        if(category == ''){
            alert('Please Select Category');
        }
        if(supplier == ''){
            alert('Please Select Supplier');
        }
        if(brand != '' && category != '' && supplier != ''){
            $('#taskModal').modal('show');
            $('#task_subject').val(supplier +' '+category+' multi');
            $('#references').val(supplier+''+category+''+brand);
        }
        
    }


    function editSupplier(scraper){
        console.log(JSON.stringify(scraper));
        $("#scraper_id").val(scraper.id);
        
        $("#run_gap").val(scraper.run_gap);
        $("#time_out").val(scraper.time_out);
        $("#starting_url").val(scraper.starting_urls);
        $("#designer_url").val(scraper.designer_url_selector);
        $("#product_url_selector").val(scraper.product_url_selector);
        $("#scrapEditModal").modal('show');
    }

    function updateSupplier(){
        id = $("#scraper_id").val();
        $.ajax({
                url: "{{ route('generic.save.scraper') }}",
                dataType: "json",
                type : "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id : id,
                    starting_url : $("#starting_url").val(),
                    designer_url : $("#designer_url").val(),
                    product_url_selector : $("#product_url_selector").val(),
                    run_gap : $("#run_gap").val(),
                    time_out: $("#time_out").val()
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },
            }).done(function (data) {
                $("#loading-image").hide();
                window.location = "/scrap/generic-scraper/mapping/"+id;
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                $("#loading-image").hide();
                alert('No response from server');
            });
           
    }

    </script>
@endsection