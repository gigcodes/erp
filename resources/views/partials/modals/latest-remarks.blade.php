<div id="latestRemark" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Latest Remarks</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="col-md-12">
          <div class="form-group col-md-6">
            <input type="text" id="table-full-search" class="table-full-search form-control" placeholder="Search..">
          </div>
        </div>
        <table class="table table-bordered fixed_header" id="latest-remark-records">
            <thead >
              <tr>
                <th scope="col" width="5%">#</th>
                <th scope="col" width="15%">Scraper Name</th>
                <th scope="col" width="15%">Created At</th>
                <th scope="col" width="15%">Posted By</th>
                <th scope="col" width="25%">Remark</th>
                <th scope="col" width="25%">Communication</th> <!-- Purpose : Add Column- DEVTASK-4219 -->
              </tr>
            </thead>
            <tbody class="show-list-records">

            </tbody>
          </table>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
