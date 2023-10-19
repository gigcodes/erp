<div id="datatablecolumnvisibilityList1" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('store-website.brand.column.update') }}" method="POST">
                <?php echo csrf_field(); ?>
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>ID</td>
                                <td>
                                    <input type="checkbox" value="ID" id="ID" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Id', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Folder Name</td>
                                <td>
                                    <input type="checkbox" value="Folder Name" id="Folder Name" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Brand', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>PostMan Status</td>
                                <td>
                                    <input type="checkbox" value="PostMan Status" id="PostMan Status" name="column" @if (!empty($dynamicColumnsToShow) && in_array('PostMan Status', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>API Issue Fix Done</td>
                                <td>
                                    <input type="checkbox" value="API Issue Fix Done" id="API Issue Fix Done" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Max Price', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Controller Name</td>
                                <td>
                                    <input type="checkbox" value="Controller Name" id="Controller Name" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Id', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Method Name</td>
                                <td>
                                    <input type="checkbox" value="Method Name" id="Method Name" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Method Name', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Request Name</td>
                                <td>
                                    <input type="checkbox" value="Request Name" id="Request Name" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Request Name', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Type</td>
                                <td>
                                    <input type="checkbox" value="Type" id="Type" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Type', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>URL</td>
                                <td>
                                    <input type="checkbox" value="URL" id="URL" name="column" @if (!empty($dynamicColumnsToShow) && in_array('URL', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Request Parameter</td>
                                <td>
                                    <input type="checkbox" value="Request Parameter" id="Request Parameter" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Request Parameter', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Params</td>
                                <td>
                                    <input type="checkbox" value="Params" id="Params" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Params', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Headers</td>
                                <td>
                                    <input type="checkbox" value="Headers" id="Headers" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Headers', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Request type</td>
                                <td>
                                    <input type="checkbox" value="Request type" id="Request type" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Request type', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Request Response</td>
                                <td>
                                    <input type="checkbox" value="Request Response" id="Request Response" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Request Response', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Response Code</td>
                                <td>
                                    <input type="checkbox" value="Response Code" id="Response Code" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Response Code', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Grumphp Errors</td>
                                <td>
                                    <input type="checkbox" value="Grumphp Errors" id="Grumphp Errors" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Grumphp Errors', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Magento API Standards</td>
                                <td>
                                    <input type="checkbox" value="Magento API Standards" id="Magento API Standards" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Magento API Standards', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Swagger DocBlock</td>
                                <td>
                                    <input type="checkbox" value="Swagger DocBlock" id="Swagger DocBlock" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Swagger DocBlock', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Used for</td>
                                <td>
                                    <input type="checkbox" value="Used for" id="Used for" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Used for', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Used in</td>
                                <td>
                                    <input type="checkbox" value="Used in" id="Used in" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Used in', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column" @if (!empty($dynamicColumnsToShow) && in_array('Action', $dynamicColumnsToShow)) checked @endif>
                                </td>
                            </tr>
                        </div>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
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
                url: '{{route('postman.column.update')}}',
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