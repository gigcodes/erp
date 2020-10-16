<div id="add-vendor-info-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div id="myDiv">
			   	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
			</div>

            <div class="modal-header">
                <h2>Publish Post</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
				<form role="form" method="post" action="{{ route('post.store') }}" name="post-create" autocomplete="off" style="margin-top: 30px;">
					@csrf

					<div class="row">
						<div class="col-md-12">
							<small style="color: red;">Please Maintain aspect ratios between 0.800 and 1.910 for Photos</small>
							<div class="card">
								<div class="card-header pl-3">
									<h3 class="card-title">@lang('New post')</h3>
									<div class="card-options">
										<select name="account" class="form-control form-control-sm target-account">
											@foreach($accounts as $account)
											<option value="{{ $account->id }}">{{ $account->last_name }}</option>
											@endforeach
										</select>
									</div>
								</div>
								<div class="p-3">

									<div class="form-group">
										<div class="selectgroup w-100">
											<label class="selectgroup-item">
												<input type="radio" name="type" value="post" class="selectgroup-input post-type" checked="">
												<span class="selectgroup-button">@lang('Post')</span>
											</label>
											<label class="selectgroup-item">
												<input type="radio" name="type" value="album" class="selectgroup-input post-type">
												<span class="selectgroup-button">@lang('Album')</span>
											</label>
											<label class="selectgroup-item">
												<input type="radio" name="type" value="story" class="selectgroup-input post-type">
												<span class="selectgroup-button">@lang('Story')</span>
											</label>
										</div>
									</div>
									<br class="hidden-sm hidden-xs">
									<br class="hidden-sm hidden-xs">
									<br class="hidden-sm hidden-xs">
									<br class="hidden-sm hidden-xs">
									<br class="hidden-sm hidden-xs">
									<div class="form-group">
										<label class="form-label">@lang('Location')</label>
										<select name="location" class="form-control location-lookup"></select>
									</div>

									<div class="form-group">
										<label class="form-label">@lang('Caption')</label>
										<textarea rows="3" name="caption" id="caption_ig" class="form-control caption-text" placeholder="@lang('Compose a post caption')" data-emojiable="true"></textarea>
										<div id="auto"></div>
									</div>

									<p></p>

									<div class="form-group">
										<label class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input is-scheduled" name="scheduled" value="1">
											<span class="custom-control-label">@lang('Schedule')</span>
										</label>
									</div>

									<div class="form-group">
										<div class="input-icon">
											<span class="input-icon-addon"><i class="fe fe-calendar"></i></span>
											<input type="text" name="scheduled_at" class="form-control dm-date-time-picker scheduled-at" placeholder="@lang('Schedule at')" disabled="">
										</div>
									</div>

								</div>
								<div class="card-footer p-3">
									<button type="submit" class="btn btn-primary btn-block btn-schedule d-none">
										<i class="fe fe-clock"></i> @lang('Schedule post')
									</button>
									<button type="submit" class="btn btn-success btn-block btn-publish mt-0">
										<i class="fe fe-check"></i> @lang('Publish now')
									</button>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
        </div>
	</div>
</div>
   