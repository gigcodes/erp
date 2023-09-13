<div id="columnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('magento_module.column.update') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <?php
                                $magentoModule = new App\MagentoModule();
                                $columns = $magentoModule->getColumns();

                                $visibilityModel = App\models\ColumnVisbility::select('columns')->where('user_id', auth()->user()->id)->first();
                                 $hideColumns = $visibilityModel->columns ?? "";
                                 $dynamicColumnsToShow = json_decode($hideColumns, true);
                                if ($dynamicColumnsToShow !== null) {
                                    $dynamicColumnsToShow = array_map('intval', $dynamicColumnsToShow);
                                } else {
                                    $dynamicColumnsToShow = []; // Set to an empty array or handle as needed
                                }  
                                
                                foreach ($columns as $key => $col) { ?>
                               <tr>
                                <td>&nbsp;&nbsp;&nbsp;{{$col}}</td>
                                <td>
                                    <input type="checkbox" value="{{$key}}" id="{{$key}}" name="column"
                                        @if (in_array($key, $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <?php } ?>
                        </div>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit-status-color">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('form').on('submit', function(event) {
            event.preventDefault();
            
            var selectedColumns = [];
    
            $('input[name="column"]:checked').each(function() {
                selectedColumns.push($(this).val());
            });
                var formData = {
                columns: selectedColumns 
            };
    
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route('magento_module.column.update') }}',
                data: formData,
                success: function(response) {
                    toastr["success"]("column Hide Update successfully");
                    location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                    location.reload();
                }
            });
        });
    });
    </script>
    

