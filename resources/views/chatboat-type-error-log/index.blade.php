@extends('layouts.app')

@section('title', 'Twillio Missing Keywrods')

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
	.border-red {
		border: 1px solid red;
	}
</style>
@endsection

@section('content')
  <div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
  </div>
  <div class="row m-0">
    <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">Twillio Missing Keywrods
      {!! Session::has('msg') ? Session::get("msg") : '' !!}
      <div class="pull-right">
        <button type="button" class="btn btn-image pr-0" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
      </div>
      </h2>
  </div>

  <div class="col-lg-12 margin-tb">
    <div class="tab-content ">
      <table id="magento_list_tbl_895" class="table table-bordered table-hover">
        <thead>
          <th >ID</th>
          <th>Missing word</th>
          <th>Store Website</th>
          <th>Call sid</th>
          <th>Phone Number</th>
          
        </thead>
        <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
          
          @foreach($data as $item)
            <tr id="tr_{{$item->chatId}}">
              <td>{{ $item->chatId }}</td>
              <td>{{$item->type_error}}</td>
              <td>{{$item->website}} </td>
              <td>{{$item->call_sid}}</td>
              <td>{{$item->phone_number}}</td>
              {{-- <td>
                {!! Form::open(['method' => 'GET','route' => ['chatbot.type.error.log'],'style'=>'display:inline']) !!}
                <input type="hidden" value="{{$item->chatId}}" name="id">
                <button type="submit" class="btn btn-image"><img src="/images/delete.png"/></button>
                {!! Form::close() !!}
                <a href="javascript:;" class="btn btn-image get-details" data-id="{{$item->chatId}}"><img src="/images/view.png" style="cursor: nwse-resize;"></a>
            </td> --}}
            </tr>
          @endforeach
          
        </tbody>
      </table>
      {!! $data->render() !!}
    </div>   
   </div>
</div>
<div class="row">
  <div class="col-md-12">
      <div class="alert alert-success" id="alert-msg" style="display: none;">
          <p></p>
      </div>
  </div>
</div>
<div class="col-md-12 margin-tb" id="page-view-result">

</div>
@endsection

@section('scripts')
<script type="text/javascript">
 
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
  });

	}

</script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript">
  page.init({
      bodyView : $("#common-page-layout"),
      baseUrl : "<?php echo url("/"); ?>"
  });
</script>

@endsection
