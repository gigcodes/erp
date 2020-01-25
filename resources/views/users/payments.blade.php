@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Payments</h2>
    </div>
</div>
<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Hours Worked</th>
                <th>Currency</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>
                    <a href="#{{$user->id}}-expandable" data-toggle="collapse" aria-expanded="false">{{$user->name}}</span>
                </td>
                <td>{{$user->secondsTracked / 3600 }}</td>
                <td>{{$user->currency}}</td>
                <td>{{$user->total}}</td>
            </tr>
            <tr id="{{$user->id}}-expandable" class="collapse">
                <td colspan="4">
                    <div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Tracked Time</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->trackedActivitiesForWeek as $activity)
                                <tr>
                                    <td>{{ $activity->starts_at }}</td>
                                    <td>{{ $activity->tracked }}</td>
                                    <td>{{ $activity->earnings }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
</div>
@endsection