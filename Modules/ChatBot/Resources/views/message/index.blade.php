@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Message List | Chatbot')

@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/css/dialog-node-editor.css">
<style type="text/css">
  .panel-img-shorts{
      width:100px;
      height:100px;
      display: inline-block;
  }
  .panel-img-shorts .close{
      display:block;
      float:right;
      width:15px;
      height:15px;
      background:url(https://web.archive.org/web/20110126035650/http://digitalsbykobke.com/images/close.png) no-repeat center center;
  }
</style>
<div class="row">
	<div class="col-lg-12 margin-tb">
	    <h2 class="page-heading">Message List | Chatbot</h2>
	</div>
</div>

<div class="row">
    <div class="col-lg-12 margin-tb" style="margin-bottom: 10px;">
        <div class="pull-left">
          <div class="form-inline">
              <form method="get">
                  <?php echo Form::text("search",request("search",null),["class" => "form-control", "placeholder" => "Enter input here.."]); ?>      
                  <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image">
                      <img src="/images/search.png" style="cursor: default;">
                  </button>
              </form>
          </div>
        </div>
    </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="table-responsive-lg" id="page-view-result">
      @include("chatbot::message.partial.list")
    </div>
  </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
  50% 50% no-repeat;display:none;">
</div>
<script src="/js/bootstrap-toggle.min.js"></script>
<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript">
  $(document).on("click",".approve-message",function() {
      var $this = $(this);
      $.ajax({
          type: 'POST',
          url: "/chatbot/messages/approve",
          beforeSend : function() {
            $("#loading-image").show();
          },
          data: {
            _token: "{{ csrf_token() }}",
            id: $this.data("id"),
          },
          dataType:"json"
        }).done(function(response) {
          $("#loading-image").hide();
          if(response.code == 200) {
            toastr['success'](response.message, 'success');
          }
        }).fail(function(response) {
          $("#loading-image").hide();
          console.log("Sorry, something went wrong");
        }); 
  });
 
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

  $(document).on("click",".delete-images",function() {

     var tr = $(this).closest("tr");
     var checkedImages = tr.find(".close:checkbox:checked").closest(".panel-img-shorts");
     var form = tr.find('.remove-images-form');
     $.ajax({
        type: 'POST',
        url: form.attr("action"),
        data: form.serialize(),
        beforeSend : function() {
          $("#loading-image").show();
        },
        dataType:"json"
      }).done(function(response) {
        $("#loading-image").hide();
        if(response.code == 200) {
            $.each(checkedImages, function(k, e){
                $(e).remove();
            });
            toastr['success'](response.message, 'success');
        }
      }).fail(function(response) {
        $("#loading-image").hide();
        console.log("Sorry, something went wrong");
      });
  });

</script>
@endsection
