@extends('layouts.app')


@section('favicon' , 'task.png')

@section('title', 'Voucher Coupons')

@section("styles")
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
  <style type="text/css">
    .preview-category input.form-control {
      width: auto;
    }

    #loading-image {
              position: fixed;
              top: 50%;
              left: 50%;
              margin: -50px 0px 0px -50px;
          }

      .dis-none {
              display: none;
          }
      .pd-5 {
        padding: 3px;
      }
      .toggle.btn {
        min-height:25px;
      }
      .toggle-group .btn {
        padding: 2px 12px;
      }
      .latest-remarks-list-view tr td {
        padding:3px !important;
      }
  </style>

@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb mb-3">
            <h2 class="page-heading">Voucher Coupons</h2>
              <div class="col-sm">
                <div class="pull-right">
                    <button type="button" class="btn btn-secondary btn-xs ml-3 mr-3" data-toggle="modal" data-target="#plateformModal"><i class="fa fa-plus"></i>Add Plateform</button>
                    <button type="button" class="btn btn-secondary btn-xs ml-3 mr-3" data-toggle="modal" data-target="#addvoucherModel"><i class="fa fa-plus"></i>Add Voucher</button>
                    
                 </div>
            </div>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row mb-3">
      <div class="col-sm-12">
        <form action="{{ route('list.voucher') }}" method="GET" class="form-inline align-items-start voucher-search" id="searchForm">
          <div class="row m-0 full-width" style="width: 100%;">
              <div class="col-md-2 col-sm-12">
              <select class="form-control select-multiple" name="plateform_id" id="plateform_id">
                  <option value="">Select Plate form</option>
                  @foreach($platform as $key => $plate)
                    <option value="{{ $key }}" @if(request('plateform_id') == $plate) selected @endif >{{ $plate }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2 col-sm-12">
                <select class="form-control select-multiple" name="email_add" id="email_add">
                  <option value="">Select Email</option>
                  @foreach($emails as $ekey => $emailid)
                    <option value="{{ $emailid }}" @if(request('email_add') == $emailid) selected @endif>{{ $ekey }}</option>
                  @endforeach
                </select>
                    @if($errors->has('email_add'))
                      <div class="form-control-plateform">{{$errors->first('email_add')}}</div>
                    @endif
              </div>

              <div class="col-md-2 col-sm-12">
                  <select class="form-control select-multiple" name="whatsapp_id" id="whatsapp_id">
                    <option value="">Select Number</option>
                    @foreach($whatsapp_configs as $key => $num)
                      <option value="{{ $key }}" @if(request('whatsapp_id') == $key) selected @endif >{{ $num }}</option>
                    @endforeach
                  </select>
                    @if($errors->has('whatsapp_id'))
                      <div class="form-control-plateform">{{$errors->first('whatsapp_id')}}</div>
                    @endif
              </div>
                
            <div class="col-md-1"><button type="submit" class="btn btn-image"><img src="/images/search.png" /></button></div>
          </div>
        </form>
      </div>
    </div>


     <div class="col-sm">
        <div class="table-responsive vendor-payments-list">
        <table class="table table-bordered"style="table-layout: fixed;">
        <tr>
          <th style="width:1%;">SR. No</th>
          {{-- <th style="width:2%";>User</th> --}}
          <th style="width:3%";>Platform</th>
          <th style="width:2%;">Email Address</th>
          <th style="width:2%;">whatsapp Number</th>
          <th style="width:2%;">remark</th>
          <th style="width:3%;">Action</th>
        </tr>
          @php
            $totalRateEstimate = 0;
            $totalCount = 0;
            $totalBalance = 0;
          @endphp
          @foreach ($voucher as $vou)
            <?php $totalCount++;?>
            <tr>
              <td>{{$totalCount}}</td>
              {{-- <td class="Website-task">
                @if(isset($vou->user)) {{  $task->user->name }} @endif
              </td> --}}
              <td>{{ $vou->plateform_name}}</td>
              <td class="Website-task">{{ str_limit($vou->from_address, 20, $end = '...') }}</td>
              <td>{{ $vou->number }} </td>
              <td>{{ $vou->remark }} </td>
              <td>
                <button type="button" data-toggle="tooltip" title="edit" data-id="{{$vou->id}}" class="btn btn-edit pd-5">
                    <i class="fa fa-edit" aria-hidden="true"></i>
                </button>
                <button type="button" data-id="{{ $vou->id }}"  title="Remark" class="btn btn-store-development-remark pd-5">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-xs  link-delete" title="Delete Record"  data-id="{{ $vou->id }}" >
                  <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-xs btn-store-code" title="Coupon Codes"  data-id="{{ $vou->id }}" >
                  <b>C</b>
                </button>
                {{-- <button type="button" class="btn btn-xs btn-store-code-list" title="List of Coupon Code"  data-id="{{ $vou->id }}" >
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </button> --}}
                <button type="button" class="btn btn-xs btn-store-code-order"  title="Orders" data-id="{{ $vou->id }}" >
                  <b>O</b>
                </button>
                {{-- <button type="button" class="btn btn-xs btn-store-code-order-list"  title="List of Coupon Code Order" data-id="{{ $vou->id }}" >
                  <i class="fa fa-info-circle" aria-hidden="true"></i>
                </button> --}}
                    
              </td>
            </tr>
          @endforeach
      </table>
      {{$voucher->links()}}
    </div>
    </div>


    <div id="paymentModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content" id="payment-content">
                
            </div>
        </div>
    </div>

    <div id="plateformModal" class="modal fade in" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Add Plateform</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
              </div>
                <form action="#" method="POST" id="plateform_form">
                    @csrf
                      <div class="modal-body">
                          <div class="form-group">
                              {!! Form::label('plateform_name', 'Name', ['class' => 'form-control-label']) !!}
                              {!! Form::text('plateform_name', null, ['class'=>'form-control  '.($errors->has('plateform_name')?'form-control-danger':(count($errors->all())>0?'form-control-success':'')),'required','rows'=>3]) !!}
                                  @if($errors->has('rplateform_name'))
                          <div class="form-control-feedback">{{$errors->first('plateform_name')}}</div>
                                      @endif
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-default save-plateform">Save</button>
                      </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <div id="addvoucherModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form action="" method="POST" id="addupdate" >
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <h4 class="modal-title">Add / Update Voucher</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                            <div class="form-group">
                                {!! Form::label('plateform_id', 'Plateform', ['class' => 'form-control-label']) !!}
                                <select class="form-control select-multiple" name="plateform_id" id="plateformadd_id" required style="width: 100%;">
                                  <option value="">Select Plateform Name</option>
                                  @foreach($platform as $key => $plat)
                                    <option value="{{ $key }}">{{ $plat }}</option>
                                  @endforeach
                                </select>
                                    @if($errors->has('plateform_id'))
                                      <div class="form-control-plateform">{{$errors->first('plateform_id')}}</div>
                                    @endif
                            </div>


                            <div class="form-group">
                              {!! Form::label('email_id', 'Email', ['class' => 'form-control-label']) !!}
                              <select class="form-control select-multiple" name="email_id" id="email_id" required style="width: 100%;">
                                <option value="">Select Email</option>
                                @foreach($emails as $ekey => $emailid)

                                  <option value="{{ $emailid }}">{{ $ekey }}</option>
                                @endforeach
                              </select>
                                  @if($errors->has('email_id'))
                                    <div class="form-control-plateform">{{$errors->first('email_id')}}</div>
                                  @endif
                            </div>

                            <div class="form-group">
                                {!! Form::label('whatsapp_config_id', 'Number', ['class' => 'form-control-label']) !!}
                                <select class="form-control select-multiple" name="whatsapp_config_id" id="whatsapp_config_id" required style="width: 100%;">
                                  <option value="">Select Number</option>
                                  @foreach($whatsapp_configs as $key => $num)
                                    <option value="{{ $key }}">{{ $num }}</option>
                                  @endforeach
                                </select>
                                  @if($errors->has('whatsapp_config_id'))
                                    <div class="form-control-plateform">{{$errors->first('whatsapp_config_id')}}</div>
                                  @endif
                            </div>
                            <div class="form-group">
                              {!! Form::label('password', 'Password', ['class' => 'form-control-label']) !!}
                              <input type="text" class="form-control" name="password" id="password" style="width: 100%;"/>
                                
                                @if($errors->has('password'))
                                  <div class="form-control-plateform">{{$errors->first('password')}}</div>
                                @endif
                          </div>
                        </div>
                    
                      <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-danger save-voucher">Submit</button>
                      </div>
                    </div>
                </form>
            </div>

        </div>

        <div id="remarkModel" class="modal fade in" role="dialog">
          <div class="modal-dialog">
  
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Add Plateform</h4>
                  <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                  <form action="#" method="POST" id="plateform_form">
                      @csrf
                      <input type="hidden" id="hidden-id">
                        <div class="modal-body">
                            <div class="form-group">
                                {!! Form::label('remark_name', 'Remark', ['class' => 'form-control-label']) !!}
                                {!! Form::text('remark_name', null, ['class'=>'form-control  '.($errors->has('remark_name')?'form-control-danger':(count($errors->all())>0?'form-control-success':'')),'required','rows'=>3]) !!}
                                    @if($errors->has('remark_name'))
                            <div class="form-control-feedback">{{$errors->first('remark_name')}}</div>
                                        @endif
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-default save-remark">Save</button>
                        </div>
                      </div>
                  </form>
              </div>
  
          </div>
      </div>
    </div>

    <div id="addVoucherCouponCodeModel" class="modal fade" role="dialog">
      <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
              <form action="" method="POST" id="addupdateCode" >
                  @csrf

                  <input type="hidden" name="voucher_coupons_id" id="voucher_coupons_id">
                  <div class="modal-header">
                      <h4 class="modal-title">Add Voucher Coupon Code</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                          <div class="form-group">
                              {!! Form::label('coupon_code', 'Coupon Code', ['class' => 'form-control-label']) !!}
                              <input type="text" class="form-control coupon_code" name="coupon_code" id="coupon_code" required style="width: 100%;">
                                  @if($errors->has('coupon_code'))
                                    <div class="form-control-plateform">{{$errors->first('coupon_code')}}</div>
                                  @endif
                          </div>
                          <div class="form-group">
                            {!! Form::label('valid_date', 'Valid Date', ['class' => 'form-control-label']) !!}
                            <input type="text" class="form-control valid_date" name="valid_date" id="valid_date" style="width: 100%;" required/>
                              
                              @if($errors->has('valid_date'))
                                <div class="form-control-plateform">{{$errors->first('valid_date')}}</div>
                              @endif
                        </div>
                        <div class="form-group">
                          {!! Form::label('code_remark', 'Remark', ['class' => 'form-control-label']) !!}
                          <input type="text" class="form-control datepicker" name="code_remark" id="code_remark" style="width: 100%;" required/>
                            
                            @if($errors->has('valid_date'))
                              <div class="form-control-plateform">{{$errors->first('code_remark')}}</div>
                            @endif
                      </div>
                      </div>
                  
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-default save-voucher-code">Submit</button>
                    </div>
                  </div>
              </form>
          </div>
      </div>

      <div id="addVoucherCouponCodeOrderModel" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
              <form action="" method="POST" id="addupdateCodeOrderForm" >
                  @csrf
                  <input type="hidden" name="voucher_coupons_id" class="voucher_coupons_id">
                  <div class="modal-header">
                      <h4 class="modal-title">Add Voucher Coupon Code Order</h4>
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      {!! Form::label('date_order_placed', 'Date Order Placed', ['class' => 'form-control-label']) !!}
                      <input type="text" class="form-control date_order_placed" name="date_order_placed" id="date_order_placed" style="width: 100%;" required/>
                        @if($errors->has('date_order_placed'))
                          <div class="form-control-plateform">{{$errors->first('date_order_placed')}}</div>
                        @endif
                    </div>
                  
                    <div class="form-group">
                        {!! Form::label('order_no', 'Order No', ['class' => 'form-control-label']) !!}
                        <input type="text" class="form-control" name="order_no" id="order_no" required style="width: 100%;">
                            @if($errors->has('order_no'))
                              <div class="form-control-plateform">{{$errors->first('order_no')}}</div>
                            @endif
                    </div>

                    <div class="form-group">
                      {!! Form::label('order_amount', 'Order Amount', ['class' => 'form-control-label']) !!}
                      <input type="text" class="form-control" name="order_amount" id="order_amount" required style="width: 100%;">
                          @if($errors->has('order_amount'))
                            <div class="form-control-plateform">{{$errors->first('order_amount')}}</div>
                          @endif
                    </div>

                    <div class="form-group">
                      {!! Form::label('discount', 'Discount', ['class' => 'form-control-label']) !!}
                      <input type="text" class="form-control" name="discount" id="discount" required style="width: 100%;">
                          @if($errors->has('discount'))
                            <div class="form-control-plateform">{{$errors->first('discount')}}</div>
                          @endif
                    </div>

                    <div class="form-group">
                      {!! Form::label('final_amount', 'Final Amount', ['class' => 'form-control-label']) !!}
                      <input type="text" class="form-control" name="final_amount" id="final_amount" required style="width: 100%;">
                          @if($errors->has('final_amount'))
                            <div class="form-control-plateform">{{$errors->first('final_amount')}}</div>
                          @endif
                    </div>

                    <div class="form-group">
                      {!! Form::label('refund_amount', 'Refund Amount', ['class' => 'form-control-label']) !!}
                      <input type="text" class="form-control" name="refund_amount" id="refund_amount" required style="width: 100%;">
                          @if($errors->has('refund_amount'))
                            <div class="form-control-plateform">{{$errors->first('refund_amount')}}</div>
                          @endif
                    </div>

                    <div class="form-group">
                      {!! Form::label('code_remark', 'Remark', ['class' => 'form-control-label']) !!}
                      <input type="text" class="form-control datepicker" name="code_remark" id="code_remark" style="width: 100%;" required/>
                        
                        @if($errors->has('valid_date'))
                          <div class="form-control-plateform">{{$errors->first('code_remark')}}</div>
                        @endif
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default save-voucher-code-order">Submit</button>
                  </div>
                </div>
                </form>
              </div>
          </div>
        </div>

        
        <div id="voucher-code-list-model" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-sm">
            <div class="modal-content">
              <table class="table">
                <thead class="thead-dark">
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">First</th>
                    <th scope="col">Last</th>
                    <th scope="col">Handle</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">1</th>
                    <td>Mark</td>
                    <td>Otto</td>
                    <td>@mdo</td>
                  </tr>
                  <tr>
                    <th scope="row">2</th>
                    <td>Jacob</td>
                    <td>Thornton</td>
                    <td>@fat</td>
                  </tr>
                  <tr>
                    <th scope="row">3</th>
                    <td>Larry</td>
                    <td>the Bird</td>
                    <td>@twitter</td>
                  </tr>
                </tbody>
              </table>
              
              <table class="table">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Coupon code</th>
                    <th scope="col">Valid Date</th>
                    <th scope="col">Remark</th>
                  </tr>
                </thead>
                <tbody>
                  
                </tbody>
              </table>
            </div>
          </div>
        </div>
      
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
  <script src="{{env('APP_URL')}}/js/bootstrap-datetimepicker.min.js"></script>
  <script type="text/javascript">
  
    $('.assign-to.select2').select2({
      width: "100%"
    });

    $(".valid_date").datetimepicker({
                format: 'YYYY-MM-DD'
    });
    $(".date_order_placed").datetimepicker({
                format: 'YYYY-MM-DD'
    });
    
    var uploadedDocumentMap = {}
    Dropzone.options.documentDropzone = {
      url: '{{ route("voucher.upload-documents") }}',
      maxFilesize: 20, // MB
      addRemoveLinks: true,
      headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
      },
      success: function (file, response) {
          $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
          uploadedDocumentMap[file.name] = response.name
      },
      removedfile: function (file) {
          file.previewElement.remove()
          var name = ''
          if (typeof file.file_name !== 'undefined') {
            name = file.file_name
          } else {
            name = uploadedDocumentMap[file.name]
          }
          $('form').find('input[name="document[]"][value="' + name + '"]').remove()
      },
      init: function () {

      }
  }
   

  $(document).on("click",".save-plateform",function(e){
    e.preventDefault();
    var $this = $(this);
    var formData = new FormData($this.closest("form")[0]);
    $.ajax({
      url: '{{route("voucher.plateform.create")}}',
      type: 'POST',
      headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        dataType:"json",
      data: $this.closest("form").serialize(),
      beforeSend: function() {
        $("#loading-image").show();
            }
    }).done(function (data) {
      $("#loading-image").hide();
      toastr["success"]("Document uploaded successfully");
      location.reload();
    }).fail(function (jqXHR, ajaxOptions, thrownError) {      
      toastr["error"](jqXHR.responseJSON.message);
      $("#loading-image").hide();
    });
  });

  $(document).on("click",".save-voucher",function(e){
    e.preventDefault();
    var $this = $(this);
    var formData = new FormData($this.closest("form")[0]);
    $.ajax({
      url: '{{route("voucher.store")}}',
      type: 'POST',
      headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        dataType:"json",
      data: $this.closest("form").serialize(),
      beforeSend: function() {
        $("#loading-image").show();
            }
    }).done(function (data) {
      $("#loading-image").hide();
      toastr["success"](data.message);
      location.reload();
    }).fail(function (jqXHR, ajaxOptions, thrownError) {      
      toastr["error"](jqXHR.responseJSON.message);
      $("#loading-image").hide();
    });
  });
  


  $(document).on("click",".btn-edit",function(e){
    e.preventDefault();
    var $this = $(this);
    let id = $this.data('id');
    var formData = new FormData($this.closest("form")[0]);
    $.ajax({
      url: '{{route("voucher.edit")}}',
      type: 'POST',
      headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        
      data: {
        id : id
      },
      beforeSend: function() {
        $("#loading-image").show();
            }
    }).done(function (response) {
      $("#loading-image").hide();
        form = $('#addupdate');
        
        $.each(response.data, function(key, v) {
          console.log(key);  
          if(key == 'platform_id'){
            $("#plateformadd_id").select2().val(v).trigger("change");
          }else if(key == 'email_address_id'){
            $("#email_id").select2().val(v).trigger("change");
          }else if(key == 'whatsapp_config_id'){
            $("#whatsapp_config_id").select2().val(v).trigger("change");
          }else if(key == 'password') {
            $("#password").val(v)
          }else if(key == 'id') {
            $("#id").val(v)
          }       

        });
      $('#addvoucherModel').modal('show');
      toastr["success"](response.message);
    }).fail(function (jqXHR, ajaxOptions, thrownError) {      
      toastr["error"](jqXHR.responseJSON.message);
      $("#loading-image").hide();
    });
  });
  $('.select-multiple').select2({width: '100%'});

  $(document).on("click",".btn-store-development-remark",function() {
      var $this = $(this);
      $("#remarkModel").modal("show");
       $("#hidden-id").val($this.data("id"));
  });
  $(document).on('click', '.save-remark', function(e) {
      e.preventDefault();
      var thiss = $(this);
      
      let id = $("#hidden-id").val();
      let remark = $("#remark_name").val();
      var type = 'post';
        $.ajax({
          url: '/vouchers-coupons/voucher/remark/'+id,
          type: type,
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        
          data: {
            id : id,
            remark : remark
          },
          beforeSend: function() {
            $("#loading-image").show();
          }
        }).done( function(response) {
          $("#remarkModel").modal("hide");
          toastr["success"](response.message);
        }).fail(function (jqXHR, ajaxOptions, thrownError) {      
          console.log(jqXHR.responseJSON.message);
          toastr["error"](jqXHR.responseJSON.message);
          $("#loading-image").hide();
        });
    });

    $(document).on("click",".link-delete",function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var $this = $(this);
        if(confirm("Are you sure you want to delete records ?")) {
          $.ajax({
            url: '/vouchers-coupons/voucher/delete',
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
              dataType:"json",
            data: { id : id},
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (data) {
            $("#loading-image").hide();
            toastr["success"]("Document deleted successfully");
            $this.closest("tr").remove();
          }).fail(function (jqXHR, ajaxOptions, thrownError) {
            toastr["error"]("Oops,something went wrong");
            $("#loading-image").hide();
          });
        }
      });
      $(document).on("click",".btn-store-code",function() {
          var $this = $(this);
          $("#addVoucherCouponCodeModel").modal("show");
          $("#voucher_coupons_id").val($this.data("id"));
      });
      $(document).on("click",".save-voucher-code",function(e){
        e.preventDefault();
        var $this = $(this);
        var formData = new FormData($this.closest("form")[0]);
        $.ajax({
          url: '{{route("voucher.code.create")}}',
          type: 'POST',
          headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType:"json",
            data: $this.closest("form").serialize(),
          beforeSend: function() {
            $("#loading-image").show();
                }
        }).done(function (data) {
          $("#loading-image").hide();
          toastr["success"](data.message);
        }).fail(function (response) {      
          toastr["error"](response.message);
          $("#loading-image").hide();
        });
      });

      $(document).on("click",".btn-store-code-order",function() {
          var $this = $(this);
          $("#addVoucherCouponCodeOrderModel").modal("show");
          $(".voucher_coupons_id").val($this.data("id"));
      });
      $(document).on("click",".save-voucher-code-order",function(e){
        e.preventDefault();
        var $this = $(this);
        var formData = new FormData($this.closest("form")[0]);
        $.ajax({
          url: '{{route("voucher.code.order.create")}}',
          type: 'POST',
          headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType:"json",
            data: $this.closest("form").serialize(),
          beforeSend: function() {
            $("#loading-image").show();
                }
        }).done(function (data) {
          $("#loading-image").hide();
          toastr["success"](data.message);
        }).fail(function (response) {      
          toastr["error"](response.message);
          $("#loading-image").hide();
        });
      });


    $(document).on('click', '.expand-row', function() {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
      }
    });
    $('#rejectVoucherModal').on('show.bs.modal', function (event) {
        var modal = $(this)
        var button = $(event.relatedTarget)
        var voucher = button.data('voucher')
        var url = "{{ url('voucher') }}/" + voucher.id + '/reject';
        modal.find('form').attr('action', url);
    });
    
    $(document).on("click",".btn-store-code-list",function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $(this).data("id");
        $.ajax({
          url: '{{route("voucher.code.list")}}',
          type: 'GET',
          headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
          dataType:"json",
          data :{
            voucher_coupons_id : id
          },
          beforeSend: function() {
            $("#loading-image").show();
                }
        }).done(function (response) {
          $("#loading-image").hide();
          var html = "";
          $.each(response.data,function(k,v){
            html += "<tr>";
              html += "<td>"+v.id+"</td>";
              html += "<td>"+v.coupon_code+"</td>";
              html += "<td><div class='form-row'>"+v.valid_date+"</div></td>";
              html += "<td><div class='form-row'>"+v.remark+"</div></td>";
              html += '<td><a class="btn-secondary code-delete" data-type="code" data-id='+v.id+'><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
            html += "</tr>";
          });
          $(".voucher-code-list").html(html);
          $("#voucher-code-list-model").modal("show");
          toastr["error"](response.message);
        }).fail(function (response, ajaxOptions, thrownError) {
          toastr["error"](response.message);
          $("#loading-image").hide();
        });
      });













    


    

    $(document).on("click",".btn-file-list",function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $(this).data("payment-receipt-id");
        $.ajax({
          url: '/voucher/'+id+'/list-documents',
          type: 'GET',
          headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dataType:"json",
          beforeSend: function() {
            $("#loading-image").show();
                }
        }).done(function (response) {
          $("#loading-image").hide();
          var html = "";
          $.each(response.data,function(k,v){
            html += "<tr>";
              html += "<td>"+v.id+"</td>";
              html += "<td>"+v.url+"</td>";
              html += "<td><div class='form-row'>"+v.user_list+"</div></td>";
              html += '<td><a class="btn-secondary" href="'+v.url+'" data-site-id="'+v.site_id+'" target="__blank"><i class="fa fa-download" aria-hidden="true"></i></a>&nbsp;<a class="btn-secondary link-delete-document" data-payment-receipt-id="'+v.payment_receipt_id+'" data-id='+v.id+' href="_blank"><i class="fa fa-trash" aria-hidden="true"></i></a></td>';
            html += "</tr>";
          });
          $(".display-document-list").html(html);
          $("#file-upload-area-list").modal("show");
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
          toastr["error"]("Oops,something went wrong");
          $("#loading-image").hide();
        });
      });

    
  </script>
@endsection
