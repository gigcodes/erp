@extends('layouts.app')
@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Twilio Recordings</h2>
        </div>
    </div>
 {{--   <form method="get" action="">
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Start Date</label>
                <div class='input-group date' id='start-date'>
                    <input type='text' class="form-control" name="st_date" value="{{ isset($start_date) ? $start_date : '' }}" />

                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                </div>
            </div>
            <div class="col-md-4">
                <label>End Date</label>
                <div class='input-group date' id='end-date'>
                    <input type='text' class="form-control" name="en_date" value="{{ isset($end_date) ? $end_date : '' }}" />
                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                </div>        </div>
            <div class="col-md-2">
                <button class="btn btn-image" type="submit">
                    <img src="/images/filter.png" />
                </button>
            </div>
        </div>
    </form>
--}}

    <div class="row mt-3">
        <div class="col-md-10 col-sm-12">
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th scope="col" class="text-center">Record SID</th>
                            <th scope="col" class="text-center">Call SID</th>
                            <th scope="col" class="text-center">Price</th>
                            <th scope="col" class="text-center">Price Unit</th>
                            <th scope="col" class="text-center">Source</th>
                            <th scope="col" class="text-center">Download</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        @if(isset($result))
                            @foreach($result->recordings as $res)
                                <tr>
                                    <td>{{ $res->sid }}</td>
                                    <td>{{ $res->call_sid }}</td>
                                    <td>{{ $res->price }}</td>
                                    <td>{{ $res->price_unit }}</td>
                                    <td>{{ $res->source }}</td>
                                    <td><a href="{{ route('download-mp3', $res->sid).'?id='.request()->get('id') }}">Download Mp3</a></td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>


    </div>

@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#start-date').datetimepicker({
                format: 'YYYY-MM-DD'
            });
            $('#end-date').datetimepicker({
                format: 'YYYY-MM-DD'
            });
        });
    </script>
@endsection