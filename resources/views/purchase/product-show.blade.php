@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

<div class="row">
  <div class="col-lg-12 margin-tb">
    <div class="pull-left">
      <h2>Purchase Product</h2>
    </div>
    <div class="pull-right">
      <a class="btn btn-secondary" href="{{ route('purchase.index') }}">Back</a>
    </div>
  </div>
</div>


@if ($message = Session::get('success'))
<div class="alert alert-success">
  <p>{{ $message }}</p>
</div>
@endif



<div class="row">
  <div class="col-md-6 col-12">
    <div class="form-group">
      <strong>ID:</strong> {{ $product->id }}
    </div>

    <div class="form-group">
      <strong>Name:</strong> {{ $product->name }}
    </div>

    <div class="form-group">
      <strong>Brand:</strong> {{ \App\Http\Controllers\BrandController::getBrandName($product->brand) }}
    </div>

    <div class="form-group">
      <strong>Color:</strong> {{ $product->color }}
    </div>

    <div class="form-group">
      <strong>Price (in Euro):</strong> {{ $product->price }}
    </div>

    <div class="form-group">
      <strong>Purchase price:</strong> <span id="purchase-price">{{ isset($product->percentage) || isset($product->factor) ? ($product->price - ($product->price * $product->percentage / 100) - $product->factor) : ($product->price) }}</span>
    </div>

    <div class="form-group">
      <strong>Percentage %:</strong>
      <input type="number" name="percentage" class="form-control" placeholder="10%" value="{{ $product->percentage }}" min="0" max="100">
    </div>

    <div class="form-group">
      <strong>Amount:</strong>
      <input type="number" name="factor" class="form-control" placeholder="1.22" value="{{ $product->factor }}" min="0" step="0.01">
      <a href="#" class="btn-link save-purchase-price">Save</a>
    </div>

    <div class="form-group">
      <strong>Order price:</strong> {{ $product->price_special }}
    </div>

    <div class="form-group">
      <strong>Supplier Link:</strong> {{ $product->supplier_link }}
    </div>

    <div class="form-group">
      <strong>Size Details:</strong>
      @if (count($order_details) > 0)
        <ul>
          @foreach ($order_details as $value)
            <li>{{ $value->size }}</li>
          @endforeach
        </ul>
      @endif
    </div>

    <div class="form-group">
      <strong>Order Details:</strong>
      @if (count($order_details) > 0)
        <ul>
          @foreach ($order_details as $value)
            <li><a href="{{ route('order.show', $value->order_id) }}">{{ $value->order_id }}</a></li>
          @endforeach
        </ul>
      @endif
    </div>

    {{-- <div class="form-group">
      <strong>Status:</strong>
      <Select name="status" class="form-control" id="change_status">
           @foreach($purchase_status as $key => $value)
            <option value="{{$value}}" {{$value == $order->status ? 'Selected=Selected':''}}>{{$key}}</option>
            @endforeach
      </Select>
      <span id="change_status_message" class="text-success" style="display: none;">Successfully changed status</span>
    </div> --}}

  </div>
  <div class="col-md-6 col-12">
    <div class="row">
      @foreach ($product->getMedia(config('constants.media_tags')) as $image)
        <div class="col-md-4">
          <img src="{{ $image->getUrl() }}" class="img-responsive" alt="">
        </div>
      @endforeach
    </div>
  </div>
</div>

