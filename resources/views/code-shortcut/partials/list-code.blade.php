	@foreach($codeshortcut as $code)
	<tr>
		<td>{{$code->id}}</td>
		<td>{{$code->user_detail->name}}</td>
		<td>{{$code->supplier_id==0?'':$code->supplier_detail->supplier}}</td>
		<td>{{$code->code}}</td>
		<td>{{$code->description}}</td>
		<td>{{$code->created_at}}</td>
		<td>
			<a class="btn btn-image edit_modal" data-id="{{$code->id}}" data-code="{{$code->code}}" data-des="{{$code->description}}" data-supplier="{{$code->supplier_id}}"><img src="/images/edit.png" style="cursor: default; width: 16px;"></a>
			<button class="btn btn-image" onclick="confirmDelete('{{$code->code}}','{{route('code-shortcuts.destory',$code->id)}}')" ><img src="/images/delete.png" style="cursor: default; width: 16px;"></button>
		</td>

	</tr>
	@endforeach
	