@extends('layouts.app')

@section('title', 'Scraper Log List')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
<div class="container-fluid">
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Scraper Logs ( {{ $scraperLogs->total() }})</h2>
             <div class="pull-right">
                <button type="button" class="btn btn-image" onclick="location.reload()"><img src="/images/resend2.png" /></button>
            </div>

        </div>
    </div>

    @include('partials.flash_messages')

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th>Id</th>
                <th>Ip address</th>
                <th>Website</th>
                <th>Url</th>
                <th>Sku</th>
                <th>Original sku</th>
                <th>Title</th>
                <th>Validation result</th>
                <th>Created at</th>
            </tr>
            <tr>
                <th><input type="text" class="search form-control filter-serach-string" data-id="id"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="ip_address"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="website"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="url"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="sku"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="original_sku"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="title"></th>
                <th><input type="text" class="search form-control filter-serach-string" data-id="validation_result"></th>
                <th></th>
            </tr>
            </thead>

            <tbody id="content_data" class="infinite-scroll">
                @include('logging.partials.scraper-logs')
            </tbody>

            {!! $scraperLogs->render() !!}

        </table>
    </div>
</div>


@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script type="text/javascript">

        var callResult = function(url,sendingPost,append) {
            $.ajax({
                url: url,
                dataType: "json",
                data: sendingPost,
                beforeSend: function() {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                
                if(append) {
                    $("#log-table tbody").append(data.tbody);
                }else{
                    $("#log-table tbody").empty().html(data.tbody);
                }

                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        };

         $(".search").autocomplete({
            source: function(request, response) {
                var fields = $(".filter-serach-string");
                    var sendingPost = {};
                    $.each(fields, function(k,v){
                        sendingPost[$(v).data("id")] = $(v).val();
                    });
                    callResult("{{ route('log-scraper.index') }}",sendingPost,false);
            },
            minLength: 1,
        });

        $(window).scroll(function() {
            if($(window).scrollTop() >= ($(document).height() - $(window).height() - 5)) {
               $(".pagination").find(".active").next().find("a").trigger("click");
            }
        });

        //initialize pagination
        $(document).on("click",".page-link",function(e) {
            e.preventDefault();
            var activePage = $(this).closest(".pagination").find(".active").text();
            var clickedPage = $(this).text();
            var append = true;
            if(clickedPage == "‹" || clickedPage < activePage) {
                $('html, body').animate({scrollTop: ($(window).scrollTop() - 500) + "px"}, 200);
                append = false;
            }
            
            callResult($(this).attr("href"),{},append);
        });


    </script>
@endsection