<div class="row mt-5">
  <div class="col-xs-12">
    <div class="row">
      <div class="col-xs-12 col-sm-6">
        <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
          @csrf

          <div class="form-group">
            <div class="upload-btn-wrapper btn-group">
              <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
              <input type="file" name="image" />
              <button type="submit" class="btn btn-image px-1"><img src="/images/filled-sent.png" /></button>
            </div>
          </div>

          <div class="form-group flex-fill">
            <textarea class="form-control" name="body" placeholder="Received from Customer"></textarea>

            <input type="hidden" name="moduletype" value="product" />
            <input type="hidden" name="moduleid" value="{{ $product->id }}" />
            <input type="hidden" name="assigned_user" value="" />
            <input type="hidden" name="status" value="0" />
          </div>

        </form>
      </div>

      <div class="col-xs-12 col-sm-6">
        <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
          @csrf

          <div class="form-group">
            <div class="upload-btn-wrapper btn-group">
              <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
              <input type="file" name="image" />
              <button type="submit" class="btn btn-image px-1"><img src="/images/filled-sent.png" /></button>
            </div>
          </div>

          <div class="form-group flex-fill">
            <textarea class="form-control mb-3" name="body" placeholder="Send for Approval" id="message-body"></textarea>

            <input type="hidden" name="moduletype" value="product" />
            <input type="hidden" name="moduleid" value="{{ $product->id }}" />
            <input type="hidden" name="status" value="1" />
            <input type="hidden" name="assigned_user" value="" />

            <p class="pb-4" style="display: block;">
              <select name="quickComment" id="quickComment" class="form-control">
                <option value="">Quick Reply</option>
                @foreach($approval_replies as $reply )
                <option value="{{$reply->reply}}">{{$reply->reply}}</option>
                @endforeach
              </select>
            </p>
          </div>

        </form>
      </div>
      <div class="col-xs-12 col-sm-6">
        <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data" class="d-flex">
          @csrf

          <div class="form-group">
            <div class="upload-btn-wrapper btn-group">
              <button class="btn btn-image px-1"><img src="/images/upload.png" /></button>
              <input type="file" name="image" />
              <button type="submit" class="btn btn-image px-1"><img src="/images/filled-sent.png" /></button>
            </div>
          </div>

          <div class="form-group flex-fill">
            <textarea class="form-control mb-3" name="body" placeholder="Internal Communications" id="internal-message-body"></textarea>

            <input type="hidden" name="moduletype" value="product" />
            <input type="hidden" name="moduleid" value="{{ $product->id }}" />
            <input type="hidden" name="status" value="4" />

            <strong>Assign to</strong>
            <select name="assigned_user" class="form-control mb-3" required>
              <option value="">Select User</option>
              @foreach($users as $user)
              <option value="{{$user['id']}}">{{$user['name']}}</option>
              @endforeach
            </select>

            <p class="pb-4" style="display: block;">
              <select name="quickCommentInternal" id="quickCommentInternal" class="form-control">
                <option value="">Quick Reply</option>
                @foreach($internal_replies as $reply )
                <option value="{{$reply->reply}}">{{$reply->reply}}</option>
                @endforeach
              </select>
            </p>
          </div>

        </form>
      </div>

    </div>
  </div>

</div>

