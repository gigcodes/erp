<li class="nav-item dropdown notification-dropdown">
    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" aria-haspopup="true" aria-expanded="false" v-pre>
        Notifications<span class="caret"></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

        @foreach($notifications as $notification)

        <li class="dropdown-item {{ $notification->isread ? 'isread' : '' }}">
            <div class="row">
            <div class="col-10"><p>{{ $notification->uname }} {{ $notification->message }} {{$notification->pname ? $notification->pname : $notification->sku }} </p></div>

            <div class="col-2">
                <button class="btn btn-notify" data-id="{{ $notification->id }}" >&#10003</button>
            </div>
            </div>
        </li>
        <li class=""></li>

        @endforeach
            <div class="notify-drop-footer text-center">
                <a href="{{ route('notifications') }}">See All</a>
            </div>
    </ul>


</li>

<script>

    let interval = 1000*20;  // 1000 = 1 second
    function getNotificaitons() {

        jQuery.ajax({
            type: 'GET',
            url: '{{ Route('notificationJson') }}',
            dataType: 'json',
            success: function (data) {
                console.log(data);
            },
            complete: function (data) {
                // Schedule the next
                setTimeout(getNotificaitons, interval);
            }
        });
    }
    setTimeout(getNotificaitons, interval);

</script>
