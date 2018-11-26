@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Leads</h2>

                <form action="/leads/" method="GET">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                          <div class="row">
                              <div class="col-md-8 pr-0">
                                  <input name="term" type="text" class="form-control"
                                         value="{{ isset($term) ? $term : '' }}"
                                         placeholder="Search">
                              </div>
                              <div class="col-md-4 pl-0">
                                  <button type="submit" class="btn btn-primary">Filter</button>
                              </div>
                          </div>
                      </div>
                    </div>

                    <div class="col-md-2">
                      <strong>Brands</strong>
                      @php $brands = \App\Brand::getAll(); @endphp
                      {!! Form::select('brand[]',$brands, (isset($brand) ? $brand : ''), ['placeholder' => 'Select a Brand','class' => 'form-control', 'multiple' => true]) !!}
                    </div>

                    <div class="col-md-2">
                      <strong>Rating</strong>
                      <select name="rating[]" class="form-control" multiple>
                              <option value>Select Rating</option>
                              <option value="1" {{ isset($rating) && in_array(1, $rating) ? 'selected' : '' }}>1</option>
                              <option value="2" {{ isset($rating) && in_array(2, $rating) ? 'selected' : '' }}>2</option>
                              <option value="3" {{ isset($rating) && in_array(3, $rating) ? 'selected' : '' }}>3</option>
                              <option value="4" {{ isset($rating) && in_array(4, $rating) ? 'selected' : '' }}>4</option>
                              <option value="5" {{ isset($rating) && in_array(5, $rating) ? 'selected' : '' }}>5</option>
                              <option value="6" {{ isset($rating) && in_array(6, $rating) ? 'selected' : '' }}>6</option>
                              <option value="7" {{ isset($rating) && in_array(7, $rating) ? 'selected' : '' }}>7</option>
                              <option value="8" {{ isset($rating) && in_array(8, $rating) ? 'selected' : '' }}>8</option>
                              <option value="9" {{ isset($rating) && in_array(9, $rating) ? 'selected' : '' }}>9</option>
                              <option value="10" {{ isset($rating) && in_array(10, $rating) ? 'selected' : '' }}>10</option>
                      </select>
                    </div>
                  </div>
                </form>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('leads.create') }}"> Create New Leads</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered" style="margin-top: 25px">
        <tr>
            <th><a href="/leads?sortby=id{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">ID</a></th>
            <th><a href="/leads?sortby=client_name{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Client Name</a></th>
            <th><a href="/leads?sortby=city{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">City</a></th>
            <th><a href="/leads?sortby=rating{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Rating</a></th>
            <th><a href="/leads?sortby=assigned_user{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Assigned to</a></th>
            <th>Products</th>
            <th><a href="/leads?sortby=communication{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Communication</a></th>
            <th><a href="/leads?sortby=status{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Status</a></th>
            <th><a href="/leads?sortby=created_at{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Created</a></th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($leads_array as $key => $lead)
            <tr class="{{ \App\Helpers::statusClass($lead['assign_status'] ) }}">
                <td>{{ $lead['id'] }}</td>
                <td>{{ $lead['client_name'] }}</td>
                <td>{{ $lead['city']}}</td>
                <td>{{ $lead['rating']}}</td>
                <td>{{App\User::find($lead['assigned_user'])->name}}</td>
                <td>{{App\Helpers::getproductsfromarraysofids($lead['selected_product'])}}</td>
                <td>
                  @if (strpos($lead['communication']['body'], 'message-img') !== false)
                    {{ substr($lead['communication']['body'], 0, strpos($lead['communication']['body'], '<img')) }}
                  @else
                    {{ $lead['communication']['body'] }}
                  @endif
                </td>
                <td>{{App\Helpers::getleadstatus($lead['status'])}}</td>
                <td>{{ Carbon\Carbon::parse($lead['created_at'])->format('d-m H:i') }}</td>
                <td>
                    <a class="btn btn-primary" href="{{ route('leads.show',$lead['id']) }}">View</a>
                    <a class="btn btn-primary" href="{{ route('leads.edit',$lead['id']) }}">Edit</a>

                    {!! Form::open(['method' => 'DELETE','route' => ['leads.destroy', $lead['id']],'style'=>'display:inline']) !!}
                    {!! Form::submit('Archive', ['class' => 'btn btn-info']) !!}
                    {!! Form::close() !!}

                    @can('admin')
                        {!! Form::open(['method' => 'DELETE','route' => ['leads.permanentDelete', $lead['id']],'style'=>'display:inline']) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>

    {!! $leads->appends(Request::except('page'))->links() !!}
    {{--{!! $leads->links() !!}--}}

@endsection
