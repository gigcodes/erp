@php $width = 7; @endphp
@foreach($keywords as $i=>$keywordDetail) 
	<tr>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="domain-report" data-id="{{$i}}">
				<span class="show-short-domain-report-{{$i}}">{{ str_limit('Domain Report', $width, '...')}}</span>
				<span style="word-break:break-all;" class="show-full-domain-report-{{$i}} hidden">Domain Report</span>
			</td>	
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="subtype" data-id="{{$i}}">
			<span class="show-short-subtype-{{$i}}">{{ str_limit($keywordDetail['subtype'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-subtype-{{$i}} hidden">{{$keywordDetail['subtype']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="keyword" data-id="{{$i}}">
			<span class="show-short-keyword-{{$i}}">{{ str_limit($keywordDetail['keyword'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-keyword-{{$i}} hidden">{{ $keywordDetail['keyword'] }}</span>
			<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-detail" title="Show Detail" data-toggle="modal" data-target="#compModal"><i class="fa fa-info-circle"></i></button>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="position" data-id="{{$i}}">
			<span class="show-short-position-{{$i}}">{{ str_limit($keywordDetail['position'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-position-{{$i}} hidden">{{$keywordDetail['position']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="previous-position" data-id="{{$i}}">
			<span class="show-short-previous-position-{{$i}}">{{ str_limit($keywordDetail['previous_position'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-previous-position-{{$i}} hidden">{{$keywordDetail['previous_position']}}</span>
		</td>
		
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="position-difference" data-id="{{$i}}">
			<span class="show-short-position-difference-{{$i}}">{{ str_limit($keywordDetail['position_difference'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-position-difference-{{$i}} hidden">{{$keywordDetail['position_difference']}}</span>
		</td>
		
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="search-volume" data-id="{{$i}}">
			<span class="show-short-search-volume-{{$i}}">{{ str_limit($keywordDetail['search_volume'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-search-volume-{{$i}} hidden">{{$keywordDetail['search_volume']}}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="cpc" data-id="{{$i}}">
			<span class="show-short-cpc-{{$i}}">{{ str_limit($keywordDetail['cpc'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-cpc-{{$i}} hidden">{{$keywordDetail['cpc']}}</span>
		</td>

		<td class="expand-row-msg" style="width:{{$width}}%" data-name="url" data-id="{{$i}}">
			<span class="show-short-url-{{$i}}">{{ str_limit($keywordDetail['url'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-url-{{$i}} hidden">{{ $keywordDetail['url'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="traffic-percentage" data-id="{{$i}}">
			<span class="show-short-traffic-percentage-{{$i}}">{{ str_limit($keywordDetail['traffic_percentage'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-traffic-percentage-{{$i}} hidden"> {{  $keywordDetail['traffic_percentage'] }}</span>
		</td>
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="traffic-cost" data-id="{{$i}}">
			<span class="show-short-traffic-cost-{{$i}}">{{ str_limit($keywordDetail['traffic_cost'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-traffic-cost-{{$i}} hidden">{{$keywordDetail['traffic_cost']}}</span>
		</td>
		
		<td class="expand-row-msg" style="width:{{$width}}%" data-name="competition" data-id="{{$i}}">
			<span class="show-short-competition-{{$i}}">{{ str_limit($keywordDetail['competition'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-competition-{{$i}} hidden">{{$keywordDetail['competition']}}</span>
		</td>

		<td class="expand-row-msg" style="width:{{$width}}%" data-name="number-of-results" data-id="{{$i}}">
			<span class="show-short-number-of-results-{{$i}}">{{ str_limit($keywordDetail['number_of_results'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-number-of-results-{{$i}} hidden">{{$keywordDetail['number_of_results']}}</span>
		</td>

		<td class="expand-row-msg" style="width:{{$width}}%" data-name="trends" data-id="{{$i}}">
			<span class="show-short-trends-{{$i}}">{{ str_limit($keywordDetail['trends'], $width, '...')}}</span>
			<span style="word-break:break-all;" class="show-full-trends-{{$i}} hidden">{{$keywordDetail['trends']}}</span>
		</td>

	</tr>
@endforeach