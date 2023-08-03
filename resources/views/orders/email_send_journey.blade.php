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

    <div class="col-lg-12 margin-tb">
        <div class="row" style="margin-bottom:10px;">
            <div class="col-12">
                <form action="{{ route('order.get.email.send.journey.logs') }}" method="get" class="search">
                    <div class="row">
                        <div class="col-md-3 pd-sm">
                            <?php 
                                if(request('from_email')){   $from_email = request('from_email'); }
                                else{ $from_email = ''; }
                            ?>
                            <select name="from_email" id="from_email" class="form-control select2">
                                <option value="" @if($from_email=='') selected @endif>-- Select From Email--</option>
                                @forelse($groupByFromEmail as $fromEmail)
                                <option value="{{ $fromEmail }}" @if($from_email==$fromEmail) selected @endif>{!! $fromEmail !!}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="col-md-3 pd-sm">
                            <?php 
                                if(request('to_email')){   $to_email = request('to_email'); }
                                else{ $to_email = ''; }
                            ?>
                            <select name="to_email" id="to_email" class="form-control select2">
                                <option value="" @if($to_email=='') selected @endif>-- Select To Email--</option>
                                @forelse($groupByToEmail as $toEmail)
                                <option value="{{ $toEmail }}" @if($to_email==$toEmail) selected @endif>{!! $toEmail !!}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                        <div class="col-md-3 pd-sm">
                            <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                        </div>
                        <div class="col-md-3 pd-sm pl-0 mt-2">
                                <button type="submit" class="btn btn-image search">
                                <img src="{{ asset('images/search.png') }}" alt="Search">
                            </button>
                            <a href="{{ route('order.get.email.send.journey.logs') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="col-md-12">
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
                            <?php 
                            // $orderJourneyResult = \App\OrderEmailSendJourneyLog::where('order_id', '=', $orderJourneyData->order_id)->orderBy('id', 'ASC')->get(); 
                            //     $counter = 6;            
                            //     $i=1;
                            //     $stepsName = '';
                            //     $stepsName1 = '';
                            //     foreach ($orderJourneyResult as $key => $value) {
                            //         $orderJourneyData->from_email = $value->from_email;
                            //         $orderJourneyData->to_email = $value->to_email;
                            //         $i++;
                            //         if($value->steps == 'Magento Error' || $value->steps == 'Email type via Error'){
                            //             $stepsName = $value->error_msg;
                                        
                            //         }else {
                            //             $stepsName = $value->steps;
                            //         }
                            ?>
                                {{-- <td>
                                    {!! $stepsName !!}
                                    
                                </td> --}}
                            <?php
                                // }
                                // $counter = $counter - $i;
                                // for($ic = 0; $ic<=$counter; $ic++) {
                            ?>
                                {{-- <td></td> --}}
                            <?php 
                                // } 

                            ?>

                            <td style="overflow-wrap: anywhere;">
                                @if (isset($groupedLogs[$orderJourneyData->order_id]['Status Change']) && $statusChange = $groupedLogs[$orderJourneyData->order_id]['Status Change']->first())
                                    {{ $statusChange['steps']}}
                                @else
                                    -
                                @endif
                            </td>

                            <td style="overflow-wrap: anywhere;">
                                @if (isset($groupedLogs[$orderJourneyData->order_id]['Email type via Order update status']) && $emailTypeViaOrderUpdateStatus = $groupedLogs[$orderJourneyData->order_id]['Email type via Order update status']->first())
                                    {{ $emailTypeViaOrderUpdateStatus['steps']}}
                                @else
                                    -
                                @endif
                            </td>

                            <td style="overflow-wrap: anywhere;">
                                @if (isset($groupedLogs[$orderJourneyData->order_id]['Email type via Error']) && $emailTypeViaError = $groupedLogs[$orderJourneyData->order_id]['Email type via Error']->first())
                                    {{ $emailTypeViaError['steps'] }}
                                    @if (isset($emailTypeViaError['error_msg']) && $emailTypeViaError['error_msg'] != "")
                                    <i class="fa fa-info-circle" style="cursor: pointer;" data-toggle="modal" data-target="#errorModal" data-full-html="{{ $emailTypeViaError['error_msg'] }}"></i>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>

                            <td style="overflow-wrap: anywhere;">
                                @if (isset($groupedLogs[$orderJourneyData->order_id]['Email type IVA SMS Order update status']) && $emailTypeViaIVASmsOrderUpdateStatus = $groupedLogs[$orderJourneyData->order_id]['Email type IVA SMS Order update status']->first())
                                    {{ $emailTypeViaIVASmsOrderUpdateStatus['steps'] }}
                                @else
                                    -
                                @endif
                            </td>

                            <td style="overflow-wrap: anywhere;">
                                @if (isset($groupedLogs[$orderJourneyData->order_id]['Magento Order update status']) && $magentoOrderUpdateStatus = $groupedLogs[$orderJourneyData->order_id]['Magento Order update status']->first())
                                    {{ $magentoOrderUpdateStatus['steps'] }}
                                @else
                                    -
                                @endif
                            </td>

                            <td style="overflow-wrap: anywhere;">
                                @if (isset($groupedLogs[$orderJourneyData->order_id]['Magento Error']) && $magentoError = $groupedLogs[$orderJourneyData->order_id]['Magento Error']->first())
                                    {{ $magentoError['steps'] }}
                                    @if (isset($magentoError['error_msg']) && $magentoError['error_msg'] != "")
                                    <i class="fa fa-info-circle" style="cursor: pointer;" data-toggle="modal" data-target="#errorModal" data-full-html="{{ $magentoError['error_msg'] }}"></i>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            
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

        <div class="modal fade" id="errorModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
        
                    <!-- Modal body -->
                    <div class="modal-body">
                        <!-- Display the full HTML content in an iframe -->
                        <iframe id="errorModalIframe" src="" frameborder="0" style="width: 100%; height: 400px;"></iframe>
                    </div>
        
                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function() {
    $('.select2').select2();

    $('#errorModal').on('shown.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var fullHtml = button.data('full-html'); // Get the data-full-html value
        var errorModalIframe = document.getElementById('errorModalIframe'); // Get the iframe element
        errorModalIframe.srcdoc = fullHtml; // Set the srcdoc attribute with the fullHtml value
    });
});
</script>
     
@endsection
