@extends('layouts.app')

@section('content')

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">

    <div class="row">
        <div class="col-lg-12 margin-tb">
                <h2 class="page-heading">Customers List</h2>
                <div class="pull-left">
                  <form action="/customers/" method="GET">
                      <div class="form-group">
                          <div class="row">
                              <div class="col-md-12">
                                  <input name="term" type="text" class="form-control"
                                         value="{{ isset($term) ? $term : '' }}"
                                         placeholder="Search">
                              </div>
                              <div class="col-md-4">
                                  <button hidden type="submit" class="btn btn-primary">Submit</button>
                              </div>
                          </div>
                      </div>
                  </form>
                </div>

            <div class="pull-right mt-4">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#mergeModal">Merge Customers</button>
                <a class="btn btn-secondary" href="{{ route('customer.create') }}">+</a>
            </div>
        </div>
    </div>

    <div id="mergeModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Merge Customers</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <form action="{{ route('customer.merge') }}" method="POST">
            <div class="modal-body">
              <div class="form-group">
                  <strong>Main Client:</strong>
                  <select class="selectpicker form-control" data-live-search="true" data-size="15" name="first_customer" id="first_customer" title="Choose a Main Customer" required>
                    @foreach ($customers_all as $customer)
                     <option data-tokens="{{ $customer['name'] }} {{ $customer['email'] }}  {{ $customer['phone'] }} {{ $customer['instahandler'] }}" value="{{ $customer['id'] }}">{{ $customer['id'] }} - {{ $customer['name'] }} - {{ $customer['phone'] }}</option>
                   @endforeach
                 </select>

                  @if ($errors->has('first_customer'))
                      <div class="alert alert-danger">{{$errors->first('first_customer')}}</div>
                  @endif
              </div>

              <div class="form-group">
                  <strong>Additional Client:</strong>
                  <select class="selectpicker form-control" data-live-search="true" data-size="15" name="second_customer" id="second_customer" title="Choose a Main Customer" required>
                    @foreach ($customers_all as $customer)
                     <option data-tokens="{{ $customer['name'] }} {{ $customer['email'] }}  {{ $customer['phone'] }} {{ $customer['instahandler'] }}" value="{{ $customer['id'] }}">{{ $customer['id'] }} - {{ $customer['name'] }} - {{ $customer['phone'] }}</option>
                   @endforeach
                 </select>

                  @if ($errors->has('second_customer'))
                      <div class="alert alert-danger">{{$errors->first('second_customer')}}</div>
                  @endif
              </div>

              <div class="form-group">
                <button type="button" class="btn btn-secondary load-customers">Load Data</button>
              </div>

              <div class="row" id="customers-data" style="display: none;">
                <div class="col-md-6">
                  @csrf
                  <input type="hidden" name="first_customer_id" id="first_customer_id" value="">
                  <input type="hidden" name="second_customer_id" id="second_customer_id" value="">
                  <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" class="form-control" name="name" placeholder="Client Name" id="first_customer_name" value="" required />
                  </div>

                  <div class="form-group">
                    <strong>Email:</strong>
            				<input type="email" class="form-control" name="email" placeholder="example@example.com" id="first_customer_email" value=""/>
                  </div>

                  <div class="form-group">
                    <strong>Phone:</strong>
            				<input type="number" class="form-control" name="phone" placeholder="900000000" id="first_customer_phone" value="" />
                  </div>

                  <div class="form-group">
                    <strong>Instagram Handle:</strong>
            				<input type="text" class="form-control" name="instahandler" placeholder="instahandle" id="first_customer_instahandler" value="" />
                  </div>

                  <div class="form-group">
            				<strong>Rating:</strong>
            				<Select name="rating" class="form-control" id="first_customer_rating" required>
      								<option value="1">1</option>
      								<option value="2">2</option>
      								<option value="3">3</option>
      								<option value="4">4</option>
      								<option value="5">5</option>
      								<option value="6">6</option>
      								<option value="7">7</option>
      								<option value="8">8</option>
      								<option value="9">9</option>
      								<option value="10">10</option>
            				</Select>
            			</div>

                  <div class="form-group">
                    <strong>Address:</strong>
            				<input type="text" class="form-control" name="address" placeholder="Street, Apartment" id="first_customer_address" value="" />
                  </div>

                  <div class="form-group">
                    <strong>City:</strong>
            				<input type="text" class="form-control" name="city" placeholder="Mumbai" id="first_customer_city" value="" />
                  </div>

                  <div class="form-group">
                    <strong>Country:</strong>
            				<input type="text" class="form-control" name="country" placeholder="India" id="first_customer_country" value="" />
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" class="form-control" id="second_customer_name" value="" readonly />
                  </div>

                  <div class="form-group">
                    <strong>Email:</strong>
                    <input type="email" class="form-control" id="second_customer_email" value="" readonly />
                  </div>

                  <div class="form-group">
                    <strong>Phone:</strong>
                    <input type="number" class="form-control" id="second_customer_phone" value="" readonly />
                  </div>

                  <div class="form-group">
                    <strong>Instagram Handle:</strong>
                    <input type="text" class="form-control" id="second_customer_instahandler" value="" readonly />
                  </div>

                  <div class="form-group">
                    <strong>Rating:</strong>
                    <input type="text" class="form-control" id="second_customer_rating" readonly />
                  </div>

                  <div class="form-group">
                    <strong>Address:</strong>
                    <input type="text" class="form-control" id="second_customer_address" value="" readonly />
                  </div>

                  <div class="form-group">
                    <strong>City:</strong>
                    <input type="text" class="form-control" id="second_customer_city" value="" readonly />
                  </div>

                  <div class="form-group">
                    <strong>Country:</strong>
                    <input type="text" class="form-control" id="second_customer_country" value="" readonly />
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary" id="mergeButton" disabled>Merge</button>
            </div>
          </form>
        </div>

      </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($message = Session::get('warning'))
        <div class="alert alert-warning">
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

    <table class="table table-bordered">
        <tr>
          <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=name{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Name</a></th>
          @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
            <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=email{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Email</a></th>
            <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=phone{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Phone</a></th>
            <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=instagram{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Instagram</a></th>
          @endif
          <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=rating{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Lead Rating</a></th>
          <th>Lead/Order Status</th>
          <th>Message Status</th>
          <th><a href="/customers{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=communication{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Communication</a></th>
          <th width="150px">Action</th>
        </tr>
        @foreach ($customers as $key => $customer)
            <tr class="{{ ((!empty($customer['communication']['body']) && $customer['communication']['status'] == 0) || $customer['communication']['status'] == 1 || $customer['communication']['status'] == 5) ? 'row-highlight' : '' }} {{ ((!empty($customer['communication']['message']) && $customer['communication']['status'] == 0) || $customer['communication']['status'] == 1 || $customer['communication']['status'] == 5) ? 'row-highlight' : '' }}">
              <td>{{ $customer['name'] }}</td>
              @if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('HOD of CRM'))
                <td>{{ $customer['email'] }}</td>
                <td>{{ $customer['phone'] }}</td>
                <td>{{ $customer['instahandler'] }}</td>
              @endif
              <td>
                @if ($customer['lead'])
                  {{ $customer['lead']['rating'] }}
                @endif
              </td>
              <td>
                @if ($customer['lead'])
                  @php $status = array_flip((new \App\Status)->all()); @endphp
                  {{ $status[$customer['lead']['status']] }}
                @endif
                {{ $customer['lead'] && $customer['order'] ? ' / ' : '' }}
                @if ($customer['order'])
                  {{ $customer['order']['order_status'] }}
                @endif
              </td>
              <td>
                @if (!empty($customer['communication']['body']))
                  @if ($customer['communication']['status'] == 5 || $customer['communication']['status'] == 3)
                    Read
                  @elseif ($customer['communication']['status'] == 6)
                    Replied
                  @elseif ($customer['communication']['status'] == 1)
                    <span>Awaiting Approval</span>
                    {{-- <a href data-url="/message/updatestatus?status=2&id={{ $customer['communication']['id'] }}&moduleid={{ $customer['communication']['moduleid'] }}&moduletype={{ $customer['communication']['moduletype'] }}" style="font-size: 9px" class="change_message_status">Approve</a> --}}
                  @elseif ($customer['communication']['status'] == 2)
                    Approved
                  @elseif ($customer['communication']['status'] == 4)
                    Internal Message
                  @elseif ($customer['communication']['status'] == 0)
                    Unread
                  @endif
                @endif

                @if (!empty($customer['communication']['message']))
                  @if ($customer['communication']['status'] == 5)
                    Read
                  @elseif ($customer['communication']['status'] == 6)
                    Replied
                  @elseif ($customer['communication']['status'] == 1)
                    <span>Awaiting Approval</span>
                    {{-- <a href data-url="/whatsapp/approve/orders?messageId={{ $customer['communication']['id'] }}" style="font-size: 9px" class="change_message_status approve-whatsapp" data-messageid="{{ $customer['communication']['id'] }}">Approve</a> --}}
                  @elseif ($customer['communication']['status'] == 2)
                    Approved
                  @elseif ($customer['communication']['status'] == 0)
                    Unread
                  @endif
                @endif
              </td>
              <td>
                @if (isset($customer['communication']['body']))
                  @if (strpos($customer['communication']['body'], '<br>') !== false)
                    {{ substr($customer['communication']['body'], 0, strpos($customer['communication']['body'], '<br>')) }}
                  @else
                    {{ $customer['communication']['body'] }}
                  @endif
                @else
                  {{ $customer['communication']['message'] }}
                @endif
              </td>
              <td>
                <a class="btn btn-image" href="{{ route('customer.show', $customer['id']) }}"><img src="/images/view.png" /></a>
                <a class="btn btn-image" href="{{ route('customer.edit',$customer['id']) }}"><img src="/images/edit.png" /></a>

                {!! Form::open(['method' => 'DELETE','route' => ['customer.destroy', $customer['id']],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                {!! Form::close() !!}
              </td>
            </tr>
        @endforeach
    </table>

    {!! $customers->appends(Request::except('page'))->links() !!}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
    <script type="text/javascript">
      $('.load-customers').on('click', function() {
        var thiss = $(this);
        var first_customer = $('#first_customer').val();
        var second_customer = $('#second_customer').val();

        if (first_customer == second_customer) {
          alert('You selected the same customers');

          return;
        }

        $.ajax({
          type: "GET",
          url: "{{ route('customer.load') }}",
          data: {
            first_customer: first_customer,
            second_customer: second_customer
          },
          beforeSend: function() {
            $(thiss).text('Loading...');
          }
        }).done(function(response) {
          $('#first_customer_id').val(response.first_customer.id);
          $('#second_customer_id').val(response.second_customer.id);

          $('#first_customer_name').val(response.first_customer.name);
          $('#first_customer_email').val(response.first_customer.email);
          $('#first_customer_phone').val(response.first_customer.phone ? (response.first_customer.phone).replace(/[\s+]/g, '') : '');
          $('#first_customer_instahandler').val(response.first_customer.instahandler);
          $('#first_customer_rating').val(response.first_customer.rating);
          $('#first_customer_address').val(response.first_customer.address);
          $('#first_customer_city').val(response.first_customer.city);
          $('#first_customer_country').val(response.first_customer.country);

          $('#second_customer_name').val(response.second_customer.name);
          $('#second_customer_email').val(response.second_customer.email);
          $('#second_customer_phone').val(response.second_customer.phone ? (response.second_customer.phone).replace(/[\s+]/g, '') : '');
          $('#second_customer_instahandler').val(response.second_customer.instahandler);
          $('#second_customer_rating').val(response.second_customer.rating);
          $('#second_customer_address').val(response.second_customer.address);
          $('#second_customer_city').val(response.second_customer.city);
          $('#second_customer_country').val(response.second_customer.country);

          $('#customers-data').show();
          $('#mergeButton').prop('disabled', false);
          $(thiss).text('Load Data');
        }).fail(function(response) {
          console.log(response);
          alert('There was error loading customers data');
        });
      });
    </script>

@endsection
