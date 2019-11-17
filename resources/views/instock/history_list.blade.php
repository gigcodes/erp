<div class="col-md-12">
  <div class="form-group mr-3">
      <select data-placeholder="Select location" data-product-id="{{ $product->id }}" class="form-control location-change-product" name="location">
        <optgroup label="Locations">
          @foreach ($locations as $name)
            <option value="{{ $name }}" {{ isset($product->location) && $product->location == $name ? 'selected' : '' }}>{{ $name }}</option>
          @endforeach
        </optgroup>
      </select>
    </div>
</div> 
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