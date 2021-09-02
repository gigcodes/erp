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
            <h2 class="page-heading">Cash Flow
            
            <div class="pull-right">
              <button type="button" class="btn btn-secondary mr-2" data-toggle="modal" data-target="#cashCreateModal">+</button>
            </div>
            </h2>
            
            <form class="form-search-data">
               
                <div class="row">
                    <div class="col-xs-6 col-md-2 pd-2">
                        <div class="form-group cls_task_subject">
                            <input type="text" name="site_name" placeholder="Search Site Name" id="site_name" class="form-control input-sm ui-autocomplete-input" value="" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-2 pd-2">
                        <div class="form-group">
                        <input type="text" name="daterange" placeholder="Search date" id="daterange" class="form-control input-sm ui-autocomplete-input" value="<?php if (isset($_GET['daterange'])) { echo $_GET['daterange'] ;} ?>" autocomplete="off">    </div>
                    </div>
                    
                      <div class="col-xs-6 col-md-2 pd-2">
                        <div class="form-group">
                            <select onchange="get_bname();" name="module_type" id="module_type" class="form-control input-sm">
                                <option selected="" value="0"> Filter By Module / Type</option>
                                <option value="order"  <?php if (isset($_GET['module_type']) && $_GET['module_type']=='order') {  echo "selected='selected'" ;} ?> >Order</option>
                                <option value="payment_receipt"  <?php if (isset($_GET['module_type']) && $_GET['module_type']=='payment_receipt') {  echo "selected='selected'" ;} ?> >Payment Receipt</option>
                                <option value="assent_manager" <?php if (isset($_GET['module_type']) && $_GET['module_type']=='assent_manager') {  echo "selected='selected'" ;} ?> >Assent Manager</option>
                          
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-2 pd-2">
                        <div class="form-group">
                            <select name="b_name" id="b_name" class="form-control input-sm">
                                <option value="">Benefiiciary</option>
                                
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-6 col-md-2 pd-2">
                        <div class="form-group">
                            <select name="type" id="type" class="form-control input-sm">
                                <option value="">Filter by Type</option>
                                <option value="Pending"  <?php if (isset($_GET['type']) && $_GET['type']=='Pending') {  echo "selected='selected'" ;} ?> >Pending</option>
                                <option value="Received"  <?php if (isset($_GET['type']) && $_GET['type']=='Received') {  echo "selected='selected'" ;} ?> >Received</option>
                                <option value="Paid" <?php if (isset($_GET['type']) && $_GET['type']=='Paid') {  echo "selected='selected'" ;} ?> >Paid</option>
                                
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
                   <th>Date</th>
                   <th>Module</th>
                   <td>Website</td>
                   <td>Beneficiary Name</td>
                   <th>Type</th>
                   <th>Description</th>
                   <th>Amount</th>
                   <th>Amount(EUR)</th>
                   <th>Erp Amount</th>
                   <th>Erp Amount(EUR)</th>
                   <th>Monetary Account</th>
                   <th>Type</th>
                   <th>Actions</th>
               </tr>
               </thead>

               <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
               @foreach ($cash_flows as $cash_flow)
                   <tr>
                       <td class="small">{{ date('Y-m-d', strtotime($cash_flow->date)) }}</td>
                       <td>{!! $cash_flow->getLink() !!}</td>
                       <td>@switch($cash_flow->cash_flow_able_type)
                             @case('\App\Order')
                                  <a href="{{ @$cash_flow->cashFlowAble->storeWebsiteOrder->storeWebsite->website_url }}" target="_blank">{{$cash_flow->cashFlowAble->storeWebsiteOrder->storeWebsite->website ?? ''}}</a></p>        
                                  @break
                           @default
                             
                           @endswitch 

                       </td>
                       <td>{!! $cash_flow->get_bname()!!} </td>
                       <td>{{ class_basename($cash_flow->cashFlowAble) }}</td>
                       <td>
                           {{ $cash_flow->description }}
                           @if ($cash_flow->files)
                               <ul>
                                   @foreach ($cash_flow->files as $file)
                                       <li><a href="{{ route('cashflow.download', $file->id) }}" class="btn-link">{{ $file->filename }}</a></li>
                                   @endforeach
                               </ul>
                           @endif
                       </td>
                       <td>@if(!is_numeric($cash_flow->currency))  {{$cash_flow->currency}}  @endif{{ $cash_flow->amount }}</td>
                       <td>{{ $cash_flow->amount_eur }}</td>
                       <td>{{$cash_flow->currency}} {{ $cash_flow->erp_amount }}</td>
                       <td>{{ $cash_flow->erp_eur_amount }}</td>
                       <td>
                        {{($cash_flow->monetaryAccount)?$cash_flow->monetaryAccount->name: "N/A"}}
                       </td>
                       <td>{{ ucwords($cash_flow->type) }}</td>
                       <td>
                           <a title="Do Payment" data-id="{{ $cash_flow->id }}" data-mnt-amount="{{ $cash_flow->amount }}" data-mnt-account="{{ $cash_flow->monetary_account_id }}" class="do-payment-btn"><span><i class="fa fa-money" aria-hidden="true"></i></span></a>
                           {!! Form::open(['method' => 'DELETE','route' => ['cashflow.destroy', $cash_flow->id],'style'=>'display:inline']) !!}
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
          <form action="{{ route('cashflow.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="modal-header">
              <h4 class="modal-title">Store a Record</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              {{-- <div class="form-group">
                <select class="selectpicker form-control" data-live-search="true" data-size="15" name="user_id" title="Choose a User" required>
                    @foreach ($users as $user)
                      <option data-tokens="{{ $user->name }} {{ $user->email }}" value="{{ $user->id }}"  {{ $user->id == old('user_id') ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>

                @if ($errors->has('user_id'))
                  <div class="alert alert-danger">{{$errors->first('user_id')}}</div>
                @endif
              </div> --}}

              <div class="form-group">
                <strong>Description:</strong>
                <textarea name="description" class="form-control" rows="8" cols="80">{{ old('description') }}</textarea>

                @if ($errors->has('description'))
                  <div class="alert alert-danger">{{$errors->first('description')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Date:</strong>
                <div class='input-group date' id='date-datetime'>
                  <input type='text' class="form-control" name="date" value="{{ date('Y-m-d H:i') }}" required />

                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>

                @if ($errors->has('date'))
                  <div class="alert alert-danger">{{$errors->first('date')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Amount:</strong>
                <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" required>

                @if ($errors->has('amount'))
                  <div class="alert alert-danger">{{$errors->first('amount')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Type:</strong>
                <select class="form-control" name="type" required id="cashflow_type">
                  <option value="received" {{ 'received' == old('type') ? 'selected' : '' }}>Received</option>
                  <option value="paid" {{ 'paid' == old('type') ? 'selected' : '' }}>Paid</option>
                </select>

                @if ($errors->has('type'))
                  <div class="alert alert-danger">{{$errors->first('type')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Category:</strong>
                <select class="form-control" name="cash_flow_category_id" id="cashflow_category">
                  <option value="">Select Category</option>
                  @foreach ($categories['received'] as $id => $category)
                    <option value="{{ $id }}" {{ $id == old('cash_flow_category_id') ? 'selected' : '' }}>{{ $category }}</option>
                  @endforeach
                </select>

                @if ($errors->has('cash_flow_category_id'))
                  <div class="alert alert-danger">{{$errors->first('cash_flow_category_id')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>Files:</strong>
                <input type="file" name="file[]" class="form-control" value="" multiple>

                @if ($errors->has('file'))
                  <div class="alert alert-danger">{{$errors->first('file')}}</div>
                @endif
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Add</button>
            </div>
          </form>
        </div>

      </div>
    </div>


<div id="do-payment-model" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('cashflow.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h4 class="modal-title">Create a receipt for <span class="text-cashflow-id"></span></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
              <input type="hidden" name="cash_flow_id" id="cashflow-id-txt">
              <strong>Amount:</strong>
              <?php
                  echo Form::text('amount',null, ['placeholder' => 'Insert Amount','class' => 'form-control', 'id' => 'cashflow-amount']);
              ?>
            </div>  
            <div class="form-group">
              <strong>Type:</strong>
              <?php
                  echo Form::select('type',["received" => "Received" , "paid" => "Paid"], null, ['placeholder' => 'Select a type','class' => 'form-control']);
              ?>
            </div>
            <div class="form-group">
              <strong>Date:</strong>
              <?php
                  echo Form::text('date', null, ['placeholder' => 'Enter date','class' => 'form-control enter-date']);
              ?>
            </div>
            <div class="form-group">
              <strong>Monetary Account:</strong>
              <?php
                  $monetaryAccount = \App\MonetaryAccount::pluck("name","id")->toArray(); 
                  echo Form::select('monetary_account_id',$monetaryAccount, null, ['placeholder' => 'Select a Account','class' => 'form-control', "id" => "monetary-account-id-txt"]);
              ?>
            </div>
            <div class="form-group">
              <strong>Note:</strong>
              <?php
                  echo Form::textarea('description',null, ['placeholder' => 'Insert note','class' => 'form-control']);
              ?>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary submit-cashflow">Add</button>
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
      $('#date-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
      });

      $('.enter-date').datetimepicker({
        format: 'YYYY-MM-DD'
      });
    });

    $('#cashflow_type').on('change', function() {
      var type = $(this).val();
      var categories = {!! json_encode($categories) !!};

      $('#cashflow_category').empty();

      $('#cashflow_category').append($('<option>', {
        value: '',
        text: 'Select Category'
      }));

      Object.keys(categories[type]).forEach(function(category_id) {
        $('#cashflow_category').append($('<option>', {
          value: category_id,
          text: categories[type][category_id]
        }));
      });
    });


    $(document).on("click",".do-payment-btn",function() {
      var $this = $(this);
      var id  = $this.data("id");
      var mnt = $this.data("mnt-account");
      var amount = $this.data("mnt-amount");
      $("#do-payment-model").find(".text-cashflow-id").html("#"+id);
      $("#do-payment-model").find("#cashflow-id-txt").val(id);
      $("#do-payment-model").find("#monetary-account-id-txt").val(mnt);
      $("#do-payment-model").find("#cashflow-amount").val(amount);
      $("#do-payment-model").modal("show");
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

        function get_bname()
        {
          module_type=$("#module_type").val();
          $("#b_name").empty();
          if (module_type!='')
          {
              
            $.ajax({
                    url: "{{url('cashflow/getbnamelist')}}?model_type="+ module_type,
                    type: 'GET',
                    success: function (data) {
                      $("#b_name").append("<option value=''>Benefiiciary</option>");
                      Object.entries(data).forEach(entry => {
                        const [key, value] = entry;
                        $("#b_name").append("<option value='"+data[key]+"'>"+data[key]+"</option>");
                     });
               
                    },
                    error: function () {
                       
                    }
                });
              
              
              
          }      
        }

  </script>      
@endsection
