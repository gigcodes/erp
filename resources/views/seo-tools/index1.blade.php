@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection
@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Seo Details</h2>
                    <button type="button" class="btn btn-secondary float-right mr-3" data-toggle="modal"
                            data-target="#addToolModal">
                        Add Tool
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Model Add Seo tool START -->
    <div id="addToolModal" class="modal fade in" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content" style="padding: 0 10px 10px">
                <div class="modal-header">
                    <h3>Add new tool</h3>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <form name="add-seo-tool" style="padding:10px;"
                      action="{{ route('save.seo-tool') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" name="tool" placeholder="Tool" value="" required="">
                    </div>
                    <div class="form-group mt-3">
                        <input type="text" class="form-control" name="api_key" placeholder="Api Key" value="" required="">
                    </div>
                    <button type="submit" class="btn btn-secondary">Add Tool</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Model Add Seo tool END -->


    @if(Session::has('message'))
        <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
    @endif
	<div class="row">
		<div class="form-group col-md-12">
			<div class="col-md-4 ">
				<label for="with_archived">Select Website</label>
				{{ Form::select('search', $websites, null, array('class'=>'search select2', 'placeholder'=>'Seletc Website', 'id'=>'search')) }}
			</div>
		</div>
	</div>
	
	<ul class="nav nav-tabs">
	@foreach($tools as $key=>$tool)
	  <li class="@if($key == 0)active @endif" role="presentation">
		<a data-toggle="tab" href="#tool_{{$tool['id']}}">{{$tool['tool']}}</a>
	  </li>
	@endforeach
	</ul>
	<div class="tab-content" id="myTabContent">
	</div>
		
	
	
			
		
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
		$('.select2').select2();
        $('.search').change(function(){ 
            var websiteId = $(this).val();
            var website = $('.search').select2('data'); 
			 $.ajax({
                url : "{{ route('fetch-seo-details') }}",
                type : "POST",
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                data : {
                    websiteId : websiteId,
                    website : website[0].text
                },
                success : function (data){ console.log(data);console.log(data.status_code);
					if(data.status_code == 200) {
						$('#myTabContent').html(data.response);
					}               
                },
                error : function (response){

                }
            });
        });
 $(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
  });
    </script>
@endsection