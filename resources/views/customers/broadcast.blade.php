@extends('layouts.app')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('content')



    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">
              Broadcast Messages

              @if ($cron_job->last_status == 'error')
                <span class="badge">Cron Job Error</span>
              @endif
            </h2>

            {{-- <div class="row mb-3">
              <div class="col">
                <h3>Last Set Sent: <span class="font-weight-bold">{{ $last_set_completed_count }}</span></h3>
              </div>
              <div class="col">
                <h3>Last Set Received: <span class="font-weight-bold">{{ $last_set_received_count }}</span></h3>
              </div>
            </div> --}}

            <div class="">
                <form action="/broadcast/" method="GET" class="form-inline">
                        {{-- <div class="row"> --}}
                            {{-- <div class="col-md-4"> --}}
                                {{-- <input name="term" type="text" class="form-control"
                                       value="{{ isset($term) ? $term : '' }}"
                                       placeholder="Search"> --}}
                             <div class="form-group">
                               <div class='input-group date' id='schedule-datetime'>
                                 <input type='text' class="form-control" name="sending_time" value="{{ $date }}" required />

                                 <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                 </span>
                               </div>

                               @if ($errors->has('sending_time'))
                                   <div class="alert alert-danger">{{$errors->first('sending_time')}}</div>
                               @endif
                             </div>


                            {{-- </div>
                            <div class="col-md-4"> --}}
                             {{-- <div class="form-group ml-3">
                               <select class="selectpicker form-control" data-live-search="true" data-size="15" name="customer" title="Choose a Customer">
                                 @foreach ($customers_all as $customer)
                                   <option data-tokens="{{ $customer['name'] }} {{ $customer['email'] }}  {{ $customer['phone'] }} {{ $customer['instahandler'] }}" value="{{ $customer['id'] }}" {{ $selected_customer == $customer['id'] ? 'selected' : '' }}>{{ $customer['id'] }} - {{ $customer['name'] }} - {{ $customer['phone'] }}</option>
                                 @endforeach
                               </select>

                               @if ($errors->has('customer'))
                                   <div class="alert alert-danger">{{$errors->first('customer')}}</div>
                               @endif
                             </div> --}}


                            {{-- </div>
                            <div class="col-md-4"> --}}
                                <button type="submit" class="btn btn-image ml-3"><img src="/images/filter.png" /></button>
                            {{-- </div>
                        </div> --}}

                        <a href="{{ route('broadcast.index') }}" class="btn btn-xs btn-secondary">Clear</a>
                </form>
            </div>

            <div class="pull-right">
              {{-- @if ($last_stopped)
                <form action="{{ route('broadcast.restart') }}" method="POST">
                  @csrf

                  <button type="submit" class="btn btn-secondary">Restart Last Set</button>
                </form>
              @endif --}}

              <a href="{{ route('customer.whatsapp.stop.all') }}" class="btn btn-secondary">STOP ALL</a>
              <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#sendAllModal">Create Broadcast</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    {{-- <div class="card activity-chart mb-3">
      <canvas id="horizontalBroadcastBarChart" style="height: 100px;"></canvas>
    </div> --}}

    <div class="card activity-chart my-3">
      <canvas id="broadcastChart" style="height: 300px;"></canvas>
    </div>

    {{-- <div class="row">
      @foreach ($message_groups as $group_id => $group)
        <div class="col-md-3 mb-3">
          <button type="button" class="btn btn-secondary" data-toggle="collapse" data-target="#groupCollapse{{ $group_id }}">Group ID {{ $group_id }}</button>

          <div class="collapse mt-3" id="groupCollapse{{ $group_id }}">
            <div class="card card-body">
              @if ($group['can_be_stopped'])
                <div class="my-1">
                  <strong>Preview:</strong>
                  {{ $group['message'] }}
                  <div class="my-1">{{ $group['sent'] }} sent of {{ $group['total'] }}</div>
                </div>

                <form class="my-1" action="{{ route('broadcast.stop.group', $group_id) }}" method="POST">
                  @csrf

                  <div class="form-group">
                    <select class="form-control input-sm" name="whatsapp_number">
                      <option value="">Select Whatsapp Number</option>
                      @foreach ($api_keys as $api_key)
                        <option value="{{ $api_key->number }}">{{ $api_key->number }}</option>
                      @endforeach
                    </select>
                  </div>

                  <button type="submit" class="btn btn-xs btn-secondary">Stop</button>
                </form>
              @else
                <div class="my-1">
                  <strong>Preview:</strong>
                  {{ $group['message'] }}
                  <div class="my-1">{{ $group['sent'] }} sent of {{$group['total'] }}</div>
                </div>

                <form class="my-1" action="{{ route('broadcast.restart.group', $group_id) }}" method="POST">
                  @csrf

                  <div class="form-group">
                    <select class="form-control input-sm" name="whatsapp_number">
                      <option value="">Select Whatsapp Number</option>
                      @foreach ($api_keys as $api_key)
                        <option value="{{ $api_key->number }}">{{ $api_key->number }}</option>
                      @endforeach
                    </select>
                  </div>

                  <button type="submit" class="btn btn-xs btn-secondary">Restart</button>
                </form>

                <form class="my-1" action="{{ route('broadcast.delete.group', $group_id) }}" method="POST">
                  @csrf

                  <button type="submit" class="btn btn-xs btn-secondary">Delete</button>
                </form>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div> --}}
    <div id="exTab2" class="container">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#calendar" data-toggle="tab">Calendar</a>
        </li>
        <li>
          <a href="#broadcast-images" data-toggle="tab">Broadcast Images</a>
        </li>
      </ul>
    </div>

    <div class="tab-content">
      <div class="tab-pane active mt-3" id="calendar">
        <div class="row">
          <div class="col-xs-12">
            @foreach ($message_groups as $date => $data)
              <div class="card">
                <div class="card-header">{{ $date }}</div>

                <div class="card-body">
                  @if (count($data) > 0)
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <tr>
                          <th width="10%">Date</th>
                          <th width="25%">Broadcast</th>
                          <th width="40%">Data</th>
                          <th width="15%">Actions</th>
                          <th width="10%">Phone</th>
                        </tr>

                      @foreach ($data as $group_id => $group)
                      <tr>
                        <td>
                          <strong>Start:</strong> {{ \Carbon\Carbon::parse($group['sending_time'])->format('H:i d-m') }}

                          <br>

                          <strong>End:</strong> {{ \Carbon\Carbon::parse($group['expecting_time'])->format('H:i d-m') }}
                        </td>
                        <td>
                          <strong>Group ID {{ $group_id }}</strong>
                          <br>

                          {{ $group['message'] }}

                          @if (count($group['image']) > 0)
                            @foreach ($group['image'] as $image)
                              <img src="{{ $image['url'] }}" class="img-responsive" style="width: 50px;" alt="">
                            @endforeach
                          @endif

                          @if (count($group['linked_images']) > 0)
                            @foreach ($group['linked_images'] as $image)
                              @if (is_array($image) && array_key_exists('url', $image))
                                <img src="{{ $image['url'] }}" class="img-responsive" style="width: 50px;" alt="">
                              @endif
                            @endforeach
                          @endif
                        </td>
                        <td>
                          <div class="card activity-chart mb-3">
                            <canvas id="horizontalBroadcastBarChart{{ $date }}{{ $group_id }}" style="height: 120px;"></canvas>
                          </div>
                        </td>
                        <td>
                          @if ($group['can_be_stopped'])
                            <form class="my-1" action="{{ route('broadcast.stop.group', $group_id) }}" method="POST">
                              @csrf

                              <div class="form-group">
                                <select class="form-control input-sm" name="whatsapp_number">
                                  <option value="">Select Whatsapp Number</option>
                                  @foreach ($api_keys as $api_key)
                                    <option value="{{ $api_key->number }}">{{ $api_key->number }}</option>
                                  @endforeach
                                </select>
                              </div>

                              <button type="submit" class="btn btn-xs btn-secondary">Stop</button>
                            </form>
                          @else
                            <form class="my-1" action="{{ route('broadcast.restart.group', $group_id) }}" method="POST">
                              @csrf

                              <div class="form-group">
                                <select class="form-control input-sm" name="whatsapp_number">
                                  <option value="">Select Whatsapp Number</option>
                                  @foreach ($api_keys as $api_key)
                                    <option value="{{ $api_key->number }}">{{ $api_key->number }}</option>
                                  @endforeach
                                </select>
                              </div>

                              <div class="form-group">
                                <strong>Frequency</strong>
                                <input type="number" class="form-control input-sm" name="frequency" value="10" min="1" required>
                              </div>

                              <button type="submit" class="btn btn-xs btn-secondary">Restart</button>
                            </form>

                            <form class="my-1" action="{{ route('broadcast.delete.group', $group_id) }}" method="POST">
                              @csrf

                              <button type="submit" class="btn btn-xs btn-secondary">Delete</button>
                            </form>
                          @endif
                        </td>
                        <td>
                          @if ($group['whatsapp_number'])
                            {{ $group['whatsapp_number'] }}
                          @else
                            Sent from every number
                          @endif
                        </td>
                      </tr>
                    @endforeach
                    </table>
                  </div>

                  @else
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <tr>
                          <td>Upload New</td>
                          <td>
                            <form action="{{ route('broadcast.images.upload') }}" method="POST" enctype="multipart/form-data">
                              @csrf

                              <input type="hidden" name="sending_time" value="{{ $date }}">

                              <div class="form-group">
                                <input type="file" name="images[]" value="" required>
                              </div>

                              <button type="submit" class="btn btn-xs btn-secondary">Upload</button>
                            </form>
                          </td>
                          <td></td>
                          <td></td>
                          <td></td>
                        </tr>
                      </table>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      @include('customers.partials.modal-upload-images')
      @include('customers.partials.modal-send-to-all')

      <div class="tab-pane mt-3" id="broadcast-images">
        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#uploadImagesModal">Upload Images</button>
        <div class="row">
          @foreach ($broadcast_images as $image)
          <div class="col-md-3 col-xs-6 text-center mb-5">
            <img src="{{ $image->hasMedia(config('constants.media_tags')) ? $image->getMedia(config('constants.media_tags'))->first()->getUrl() : '#no-image' }}" class="img-responsive grid-image" alt="" />

            {{ $image->sending_time ?? '' }}

            @if ($image->products)
              <span class="badge">Linked</span>
            @else
              {{-- <a href="{{ route('attachImages', ['broadcast-images', $image->id]) }}" class="btn-link">Link</a> --}}
              <a href="{{ route('attachProducts', ['broadcast-images', $image->id]) }}" class="btn-link">Link Products</a>
            @endif

            <input type="checkbox" class="form-control image-selection hidden" value="{{ $image->id }}">
            {{-- <a class="btn btn-image" href="{{ route('image.grid.show',$image->id) }}"><img src="/images/view.png" /></a> --}}

            {{-- @can ('social-create') --}}
              {{-- <a class="btn btn-image" href="{{ route('image.grid.edit',$image->id) }}"><img src="/images/edit.png" /></a> --}}

            {!! Form::open(['method' => 'DELETE','route' => ['broadcast.images.delete', $image->id],'style'=>'display:inline']) !!}
              <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
            {!! Form::close() !!}
            {{-- @endcan --}}

            {{-- <a href="{{ route('image.grid.download', $image->id) }}" class="btn-link">Download</a>

            @if (isset($image->approved_user))
              <span>Approved by {{ App\User::find($image->approved_user)->name}} on {{ Carbon\Carbon::parse($image->approved_date)->format('d-m') }}</span>
            @endif --}}
          </div>
          @endforeach
        </div>

        {!! $broadcast_images->appends(Request::except('page'))->links() !!}
      </div>
    </div>



    {{-- <div id="exTab2" class="container">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#all-messages" data-toggle="tab">All Messages</a>
        </li>
        @if (count($last_set_completed) > 0)
          <li>
            <a href="#last-completed-messages" data-toggle="tab">Last Set Completed</a>
          </li>
        @endif
      </ul>
    </div>

    <div class="tab-content">
      <div class="tab-pane active mt-3" id="all-messages">
        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead>
                  <th>Customer Name</th>
                  <th>Phone</th>
                  <th>Whatsapp Number</th>
                  <th>Message</th>
                  <th>Group ID</th>
                  <th>Sent</th>
                  <th>Received</th>
                  <th>Status</th>
                  <th>Scheduled Date</th>
                  <th>Action</th>
                </thead>
                <tbody>
                @foreach ($message_queues as $key => $message_queue)
                  <tr>
                    <td>
                      @if ($message_queue->customer)
                        <a href="{{ route('customer.show', $message_queue->customer->id) }}" target="_blank">{{ $message_queue->customer->name }}</a>
                      @endif
                    </td>
                    <td>
                      @if ($message_queue->customer)
                        <div class="phone-container">
                          {{ $message_queue->customer->phone }}
                        </div>

                        <input type="number" name="phone" class="form-control phone-edit-input hidden" value="{{ $message_queue->customer->phone }}">
                        <a href="#" class="btn-link quick-edit-phone-button" data-id="{{ $message_queue->customer_id }}">Edit</a>
                      @else
                        {{ $message_queue->phone }}
                      @endif
                    </td>
                    <td>
                      {{ $message_queue->whatsapp_number }}
                    </td>
                    <td>{{ json_decode($message_queue->data, true)['message'] }}</td>
                    <td>{{ $message_queue->group_id }}</td>
                    <td>
                      @if ($message_queue->sent == 1)
                        <img src='/images/1.png' />
                      @endif
                    </td>
                    <td>
                      @if ($message_queue->customer && $message_queue->sent == 1)
                        @if ($message_queue->chat_message && $message_queue->chat_message->sent == 1)
                          <img src='/images/1.png' />
                        @endif
                      @endif
                    </td>
                    <td>
                      @if ($message_queue->status == 1)
                        Stopped
                      @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($message_queue->sending_time)->format('H:i d-m') }}</td>
                    <td>
                      @if (isset($message_queue->customer) && $message_queue->customer->do_not_disturb == 0)
                        <form action="{{ route('broadcast.donot.disturb', $message_queue->customer_id) }}" method="POST">
                          @csrf

                          <button type="submit" class="btn btn-xs btn-secondary">Do Not Disturb</button>
                        </form>
                      @elseif (isset($message_queue->customer) && $message_queue->customer->do_not_disturb == 1)
                        <span class="badge">Do Not Disturb</span>
                      @endif


                    </td>
                  </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {!! $message_queues->appends(Request::except('page'))->links() !!}
      </div>

      @if (count($last_set_completed) > 0)
        <div class="tab-pane mt-3" id="last-completed-messages">
          <div class="table-responsive mt-3">
              <table class="table table-bordered">
                  <thead>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Group ID</th>
                    <th>Sent</th>
                    <th>Received</th>
                    <th>Status</th>
                    <th>Scheduled Date</th>
                    <th>Action</th>
                  </thead>
                  <tbody>
                  @foreach ($last_set_completed as $key => $message_queue)
                    <tr>
                      <td>
                        @if ($message_queue->customer)
                          <a href="{{ route('customer.show', $message_queue->customer->id) }}" target="_blank">{{ $message_queue->customer->name }}</a>
                        @endif
                      </td>
                      <td>{{ $message_queue->customer ? $message_queue->customer->phone : $message_queue->phone }}</td>
                      <td>{{ json_decode($message_queue->data, true)['message'] }}</td>
                      <td>{{ $message_queue->group_id }}</td>
                      <td>
                        @if ($message_queue->sent == 1)
                          <img src='/images/1.png' />
                        @endif
                      </td>
                      <td>
                        @if ($message_queue->customer && $message_queue->sent == 1)
                          @if ($message_queue->chat_message && $message_queue->chat_message->sent == 1)
                            <img src='/images/1.png' />
                          @endif
                        @endif
                      </td>
                      <td>
                        @if ($message_queue->status == 1)
                          Stopped
                        @endif
                      </td>
                      <td>{{ \Carbon\Carbon::parse($message_queue->sending_time)->format('H:i d-m') }}</td>
                      <td>
                        @if (isset($message_queue->customer) && $message_queue->customer->do_not_disturb == 0)
                          <form action="{{ route('broadcast.donot.disturb', $message_queue->customer_id) }}" method="POST">
                            @csrf

                            <button type="submit" class="btn btn-xs btn-secondary">Do Not Disturb</button>
                          </form>
                        @elseif (isset($message_queue->customer) && $message_queue->customer->do_not_disturb == 1)
                          <span class="badge">Do Not Disturb</span>
                        @endif


                      </td>
                    </tr>
                  @endforeach
                  </tbody>
              </table>
          </div>

          {!! $last_set_completed->appends(Request::except('completed-page'))->links() !!}
        </div>
      @endif
    </div> --}}

