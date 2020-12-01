<div class="modal fade" id="largeImageModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content" style="width: 200% !important; right: 220px !important">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
           
                <div class="modal-body" id="images">
                    
                </div>
                <input type="hidden" id="account_id">
                <div class="modal-footer">
                	<button type="button" class="btn btn-default" onclick="getCaptions()" id="next_button">Next</button>
                	<button type="button" class="btn btn-default" onclick="submitPost()" style="display: none;" id="submit_button">Submit Post</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
        </div>
    </div>
</div>