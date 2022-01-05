@extends('layouts.app')

@section('content')
    <div class="container_fluid">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>Upteam logs</h3>
            </div>
            <div class="panel-body">
           
                <table class="table table-bordered table-hover" style="table-layout:fixed;">
                    <thead>
                    <tr>
						<th width="25%">Date</th>
						<th width="75%">log</th>
					</tr>
                    
                    </thead>
                    <tbody>
						@foreach($logs as $log)
							<tr>
								<td width="25%">{{$log['created_at']}}</td>
								<td width="75%">{{$log['log_description']}}</td>
							</tr>
						@endforeach
                    </tbody>
                </table>
				{{$logs->links()}}
            </div>
        </div>
    </div>


@endsection