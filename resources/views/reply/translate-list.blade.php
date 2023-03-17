@extends('layouts.app')
<style>
    .editbtn_model {
        position: unset !important;
    }

    .chat-msg{
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-word;
    }
    .chat-msg:hover {
        white-space: normal;
        overflow: visible;
    }
</style>
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Quick Replies Translate List</h2>
        <div class="pull-left">
            <div class="row">
                <div class="col-md-12 ml-sm-4">            
                    <form action="{{ route('reply.replyTranslateList') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-6 pd-sm">
                                {{ Form::select("store_website_id", ["" => "-- Select Website --"] + \App\StoreWebsite::pluck('website','id')->toArray(),request('store_website_id'),["class" => "form-control"]) }}
                            </div>
                            <div class="col-md-5 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                            </div>
                            

                            <div class="col-md-1 pd-sm">
                                 <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="float-right my-3 pr-5">
            @if(auth()->user()->hasRole('Lead Translator'))
                <a class="btn btn-secondary text-white btn_history_permissions" data-toggle="modal" data-target="#history_permissions_model">Permission History</a>
            @endif
                @if(auth()->user()->hasRole('Lead Translator'))
                <a class="btn btn-secondary text-white btn_select_user" data-toggle="modal" data-target="#remove_permissions_model">Remove Permission</a>
            @endif
            @if(auth()->user()->hasRole('Lead Translator'))
                <a class="btn btn-secondary text-white btn_select_user" data-toggle="modal" data-target="#permissions_model">Permission</a>
            @endif
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content ">
    <!-- Pending task div start -->
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;"> 
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;" id="quick-reply-list">
                        <tr>
                            <th class="chat-msg" width="5%">ID</th>
                            <th class="chat-msg" width="10%">Store website</th>
                            <th class="chat-msg" width="10%">Category</th>
							<th class="chat-msg" width="7%">Translate From</th>
                            <th class="chat-msg" width="10%">Original Reply</th>
                            @foreach ($lang as $reply)
                                <th class="chat-msg" width="9%">{{$reply}}</th>
                            @endforeach
                            <th class="chat-msg" width="9%">Created On</th>
							<th class="chat-msg" width="9%">Updated On</th>
                        </tr>
                        @foreach (json_decode($replies) as $key => $reply)
                            <tr>
                                <td id="reply_id">{{ $reply->id }}</td>
                                <td class="expand-row table-hover-cell" style="word-break: break-all;" id="reply-store-website">
                                    <div class="td-mini-container">
                                        {!! strlen($reply->website) > 10 ? substr($reply->website, 0, 10).'...' : $reply->website !!}
                                    </div>
                                    <div class="td-full-container hidden">
                                        {{ $reply->website }}
                                    </div>
                                </td>
                                <td>
                                    <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                        <div class="td-mini-container">
                                            {!! strlen($reply->category_name) > 10 ? substr($reply->category_name, 0, 10).'...' : $reply->category_name !!}
                                        </div>
                                        <div class="td-full-container hidden">
                                            {{ $reply->category_name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="expand-row" id="reply_model">{{ $reply->translate_from }}</td>
								<td style="cursor:pointer;" id="reply_text" class="expand-row change-reply-text" data-id="{{ $reply->id }}" data-message="{{ $reply->original_text }}">
                                    <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                        <div class="td-mini-container">
                                            {!! strlen($reply->original_text) > 20 ? substr($reply->original_text, 0, 20).'...' : $reply->original_text !!}
                                        </div>
                                        <div class="td-full-container hidden">
                                            {{ $reply->original_text }}
                                        </div>
                                    </div>
                                </td>
                                @foreach($reply->translate_text as $key => $translate)
                                    @php
                                      $translate = json_decode(json_encode($translate), true)
                                    @endphp
                                    @if(isset($translate['ar']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['ar'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['ar']) > 10 ? substr($translate['ar'], 0, 10).'..' : $translate['ar'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['ar'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                        <div>
                                                            <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                                <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                            <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                                <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                @endif
                                                @php
                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                @endphp
                                                @if(isset($check_edit_permission))
                                                    <a href="#" class="editbtn_model" data-value="{{$translate['ar']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                @endif
                                                @if(isset($check_view_permission))
                                                    <a href="#" class="history_model" data-value="{{$translate['ar']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @else
                                                    @php
                                                        $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                        $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                    @endphp
                                                    @if(isset($check_edit_permission))
                                                        <a href="#" class="editbtn_model" data-value="{{$translate['ar']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                    @endif
                                                    @if(isset($check_view_permission))
                                                        <a href="#" class="history_model" data-value="{{$translate['ar']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                    @endif
                                            @endif
                                        </td>
                                    @elseif(isset($translate['en']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['en'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['en']) > 10 ? substr($translate['en'], 0, 10).'..' : $translate['en'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['en'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$translate['en']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$translate['en']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$translate['en']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$translate['en']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @elseif(isset($translate['zh-CN']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['zh-CN'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['zh-CN']) > 10 ? substr($translate['zh-CN'], 0, 10).'..' : $translate['zh-CN'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['zh-CN'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$translate['zh-CN']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$translate['zh-CN']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$translate['zh-CN']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$translate['zh-CN']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @elseif(isset($translate['ja']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['ja'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['ja']) > 10 ? substr($translate['ja'], 0, 10).'..' : $translate['ja'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['ja'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$translate['ja']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$translate['ja']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$translate['ja']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$translate['ja']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @elseif(isset($translate['ko']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['ko'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['ko']) > 10 ? substr($translate['ko'], 0, 10).'..' : $translate['ko'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['ko'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$translate['ko']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$translate['ko']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$translate['ko']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$translate['ko']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @elseif(isset($translate['ur']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['ur'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['ur']) > 10 ? substr($translate['ur'], 0, 10).'..' : $translate['ur'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['ur'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$translate['ur']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$translate['ur']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$translate['ur']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$translate['ur']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @elseif(isset($translate['ru']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['ru'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['ru']) > 10 ? substr($translate['ru'], 0, 10).'..' : $translate['ru'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['ru'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$translate['ru']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$translate['ru']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$translate['ru']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$translate['ru']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @elseif(isset($translate['it']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['it'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['it']) > 10 ? substr($translate['it'], 0, 10).'..' : $translate['it'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['it'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$translate['it']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$translate['it']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$translate['it']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$translate['it']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @elseif(isset($translate['fr']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['fr'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['fr']) > 10 ? substr($translate['fr'], 0, 10).'..' : $translate['fr'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['fr'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$translate['fr']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$translate['fr']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$translate['fr']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$translate['fr']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @elseif(isset($translate['es']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['es'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['es']) > 10 ? substr($translate['es'], 0, 10).'..' : $translate['es'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['es'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$translate['es']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$translate['es']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$translate['es']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$translate['es']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @elseif(isset($translate['nl']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['nl'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['nl']) > 10 ? substr($translate['nl'], 0, 10).'..' : $translate['nl'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['nl'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$translate['nl']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$translate['nl']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$translate['nl']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$translate['nl']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @elseif(isset($translate['de']))
                                        <td style="cursor:pointer;" id="reply_text_translate" data-id="{{$reply->translate_id[$key]}}" data-message="{{ $translate['de'] }}">
                                            <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                                <div class="td-mini-container">
                                                    {!! strlen($translate['de']) > 10 ? substr($translate['de'], 0, 10).'..' : $translate['de'] !!}
                                                </div>
                                                <div class="td-full-container hidden">
                                                    {{ $translate['de'] }}
                                                </div>
                                            </div>
                                            @if(auth()->user()->hasRole('Lead Translator'))
                                                <a href="#" class="history_model float-right" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#history"> <i class="fa fa-history" aria-hidden="true"></i></a>
                                                @if(!empty($reply->translate_status[$key]) && $reply->translate_status[$key] == "new")
                                                    <div>
                                                        <input type="radio" class="float-left mt-4" data-id="{{$reply->translate_id[$key]}}" id="radio1" data-lang="{{$reply->translate_lang[$key]}}" name="radio1" value="checked">
                                                        <label style="margin-top: 6px; margin-left: 5px;">Accept</label>
                                                        <input type="radio" class="float-left mt-2" data-id="{{$reply->translate_id[$key]}}" data-lang="{{$reply->translate_lang[$key]}}" id="radio2" name="radio1" value="unchecked">
                                                        <label style="margin-top: 12px; margin-left: 5px;">Reject</label>
                                                        <div>
                                                            @endif
                                                            @php
                                                                $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                            @endphp
                                                            @if(isset($check_edit_permission))
                                                                <a href="#" class="editbtn_model" data-value="{{$translate['de']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                            @endif
                                                            @if(isset($check_view_permission))
                                                                <a href="#" class="history_model" data-value="{{$translate['de']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                            @endif
                                                            @else
                                                                @php
                                                                    $check_edit_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'edit')->first();
                                                                    $check_view_permission = \App\Models\QuickRepliesPermissions::where('user_id', auth()->user()->id)->where('lang_id', $reply->translate_lang[$key])->where('action', 'view')->first();
                                                                @endphp
                                                                @if(isset($check_edit_permission))
                                                                    <a href="#" class="editbtn_model" data-value="{{$translate['de']}}" data-lang="{{$reply->translate_lang[$key]}}" data-user="{{auth()->user()->id}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal" data-target="#edit_model"><i class="fa fa-pencil"></i> </a>
                                                                @endif
                                                                @if(isset($check_view_permission))
                                                                    <a href="#" class="history_model" data-value="{{$translate['de']}}" data-lang="{{$reply->translate_lang[$key]}}" data-id="{{$reply->translate_id[$key]}}" data-toggle="modal"  data-target="#history"> <i class="fa fa-eye"></i> </a>
                                                @endif
                                            @endif
                                        </td>
                                    @else
                                        <td></td>
                                    @endif
                                @endforeach
                                <td>
                                    <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                        <div class="td-mini-container">
                                            {!! strlen($reply->created_at) > 10 ? substr($reply->created_at, 0, 10).'...' : $reply->created_at !!}
                                        </div>
                                        <div class="td-full-container hidden">
                                            {{ date('Y-m-d H:i',strtotime($reply->created_at)) }}
                                        </div>
                                    </div>
                                </td>
								<td>
                                    <?php if($reply->updated_at!='' && $reply->updated_at!=null) {
								    ?>
                                    <div class="expand-row table-hover-cell" style="word-break: break-all;">
                                        <div class="td-mini-container">
                                            {!! strlen($reply->updated_at) > 10 ? substr($reply->updated_at, 0, 10).'...' : $reply->updated_at !!}
                                        </div>
                                        <div class="td-full-container hidden">
                                            {{ date('Y-m-d H:i',strtotime($reply->updated_at)) }}
                                        </div>
                                    </div>
                                <?php
								} else { echo '-'; } ?>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
{{--                    {!! $replies->appends(request()->except('page'))->links() !!}--}}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reply-update-form-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
             <form method="POST" action="{{route('reply.replyUpdate')}}" id="reply-update-form" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Reply</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="id" id="reply-update-model-text-id">
                            <textarea id="reply-update-model-text-reply" class="form-control" name="reply"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                   <!-- <button type="submit" id="create-camp-btn" class="btn btn-secondary">Update</button> -->
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="reply_history_modal">
    <div class="modal-dialog" role="document"style="width: 60%; max-width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tracked time history</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="reply_history_div">
                        <table class="table" style="table-layout:fixed;">
                            <thead>
                                <tr>
                                    <th width="11%">User Name</th>
                                    <th width="60%">Last Message</th>
                                    <th width="17%">Updated Time</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="reply_logs_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Watson push Logs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="reply_logs_div">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Request</th>
                                    <th>Response</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="remove_permissions_model" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Edit Permission</h4>
            </div>
            <div class="modal-body">
                <form action="{{route('remove.permissions')}}" class="permission_form" method="post">
                    @csrf
                    <input type="hidden" class="form-control" name="type" value="remove_permission">
                    <input type="hidden" class="form-control" name="user_permission_id" id="user_permission_id" value="">
                    <div class="form-group">
                        <label>Select User :</label>
                        <select class="form-control" id="selectuserid" name="selectusername">
                            <option>Select</option>
                            @foreach (App\User::where('is_active', '1')->orderBy('name', 'ASC')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label>View Permission</label>
                        <div class="row">
                            <div class="col-12 d-flex">
                            @foreach ($lang as $reply)
                                <div class="form-group p-3">
                                    <label for="lang_name">{{$reply}}</label>
                                    <input type="checkbox" name="view_lang_name[]" id="view_lang_name_{{$reply}}" value="{{$reply}}" style="height: 13px">
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label>Edit Permission</label>
                        <div class="row">
                            <div class="col-12 d-flex">
                                @foreach ($lang as $reply)
                                    <div class="form-group p-3">
                                        <label for="lang_name">{{$reply}}</label>
                                        <input type="checkbox" name="edit_lang_name[]" id="edit_lang_name_{{$reply}}" value="{{$reply}}" style="height: 13px">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="d-none alert alert-class">

                    </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Remove
                        Permission</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="history_permissions_model" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">History</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered text-wrap w-auto min-w-100" style="overflow-x:auto; display: block">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Keyword</th>
                        @foreach ($lang as $reply)
                            <th width="5%">{{$reply}}</th>
                        @endforeach
                        <th>Updator</th>
                        <th>Approver</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody class="all_data_history">
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="permissions_model" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">Edit Permission</h4>
            </div>
            <div class="modal-body">
                <form class="permission_form">
                    <div class="form-group">
                        <label>Select User :</label>
                        <select class="form-control" id="selectUserId" name="user">
                            <option>Select</option>
                            @foreach (App\User::where('is_active', '1')->orderBy('name', 'ASC')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Lanuage</label>
                        <select class="form-control" name="lang" id="selectLangId">
                            <option>Select</option>
                            @foreach ($lang as $reply)
                                <option value="{{$reply}}">{{$reply}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Action</label>
                        <select class="form-control" name="action" id="actionId">
                            <option>Select</option>
                            <option value="view">view</option>
                            <option value="edit">edit</option>
                        </select>
                    </div>
                    <div class="d-none alert alert-class">

                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-submit-form">Add
                    Permission</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="history" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title position-absolute">History</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered text-wrap w-auto min-w-100">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Keyword</th>
                        @foreach ($lang as $reply)
                        <th width="5%">{{$reply}}</th>
                        @endforeach
                        <th>Updator</th>
                        <th>Approver</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody class="data_history">
                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_model" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="form-update" action="{{route('reply.replyTranslateupdate')}}">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title position-absolute">Update Value</h4>
                </div>
                <div class="modal-body edit_model_body">
                    @csrf
                    <input type="text" name="update_record" class="form-control update_record" />
                    <div class="d-none add_hidden_data"></div>


                </div>
                <div class="modal-footer">
                    <input type="submit" value="update" name="update" class="btn btn-secondary" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">

$(document).on('click', '.expand-row', function() {
    var selection = window.getSelection();
    if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
    }
});

$(document).on("click",".fa-trash-bin-record",function() {
    var $this = $(this);
    $.ajax({
        url: "{{ url('reply-list/delete') }}",
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          id: $this.data("id")
        },
        beforeSend: function() {
            $("#loading-image").show();
        }
      }).done( function(response) {
            $("#loading-image").hide();
            if(response.code == 200) {
                toastr["success"](response.message);
                location.reload();
            }else{
               toastr["error"]('Record is unable to delete!');
            }
      }).fail(function(errObj) {
            $("#loading-image").hide();
      });
});

$(document).on("click",".push_to_watson",function() {
    var $this = $(this);
    $.ajax({
        url: "{{ url('push-reply-to-watson') }}",
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          id: $this.data("id")
        },
        beforeSend: function() {
            $("#loading-image").show();
        }
      }).done( function(response) {
            $("#loading-image").hide();
            if(response.code == 200) {
                toastr["success"](response.message);
                //location.reload();
            }else{
               toastr["error"]('Unable to push!');
            }
      }).fail(function(errObj) {
            $("#loading-image").hide();
      });
});

$(document).on("click",".change-reply-text",function(e) {
    e.preventDefault();
    var $this = $(this);
    $("#reply-update-model-text-id").val($this.data("id"));
    $("#reply-update-model-text-reply").val($this.data("message"));
    $("#reply-update-form-modal").modal("show");
});

$(document).on('click', '.show-reply-history', function() {
    var issueId = $(this).data('id');
    $('#reply_history_div table tbody').html('');
    $.ajax({
        url: "{{ route('reply.replyhistory') }}",
        data: {id: issueId},
        success: function (data) {
            if(data != 'error') {
                $.each(data.histories, function(i, item) {
                    $('#reply_history_div table tbody').append(
                        '<tr>\
                        <td class="Website-task">'+ ((item['name'] != null) ? item['name'] : '') +'</td>\
                        <td class="Website-task">'+ ((item['last_message'] != null) ? item['last_message'] : '') +'</td>\
                        <td>'+ ((item['created_at'] != null) ? item['created_at'] : '') +'</td>\
                        </tr>'
                        );
                });
            }
        }
    });
    $('#reply_history_modal').modal('show');
});

$(document).on('click', '.show_logs', function() {
    var issueId = $(this).data('id');
    $('#reply_logs_div table tbody').html('');
    $.ajax({
        url: "{{ route('reply.replylogs') }}",
        data: {id: issueId},
        success: function (data) {
            if(data != 'error') {
                $.each(data.logs, function(i, item) {
                    $('#reply_logs_div table tbody').append(
                        '<tr>\
                        <td>'+ ((item['created_at'] != null) ? item['created_at'] : '') +'</td>\
                        <td>'+ ((item['request'] != null) ? item['request'] : '') +'</td>\
                        <td>'+ ((item['response'] != null) ? item['response'] : '') +'</td>\
                        </tr>'
                        );
                });
            }
        }
    });
    $('#reply_logs_modal').modal('show');
});

$(document).on('click', ".editbtn_model", function() {
    setTimeout(function() {
        $("#Show_message_display").modal('hide');
    }, 1);
    var id = $(this).data('id');
    var formValue = $(this).data('value');
    var userId = $(this).data('user');
    var langId = $(this).data('lang');
    $(".update_record").val(formValue);
    let html = `<input type="hidden" name="update_by_user_id" value='`+userId+`'>
        <input type="hidden" name="lang_id" value='`+langId+`'>
        <input type="hidden" name="record_id" value='`+id+`'>`;
    $(".add_hidden_data").html(html);

});

$(document).on('click','.history_model',function(){
    setTimeout(function() {
        $("#Show_message_display").modal('hide');
    }, 0.1);
    var id = $(this).data('id');
    var key = $(this).data('key');
    var language = $(this).data('lang');

    $.ajax({
        url:"{{ route('reply.replyTranslatehistory') }}",
        method:'POST',
        data:{'id':id,"key":key,"language":language,'_token':"{{csrf_token()}}"},
        success:function(response){
            $("#Show_message_display").modal('hide');
            $(".data_history").html('');
            if(response.data.length == 0){
                $(".data_history").html('<tr colspan="12"><td class="text-center">No Data Found</td></tr>');
            }else{
                $(".data_history").html(response.data);
            }
        }
    })
});

$(document).on('click','.btn_history_permissions',function(){
    setTimeout(function() {
        $("#Show_message_display").modal('hide');
    }, 0.1);
    var id = $(this).data('id');
    var key = $(this).data('key');
    var language = $(this).data('lang');

    $.ajax({
        url:"{{ route('reply.replyTranslatehistory') }}",
        method:'POST',
        data:{'type':'all_view','_token':"{{csrf_token()}}"},
        success:function(response){
            $("#Show_message_display").modal('hide');
            $(".all_data_history").html('');
            if(response.data.length == 0){
                $(".all_data_history").html('<tr colspan="12"><td class="text-center">No Data Found</td></tr>');
            }else{
                $(".all_data_history").html(response.data);
            }
        }
    })
});

$(document).on('click', ".btn-submit-form", function() {
    var userId = $("#selectUserId").val();
    var langId = $("#selectLangId").val();
    var actionId = $("#actionId").val();
    $(".alert-class").text('');
    $.ajax({
        url:'{{route('reply.permissions')}}',
        method:'POST',
        data:{'user_id':userId,'lang_id':langId,'action':actionId,'_token':"{{csrf_token()}}"},
        success:function(response){
            if(response.status === 200){
                $(".alert-class").text("Successfully added");
                $(".alert-class").addClass("alert-success");
                $(".alert-class").removeClass("alert-danger");
                $(".alert-class").removeClass("d-none");
                window.location.reload();
            }else{
                $("#selectUserId").val('');
                $("#selectLangId").val('');
                $("#actionId").val('');
                $(".alert-class").removeClass("alert-success");
                $(".alert-class").text("Permission already exist");
                $(".alert-class").removeClass("d-none");
                $(".alert-class").addClass("alert-danger");
            }
        }
    })
});

$(document).on("change",'input:radio[name="radio1"]',function(){
    var id,language,status;
    if($(this).val() == 'checked'){
        id = $(this).data('id');
        language = $(this).data('lang');
        status = "checked";
    }else if($(this).val() == 'unchecked'){
        id = $(this).data('id');
        language = $(this).data('lang');
        status = "unchecked";
    }
    $.ajax({
        url:'{{route('reply.approved_by_admin')}}',
        method:"POST",
        data:{"id":id,"lang":language,"status":status,"_token":"{{csrf_token()}}"},
        success:function(response){
            if (response.status == 200)
            {
                toastr["success"]('Data Updated Successully');
                location.reload();
            }else {
                toastr["error"]('Unable to update this record!');
            }
        }
    })
});

$('#selectuserid').on("change", function(){
    var user_id = $('#selectuserid').val();
    $('#user_permission_id').val(user_id);

    $.ajax({
        url:'{{route('remove.permissions')}}',
        method:"POST",
        data:{"id":user_id,"_token":"{{csrf_token()}}"},
        success:function(response){
            if (response.status == 200)
            {
                $.each(response.edit_lang, function( index, value ) {
                    $('#edit_lang_name_'+value).attr('checked', true);
                });

                $.each(response.view_lang, function( index, value ) {
                    $('#view_lang_name_'+value).attr('checked', true);
                });
                toastr["success"]('Data Updated Successully');
            }else {
                toastr["error"]('Unable to update this record!');
            }
        }
    })
});


function updateTranslateReply(ele) {
    let btn = jQuery(ele);
    let reply_id = btn.data('reply_id');
    let is_flagged = btn.data('is_flagged');
	
	//alert(jQuery(ele).is(':checked'));
	
	//alert(is_flagged)

    if (confirm(btn.data('is_flagged') == 1 ? 'Are you sure? Do you want unflagged this ?' : 'Are you sure want flagged this ?')) {
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('reply.replytranslate') }}",
            type: 'POST',
            data: {
                reply_id: reply_id,
                is_flagged: is_flagged,
            },
            dataType: 'json',
            beforeSend: function () {
                jQuery("#loading-image").show();
            },
            success: function (res) {
                toastr["success"](res.message);
                jQuery("#loading-image").hide();
                btn.find('.fa').removeClass('fa-toggle-on fa-toggle-off');
                if (is_task_planned == 1) {
                    btn.find('.fa').addClass('fa-toggle-off');
                }
                else {
                    btn.find('.fa').addClass('fa-toggle-on');
                }
                btn.data('is_task_planned', is_task_planned == 1 ? 0 : 1);
            },
            error: function (res) {
                if (res.responseJSON != undefined) {
                    toastr["error"](res.responseJSON.message);
                }
                jQuery("#loading-image").hide();
            }
        });
    }
}
</script>
@endsection

