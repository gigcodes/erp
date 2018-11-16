@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Leads</h2>

                <form action="/leads/" method="GET">
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
            <th>Communication</th>
            <th><a href="/leads?sortby=status{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Status</a></th>
            <th><a href="/leads?sortby=created_at{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Created</a></th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($leads as $key => $lead)
            <tr class="{{ \App\Helpers::statusClass($lead->assign_status ) }}">
                <td>{{ $lead->id }}</td>
                <td>{{ $lead->client_name }}</td>
                <td>{{ $lead->city}}</td>
                <td>{{ $lead->rating}}</td>
                <td>{{App\User::find($lead->assigned_user)->name}}</td>
                <td>{{App\Helpers::getproductsfromarraysofids($lead->selected_product)}}</td>
                <td>
                  @if (strpos(App\Helpers::getlatestmessage($lead->id, 'leads'), 'message-img') !== false)
                    {{ substr(App\Helpers::getlatestmessage($lead->id, 'leads'), 0, strpos(App\Helpers::getlatestmessage($lead->id, 'leads'), '<img')) }}
                  @else
                    {{ App\Helpers::getlatestmessage($lead->id, 'leads') }}
                  @endif
                </td>
                <td>{{App\Helpers::getleadstatus($lead->status)}}</td>
                <td>{{ $lead->created_at }}</td>
                <td>
                    <a class="btn btn-primary" href="{{ route('leads.show',$lead->id) }}">View</a>
                    <a class="btn btn-primary" href="{{ route('leads.edit',$lead->id) }}">Edit</a>

                    {!! Form::open(['method' => 'DELETE','route' => ['leads.destroy', $lead->id],'style'=>'display:inline']) !!}
                    {!! Form::submit('Archive', ['class' => 'btn btn-info']) !!}
                    {!! Form::close() !!}

                    @can('admin')
                        {!! Form::open(['method' => 'DELETE','route' => ['leads.permanentDelete', $lead->id],'style'=>'display:inline']) !!}
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
