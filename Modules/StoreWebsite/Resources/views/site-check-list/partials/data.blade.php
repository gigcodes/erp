@if ($store_websites)
    @foreach ($site_development_categories as $sdc)
        <tr>
            <td>
                <span class="break-text"
                    title="{{ $sdc->title }}">{{ strlen($sdc->title) > 25 ? substr_replace($sdc->title, '...', 25) : $sdc->title }}
                </span>
            </td>
            @foreach ($store_websites as $sw)
                @php
                    $website = $sw;
                    $check = 0;
                    $site = $sdc->getDevelopment($sdc->id, $sw->id);
                    // dd($sw, $site);
                    if ($site) {
                        if ($site->is_site_list == 1) {
                            $check = 1;
                        }
                    }
                @endphp
                <td>
                    @php
                        $websitenamestr = $sw ? $sw->title : '';
                    @endphp
                    <div class="col-md-12 mb-1 p-0 d-flex pt-2 mt-1">
                        <input style="margin-top: 0px;" type=" text" class="form-control width-auto quick-message-field"
                            name="message" placeholder="Message" value="" id="remark_{{ $sdc->id . $sw->id }}"
                            data-catId="{{ $sdc->id }}" data-siteId="{{ $site ? $site->id : '' }}"
                            data-websiteId="{{ $website ? $website->id : '' }}" />

                        <button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{ $sdc->id . $sw->id }})"><img
                                src="/images/filled-sent.png" /></button>
                    </div>
                    <div class="col-md-12 mb-1 p-0 d-flex pt-2 mt-1">
                        {{ Form::select('status', ['' => '-- Select --'] + $allStatus, $site['status'], [
                            'class' => 'form-control save-item-select width-auto globalSelect2',
                            'data-category' => $sdc->id,
                            'data-type' => 'status',
                            'data-swid' => $sw['id'],
                            'data-site' => $site ? $site->id : '',
                        ]) }}
                        @if ($site)
                            <button type="button" data-site-id="{{ $site ? $site->id : '' }}"
                                class="btn btn-status-histories-get pd-5" title="Status History">
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                            </button>
                        @endif

                    </div>
                    <div class="col-md-12 p-0 pl-1">
                        <div class="d-flex">
                            <button type="button" style="vertical-align: middle;"
                                data-site-id="@if ($site) {{ $site->id }} @endif"
                                data-site-category-id="{{ $sdc->id }}"
                                data-store-website-id="@if ($website) {{ $website->id }} @endif"
                                class="btn btn-sm btn-store-development-remark">
                                <i class="fa fa-comment" aria-hidden="true"></i>
                            </button>
                            @if ($check)
                                <button title="create quick task" style="vertical-align: middle;" type="button"
                                    class="btn btn-sm d-inline create-quick-task "
                                    data-id="@if ($site) {{ $site->id }} @endif"
                                    data-title="@if ($site) {{ $websitenamestr . ' ' . $site->title }} @endif"
                                    data-category_id="{{ $sdc->id }}">
                                    <i class="fa fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-sm d-inline count-dev-customer-tasks"
                                    title="Show task history" title="Show Task History"
                                    data-id="@if ($site) {{ $site->id }} @endif">
                                    <i class="fa fa-info-circle"></i>
                                </button>
                            @endif

                            <a href="javascript:;" style="vertical-align: middle;" data-sdcid="{{ $sdc->id }}"
                                data-swid="{{ $sw->id }}" data-sdid="{{ isset($site->id) ? $site->id : 0 }}"
                                class="btn btn-sm upload-document-btn">
                                <img width="15px" src="/images/attach.png" alt="" style="cursor: default;">
                            </a>

                            <a href="javascript:;" style="vertical-align: middle;" data-sdcid="{{ $sdc->id }}"
                                data-swid="{{ $sw->id }}" data-sdid="{{ isset($site->id) ? $site->id : 0 }}"
                                class="btn btn-sm list-document-btn">
                                <img width="15px" src="/images/archive.png" alt="" style="cursor: default;">
                            </a>
                        </div>

                        <div class="d-flex">
                            @if ($site)
                                @if ($site->lastRemark)
                                    <div class="justify-content-between expand-row-msg"
                                        data-id="@if ($site->lastRemark) {{ $site->lastRemark->id }} @endif">
                                        <span title="{{ $site->lastRemark->remarks }}"
                                            class="td-full-container-{{ $site->lastRemark ? $site->lastRemark->id : 0 }}">
                                            @if ($site->lastRemark)
                                                {{ str_limit($site->lastRemark->remarks, 15, '...') }}
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </td>
            @endforeach


        </tr>
    @endforeach
@else
    <tr>
        <td>Sorry No Data Available.</td>
    </tr>
@endif
