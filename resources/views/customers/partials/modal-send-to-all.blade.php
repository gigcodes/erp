<div id="sendAllModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Send Message to All Customers</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('customer.whatsapp.send.all') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="linked_images" id="linked_images" value="">

                <div class="modal-body">
                  {{-- @if ($queues_total_count > $queues_sent_count)
                    <div class="form-group alert alert-success">
                      <strong>Background Status:</strong>
                      <br>
                      {{ $queues_sent_count }} of {{ $queues_total_count }} customers are processed
                      <br>
                      <a href="{{ route('customer.whatsapp.stop.all') }}" class="btn btn-xs btn-danger">STOP</a>
                    </div>

                    <hr>
                  @endif --}}

                  <div class="form-group">
                    <strong>Schedule Date:</strong>
                    <div class='input-group date' id='schedule-datetime'>
                      <input type='text' class="form-control" name="sending_time" id="sending_time_field" value="{{ date('Y-m-d H:i') }}" required />

                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                    </div>

                    @if ($errors->has('sending_time'))
                        <div class="alert alert-danger">{{$errors->first('sending_time')}}</div>
                    @endif
                  </div>

                  <div class="form-group">
                    <strong>Frequency</strong>
                    <input type="number" name="frequency" class="form-control" value="10" required />
                  </div>

                    <div class="form-group">
                        <strong>Message</strong>
                        <textarea name="message" id="message_to_all_field" rows="8" cols="80" class="form-control"></textarea>
                    </div>

                    <div class="form-group">
                        <input type="file" name="images[]" multiple />
                    </div>

                    <div class="form-group">
                      <button type="button" class="btn btn-xs btn-secondary link-images-button">Link Images</button>
                    </div>

                    {{-- <div class="form-group">
                      <a href="#" class="btn btn-image attach-images-btn"><img src="/images/attach.png" />Attach from Grid</a>
                    </div> --}}

                    <div class="form-group">
                        <input type="checkbox" id="send_type" name="to_all" checked>
                        <label for="send_type">Send Message to All Existing Customers</label>
                    </div>

                    <hr>

                    <div class="form-group">
                      <strong>Select Group of Customers</strong>
                      <select class="form-control" name="rating">
                        <option value="">Select a Rating</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <select class="form-control" name="gender">
                        <option value>Both Genders</option>
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                      </select>
                    </div>

                    <hr>

                    <div class="form-group">
                      <strong>Sending Number</strong>
                      <select class="form-control" name="whatsapp_number">
                        <option value="">Select a Number</option>

                        @foreach ($api_keys as $api_key)
                          <option value="{{ $api_key->number }}">{{ $api_key->number }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group">
                      <strong>Upload Phone Numbers</strong>
                      <input type="file" name="file" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Send Message</button>
                </div>
            </form>
        </div>

    </div>
</div>
