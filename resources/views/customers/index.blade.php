@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Customers List</h2>

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
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('customer.create') }}">+</a>
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

@endsection
