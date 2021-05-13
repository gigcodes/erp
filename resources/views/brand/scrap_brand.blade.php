@extends('layouts.app')

@section('favicon' , 'supplierstats.png')

@section('title', 'Scrape Brand')

@section('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style type="text/css">
        .dis-none {
            display: none;
        }

        #remark-list li {
            width: 100%;
            float: left;
        }

        .fixed_header {
            table-layout: fixed;
            border-collapse: collapse;
        }

        .fixed_header tbody {
            width: 100%;
            overflow: auto;
            height: 250px;
        }

        .fixed_header thead {
            background: black;
            color: #fff;
        }
        .modal-lg{
            max-width: 1500px !important; 
        }

        .remark-width{
            white-space: nowrap;
            overflow-x: auto;
            max-width: 20px;
        }
    </style>
@endsection

@section('large_content')
        @php
            $user = auth()->user();
            $isAdmin = $user->isAdmin();
            $hod = $user->hasRole('HOD of CRM');
        @endphp

    <div class="row mb-5">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Scrapped Brand <span class="total-info-"></span></h2>
        </div>
    </div>

    @include('partials.flash_messages')
    <?php $status = request()->get('status', ''); ?>
    <?php $excelOnly = request()->get('excelOnly', ''); ?>
    <form class="" action="/scrap/scrap-brand">
        <div class="row">
            <div class="form-group mb-3 col-md-2">
                <input name="term" type="text" class="form-control" id="brand-search" value="{{ request()->get('term','') }}" placeholder="Enter Brand name">
            </div>
            <div class="form-group mb-3 col-md-2">
                <?php echo Form::select("scraper_made_by", ['' => '-- Select Made By --'] + \App\User::all()->pluck("name", "id")->toArray(), request("scraper_made_by"), ["class" => "form-control select2"]) ?>
            </div>
            <div class="form-group mb-3 col-md-2">
                <?php echo Form::select("scraper_type", ['' => '-- Select Type --'] + \App\Helpers\DevelopmentHelper::scrapTypes(), request("scraper_type"), ["class" => "form-control select2"]) ?>
            </div>
            
            <div class="form-group mb-3 col-md-2">
                <select name="scrapers_status" class="form-control form-group">
                    @foreach(\App\Scraper::STATUS as $k => $v)
                        <option <?php echo request()->get('scrapers_status','') == $k ? 'selected=selected' : '' ?> value="<?php echo $k; ?>"><?php echo $v; ?></option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-3 col-md-2">
                <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
            </div>
        </div>
    </form>
    

   <?php $totalCountedUrl = 0; ?>
    <div class="row no-gutters mt-3">
        <div class="col-md-12" id="plannerColumn">
            <div class="">
                <table class="table table-bordered table-striped sort-priority-scrapper">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Brand</th>
                        <th>Brand Qty</th>
                        <th>Functions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $arMatchedScrapers = []; $i=0; @endphp
                    @foreach ($activeSuppliers as $supplier)
                    <?php $totalCountedUrl += !empty($data) ? $data->total : 0; ?>
                            <td width="2%">
                            {{$supplier->id}}
                            </td>
                            <td width="14%">
                            {{$supplier->name}}
                            </td>
                            <td width="5%">
                            {{$supplier->products->count()}}
                            </td>
                            <td width="14%">
                                <button style="padding: 3px" data-id="{{ $supplier->id }}" type="button" class="btn btn-image d-inline get-tasks-remote" title="Task list">
                                     <i class="fa fa-tasks"></i>
                                </button> 
                            </td>
                            </tr>
                               
                            </tr>
                            
                    @endforeach
                </table>
                @include('partials.modals.remarks',['type' => 'scrap'])
                @include('partials.modals.latest-remarks',[])
            </div>
        </div>
    </div>

    <div id="addRemarkModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Note</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <form action="{{ route('scrap/add/note') }}" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label>Scraper Name</label>
                            <select name="scraper_name" class="form-control select2" required>
                                @forelse ($allScrapper as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Note</label>
                            <textarea rows="2" name="remark" class="form-control" placeholder="Note" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Screenshot</label>
                            <input type="file" class="form-control" name="image">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </div>
                </form>
            </div>
        </div>
      </div>



      <div id="remarkHistory" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Remark History</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                    <div class="modal-body" id="remark-history-content">
                      
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
      </div>


    
    
      <div id="show-content-model-table" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                       
                    </div>
                </div>
            </div>
      </div>
      <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                    <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                    <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                    <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
               50% 50% no-repeat;display:none;">
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="/js/bootstrap-datepicker.min.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script type="text/javascript">

        $(".total-info").html("({{$totalCountedUrl}})");

         $(document).on("change", ".quickComments", function (e) {
            var message = $(this).val();
            var select = $(this);

            if ($.isNumeric(message) == false) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "/scrap/statistics/reply/add",
                    dataType: "json",
                    method: "POST",
                    data: {reply: message}
                }).done(function (data) {
                    var vendors_id =$(select).find("option[value='']").data("vendorid");
                    var message_re = data.data.reply;
                    $("textarea#messageid_"+vendors_id).val(message_re);

                    console.log(data)
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            }
            
            var vendors_id =$(select).find("option[value='']").data("vendorid");
            var message_re = $(this).find("option:selected").html();

            $("textarea#messageid_"+vendors_id).val($.trim(message_re));

        });

        $(document).on("click", ".delete_quick_comment-scrapp", function (e) {
            var deleteAuto = $(this).closest(".d-flex").find(".quickComments").find("option:selected").val();
            if (typeof deleteAuto != "undefined") {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: BASE_URL+"/scrap/statistics/reply/delete",
                    dataType: "json",
                    method: "POST",
                    data: {id: deleteAuto}
                }).done(function (data) {
                    if (data.code == 200) {
                        
                        $(".quickComment").each(function(){
                        var selecto=  $(this)
                            $(this).children("option").not(':first').each(function(){
                            $(this).remove();


                            });
                            $.each(data.data, function (k, v) {
                                $(selecto).append("<option  value='" + k + "'>" + v + "</option>");
                            });
                            $(selecto).select2({tags: true});
                        });


                    }

                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    alert('No response from server');
                });
            }
        });


        $(".scrapers_status").select2();


        $(document).on("keyup",".table-full-search",function() {
            var input, filter, table, tr, td, i, txtValue;
              input = document.getElementById("table-full-search");
              filter = input.value.toUpperCase();
              table = document.getElementById("latest-remark-records");
              tr = table.getElementsByTagName("tr");

              // Loop through all table rows, and hide those who don't match the search query
              for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                  txtValue = td.textContent || td.innerText;
                  if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                  } else {
                    tr[i].style.display = "none";
                  }
                }
              }
        });

        $(document).on("click",".get-latest-remark",function(e) {
            $.ajax({
                type: 'GET',
                url: '{{ route('scrap.latest-remark') }}',
                dataType:"json"
            }).done(response => {
                var html = '';
                var no = 1;
                if(response.code == 200) {
                    $.each(response.data, function (index, value) {
                        html += '<tr><td>' + value.scraper_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td><td>' + value.user_name + '</td><td>' + value.remark + '</td></tr>';
                        no++;
                    });
                    $("#latestRemark").find('.show-list-records').html(html);
                    $("#latestRemark").modal("show");
                }else{
                    toastr['error']('Oops, something went wrong', 'error');
                }
            });
        });

        $(document).on('click', '.filter-auto-remark', function (e) {
            var name = $('#add-remark input[name="id"]').val();
            var auto = $(this).is(":checked");
            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('scrap.getremark') }}',
                data: {
                    name: name,
                    auto : auto
                },
            }).done(response => {
                var html = '';
                var no = 1;
                $.each(response, function (index, value) {
                    html += '<tr><td>' + value.remark + '</td><td>' + value.user_name + '</td><td>' + moment(value.created_at).format('DD-M H:mm') + '</td></tr>';
                    no++;
                });
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });

        $('#scrapAddRemarkbutton').on('click', function () {
            var id = $('#add-remark input[name="id"]').val();
            var remark = $('#add-remark').find('textarea[name="remark"]').val();

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('scrap.addRemark') }}',
                data: {
                    id: id,
                    remark: remark,
                    need_to_send: ($(".need_to_send").is(":checked")) ? 1 : 0,
                    inlcude_made_by: ($(".inlcude_made_by").is(":checked")) ? 1 : 0
                },
            }).done(response => {
                $('#add-remark').find('textarea[name="remark"]').val('');

                var no = $("#remark-list").find("tr").length + 1;
                html = '<tr><td>' + remark + '</td><td>You</td><td>' + moment().format('DD-M H:mm') + '</td></tr>';
                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function (response) {
                alert('Could not fetch remarks');
            });

        });

        $(document).on("click", ".btn-set-priorities", function () {

        });

        $(document).on("change", ".scraper_type", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "scraper_type",
                    field_value: tr.find(".scraper_type").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".scraper_made_by", function () {
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $.ajax({
                type: 'GET',
                url: '/scrap/statistics/update-field',
                data: {
                    search: id,
                    field: "scraper_made_by",
                    field_value: tr.find(".scraper_made_by").val()
                },
            }).done(function (response) {
                toastr['success']('Data updated Successfully', 'success');
            }).fail(function (response) {

            });
        });

        $(document).on("change", ".scrapers_status", function (e) {
            e.preventDefault();
            var tr = $(this).closest("tr");
            var id = tr.data("eleid");
            $("#remark-confirmation-box").modal("show").on("click",".btn-confirm-remark",function(e) {
                e.preventDefault();
                 var remark =  $("#confirmation-remark-note").val();
                 if($.trim(remark) == "") {
                    alert("Please Enter remark");
                    return false;
                 }
                 $.ajax({
                    type: 'GET',
                    url: '/scrap/statistics/update-field',
                    data: {
                        search: id,
                        field: "status",
                        field_value: tr.find(".scrapers_status").val(),
                        remark : remark    
                    },
                }).done(function (response) {
                    toastr['success']('Data updated Successfully', 'success');
                    $("#remark-confirmation-box").modal("hide");
                }).fail(function (response) {
                });
            });

            return false;
        });

        $(".select2").select2();

        $(document).on("click",".show-scraper-history",function (e){
            e.preventDefault();
            var date = $(this).data("date");
            $.ajax({
                url: '/scrap/server-status-history',
                type: 'GET',
                data: {date: date},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Server status history");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        

        $(document).on("click",".get-tasks-remote",function (e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: '/scrap/task-list',
                type: 'GET',
                data: {id: id},
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Task List");
                model.find(".modal-body").html(response);
                model.modal("show");
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        $(document).on("click",".btn-create-task",function (e){
            e.preventDefault();
            var $this = $(this).closest("form");
            $.ajax({
                url: $this.attr("action"),
                type: $this.attr("method"),
                data: $this.serialize() + "&type=scrap",
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                $("#loading-image").hide();
                var model  = $("#show-content-model-table");
                model.find(".modal-title").html("Task List");
                model.find(".modal-body").html(response);
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });


        $(document).on("click","#show-content-model-table li",function (e){
            e.preventDefault();
            var a = $(this).find("a");
            if(typeof a != "undefined") {
                $.ajax({
                    url: a.attr("href"),
                    type: 'GET',
                    beforeSend: function () {
                        $("#loading-image").show();
                    }
                }).done(function(response) {
                     $("#loading-image").hide();
                    var model  = $("#show-content-model-table");
                    model.find(".modal-body").html(response);
                }).fail(function() {
                    $("#loading-image").hide();
                    alert('Please check laravel log for more information')
                });
            }
        });

        $(document).on("click",".flag-scraper",function (e){
            e.preventDefault();
            var flag = $(this).data("flag");
            var id = $(this).data("id");
            var $this =  $(this);
            $.ajax({
                url: "/scrap/statistics/update-field",
                type: 'GET',
                data: {
                    search: id,
                    field: "flag",
                    field_value: flag
                },
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function(response) {
                 $("#loading-image").hide();
                 if(response.data.flag == 1) {
                    $this.closest(".flag-scraper-div").append('<button type="button" class="btn btn-image flag-scraper" data-flag="0" data-id="'+response.data.supplier_id+'"><img src="/images/flagged.png" /></button>');
                 }else{
                    $this.closest(".flag-scraper-div").append('<button type="button" class="btn btn-image flag-scraper" data-flag="1" data-id="'+response.data.supplier_id+'"><img src="/images/unflagged.png" /></button>');
                 }
                 $this.remove();
            }).fail(function() {
                $("#loading-image").hide();
                alert('Please check laravel log for more information')
            });
        });

        $(document).on('click', '.send-message1', function () {
            var thiss = $(this);
            var data = new FormData();
            var task = $(this).data('task-id');
            var message = $("#messageid_"+task).val();
            data.append("issue_id", task);
            data.append("message", message);
            data.append("status", 1);
            data.append("sendTo", $(".send-message-number-"+task).val());

            if (message.length > 0) {
                if (!$(this).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/issue',
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                            $("#loading-image").show();
                        }
                    }).done(function (response) {
                        
                        $("#message-chat-txt-"+task).html(response.message.message);
                        $("#messageid_"+task).val('');
                        $("#loading-image").hide();
                        $(this).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(this).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                        $("#loading-image").hide();
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });

    </script>
@endsection
