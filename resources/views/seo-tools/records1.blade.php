@foreach($response as $tool=>$toolResponse) 
  <div class="tab-pane fade @if($tool == 1) in active @endif" id="tool_{{$tool}}">
@if($tool < 3)
 @foreach($toolResponse as $type=>$data) 
	@foreach($data as $column=>$columnValues)
	 <div class="table-responsive" >	
		 <table class="table table-striped table-bordered"> 
		@php $i=0; @endphp 
					 
		  @foreach(json_decode($columnValues, true) as $values)  
			<tr>
			<?php 
					$type = ucfirst(str_replace('_', ' ', $type));
					$column = ucfirst(str_replace('_', ' ', $column));
					$recordCount = count($values)+2;
					$width = floor(100/$recordCount);
				?>
				 @if($i == 0)
					<th style="width:{{$width}}%">{{$type}}</th>		
					<th style="width:{{$width}}%">{{$column}}</th>		
				 @else
					<td class="expand-row-msg" style="width:{{$width}}%" data-name="type" data-id="{{$i}}">
						<span class="show-short-type-{{$i}}">{{ str_limit($type, $width, '...')}}</span>
						<span style="word-break:break-all;" class="show-full-type-{{$i}} hidden">{{$type}}</span>
					</td>		
					<td class="expand-row-msg" style="width:{{$width}}%" data-name="column" data-id="{{$i}}">
						<span class="show-short-column-{{$i}}">{{ str_limit($column, $width, '...')}}</span>
						<span style="word-break:break-all;" class="show-full-column-{{$i}} hidden">{{$column}}</span>
					</td>	
				 @endif
		
				
				
				@foreach($values as $recordKey=>$record) 
					@if(is_array($record))
						{{dd($record)}}
					@endif
				    @if($i == 0)
						<th style="width:{{$width}}%">{{ucfirst(str_replace('_', ' ', $record))}}</th>		
					@else
						@php $recordNew = 'col_'.$recordKey; @endphp
						<td class="expand-row-msg" style="width:{{$width}}%" data-name="{{$recordNew}}" data-id="{{$i}}">
							<span class="show-short-{{$recordNew}}-{{$i}}">{{ str_limit($record, $width, '...')}}</span>
							<span style="word-break:break-all;" class="show-full-{{$recordNew}}-{{$i}} hidden">{{$record}}</span>
						</td>
					@endif
					
				@endforeach
				@php $i++; @endphp
				
				</tr>
			</td>
			
		  @endforeach
		</table> 
		</div>
		
	@endforeach
@endforeach
@elseif($tool ==  2)
 <div class="table-responsive" >	
 @foreach($toolResponse as $type=>$data)  
	@foreach($data as $column=>$columnValues)
		@foreach(json_decode($columnValues, true) as $values)  
			@foreach($values as $recordKey=>$record) 
				{{ dd($record) }}
			@endforeach
		@endforeach
	@endforeach
 @endforeach
</div>
@endif
</div>

@endforeach