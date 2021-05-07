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
<script type="text/javascript">


  var getResults = function(href) {
    $.ajax({
        type: 'GET',
        url: href,
        beforeSend : function() {
          $("#loading-image").show();
        },
        dataType:"json"
      }).done(function(response) {
        $("#loading-image").hide();
        if(response.code == 200) {
          var removePage = response.page;
           if(removePage > 0) {
              var pageList = $("#page-view-result").find(".page-template-"+removePage);
              pageList.nextAll().remove();
              pageList.remove();
           }
           if(removePage > 1) {
             $("#page-view-result").find(".pagination").first().remove();
           }
          $("#page-view-result").append(response.tpl);
        }
      }).fail(function(response) {
        $("#loading-image").hide();
        console.log("Sorry, something went wrong");
      });
  };

  $("#page-view-result").on("click",".page-link",function(e) {
      e.preventDefault();

        var activePage = $(this).closest(".pagination").find(".active").text();
        var clickedPage = $(this).text();
        if(clickedPage == "‹" || clickedPage < activePage) {
            $('html, body').animate({scrollTop: ($(window).scrollTop() - 50) + "px"}, 200);
            getResults($(this).attr("href"));
        }else{
            getResults($(this).attr("href"));
        }

    });

  $(window).scroll(function() {
      if($(window).scrollTop() > ($(document).height() - $(window).height() - 10)) {
          $("#page-view-result").find(".pagination").find(".active").next().find("a").click();
      }
  });

  	function cb(start, end) {
    	$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    	$('#custom').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
  	}

  	var start = moment().subtract(29, 'days');
	var end = moment();

	$('#reportrange').daterangepicker({
	        startDate: start,
	        endDate: end,
	        ranges: {
	         'Today': [moment(), moment()],
	         'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
	         'Last 7 Days': [moment().subtract(6, 'days'), moment()],
	         'Last 30 Days': [moment().subtract(29, 'days'), moment()],
	         'This Month': [moment().startOf('month'), moment().endOf('month')],
	         'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
	     }
	}, cb);

	cb(start, end);


</script>
@endsection