@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

  @endsection

@section('content')
    <style>
        .btn-secondary{
            color: #757575;
            border: 1px solid #ddd;
            background-color: #fff;
        }
    </style>
    <div class="row m-0 p-0">
        <div class="col-lg-12 margin-tb p-0">
            <h2 class="page-heading">Discount Sale  Price
            
            <div class="pull-right">
              <button type="button" class="btn btn-secondary mr-2" data-toggle="modal" data-target="#cashCreateModal">+</button>
            </div>
            </h2>
            
            <form class="form-search-data">
               
                <div class="row">
                   
                    
                      <div class="col-xs-6 col-md-3 pd-3">
                        <div class="form-group">
                        <strong>Type:</strong>
                <select class="form-control" onchange="filltype(this.value,1);" name="type" >
                <option value="" ></option>
                  <option value="brand" {{ 'brand' == old('type') ? 'selected' : '' }}>Brand</option>
                  <option value="category" {{ 'paid' == old('type') ? 'selected' : '' }}>Category</option>
                  <option value="product" {{ 'product' == old('type') ? 'selected' : '' }}>Product</option>
                  <option value="store_website" {{ 'store_website' == old('type') ? 'selected' : '' }}>Store Website</option>
                </select>
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-2 pd-2">
                        <div class="form-group">
                        <strong>Sub Type:</strong>
                        <div id="d_type1">
                <select class="form-control" name="type_id" required id="type_id">
                  </select>
                </div>
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-2 pd-2">
                        <div class="form-group">
                        <strong>Supplier:</strong>
                        <select class="form-control"  name="supplier" >
                <option value="" >Select Supplier </option>
                @foreach($supplier as $s)
                    <option value="{{$s->id}}"> {{$s->supplier}} </option>
                @endforeach
            
              </select>
                        </div>
                    </div>
                    
                    

                    

                    
                    <button type="button" onclick="$('.form-search-data').submit();" class="btn btn-image btn-call-data"><img src="{{asset('/images/filter.png')}}"></button>
                    
                </div>    
                
            </form>
          
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

   <div class="row" >
        <div class="col-md-12">
        <div class="table-responsive ">
           <table class="table table-bordered">
               <thead>
               <tr>
                   <th>Sl</th>
                   <th>Type</th>
                   <td>Type Id</td>
                   <td>Supplier</td>
                   <th>From Date</th>
                   <th>To Date</th>
                   <th>Amount</th>
                   <th>Amount type</th>
                   <th>Actions</th>
               </tr>
               </thead>

               <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
               @foreach ($discountsaleprice as $d)
                   <tr>
                       <td class="small">{{ $d->id }}</td>
                       <td>{{ $d->type }} </td>
                       <td>
                         @php
                              if ($d->type=='brand')
                               {
                                $r=\App\Brand::where('id',$d->type_id)->first();
                                echo $r->name;
                               }

                               if ($d->type=='product')
                               {
                                $r=\App\Product::where('id',$d->type_id)->first();
                                echo $r->name;
                               }

                               if ($d->type=='category')
                               {
                                $r=\App\Category::where('id',$d->type_id)->first();
                                echo $r->title;
                               }

                               if ($d->type=='store_website')
                               {
                                $r=\App\StoreWebsite::where('id',$d->type_id)->first();
                                echo $r->title;
                               }
                                 
                         @endphp
                      

                       
                       </td>
                       <td>{{ $d->supplier }}</td>
                       <td>{{ date('d-m-Y',strtotime($d->start_date)) }}</td>
                       <td>{{date('d-m-Y',strtotime($d->end_date)) }}</td>
                       <td>{{ $d->amount }}</td>
                       <td>{{ $d->amount_type }}</td>
                       <td>
                         @php
                         $d->start_date=date('d-m-Y',strtotime($d->start_date));
                         $d->end_date=date('d-m-Y',strtotime($d->end_date));

                         @endphp
                       <button type="button" class="btn btn-image edit-form d-inline"  data-toggle="modal" data-target="#cashCreateModal" data-edit="{{ json_encode($d) }}"><img src="/images/edit.png" /></button>  
                       {!! Form::open(['method' => 'DELETE','url' => ['discount-sale-price', $d->id],'style'=>'display:inline']) !!}
                           <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                           {!! Form::close() !!}

                       </td>
                       
                   </tr>
               @endforeach
               </tbody>
           </table>
       </div>
       <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />

      
   </div>

    <div id="cashCreateModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ url('discount-sale-price/create') }}" method="POST" enctype="multipart/form-data">
            @csrf
              <input type="hidden" name="id" value="0" id="e_id"> 
            <div class="modal-header">
              <h4 class="modal-title">Discount Sale Price</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <div class="form-group">
                <strong>Type:</strong>
                <select class="form-control" onchange="filltype(this.value);" name="type" required id="type1">
                <option value="" ></option>
                  <option value="brand" {{ 'brand' == old('type') ? 'selected' : '' }}>Brand</option>
                  <option value="category" {{ 'paid' == old('type') ? 'selected' : '' }}>Category</option>
                  <option value="product" {{ 'product' == old('type') ? 'selected' : '' }}>Product</option>
                  <option value="store_website" {{ 'store_website' == old('type') ? 'selected' : '' }}>Store Website</option>
                </select>

                @if ($errors->has('type'))
                  <div class="alert alert-danger">{{$errors->first('type')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Type Id:</strong>
                <div id="d_type">
                <select class="form-control" name="type_id" required id="type_id">
                  </select>
                </div>
                @if ($errors->has('type'))
                  <div class="alert alert-danger">{{$errors->first('type')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Supplier:</strong>
                <select class="form-control"  name="supplier_id" id="supplier_id">
                <option value="" ></option>
                @foreach($supplier as $s)
                    <option value="{{$s->id}}"> {{$s->supplier}} </option>
                @endforeach
            
              </select>

                @if ($errors->has('supplier'))
                  <div class="alert alert-danger">{{$errors->first('supplier')}}</div>
                @endif
              </div>
             

              <div class="form-group">
                <strong>From Date:</strong>
                <div >
                  <input type='text' class="form-control" name="start_date" id="start_date" value="{{ date('Y-m-d H:i') }}" required />

                </div>

                @if ($errors->has('date'))
                  <div class="alert alert-danger">{{$errors->first('date')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>To Date:</strong>
                <div >
                  <input type='text' class="form-control" name="end_date"  id="end_date" value="{{ date('Y-m-d H:i') }}" required />

                 
                </div>

                @if ($errors->has('date'))
                  <div class="alert alert-danger">{{$errors->first('date')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Amount:</strong>
                <input type="number" name="amount"  id="amount" class="form-control" value="{{ old('amount') }}" required>

                @if ($errors->has('amount'))
                  <div class="alert alert-danger">{{$errors->first('amount')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Amount Type:</strong>
                <select class="form-control"  name="amount_type" required id="amount_type">
                <option value="amount" >Amount</option>
                  <option value="percentage">Percentage</option>
                   </select>

                @if ($errors->has('type'))
                  <div class="alert alert-danger">{{$errors->first('amount_type')}}</div>
                @endif
              </div>

              



             
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Add</button>
            </div>
          </form>
        </div>

      </div>
    </div>




@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script>
  $(function() {
    $('input[name="daterange"]').daterangepicker({
      autoUpdateInput: false,
      opens: 'left'
    }, function(start, end, label) {
      console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
    });
  });
</script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#start_date').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });

      $('#end_date').datetimepicker({
        format: 'YYYY-MM-DD'
      });
    });

    


    $(document).on("click",".submit-cashflow",function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        $.ajax({
            url: "{{url('/cashflow/do-payment')}}",
            type: 'POST',
            data : form.serialize(),
            dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function(result){
                $("#loading-image").hide();
                if(result.code == 200) {
                  $("#do-payment-model").modal("hide");
                  toastr["success"](result.message);
                }else if(result.code == 401) {
                   var html = result.message+"</br>";
                    $.each(result.data,function(i,k) {
                        $.each(k,function(p,m) {
                            html += m+"</br>";
                        });
                    });
                    toastr["error"](html);
                }
            },
            error: function (){
                $("#loading-image").hide();
            }
        });
    });

  </script>

<script>
        
        var isLoading = false;
        var page = 1;
        $(document).ready(function () {
            
            $(window).scroll(function() {
                if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
                    loadMore();
                }
            });

            function loadMore() {
                if (isLoading)
                    return;
                isLoading = true;
                var $loader = $('.infinite-scroll-products-loader');
                page = page + 1;
                $.ajax({
                    url: "{{url('cashflow')}}?ajax=1&page="+page,
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function (data) {
                        
                        $loader.hide();
                        if('' === data.trim())
                            return;
                        $('.infinite-scroll-cashflow-inner').append(data);
                        

                        isLoading = false;
                    },
                    error: function () {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }            
        });

        function filltype(type,t=0)
        {
         if (type!='')
          {
            $("#d_type").html('');            
            $.ajax({
                    url: "{{url('discount-sale-price/type')}}?type="+ type,
                    type: 'GET',
                    success: function (data) {
                      if (t==1)
                      $("#d_type1").html(data);
                      else
                      $("#d_type").html(data); 
                     
               
                    },
                    error: function () {
                       
                    }
                });
              
              
              
          }      
        }

        $(document).on('click', '.edit-form', function() {
         
      var data = $(this).data('edit');
      
     
     // var url = "{{ route('email-addresses.index') }}/" + emailAddress.id;

      //$('#emailAddressEditModal form').attr('action', url);
      $('#e_id').val(data.id);
      $('#type1').val(data.type);
      
      filltype(data.type)

      $('#amount').val(data.amount);
      $('#amount_type').val(data.amount_type);
      $('#supplier_id').val(data.supplier_id);
      $('#start_date').val(data.start_date);
      $('#end_date').val(data.end_date);
      $('#type_id').val(data.type_id);
      
      $('#start_date').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });

      $('#end_date').datetimepicker({
        format: 'YYYY-MM-DD'
      });
     

     /* $('#emailAddressEditModal').find('input[name="recovery_phone"]').val(emailAddress.recovery_phone);
      $('#emailAddressEditModal').find('input[name="recovery_email"]').val(emailAddress.recovery_email);

      $('#emailAddressEditModal').find('input[name="signature_name"]').val(emailAddress.signature_name);
	    $('#emailAddressEditModal').find('input[name="signature_title"]').val(emailAddress.signature_title);
      $('#emailAddressEditModal').find('input[name="signature_email"]').val(emailAddress.signature_email);
      $('#emailAddressEditModal').find('input[name="signature_phone"]').val(emailAddress.signature_phone);
      $('#emailAddressEditModal').find('input[name="signature_website"]').val(emailAddress.signature_website);
      $('#emailAddressEditModal').find('textarea[name="signature_address"]').val(emailAddress.signature_address);
      $('#emailAddressEditModal').find('textarea[name="signature_social"]').val(emailAddress.signature_social);
      $("#img1").attr("src",  "{{url('/')}}/uploads/" + emailAddress.signature_logo);
      $("#img2").attr("src",  "{{url('/')}}/uploads/" + emailAddress.signature_image);
     	  
      $('#edit_store_website_id').val(emailAddress.store_website_id).trigger('change');

      $('#edit_driver').val(emailAddress.driver).trigger('change');
      $('#edit_port').val(emailAddress.port).trigger('change');
      $('#edit_encryption').val(emailAddress.encryption).trigger('change');
      */
    });

  </script>      
@endsection
