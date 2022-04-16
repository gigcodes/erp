<div id="download_site_check_list_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <form action='{{ route('site-check-list.download') }}' method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-md-6">
                        <label class="">Websites </label>
                        {{ Form::select('website_id', $all_store_websites, null, ['class' => 'form-control width-auto globalSelect2','placeholder' => '-- Select --']) }}
                    </div>
                    <div class="col-md-6">
                        <label class="">Status </label>
                        {{ Form::select('status[]', $allStatus, null, ['class' => 'form-control width-auto globalSelect2','multiple' => 'multiple']) }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-default download-document-site-asset-btn">Download</button>
                </div>
            </div>
        </form>
    </div>
</div>
