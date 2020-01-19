@extends('layouts.app')



@section('title', 'Most Recent By Category')

@section("styles")
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

        
    </style>
@endsection

@section('content')
<div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
<div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Most Recent By Category (<span id="count">{{ ($categories->count() * 3) }}</span>)</h2>
            <div class="pull-right">
                 <button type="button" class="btn btn-secondary" onclick="createGroup()">Group</button>
                <button type="button" class="btn btn-image" onclick="resetSearch()"><img src="/images/resend2.png"/></button>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="category-table">
            <thead>
            <tr>
                <th style="width: 2%"><input type="checkbox" id="ckbCheckAll">  Select All</th>
                <th>Code</th>
                <th>Description</th>
            </tr>
            <tr>
            <th></th>    
            <th>{!! $category_selection !!}</th>
            <th><input type="text" id="combination" class="search form-control"></th>
          </tr>
            </thead>
             {!! $categories->appends(Request::except('page'))->links() !!}
            <tbody>
            @include('simplyduty.most-common-category.partials.data')
            </tbody>
        </table>
    </div>
    {!! $categories->appends(Request::except('page'))->links() !!}
@include('products.partials.group-hscode-modal')
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
 $(document).ready(function() {
        src = "{{ route('hscode.mostcommon.category') }}";
        $(".search").on('blur', function () {
            category = $('#category_value').val();
            if(category == 1){
                category = '';
            }
            combination = $('#combination').val();
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    category : category,
                    combination : combination,
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                 $('#count').text(data.total)
                console.log(data);
                $("#category-table tbody").empty().html(data.tbody);
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

       function resetSearch() {
           src = "{{ route('hscode.mostcommon.category') }}";
            reset = '';
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    reset : reset,
                },
                beforeSend: function() {
                        $("#loading-image").show();
                },
            
            }).done(function (data) {
                    $("#loading-image").hide();
                console.log(data);
                $("#category-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
           
       }

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
        name = "";
        composition = $('#composition').val();
        category = $('#category_value').val();
        existing_group = $('#existing_group').val();
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
                type: "POST",
                dataType: "json",
                data: {
                    name : name,
                    compositions : compositions,
                    composition : composition, 
                    category : category,
                    existing_group : existing_group,
                    "_token": "{{ csrf_token() }}",
                },
                beforeSend: function () {
                    $('#groupModal').modal('hide');
                    $("#loading-image").show();
                },
                success: function(data) {
                    $('#groupModal').modal('hide');
                    alert(data);
                    location.reload();
                },
                error: function(xhr) { // if error occured
                    alert("Error occured.please try again");
                },

            }); 

        }
    }

    $( ".category_class" ).change(function() {
            category = $(this).val();
            combination = $('#combination').val();
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    category : category,
                    combination : combination,
                },
                beforeSend: function() {
                       $("#loading-image").show();
                },
            
            }).done(function (data) {
                 $("#loading-image").hide();
                 $('#count').text((data.total*3));
                console.log(data);
                $("#category-table tbody").empty().html(data.tbody);
                if (data.links.length > 10) {
                    $('ul.pagination').replaceWith(data.links);
                } else {
                    $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                }
                
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
    });


        </script>
@endsection
