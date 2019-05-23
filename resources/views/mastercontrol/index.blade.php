@extends('layouts.app')

@section('styles')
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> --}}
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')


    <div class="row mb-5">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Master Control - {{ date('Y-m-d') }}</h2>
            <div class="pull-left">
              <form class="form-inline" action="{{ route('mastercontrol.index') }}" method="GET">
                <div class="form-group ml-3">
                  <input type="text" value="" name="range_start" hidden/>
                  <input type="text" value="" name="range_end" hidden/>
                  <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                  </div>
                </div>

                <button type="submit" class="btn btn-secondary ml-3">Submit</button>
              </form>
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

                  @if ($cron_job->last_status == 'error')
                    <span class="badge">Cron Job Error</span>
                  @endif
                </div>

                {{-- <div class="pull-right">
                  <form class="d-inline" action="{{ route('instruction.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="customer_id" value="2150">
                    <input type="hidden" name="instruction" value="Please create more broadcasts">
                    <input type="hidden" name="category_id" value="1">
                    <input type="hidden" name="assigned_to" value="{{ \App\Setting::get('image_shortcut') }}">

                    <button type="submit" class="btn quick-shortcut-button">+ Broadcast</button>
                  </form>
                </div> --}}
              </div>
            </div>

            <div class="row">
              @php
                $count = 0;
              @endphp
              @foreach ($message_groups as $date => $data)
                @if ($date == \Carbon\Carbon::now()->subDay()->format('Y-m-d'))
                  <div class="col-md-4">
                    <h4>Updated as of - {{ $date }}</h4>
                    @foreach ($data as $group_id => $group)
                        <div class="card activity-chart mb-3">
                          <div class="card-header" data-toggle="tooltip" data-placement="top" data-html="true" title="<strong>Message: </strong>{{ $group['message'] }}<br /><strong>Expected Delivery: </strong>{{ $group['expecting_time'] }}">
                            Group ID {{ $group_id }} - expected delivery on {{ \Carbon\Carbon::parse($group['expecting_time'])->format('d-m H:i') }}
                          </div>

                          @if (count($group['image']) > 0)
                            @foreach ($group['image'] as $image)
                              <img src="{{ $image['url'] }}" alt="" class="img-responsive thumbnail-200">
                            @endforeach
                          @elseif (count($group['linked_images']) > 0)
                            @foreach ($group['linked_images'] as $image)
                              <img src="{{ $image['url'] }}" alt="" class="img-responsive thumbnail-200">
                            @endforeach
                          @endif

                          <canvas id="horizontalBroadcastBarChart{{ $date }}{{ $group_id }}" style="height: 120px;"></canvas>
                        </div>
                    @endforeach
                  </div>

                  @php
                    $count++;
                  @endphp

                @elseif ($date == \Carbon\Carbon::now()->format('Y-m-d'))
                  <div class="col-md-4">
                    <h4>Live - {{ $date }}</h4>
                    @foreach ($data as $group_id => $group)
                        <div class="card activity-chart mb-3">
                          <div class="card-header" data-toggle="tooltip" data-placement="top" data-html="true" title="<strong>Message: </strong>{{ $group['message'] }}<br /><strong>Expected Delivery: </strong>{{ $group['expecting_time'] }}">
                            Group ID {{ $group_id }} - expected delivery on {{ \Carbon\Carbon::parse($group['expecting_time'])->format('d-m H:i') }}
                          </div>

                          @if (count($group['image']) > 0)
                            @foreach ($group['image'] as $image)
                              <img src="{{ $image['url'] }}" alt="" class="img-responsive thumbnail-200">
                            @endforeach
                          @elseif (count($group['linked_images']) > 0)
                            @foreach ($group['linked_images'] as $image)
                              <img src="{{ $image['url'] }}" alt="" class="img-responsive thumbnail-200">
                            @endforeach
                          @endif

                          <canvas id="horizontalBroadcastBarChart{{ $date }}{{ $group_id }}" style="height: 120px;"></canvas>
                        </div>
                    @endforeach
                  </div>

                  @php
                    $count++;
                  @endphp
                @endif
              @endforeach

              <div class="col-md-4">
                <h4>Future - till {{ array_search(end($message_groups), $message_groups) }}</h4>
                @foreach ($message_groups as $date => $data)
                  @if ($date > \Carbon\Carbon::now()->format('Y-m-d'))
                    @foreach ($data as $group_id => $group)
                      <div class="card activity-chart mb-3">
                        <div class="card-header" data-toggle="tooltip" data-placement="top" data-html="true" title="<strong>Message: </strong>{{ $group['message'] }}<br /><strong>Expected Delivery: </strong>{{ $group['expecting_time'] }}">
                          Group ID {{ $group_id }} - expected delivery on {{ \Carbon\Carbon::parse($group['expecting_time'])->format('d-m H:i') }}
                        </div>

                        @if (count($group['image']) > 0)
                          @foreach ($group['image'] as $image)
                            <img src="{{ $image['url'] }}" alt="" class="img-responsive thumbnail-200">
                          @endforeach
                        @elseif (count($group['linked_images']) > 0)
                          @foreach ($group['linked_images'] as $image)
                            <img src="{{ $image['url'] }}" alt="" class="img-responsive thumbnail-200">
                          @endforeach
                        @endif

                        <canvas id="horizontalBroadcastBarChart{{ $date }}{{ $group_id }}" style="height: 120px;"></canvas>
                      </div>
                    @endforeach
                  @endif
                @endforeach
              </div>
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
                      <strong>
                        @if (array_key_exists($user_id, $users_array))
                          <a href="{{ url('/') }}?selected_user={{ $user_id }}" target="_blank">{{ $users_array[$user_id] }}</a>
                        @else
                          User Doesnt Exist
                        @endif
                      </strong>

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
                     @if (array_key_exists(0, $task_data))
                       <li class="list-group-item">
                         <strong>{{ array_key_exists($user_id, $users_array) ? $users_array[$user_id] : 'User Doesnt Exist' }}</strong> -
                         <a href="{{ url('/') }}?selected_user={{ $user_id }}#task_{{ $task_data[0][0]['id'] }}" target="_blank">{{ $task_data[0][0]['task_details'] }}</a>
                          on <strong>{{ \Carbon\Carbon::parse($task_data[0][0]['created_at'])->format('d-m') }}</strong>
                       </li>
                     @endif
                  @endforeach

                  {{-- <li class="list-group-item">
                    <strong>{{ array_key_exists($tasks['last_pending']['assign_to'], $users_array) ? $users_array[$tasks['last_pending']['assign_to']] : 'User Doesnt Exist' }}</strong> -
                    <a href="{{ url('/') }}?selected_user={{ $tasks['last_pending']['assign_to'] }}#task_{{ $tasks['last_pending']['id'] }}" target="_blank">{{ $tasks['last_pending']['task_details'] }}</a>
                     on <strong>{{ \Carbon\Carbon::parse($tasks['last_pending']['created_at'])->format('d-m') }}</strong>
                  </li> --}}
                </ul>
              </div>

              <div class="col">
                <ul class="list-group">
                  <li class="list-group-item">
                    <a href="{{ route('instruction.index') }}" target="_blank"><h4>Instructions</h4></a>
                  </li>
                  @foreach ($instructions as $user_id => $data)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <strong>
                        @if (array_key_exists($user_id, $users_array))
                          <a href="{{ route('instruction.index') }}?user%5B%5D={{ $user_id }}" target="_blank">{{ $users_array[$user_id] }}</a>
                        @else
                          User Doesnt Exist
                        @endif
                      </strong>

                      <ul class="list-unstyled">
                        @php $count = 0; $last_pending_user = 0; $last_category_id = 0; @endphp
                        @foreach ($data as $category_id => $info)
                          <li class="d-flex justify-content-between align-items-center">
                            @if (array_key_exists($category_id, $instruction_categories_array) && $instruction_categories_array[$category_id]['icon'] != '')
                              <a href="{{ route('instruction.index') }}?user%5B%5D={{ $user_id }}#instructions_{{ $category_id }}" class="btn btn-image" target="_blank"><img src="/images/{{ $instruction_categories_array[$category_id]['icon'] }}" alt=""></a>
                            @else
                              @if (array_key_exists($category_id, $instruction_categories_array))
                                <a href="{{ route('instruction.index') }}?user%5B%5D={{ $user_id }}#instructions_{{ $category_id }}" target="_blank">{{ $instruction_categories_array[$category_id]['name'] }}</a>
                              @else
                                No Category
                              @endif
                            @endif

                            <span class="ml-2">
                              @if (array_key_exists(0, $info))
                                <span class="badge badge-red badge-pill">{{ count($info[0]) }}</span>
                                @php
                                if ($count == 0) {
                                  $last_pending_user = $user_id;
                                  $last_category_id = $category_id;
                                  $count++;
                                } @endphp
                              @else
                                <span class="badge badge-red badge-pill">0</span>
                              @endif

                              @if (array_key_exists(1, $info))
                                <span class="badge badge-green badge-pill">{{ count($info[1]) }}</span>
                              @else
                                <span class="badge badge-green badge-pill">0</span>
                              @endif
                            </span>
                          </li>
                        @endforeach
                      </ul>
                     </li>
                     @if ($last_pending_user != 0)
                       <li class="list-group-item">
                         <strong>{{ array_key_exists($last_pending_user, $users_array) ? $users_array[$last_pending_user] : 'User Doesnt Exist' }}</strong> -
                         <a href="{{ route('instruction.index') }}?user%5B%5D={{ $instructions[$last_pending_user][$last_category_id][0][0]['assigned_to'] }}#instruction_{{ $instructions[$last_pending_user][$last_category_id][0][0]['id'] }}" target="_blank">{{ $instructions[$last_pending_user][$last_category_id][0][0]['instruction'] }}</a>
                          on <strong>{{ \Carbon\Carbon::parse($instructions[$last_pending_user][$last_category_id][0][0]['created_at'])->format('d-m') }}</strong>
                       </li>
                     @endif
                  @endforeach
                </ul>
              </div>

              <div class="col">
                <ul class="list-group">
                  <li class="list-group-item">
                    <a href="{{ route('development.index') }}" target="_blank"><h4>Developer Tasks</h4></a>
                  </li>
                  @foreach ($developer_tasks as $user_id => $data)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <strong>
                        @if (array_key_exists($user_id, $users_array))
                          <a href="{{ route('development.index') }}?user={{ $user_id }}" target="_blank">{{ $users_array[$user_id] }}</a>
                        @else
                          User Doesnt Exist
                        @endif
                      </strong>

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

                     @if (array_key_exists('0', $data))
                       <li class="list-group-item">
                         <strong>{{ array_key_exists($user_id, $users_array) ? $users_array[$user_id] : 'User Doesnt Exist' }}</strong> -
                         <a href="{{ route('development.index') }}?user={{ $user_id }}#task_{{ $data['0'][0]['id'] }}" target="_blank">{{ $data['0'][0]['task'] }}</a>
                          on <strong>{{ \Carbon\Carbon::parse($data['0'][0]['created_at'])->format('d-m') }}</strong>
                       </li>
                     @endif
                  @endforeach
                </ul>
              </div>
            </div>

          </div>

          <div class="tab-pane mt-3" id="orders-tab">
            <div class="pull-left">
              <a href="{{ route('order.index') }}" target="_blank"><h4>Orders</h4></a>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Total Orders</th>
                    <th>Total Value</th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td>
                      {{ count($orders['orders']) }}
                    </td>
                    <td>
                      @php
                        $total_value = 0;
                      @endphp

                      @foreach ($orders['orders'] as $order)
                        @if ($order->order_product)
                          @foreach ($order->order_product as $order_product)
                            @php
                              $total_value += (int) $order_product->qty * (int) $order_product->product_price;
                            @endphp
                          @endforeach
                        @endif
                      @endforeach

                      {{ $total_value }}
                    </td>
                  </tr>
                </tbody>
                {{-- <thead>
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
                      @if (count($order->order_product) > 0)
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
                            {{ (int) $order->order_product[0]->product_price * $order->order_product[0]->qty }}
                        </td>
                      @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                      @endif
                    </tr>
                  @endforeach
                </tbody> --}}
              </table>
            </div>
          </div>

          <div class="tab-pane mt-3" id="purchases-tab">
            <a href="{{ route('purchase.index') }}" target="_blank"><h4>Purchases</h4></a>

            <div id="purchaseAccordion">
              @foreach ($purchases as $supplier_id => $data)
                <div class="card">
                  <div class="card-header" id="headingPurchase{{ $supplier_id }}">
                    <h5 class="mb-0">
                      <button class="btn btn-link collapsed collapse-fix" data-toggle="collapse" data-target="#purchase{{ $supplier_id }}" aria-expanded="false" aria-controls="purchase{{ $supplier_id }}">
                        <strong>{{ array_key_exists($supplier_id, $suppliers_array) ? $suppliers_array[$supplier_id] : 'Supplier doesn`t exist' }}</strong>

                        @php $sold_price = 0; $actual_price = 0; @endphp
                        @foreach ($data as $purchase)
                          @foreach ($purchase['products'] as $product)
                            @php $actual_price += $product['price'] @endphp

                            @foreach ($product['orderproducts'] as $order_product)
                              @php
                                $sold_price += $order_product['product_price'];
                              @endphp
                            @endforeach
                          @endforeach
                        @endforeach

                        Gross Profit: {{ $sold_price - ($actual_price * 78) }}
                      </button>
                    </h5>
                  </div>
                  <div id="purchase{{ $supplier_id }}" class="collapse collapse-element" aria-labelledby="headingPurchase{{ $supplier_id }}" data-parent="#purchaseAccordion">
                    <div class="card-body">
                      <div class="table-responsive">
                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>Purchase ID</th>
                              <th>Status</th>
                              <th>Date</th>
                              <th>Customers</th>
                              <th>Products</th>
                              {{-- <th>Qty</th> --}}
                              <th>Retail Price</th>
                              {{-- <th>Sold Price</th> --}}
                              <th>Buying Price</th>
                              <th>Gross Profit</th>
                            </tr>
                          </thead>

                          <tbody>
                            @foreach ($data as $purchase)
                              @php
                                $purchase_products_count = 1;
                                if (count($purchase['products']) > 0) {
                                  $purchase_products_count = count($purchase['products']) + 1;
                                }
                              @endphp
                                <tr>
                                  <td rowspan="{{ $purchase_products_count }}"><a href="{{ route('purchase.show', $purchase['id']) }}" target="_blank">{{ $purchase['id'] }}</a></td>
                                  <td rowspan="{{ $purchase_products_count }}">{{ $purchase['status'] }}</td>
                                  <td rowspan="{{ $purchase_products_count }}">{{ Carbon\Carbon::parse($purchase['created_at'])->format('d-m-Y') }}</td>
                                  {{-- <td rowspan="{{ $purchase_products_count }}">{{ Carbon\Carbon::parse($purchase['created_at'])->format('d-m-Y') }}</td> --}}
                                  {{-- <td rowspan="{{ $purchase_products_count }}">{{ $purchase['purchase_handler'] ? $users[$purchase['purchase_handler']] : 'nil' }}</td> --}}
                                  {{-- <td rowspan="{{ $purchase_products_count }}">{{ $purchase['purchase_supplier']['supplier'] }}</td> --}}
                                  {{-- <td rowspan="{{ $purchase_products_count }}">{{ $purchase['status']}}</td> --}}
                                </tr>

                                @if ($purchase['products'])
                                  @php
                                    $qty = 0;
                                    $sold_price = 0;
                                  @endphp
                                  @foreach ($purchase['products'] as $product)
                                    <tr>
                                      <td>
                                        @if ($product['orderproducts'])
                                          {{-- <ul> --}}
                                            @foreach ($product['orderproducts'] as $order_product)
                                              <li>
                                                @if ($order_product['order'])
                                                  @if ($order_product['order']['customer'])
                                                    <a href="{{ route('customer.show', $order_product['order']['customer']['id']) }}" target="_blank">{{ $order_product['order']['customer']['name'] }}</a>
                                                  @else
                                                    No Customer
                                                  @endif
                                                @else
                                                  No Order
                                                @endif

                                                 - Qty. <strong>{{ $qty = $order_product['qty'] }}</strong>
                                                 - Sold Price: <strong>{{ $order_product['product_price'] }}</strong>

                                                @php
                                                  $sold_price += $order_product['product_price'];
                                                @endphp
                                              </li>
                                              @php $qty = 0; @endphp
                                            @endforeach
                                          {{-- </ul> --}}
                                        @else
                                          <li>No Order Product</li>
                                        @endif
                                      </td>
                                      <td>
                                        <img src="{{ $product['imageurl'] }}" class="img-responsive" width="50px">
                                      </td>
                                      {{-- <td>
                                        @if (count($product['orderproducts']) > 0)
                                          <ul>
                                            @foreach ($product['orderproducts'] as $order_product)
                                              <li>{{ $qty = $order_product['qty'] }}</li>
                                              @php

                                                $qty = 0;
                                              @endphp
                                            @endforeach
                                          </ul>
                                        @endif
                                      </td> --}}
                                      <td>{{ $product['price'] }}</td>
                                      {{-- <td>
                                        @php $sold_price = 0; @endphp
                                        <ul>
                                          @foreach ($product['orderproducts'] as $order_product)
                                            <li>{{ $order_product['product_price'] }}</li>

                                            @php
                                              $sold_price += $order_product['product_price'];
                                            @endphp
                                          @endforeach
                                        </ul>
                                      </td> --}}
                                      <td>
                                        @php $actual_price = 0; @endphp
                                        @php $actual_price += $product['price'] @endphp

                                        {{ $product['price'] * 78 }}
                                      </td>
                                      <td>
                                        {{ $sold_price - ($actual_price * 78) }}
                                      </td>

                                    </tr>
                                  @endforeach
                                @endif
                            @endforeach
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="tab-pane mt-3" id="products-tab">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th><a href="{{ route('scrap.activity') }}" target="_blank">Scraped</a></th>
                    <th><a href="{{ route('products.listing') }}" target="_blank">Scraped Created</a></th>
                    <th><a href="{{ route('products.listing') }}" target="_blank">Listed</a></th>
                    <th><a href="{{ route('scrap.activity') }}" target="_blank">Inventory</a></th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td>
                      <ul class="list-group">
                        @foreach ($scraped_count as $data)
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $data->website }}

                            <span class="badge badge-pill">{{ $data->total }}</span>
                           </li>
                        @endforeach
                      </ul>
                    </td>
                    <td>
                      <ul class="list-group">
                        @foreach ($products_count as $data)
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $data->website }}

                            <span class="badge badge-pill">{{ $data->total }}</span>
                           </li>
                        @endforeach
                      </ul>
                    </td>
                    <td>
                      <ul class="list-group">
                        @foreach ($listed_days_ago_count as $data)
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $data->website }}

                            <span class="badge badge-pill">{{ $data->total }}</span>
                           </li>
                        @endforeach
                      </ul>
                    </td>
                    <td>
                      <ul class="list-group">
                        @foreach ($inventory_data as $website => $data)
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $website }}

                            <span>
                              @if (array_key_exists('0', $data))
                                <span class="badge badge-red badge-pill">{{ $data['0'] }}</span>
                              @else
                                <span class="badge badge-red badge-pill">0</span>
                              @endif

                              @if (array_key_exists('1', $data))
                                <span class="badge badge-green badge-pill">{{ $data['1'] }}</span>
                              @else
                                <span class="badge badge-green badge-pill">0</span>
                              @endif
                            </span>
                           </li>
                        @endforeach
                      </ul>
                    </td>
                    {{-- <td>
                      <ul class="list-group">
                        @foreach ($scraped_days_ago_count as $data)
                          <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $data->website }}

                            <span class="badge badge-pill">{{ $data->total }}</span>
                           </li>
                        @endforeach
                      </ul>
                    </td> --}}

                    {{-- <td>

                    </td> --}}
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="tab-pane mt-3" id="reviews-tab">
            <div class="row">
              <div class="col-md-4">
                <ul class="list-group">
                  <li class="list-group-item">
                    <a href="{{ route('review.index') }}" target="_blank"><h4>Reviews</h4></a>
                  </li>
                  @foreach ($reviews as $platform => $data)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <strong>
                        @if ($platform == '')
                          No Platform
                        @else
                          {{ ucwords($platform) }}
                        @endif
                      </strong>

                      <span>
                        <span class="badge badge-red badge-pill">{{ $data['notposted'] }}</span>

                        <span class="badge badge-green badge-pill">{{ $data['posted'] }}</span>
                      </span>
                     </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>

          <div class="tab-pane mt-3" id="emails-tab">
            <div class="row">
              <div class="col-xs-4">
                <div class="col">
                  <ul class="list-group">
                    <li class="list-group-item">
                      <a href="{{ route('supplier.index') }}" target="_blank"><h4>Suppliers</h4></a>
                    </li>
                    @foreach ($emails as $supplier_id => $data)
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>
                          @if (array_key_exists($supplier_id, $suppliers_array))
                            <a href="{{ route('supplier.show', $supplier_id) }}" target="_blank">{{ $suppliers_array[$supplier_id] }}</a>
                          @else
                            Supplier Doesnt Exist
                          @endif
                        </strong>

                        <span>
                          {{-- @if (array_key_exists('no', $data)) --}}
                            <span class="badge badge-red badge-pill">{{ $data['0'] }}</span>
                          {{-- @else
                            <span class="badge badge-red badge-pill">0</span>
                          @endif --}}

                          {{-- {{dd($data)}} --}}
                          {{-- @if (array_key_exists('yes', $data)) --}}
                            <span class="badge badge-green badge-pill">{{ $data['1'] }}</span>
                          {{-- @else
                            <span class="badge badge-green badge-pill">0</span>
                          @endif --}}
                        </span>
                       </li>
                    @endforeach

                    {{-- <li class="list-group-item">
                      <strong>{{ array_key_exists($last_pending_instruction['assigned_to'], $users_array) ? $users_array[$last_pending_instruction['assigned_to']] : 'User Doesnt Exist' }}</strong> -
                      <a href="{{ route('instruction.index') }}?user%5B%5D={{ $last_pending_instruction['assigned_to'] }}" target="_blank">{{ $last_pending_instruction['instruction'] }}</a>
                       on <strong>{{ \Carbon\Carbon::parse($last_pending_instruction['created_at'])->format('d-m') }}</strong>
                    </li> --}}
                  </ul>
                </div>
              </div>
            </div>
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
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
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

    let r_s = '{{ $start }}';
    let r_e = '{{ $end }}';

    let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(1, 'days');
    let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();

    jQuery('input[name="range_start"]').val(start.format('YYYY-MM-DD'));
    jQuery('input[name="range_end"]').val(end.format('YYYY-MM-DD'));

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        maxYear: 1,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    cb(start, end);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {

        jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
        jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));

    });
    var tabs = [];
    var red_tabs = localStorage['red_tabs'];

    if (red_tabs) {
      tabs = JSON.parse(red_tabs);
      tabs.forEach(function(index) {
        $('a[href="' + index + '"]').addClass('text-danger');
      });
    }

    $('#exTab2 li').on('dblclick', function() {
      var href = $(this).find('a').attr('href');

      if (red_tabs) {
        tabs = JSON.parse(red_tabs);
        console.log(red_tabs);

        if (tabs.indexOf(href) < 0) {
          tabs.push(href);
        } else {
          tabs.splice(tabs.indexOf(href), 1);
        }

        localStorage['red_tabs'] = JSON.stringify(tabs);
        red_tabs = localStorage['red_tabs'];

      } else {
        tabs.push(href);
        localStorage['red_tabs'] = JSON.stringify(tabs);
        red_tabs = localStorage['red_tabs'];
      }

      $(this).find('a').toggleClass('text-danger');
    });
  </script>
@endsection
