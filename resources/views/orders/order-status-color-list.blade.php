@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Order Status</h2>
    </div>
    <div class="mt-3 col-md-12">
		<form action="{{route('order.status.color')}}" method="get" class="search">
            <div class="col-md-2 pd-sm">
                {{-- <h5> Search Users </h5>  --}}
                <?php 
                    // if(request('usersIds')){  $usersIds = request('usersIds'); }
                    // else{ $usersIds = []; }
                ?>
                {{-- <select name="usersIds[]" id="usersIds" class="form-control select2" multiple>
                    <option  Value="">Select Users</option>
                    @foreach ($users as $user)
                    <option  Value="{{$user->id}}"  @if(in_array($user->id, $usersIds)) selected @endif>{!! $user->name !!}</option>
                    @endforeach
                </select> --}}
             </div>
			{{-- <div class="col-lg-2">
                <h5> Search branch </h5> 
				<input class="form-control" type="text" id="search_error" placeholder="Search branch" name="search_branch" value="{{ (request('search_branch') ?? "" )}}">
			</div>
			<div class="col-lg-2">
                <h5> Search Error Code </h5> 
				<input class="form-control" type="text" id="error_code" placeholder="Search Error Code" name="error_code" value="{{ (request('error_code') ?? "" )}}">
			</div>
            <div class="col-lg-2"><br>
				<input class="form-control" type="text" id="error_msg" placeholder="Search Error Message" name="error_msg" value="{{ (request('error_msg') ?? "" )}}">
			</div>
			<div class="col-lg-2"><br>
				<input class="form-control" type="date" name="date" value="{{ (request('date') ?? "" )}}">
			</div> --}}
			{{-- <div class="col-lg-2"><br>
				<button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
				   <img src="{{ asset('images/search.png') }}" alt="Search">
			   </button>
			   <a href="{{route('order.status.color')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
			</div> --}}
		</form>
	</div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content">
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="build-process-error-logs-list">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="10%">Status</th>
                            <th width="10%">magento Status</th>
                            <th width="10%">Message</th>
                            <th width="3%">Add Color</th>
                            <th width="3%">Created At</th>

                        </tr>
                        @foreach ($orderstatus as $key => $orderstat)
                            <tr data-id="{{ $orderstat->id }}">
                                <td>{{ $orderstat->id }}</td>
                                <td>  
                                    {{ $orderstat->status }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $orderstat->magento_status ?? '' }}
                                </td>
                                <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                       {{ strlen($orderstat->message_text_tpl) > 30 ? substr($orderstat->message_text_tpl, 0, 30).'...' :  $orderstat->message_text_tpl }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{ $orderstat->message_text_tpl }}
                                    </span>
                                </td>
                                <td style="display: flex;padding: 1px;">
                                    <input type="color" name="status_color" class="form-control" value="{{$orderstat->color}}" id="colorInput_{{$orderstat->id}}">                                   
                                          <button type="button" class="btn btn-image edit-vendor" onclick="updateOrderColor({{$orderstat->id}})" style="float: right;">
                                            <i class="fa fa-arrow-circle-right fa-lg"></i>
                                        </button>
                                </td>     
                                <td class="expand-row" style="word-break: break-all">
                                    {{ $orderstat->created_at }}
                                </td>
                              
                            </tr>
                        @endforeach
                    </table>
                </div>
                {!! $orderstatus->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>

    $('.select2').select2();

    $(document).on('click', '.expand-row', function () {
        var selection = window.getSelection();
        if (selection.toString().length === 0) {
            $(this).find('.td-mini-container').toggleClass('hidden');
            $(this).find('.td-full-container').toggleClass('hidden');
        }
    });

    function updateOrderColor(orderId) {
            var colorValue = $("#colorInput_" + orderId).val();
            $.ajax({
                url: "{{route('order.status.color.Update')}}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    orderId: orderId,
                    colorValue: colorValue
                },
                success: function(response) {
                    toastr["success"](response.message, "Message");
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(xhr.responseText);
                }
            });
        }
</script>
@endsection