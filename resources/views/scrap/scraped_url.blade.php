@extends('layouts.app')

@section('title', 'Scraped URL Info')

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
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Scraped URLs</h2>
             <div class="pull-right">
                <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
            </div>

        </div>
    </div>

    @include('partials.flash_messages')

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th width="30%">Website</th>
                <th width="10%">Url</th>
                <th width="10%">Sku</th>
                <th width="40%">Brand</th>
                <th width="10%">Title</th>
                <th width="10%">Currency</th>
                <th width="10%">Price</th>
                <th width="10%"><button class="btn btn-link" onclick="sortByDateCreated()" id="header-created" value="0">Created_at</button></th>
                <th width="10%"><button class="btn btn-link" onclick="sortByDateUpdated()" id="header-updated" value="0">Updated_at</button></th>

            </tr>
            <tr>
                <th width="30%">
                    @php 
                    $websites = \App\Loggers\LogScraper::select('id','website')->groupBy('website')->get();
                    @endphp
                    <select class="form-control select-multiple2" data-placeholder="Select websites.." multiple id="website">
                                <optgroup label="Websites">
                                  @foreach ($websites as $website)
                                    <option value="{{ $website->website }}">{{ $website->website }}</option>
                                  @endforeach
                                </optgroup>
                              </select>
                </th>
                <th width="10%"><input type="text" class="search form-control" id="url"></th>
                <th width="10%"><input type="text" class="search form-control" id="sku"></th>
                <th width="40%"> @php $brands = \App\Brand::getAll(); @endphp
                              <select class="form-control select-multiple2" name="brand[]" id="brand" data-placeholder="Select brand.." multiple >
                                <optgroup label="Brands">
                                  @foreach ($brands as $key => $name)
                                    <option value="{{ $name }}" {{ isset($brand) && $brand == $key ? 'selected' : '' }}>{{ $name }}</option>
                                  @endforeach
                                </optgroup>
                              </select></th>
                <th width="15%"><input type="text" class="search form-control" id="title"></th>
                <th width="10%"><input type="text" class="search form-control" id="currency"></th>
                <th width="10%"><input type="text" class="search form-control" id="price"></th>
                <th width="10%"> <div class='input-group' id='created-date'>
                        <input type='text' class="form-control " name="phone_date" value="" placeholder="Date" id="created_date" />
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div></th>
                <th> <div class='input-group' id='updated-date'>
                        <input type='text' class="form-control " name="phone_date" value="" placeholder="Date" id="updated_date" />
                            <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div></th>
            </tr>
            </thead>

            <tbody id="content_data">
             @include('scrap.partials.scraped_url_data')
            </tbody>

            {!! $logs->render() !!}

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
        $('#brand').on('change', function (e) {
            website = $('#website').val();
            brand = $('#brand').val();
            url = $('#url').val();
            sku = $('#sku').val();
            title = $('#title').val();
            currency = $('#currency').val();
            price = $('#price').val();
            src = "/scrap/scraped-urls";
           $.ajax({
                url: src,
                dataType: "json",
                data: {
                    website : website,
                    url : url,
                    sku : sku,
                    title : title,
                    currency : currency,
                    price : price,
                    brand: brand,
                },
                beforeSend: function() {
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
                alert('No response from server');
            });
        });

        $('#website').on('change', function (e) {
            website = $('#website').val();
            brand = $('#brand').val();
            url = $('#url').val();
            sku = $('#sku').val();
            title = $('#title').val();
            currency = $('#currency').val();
            price = $('#price').val();
           src = "/scrap/scraped-urls"; 
           $.ajax({
                url: src,
                dataType: "json",
                data: {
                    website : website,
                    url : url,
                    sku : sku,
                    title : title,
                    currency : currency,
                    price : price,
                    brand: brand,
                },
                beforeSend: function() {
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
                alert('No response from server');
            });
        });
    });

    
    
        function myFunction(input) {
        /* Get the text field */
        var copyText = document.getElementById(input);

        /* Select the text field */
        copyText.select();
        copyText.setSelectionRange(0, 99999); /*For mobile devices*/

        /* Copy the text inside the text field */
        document.execCommand("copy");

        /* Alert the copied text */
        alert("Copied the text: " + copyText.value);
        }



            $(function() {

                var start = moment().subtract(0, 'days');
                var end = moment();

                function cb(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    $('#custom').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
                }

                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                     'Today': [moment(), moment()],
                     'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                     'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                     'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                     'This Month': [moment().startOf('month'), moment().endOf('month')],
                     'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                 }
             }, cb)
                cb(start, end);
            });

    //Ajax Request For Search
    $(document).ready(function () {
          
        
        //Expand Row
         $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        //Filter by date
        count = 0;
        $('#created-date').datetimepicker(
            { format: 'YYYY/MM/DD' }).on('dp.change', 
            function (e) 
            {
            if(count > 0){    
             var formatedValue = e.date.format(e.date._f);
                created = $('#created_date').val();
                updated = $('#updated_date').val();
                website = $('#website').val();
                url = $('#url').val();
                sku = $('#sku').val();
                title = $('#title').val();
                currency = $('#currency').val();
                price = $('#price').val();
                brand = $('#brand').val();

                src = "/scrap/scraped-urls";
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        created : created,
                        updated : updated,
                        website : website,
                        url : url,
                        sku : sku,
                        title : title , 
                        currency : currency , 
                        price : price , 
                        brand : brand,

                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },

                }).done(function (data) {
                    $("#loading-image").hide();
                $("#content_data").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                    

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });  

            } 
            count++;       
            });

            
            count = 0;
        $('#updated-date').datetimepicker(
            { format: 'YYYY/MM/DD' }).on('dp.change', 
            function (e) 
            {
            if(count > 0){    
             var formatedValue = e.date.format(e.date._f);
                created = $('#created_date').val();
                updated = $('#updated_date').val();
                website = $('#website').val();
                url = $('#url').val();
                sku = $('#sku').val();
                title = $('#title').val();
                currency = $('#currency').val();
                price = $('#price').val();
                brand = $('#brand').val();

                src = "/scrap/scraped-urls";
                $.ajax({
                    url: src,
                    dataType: "json",
                    data: {
                        created : created,
                        updated : updated,
                        website : website,
                        url : url,
                        sku : sku,
                        title : title , 
                        currency : currency , 
                        price : price , 
                        brand : brand,

                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },

                }).done(function (data) {
                    $("#loading-image").hide();
                $("#content_data").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                    

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });  

            } 
            count++;       
            });


        //Search    
        src = "/scrap/scraped-urls";
        $(".search").autocomplete({
        source: function(request, response) {
            url = $('#url').val();
            sku = $('#sku').val();
            title = $('#title').val();
            currency = $('#currency').val();
            price = $('#price').val();
            
           $.ajax({
                url: src,
                dataType: "json",
                data: {
                    website : website,
                    url : url,
                    sku : sku,
                    title : title,
                    currency : currency,
                    price : price,
                    
                },
                beforeSend: function() {
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
                alert('No response from server');
            });
        },
        minLength: 1,
       
        });
         });

         function refreshPage() {
             blank = ''
             $.ajax({
                url: src,
                dataType: "json",
                data: {
                    blank : blank
                },
                beforeSend: function() {
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
                alert('No response from server');
            });
         }

 

         function sortByDateCreated() {
            orderCreated = $('#header-created').val();
            website = $('#website').val();
            url = $('#url').val();
            sku = $('#sku').val();
            title = $('#title').val();
            currency = $('#currency').val();
            price = $('#price').val();
            brand = $('#brand').val();

            src = "/scrap/scraped-urls";
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    website : website,
                    url : url,
                    sku : sku,
                    title : title , 
                    currency : currency , 
                    price : price , 
                    brand : brand,
                    orderCreated : orderCreated,
                },
                beforeSend: function () {
                    if(orderCreated == 0){
                        $('#header-created').val('1');
                    }else{
                        $('#header-created').val('0');
                    }
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
            $("#content_data").empty().html(data.tbody);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }
                

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });  

         }

         
         function sortByDateUpdated() {
            orderUpdated = $('#header-updated').val();
            website = $('#website').val();
            url = $('#url').val();
            sku = $('#sku').val();
            title = $('#title').val();
            currency = $('#currency').val();
            price = $('#price').val();
            brand = $('#brand').val();

            src = "/scrap/scraped-urls";
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    website : website,
                    url : url,
                    sku : sku,
                    title : title , 
                    currency : currency , 
                    price : price , 
                    brand : brand,
                    orderUpdated : orderUpdated,
                },
                beforeSend: function () {
                    if(orderUpdated == 0){
                        $('#header-updated').val('1');
                    }else{
                        $('#header-updated').val('0');
                    }
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
            $("#content_data").empty().html(data.tbody);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }
                

            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });  

         }
    </script>
@endsection