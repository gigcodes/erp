@extends('layouts.app')

@section('title', 'Supplier Inventory History')

@section('large_content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-md-12 p-0">
            <h2 class="page-heading">Supplier Discount Files 

                <div class="pull-right">
                    
                    <button type="button" class="btn btn-secondary mr-2" data-toggle="modal" data-target="#Log-details" style="background: #fff !important;
                    border: 1px solid #ddd !important;
                    color: #757575 !important;">Log Details</button>
                </div>

               </h2>
        </div>

        <div class="col-12">
          <div class="pull-left">
            <div class="form-group" style="margin-bottom:2px;">
                <div class="row">
                    <form method="GET" action="{{ route('supplier.discount.files') }}">
                        <div class="flex" style="margin-bottom:-7px;">
                           
                            <div class="form-group" style="width:300px; padding-left:16px;">
                               
                                <select class="form-control select-multiple globalSelect2" id="supplier-selects" tabindex="-1" aria-hidden="true" name="supplier">
                                    <option value="">Search Supplier...</option>
                                    @foreach($suppliers as $supplier)
                                        @if(isset($request->supplier) && $supplier->id==$request->supplier)
                                            <option value="{{$supplier->id}}" selected="selected">{{$supplier->supplier}}</option>
                                        @else
                                            <option value="{{$supplier->id}}">{{$supplier->supplier}}</option>
                                        @endif
                                    @endforeach
                                </select>

                              </div>
                              <div class="form-group" style="width:300px; padding-left:10px;">
                                <select class="form-control select-multiple globalSelect2" id="brand-selects" tabindex="-1" aria-hidden="true" name="brands" >
                                    <option value="">Search Brands...</option>
                                    @foreach ($brand_data as $key=> $row ) 
                                        @if(isset($request->brands) && $row->brand->id==$request->brands)
                                            <option value="{{$row->brand->id}}" selected="selected">{{$row->brand->name}}</option>
                                        @else
                                            <option value="{{$row->brand->id}}">{{$row->brand->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                
                              </div>

                              <div class="col-md-2">
                                <button type="submit" class="btn btn-image ml-3"><img src="{{asset('images/filter.png')}}" style="margin-left: -10px;"/></button>
        
                                <a href="{{ route('supplier.discount.files') }}" class="fa fa-refresh" aria-hidden="true" style="margin-left: 3px;"></a>
                            </div>

                            
                        </div>
                    </form>
                </div>

            </div>
          </div>

          <div class="pull-right">
            <div class="form-group">
              &nbsp;
            </div>
          </div>
        </div>
    </div>

    @include('partials.flash_messages')

<form method="post" action="{{route('supplier.discount.files.post')}}" enctype="multipart/form-data" class="excel_form">
@csrf 
     <div class="form-group">
       
        <div class="row"> 
            
            <div class="col-md-3">
                <select class="form-control select-multiple" id="supplier-select" tabindex="-1" aria-hidden="true" name="supplier" >
                    <option value="">Select Supplier</option>
                    @foreach($suppliers as $supplier)
                       
                            <option value="{{$supplier->id}}">{{$supplier->supplier}}</option>
                       
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input class="form-control" type="file" id="file" name="excel" placeholder="Select File..." accept=".xlsx, .xls"/>
            </div>
            <div class="col-md-3 d-flex justify-content-between">
                <button type="submit" class="btn btn-secondary add_save_excel" >Import</button>
            </div> 

            <div id="loading-image_" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
            </div>

            </div>
    </div>
</form>

    <div class="row">
        <div class="col-md-12">
          
            <table id="table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Brand</th>
                        <th>Supplier Name</th> 
                        <th>Gender </th>
                        <th>Category</th>
                        <th>Generice price</th>
                        <th>Exceptions</th>
                        <th>Condition from retail</th>
                        <th>Retail condition for exceptions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $key=> $row ) 
                    <tr>
                       <td>{{$row->brand->name}}</td> 
                       <td>{{$row->supplier->supplier}}</td>
                       <td>{{$row->gender}}</td> 
                       <td>{{$row->category}}</td> 
                       <td>{{$row->generic_price ?? '-'}}</td> 
                       <td>{{$row->exceptions ?? '-'}}</td> 
                       <td>{{$row->condition_from_retail ?? '-'}} 
                            <a title="Log Details" class="fa fa-info-circle discount_log" data-id="{{ $row->id }}" data-header="condition_from_retail" style="font-size:15px; margin-left:10px; color: #757575;"></a>
                        </td> 
                        <td>{{$row->condition_from_retail_exceptions ?? '-'}}
                            @if($row->condition_from_retail_exceptions)
                            <a title="Log Details" class="fa fa-info-circle discount_log" data-id="{{ $row->id }}" data-header="condition_from_retail_exceptions" style="font-size:15px; margin-left:10px; color: #757575;"></a>
                            @endif
                        </td> 
                    </tr>
                    @endforeach
                    <tr>
                         <td colspan="11">
                            {{ $rows->appends($request->except('page'))->links() }}.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

 <div id="brand-history-model" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Brand History</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
            <div class="modal-body">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
      </div>
  </div>
</div>

<div id="log-history-model" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Brand History</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="" id="approve-log-btn" method="GET">
                @csrf
                <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="supplier_brand_discounts_id" id="supplier_brand_discounts_id">
                    <div class="col-md-12">
                        <table class="table">
                            <thead>
                                <tr>
                                   
                                    <th>Old Value</th>
                                    <th>New Value</th>
                                    <th>Updated By</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>             
        </div>
    </div>
  </div>

  
  <!-- Modal -->
  <div class="modal fade" id="Log-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Log Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            
            <form action="" id="excel-log-btn" method="POST" >
                @csrf

                <div class="modal-body">
                    
                <div class="row">
                   
                    <div class="col-md-12" id="log-details">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Excel Name</th>
                                    <th>Updated by</th>
                                    <th>Created at</th>
                                    <th>Action</th>
                                                        
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($excel_data as $key => $value)
                                <tr>
                                     <td> {{$value->excel_name}} </td>
                                     <td> {{$value->users->name}} </td>
                                     <td> {{$value->created_at}} </td>
                                    <td>
                                        {{-- <a title="Download Invoice" class="btn btn-image" href="">
                                            <i class="fa fa-download downloadpdf"></i>
                                        </a> --}}
                                        <a href='/product/discount/excel/files/?filename={{$value->excel_name}}' title='Download Excel' class='btn btn-image ml-1 download_excel' ><i class='fa fa-download' aria-hidden='true'></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                   
                </div>
            </form>
        </div>
      
      </div>
    </div>
  </div>

@endsection

@section('scripts')

<script type="text/javascript">
 
    
    $('.excel_form').submit(function(e){
     
        if($('#supplier-select').val() == ''){
            toastr['error']('Please select supplier');
            e.preventDefault();
        }else if($('#file').val() == ''){
            toastr['error']('Please upload file');
            e.preventDefault();
        }else{
            $("#loading-image_").css('display', 'block');
        }
    });
    

    $(document).on("click", ".discount_log", function(e) {

        var id = $(this).data('id');
        var header = $(this).data('header');
        
        $('#log-history-model table tbody').html('');
        $.ajax({
        url: "{{ route('log-history/discount/brand') }}",
        data: {id: id,header : header },
        success: function (data) {
            if(data != 'error') {
                $('input[name="supplier_brand_discounts_id"]').val(id);
             
                $.each(data, function(i, item) {
                    
                    $('#log-history-model table tbody').append(
                        '<tr>\
                            <td>'+ ((item['old_value']) ? item['old_value'] : '-') +'</td>\
                            <td>'+item['new_value']+'</td>\<td>'+item['name']+'</td>\
                            <td>'+ moment(item['updated_at']).format('DD/MM/YYYY') +'</td>\
                        </tr>'
                    );
                });
            }
        }
        });

        $('#log-history-model').modal('show');
    });
                  
</script>

@endsection



