@extends('bookstack::base')

@section('body-class', 'tri-layout')

@section('content')

    <div class="tri-layout-mobile-tabs text-primary print-hidden">
        <div class="grid half no-break no-gap">
            <div class="tri-layout-mobile-tab px-m py-s" tri-layout-mobile-tab="info">
                {{ trans('bookstack::common.tab_info') }}
            </div>
            <div class="tri-layout-mobile-tab px-m py-s active" tri-layout-mobile-tab="content">
                {{ trans('bookstack::common.tab_content') }}
            </div>
        </div>
    </div>

    <div class="tri-layout-container" tri-layout @yield('container-attrs') >
 

        <div class="@yield('body-wrap-classes') tri-layout-middle">
        @yield('body')
        </div>

    </div>

@stop
