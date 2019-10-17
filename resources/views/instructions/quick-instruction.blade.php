@extends('layouts.modal')

@section('title', 'Quick instruction modal')

@section("styles")
@endsection

@section('content')
    <div class="container">
        <h1>Quick Instruction</h1>
        @if ( $instruction != null )
            <div class="row">
                <div class="col-md-5">
                    <h3>Customer</h3>
                    <table class="table table-striped">
                        <tr>
                            <td>ID</td>
                            <td>{{ $instruction->customer->id}}</td>
                        </tr>
                        <tr>
                            <td>Name</td>
                            <td>{{ $instruction->customer->name }}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>{{ $instruction->customer->address }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-7">
                    <h3>Instruction</h3>
                    <span style="background-color: #FFFF00; padding: 3px; font-size: 1.5em;">{!! nl2br($instruction->instruction) !!}</span>
                    <h3>Chat</h3>
                    <div id="chat-history" class="load-communication-modal" data-object="customer" data-all="1" data-attached="1" data-id="{{ $instruction->customer_id }}" style="max-height: 80vh; overflow-x: scroll;">
                    </div>
                </div>
            </div>
        @else
            <h2>No more instructions</h2>
        @endif

    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#chat-history').trigger('click');
        });
    </script>
@endsection