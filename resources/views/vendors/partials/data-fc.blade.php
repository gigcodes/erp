@foreach ($VendorFlowchart as $vendor)
    @if(!empty($dynamicColumnsToShowVendorsfc))
        <tr>
            @if (!in_array('ID', $dynamicColumnsToShowVendorsfc))
                <td>{{ $vendor->name }}</td>
            @endif
            @if($vendor_flow_charts)
                @foreach($vendor_flow_charts as $flow_chart)
                    @if (!in_array($flow_chart->id, $dynamicColumnsToShowVendorsfc))
                        <td>
                            <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                                <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="message" placeholder="Remarks" id="remark_{{ $vendor->id }}_{{ $flow_chart->id }}" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}">
                                <div style="margin-top: 0px;" class="d-flex p-0">
                                    <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{ $vendor->id }}, {{ $flow_chart->id }})"><img src="/images/filled-sent.png"></button>
                                    <button type="button" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}" class="btn btn-image remarks-history-show p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                                </div>
                            </div>
                        </td>
                    @endif
                @endforeach
            @endif
        </tr>
    @else
        <tr>
            <td>{{ $vendor->name }}</td>
            @if($vendor_flow_charts)
                @foreach($vendor_flow_charts as $flow_chart)
                    <td>
                        <div class=" mb-1 p-0 d-flex pt-2 mt-1">
                            <input style="margin-top: 0px;width:80% !important;" type="text" class="form-control " name="message" placeholder="Remarks" id="remark_{{ $vendor->id }}_{{ $flow_chart->id }}" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}">
                            <div style="margin-top: 0px;" class="d-flex p-0">
                                <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{ $vendor->id }}, {{ $flow_chart->id }})"><img src="/images/filled-sent.png"></button>
                                <button type="button" data-vendorid="{{ $vendor->id }}" data-flow_chart_id="{{ $flow_chart->id }}" class="btn btn-image remarks-history-show p-0 ml-2" title="Status Histories"><i class="fa fa-info-circle"></i></button>
                            </div>
                        </div>
                    </td>
                @endforeach
            @endif
        </tr>
    @endif
@endforeach