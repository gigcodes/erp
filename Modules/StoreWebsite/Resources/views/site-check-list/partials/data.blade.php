@if ($store_websites)
    @foreach ($site_development_categories as $key1 => $sdc)
        <tr>
            <td>
                {{ $sdc->title }}
            </td>
            @foreach ($store_websites as $key2 => $sw)
                @php
                    $website = $sw;
                    $check = 0;
                    $site = $sdc->getDevelopment($sdc->id, $sw->id);
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
                    <div class="col-md-12 mb-1 p-0 d-flex  pt-2 mt-1">
                        <input style="margin-top: 0px;width:auto !important;" type="text"
                            class="form-control quick-message-field" name="message" placeholder="Message" value=""
                            id="remark_{{ $sdc->id . $sw->id }}" data-catId="{{ $sdc->id }}"
                            data-siteId="@if ($site) {{ $site->id }} @endif"
                            data-websiteId="@if ($website) {{ $website->id }} @endif">



                        <br />

                        <div style="margin-top: 0px;" class="d-flex p-0">
                            <button class="btn pr-0 btn-xs btn-image "
                                onclick="saveRemarks({{ $sdc->id . $sw->id }})"><img
                                    src="/images/filled-sent.png" /></button>
                        </div>
                    </div>
                    <div class="col-md-12 p-0 pl-1">
                        {{-- {{ $site->id }} --}}
                        {{-- @php
                            $website = $sw;
                            $check = 0;
                            $site = $sdc->getDevelopment($sdc->id, $sw->id);
                            if ($site) {
                                if ($site->is_site_list == 1) {
                                    $check = 1;
                                }
                            }
                        @endphp --}}

                        {{ Form::select('status', ['' => '-- Select --'] + $allStatus, $site['status'], [
                            'class' => 'form-control save-item-select width-auto globalSelect2',
                            // 'data-category' => $category->id,
                            'data-type' => 'status',
                            'data-site' => $site ? $site->id : '',
                        ]) }}

                    </div>
                    <div class="col-md-12 p-0 pl-1">
                        <div class="d-flex">
                            @if ($site)
                                @if ($site->lastRemark)
                                    Remarks =
                                @endif
                                <div class="justify-content-between expand-row-msg"
                                    data-id="@if ($site->lastRemark) {{ $site->lastRemark->id }} @endif">
                                    <span
                                        class="td-full-container-@if ($site->lastRemark) {{ $site->lastRemark->id }} @endif">
                                        @if ($site->lastRemark)
                                            {{ str_limit($site->lastRemark->remarks, 10, '...') }}
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                        <button type="button"
                            data-site-id="@if ($site) {{ $site->id }} @endif"
                            data-site-category-id="{{ $sdc->id }}"
                            data-store-website-id="@if ($website) {{ $website->id }} @endif"
                            class="btn btn-store-development-remark pd-5">
                            <i class="fa fa-comment" aria-hidden="true"></i>
                        </button>
                        @if ($check)
                            <button title="create quick task" type="button"
                                class="btn btn-image d-inline create-quick-task p-2"
                                data-id="@if ($site) {{ $site->id }} @endif"
                                data-title="@if ($site) {{ $websitenamestr . ' ' . $site->title }} @endif"
                                data-category_id="{{ $sdc->id }}"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-image d-inline count-dev-customer-tasks p-2"
                                title="Show task history" title="Show Task History"
                                data-id="@if ($site) {{ $site->id }} @endif"><i
                                    class="fa fa-info-circle"></i></button>
                        @endif
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
