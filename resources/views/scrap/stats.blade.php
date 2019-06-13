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
                    <a class="nav-link" data-toggle="tab" href="#menu2">Others</a>
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
                    //Other items..
                </div>
            </div>
        </div>

    </div>
@endsection

@section('styles')
@endsection

@section('scripts')
@endsection
