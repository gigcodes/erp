<div id="categoryBrandModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Send Last 20 Scraped Images</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="{{ route('customer.send.scraped') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="customer_id" value="">

        <div class="modal-body">
          <div class="form-group">
            <strong>Category</strong>
            {!! $category_suggestion !!}
          </div>

          <div class="form-group">
            <strong>Brand</strong>

            <select class="form-control" name="brand[]" multiple>
              <option value="">Select a Brand</option>

              @foreach ($brands as $brand)
                <option value="{{ $brand['id'] }}">{{ $brand['name'] }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary" id="sendScrapedButton">Send</button>
        </div>
      </form>
    </div>

  </div>
</div>
