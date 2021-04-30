<table class="table table-striped table-bordered">
   <tbody>
      <tr>
         <th>ID</th>
         <th>URL</th>
         <th>Count</th>
      </tr>
         @foreach($logsGroupWise as $i => $lgw)
              <tr>
                 <td>{{ $i + 1 }}</td>
                 <td>{{ $lgw->url }}</td>
                 <td>{{ $lgw->total_request }}</td>
              </tr>
         @endforeach
   </tbody>
</table>