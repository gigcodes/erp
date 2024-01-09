@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Tables | Database')

@section('content')

<div class="row">
  <div class="col-lg-12 margin-tb">
      <h2 class="page-heading">
        Tables | Database 
        <button type="button" class="btn btn-secondary truncate-tables-btn" style=" float: right;">
            Truncate Table
        </button> 
      </h2>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="table-responsive-lg" id="page-view-result">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Table Name</th>
            <th>Table Size</th>
          </tr>
        </thead>

        <tbody>
        <?php 
        if (!empty($tables)) {
            foreach ($tables as $value) {
                $sizeResult = Illuminate\Support\Facades\DB::select(DB::raw("SHOW TABLE STATUS LIKE '$value'"));
            
                // Extract the size information
                $sizeInBytes = $sizeResult[0]->Data_length + $sizeResult[0]->Index_length;
                $sizeInKB = round($sizeInBytes / 1024, 2); // Convert bytes to KB ?>
                <tr>
                    <td>
                        <input type="checkbox" name="tables_check" class="tables_check" value="{{ $value }}" data-id="{{ $value }}">
                    </td>
                  <td>{{ $value }}</td>
                  <td>{{ $sizeInKB . " KB" }}</td>
                </tr>
              <?php }?>
          <?php }?>
        </tbody>
        </table>
    </div>
  </div>
</div>


<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
</div>
<script type="text/javascript">
    $(document).on("click",".truncate-tables-btn",function() {
        var selectedCheckboxes = [];
        var fileIDs = [];

        $('input[name="tables_check"]:checked').each(function() {
            var fileID = $(this).data('id');
            var checkboxValue = $(this).val();

            fileIDs.push(fileID);
            selectedCheckboxes.push(checkboxValue);
        });

        if (selectedCheckboxes.length === 0) {
            alert('Please select at least one checkbox.');
            return;
        }  

        var formData = {
            ids: selectedCheckboxes 
        };

        if (confirm('Are you sure you want to truncate the selected tables?')) {

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route('truncate-tables') }}',
                data: formData,
                success: function(response) {
                    toastr["success"]("Your selected batabase tables has been truncate successfully");
                    //location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                    //location.reload();
                }
            });      

        }
    });
</script>
@endsection