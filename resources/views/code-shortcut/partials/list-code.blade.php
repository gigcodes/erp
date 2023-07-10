	@foreach($codeshortcut as $code)
	<tr>
		<td>{{$code->id}}</td>
		<td>{{$code->code_shortcuts_platform_id == null ?'':$code->platform->name}}</td>
		<td>{{$code->website}}</td>
		<td>{{$code->title}}</td>
		<td>{{$code->code}}</td>
		<td>{{$code->description}}</td>
		<td>{{$code->solution}}</td>
		<td>{{$code->user_detail->name}}</td>
		<td>{{$code->supplier_id==0?'':$code->supplier_detail->supplier}}</td>
		<td>{{ $code->created_at->format('Y-m-d') }}</td>
		@if($code->filename !== null)
		<td> <img src="./codeshortcut-image/{{ $code->filename}}" height='50' width="50"></td>
		@else 
		<td>-</td>
		@endif
		<td>
			<a class="btn btn-image edit_modal" data-id="{{$code->id}}" data-code="{{$code->code}}" data-des="{{$code->description}}" data-supplier="{{$code->supplier_id}}" data-title="{{$code->title}}" data-solution="{{$code->solution}}"  data-platformId="{{$code->code_shortcuts_platform_id}}" data-shortcutfilename="{{ $code->filename }}"><img src="/images/edit.png" style="cursor: default; width: 16px;"></a>
			<button class="btn btn-image" onclick="confirmDelete('{{$code->code}}','{{route('code-shortcuts.destory',$code->id)}}')" ><img src="/images/delete.png" style="cursor: default; width: 16px;"></button>
		</td>

	</tr>
	@endforeach
	