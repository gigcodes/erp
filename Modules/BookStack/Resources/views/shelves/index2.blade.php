
@extends('layouts.app')

@section('favicon' , 'shstid.png')

@section('title', 'Scrape Statistics')

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

        .status .select2 .select2-selection{
            width:80px;
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
            <h2 class="page-heading">Shelves <span class="total-info"></span></h2>
        </div>
    </div>

    @include('partials.flash_messages')
    <?php $status = request()->get('status', ''); ?>
    <?php $excelOnly = request()->get('excelOnly', ''); ?>
    <form class="" action="/scrap/statistics">
        <div class="row">
            <div class="form-group mb-3 col-md-2">
                <input name="term" type="text" class="form-control" id="product-search" value="{{ request()->get('term','') }}" placeholder="Enter sh nid">
            </div>
            <div class="form-group mb-3 col-md-2">
                <?php echo Form::select("scraper_made_by", ['' => '-- Select Made By --'] + \App\User::all()->pluck("name", "id")->toArray(), request("scraper_made_by"), ["class" => "form-control select2"]) ?>
            </div>
            <div class="form-group mb-3 col-md-2">
                <?php echo Form::select("scraper_type", ['' => '-- Select Type --'] + \App\Helpers\DevelopmentHelper::scrapTypes(), request("scraper_type"), ["class" => "form-control select2"]) ?>
            </div>
            <div class="form-group mb-3 col-md-2">
                <select name="excelOnly" class="form-control form-group select2">
                    <option <?php echo $excelOnly == '' ? 'selected=selected' : '' ?> value="">All scrapers</option>
                    <option <?php echo $excelOnly == -1 ? 'selected=selected' : '' ?> value="-1">Without Excel</option>
                    <option <?php echo $excelOnly == 1 ? 'selected=selected' : '' ?> value="1">Excel only</option>
                </select>
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
                        <th>Name</th> 
                        <th>Description</th> 
                        <th>Created at</th> 
                        <th>Action</th> 
                    </tr>
                    </thead>
                    <tbody>
                    @php $i=0; @endphp
                    @foreach ($shelves as $sh)
                    <tr>
                        <td width="5%">{{ ++$i }}&nbsp;</td> 
                        <td width="15%">{{ $sh->name  }}&nbsp;</td> 
                        <td width="15%">{{ $sh->description  }}&nbsp;</td> 
                        <td width="15%">{{ $sh->created_at  }}&nbsp;</td> 
                        <td width="50%">
                            <div style="float:left;">       
                                <button style="padding:1px;" type="button" class="btn btn-image d-inline" onclick="showShelf('{{ $sh->slug }}' , '{{ $sh->slug }}' )" title="Show Shelf"><i class="fa fa-eye"></i></button>
                                <button style="padding:1px;" type="button" class="btn btn-image d-inline" onclick="addShelf('{{ $sh->slug }}' , '{{ $sh->slug }}' )" title="Add Shelf"><i class="fa fa-plus"></i></button>
                                <button style="padding:1px;" type="button" class="btn btn-image d-inline" onclick="editShelf('{{ $sh->slug }}' , '{{ $sh->slug }}' )" title="Edit Shelf"><i class="fa fa-edit"></i></button>
                                <button style="padding:1px;" type="button" class="btn btn-image d-inline" onclick="removeShelf('{{ $sh->slug }}' , '{{ $sh->slug }}' )" title="Remove Shelf"><i class="fa fa-remove"></i></button>
                                <button style="padding:1px;" type="button" class="btn btn-image d-inline" onclick="permissionShelf('{{ $sh->slug }}' , '{{ $sh->slug }}' )" title="Permission"><i class="fa fa-lock"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>  
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
                    <div class="row">
                        <div class="form-group mb-3 col-sm-6">
                            <label for="name">Name</label>
                            <input name="term" type="text" class="form-control name" id="name" >
                        </div>
                        <div class="form-group mb-3 col-sm-6">
                            <label for="name">Description</label>
                            <input name="description" type="text" class="form-control description" id="description" >
                        </div>
                        <div class="col-sm-12 d-none" id="book_table">
                            <label for="">Books</label>
                            <table class="table table-bordered table-striped sort-priority-scrapper">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th> 
                                    <th>Description</th> 
                                    <th>Created at</th>  
                                </tr>
                                </thead>
                                <tbody>  
                                </tbody>
                            </table>  
                        </div>
                    </div>
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
    $(document).on("click",".show-shelf",function (e){
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
function emptyModel(){
    var model  = $("#show-content-model-table");
    model.find(".modal-body .name").val('');
    model.find(".modal-body .description").val('');
    model.find(".modal-body .table-bordered tbody").html('');
    model.find(".modal-body #book_table").addClass('d-none');
}
function showShelf(slug){
    emptyModel();
    var slug = slug;
    $.ajax({
        url: `/kb/shelves/show/${slug}`,
        type: 'GET',
        beforeSend: function () {
            $("#loading-image").show();
        }
    }).done(function(response) {
        $("#loading-image").hide();
        var model  = $("#show-content-model-table");
        model.find(".modal-title").html("Shelf Preview");
        model.find(".modal-body .name").val(response.shelf.name);
        model.find(".modal-body .description").val(response.shelf.description);
        if(response.books !== null){
            model.find(".modal-body #book_table").removeClass('d-none');
            $.each(response.books, function(index, val){
                let html = `
                <tr>
                    <th>${index+1}</th> 
                    <th>${val.name}</th> 
                    <th>${val.description}</th> 
                    <th>${val.created_at}</th> 
                </tr>
                `;
                model.find(".modal-body .table-bordered tbody").append(html);
            });
        }
        model.modal("show");
    }).fail(function() {
        $("#loading-image").hide();
        alert('Please check laravel log for more information')
    });
}

function addShelf(id){
    alert(id);
}

function editShelf(id){
    alert(id);
}

function removeShelf(id){
    alert(id);
}

function permissionShelf(id){
    alert(id);
}
    
</script>
@endsection
