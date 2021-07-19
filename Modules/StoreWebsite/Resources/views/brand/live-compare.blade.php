<table class="table table-bordered">
    <thead>
      <tr>
        <th width="10%">Id</th>
        <th width="10%">Brand Name</th>
      </tr>
    </thead>
    <tbody>
      @forelse($availableBrands as $avb)
        <tr>
          <td>{!! $avb->id !!}</td>
          <td>{!! $avb->name !!}</td>
        </tr>
      @empty
        <tr><td colspan="2"><center>No brand found</center></td></tr>
      @endforelse
    </tbody>
</table>