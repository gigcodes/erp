<!-- <div id="gtmetrix-stats-modal" class="modal fade" >
    <div class="modal-dialog modal-md model-width w-100">
      <div class="modal-content" style="width: 100%;"> -->
        <div class="modal-header">
          <h4 class="modal-title">{{$title}}</span></h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <table class="table fixed_header table-responsive" id="latest-remark-records">
            <thead class="thead-dark">
              <tr>
                <th class="w-100">Recommendation</th>
                <th class="w-100">Grade</th>
              </tr>
            </thead>
            <tbody class="show-list-records" >
              @if(!empty($data))
                @foreach ($data as $statsdata)
                  <tr><td class="w-100">{{$statsdata['name']}}</td><td class="w-100">{{$statsdata['score']}}</td></tr>
                @endforeach
              @else
                <tr><td colspan=2>No Results found !!</td></tr>
              @endif
            </tbody>
          </table>
        </div>
      <!-- </div>
    </div>
</div> -->