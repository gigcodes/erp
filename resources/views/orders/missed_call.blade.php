@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Missed Call</h2>

    </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{ $message }}</p>
</div>
@endif

<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th style="width: 20%">Mobile Number</th>
            <th style="width: 25%">Message</th>
            <th style="width: 25%">Call Recording</th>
            <th style="width: 20%">Call Time</th>
            <th class="text-right" style="width: 10%">Action</th>
        </tr>
        @foreach ($callBusyMessages['data'] as $key => $callBusyMessage)
        <tr class="">
            <td>
              @if(isset($callBusyMessage['customer_name']))
                {{ $callBusyMessage['customer_name'] }}
              @else
                {{ $callBusyMessage['twilio_call_sid'] }}
              @endif
            </td>
            <td>{{ $callBusyMessage['message'] }}</td>
            <td>
				<audio src="{{$callBusyMessage['recording_url']}}" controls preload="metadata">
				  <p>Alas, your browser doesn't support html5 audio.</p>
				</audio>
				<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-http-status" title="Http Status" data-toggle="modal" data-target="#show-recording-text{{$key}}" data-request="N/A" data-response="N/A">
					<i class="fa fa-headphones"></i>
				</button>
				<div id="show-recording-text{{$key}}" class="modal fade" role="dialog" >
					<div class="modal-dialog" style="width:100%;max-width:96%">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">Audio Text</h4>
							</div>
							<div class="modal-body">
								{{ $callBusyMessage['audio_text'] }}
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
				
			</td>
            <td>{{ $callBusyMessage['created_at'] }}</td>

            <td>
                @if(isset($callBusyMessage['customerid']))
                <a class="btn btn-image" href="{{ route('customer.show',$callBusyMessage['customerid']) }}"><img src="/images/view.png" /></a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>

 <script type="text/javascript">

jQuery(document).ready(function( $ ) {
  $('audio').on("play", function (me) {
    $('audio').each(function (i,e) {
      if (e !== me.currentTarget) {
        this.pause();
      }
    });
  });
})

   </script>

@endsection