<div class="row">
  <div class="col-xs-12" id="message-container">
    <h3>Messages</h3>

    @foreach($messages as $message)
    @if($message['status'] == '0' || $message['status'] == '5' || $message['status'] == '6')
    <div class="talk-bubble round grey">
      <div class="talktext">
        @if (strpos($message['body'], 'message-img') !== false)
        @if (strpos($message['body'], '<br>') !== false)
        @php $exploded = explode('<br>', $message['body'])
        @endphp

        <p class="collapsible-message" data-messageshort="{{ strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] }}" data-message="{{ $message['body'] }}" data-expanded="false" data-messageid="{{ $message['id'] }}">
          {!! strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] !!}
        </p>
        @else
        @php
        preg_match_all('/<img src="(.*?)" class="message-img" \ />/', $message['body'], $match);
        $images = '<br>';
        foreach ($match[0] as $key => $image) {
        $images .= str_replace('message-img', 'message-img thumbnail-200', $image);
        }
        @endphp

        <p class="collapsible-message" data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images }}"
          data-message="{{ $message['body'] }}" data-expanded="false" data-messageid="{{ $message['id'] }}">
          {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img')))> 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '
            <img')) . $images !!} </p> @endif
            @else
            <p class="collapsible-message" data-messageshort="{{ strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] }}" data-message="{{ $message['body'] }}" data-expanded="false">
              {!! strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] !!}
            </p>
            @endif

            <em>Customer {{ Carbon\Carbon::parse($message['created_at'])->format('d-m H:i') }} </em>

            @if ($message['status'] == '0')
            <a href data-url="/message/updatestatus?status=5&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=purchase" style="font-size: 9px" class="change_message_status">Mark as Read </a>
            @endif
            @if ($message['status'] == '0') |
            @endif
            @if ($message['status'] == '0' || $message['status'] == '5')
            <a href data-url="/message/updatestatus?status=6&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=purchase" style="font-size: 9px" class="change_message_status">Mark as Replied </a>
            @endif
      </div>
    </div>

    @elseif($message['status'] == '4')
    <div class="talk-bubble round dashed-border" data-messageid="{{$message['id']}}">
      <div class="talktext">
        @if (strpos($message['body'], 'message-img') !== false)
        @if (strpos($message['body'], '<br>') !== false)
        @php $exploded = explode('<br>', $message['body'])
        @endphp

        <p class="collapsible-message" data-messageshort="{{ strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] }}" data-message="{{ $message['body'] }}" data-expanded="false" data-messageid="{{ $message['id'] }}">
          {!! strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] !!}
        </p>
        @else
        @php
        preg_match_all('/<img src="(.*?)" class="message-img" \ />/', $message['body'], $match);
        $images = '<br>';
        foreach ($match[0] as $key => $image) {
        $images .= str_replace('message-img', 'message-img thumbnail-200', $image);
        }
        @endphp

        <p class="collapsible-message" data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images }}"
          data-message="{{ $message['body'] }}" data-expanded="false" data-messageid="{{ $message['id'] }}">
          {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img')))> 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '
            <img')) . $images !!} </p> @endif
            @else
            <p class="collapsible-message" data-messageshort="{{ strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] }}" data-message="{{ $message['body'] }}" data-expanded="false">
              {!! strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] !!}
            </p>
            @endif

            <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ ($message['assigned_to'] != 0 && $message['userid'] != $message['assigned_to']) ? ' - ' . App\Helpers::getUserNameById($message['assigned_to']) : '' }}
              {{ Carbon\Carbon::parse($message['created_at'])->format('d-m H:i') }} <img id="status_img_{{$message['id']}}" src="/images/1.png"> &nbsp;</em>
      </div>
    </div>
    @else
    <div class="talk-bubble round" data-messageid="{{$message['id']}}">
      <div class="talktext">
        <span id="message_body_{{$message['id']}}">
          @if (strpos($message['body'], 'message-img') !== false)
          @if (strpos($message['body'], '<br>') !== false)
          @php $exploded = explode('<br>', $message['body'])
          @endphp

          <p class="collapsible-message" data-messageshort="{{ strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] }}" data-message="{{ $message['body'] }}" data-expanded="false" data-messageid="{{ $message['id'] }}">
            {!! strlen($exploded[0]) > 150 ? (substr($exploded[0], 0, 147) . '...<br>' . $exploded[1]) : $message['body'] !!}
          </p>
          @else
          @php
          preg_match_all('/<img src="(.*?)" class="message-img" \ />/', $message['body'], $match);
          $images = '<br>';
          foreach ($match[0] as $key => $image) {
          $images .= str_replace('message-img', 'message-img thumbnail-200', $image);
          }
          @endphp

          <p class="collapsible-message" data-messageshort="{{ strlen(substr($message['body'], 0, strpos($message['body'], '<img'))) > 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '<img')) . $images }}"
            data-message="{{ $message['body'] }}" data-expanded="false" data-messageid="{{ $message['id'] }}">
            {!! strlen(substr($message['body'], 0, strpos($message['body'], '<img')))> 150 ? (substr($message['body'], 0, 147) . '...' . $images) : substr($message['body'], 0, strpos($message['body'], '
              <img')) . $images !!} </p> @endif
              @else
              <p class="collapsible-message" data-messageshort="{{ strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] }}" data-message="{{ $message['body'] }}" data-expanded="false">
                {!! strlen($message['body']) > 110 ? (substr($message['body'], 0, 107) . '...') : $message['body'] !!}
              </p>
              @endif
        </span>
        <textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea{{$message['id']}}" style="display: none;">{!! $message['body'] !!}</textarea>

        <em>{{ App\Helpers::getUserNameById($message['userid']) }} {{ Carbon\Carbon::parse($message['created_at'])->format('d-m H:i') }} <img src="/images/{{$message['status']}}.png"> &nbsp;
          @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
          <a href data-url="/message/updatestatus?status=3&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=purchase" style="font-size: 9px" class="change_message_status">Mark as sent </a>
          @endif

          @if($message['status'] == '1' and App\Helpers::getadminorsupervisor() == true)
          <a href data-url="/message/updatestatus?status=2&id={{$message['id']}}&moduleid={{$message['moduleid']}}&moduletype=purchase" style="font-size: 9px" class="change_message_status wa_send_message" data-messageid="{{ $message['id'] }}">Approve</a>

          <a href="#" style="font-size: 9px" class="edit-message" data-messageid="{{$message['id']}}">Edit</a>
          @endif

          @if($message['status'] == '2' and App\Helpers::getadminorsupervisor() == false)
          @if (strpos($message['body'], 'message-img') !== false)
          <button class="copy-button btn btn-secondary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="purchase" data-message="{{ substr($message['body'], 0, strpos($message['body'], '<img')) }}"> Copy message </button>
          @else
          <button class="copy-button btn btn-secondary" data-id="{{$message['id']}}" moduleid="{{$message['moduleid']}}" moduletype="purchase" data-message="{{ $message['body'] }}"> Copy message </button>
          @endif
          @endif

        </em>
      </div>
    </div>

    @endif
    @endforeach
    @if(!empty($message['id']))
    <div class="show_more_main" id="show_more_main{{$message['id']}}">
      <span id="{{$message['id']}}" class="show_more" title="Load more posts" data-moduleid={{$message['moduleid']}} data-moduletype="purchase">Show more</span>
      <span class="loding" style="display: none;"><span class="loding_txt">Loading...</span></span>
    </div>
    @endif

  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script type="text/javascript">
  $('#completion-datetime').datetimepicker({
    format: 'YYYY-MM-DD HH:mm'
  });

  $('.edit-message').on('click', function(e) {
    e.preventDefault();
    var message_id = $(this).data('messageid');

    $('#message_body_' + message_id).css({
      'display': 'none'
    });
    $('#edit-message-textarea' + message_id).css({
      'display': 'block'
    });

    $('#edit-message-textarea' + message_id).keypress(function(e) {
      var key = e.which;

      if (key == 13) {
        e.preventDefault();
        var token = "{{ csrf_token() }}";
        var url = "{{ url('message') }}/" + message_id;
        var message = $('#edit-message-textarea' + message_id).val();

        $.ajax({
          type: 'POST',
          url: url,
          data: {
            _token: token,
            body: message
          },
          success: function(data) {
            $('#edit-message-textarea' + message_id).css({
              'display': 'none'
            });
            $('#message_body_' + message_id).text(message);
            $('#message_body_' + message_id).css({
              'display': 'block'
            });
          }
        });
      }
    });
  });

  $(document).on('click', ".collapsible-message", function() {
    var short_message = $(this).data('messageshort');
    var message = $(this).data('message');
    var status = $(this).data('expanded');

    if (status == false) {
      $(this).addClass('expanded');
      $(this).html(message);
      $(this).data('expanded', true);
      $(this).siblings('.thumbnail-wrapper').remove();
      $(this).parent().find('.message-img').removeClass('thumbnail-200');
      $(this).parent().find('.message-img').parent().css('width', 'auto');
    } else {
      $(this).removeClass('expanded');
      $(this).html(short_message);
      $(this).data('expanded', false);
      $(this).parent().find('.message-img').addClass('thumbnail-200');
      $(this).parent().find('.message-img').parent().css('width', '200px');
    }
  });

  $('#change_status').on('change', function() {
    var token = "{{ csrf_token() }}";
    var status = $(this).val();
    var id = {{ $product->id }};

    $.ajax({
      url: '/product/' + id + '/changestatus',
      type: 'POST',
      data: {
        _token: token,
        status: status
      }
    }).done(function(response) {
      $('#change_status_message').fadeIn(400);
      setTimeout(function() {
        $('#change_status_message').fadeOut(400);
      }, 2000);
    }).fail(function(errObj) {
      alert("Could not change status");
    });
  });

  $(document).on('click', '.change_message_status', function(e) {
    e.preventDefault();
    var url = $(this).data('url');
    var thiss = $(this);

    $.ajax({
      url: url,
      type: 'GET',
      beforeSend: function() {
        $(thiss).text('Loading');
      }
    }).done(function(response) {
      $(thiss).remove();
    }).fail(function(errObj) {
      alert("Could not change status");
    });
  });

  $(document).on('click', '.thumbnail-delete', function() {
    var thiss = $(this);
    var image = $(this).data('image');
    var message_id = $(this).closest('.talk-bubble').find('.collapsible-message').data('messageid');
    var message = $(this).closest('.talk-bubble').find('.collapsible-message').data('message');
    var token = "{{ csrf_token() }}";
    var url = "{{ url('message') }}/" + message_id;

    var image_container = '<div class="thumbnail-wrapper"><img src="' + image + '" class="message-img thumbnail-200" /><span class="thumbnail-delete" data-image="' + image + '">x</span></div>';
    var new_message = message.replace(image_container, '');

    if (new_message.indexOf('message-img') != -1) {
      var short_new_message = new_message.substr(0, new_message.indexOf('<div class="thumbnail-wrapper">')).length > 150 ? (new_message.substr(0, 147)) : new_message;
    } else {
      var short_new_message = new_message.length > 150 ? new_message.substr(0, 147) + '...' : new_message;
    }

    $.ajax({
      type: 'POST',
      url: url,
      data: {
        _token: token,
        body: new_message
      },
      success: function(data) {
        $(thiss).parent().remove();
        $('#message_body_' + message_id).children('.collapsible-message').data('messageshort', short_new_message);
        $('#message_body_' + message_id).children('.collapsible-message').data('message', new_message);
      }
    });
  });

  $('input[name="percentage"], input[name="factor"]').on('keyup', function() {
    if ($('input[name="percentage"]').val() < 0) {
      $('input[name="percentage"]').val(0);
    } else if ($('input[name="percentage"]').val() > 100) {
      $('input[name="percentage"]').val(100);
    }
    var price = {{ $product->price }};
    var percentage = $('input[name="percentage"]').val();
    var factor = $('input[name="factor"]').val();

    $('#purchase-price').text(price - (price * percentage / 100) - factor);
  });

  $('.save-purchase-price').on('click', function(e) {
    e.preventDefault();

    var thiss = $(this);
    var url = "{{ route('purchase.product.percentage', $product->id) }}";
    var token = "{{ csrf_token() }}";
    var percentage = $('input[name="percentage"]').val();
    var factor = $('input[name="factor"]').val();

    $.ajax({
      type: 'POST',
      url: url,
      data: {
        _token: token,
        percentage: percentage,
        factor: factor
      },
      beforeSend: function() {
        $(thiss).text('Saving');
      },
      success: function() {
        $(thiss).text('Save');
      }
    });
  });
</script>

@endsection
