@extends('layouts.app')

@section('title', 'Hs Code')

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
            <h2 class="page-heading">Hs Code Generator ({{ count($compositions) }})</h2>
             <div class="pull-right">
                <button type="button" class="btn btn-secondary" onclick="createGroup()">New Group</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')
    <form action="/product/hscode" method="get">
     <div class="mt-3 col-md-12">
        <div class="row">
            <div class="col-md-6">
                 {!! $category_selection !!}
            </div>
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" value="{{ $keyword }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
            </div>
        </div>    
     </div>

    </form>
    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th style="width: 2%"><input type="checkbox" class="form-control" id="ckbCheckAll">Select All</th>
                
                <th width="30%">Combinations</th>
            </tr>
            </thead>
            <tbody id="content_data">
            @foreach($compositions as $composition)
            <tr>
                <td><input type="checkbox" class="form-control checkBoxClass" value="{{ $composition }} {{ $childCategory }} {{ $parentCategory }}" name="composition"></td>
               <td>{{ $composition }} {{ $childCategory }} {{ $parentCategory }}</td>
            </tr>
                
            @endforeach
            </tbody>
        </table>
    </div>
 
@include('products.partials.group-hscode-modal')
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


    function createGroup() {
        $('#groupModal').modal('show');
    }

    $(document).ready(function () {
        $("#ckbCheckAll").click(function () {
            $(".checkBoxClass").prop('checked', $(this).prop('checked'));
        });
    });

    function submitGroup(){
        $('#groupModal').modal('hide');
        hscode = $('#hscode').val();
        name = $('#name').val();
        category = "{{ $parentCategory }}";
        var compositions = [];
            $.each($("input[name='composition']:checked"), function(){
                compositions.push($(this).val());
            });
        if(compositions.length == 0){
            alert('Please Select Combinations');
        }else{

            src = "{{ route('hscode.save.group') }}";
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    hscode : hscode,
                    name : name,
                    compositions : compositions,
                    category : category,
                },
                beforeSend: function () {
                    $("#loading-image").show();
                },

            }).done(function (data) {
                $("#loading-image").hide();
                alert('SucessFully Created Group');
                location.reload();
            }); 

        }



    }



     </script>
   
@endsection