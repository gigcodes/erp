<div id="ConfigCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('social.config.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Social Config</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Website:</strong>
                        <select class="form-control" name="store_website_id" required>
                            <option value="0">Select Website</option>
                            @foreach($websites as $website)
                                <option value="{{ $website->id }}">{{ $website->title }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('website'))
                            <div class="alert alert-danger">{{$errors->first('website')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <strong>Platform:</strong>
                        <select class="form-control" name="platform" required>
                            <option selected disabled>Select Platform</option>
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>

                        </select>

                    </div>
                    <div class="form-group">
                        <strong>Name:</strong>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>

                        @if ($errors->has('name'))
                            <div class="alert alert-danger">{{$errors->first('name')}}</div>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="">Choose Ads Manager Account</label>
                        <select class="form-control" name="ads_manager" id="adset_id">
                            <option value="">Select Ads Manager</option>
                            @foreach($ad_accounts as $ad_account)
                                <option value="{{ $ad_account['id'] }}">{{ $ad_account['name'] }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('adset_id'))
                            <p class="text-danger">{{$errors->first('adset_id')}}</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Page Id:</strong>
                        <input type="text" name="page_id" class="form-control" value="{{ old('page_id') }}">

                        @if ($errors->has('token'))
                            <div class="alert alert-danger">{{$errors->first('page_id')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Account Id:</strong>
                        <input type="text" name="account_id" class="form-control" value="{{ old('account_id') }}">

                        @if ($errors->has('account_id'))
                            <div class="alert alert-danger">{{$errors->first('account_id')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Page Token:</strong>
                        <input type="text" name="page_token" class="form-control" value="{{ old('page_token') }}">

                        @if ($errors->has('page_token'))
                            <div class="alert alert-danger">{{$errors->first('page_token')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Language of Page:</strong>
                        <select class="form-control" name="page_language" required>
                            <option value="0">Select language of page</option>
                            @foreach($languages as $language)
                                <option value="{{ $language->locale }}">{{ $language->name }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('languages'))
                            <div class="alert alert-danger">{{$errors->first('languages')}}</div>
                        @endif
                    </div>


                    <div class="form-group">
                        <strong>Webhook Verify Token:</strong>
                        <input type="text" name="webhook_token" class="form-control" value="{{ old('webhook_token') }}">

                        @if ($errors->has('webhook_token'))
                            <div class="alert alert-danger">{{$errors->first('webhook_token')}}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <strong>Status:</strong>
                        <select class="form-control" name="status" required>
                            <option>Select Status</option>
                            <option value="1">Active</option>
                            <option value="2">Blocked</option>
                            <option value="0">Inactive</option>
                        </select>
                        @if ($errors->has('status'))
                            <div class="alert alert-danger">{{$errors->first('status')}}</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Store</button>
                </div>
            </form>
        </div>
    </div>
</div>
