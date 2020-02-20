@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Message List | Chatbot')

@section('content')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/css/dialog-node-editor.css">

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
    <div class="table-responsive-lg">
      <table class="table">
        <thead>
          <tr>
          	<th width="2%">Customer #</th>
            <th width="15%">User input</th>
            <th width="15%">Bot Replied</th>
            <th width="30%">Images</th>
            <th width="5%">Action</th>
          </tr>
        </thead>
        <tbody>
    	  <?php if(!empty($pendingApprovalMsg)) { ?>
    	  		<?php foreach($pendingApprovalMsg as $pam) { ?>
	              <tr>
	                <td>{{ $pam->customer_id }}</td>
	                <td>{{ $pam->question }}</td>
	                <td>{{ $pam->message }}</td>
	                <td>
                    @if($pam->hasMedia(config('constants.media_tags')))
                      @foreach($pam->getMedia(config('constants.media_tags')) as $medias)
                        <img width="75px" heigh="75px" src="{{ $medias->getUrl() }}">
                      @endforeach
                    @endif
                  </td>
	                <td>
                    <a href="javascript:;" class="approve-message" data-id="{{ $pam->chat_id }}">
                      <img width="15px" height="15px" src="/images/completed-green.png"> 
                    </a>
                  </td>
	              </tr>
	          	<?php } ?>
          <?php } ?>
        </tbody>
      </table>
     <?php echo $pendingApprovalMsg->links(); ?>
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
</script>
@endsection
