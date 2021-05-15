@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Size | Database')

@section('content')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
@endsection

<div class="row">
	<div class="col-lg-12 margin-tb">
	    <h2 class="page-heading">Size | Tables</h2>
	</div>
</div>
<div class="row">
    <div class="col-lg-12 margin-tb" style="margin-bottom: 10px;">
        <form id="message-fiter-handler" action="" method="GET">
          <div class="pull-left">
            <div class="form-group">
              <input type="text" name="table_name">
            </div>
          </div>  
          <div class="pull-left">
                <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-filter-report">
            <img src="/images/search.png" style="cursor: default;">
          </button>
          <a style="display: inline-block;width: 10%" class="btn btn-sm btn-image" href="?">
            <img src="/images/clear-filters.png" style="cursor: default;">
          </a>
          </div>
    </form>
    </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="table-responsive-lg" id="page-view-result">
      @include("database.partial.list-table")
    </div>
  </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@endsection