@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Notifications</h2>
            </div>
        </div>
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table notification-table">
        @foreach ($notifications as $notification)
            <tr class="{{ $notification->isread ? 'isread' : '' }}">
                <td>{{ $notification->uname }} {{ $notification->message }} {{$notification->pname ? $notification->pname : $notification->sku }} </td>
                <td style="width: 20px"><button class="btn btn-notify" data-id="{{ $notification->id }}" >&#10003</button></td>
            </tr>
        @endforeach
    </table>

    {!! $notifications->links() !!}

@endsection