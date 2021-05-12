<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-12 form-inline">
                    <textarea rows="1" style="width: 90%" class="form-control send-message-textbox" name="message" data-customerid="{{ $customer->id }}" placeholder="Message"></textarea>
                    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message-open" data-customerid="{{ $customer->id }}"><img src="/images/filled-sent.png"/></button>
                    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-with-audio-message" data-customerid="{{ $customer->id }}">
                        <img src="/images/customer-call-recording.png"/>
                    </button>
                </div>
			</div>
		</div>
	</div>
	<div class="col-md-12 expand-row dis-none">
	</div>
</div>