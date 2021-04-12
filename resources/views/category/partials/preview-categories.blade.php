<div class="modal-content">
  <form class="update-reference-category-form" action="/category/new-references/save-category" method="post">
     {!! csrf_field() !!}
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
                            <td>
                              {!! \App\Category::attr(["name" => "updated_category[".$link["from"]."]", "class" => "form-control select2"])->selected(!empty($link["to"]) ? [$link["to"]] : [1])->renderAsDropdown() !!}
                            </td>
                          </tr>
                        @endforeach
                    </tbody>
                </table>
          </div>
      </div>
      <div class="modal-footer">
          <button type="submit" class="btn btn-default">Update</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
   </form>
</div>