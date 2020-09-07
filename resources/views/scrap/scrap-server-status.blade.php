@extends('layouts.app')

@section('favicon' , 'supplierstats.png')

@section('title', 'Scrape Statistics')
@section('large_content')

    <div class="row mb-5">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Scrapper Server Status <span class="total-info"></span></h2>
        </div>
    </div>

    @include('partials.flash_messages')
    <div class="row no-gutters mt-3">
        <div class="col-md-12" id="plannerColumn">
            <div class="table-responsive">
                <table class="table table-bordered table-striped sort-priority-scrapper">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>SST</th>
                        <th>ATIS</th>
                        <th>Scrapper</th>
                        <th>TPLS</th>
                        <th>SET</th>
                        <th>Dur</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($scrappers))
                        @foreach($scrappers as $scrapper)
                            <?php
                                $start_time = new DateTime(@$scrapper->last_started_at);
                                $end_time = new DateTime(@$scrapper->last_completed_at);
                                $interval = @$start_time->diff($end_time);
                            ?>
                            <tr>
                                <td>{{ @$scrapper->id }}</td>
                                <td>{{ @$scrapper->scraper_start_time }}</td>
                                <td>{{ @$scrapper->last_started_at }}</td>
                                <td>{{ @$scrapper->scraper_name }}</td>
                                <td>{{ @$scrapper->updated_at }}</td>
                                <td>{{ @$scrapper->last_completed_at }}</td>
                                <td>{{ @$interval->format('%H hours %i minutes %s seconds') }}</td>
                                <td>
                                    <a class="btn d-inline btn-image openHistory" data-attr="{{ @$scrapper->id }}" id="{{ @$scrapper->id }}">
                                        <img src="/images/view.png" />
                                    </a>
                                </td>
                            </tr>
                            <tr class="close_all open_request_{{ @$scrapper->id }}" style="display: none;">
                                <td>
                                    <label>Start Time</label>
                                    <span>{{ @$scrapper->getScrapHistory->start_time }}</span>
                                </td>
                                <td>
                                    <label>End Time</label>
                                    <span>{{ @$scrapper->getScrapHistory->end_time }}</span>
                                </td>
                                <td>
                                    <label>Sent Request</label>
                                    <span>{{ @$scrapper->getScrapHistory->request_sent }}</span>
                                </td>
                                <td>
                                    <label>Failed Request</label>
                                    <span>{{ @$scrapper->getScrapHistory->request_failed }}</span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                @if(isset($scrappers))
                    {!! $scrappers->links() !!}
                @endif
            </div>

        </div>
    </div>



@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
       var counter = 0;
       $(document).on("click",'.openHistory', function(){
            if(counter == 1){
                $('.close_all').hide();
                counter = 0;
            }
            var id = $(this).attr('id');
            if(counter == 0){
                $('.open_request_'+id).show();
                counter = 1;
            }else{
                $('.open_request_'+id).hide();
                counter = 0;
            }
       }) ;
    });

</script>
@endsection