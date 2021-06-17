@extends('layouts.app')

@section('link-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.jqueryui.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/scroller/2.0.1/css/scroller.jqueryui.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection
@section('content')



<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Keyword Response Logs</h2>
    </div>
    <div class="col-lg-12">
        &nbsp;
    </div>
</div>

<div class="row">
    <div class="table-responsive">
        <table class="table table-striped table-bordered" style="width: 99%" id="keywordassign_table">
            <thead>
                <tr>
                    <th width="15%">Model</th>
                    <th width="15%">Model Id</th>
                    <th width="15%">Keyword</th>
                    <th width="15%">Keyword Match</th>
                    <th width="15%">Message Send Id</th>
                    <th width="20%">Comment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($keywordlogs as $key => $value)
                    <tr>
                        <td>{{$value->model}}</td>
                        <td>{{$value->model_id}}</td>
                        <td>{{$value->keyword}}</td>
                        <td>{{$value->keyword_match}}</td>
                        <td>{{$value->message_sent_id}}</td>
                        <td>{{$value->comment}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            {{ $keywordlogs->appends($request->except('page'))->links() }}.
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script type="text/javascript">
   

</script>
@endsection