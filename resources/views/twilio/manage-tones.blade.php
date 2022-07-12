@extends('layouts.app')
@section('favicon' , 'productstats.png')
@section('title', 'Twilio Message Tones')
@section('content')
    <?php $base_url = URL::to('/');?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Twilio Manage Tones</h2>
        </div>
    </div>
    
    <div class="row pl-5 pr-5">
        <div class="col-md-12">
            <div class="col-md-1 p-2  border-top border-bottom border-right border-left"><strong>#</strong></div>
            <div class="col-md-1 p-2 border-top border-bottom border-right border-left"><strong>Store Website</strong></div>
            <div class="col-md-3 p-2 border-top border-bottom border-right border-left"><strong>End work ring</strong></div>
            <div class="col-md-3 p-2 border-top border-bottom border-right border-left"><strong>Intro ring</strong></div>
            <div class="col-md-3 p-2  border-top border-bottom border-right border-left"><strong>Busy ring</strong></div>
            <div class="col-md-1 p-2 border-top border-bottom border-right border-left"><strong>Action</strong></div>
        </div> 
           
           
			    @foreach($twilioMessageTones as $i=>$twilioMessageTone)
                    <div class="col-md-12 ">
						{{ Form::open(array('url'=>url('twilio/save-message-tone'), 'files'=>true, 'class'=>'ajax-submit'))}}
							<div class="col-md-1 p-2  border-top border-bottom border-right border-left" style="height: 46px !important;">{{ $i+1 }} {{Form::hidden('store_website_id', $twilioMessageTone['websiteId']) }}</div>

							<div class="col-md-1 Website-task p-2  border-top border-bottom border-right border-left" style="height: 46px !important;">{{ $twilioMessageTone->website }}</div>

							<div class="col-md-3 p-2  border-top border-bottom border-right border-left"><input type="file" name="end_work_ring">
								@if($twilioMessageTone->end_work_ring != null)
									<div class="d-flex">
										<audio src="{{url('twilio/'.$twilioMessageTone->end_work_ring)}}" controls="" preload="metadata">
										</audio>
									</div>
								@endif
							</div>
							<div class="col-md-3 p-2  border-top border-bottom border-right border-left"><input type="file" name="intro_ring">
								@if($twilioMessageTone->intro_ring != null)
									<div class="d-flex">
										<audio src="{{url('twilio/'.$twilioMessageTone->intro_ring)}}" controls="" preload="metadata">
										</audio>
									</div>
								@endif
							</div>
							<div class="col-md-3 p-2  border-top border-bottom border-right border-left"><input type="file" name="busy_ring">
								@if($twilioMessageTone->busy_ring != null)
									<div class="d-flex ">
										<audio src="{{url('twilio/'.$twilioMessageTone->busy_ring)}}" controls="" preload="metadata">
										</audio>
									</div>
								@endif
							</div>
							<div class="col-md-1 p-2  border-top border-bottom border-right border-left"><button class="btn btn-secondary" type="submit" >Save</button></div>
						</form>
					</div> 
                @endforeach
        </div>
    </div>
@endsection

@section('scripts')
    <script>
    		$('.ajax-submit').on('submit', function(e) { 
			e.preventDefault(); 
			$.ajax({
                type: $(this).attr('method'),
				url: $(this).attr('action'),
				data: new FormData(this),
				processData: false,
				contentType: false,
				success: function(data) { console.log(data);
					if(data.statusCode == 400) { 
					$.each(data.errors, function( index, value ) {
						toastr["error"](value);
					});
						
					} else {
						toastr["success"](data.message);
						/*setTimeout(function(){
                          location.reload();
                        }, 1000);*/
 
					}
				},
				done:function(data) {
					console.log('success '+data);
				}
            });
		});
    </script>
@endsection
