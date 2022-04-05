@extends('layouts.app')
@section('title', 'Call Management')
@section('content')

    <style>
        td audio {
            height: 30px;
        }

        td {
            padding: 5px 8px 0 !important;

        }

        #customer_order_details {
            padding: 10px 0 !important;
        }

    </style>

    <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">
        Order Email Journey
    </h2>




    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif





    <div class="col-md-12">
        <div class="col-md-12">
            <h3>Order Email Journey</h3>
            <div class="table-responsive">
                <table id="show-ordres-table" class="table table-bordered table-hover" style="table-layout:fixed;">
                    <thead class="reserved-calls">
                        <tr>
                            <th>ID</th>
                            <th>Order ID</th>
                            <th>Steps</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="order-email-journey-table-body">
                        @foreach ($orderJourney as $orderJourneyData)
                            <tr>
                                <td>
                                    {{ $orderJourneyData->id }}
                                </td>
                                <td>
                                    {{ $orderJourneyData->order_id }}
                                </td>
                                <td>
                                    {!! $orderJourneyData->steps !!}
                                </td>
                                <td>
                                    {{ $orderJourneyData->from_email }}
                                </td>
                                <td>
                                    {{ $orderJourneyData->to_email }}
                                </td>
                                <td>
                                    {!! $orderJourneyData->subject !!}
                                </td>
                                <td>
                                    <table>{!! $orderJourneyData->message !!}</table>
                                </td>
                                <td>{!! $orderJourneyData->created_at !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

@endsection

@section('scripts')
    
@endsection
