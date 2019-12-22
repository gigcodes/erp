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

                <button type="button" class="btn btn-secondary" onclick="createGroup()">Group</button>
            </div>
        </div>
    </div>
    
        <div class="col-md-12">
            <div class="panel-group">
                <div class="panel mt-5 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" href="#collapse1">Groups</a>
                        </h4>
                    </div>
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="pull-right">
                            
                     </div>
                            <table class="table table-bordered table-striped" id="phone-table">
                                <thead>
                                <tr>
                                    <th>Group Name</th>
                                    <th>Hscode</th>
                                    <th>Composition</th>
                                    <th>Composition Count</th>
                                    <!-- <th>Edit</th> -->
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($groups as $group)    
                                <tr>
                                    <td>{{ $group->name }}</td>
                                    <td>{{ $group->hsCode->code }}</td>
                                    <td>{{ $group->composition }}</td>
                                    <td>{{ $group->groupComposition->count() }}</td>
                                    <!-- <td><button onclick="editGroup({{ $group->id }})">Edit</button> -->
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    
    @include('partials.flash_messages')
    <form action="/product/hscode" method="get">
     <div class="mt-3 col-md-12">
        <div class="row">
            <div class="col-md-4">
                 {!! $category_selection !!}
            </div>
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" value="{{ $keyword }}">
            </div>
            <div class="col-md-2">
                <input type="checkbox" name="group" @if($groupSelected == 'on') checked @endif>Include Group
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
                <th style="width: 2%"><input type="checkbox" id="ckbCheckAll">  Select All</th>
                
                <th width="30%">Combinations</th>
            </tr>
            </thead>
            <tbody id="content_data">
            @foreach($compositions as $composition)
            <tr>
                <td><input type="checkbox" class="form-control checkBoxClass" value="{{ $composition }} {{ $childCategory }} {{ $parentCategory }}" name="composition"></td>
               <td>{{ $composition }} [ {{ $childCategory }} ] > {{ $parentCategory }}</td>
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
        
        hscode = $('#hscode').val();
        name = $('#name').val();
        composition = $('#composition').val();
        category = "{{ $parentCategory }}";
        existing_group = $('#existing_group').val();
        var compositions = [];
            $.each($("input[name='composition']:checked"), function(){
                compositions.push($(this).val());
            });
        if(compositions.length == 0){
            alert('Please Select Combinations');
        }else if(hscode == '' && hscode == null){
            alert('Please Select Hscode First');
        }else{
            src = "{{ route('hscode.save.group') }}";
            $.ajax({
                url: src,
                type: "POST",
                dataType: "json",
                data: {
                    hscode : hscode,
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

            }).done(function (data) {
                $("#loading-image").hide();
                alert('SucessFully Created Group');
                location.reload();
            }); 

        }
    }


    function editGroup(id){

    }

     </script>
   
@endsection