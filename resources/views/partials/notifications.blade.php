<li class="nav-item dropdown notification-dropdown">
    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" aria-haspopup="true"
       aria-expanded="false" v-pre>
        Notifications<span class="caret"></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

        @foreach($notifications as $notification)

            <li class="dropdown-item {{ $notification->isread ? 'isread' : '' }}">
                <div class="row">
                    <div class="col-10">
                        <a class="notification-link"
                           href="{{ $notification->sale_id ? route('sales.edit',$notification->sale_id) : route('products.show',$notification->product_id)  }}">
                            <p>{{ $notification->uname }} {{ str_limit($notification->message,50,'...') }}
                                {{$notification->pname ? $notification->pname : $notification->sku }}
                                at {{ $notification->created_at }}
                            </p>
                        </a>
                    </div>

                    <div class="col-2">
                        <button class="btn btn-notify" data-id="{{ $notification->id }}"></button>
                        {{-- &#10003 --}}
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
    let interval = 1000 * 30;  // 1000 = 1 second
    const notificationShowCount = 15;
    // const TaskShowCount = 5;
    // const OrderShowCount = 5;
    // const LeadShowCount = 5;
    // const MessageShowCount = 5;

    let allUsers = {!! json_encode( \App\Helpers::getUserArray( \App\User::all() ) ) !!};
    let current_userid = '{{ Auth::id() }}';
    let current_username = '{{ Auth::user()->name }}';
    let is_admin = "{{ Auth::user()->hasRole('Admin') }}";

    // Queue class
    class Queue {
        // Array is used to implement a Queue
        constructor() {

            this.items = [];
            this.isFirst = true;
            this.count = 0;
            this.taskcount = 0;
            this.ordercount = 0;
            this.leadcount = 0;
            this.messagecount = 0;
        }
        // Functions to be implemented
        // enqueue function
        enqueue(element) {

            // adding element to the queue
            let i;
            for (i = 0; i < this.items.length; i++) {

                if (this.items[i].id === element.id) {
                    break;
                }
            }

            if (i === this.items.length) {

                element['isShown'] = false;
                this.items.push(element);


            }

            /*if(this.isFirst){
                toast(this.front());
                this.isFirst = false;
                this.count++;
            }*/

            this.notificationCount();
        }

        // dequeue function
        dequeue() {
            // removing element from the queue
            // returns underflow when called
            // on empty queue
            if (this.isEmpty())
                return false;
                // return "Underflow";
            let result = this.items.shift();

            this.notificationCount();
            return result;
        }

        dequeueWithId(notificationId){

            if (this.isEmpty())
                return false;

            for (let i = 0; i < this.items.length; i++) {

                if (this.items[i].id === notificationId) {

                    let item = this.items.splice(i,1);
                    this.count--;

                    switch (item[0].model_type) {
                      case 'App\\Sale':
                          this.ordercount--;

                          break;
                      case 'App\\Task':
                          this.taskcount--;

                          break;
                      case 'User':
                          this.taskcount--;

                          break;
                      case 'App\\Order':
                          this.ordercount--;

                          break;
                      case 'App\\Leads':
                          this.leadcount--;

                          break;
                      case 'order':
                          this.ordercount--;

                          break;
                      case 'leads':
                          this.leadcount--;

                          break;
                    }
                }
            }

            this.notificationCount();
        }

        // front function
        front() {
            // returns the Front element of
            // the queue without removing it.
            if (this.isEmpty())
                return false;
            // return "No elements in Queue";
            return this.items[0];
        }

        // isEmpty function
        isEmpty() {
            // return true if the queue is empty.
            return this.items.length === 0;
        }

        getQueue(){
            return this.items;
        }

        notificationCount(){
            if($('#notification_count').length === 0) {
                $('.notifications-container').prepend('<div id="notification_count"></div>');
            }

            $('#notification_count').html(this.items.length);
        }

        showNotification(){
            for (let i = 0; i < this.items.length; i++) {

                if( this.count === notificationShowCount)
                    break;

                if( !this.items[i]['isShown'] ) {
                  switch (this.items[i].model_type) {
                    case 'App\\Sale':
                      if (this.ordercount < notificationShowCount / 3) {
                        this.items[i]['isShown'] = true;
                        toast(this.items[i]);
                        this.ordercount++;

                      }
                      break;


                    case 'App\\Task':
                      if (this.taskcount < notificationShowCount / 3) {
                        this.items[i]['isShown'] = true;
                        toast(this.items[i]);
                        this.taskcount++;

                      }
                      break;

                    case 'App\\SatutoryTask':
                      if (this.taskcount < notificationShowCount / 3) {
                        this.items[i]['isShown'] = true;
                        toast(this.items[i]);
                        this.taskcount++;

                      }
                      break;

                      // break;
                    case 'User':
                      if (this.taskcount < notificationShowCount / 3) {
                        this.items[i]['isShown'] = true;
                        toast(this.items[i]);
                        this.taskcount++;

                      }
                      break;

                      // break;
                    case 'App\\Order':
                      if (this.ordercount < notificationShowCount / 3) {
                        this.items[i]['isShown'] = true;
                        toast(this.items[i]);
                        this.ordercount++;

                      }
                      break;

                      // break;
                    case 'App\\Leads':
                      if (this.leadcount < notificationShowCount / 3) {
                        this.items[i]['isShown'] = true;
                        toast(this.items[i]);
                        this.leadcount++;

                      }
                      break;

                      // break;
                    case 'order':
                      if (this.messagecount < notificationShowCount / 3) {
                        this.items[i]['isShown'] = true;
                        toast(this.items[i]);
                        this.ordercount++;

                      }
                      break;

                      // break;
                    case 'leads':
                      if (this.messagecount < notificationShowCount / 3) {
                        this.items[i]['isShown'] = true;
                        toast(this.items[i]);
                        this.leadcount++;

                      }
                      break;

                      // break;
                  }
                    // this.items[i]['isShown'] = true;
                    // toast(this.items[i]);
                    this.count++;

                }
            }
        }

        postPoneNotification(notificationId){
            if (this.isEmpty())
                return false;

            for (let i = 0; i < this.items.length; i++) {

                if (this.items[i].id === notificationId) {

                    let item = this.items.splice(i,1);
                    this.count--;

                    switch (item[0].model_type) {
                      case 'App\\Sale':
                          this.ordercount--;

                          break;
                      case 'App\\Task':
                          this.taskcount--;

                          break;
                      case 'App\\SatutoryTask':
                          this.taskcount--;

                          break;
                      case 'User':
                          this.taskcount--;

                          break;
                      case 'App\\Order':
                          this.ordercount--;

                          break;
                      case 'App\\Leads':
                          this.leadcount--;

                          break;
                      case 'order':
                          this.ordercount--;

                          break;
                      case 'leads':
                          this.leadcount--;

                          break;
                    }

                    this.enqueue(item[0]);
                    break;
                }
            }
            this.notificationCount();
            this.showNotification();
        }

    }

    // $(document).on('click','.toast-info',function () {
    //    $('.toast-info').toggleClass('toast-stack');
    //    $('#toast-container').toggleClass('toast-container-stacked');
    // });

    $(document).on('click','.notification',function () {
      $('.stack-container').not($(this).parent()).addClass('stacked');
      $(this).parent().toggleClass('stacked');

       // $(this).parent().toggleClass('notification-height');
       // $('#toast-container').toggleClass('toast-container-stacked');
    });

    let notificationQueue = new Queue();

    function getNotificaitons() {

        jQuery.ajax({
            type: 'GET',
            url: '{{ Route('pushNotifications') }}',
            dataType: 'json',
            success: data => {

                data.forEach(notification => {
                    notificationQueue.enqueue(notification);
                });

                notificationQueue.showNotification();
                notificationQueue.notificationCount();
            },
            complete: data => setTimeout(getNotificaitons, interval),// Schedule the next
        });
    }

    //Instantly get notifications on page load.
    getNotificaitons();

    function toast(notification) {
        let link, message, img_position, message_without_img, notification_html, close_button;

        if (notification.type === 'button' && (is_admin == false)) {
          close_button = '';
        } else {
          close_button = '<button type="button" class="notification-close" role="button" data-id="' + notification.id + '">x</button>';
        }

        switch (notification.model_type) {
            case 'App\\Sale' :

                link = '/sales/' + notification.model_id + '/edit';
                message = `<h4>ID : ${notification.model_id} New Sale</h4><a href="${link}" style="padding-bottom: 10px;">${notification.message.length > 40 ? (notification.message.substring(0, 40 - 3) + '...') : notification.message} - ${moment(notification.created_at).format('H:m')}</a>`;

                notification_html = '<div class="notification">' + close_button + message + '</div>';
                $('#orders-notification').append(notification_html);
                $('#orders-notification').css({'display': 'block'});

                break;

            case 'App\\Task':
                link = `/task#task_${notification.model_id}`;
                message = `<h4>ID : ${notification.model_id} Task</h4>
                            <span>By :- ${ allUsers[notification.user_id] }</span><br>
                            <a href="${link}">${notification.message.length > 40 ? (notification.message.substring(0, 40 - 3) + '...') : notification.message}</a>${ getStatusButtons(notification) }`;

                notification_html = '<div class="notification">' + close_button + message + '</div>';
                $('#tasks-notification').append(notification_html);
                $('#tasks-notification').css({'display': 'block'});

                break;

            case 'App\\SatutoryTask':
                link = `/task#task_${notification.model_id}`;
                message = `<h4>ID : ${notification.model_id} Task</h4>
                            <span>By :- ${ allUsers[notification.user_id] }</span><br>
                            <a href="${link}">${notification.message.length > 40 ? (notification.message.substring(0, 40 - 3) + '...') : notification.message}</a>${ getStatusButtons(notification) }`;

                notification_html = '<div class="notification">' + close_button + message + '</div>';
                $('#tasks-notification').append(notification_html);
                $('#tasks-notification').css({'display': 'block'});

                break;

            case  'User':
                link = `/#task_${notification.model_id}`;
                message = `<h4>ID : ${notification.model_id} Task</h4><a href="${link}" style="padding-bottom: 10px; display: block;">${notification.message.length > 40 ? (notification.message.substring(0, 40 - 3) + '...') : notification.message} - ${moment(notification.created_at).format('H:m')}</a>`;

                notification_html = '<div class="notification">' + close_button + message + '</div>';
                $('#tasks-notification').append(notification_html);
                $('#tasks-notification').css({'display': 'block'});

                break;

            case 'App\\Leads':

                link = '/leads/' + notification.model_id;
                message = `<h4>ID : ${notification.model_id} New Lead</h4><a href="${link}">${notification.message.length > 40 ? (notification.message.substring(0, 40 - 3) + '...') : notification.message} - ${moment(notification.created_at).format('H:m')}</a>${ getStatusButtons(notification) }`;
                notification_html = '<div class="notification">' + close_button + message + '</div>';
                $('#leads-notification').append(notification_html);
                $('#leads-notification').css({'display': 'block'});
                break;

            case 'App\\Order':

                link = '/order/' + notification.model_id + '/edit';
                message = `<h4>ID : ${notification.model_id} New Order</h4><a href="${link}">${notification.message.length > 40 ? (notification.message.substring(0, 40 - 3) + '...') : notification.message} - ${moment(notification.created_at).format('H:m')}</a>${ getStatusButtons(notification) }`;

                notification_html = '<div class="notification">' + close_button + message + '</div>';
                $('#orders-notification').append(notification_html);
                $('#orders-notification').css({'display': 'block'});

                break;


            case 'order':
                img_position = notification.message.indexOf("<img");
                message_without_img = img_position != -1 ? notification.message.substring(0, img_position) : notification.message;
                link = '/order/' + notification.model_id;
                message = `<h4>New Message on Order from ${notification.user_name}</h4><a href="${link}" style="padding-bottom: 10px; display: block;">${message_without_img.length > 40 ? (message_without_img.substring(0, 40 - 3) + '...') : message_without_img} - ${moment(notification.created_at).format('H:m')}</a>`;

                notification_html = '<div class="notification">' + close_button + message + '</div>';
                $('#orders-notification').append(notification_html);
                $('#orders-notification').css({'display': 'block'});

                break;

            case 'leads':
            img_position = notification.message.indexOf("<img");
            message_without_img = img_position != -1 ? notification.message.substring(0, img_position) : notification.message;
                link = '/leads/' + notification.model_id;
                message = `
                            <h4>New Message on Lead from ${notification.user_name}</h4>
                            <a href="${link}" style="padding-bottom: 10px; display: block;">${notification.message.length > 40 ? (notification.message.substring(0, 40 - 3) + '...') : notification.message} - ${moment(notification.created_at).format('H:m')}</a>`;

                notification_html = '<div class="notification">' + close_button + message + '</div>';
                $('#leads-notification').append(notification_html);
                $('#leads-notification').css({'display': 'block'});

                break;

            default:
                return;
        }

        // toastr.options = {
        //     // "closeButton": ((notification.type === 'button') && (is_admin == false)) || (notification.reminder == 1) ? false : true,
        //     "closeButton": ((notification.type === 'button') && (is_admin == false))? false : true,
        //     "debug": false,
        //     "newestOnTop": true,
        //     "progressBar": false,
        //     "positionClass": "toast-top-right",
        //     "preventDuplicates": false,
        //     "showDuration": "500",
        //     "hideDuration": "400",
        //     "timeOut": 0,
        //     "extendedTimeOut": 0,
        //     "showEasing": "swing",
        //     "hideEasing": "linear",
        //     "showMethod": "fadeIn",
        //     "hideMethod": "fadeOut",
        //     "tapToDismiss" : false,
        // };
        //
        // toastr.options.onCloseClick = () => markNotificationRead(notification.id);
        // toastr.options.onHidden = () => nextNotification(notification.id);
        // // toastr.options.onHidden = () => markNotificationRead(notification.id);
        // // toastr.options.onclick = () => { return false; };
        //
        // toastr['info'](message);
    }

    function markNotificationRead(id) {
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: '/pushNotificationMarkRead/' + id
        });
    }

    function nextNotification(id) {
        notificationQueue.dequeueWithId(parseInt(id));
        notificationQueue.showNotification();
    }

    function getStatusButtons(notificaiton) {

        if(notificaiton.type !== 'button')
            return '';

        return `<div class="row notification-row">
                   <div data-id="${notificaiton.id}" class="btn-group btn-group-justified">
                        <button value="1" class="n-status btn btn-notification text-success">Accept</button>
                        <button value="2" class="n-status btn btn-notification">Postpone</button>
                        <button value="3" class="n-status btn btn-notification text-danger">Decline</button>
                    </div>
                </div>`;

    }

    $(document).on('click','#notification_count',function () {
       $('.notifications-container').toggleClass('notifications-hide');
    });

    $(document).on('click','.notification-close',function (e) {
      e.stopPropagation();
      var notification_id = $(this).data('id');

      markNotificationRead(notification_id);
      nextNotification(notification_id);

      $(this).parent().fadeOut(400);
    });



</script>
