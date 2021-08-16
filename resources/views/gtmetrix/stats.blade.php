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
              <?php $price = array_column($data, 'score');
          array_multisort($price, SORT_ASC, $data);
              ?>
                @foreach ($data  as $statsdata)
                  <tr>
                    <td class="w-100">{{$statsdata['name']}}</td>
                    <td class="w-100"> 
                 
                    @if($statsdata['score'] >= 89)
                    @php $color = 'bg-success' ; @endphp
                    @endif
                    @if($statsdata['score'] <= 48)
                    @php $color = 'bg-danger' ; @endphp
                    @endif
                    @if($statsdata['score'] <= 60 && $statsdata['score'] >= 48 )
                    @php $color = 'bg-warning' ; @endphp
                    @endif
                    <div class="progress">
                      <div class="progress-bar {{$color}} progress-bar-striped " role="progressbar" style="width: {{$statsdata['score']}}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{$statsdata['score']}}%</div>
                    </div>
                    </td>
                </tr>
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