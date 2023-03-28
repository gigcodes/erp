@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Database | Query Process List')

@section('content')

<div class="row">
	<div class="col-lg-12 margin-tb" id="process_count">
	    <h2 class="page-heading">Database | Query Process List</h2>
	</div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="row">
            <div class="col-md-8 pl-5">
                <h4 class="header-title mb-3">
                    Query Process List <a href="javascript:;" id="refresh-process-list"><span class="glyphicon glyphicon-refresh"></span></a>
                </h4>
            </div>
            <div class="col-md-4 pr-5">
                <form class="form-inline pull-right">
                    <!-- <div class="form-group mr-2">
                        <input type="text" class="form-control" id="databaseName" placeholder="Enter database name">
                    </div> -->
                    <div id="progress" style="display: none;">
                    <img src="/images/loading_new.gif" style="cursor: pointer; width: 30px;">
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="dbExport()">Export Database</button>
                </form>

            </div>
        </div>

      <div class="card-box">
        <div class="table-responsive table-process-list-disp">
        </div>
      </div>
  </div>
</div>
@include("database.partial.template")
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
</div>
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript">

  var showProcessList = function() {
      $.ajax({
          type: 'GET',
          url: "<?php echo route('database.process.list'); ?>",
          dataType:"json"
        }).done(function(response) {
          if(response.code == 200) {
            var count = response.records.length;
            var tpl = $.templates("#template-list-state-process-list");
            var tplHtml       = tpl.render(response);
                $(".table-process-list-disp").html(tplHtml);
                $("#process_count h2").append(" ("+count+")");
          }
        }).fail(function(response) {
          console.log("Sorry, something went wrong");
        });
  };

  showProcessList();

  $(document).on("click","#refresh-process-list",function() {
      showProcessList();
  });

  $(document).on("click",".kill-process",function(){
        var $this = $(this);
        $.ajax({
          type: 'GET',
          url: "<?php echo route('database.process.kill'); ?>",
          data : {id : $this.data("id")},
          dataType:"json"
        }).done(function(response) {
          if(response.code == 200) {
            showProcessList();
          }
        }).fail(function(response) {
          console.log("Sorry, something went wrong");
        });
  });

  function dbExport() {
      // let dbName = $('#databaseName').val();
      // if(dbName === '') {
      //     toastr['error']('Please enter a valid database name!', 'error');
      //     return false;
      // }

      if (confirm("Are you sure you want to run this command?")) {
          $('#progress').show();
          $.ajax({
              url: "{{ route('database.export') }}",
              type: "post",
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data: {
                 // db_name: dbName
              }
          }).done(function (response) {
              $('#progress').hide();
              if (response.code == '200') {
                  window.open(response.data, "_blank");
                  toastr['success'](response.message, 'success');
              } else {
                  toastr['error'](response.message, 'error');
              }
          }).fail(function (errObj) {
              toastr['error'](errObj.message, 'error');
          });
      } else {
          return false;
      }
  }

</script>
@endsection