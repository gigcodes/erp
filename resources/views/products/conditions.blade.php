@extends('layouts.app')


@section('favicon' , 'productstats.png')


@section('title', 'Push to magento Conditions')


@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Push to magento Conditions ({{$conditions->count()}})</h2>
        </div>
    </div>
	
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <tr>
                    <th width="10%">#</th>
                    <th width="20%">Condition</th>
                    <th width="60%">Description</th>
                    <th width="10%">Action</th>
                </tr>
                @foreach($conditions as $i=>$condition)
                    <tr>
                        <td width="10%">{{ $i+1 }}</td>
                        <td width="20%">
                            {{ $condition['condition'] }}
                        </td>
                        <td width="60%">
                           {{ $condition['description'] }}
                        </td>
                        <td width="10%"> 
							{{Form::select('status', [1=>'Enable', 0=>'Disable'], $condition['status'], array('class'=>'form-control status', 'data-id'=>$condition['id']))}}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $( ".status" ).change(function() {
			var status = $(this).val();
			var id = $(this).data('id');
			$.ajax({
			  url: '{{ url("products/conditions/status/update") }}'+'?id='+id+'&status='+status,
			  method: 'GET'
			}).done(function(response) {
			  alert('Status Updated');
			});
		});
    </script>
@endsection
