@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Scrap Stats</h2>
        </div>
        <div class="col-md-12 mb-4">
            <form action="{{ action('ScrapStatisticsController@index') }}" method="get">
                <div class="row">
                    <div class="col-md-2">
                        <input name="date" type="date" class="form-control" value="{{$request->get('date') ?? date('Y-m-d')}}">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-default">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#home">Scrap Counts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#menu1">Scrap Progress</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#menu2">Progress</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div id="home" class="container tab-pane active">
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-striped table-bordered table-sm">
                                <tr>
                                    <th colspan="2">
                                        Scraped Existing Products
                                    </th>
                                </tr>
                                @foreach($scrapedExistingProducts as $entry)
                                    <tr>
                                        <td>
                                            {{ $entry->supplier }}
                                        </td>
                                        <th>
                                            {{$entry->total}}
                                        </th>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-striped table-bordered table-sm">
                                <tr>
                                    <th colspan="2">
                                        Scraped New Products
                                    </th>
                                </tr>
                                @foreach($scrapedNewProducts as $entry)
                                    <tr>
                                        <td>
                                            {{ $entry->supplier }}
                                        </td>
                                        <th>
                                            {{$entry->total}}
                                        </th>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
                <div id="menu1" class="container tab-pane fade">
                    @foreach($progressStats as $key=>$stat)
                        <h2 class="text-center">{{ $key }}</h2>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <tr>
                                    @foreach($stat as $st)
                                        <td>
                                            {{ $st->brand }}<br>
                                            {{ $st->total }}
                                        </td>
                                    @endforeach
                                </tr>
                            </table>
                        </div>
                    @endforeach
                </div>
                <div id="menu2" class="container tab-pane fade"><br>
                    <div class="progress" style="margin-bottom: 10px;">
                        <div class="progress-bar" role="progressbar" style="width: {{$totalProgress}}%" aria-valuenow="{{$totalProgress}}" aria-valuemin="0" aria-valuemax="100">{{ $totalProgress }}%</div>
                    </div>
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Supplier</th>
                            <th>Progress</th>
                            <td>Started At</td>
                            <td>Last Scraped</td>
                            <th>Brands Scraped</th>
                        </tr>
                        @foreach($progress as $key=>$progressItem)
                            <tr>
                                <td>{{$key}}</td>
                                <td>
                                    <div class="progress" style="margin-bottom: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{$progressItem[1]}}%" aria-valuenow="{{$progressItem[1]}}" aria-valuemin="0" aria-valuemax="100">{{ $progressItem[1]}}%</div>
                                    </div>
                                    {{ $progressItem[0] . ' of ' . $progressItem[2] }}
                                </td>
                                <td>
                                    {{ $progressItem[4]->started_at ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ $progressItem[4]->ended_at ?? 'N/A' }}
                                </td>
                                <td class="expand-row">
                                    <span class="td-mini-container">
                                        {!! strlen($progressItem[3]) > 20 ? substr($progressItem[3], 0, 20).'...' : $progressItem[3] !!}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {!! $progressItem[3] !!}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('styles')
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.expand-row', function() {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                // if ($(this).data('switch') == 0) {
                //   $(this).text($(this).data('details'));
                //   $(this).data('switch', 1);
                // } else {
                //   $(this).text($(this).data('subject'));
                //   $(this).data('switch', 0);
                // }
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });
    </script>
@endsection
