@extends('layouts.app')
@section('title', 'Analytics Data')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Analytics Data</h2>
        </div>
        <form action="{{route('filteredAnalyticsResults')}}" method="get" class="form-inline">
            <div class="form-group">
                <div class="col-md-4 col-lg-6 col-xl-6">
                    <label for="start_date">Start Date</label>
                    <input name="start_date" type="date" placeholder="Start Date" class="form-control" value="{{!empty(request()->start_date) ? request()->start_date : ''}}">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4 col-lg-6 col-xl-6">
                    <label for="end_date">End Date</label>
                    <input name="end_date" type="date" placeholder="End Date" class="form-control" value="{{!empty(request()->end_date) ? request()->end_date : ''}}">
                </div>
            </div>
            <div class="form-group mt-4">
                <div class="col-md-4 col-lg-6 col-xl-6">
                    <button class="btn btn-default">Submit</button>
                </div>
            </div>
        </form>
        {{-- <form action="{{route('filteredAnalyticsResults')}}" method="get" class="form-inline float-right">
            <div class="form-group">
                <div class="col-md-4 col-lg-6 col-xl-6">
                    <input name="location" type="text" placeholder="City/Country" class="form-control" value="{{!empty(request()->location) ? request()->location : ''}}">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4 col-lg-6 col-xl-6">
                    <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                </div>
            </div>
        </form> --}}
    </div>
    <div class="row">
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
                        @foreach ($data as $key => $items)
                        @php
                            $new_items = App\Http\Controllers\AnalyticsController::customPaginator(request(), $items, 100);
                        @endphp
                            @foreach ($new_items as $key => $new_item)
                                <tr>
                                    <td>{{\Carbon\Carbon::parse($new_item['date'])->format('d M, Y')}}</td>
                                    <td>{{$new_item['time']}}mins</td>
                                    <td>{{$new_item['city']}},{{$new_item['country']}}</td>
                                    <td width=30%>{{url($new_item['page_path'])}}</td>
                                    <td>{{$new_item['avgSessionDuration']}}secs</td>
                                    <td>{{$new_item['user_type']}}</td>
                                    <td>{{($new_item['device_info'] === '(not set)' ? 'N/A' : $new_item['device_info'])}}/{{($new_item['operatingSystem'] === '(not set)' ? 'N/A' : $new_item['operatingSystem'])}}</td>
                                    <td>{{$new_item['bounceRate']}}</td>
                                    <td>{{($new_item['social_network'] === '(not set)' ? 'N/A' : $new_item['social_network'])}}</td>
                                </tr>  
                            @endforeach  
                        @endforeach
                    </tbody>
                </table>
                <div class="col-md-12 text-center">
                    {!! $new_items->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection