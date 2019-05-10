<div id="emailAccordion">
  @if (count($emails) > 0)
    @foreach ($emails as $key => $email)
      <div class="card">
        <div class="card-header" id="headingEmail{{ $key }}">
          <h5 class="mb-0">
            <p class="btn-link collapsed collapse-fix email-fetch" data-toggle="collapse" data-target="#emailAcc{{ $key }}" data-uid="{{ $email['uid'] ?? 'no' }}" data-id="{{ $email['id'] ?? 'null' }}" data-type="{{ $type }}" aria-expanded="false" aria-controls="">
              <strong>{{ $email['subject'] }}</strong>
              {{ $email['from'] }}
              <br>
              {{ $email['date'] }}
            </p>
          </h5>
        </div>
        <div id="emailAcc{{ $key }}" class="collapse collapse-element" aria-labelledby="headingInstruction" data-parent="#instructionAccordion">
          <div class="email-content">
            <div class="card p-3">

            </div>
          </div>
        </div>
      </div>
    @endforeach

    {!! $emails->appends(Request::except('page'))->links() !!}
  @else
    No emails for this customer
  @endif
</div>

{{-- <div class="row">
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
              {{ $email['from'] }}
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
    <div class="mb-3">
      <button type="button" class="btn btn-xs btn-secondary resend-email-button" data-toggle="modal" data-target="#chooseRecipientModal" data-id="" data-emailtype="" data-type="">Resend</button>

      @if (isset($to_email))
        {{ $to_email }}
      @endif
    </div>

    <div class="card p-3">

    </div>
  </div>
</div> --}}
