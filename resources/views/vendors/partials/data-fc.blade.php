@foreach ($VendorFlowchart as $vendor)
    @if(!empty($dynamicColumnsToShowVendorsfc))
        <tr>
            @if (!in_array('ID', $dynamicColumnsToShowVendorsfc))
                <td>{{ $vendor->name }}</td>
            @endif

            @if (!in_array('Categgory', $dynamicColumnsToShowVendorsfc))
                <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
            @endif
            
            @if($vendor_flow_charts)
                @foreach($vendor_flow_charts as $flow_chart)
                    @php
                        $status_color = new stdClass();
                        $status_hcolor = \App\Models\VendorFlowChartStatusHistory::where('flow_chart_id',$flow_chart->id)->where('vendor_id',$vendor->id)->orderBy('id', 'DESC')->first();
                        if (!empty($status_hcolor->new_value)) {
                            $status_color = \App\Models\VendorFlowChartStatus::where('id',$status_hcolor->new_value)->first();
                        }
                    @endphp
                    @if (!in_array($flow_chart->id, $dynamicColumnsToShowVendorsfc))
                        <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <input style="margin-top: 0px;width:40% !important;" type="text" class="form-control " name="message" placeholder="Remarks" id="remark_{{ $vendor->id }}_{{ $flow_chart->id }}" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}">
                                <div style="margin-top: 0px;" class="d-flex p-0">
                                    <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{ $vendor->id }}, {{ $flow_chart->id }})"><img src="/images/filled-sent.png"></button>
                                    <button type="button" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}" class="btn btn-image remarks-history-show p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                                </div>

                                <select style="margin-top: 0px;width:40% !important;" class="form-control status-dropdown" name="status" class="status-dropdown" data-id="{{$vendor->id}}" data-flow_chart_id="{{$flow_chart->id}}">
                                    <option value="">Select Status</option>
                                    @foreach ($status as $stat)
                                        <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                    @endforeach
                                </select>
                                <button type="button" data-id="{{ $vendor->id  }}" data-flow_chart_id="{{$flow_chart->id}}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                            </div>
                        </td>
                    @endif
                @endforeach
            @endif
        </tr>
    @else
        <tr>
            <td>{{ $vendor->name }}</td>
            <td>@if(!empty($vendor->category->title)) {{ $vendor->category->title }} @endif</td>
            @if($vendor_flow_charts)
                @foreach($vendor_flow_charts as $flow_chart)
                    @php
                        $status_color = new stdClass();
                        $status_hcolor = \App\Models\VendorFlowChartStatusHistory::where('flow_chart_id',$flow_chart->id)->where('vendor_id',$vendor->id)->orderBy('id', 'DESC')->first();
                        if (!empty($status_hcolor->new_value)) {
                            $status_color = \App\Models\VendorFlowChartStatus::where('id',$status_hcolor->new_value)->first();
                        }
                    @endphp
                    <td style="background-color: {{$status_color->status_color ?? ""}}!important;">
                        <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                            <input style="margin-top: 0px;width:40% !important;" type="text" class="form-control " name="message" placeholder="Remarks" id="remark_{{ $vendor->id }}_{{ $flow_chart->id }}" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}">
                            <div style="margin-top: 0px;" class="d-flex p-0">
                                <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{ $vendor->id }}, {{ $flow_chart->id }})"><img src="/images/filled-sent.png"></button>
                                <button type="button" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}" class="btn btn-image remarks-history-show p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                            </div>

                            <select style="margin-top: 0px;width:40% !important;" class="form-control status-dropdown" name="status" class="status-dropdown" data-id="{{$vendor->id}}" data-flow_chart_id="{{$flow_chart->id}}">
                                <option value="">Select Status</option>
                                @foreach ($status as $stat)
                                    <option value="{{$stat->id}}">{{$stat->status_name}}</option>
                                @endforeach
                            </select>
                            <button type="button" data-id="{{ $vendor->id  }}" data-flow_chart_id="{{$flow_chart->id}}" class="btn btn-image status-history-show p-0 ml-2"  title="Status Histories" ><i class="fa fa-info-circle"></i></button>
                        </div>
                    </td>
                @endforeach
            @endif
        </tr>
    @endif
@endforeach