@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> --}}
@endsection

@section('content')


    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Master Control - {{ date('Y-m-d') }}</h2>
            <div class="pull-left">
              {{-- <form action="/customers/" method="GET" class="form-inline">
                <input name="term" type="text" class="form-control"
                       value="{{ isset($term) ? $term : '' }}"
                       placeholder="Search" id="customer-search">

                <div class="form-group ml-3">
                  <select class="form-control" name="type">
                    <optgroup label="Type">
                      <option value="">Select</option>
                      <option value="new" {{ isset($type) && $type == 'new' ? 'selected' : '' }}>New</option>
                      <option value="delivery" {{ isset($type) && $type == 'delivery' ? 'selected' : '' }}>Delivery</option>
                      <option value="Refund to be processed" {{ isset($type) && $type == 'Refund to be processed' ? 'selected' : '' }}>Refund</option>
                      <option value="unread" {{ isset($type) && $type == 'unread' ? 'selected' : '' }}>Unread</option>
                      <option value="unapproved" {{ isset($type) && $type == 'unapproved' ? 'selected' : '' }}>Unapproved</option>
                    </optgroup>
                  </select>
                </div>

                <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
              </form> --}}
            </div>

            <div class="pull-right mt-4">
              {{-- <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#mergeModal">Merge Customers</button> --}}
              {{-- <a class="btn btn-secondary" href="{{ route('customer.create') }}">+</a> --}}
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div id="exTab2" class="container">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#broadcasts-tab" data-toggle="tab" class="btn btn-image">Broadcasts</a>
        </li>
        <li>
          <a href="#tasks-tab" data-toggle="tab" class="btn btn-image">Tasks</a>
        </li>
        <li>
          <a href="#orders-tab" data-toggle="tab" class="btn btn-image">Orders</a>
        </li>
        <li>
          <a href="#purchases-tab" data-toggle="tab" class="btn btn-image">Purchases</a>
        </li>
        <li>
          <a href="#products-tab" data-toggle="tab" class="btn btn-image">Scraping</a>
        </li>
        <li>
          <a href="#reviews-tab" data-toggle="tab" class="btn btn-image">Reviews</a>
        </li>
        <li>
          <a href="#emails-tab" data-toggle="tab" class="btn btn-image">Emails</a>
        </li>
        <li>
          <a href="#accounting-tab" data-toggle="tab" class="btn btn-image">Accounting</a>
        </li>
      </ul>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <div class="tab-content">
          <div class="tab-pane active mt-3" id="broadcasts-tab">
            <div class="row">
              <div class="col">
                <div class="pull-left">
                  <a href="{{ route('broadcast.index') }}" target="_blank"><h3>Broadcasts</h3></a>
                </div>

                <div class="pull-right">
                  <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="customer_id" value="2150">
                    <input type="hidden" name="instruction" value="Please create more broadcasts">
                    <input type="hidden" name="category_id" value="1">
                    <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('image_shortcut') }}">

                    <button type="submit" class="btn quick-shortcut-button">+ Broadcast</button>
                  </form>
                </div>
              </div>
            </div>

            <div class="row">
              @php
                $count = 0;
              @endphp
              @foreach ($message_groups as $date => $data)
                @if ($count == 0)
                  <div class="col-md-4">
                    <h4>Live - {{ $date }}</h4>
                    @foreach ($data as $group_id => $group)
                        <div class="card activity-chart mb-3">
                          <div class="card-header" data-toggle="tooltip" data-placement="top" data-html="true" title="<strong>Message: </strong>{{ $group['message'] }}<br /><strong>Expected Delivery: </strong>{{ $group['expecting_time'] }}">
                            Group ID {{ $group_id }}
                          </div>

                          <canvas id="horizontalBroadcastBarChart{{ $date }}{{ $group_id }}" style="height: 120px;"></canvas>
                        </div>
                    @endforeach
                  </div>

                  @php
                    $count++;
                  @endphp

                @elseif ($count == 1)
                  <div class="col-md-4">
                    <h4>Planned - {{ $date }}</h4>
                    @foreach ($data as $group_id => $group)
                        <div class="card activity-chart mb-3">
                          <div class="card-header" data-toggle="tooltip" data-placement="top" data-html="true" title="<strong>Message: </strong>{{ $group['message'] }}<br /><strong>Expected Delivery: </strong>{{ $group['expecting_time'] }}">
                            Group ID {{ $group_id }}
                          </div>

                          <canvas id="horizontalBroadcastBarChart{{ $date }}{{ $group_id }}" style="height: 120px;"></canvas>
                        </div>
                    @endforeach
                  </div>

                  @php
                    $count++;
                  @endphp
                @else
                  <div class="col-md-4">
                    <h4>Future - {{ $date }}</h4>
                    @foreach ($data as $group_id => $group)
                        <div class="card activity-chart mb-3">
                          <div class="card-header" data-toggle="tooltip" data-placement="top" data-html="true" title="<strong>Message: </strong>{{ $group['message'] }}<br /><strong>Expected Delivery: </strong>{{ $group['expecting_time'] }}">
                            Group ID {{ $group_id }}
                          </div>

                          <canvas id="horizontalBroadcastBarChart{{ $date }}{{ $group_id }}" style="height: 120px;"></canvas>
                        </div>
                    @endforeach
                  </div>
                @endif
              @endforeach
            </div>
          </div>

          <div class="tab-pane mt-3" id="tasks-tab">
            <div class="row">
              <div class="col">
                <ul class="list-group">
                  <li class="list-group-item">
                    <a href="{{ url('/') }}" target="_blank"><h4>Tasks</h4></a>
                  </li>
                  @foreach ($tasks['tasks'] as $user_id => $task_data)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <strong>{{ array_key_exists($user_id, $users_array) ? $users_array[$user_id] : 'User Doesnt Exist' }}</strong>

                      <span>
                        @if (array_key_exists(0, $task_data))
                          <span class="badge badge-red badge-pill">{{ count($task_data[0]) }}</span>
                        @else
                          <span class="badge badge-red badge-pill">0</span>
                        @endif

                        @if (array_key_exists(1, $task_data))
                          <span class="badge badge-green badge-pill">{{ count($task_data[1]) }}</span>
                        @else
                          <span class="badge badge-green badge-pill">0</span>
                        @endif
                      </span>
                     </li>
                  @endforeach

                  <li class="list-group-item">
                    <strong>{{ array_key_exists($tasks['last_pending']['assign_to'], $users_array) ? $users_array[$tasks['last_pending']['assign_to']] : 'User Doesnt Exist' }}</strong> - {{ $tasks['last_pending']['task_details'] }} on <strong>{{ \Carbon\Carbon::parse($tasks['last_pending']['created_at'])->format('d-m') }}</strong>
                  </li>
                </ul>
              </div>

              <div class="col">
                <ul class="list-group">
                  <li class="list-group-item">
                    <a href="{{ route('instruction.index') }}" target="_blank"><h4>Instructions</h4></a>
                  </li>
                  @foreach ($instructions as $user_id => $data)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <strong>{{ array_key_exists($user_id, $users_array) ? $users_array[$user_id] : 'User Doesnt Exist' }}</strong>

                      <span>
                        @if (array_key_exists(0, $data))
                          <span class="badge badge-red badge-pill">{{ count($data[0]) }}</span>
                        @else
                          <span class="badge badge-red badge-pill">0</span>
                        @endif

                        @if (array_key_exists(1, $data))
                          <span class="badge badge-green badge-pill">{{ count($data[1]) }}</span>
                        @else
                          <span class="badge badge-green badge-pill">0</span>
                        @endif
                      </span>
                     </li>
                  @endforeach

                  <li class="list-group-item">
                    <strong>{{ array_key_exists($last_pending_instruction['assigned_to'], $users_array) ? $users_array[$last_pending_instruction['assigned_to']] : 'User Doesnt Exist' }}</strong> - {{ $last_pending_instruction['instruction'] }} on <strong>{{ \Carbon\Carbon::parse($last_pending_instruction['created_at'])->format('d-m') }}</strong>
                  </li>
                </ul>
              </div>

              <div class="col">
                <ul class="list-group">
                  <li class="list-group-item">
                    <a href="{{ route('development.index') }}" target="_blank"><h4>Developer Tasks</h4></a>
                  </li>
                  @foreach ($developer_tasks as $user_id => $data)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <strong>{{ array_key_exists($user_id, $users_array) ? $users_array[$user_id] : 'User Doesnt Exist' }}</strong>

                      <span>
                        @if (array_key_exists('0', $data))
                          <span class="badge badge-red badge-pill">{{ count($data['0']) }}</span>
                        @else
                          <span class="badge badge-red badge-pill">0</span>
                        @endif

                        @if (array_key_exists('1', $data))
                          <span class="badge badge-green badge-pill">{{ count($data['1']) }}</span>
                        @else
                          <span class="badge badge-green badge-pill">0</span>
                        @endif
                      </span>
                     </li>
                  @endforeach

                  <li class="list-group-item">
                    <strong>{{ array_key_exists($last_pending_developer_task['user_id'], $users_array) ? $users_array[$last_pending_developer_task['user_id']] : 'User Doesnt Exist' }}</strong> - {{ $last_pending_developer_task['task'] }} on <strong>{{ \Carbon\Carbon::parse($last_pending_developer_task['created_at'])->format('d-m') }}</strong>
                  </li>
                </ul>
              </div>
            </div>

          </div>

          <div class="tab-pane mt-3" id="orders-tab">
            <a href="{{ route('order.index') }}" target="_blank"><h4>Orders</h4></a>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th rowspan="2">Product Name</th>
                    <th rowspan="2">Price</th>
                    <th rowspan="2">Qty</th>
                    <th rowspan="2">Total</th>
                    <th>COD</th>
                  </tr>

                  <tr>
                    <td>{{ $orders['cod'] }}</td>
                  </tr>
                </thead>

                <tbody>
                  @foreach ($orders['orders'] as $order)
                    <tr>
                      @if ($order->order_product)
                        <td>
                          {{ $order->order_product[0]->product ? $order->order_product[0]->product->name : 'No Product' }}
                        </td>
                        <td>
                          {{ $order->order_product[0]->product_price }}
                        </td>
                        <td>
                          {{ $order->order_product[0]->qty }}
                        </td>
                        <td>
                            {{ $order->order_product[0]->product_price * $order->order_product[0]->qty }}
                        </td>
                      @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                      @endif
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="tab-pane mt-3" id="purchases-tab">
            <a href="{{ route('purchase.index') }}" target="_blank"><h4>Purchases</h4></a>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Purchase ID</th>
                    <th>Customers</th>
                    <th>Products</th>
                    <th>Qty</th>
                    <th>Retail Price</th>
                    <th>Sold Price</th>
                    <th>Buying Price</th>
                    <th>Gross Profit</th>
                  </tr>
                </thead>

                <tbody>
                  @foreach ($purchases as $purchase)
                    <tr>
                      <td><a href="{{ route('purchase.show', $purchase['id']) }}">{{ $purchase['id'] }}</a></td>
                      <td>
                        <ul>
                          @foreach ($purchase['products'] as $product)
                            @if (count($product['orderproducts']) > 0)
                              @foreach ($product['orderproducts'] as $order_product)
                                <li>
                                  {{ $order_product['order'] ? ($order_product['order']['customer'] ? $order_product['order']['customer']['name'] : 'No Customer') : 'No Order' }}
                                </li>
                              @endforeach
                            @endif
                          @endforeach
                        </ul>
                      </td>
                      <td>
                        @foreach ($purchase['products'] as $product)
                          <img src="{{ $product['imageurl'] }}" class="img-responsive" width="50px">
                        @endforeach
                      </td>
                      {{-- <td>{{ $purchase['purchase_supplier']['supplier'] }}</td> --}}
                      <td>
                        @php
                          $qty = 0;
                        @endphp
                        <ul>
                          @foreach ($purchase['products'] as $product)
                            @if (count($product['orderproducts']) > 0)
                              @foreach ($product['orderproducts'] as $order_product)
                                @php
                                  $qty += $order_product['qty'];
                                @endphp
                              @endforeach
                            @endif

                            <li>
                              {{ $qty }}
                            </li>

                            @php
                              $qty = 0;
                            @endphp
                          @endforeach
                        </ul>
                      </td>
                      <td>
                        {{-- @php $retail_price = 0; @endphp
                        @foreach ($purchase['products'] as $product)
                          @php $retail_price += $product['price'] @endphp
                        @endforeach

                        {{ $retail_price }} --}}

                        <ul>
                          @foreach ($purchase['products'] as $product)
                            <li>
                              {{ $product['price'] }}
                            </li>
                          @endforeach
                        </ul>
                      </td>
                      <td>
                        <ul>
                          @php $sold_price = 0; @endphp
                          @foreach ($purchase['products'] as $product)
                            @foreach ($product['orderproducts'] as $order_product)
                              <li>{{ $order_product['product_price'] }}</li>

                              @php
                                $sold_price += $order_product['product_price'];
                              @endphp
                            @endforeach
                          @endforeach
                        </ul>
                      </td>
                      <td>
                        <ul>
                          @php $actual_price = 0; @endphp
                          @foreach ($purchase['products'] as $product)
                            @php $actual_price += $product['price'] @endphp

                            <li>{{ $product['price'] * 78 }}</li>
                          @endforeach
                        </ul>
                      </td>
                      <td>
                        {{ $sold_price - ($actual_price * 78) }}
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <div class="tab-pane mt-3" id="products-tab">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th rowspan="2"><a href="{{ route('scrap.activity') }}" target="_blank">Scraped</a></th>
                    <th rowspan="2"><a href="{{ route('products.listing') }}" target="_blank">Created</a></th>
                    <th colspan="2">On {{ \Carbon\Carbon::now()->subDays(2)->format('d-m') }}</th>
                    <th rowspan="2">Actions</th>
                  </tr>

                  <tr>
                    <th>Scraped</th>
                    <th>Listed</th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td>
                      {{ $scraped_count['0']->total }}
                    </td>
                    <td>
                      {{ $products_count['0']->total }}
                    </td>
                    <td>
                      {{ $scraped_days_ago_count['0']->total }}
                    </td>
                    <td>
                      {{ $listed_days_ago_count['0']->total }}
                    </td>
                    <td>

                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="tab-pane mt-3" id="reviews-tab">
            Reviews
          </div>

          <div class="tab-pane mt-3" id="emails-tab">
            Supplier Emails
          </div>

          <div class="tab-pane mt-3" id="accounting-tab">
            Accounting
          </div>
        </div>
      </div>
    </div>

    {{-- <div class="row">
      <div class="col-md-6">
        <a href="{{ route('customer.index') }}" target="_blank"><h3>Messages</h3></a>
          <h4>Unread: {{ $unread_messages[0]->unread }}</h4>
          <h4>Waiting Approval: {{ $unread_messages[0]->waiting_approval }}</h4>
      </div>
    </div> --}}

@endsection

@section('scripts')
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> --}}

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
  <script type="text/javascript">
  $(document).ready(function() {
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });
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

    $(document).on('click', '.quick-shortcut-button', function(e) {
      e.preventDefault();

      var customer_id = $(this).parent().find('input[name="customer_id"]').val();
      var instruction = $(this).parent().find('input[name="instruction"]').val();
      var category_id = $(this).parent().find('input[name="category_id"]').val();
      var assigned_to = $(this).parent().find('input[name="assigned_to"]').val();
      var thiss = $(this);
      var text = $(this).text();

      $.ajax({
        type: "POST",
        url: "{{ route('instruction.store') }}",
        data: {
          _token: "{{ csrf_token() }}",
          customer_id: customer_id,
          instruction: instruction,
          category_id: category_id,
          assigned_to: assigned_to,
        },
        beforeSend: function() {
          $(thiss).text('Loading...');
        }
      }).done(function(response) {
        $(thiss).text(text);
      }).fail(function(response) {
        $(thiss).text(text);

        alert('Could not execute shortcut!');

        console.log(response);
      });
    });
  </script>
@endsection
