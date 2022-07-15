    @php
        $isAdmin = auth()
            ->user()
            ->isAdmin();
        $isHod = auth()
            ->user()
            ->hasRole('HOD of CRM');
        $hasSiteDevelopment = auth()
            ->user()
            ->hasRole('Site-development');
        $userId = auth()->user()->id;
        $pagrank = $categories->perPage() * ($categories->currentPage() - 1) + 1;
    @endphp
    @foreach ($site_development_categories as $key => $category)
        <?php
    //$site = $category->getDevelopment($category->id, isset($website) ? $website->id : $category->website_id, $category->site_development_id);//
    $site = $category->getDevelopment($category->id,  $category->website_id, $category->site_development_id);
    $uiCheck = App\Uicheck::where('site_development_id', $category->site_id)->first();
    // dd($uiCheck);
    //if ($isAdmin || $hasSiteDevelopment || ($site && $site->developer_id == $userId)) {
    ?>

        <tr>
            <td>
                {{ $pagrank++ }}
            </td>
            <td>
                {{ $category->title }}
            </td>
            <td>
                @if (Auth::user()->hasRole('Admin'))
                {{-- {{ Form::select('website_id',['' => '- Select-'] + $all_store_websites->toArray(),$uiCheck->website_id ?? '',['class' => 'save-item-select globalSelect2','data-category' => $category->id,'data-type' => 'site_development_master_category_id','data-site' => $site ? $site->id : '0']) }} --}}
                <select name="website_id"  class="save-item-select globalSelect2 website_id" data-category="{{$category->id ?? '0'}}" data-id="{{$uiCheck->id ?? '0' }}" data-site_development_id="{{$category->site_id }}">
                    <option value="">--Select--</option>
                    @foreach ($all_store_websites as $website )
                        <?php $selected = "";  
                        $uiWebsiteId = $uiCheck->website_id ?? '';
                        if($uiWebsiteId == $website->id){
                            $selected = "selected='selected'"; 
                        }
                        ?>
                        <option value="{{$website->id}}" {{$selected}} >{{$website->website}}</option>
                    @endforeach
                </select>
                @endif
            </td>
            <td class="pt-0 pr-2">
                <div class="col-md-12 mb-1 p-0 d-flex pt-2 mt-1">
                    <input style="margin-top: 0px;width:auto !important;" type="text"
                        class="form-control " id="issue-{{$uiCheck->id ?? ''}}" name="issue-{{$uiCheck->id ?? ''}}" placeholder="Issues" value="{{$uiCheck->issue ?? ''}}"
                        >
                    <div style="margin-top: 0px;" class="d-flex p-0">
                        <button class="btn pr-0 btn-xs btn-image issue" 
                        data-category="{{$category->id ?? ''}}" data-id="{{$uiCheck->id ?? '' }}" data-site_development_id="{{$category->site_id }}"
                        ><img
                                src="/images/filled-sent.png" /></button>
                    </div>
                </div>
            </td>

            <td>
                <div class="col-md-12 mb-1 p-0 d-flex pl-4 pt-2 mt-1 msg">
                    @if (Auth::user()->hasRole('Admin'))
                    <input type="text" style="width: 100%; float: left;"
                        class="form-control quick-message-field input-sm" name="message"
                        placeholder="Message" value="">
                    <div class="d-flex p-0">
                        <button style="float: left;padding: 0 0 0 5px"
                            class="btn btn-sm btn-image send-message" title="Send message"
                            data-category="{{$category->id ?? ''}}" data-taskid="{{$uiCheck->id ?? '' }}"  data-id="{{$uiCheck->id ?? '' }}" data-site_development_id="{{$category->site_id }}"
                            ><img src="/images/filled-sent.png"
                                style="cursor: default;"></button>
                    </div>
                    @endif
                    <button type="button"
                        class="btn btn-xs btn-image load-communication-modal load-body-class"
                        data-object="uicheck"
                        data-id="{{ $uiCheck->id ?? '' }}" title="Load messages"
                        data-category="{{$category->id ?? ''}}" data-site_development_id="{{$category->site_id }}"
                        data-dismiss="modal"><img src="/images/chat.png" alt=""></button>
                </div>

                <div class="col-md-12 p-0 pl-1 text">
                    <!-- START - Purpose : Show / Hide Chat & Remarks , Add Last Remarks - #DEVTASK-19918 -->
                    <div class="d-flex">
                        <div class="justify-content-between expand-row-msg-chat"
                            data-id="{{ $uiCheck->id  ?? ''  }}"
                            data-category="{{$category->id ?? ''}}" data-site_development_id="{{$category->site_id }}">
                            <span class="td-full-chat-container-{{ $assignedTo['id']  ?? 0}} pl-1">
                                {{ str_limit($assignedTo['message'] ?? 0, 20, '...') }} </span>
                        </div>
                    </div>
                    <div class="expand-row-msg-chat" data-id="{{ $uiCheck->id ?? ''   }}">
                        <span class="td-full-chat-container-{{ $uiCheck->id  ?? ''  }} hidden">
                            {{ $assignedTo['message'] ?? 0 }} </span>
                    </div>
                    <!-- END - #DEVTASK-19918 -->
                </div>
            </td>

            
            <td class="pt-1">
                <?php echo Form::select('developer_status', ['' => '-- Select --'] + $allStatus, $uiCheck->dev_status_id ?? '', [
                    'class' => 'form-control save-item-select width-auto globalSelect2 developer_status',
                    'data-type' => 'developer_status',
                    "data-category" =>  $category->id ?? '', 
                    "data-id" => $uiCheck->id ?? '', 
                    "data-site_development_id" => $category->site_id
                ]); ?>

            </td>

            <td class="pt-1">
                @if (Auth::user()->hasRole('Admin'))
                <?php echo Form::select('admin_status', ['' => '-- Select --'] + $allStatus, $uiCheck->admin_status_id ?? '', [
                    'class' => 'form-control save-item-select width-auto globalSelect2 admin_status',
                    'data-type' => 'admin_status',
                    "data-category" =>  $category->id ?? '', 
                    "data-id" => $uiCheck->id ?? '',  
                    "data-site_development_id" => $category->site_id
                ]); ?>
                @endif
            </td>
        </tr>
  
    @endforeach
