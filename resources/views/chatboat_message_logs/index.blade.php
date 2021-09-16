@extends('layouts.app')

@section('title', 'Log List Magento')

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
  input {
    width: 100px;
  }
  .btn-primary, .btn-primary:hover{
    background: #fff;
    color:#757575;
    border: 1px solid #ddd;
  }
  .select2-container{
    width: 100% !important;
  }
  .select2-container--default .select2-selection--multiple{
    border: 1px solid #ddd !important;
  }
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 5px;
  }
 label{
   margin-top:5px;
   margin-bottom:4px;
   margin-left:3px;
 }
.export_btn {
	margin: 27px 0 0 0;
}
@media(max-width:767px){
	div#Export_popup .col-xs-12{padding:0;}
	.export_btn{margin:10px 0 0 0;}
}
    .ac-btns button{
      height: 20px;
        width: auto;
    }
</style>
@endsection

@section('content')
  <div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
  </div>
  <div class="row m-0">
    <div class="col-lg-12 margin-tb p-0">
      <h2 class="page-heading">Chat Boat Log ({{ $total_count }})
      <div class="pull-right">
        <button type="button" class="btn btn-image pr-0" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
      </div>
      </h2>
  </div>
  </div>

  <div class="col-md-12 pl-3 pr-3">
    <div class="mb-3">

     <div class="row m-0">
        <div class="table-responsive">
          <table id="magento_list_tbl_895" class="table table-bordered table-hover" style="table-layout: fixed">
            <thead>
              <th style="width:5%">Product ID</th>
              <th style="width:5%">Model</th>
              <th style="width:5%">Model Id</th>
              <th style="width:6%">Chat Message Id</th>
              <th style="width:6%">Message</th>
              <th style="width:5%">Status</th>
              <th style="width:5%">Last Updated</th>
              <th style="width:8%">Action</th>
            </thead>
            <tbody class="infinite-scroll-pending-inner">
              @foreach($logListMagentos as $item)
          <tr>
                  <td>
                    <a class="show-product-information" data-id="{{ $item->id }}" href="/products/{{ $item->id }}" target="__blank">{{ $item->id }}</a>
                  </td>
                  <td> {{$item->model}} </td>
                  <td> {{$item->model_id}} </td>
                  <td> {{$item->chat_message_id}} </td>
                  <td class="expand-row-msg" data-name="message" data-id="{{$item->id}}">
                    <span class="show-short-message-{{$item->id}}">{{ str_limit($item->message, 6, '...')}}</span>
                    <span style="word-break:break-all;" class="show-full-message-{{$item->id}} hidden">{{$item->message}}</span>
                  </td>
                  <td> {{$item->status}} </td>
                  <td>
                    @if(isset($item->updated_at))
                      {{ date('M d, Y',strtotime($item->updated_at))}}
                    @endif
                  </td>
                  
                  <td style="padding: 1px 7px">
                    <button class="btn btn-xs btn-none-border chatbot-log-list" data-id="{{$item->id}}"><i class="fa fa-eye"></i></button>
                  </td>
                </tr>
              @endforeach()
            </tbody>
          </table>


        </div>
        
     </div>
     <div id="show-log-list-chat-error" class="modal fade" role="dialog" style="margin: 150px;">
      <div class="modal-dialog modal-lg" style="margin: 0px;">
        <div class="modal-content" style="width: 1500px">
          <div class="modal-header">
            <h4 class="modal-title">Log message</h4>
          </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover" style="table-layout:fixed;">
                  <thead>
                    <th>ID</th>
                    <th>Request</th>
                    <th>Response</th>
                    <th>Status</th>
                    <th>Created at</th>
                  </thead>
                  <tbody class="show-log-list-chat-error-data">

                  </tbody>
                </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            </div>
        </div>
      </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
  $(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  });

  $(document).on("click",".chatbot-log-list",function(e) {
      var id = $(this).data("id");
      $.ajax({
        url: '/chatbot-message-log/' + id + '/history',
        type: 'GET',
        beforeSend: function() {
          $("#loading-image").show();
        }
      }).done(function(response) {
        $("#loading-image").hide();
        $(".show-log-list-chat-error-data").html(response);
        $("#show-log-list-chat-error").modal("show");
      }).fail(function(jqXHR, ajaxOptions, thrownError) {
        toastr["error"]("Oops,something went wrong");
        $("#loading-image").hide();
      });
  });

</script>
@if (Session::has('errors'))
  <script>
  toastr["error"]("{{ $errors->first() }}", "Message")
</script>
@endif
@if (Session::has('success'))
  <script>
  toastr["success"]("{{Session::get('success')}}", "Message")
</script>
@endif

@endsection
