@extends('layouts.app')


@section('content')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <section class="dashboard-counts section-padding">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('activity') }}" method="GET" enctype="multipart/form-data">
                        <div class="row">
                        <!--<div class="col-md-4">
                            <div class="form-group">
                                <strong>Type</strong>
					            <?php
						/*					            echo Form::select( 'type', $type, old('type'), [
                                                            'placeholder' => 'Select a value',
                                                            'class'       => 'form-control'
                                                        ] );*/?>
                                </div>
                            </div>-->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>User</strong>
									<?php
									echo Form::select( 'selected_user', $users, $selected_user , [
										'class'       => 'form-control',
										'multiple' => 'multiple',
										'id' => 'userList',
										'name' => 'selected_user[]'
									] );?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Date Range</strong>
                                    <input type="text" value="{{ $range_start }}" name="range_start" hidden/>
                                    <input type="text" value="{{ $range_end  }}" name="range_end" hidden/>
                                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <strong>&nbsp;</strong>
                                <button type="submit" class="btn btn-secondary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-counts section-padding">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 col-md-12">
                    <div class="row">
                        <!-- Count item widget-->
                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-user"></i></div>
                                <div class="name"><strong class="text-uppercase">Selections</strong>
                                    <div class="count-number
                                        {{ ( $total_data['selection'] < $benchmark['selections'] ) ? 'red' : '' }}">
                                        {{ $total_data['selection'] }}
                                    </div>
                                    <span>Out of {{ $benchmark['selections'] }}</span>
                                    @if( $benchmark['selections'] - $total_data['selection'] > 0 )
                                        <span>Pending : {{ $benchmark['selections'] - $total_data['selection'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Count item widget-->
                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-padnote"></i></div>
                                <div class="name"><strong class="text-uppercase">Searches</strong>
                                    <div class="count-number {{ ( $total_data['searcher'] < $benchmark['searches'] ) ? 'red' : '' }} ">{{ $total_data['searcher']  }}</div>
                                    <span>Out of {{ $benchmark['searches'] }}</span>
                                    @if( $benchmark['searches'] - $total_data['searcher'] > 0 )
                                        <span>Pending : {{ $benchmark['searches'] - $total_data['searcher'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Count item widget-->
                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-check"></i></div>
                                <div class="name"><strong class="text-uppercase">Attribute filling</strong>
                                    <div class="count-number {{ ( $total_data['attribute'] < $benchmark['attributes'] ) ? 'red' : '' }} ">{{ $total_data['attribute'] }}</div>
                                    <span>Out of {{ $benchmark['attributes'] }}</span>
                                    @if( $benchmark['attributes'] - $total_data['attribute'] > 0 )
                                        <span>Pending : {{ $benchmark['attributes'] - $total_data['attribute'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Count item widget-->
                        {{-- <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-bill"></i></div>
                                <div class="name"><strong class="text-uppercase">Supervise</strong>
                                    <div class="count-number {{ ( $total_data['supervisor'] < $benchmark['supervisor'] ) ? 'red' : '' }} ">{{ $total_data['supervisor']  }}</div>
                                    <span>Out of {{ $benchmark['supervisor'] }}</span>
                                    @if( $benchmark['supervisor'] - $total_data['supervisor'] > 0 )
                                        <span>Pending : {{ $benchmark['supervisor'] - $total_data['supervisor'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div> --}}

                        <!-- Count item widget-->
                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-bill"></i></div>
                                <div class="name"><strong class="text-uppercase">Imagecropping</strong>
                                    <div class="count-number {{ ( $total_data['imagecropper'] < $benchmark['imagecropper'] ) ? 'red' : '' }} ">{{ $total_data['imagecropper'] }}</div>
                                    <span>Out of {{ $benchmark['imagecropper'] }}</span>
                                    @if( $benchmark['imagecropper'] - $total_data['imagecropper'] > 0 )
                                        <span>Pending : {{ $benchmark['imagecropper'] - $total_data['imagecropper'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Count item widget-->
                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-list"></i></div>
                                <div class="name"><strong class="text-uppercase">Approvals</strong>
                                    <div class="count-number {{ ( $total_data['approver'] < $benchmark['approver'] ) ? 'red' : '' }} ">{{ $total_data['approver']  }}</div>
                                    <span>Out of {{ $benchmark['approver'] }}</span>
                                    @if( $benchmark['approver'] - $total_data['approver'] > 0 )
                                        <span>Pending : {{ $benchmark['approver'] - $total_data['approver'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Count item widget-->
                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-list-1"></i></div>
                                <div class="name"><strong class="text-uppercase">Listings</strong>
                                    <div class="count-number {{ ( $total_data['lister'] < $benchmark['lister'] ) ? 'red' : '' }} ">{{ $total_data['lister'] }}</div>
                                    <span>Out of {{ $benchmark['lister'] }}</span>
                                    @if( $benchmark['lister'] - $total_data['lister'] > 0 )
                                        <span>Pending : {{ $benchmark['lister'] - $total_data['lister'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Count item widget-->
                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-list-1"></i></div>
                                <div class="name"><strong class="text-uppercase">Sales Enquiry</strong>
                                    <div class="count-number">{{ $total_data['sales'] }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-list-1"></i></div>
                                <div class="name"><strong class="text-uppercase">Todays Leads</strong>
                                    <div class="count-number">{{ $leads }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-list-1"></i></div>
                                <div class="name"><strong class="text-uppercase">Todays Orders</strong>
                                    <div class="count-number">{{ $orders }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-list-1"></i></div>
                                <div class="name"><strong class="text-uppercase">Scraped G&B</strong>
                                    <div class="count-number">{{ $scraped_gnb_count }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-list-1"></i></div>
                                <div class="name"><strong class="text-uppercase">Scraped Wise Boutique</strong>
                                    <div class="count-number">{{ $scraped_wise_count }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-list-1"></i></div>
                                <div class="name"><strong class="text-uppercase">Scraped Double F</strong>
                                    <div class="count-number">{{ $scraped_double_count }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-list-1"></i></div>
                                <div class="name"><strong class="text-uppercase">Scraped G&B Products</strong>
                                    <div class="count-number">{{ $scraped_gnb_product_count }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-list-1"></i></div>
                                <div class="name"><strong class="text-uppercase">Scraped Wise Boutique Products</strong>
                                    <div class="count-number">{{ $scraped_wise_product_count }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="wrapper count-title d-flex">
                                <div class="icon"><i class="icon-list-1"></i></div>
                                <div class="name"><strong class="text-uppercase">Scraped Double F Products</strong>
                                    <div class="count-number">{{ $scraped_double_product_count }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-counts section-padding">
        <div class="container-fluid">
            <div class="row">
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>Selection</th>
                        <th>Searcher</th>
                        <th>Attribute</th>
                        <th>Supervisor</th>
                        <th>ImageCropper</th>
                        <th>Lister</th>
                        <th>Approver</th>
                        <th>Inventory</th>
                        <th>Sale</th>
                    </tr>
                    @foreach ($results as $key => $item)
                        <tr>
                            <td>{{ $users[$key] ?? 'Unknown' }}</td>
                            <td>{{ isset( $item['selection'] ) ? $item['selection'] : 0 }}</td>
                            <td>{{ isset( $item['searcher'] ) ? $item['searcher'] : 0 }}</td>
                            <td>{{ isset( $item['attribute'] ) ? $item['attribute'] : 0 }}</td>
                            <td>{{ isset( $item['supervisor'] ) ? $item['supervisor'] : 0 }}</td>
                            <td>{{ isset( $item['imagecropper'] ) ? $item['imagecropper'] : 0 }}</td>
                            <td>{{ isset( $item['lister'] ) ? $item['lister'] : 0 }}</td>
                            <td>{{ isset( $item['approver'] ) ? $item['approver'] : 0 }}</td>
                            <td>{{ isset( $item['inventory'] ) ? $item['inventory'] : 0 }}</td>
                            <td>{{ isset( $item['sales'] ) ? $item['sales'] : 0 }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </section>

   {{-- <section class="dashboard-counts section-padding">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    --}}{{--<div class="col-xl-6 col-md-6">--}}{{--
                    <div class="row">
                        <div class="card activity-chart">
                            <div class="card-header d-flex align-items-center">
                                <h4>Activity Chart</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="ActivityChart"></canvas>
                            </div>
                        </div>
                    </div>
                    --}}{{--</div>--}}{{--
                </div>
            </div>
        </div>
    </section>--}}


    <script>

        jQuery('#userList').select2(
            {
                placeholder : 'Select a User'
            }
        );
    </script>


    <script type="text/javascript">
        $(function() {

            let r_s = jQuery('input[name="range_start"]').val();
            let r_e = jQuery('input[name="range_end"]').val()

            let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(6, 'days');
            let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();

            // jQuery('input[name="range_start"]').val(start.format('YYYY-MM-DD'));
            // jQuery('input[name="range_end"]').val(end.format('YYYY-MM-DD'));

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                maxYear: 1,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

        });

        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {

            jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
            jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));

        });

    </script>

    {{--<script>

        /*global $, document, LINECHARTEXMPLE*/
        $(document).ready(function () {

            'use strict';

            let brandPrimary = 'rgba(51, 179, 90, 1)';

            let ActivityChart = $('#ActivityChart');

            var barChartExample = new Chart(ActivityChart, {
                type: 'bar',
                data: {
                    // labels: ["SELECTIONS", "SEARCHES", "ATTRIBUTE", "IMAGECROPPING", "APPROVALS", "LISTINGS", "SALES"],
                    labels: ["Mon", "Tue", "Wed", "Thus", "Fri", "Sat", "Sun"],
                    datasets: [
                        {
                            label: "Work Done",

                            backgroundColor: '#5EBA31' ,
                            // data: [65, 59, 80, 81, 56, 55, 140],
                            data: [
                                @foreach($total_data as $key => $value)
                                {{ $value.',' }}
                                @endforeach
                            ],
                        },
                        {
                            label: "Benchmark",
                            // backgroundColor: ['rgba(203, 203, 203, 0.6)',],
                            backgroundColor: '#5738CA' ,
                            // backgroundColor: '#FF0000',

                            // data: [35, 40, 60, 47, 88, 27, 30],
                            data: [
                                @foreach($benchmark as $key => $value)
                                {{ $value.',' }}
                                @endforeach
                            ],
                        }
                    ],
                },
                options: {
                    scaleShowValues: true,
                    scales: {
                        yAxes: [{
                            // stacked: true,
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            // stacked: true,
                            ticks: {
                                autoSkip: false
                            }
                        }]
                    }
                }
            });

        });
    </script>--}}

@endsection

{{--
/*backgroundColor: [
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)'
],
*/
/*borderColor: [
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)'
],*/

borderColor: [
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)'
],--}}
