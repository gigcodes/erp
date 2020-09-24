@extends('layouts.app')

@section('title', 'Erp Leads History')

@section("styles")

  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  
@section('content')
<div class="row">
  <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Erp Leads History</h2>

  </div>
  <div class="col-md-12">

<?php $base_url = URL::to('/');?>
  <div class="pull-left cls_filter_box">
                <form class="form-inline" action="{{ route('leads.erpLeadsHistory') }}" method="GET">
                
                @csrf
                                   
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Customer</label>
                        <!-- <input placeholder="Customer" type="text" name="customer" value="" class="form-control-sm cls_commu_his form-control input-size"> -->
                        <input type="text" class="form-control-sm cls_commu_his form-control field_search lead_customer input-size" name="lead_customer" value="{{old('lead_customer')}}" placeholder="Customer" />

                    </div>
  
                    <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                    <label for="with_archived">Product</label>
                       <!-- <input placeholder="Brand Segment" type="text" name="brand_segment" value="" class="form-control-sm cls_commu_his form-control input-size"> -->
                       <input type="text" class="form-control-sm cls_commu_his form-control input-size field_search brand_segment" name="product_name" value="{{old('product_name')}}" placeholder="Product Name"/>
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Lead Status</label>
                        <select class="form-control lead_status multi_lead_status" name="lead_status"  style="width: 150px; border-radius: 2px;">
                          <option value="">Status</option>
                          @foreach($erpLeadStatus as $status)
                            <option value="{{$status['id']}}" {{($status['id'] == old('lead_status'))?'selected':'' }} >{{$status['name']}}</option>
                          @endforeach
                        </select>
                    </div>
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_updated_by">Date</label>
                        <!-- <input placeholder="Color" type="text" name="color" value="" class="form-control-sm cls_commu_his form-control input-size"> -->
                        <input type="date" class="form-control-sm cls_commu_his form-control input-size field_search lead_color" name="created_at" value="{{old('created_at')}}">
                    </div>
                    <!-- <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image" id="btnFileterErpLeads"><img src="<?php //echo $base_url;?>/images/filter.png"/></button> -->
                    <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image" id="btnFileterErpLeads"><img src="<?php echo $base_url;?>/images/filter.png"/></button>
                </form>
                
            </div>

            <?php /*<div class="col-lg-12 margin-tb" style="    margin-left: 23px;">
            <div class="pull-right mt-3" style="margin-bottom: 12px ">
                <!-- <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#emailToAllModal">Bulk Email</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#conferenceModal">Conference Call</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createVendorCategorytModal">Create Category</button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#vendorCreateModal">+</button> -->
                <label style="margin-right: 13px;">
                    <input type="checkbox" class="all_customer_check"> Select This Page
                  </label>
                  <label style="margin-right: 13px;">
                    <input type="checkbox" class="all_page_check"> Select All Page
                  </label>
                <a class="btn btn-secondary create_broadcast" href="javascript:;">Create Broadcast</a>
                <a href="javascript:;" class="btn btn-image px-1 images_attach"><img src="/images/attach.png"></a>
            </div>
        </div> */?>
        <div></div>
        <br>
        <div class="infinite-scroll">
    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="vendor-table">
            <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="5%">Lead Status</th>
                <th width="10%">Customer Name</th>
                <th width="10%">Product Name</th> 
                <th width="5%">Date</th>
            </tr>
            </thead>

            <tbody id="vendor-body">

              @foreach ($sourceData as $source)
                <tr>
                  <!-- <td>{{$source['id']}}</td> -->
                  <td class="tblcell">
                    
                    <div class="checkbox"><label class="checkbox-inline"><input name="customer_message[]" class="customer_message" type="checkbox" value="'+row.customer_id+'">{{$source['id']}}</label></div>
                  </td>
                  <td class="tblcell"> <div class="checkbox"><label class="checkbox-inline ew"><input name="customer_message[]" class="customer_message" type="checkbox" value="'+row.customer_id+'" style="display: none">{{$source['lead_status']}}</label></div></td>
                  <td class="tblcell">
                  <div class="checkbox"><label class="checkbox-inline ew"><input name="customer_message[]" class="customer_message" type="checkbox" value="'+row.customer_id+'" style="display: none"><a href="/customer/' + data.customer_id + '" target="_blank">{{$source['customer_name']}}</a></label></div></td>
                  <td class="tblcell"><div class="checkbox"><label class="checkbox-inline ew"><input name="customer_message[]" class="customer_message" type="checkbox" value="'+row.customer_id+'" style="display: none">{{$source['product_name']}}</label></div></td>
                  <td class="tblcell"><div class="checkbox"><label class="checkbox-inline ew"><input name="customer_message[]" class="customer_message" type="checkbox" value="'+row.customer_id+'" style="display: none">{{$source['created_at']}}</label></div></td>
                </tr>
              @endforeach

            </tbody>
        </table>
    </div>
    {{ $sourceData->appends(Request::except('page'))->links() }}

    </div>
   
  </div>
</div>

<div id="erp-leads" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">

      </div>
    </div>
  </div>
</div>

<div id="create_broadcast" class="modal fade" role="dialog">
  <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Message to Customers</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form enctype="multipart/form-data" id="send_message" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="form-group">
                      <strong> Selected Product :</strong>
                      <select name="selected_product[]" class="ddl-select-product form-control" multiple="multiple"></select>
                      <strong> Attach Image :</strong>
                      <div class='input-group date' id='schedule-datetime'>
                        <input type='file' class="form-control" name="image" id="image" value=""/>
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-file"></span>
                        </span>
                      </div>

                      <strong>Schedule Date:</strong>
                      <div class='input-group date' id='schedule-datetime'>
                        <input type='text' class="form-control" name="sending_time" id="sending_time_field" value="{{ date('Y-m-d H:i') }}" required />
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>
                    </div>
                    <div class="form-group">
                        <strong>Message</strong>
                        <textarea name="message" id="message_to_all_field" rows="8" cols="80" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Send Message</button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

@section('scripts')
  <script src="//cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
  <script src="//cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
  <script src="https://cdn.datatables.net/scroller/2.0.2/js/dataTables.scroller.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script type="text/javascript">

  </script>
@endsection
