<div id="scrapEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h4 class="modal-title">Edit Generic Scraper</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>Run Gap:</strong>
            <input type="integer" name="run_gap" id="run_gap" class="form-control">
          </div>
          
          <div class="form-group">
            <strong>Time Out:</strong>
            <input type="text" name="time_out" id="time_out" class="form-control">
            @if ($errors->has('name'))
              <div class="alert alert-danger">{{$errors->first('name')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Starting URL:</strong>
            <textarea type="text" name="starting_url" id="starting_url" class="form-control"></textarea>
          </div>
          <div class="form-group">
            <strong>Designer URL Selector:</strong>
            <input type="text" name="designer_url" id="designer_url" class="form-control">
          </div>
          <div class="form-group">
            <strong>Product URL Selector:</strong>
            <input type="text" name="designer_url" id="product_url_selector" class="form-control">
          </div>
          <input type="hidden" id="scraper_id">
          </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-secondary" onclick="updateSupplier()">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

