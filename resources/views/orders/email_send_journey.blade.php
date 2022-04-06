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
                            <th width="3%">ID</th>
                            <th width="3%">Order ID</th>
                            
                            <th width="5%">Status Change</th>
                            <th width="5%">Email type via Order update status</th>
                            <th width="5%">Email type via Error</th>
                            <th width="5%">Email type IVA SMS Order update status</th>
                            <th width="5%">Magento Order update status</th>
                            <th width="5%">Magento Error</th>


                            <th width="10%">From</th>
                            <th width="10%">To</th>
                            <th width="10%">Subject</th>
                            {{-- <th>Message</th> --}}
                            <th width="10%">Date</th>
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
                                <?php $orderJourneyResult = \App\OrderEmailSendJourneyLog::where('order_id', '=', $orderJourneyData->order_id)->orderBy('id', 'ASC')->get(); 
                                    $counter = 6;            
                                    $i=1;
                                    $stepsName = '';
                                    $stepsName1 = '';
                                    foreach ($orderJourneyResult as $key => $value) {
                                        $orderJourneyData->from_email = $value->from_email;
                                        $orderJourneyData->to_email = $value->to_email;
                                        $i++;
                                        if($value->steps == 'Magento Error' || $value->steps == 'Email type via Error'){
                                            $stepsName = $value->error_msg;
                                            
                                        }else {
                                            $stepsName = $value->steps;
                                        }
                                ?>
                                    <td>
                                        {!! $stepsName !!}
                                        
                                    </td>
                                <?php
                                    }
                                    $counter = $counter - $i;
                                    for($ic = 0; $ic<=$counter; $ic++) {
                                ?>
                                    <td></td>
                                <?php } ?>
                                
                                <td style="overflow-wrap: anywhere;">
                                    {{ $orderJourneyData->from_email }}
                                </td>
                                <td style="overflow-wrap: anywhere;">
                                    {{ $orderJourneyData->to_email }}
                                </td>
                                <td>
                                    {!! $orderJourneyData->subject !!}
                                </td>
                                {{-- <td>
                                    <table><tr><td>{!! $orderJourneyData->message !!}</td></tr></table>
                                </td> --}}
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
