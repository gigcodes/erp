<div class="col-md-12">
  <table class="table">
    <thead>
      <tr>
        <th scope="col">New Location Name</th>
        <th scope="col">Courier Name</th>
        <th scope="col">Courier Details</th>
        <th scope="col">Date time</th>
        <th scope="col">Created by</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($history as $h){ ?>
        <tr>
          <td><?php echo $h->location_name; ?></td>
          <td><?php echo $h->courier_name; ?></td>
          <td><?php echo $h->courier_details; ?></td>
          <td><?php echo $h->date_time; ?></td>
          <td><?php echo $h->user->name; ?></td>
        </tr>
      <?php } ?>
      </tbody>
  </table>
</div> 