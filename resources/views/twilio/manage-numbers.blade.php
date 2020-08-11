@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Manage Twilio Numbers</h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12">
            <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mr-3">
                            <form method="get" id="change_account">
                                <select class="form-control" name="id" id="twilio_id">
                                <option value="">Select Twilio Account</option>
                                @if(isset($twilio_accounts))
                                    @foreach($twilio_accounts as $account)
                                        <option value="{{ $account->id }}" @if(request()->get('id') && (request()->get('id') == $account->id)) @endif>{{ $account->twilio_email }}</option>
                                    @endforeach
                                @endif
                            </select>
                            </form>
                        </div>
                    </div>
            </div>
            <div class="row full-width" style="width: 100%;">
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group mr-3">
                            <select class="form-control" name="store_website_id" id="store_website_id">
                                <option value="">Select store website</option>
                                @if(isset($store_websites))
                                    @foreach($store_websites as $websites)
                                        <option value="{{ $websites->id }}">{{ $websites->title }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group mr-3">
                            <select class="form-control" name="twilio_number_id" id="twilio_number_id">
                                <option value="">Select twilio number</option>
                                @if(isset($numbers))
                                    @foreach($numbers as $number)
                                        <option value="{{ $number->id }}">{{ $number->phone_number }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group mr-3">
                            <button class="btn btn-secondary assign_store_website_numbers">+</button>
                        </div>
                    </div>


                </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="form-group mr-3">
                    @if(request()->get('id'))
                        <a class="btn btn-secondary" href="{{ route('twilio-call-recording', 'id='.request()->get('id')) }}">Call Recordings</a>
                    @endif
                    </div>
                </div>
            </div>
</div>
</div>
<div class="row mb-3">
<div class="col-md-10 col-sm-12">
<div class="row">
<div class="table-responsive">
<table class="table table-bordered table-hover">
    <thead>
    <tr>
        <th scope="col" class="text-center">Number</th>
        <th scope="col" class="text-center">Friendly Name</th>
        <th scope="col" class="text-center">Store Websites</th>
        <th scope="col" class="text-center">Date</th>
        <th scope="col" class="text-center">Action</th>
    </tr>
    </thead>
    <tbody class="text-center">
        @if(isset($numbers))
            @foreach($numbers as $number)
                <tr>
                    <td>{{ $number->phone_number }}</td>
                    <td>{{ $number->friendly_name }}</td>
                    <td>
                        @foreach($number->assigned_stores as $stores)
                            {{ $stores->store_website->title }}<br/>
                        @endforeach
                    </td>
                    <td>{{ \Carbon\Carbon::parse($number->date_created)->format('d-m-Y') }}</td>
                    <td>
                        <a href="#" class="call_forwarding" id="{{ $number->id }}" data-attr="{{ $number->phone_number }}">Call Forwarding</a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

</div>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="callForwardingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Call Forwarding</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form method="post" action="{{ route('twilio-call-forwarding') }}">
    @csrf
    <div class="modal-body">
        <div class="col-md-12">
            <input type="hidden" class="form-control" name="twilio_number_id" id="num_id" value="" />
            <input type="hidden" class="form-control" name="twilio_account_id" id="{{ request()->get('id') }}" value="" />

            <div class="col-md-4">
                <label>Number</label>
                <input type="text" class="form-control" name="number" id="number" required/>
            </div>
            <div class="col-md-4">
                <label>Country Code</label>
                <input type="text" class="form-control" name="area_code" required/>
            </div>
            <div class="col-md-4">
                <label>Forward to</label>
                <input type="text" class="form-control" name="phone_no" required/>
            </div>
        </div>
    </div>
    <div class="modal-footer mt-5">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
    </div>
</form>
</div>
</div>
</div>

</div>

<script type="text/javascript">
$(document).ready(function(){
$('.assign_store_website_numbers').on("click", function(){
console.log('yes');
var store_website_id = $('#store_website_id').val();
console.log(store_website_id);
if(store_website_id == ''){
alert('please select store website');
return false;
}
var twilio_number_id = $('#twilio_number_id').val();
console.log(twilio_number_id);
if(twilio_number_id == ''){
alert('please select number');
return false;
}
$.ajax({
url: "{{ route('assign-number-to-store-website') }}",
type: 'POST',
data: {
    twilio_number_id : twilio_number_id,
    store_website_id: store_website_id,
    _token: "{{csrf_token()}}"
},
success: function (data) {
    if(data.status == 1){
        toastr["success"](data.message, "Message");
    }else{
        toastr["error"](data.message, "Message");
    }

}
});
});

$('.call_forwarding').on("click", function(){
var id = $(this).attr('id');
var num = $(this).data('attr');
$('#number').val(num);
$('#num_id').val(id);
$('#callForwardingModal').modal('show');
});

$('#twilio_id').on("change", function(){
$('#change_account').submit();
});

});
</script>
@endsection