@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
  <script type="text/javascript">
    $('#schedule-datetime').datetimepicker({
      format: 'YYYY-MM-DD'
    });

    $(document).on('click', '.quick-edit-phone-button', function(e) {
      e.preventDefault();

      var id = $(this).data('id');

      $(this).siblings('.phone-edit-input').removeClass('hidden');
      $(this).siblings('.phone-container').addClass('hidden');

      $(this).siblings('.phone-edit-input').keypress(function(e) {
        var key = e.which;
        var thiss = $(this);

        if (key == 13) {
          e.preventDefault();
          var phone = $(thiss).val();

          $.ajax({
            type: 'POST',
            url: "{{ url('customer') }}/" + id + '/updatePhone',
            data: {
              _token: "{{ csrf_token() }}",
              phone: phone,
            }
          }).done(function() {
            $(thiss).addClass('hidden');
            $(thiss).siblings('.phone-container').text(phone);
            $(thiss).siblings('.phone-container').removeClass('hidden');
          }).fail(function(response) {
            console.log(response);

            alert('Could not update phone');
          });
        }
      });
    });

    $(document).ready(function () {
        // 'use strict';
        let broadcastChart = $('#broadcastChart');


        var barChartExample = new Chart(broadcastChart, {
            type: 'bar',
            data: {
                labels: [
                  @foreach ($message_groups as $date => $data)
                    @foreach ($data as $group_id => $group)
                      'Group {{ $group_id }}',
                    @endforeach
                  @endforeach
                ],
                datasets: [
                    {
                        label: "Sent",
                        fill: true,
                        backgroundColor: '#5EBA31',
                        borderColor: '#5EBA31',
                        data: [
                          @foreach ($message_groups as $date => $data)
                            @foreach ($data as $group_id => $group)
                            {{ $group['sent'].',' }}
                            @endforeach
                          @endforeach
                        ],
                    },
                    {
                        label: "Received",
                        fill: true,
                        backgroundColor: '#5738CA',
                        borderColor: '#5738CA',
                        data: [
                            @foreach ($message_groups as $date => $data)
                              @foreach ($data as $group_id => $group)
                              {{ $group['received'].',' }}
                              @endforeach
                            @endforeach
                        ],
                    },
                    {
                        label: "Stopped",
                        fill: true,
                        backgroundColor: '#DC143C',
                        borderColor: '#DC143C',
                        data: [
                          @foreach ($message_groups as $date => $data)
                            @foreach ($data as $group_id => $group)
                            {{ $group['stopped'].',' }}
                            @endforeach
                          @endforeach
                        ],
                    }
                ],
            },
            options: {
                scaleShowValues: true,
                responsive: true,
                scales: {
        					xAxes: [{
        						display: true,
        						scaleLabel: {
        							display: true,
        							labelString: 'Sets'
        						}
        					}],
        					yAxes: [{
        						display: true,
        						scaleLabel: {
        							display: true,
        							labelString: 'Count'
        						}
        					}]
        				}
            }
        });

        var group_id = '';
        @foreach ($message_groups as $date => $data)
          @foreach ($data as $group_id => $group)
            group_id = "{{ $date }}{{ $group_id }}";
            console.log(group_id);
            window['horizontalBroadcastBarChart' + group_id] = $('#horizontalBroadcastBarChart' + group_id);
            var horizontalBarChart = new Chart(window['horizontalBroadcastBarChart' + group_id], {
                type: 'horizontalBar',
                data: {
                  labels: ['Total'],
                  datasets: [
                    {
                      label: "Sent",
                      backgroundColor: '#5EBA31',
                      data: [{{ $group['sent'] }}],
                    },
                    {
                      label: "Received",
                      backgroundColor: '#5738CA',
                      data: [{{ $group['received'] }}],
                    },
                    {
                      label: "Stopped",
                      backgroundColor: '#DC143C',
                      data: [{{ $group['stopped'] }}],
                    }
                  ],
                },
                options: {
                  beginAtZero: true,
                  elements: {
        						rectangle: {
        							borderWidth: 2,
        						}
        					},
        					responsive: true,
        					legend: {
        						position: 'right',
        					},
                  scales: {
                    xAxes: [{
                      ticks: {
                        beginAtZero: true,
                        max: {{ $group['total'] }}
                      }
                    }]
                  }
                }
            });
          @endforeach
        @endforeach
    });

    var images_selection = [];

    $(document).on('click', '.link-images-button', function() {
      $('.image-selection').removeClass('hidden');

      $('#sendAllModal').find('.close').click();
      $('a[href="#broadcast-images"]').click();
    });

    $(document).on('click', '.image-selection', function() {
      var id = $(this).val();

      if ($(this).prop('checked') == true) {
        images_selection.push(id);
      } else {
        var index = images_selection.indexOf(id);
        images_selection.splice(index, 1);
      }

      $('.link-images-button').text('Link Images (' + images_selection.length + ')');

      $('#linked_images').val(JSON.stringify(images_selection));
      console.log(images_selection);
    });
  </script>
@endsection
