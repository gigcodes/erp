<div class="modal-content">
   <div class="modal-header">
      <h5 class="modal-title">List Of updated categories</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
   </div>
   <div class="modal-body">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                  <tr>
                    <th width="2%">Category</th>
                    <th width="2%">Update to</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($links as $link)
                      <tr>
                        <td>{{ $link['from'] }}</td>
                        <td>{{ $link['to'] }}</td>
                      </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>