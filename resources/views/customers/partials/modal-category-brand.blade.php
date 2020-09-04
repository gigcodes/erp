<div id="categoryBrandModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send Last Scraped Images</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- id="customerSendScrap" -->
      <form action="" id="customerSendScrap"  method="GET" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="customer_id" value="">

        <div class="modal-body">
          <div class="form-group">
            <strong>Category</strong>
            {!! $category_suggestion !!}
          </div>

          <div class="form-group">
            <strong>Brand</strong>

            <select class="form-control select-multiple" name="brand[]" multiple>
              <option value="">Select a Brand</option>

              @foreach ($brands as $brand)
                <option value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <strong>Total</strong>
            <input type="number" name="total_images" class="form-control" required>
          </div>
          <div class="form-group">
            <strong>Keyord</strong>
            <input type="text" name="term" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <!-- <button type="submit" class="btn btn-secondary" id="sendScrapedButton">Send</button> -->
          <button type="submit" class="btn btn-secondary">Send</button>
        </div>
      </form>
    </div>

  </div>
</div>
