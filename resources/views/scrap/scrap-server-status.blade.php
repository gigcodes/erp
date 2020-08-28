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
                                $start_time = new DateTime($scrapper->last_started_at);
                                $end_time = new DateTime($scrapper->last_completed_at);
                                $interval = $start_time->diff($end_time);
                            ?>
                            <tr>
                                <td>{{ $scrapper->id }}</td>
                                <td>{{ $scrapper->scraper_start_time }}</td>
                                <td>{{ $scrapper->last_started_at }}</td>
                                <td>{{ $scrapper->scraper_name }}</td>
                                <td>{{ $scrapper->updated_at }}</td>
                                <td>{{ $scrapper->last_completed_at }}</td>
                                <td>{{ $interval->format('%H hours %i minutes %s seconds') }}</td>
                                <td>
                                    <a class="btn d-inline btn-image" data-attr="{{ $scrapper->scraper_name }}" id="openHistory">
                                        <img src="/images/view.png" />
                                    </a>
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

    <div class="modal fade" id="showHistory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Scrapper History of <span id="scrapper_name" class="text-bold"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped sort-priority-scrapper">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>SST</th>
                                <th>ATIS</th>
                                <th>TPLS</th>
                                <th>SET</th>
                                <th>Dur</th>
                            </tr>
                            </thead>
                            <tbody id="scrapping_history">

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>



@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
       $(document).on("click",'#openHistory', function(){
           var scrapper_name = $(this).data('attr');
           $.ajax({
               url: "{{ url('/scrap/server-statistics/history/') }}"+'/'+scrapper_name,
               type: 'GET'
           }).done( function(response) {
               if(response.status == 1){
                   $('#scrapper_name').text(response.name);
                   $('#scrapping_history').empty();
                   var table_body_content = '';
                   var calc_time;
                   for(var i = 0; i < response.data.length; i++){
                       table_body_content += '<tr>';
                       table_body_content += "<td>"+response.data[i]['id']+"</td>";
                       table_body_content += "<td>"+response.data[i]['scraper_start_time']+"</td>";
                       table_body_content += "<td>"+response.data[i]['last_started_at']+"</td>";
                       table_body_content += "<td>"+response.data[i]['updated_at']+"</td>";
                       table_body_content += "<td>"+response.data[i]['last_completed_at']+"</td>";
                       calc_time = calculateDifference(response.data[i]['last_completed_at'], response.data[i]['last_started_at']);
                       table_body_content += "<td>"+calc_time.hour+' hours '+calc_time.minute+' minutes '+calc_time.second+' seconds'+"</td>";
                       table_body_content += '</tr>';
                   }
                   $('#scrapping_history').append(table_body_content);
                   $('#showHistory').modal('show');
               }
           });
       }) ;
    });

    function calculateDifference(end_date, start_date)
    {
        var d = Math.abs(end_date - start_date) / 1000;                           // delta
        var r = {};                                                                // result
        var s = {                                                                  // structure
            year: 31536000,
            month: 2592000,
            week: 604800, // uncomment row to ignore
            day: 86400,   // feel free to add your own row
            hour: 3600,
            minute: 60,
            second: 1
        };

        Object.keys(s).forEach(function(key){
            r[key] = Math.floor(d / s[key]);
            d -= r[key] * s[key];
        });
        console.log(r);
        return r;
    }

</script>
@endsection