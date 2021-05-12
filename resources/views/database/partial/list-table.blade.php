<table class="table table-bordered page-template-{{ $page }}">
<thead>
  <tr>
    <th>Name</th>
    <th>Size</th>
    <th>Database Name</th>
    <th>Created At</th>
  </tr>
</thead>
<tbody>
<?php if (!empty($databaseHis)) {?>
    <?php foreach ($databaseHis as $pam) {?>
        <tr>
          <td>{{ $pam->database_name }}</td>
          <td>{{ number_format($pam->size / 1024,2,'.','') }} MB</td>
          <td>{{ $pam->database }}</td>
          <td>{{ $pam->created_at }}</td>
        </tr>
      <?php }?>
  <?php }?>
</tbody>
<tfoot>
  <tr>
    <td colspan="4"><?php echo $databaseHis->appends(request()->except("page"))->links(); ?></td>
  </tr>
</tfoot>
</table>