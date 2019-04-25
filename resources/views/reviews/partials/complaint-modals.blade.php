<div id="complaintCreateModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('complaint.store') }}" method="POST">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Create a Complaint</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Customer:</strong>
            <select class="selectpicker form-control" data-live-search="true" data-size="15" name="customer_id" title="Choose a Customer" required>
              @foreach ($customers as $customer)
                <option data-tokens="{{ $customer['name'] }} {{ $customer['email'] }}  {{ $customer['phone'] }} {{ $customer['instahandler'] }}" value="{{ $customer['id'] }}">{{ $customer['id'] }} - {{ $customer['name'] }} - {{ $customer['phone'] }}</option>
              @endforeach
            </select>

            @if ($errors->has('customer_id'))
                <div class="alert alert-danger">{{$errors->first('customer_id')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Date:</strong>
            <div class='input-group date' id='complaint_date'>
              <input type='text' class="form-control" name="date" value="{{ date('Y-m-d') }}" required />

              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>

            @if ($errors->has('date'))
              <div class="alert alert-danger">{{$errors->first('date')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Platform:</strong>
            <select class="form-control" name="platform">
              <option value="">Select a Platform</option>
              <option value="instagram" {{ 'instagram' == old('platform') ? 'selected' : '' }}>Instagram</option>
              <option value="facebook" {{ 'facebook' == old('platform') ? 'selected' : '' }}>Facebook</option>
              <option value="sitejabber" {{ 'sitejabber' == old('platform') ? 'selected' : '' }}>Sitejabber</option>
              <option value="google" {{ 'google' == old('platform') ? 'selected' : '' }}>Google</option>
              <option value="trustpilot" {{ 'trustpilot' == old('platform') ? 'selected' : '' }}>Trustpilot</option>
            </select>

            @if ($errors->has('platform'))
              <div class="alert alert-danger">{{$errors->first('platform')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Complaint</strong>

            <textarea name="complaint" class="form-control" rows="8" cols="80" required>{{ old('complaint') }}</textarea>

            @if ($errors->has('complaint'))
              <div class="alert alert-danger">{{$errors->first('complaint')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Link</strong>

            <input type="text" name="link" class="form-control" value="{{ old('link') }}">

            @if ($errors->has('link'))
              <div class="alert alert-danger">{{$errors->first('link')}}</div>
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Create</button>
        </div>
      </form>
    </div>

  </div>
</div>

<div id="complaintEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h4 class="modal-title">Update a Complaint</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Customer:</strong>
            <select class="selectpicker form-control" data-live-search="true" data-size="15" id="complaint_customer_id" name="customer_id" title="Choose a Customer" required>
              @foreach ($customers as $customer)
                <option data-tokens="{{ $customer['name'] }} {{ $customer['email'] }}  {{ $customer['phone'] }} {{ $customer['instahandler'] }}" value="{{ $customer['id'] }}">{{ $customer['id'] }} - {{ $customer['name'] }} - {{ $customer['phone'] }}</option>
              @endforeach
            </select>

            @if ($errors->has('customer_id'))
                <div class="alert alert-danger">{{$errors->first('customer_id')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Date:</strong>
            <div class='input-group date' id='edit_complaint_date'>
              <input type='text' class="form-control" name="date" value="{{ date('Y-m-d') }}" required />

              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>

            @if ($errors->has('date'))
              <div class="alert alert-danger">{{$errors->first('date')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Platform:</strong>
            <select class="form-control" name="platform" id="complaint_platform">
              <option value="">Select a Platform</option>
              <option value="instagram" {{ 'instagram' == old('platform') ? 'selected' : '' }}>Instagram</option>
              <option value="facebook" {{ 'facebook' == old('platform') ? 'selected' : '' }}>Facebook</option>
              <option value="sitejabber" {{ 'sitejabber' == old('platform') ? 'selected' : '' }}>Sitejabber</option>
              <option value="google" {{ 'google' == old('platform') ? 'selected' : '' }}>Google</option>
              <option value="trustpilot" {{ 'trustpilot' == old('platform') ? 'selected' : '' }}>Trustpilot</option>
            </select>

            @if ($errors->has('platform'))
              <div class="alert alert-danger">{{$errors->first('platform')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Complaint</strong>

            <textarea name="complaint" class="form-control" rows="8" cols="80" id="complaint_complaint" required>{{ old('complaint') }}</textarea>

            @if ($errors->has('complaint'))
              <div class="alert alert-danger">{{$errors->first('complaint')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Link</strong>

            <input type="text" name="link" class="form-control" id="complaint_link" value="{{ old('link') }}">

            @if ($errors->has('link'))
              <div class="alert alert-danger">{{$errors->first('link')}}</div>
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Update</button>
        </div>
      </form>
    </div>

  </div>
</div>
