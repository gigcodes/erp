@foreach ($logs as $log)
	<tr>
		<td>{{ $filename }}</td>
		<td class="expand-row table-hover-cell">
			<span class="td-mini-container">
			{{ strlen( $log ) > 110 ? substr( $log , 0, 110).'...' :  $log }}
			</span>
			<span class="td-full-container hidden">
			{{ $log }}
			</span>
		</td>
		<td><button type="button" class="btn btn-default assign_task" data-toggle="modal" data-target="#assign_task_model">Assign Task</button></td>
	</tr>
@endforeach