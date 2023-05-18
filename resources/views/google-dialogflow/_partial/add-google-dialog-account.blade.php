<form id="add-group-form" method="POST" action="{{route('google-chatbot-accounts.add')}}">
    {{csrf_field()}}
    <div class="modal-body">
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Client Id</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="google_client_id" name="google_client_id"
                       placeholder="Client Id" value="{{ old('google_client_id') }}">
                @if ($errors->has('google_client_id'))
                    <span class="text-danger">{{$errors->first('google_client_id')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Client Secret</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="google_client_secret" name="google_client_secret"
                       placeholder="Client Secret" value="{{ old('google_client_secret') }}">
                @if ($errors->has('google_client_secret'))
                    <span class="text-danger">{{$errors->first('google_client_secret')}}</span>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="headline1" class="col-sm-2 col-form-label">Site</label>
            <div class="col-sm-10">
                <select name="site_id" id="" class="form-control">
                    <option value="">Select</option>
                    @foreach($store_websites as $store_website)
                        <option value="{{$store_website->id}}">{{$store_website->title}}</option>
                    @endforeach
                </select>
                @if ($errors->has('site_id'))
                    <span class="text-danger">{{$errors->first('site_id')}}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="float-right ml-2 custom-button btn" data-dismiss="modal"
                aria-label="Close">Close
        </button>
        <button type="submit" class="float-right custom-button btn">Create</button>
    </div>
</form>
