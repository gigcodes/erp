<div class="row">
  <div class="col-md-4">
    <div class="card">
      @if (count($emails) > 0)
        <ul class="list-group list-group-flush">
          @foreach ($emails as $email)
            <li class="list-group-item">
              <a href="#" class="email-fetch" data-uid="{{ $email['uid'] ?? 'no' }}" data-id="{{ $email['id'] ?? 'null' }}" data-type="{{ $type }}">
                <strong>{{ $email['subject'] }}</strong>
              </a>
              <br>
              {{ $email['date'] }}
            </li>
          @endforeach
        </ul>
      @else
        No emails for this user
      @endif
    </div>

    @if (count($emails) > 0)
      {!! $emails->appends(Request::except('page'))->links() !!}
    @endif
  </div>

  <div class="col-md-8" id="email-content">
    <div class="card p-3">

    </div>
  </div>
</div>
