@extends('layouts.app')

@section('styles')
@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
    .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
@endsection
@endsection

@section('content')
<div class="row">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">Broadcast List</h2>
		<div class="pull-left">
			<form action="{{ route('document.index') }}" method="GET">
				<div class="form-group">
					<div class="row">
						<div class="col-md-8">
							<input name="term" type="text" class="form-control"
							value="{{ isset($term) ? $term : '' }}"
							placeholder="user,department,filename">
						</div>

						<div class="col-md-1">
							<button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="pull-right">
			</div>
		</div>
	</div>
	 @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Sr. No</th>
                <th>DND</th>
                <th>Status</th>
                <th>Manual Approval</th>
                <th>Last Broadcast ID / D.Y.N</th>
                <th>Phone No. Assign WhatsApp</th>
               <th>Remarks</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th>
                	<select class="form-control">
	                	<option>Asked Price</option>
	                	<option>Communication Done Removed</option>
	                	<option>Due to not delivered</option>
	                	<option>Manual Reject</option>
                	</select>
            	</th>
                <th>
                	<select class="form-control">
                		<option>Yes</option>
                		<option>No</option>
                	</select>
                </th>
                <th></th>
                <th></th>
               <th></th>
            </tr>
            </thead>

            <tbody>
           		 @include('marketing.broadcasts.partials.data') 

          {!! $customers->render() !!}
          @include('marketing.broadcasts.partials.remark')
         	</tbody>
        </table>
         {!! $customers->render() !!}
    </div>

@endsection

@section('scripts')

<script type="text/javascript">
  $(".checkbox").change(function() {
            id = $(this).val();
               
            if(this.checked) {
               $.ajax({
                    type: 'GET',
                    url: '{{ route('broadcast.add.dnd') }}',
                    data: {
                        id:id,
                        type:1,
                    },success: function (data) {
                      console.log(data);
                        if(data.status == 'error'){
                           alert('Something went wrong'); 
                        }else{
                           alert('Customer Added to DND');  

                        }
                      
                    },
                    error: function (data) {
                        alert('Something went wrong'); 
                    }
                        });
            }else{
                 $.ajax({
                    type: 'GET',
                    url: '{{ route('broadcast.add.dnd') }}',
                    data: {
                        id:id,
                        type: 0
                    },
                        }).done(response => {
                         alert('Customer Removed From DND');    
                    }); 
            }
        });

   $(".checkboxs").change(function() {
            id = $(this).val();
               
            if(this.checked) {
               $.ajax({
                    type: 'GET',
                    url: '{{ route('broadcast.add.manual') }}',
                    data: {
                        id:id,
                        type:1,
                    },success: function (data) {
                      console.log(data);
                        if(data.status == 'error'){
                           alert('Something went wrong'); 
                        }else{
                           alert('Customer Added to Manual');  

                        }
                      
                    },
                    error: function (data) {
                        alert('Something went wrong'); 
                    }
                        });
            }else{
                 $.ajax({
                    type: 'GET',
                    url: '{{ route('broadcast.add.manual') }}',
                    data: {
                        id:id,
                        type: 0
                    },
                        }).done(response => {
                         alert('Customer Removed From Manual');    
                    }); 
            }
        });

   $(document).on('click', '.make-remarks', function(e) {
            e.preventDefault();

            var id = $(this).data('id');
            $('.id').val(id);
            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('broadcast.gets.remark') }}',
                data: {
                    id:id,
                },
            }).done(response => {
                var html='';

                $.each(response, function( index, value ) {
                    html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
                    html+"<hr>";
                });
                $("#makeRemarksModal").find('#remarks-list').html(html);
            });
        });

        $('#addRemarksButton').on('click', function() {
            var id = $('.id').val();
            var remark = $('.remark').val();
            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('broadcast.add.remark') }}',
                data: {
                    id:id,
                    remark:remark,
                },
            }).done(response => {
                $('.add-remarks').find('textarea[name="remark"]').val('');

                var html =' <p> '+ remark +' <br> <small>By You updated on '+ moment().format('DD-M H:mm') +' </small></p>';

                $("#makeRemarksModal").find('#remarks-list').append(html);
            }).fail(function(response) {
                console.log(response);

                alert('Could not fetch remarks');
            });
        });

        $('.whatsapp').on('change', function() {
          number =  this.value;
          id = $(this).data("id");
           $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('broadcast.update.whatsappnumber') }}',
                data: {
                    id:id,
                    number:number,
                },
            }).done(response => {
               alert('WhatsApp number updated');
            }).fail(function(response) {
                alert('Something went wrong');
            });

        });
</script>
@endsection
