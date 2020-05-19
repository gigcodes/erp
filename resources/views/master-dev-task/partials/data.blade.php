<div class="table-responsive mt-3">
    <table class="table table-bordered" id="master-table">
        <tbody>
          <tr>
            <td>Database</td>
            <td colspan="6">
              <table style="width: 100%;">
                  <tr>
                    <th>Current Size</th>
                    <th>Size Before</th>
                  </tr>
                  <tr>
                      <td>{{ ($currentSize) ? $currentSize->size : "N/A" }}</td>
                      <td>{{ ($sizeBefore) ? $sizeBefore->size : "N/A" }}</td>
                  </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>Development</td>
            <td colspan="6">
              <table style="width: 100%;">
                  <tr>
                    <th>Repository</th>
                    <th>Open Pull Request</th>
                  </tr>
                  <?php if(!empty($repoArr)) { ?>
                    <?php foreach($repoArr as $repo) { ?>
                    <?php $totalRequest = !empty($repo['pulls']) ? count($repo['pulls']) : 0 ?>
                    <?php if($totalRequest > 0) { ?>  
                        <tr>
                            <td>{{ !empty($repo['name']) ? $repo['name'] : "N/A" }}</td>
                            <td>{{ !empty($repo['pulls']) ? count($repo['pulls']) : 0 }}</td>
                        </tr>
                    <?php } ?>    
                    <?php } ?>  
                  <?php } ?>  
              </table>
            </td>
          </tr>
          <tr>
            <td>Whatsapp</td>
            <td colspan="6">
              <table style="width: 100%;">
                  <tr>
                    <th>Last 3 hours</th>
                    <th>Last 24 hours</th>
                  </tr>
                  <tr>
                      <td>{{ $last3HrsMsg }}</td>
                      <td>{{ $last24HrsMsg }}</td>
                  </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>Scraper Reports</td>
            <td colspan="6">
              <table style="width: 100%;">
                  <tr>
                    <th>Last 24 hours</th>
                  </tr>
                  <tr>
                      <td>{{ !empty($scraperReports) ? $scraperReports->cnt : 0 }}</td>
                  </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>Cron jobs</td>
            <td colspan="6">
              <table style="width: 100%;">
                  <tr>
                    <th>Signature</th>
                    <th>Start time</th>
                    <th>Last error</th>
                  </tr>
                  <?php if(!empty($cronLastErrors)){ ?>
                      <?php foreach($cronLastErrors as $cronLastError) { ?>
                        <tr>
                          <td>{{ $cronLastError->signature }}</td>
                          <td>{{ $cronLastError->start_time }}</td>
                          <td>{{ $cronLastError->last_error }}</td>
                        </tr>
                      <?php } ?>
                  <?php } ?>
              </table>
            </td>
          </tr>
       </tbody>
    </table>
</div>