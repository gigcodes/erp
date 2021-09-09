@php $width = 7; @endphp
@foreach($keywords as $i=>$keywordDetail)
	<tr>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="compitetors-report" data-id="{{$i}}">
				<span class="show-short-compitetors-report-{{$i}}">{{ str_limit('Compitetors Report', $width, '...')}}</span>
				<span style="word-break:break-all;" class="show-full-compitetors-report-{{$i}} hidden">Compitetors Report</span>
			</td>	
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="database" data-id="{{$i}}">
			<span class="show-short-database-{{$i}}">{{ str_limit($keywordDetail['database'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-database-{{$i}} hidden">{{ $keywordDetail['database'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="subtype" data-id="{{$i}}">
			<span class="show-short-subtype-{{$i}}">{{ str_limit($keywordDetail['subtype'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-subtype-{{$i}} hidden">{{$keywordDetail['subtype']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="domain" data-id="{{$i}}">
			<span class="show-short-domain-{{$i}}">{{ str_limit($keywordDetail['domain'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-domain-{{$i}} hidden">{{$keywordDetail['domain']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="common_keywords" data-id="{{$i}}">
			<span class="show-short-common_keywords-{{$i}}">{{ str_limit($keywordDetail['common_keywords'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-common_keywords-{{$i}} hidden">{{$keywordDetail['common_keywords']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="keywords" data-id="{{$i}}">
			<span class="show-short-keywords-{{$i}}">{{ str_limit($keywordDetail['keywords'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-keywords-{{$i}} hidden">{{$keywordDetail['keywords']}}</span>
		</td>
		
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="traffic" data-id="{{$i}}">
			<span class="show-short-traffic-{{$i}}">{{ str_limit($keywordDetail['traffic'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-traffic-{{$i}} hidden">{{$keywordDetail['traffic']}}</span>
		</td>
	</tr>
@endforeach
