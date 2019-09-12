
<nav class="active-link-list">
    @if($currentUser->can('settings-manage'))
        <a href="{{ url('/knowledge-base/settings') }}" @if($selected == 'settings') class="active" @endif>@icon('settings'){{ trans('bookstack::settings.settings') }}</a>
        <a href="{{ url('/knowledge-base/settings/maintenance') }}" @if($selected == 'maintenance') class="active" @endif>@icon('spanner'){{ trans('bookstack::settings.maint') }}</a>
    @endif
</nav>