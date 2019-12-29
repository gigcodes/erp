@extends('layouts.app')

@section('styles')
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
        <div class="col-md-12">
            <h1 class="text-center">Crop Reference Grid (<span id="total">{{ $total }}</span>)</h1>
            <div class="pull-right">
                 <button onclick="addTask()" class="btn btn-secondary">Add Issue</button>
                 
            </div>
                <br>
            <!--Product Search Input -->
                <form method="GET" action="crop-references-grid" class="form-inline align-items-start">
                   
                   <div class="form-group mr-3 mb-3">
                        {!! $category_selection !!}
                    </div>

                    <div class="form-group mr-3">
                        @php $brands = \App\Brand::getAll();
                        @endphp
                        <select data-placeholder="Select brands" class="form-control select-multiple2" name="brand[]" multiple id="brand">
                            <optgroup label="Brands">
                                @foreach ($brands as $id => $name)
                                    <option value="{{ $id }}" {{ isset($brand) && $brand == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group mr-3">
                        @php $suppliers = new \App\Supplier();
                        @endphp
                        <select data-placeholder="Select Supplier" class="form-control select-multiple2" name="supplier[]" multiple id="supplier">
                            <optgroup label="Suppliers">
                                @foreach ($suppliers->select('id','supplier')->where('supplier_status_id',1)->get() as $id => $suppliers)
                                    <option>{{ $suppliers->supplier }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                     <div class="form-group mr-3">
                        <select data-placeholder="Select Crop" class="form-control select-multiple2" name="crop" id="crop">
                            <optgroup label="Crop">
                                <option value="1">All</option>
                                <option value="2">Cropped</option>
                                <option value="3">Uncropped</option>
                            </optgroup>
                        </select>
                    </div>


                   

                   
                    
                    <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
                    <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
                </form>
        </div>
         
       

        {!! $products->links() !!}
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table-striped table-bordered table" id="log-table">
                    <thead>
                    <tr>
                        <th>ID <input type="checkbox" name="" id="globalCheckbox"></th>
                        <th>Category</th>
                        <th>Supplier</th>
                        <th>Brand</th>
                        <th>Original Image</th>
                        <th>Cropped Image</th>
                        <th>Time</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Issue</th>
                    </tr>
                     </thead>
                    <tbody id="content_data">
                    @include('image_references.partials.griddata')
                    </tbody>
                </table>
            </div>
        </div>

        
    </div>

    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

 @include('partials.modals.task-module')
 @include('partials.modals.large-image-modal')
   
@endsection

@section('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

<script type="text/javascript">
      $(document).ready(function () {
             $(".select-multiple").multiselect();
             $(".select-multiple2").select2();
        });     

    function bigImg(img){
        $('#image_crop').attr('src',img);
        $('#largeImageModal').modal('show');
    }

    function normalImg(){
        $('#largeImageModal').modal('hide');
    }

    function addTask() {
       var id = [];
            $.each($("input[name='issue']:checked"), function(){
                id.push($(this).val());
            });
        if(id.length == 0){
            alert('Please Select Image');
        }else{
            $('#taskModal').modal('show');
            $('#task_subject').val('Image ID '+id);
        }   
        
    }

    function refreshPage() {
         blank = ''
         $.ajax({
            url: '/crop-references-grid',
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
            $("#total").text(data.total);
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

    $('#globalCheckbox').click(function(){
            if($(this).prop("checked")) {
                $(".checkBox").prop("checked", true);
            } else {
                $(".checkBox").prop("checked", false);
            }                
        });



</script>

 <script type="text/javascript">
        $(document).ready(function () {
            $('#brand,#category,#crop,#supplier').on('change', function () {
                $.ajax({
                    url: '/crop-references-grid',
                    dataType: "json",
                    data: {
                        brand: $('#brand').val(),
                        category: $('#category').val(),
                        crop : $('#crop').val(),
                        supplier : $('#supplier').val(),
                    },
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                }).done(function (data) {
                    $("#loading-image").hide();
                    console.log(data);
                    $("#total").text(data.total);
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
    </script>

@endsection