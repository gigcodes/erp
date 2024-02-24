    <form  id="create-form" action="{{ route('social.campaign.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="modal-header">
            <h4 class="modal-title">Create Campaign</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
                    <div class="form-group">
                        <label for="">Config</label>
                        <select class="form-control" name="config_id" required >
                            @foreach($configs as $key=>$val)
                            <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('config_id'))
                        <p class="text-danger">{{$errors->first('config_id')}}</p>
                        @endif
                    </div>
                 <div class="form-group">
                    <label for="">Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Type your campaign name" required>
                    @if ($errors->has('name'))
                    <p class="text-danger">{{$errors->first('name')}}</p>
                    @endif
                  </div>
                  <div class="form-group">
                        <label for="">Objective</label>
                        <select class="form-control" name="objective" required >
                            <option value="APP_INSTALLS">APP INSTALLS</option>
                            <option value="BRAND_AWARENESS">BRAND AWARENESS</option>
                            <option value="CONVERSIONS">CONVERSIONS</option>
                            <option value="EVENT_RESPONSES">EVENT RESPONSES</option>
                            <option value="LEAD_GENERATION">LEAD GENERATION</option>
                            <option value="LINK_CLICKS">LINK CLICKS</option>
                            <option value="LOCAL_AWARENESS">LOCAL AWARENESS</option>
                            <option value="MESSAGES">MESSAGES</option>
                            <option value="OFFER_CLAIMS">OFFER CLAIMS</option>
                            <option value="PAGE_LIKES">PAGE LIKES</option>
                            <option value="POST_ENGAGEMENT">POST ENGAGEMENT</option>
                            <option value="PRODUCT_CATALOG_SALES">PRODUCT CATALOG SALES</option>
                            <option value="OUTCOME_APP_PROMOTION">OUTCOME APP PROMOTION</option>
                            <option value="OUTCOME_AWARENESS">OUTCOME AWARENESS</option>
                            <option value="OUTCOME_ENGAGEMENT">OUTCOME ENGAGEMENT</option>
                            <option value="OUTCOME_LEADS">OUTCOME LEADS</option>
                            <option value="OUTCOME_SALES">OUTCOME SALES</option>
                            <option value="OUTCOME_TRAFFIC">OUTCOME TRAFFIC</option>
                            <option value="REACH">REACH</option>
                            <option value="VIDEO_VIEWS">VIDEO VIEWS</option>
                        </select>

                        @if ($errors->has('objective'))
                        <p class="text-danger">{{$errors->first('objective')}}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="">Buying Type</label>
                        <select class="form-control" name="buying_type" >
                            <option selected value="AUCTION">AUCTION</option>
                            <option value="RESERVED ">RESERVED </option>

                        </select>

                        @if ($errors->has('buying_type'))
                        <p class="text-danger">{{$errors->first('buying_type')}}</p>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="">Daily Budget</label>
                        <input type="number" class="form-control" name="daily_budget">
                        @if ($errors->has('daily_budget'))
                        <p class="text-danger">{{$errors->first('daily_budget')}}</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="inlineRadio1" value="ACTIVE">
                            <label class="form-check-label"  for="inlineRadio1">ACTIVE</label>
                        </div>
                        <div class="form-check form-check-inline">
                        <input class="form-check-input" checked type="radio" name="status" id="inlineRadio2" value="PAUSED">
                        <label class="form-check-label"  for="inlineRadio2">PAUSED</label>
                    </div>
               </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Create Campaign</button>
        </div>
    </form>
