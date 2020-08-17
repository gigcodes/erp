@extends('layouts.app')
@section('content')
    <div class="row" xmlns="http://www.w3.org/1999/html">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Manage Twilio Numbers</h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12">
            <div class="row">
                <a href="{{ route('twilio-get-numbers', $account_id) }}">
                    <button type="button" class="btn btn-secondary">Get Twilio Numbers</button>
                </a>
            </div>
        </div>
    </div>
    <div class="row  no-gutters mt-3">
        <div class="col-md-12 col-sm-12">
            <div class="table-responsive">
                <div class="">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th scope="col" class="text-center">Sr. no.</th>
                            <th scope="col" class="text-center">Number</th>
                            <th scope="col" class="text-center">Friendly Name</th>
                            <th scope="col" class="text-center">SID</th>
                            <th scope="col" class="text-center">Voice url</th>
                            <th scope="col" class="text-center">Date Created</th>
                            <th scope="col" class="text-center">Date Updated</th>
                            <th scope="col" class="text-center">SMS url</th>
                            <th scope="col" class="text-center">Voice Receive Mode</th>
                            <th scope="col" class="text-center">Voice Application SID</th>
                            <th scope="col" class="text-center">SMS Application SID</th>
                            <th scope="col" class="text-center">Trunk SID</th>
                            <th scope="col" class="text-center">Emergency Status</th>
                            <th scope="col" class="text-center">Emergency Address SID</th>
                            <th scope="col" class="text-center">Store Website</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        @if(isset($numbers))
                            @foreach($numbers as $number)
                                <tr>
                                    <td>1</td>
                                    <td>{{ $number->phone_number }}</td>
                                    <td>{{ $number->friendly_name }}</td>
                                    <td>{{ $number->sid }}</td>
                                    <td>{{ $number->voice_url }}</td>
                                    <td>{{ \Carbon\Carbon::parse($number->date_created)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($number->date_updated)->format('d-m-Y') }}</td>
                                    <td>{{ $number->sms_url }}</td>
                                    <td>{{ $number->voice_receive_mode }}</td>
                                    <td>{{ $number->voice_application_sid }}</td>
                                    <td>{{ $number->sms_application_sid }}</td>
                                    <td>{{ $number->trunk_sid }}</td>
                                    <td>{{ $number->emergency_status }}</td>
                                    <td>{{ $number->emergency_address_sid }}</td>
                                    <td>{{ @$number->assigned_stores->store_website->title }}</td>
                                    <td>{{ $number->status }}</td>
                                    <td>
                                        <a href="javascript:void(0);" type="button" id="{{ $number->id }}" class="btn btn-image open_row">
                                            <img src="/images/forward.png" style="cursor: default;" width="2px;">
                                        </a>
                                        <a href="javascript:void(0);" class="call_forwarding btn d-inline btn-image" data-attr="{{ $number->id }}" title="Call Forwarding" ><img src="/images/view.png" style="cursor: default;"></a>
                                    </td>
                                </tr>
                                <tr class="hidden_row_{{ $number->id  }}" data-eleid="{{ $number->id }}" style="display:none;">
                                    <td colspan="3">
                                        <label>Store website:</label>
                                        <div class="input-group">
                                            <select class="form-control store_websites" id="store_website_{{ $number->id }}">
                                                <option value="">Select store website</option>
                                                @if(isset($store_websites))
                                                    @foreach($store_websites as $websites)
                                                        <option value="{{ $websites->id }}" @if(isset($number->assigned_stores)) @if($number->assigned_stores->store_website_id == $websites->id) selected @endif @endif>{{ $websites->title }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    <td colspan="3">
                                        <label>Message when agent available</label>
                                        <input type="text" class="form-control" name="message_available" id="message_available_{{ $number->id }}" value="{{ @$number->assigned_stores->message_available }}"/>
                                    </td>
                                    <td colspan="3">
                                        <label>Message when agent not available</label>
                                        <input type="text" class="form-control" name="message_not_available" id="message_not_available_{{ $number->id }}" value="{{ @$number->assigned_stores->message_not_available }}"/>
                                    </td>
                                    <td colspan="3">
                                        <label>Message when agent is busy</label>
                                        <input type="text" class="form-control" name="message_busy" id="message_busy_{{ $number->id }}" value="{{ @ $number->assigned_stores->message_busy }}"/>
                                    </td>
                                    <td colspan="3">
                                        <button class="btn btn-sm btn-image save-number-to-store" id="save_{{ $number->id }}"><img src="/images/filled-sent.png" style="cursor: default;"></button>
                                    </td>

                                </tr>
                                <tr class="call_forwarding_{{ $number->id  }}" style="display:none;">
                                    <td colspan="3">
                                        <label>Select Agent</label>
                                        <div class="input-group">
                                            <select class="form-control" id="agent_{{ $number->id }}">
                                                <option value="">Select agent</option>
                                                @if(isset($customer_role_users))
                                                    @foreach($customer_role_users as $user)
                                                        <option value="{{ $user->user->id }}">{{ $user->user->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    <td colspan="3">
                                        <button class="btn btn-sm btn-image call_forwarding_save" id="forward_{{ $number->id }}"><img src="/images/filled-sent.png" style="cursor: default;"></button>
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
                                   <label>Select Agent</label>
                                   <select class="form-control" name="agent_id">
                                       <option value="">Select Agent</option>
                                       @if(isset($customer_role_users))
                                           @foreach($customer_role_users as $user)
                                                <option value="{{ $user->user->id }}">{{ $user->user->name }}</option>
                                           @endforeach
                                       @endif
                                   </select>
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

        <!-- Modal -->
        <div class="modal fade" id="assignNumberToStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Call Forwarding</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ route('assign-number-to-store-website') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
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
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mr-3">
                                        <textarea name="message_available" class="form-control" placeholder="Greeting Message during operation hours "></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mr-3">
                                        <textarea name="message_not_available" class="form-control" placeholder="Greeting Message during non operation hours"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mr-3">
                                        <textarea name="message_busy" class="form-control" placeholder="Greeting Message if busy "></textarea>
                                    </div>
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
            $('.open_row').on("click", function(){
                var row_id = $(this).attr('id');
                $('.hidden_row_'+row_id).show();
            });

            $('.save-number-to-store').on("click", function(){
                var selected_no = $(this).attr('id');
                selected_no = selected_no.split('_');
                var num_id = selected_no[1];
                $.ajax({
                    url: '{{ route('assign-number-to-store-website') }}',
                    method: 'POST',
                    data: {
                        '_token' : "{{ csrf_token() }}",
                        'twilio_number_id' : num_id,
                        'store_website_id' : $('#store_website_'+num_id).val(),
                        'message_available' : $('#message_available_'+num_id).val(),
                        'message_not_available' : $('#message_not_available_'+num_id).val(),
                        'message_busy' : $('#message_busy_'+num_id).val()
                    }
                }).done(function(response){
                    if(response.status == 1){
                        toastr['success'](response.message);

                    }else{
                        toastr['error'](response.message);

                    }
                    console.log(response);
                });
            });

            $('.call_forwarding').on("click", function(){
                var num_id = $(this).data('attr');
                $('.call_forwarding_'+num_id).show();

                $('.call_forwarding_save').on("click", function(){
                    var agent_id = $('#agent_'+num_id).val();
                    if(agent_id == ''){
                        alert('Please select agent');
                    }
                    $.ajax({
                        url: '{{ route('manage-twilio-call-forward') }}',
                        method: 'POST',
                        data: {
                            '_token' : "{{ csrf_token() }}",
                            'twilio_account_id' : '{{ $account_id }}',
                            'twilio_number_id' : num_id,
                            'agent_id' : agent_id
                        }
                    }).done(function(response){
                        if(response.status == 1){
                            toastr['success'](response.message);
                        }else{
                            toastr['error'](response.message);
                        }
                    });
                });


            });

        });
    </script>
@endsection