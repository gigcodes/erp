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








                    <?php /*
                        if (count($category->assignedTo) > 0)
                            echo '<a href="javascript::void();" data-id="' . $pagrank . '" class="show_moreCls" style="float: left;height: 16px;width: 100%;"><i class="fa fa-info-circle"></i></a>';
                        $tableTrCounter = 0; ?>
                        @foreach ($category->assignedTo as $assignedTo)
                        <?php $tableTrCounter++;
                        if ($tableTrCounter != 1)
                            $tTrClass = 'comm-' . $pagrank . ' hidden';
                        else
                            $tTrClass = '';
                        ?>

                        <div class="row {{$tTrClass}}" style="overflow: hidden;width: 100%;float: left;margin-left: 0px;border: 1px solid #bfbfbf;padding-top: 4px;margin-top: 2px;margin-bottom: 7px;">
                            <div class="col-4" style="margin: 0;padding: 5px;">
                                @if (auth()->user()->isAdmin())
                                <select class="form-control assign-user" data-id="{{ $assignedTo['id'] }}" name="master_user_id" style="width: 100% !important;">
                                    <option value="">Select...</option>
                                    @foreach ($users_all as $value)
                                    @if ($assignedTo['assigned_to_name'] == $value->name)
                                    <option value="{{ $value->id }}" selected>{{ $value->name }}
                                    </option>
                                    @else
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                @else
                                {{ $assignedTo['assigned_to_name'] }}
                                @endif
                            </div>
                            <div class="col-8" style="margin: 0;padding: 5px;">
                                <div class="mb-1 p-0 d-flex pl-0 pt-0 mt-1 msg">
                                    <?php
                                    $MsgPreview = '# ';
                                    if ($website) {
                                        $MsgPreview = $website->website;
                                    }
                                    if ($site) {
                                        $MsgPreview = $MsgPreview . ' ' . $site->title;
                                    }
                                    ?>
                                    <input type="text" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message" value="">
                                    <div class="d-flex p-0">
                                        <button style="float: left;padding: 0 0 0 5px" class="btn btn-sm btn-image send-message" title="Send message" data-taskid="{{ $assignedTo['id'] }}"><img src="/images/filled-sent.png" style="cursor: default;"></button>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-image load-communication-modal load-body-class" data-object="{{ $assignedTo['message_type'] }}" data-id="{{ $assignedTo['id'] }}" title="Load messages" data-dismiss="modal"><img src="/images/chat.png" alt=""></button>
                                </div>

                                <div class="col-md-12 p-0 pl-1 text">
                                    <!-- START - Purpose : Show / Hide Chat & Remarks , Add Last Remarks - #DEVTASK-19918 -->
                                    <div class="d-flex">
                                        <div class="justify-content-between expand-row-msg-chat" data-id="{{ $assignedTo['id'] }}">
                                            <span class="td-full-chat-container-{{ $assignedTo['id'] }} pl-1">
                                                {{ str_limit($assignedTo['message'], 30, '...') }} </span>
                                        </div>
                                    </div>
                                    <div class="expand-row-msg-chat" data-id="{{ $assignedTo['id'] }}" style="white-space: normal;">
                                        <span class="td-full-chat-container-{{ $assignedTo['id'] }} hidden">
                                            {{ $assignedTo['message'] }} </span>
                                    </div>
                                    <!-- END - #DEVTASK-19918 -->
                                </div>
                            </div>
                        </div>

                        @endforeach
                        */ ?>
                </td>
            @endforeach


        </tr>
    @endforeach
@else
    <tr>
        <td>Sorry No Data Available.</td>
    </tr>
@endif
