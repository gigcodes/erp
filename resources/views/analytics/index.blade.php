@extends('layouts.app')
@section('title', 'Analytics Data')
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Analytics Data</h2>
    </div>
    <form action="{{route('filteredAnalyticsResults')}}" method="get" class="form-inline col-lg-12 p-0">
    <div class="margin-tb" style="padding:0">
        <div class="col-md-4">
                <div class="form-group col-md-6 p-0">
                    <input name="start_date" type="date" placeholder="Start Date" class="form-control"
                        value="{{!empty(request()->start_date) ? request()->start_date : ''}}">
                </div>
                <div class="form-group col-md-6">
                    <input name="end_date" type="date" placeholder="End Date" class="form-control"
                        value="{{!empty(request()->end_date) ? request()->end_date : ''}}">
                </div>
        </div>
        <div class="col-md-8">
            <div class="row">
        <div class="col-md-3">
                <div class="form-group">
                    <input name="location" type="text" placeholder="Country" class="form-control"
                        value="{{!empty(request()->location) ? request()->location : ''}}" style="width:100%">
                </div>
        </div>
        <div class="col-md-3">
                <div class="form-group">
                    {!! Form::select('user', $visitors, request()->user, ['class' => 'form-control']) !!}
                </div>
        </div>
        <div class="col-md-3">
                <div class="form-group">
                    <input name="device_os" type="text" placeholder="Device/OS" class="form-control"
                        value="{{!empty(request()->device_os) ? request()->device_os : ''}}" style="width:100%">
                </div>
        </div>
        <div class="form-group col-md-3" style="text-align:right">
            <button class="btn btn-default">Submit</button>
        </div>
    </div>
</div>
    </div>
    @php
        $new_data = App\Helpers::customPaginator(request(), $data, 100);
    @endphp
    <div class="col-md-12 pt-3" style="text-align:end">
        {!! $new_data->links() !!}
        <a href="{{url('display/analytics-summary')}}" class="btn btn-primary">Analytics Data Summary</a>
    </div>
</form>
    {{-- <form action="{{route('filteredAnalyticsResults')}}" method="get" class="form-inline float-right">
    <div class="form-group">
        <div class="col-md-4 col-lg-6 col-xl-6">
            <input name="location" type="text" placeholder="City/Country" class="form-control"
                value="{{!empty(request()->location) ? request()->location : ''}}">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-4 col-lg-6 col-xl-6">
            <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
        </div>
    </div>
    </form> --}}
</div>
<div class="row mt-5">
    <div class="container-fluid">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">Date</th>
                        <th scope="col" class="text-center">Time</th>
                        <th scope="col" class="text-center">Location</th>
                        <th scope="col" class="text-center">Pages</th>
                        <th scope="col" class="text-center">Avg. Time Spent</th>
                        <th scope="col" class="text-center">New/Returning User</th>
                        <th scope="col" class="text-center">Device/OS</th>
                        <th scope="col" class="text-center">Bounce Rate</th>
                        <th scope="col" class="text-center">Source</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($new_data as $key => $item)
                    <tr>
                        <td>{{\Carbon\Carbon::parse($item['date'])->format('d M, Y')}}</td>
                        <td>{{$item['time']}}mins</td>
                        <td>{{$item['city']}},{{$item['country']}}</td>
                        <td width=30%>{{$item['page_path']}}</td>
                        <td>{{$item['avgSessionDuration']}}secs</td>
                        <td>{{$item['user_type']}}</td>
                        <td>{{($item['device_info'] === '(not set)' ? 'N/A' : $item['device_info'])}}/{{($item['operatingSystem'] === '(not set)' ? 'N/A' : $item['operatingSystem'])}}
                        </td>
                        <td>{{$item['bounceRate']}}</td>
                        <td>{{($item['social_network'] === '(not set)' ? 'N/A' : $item['social_network'])}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="col-md-12 text-center">
                {!! $new_data->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection