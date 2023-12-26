<div id="ppdatatablecolumnvisibilityList" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Columns Visibility Listing</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('purchase-product.column.update') }}" method="POST" id="postman-column-update">
                @csrf
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Column Name</b></td>
                            <td class="text-center"><b>hide</b></td>
                        </tr>
                        <div id="columnVisibilityControls">
                            <tr>
                                <td>#</td>
                                <td>
                                    <input type="checkbox" value="checkbox" id="checkbox" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('#', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>ID</td>
                                <td>
                                    <input type="checkbox" value="ID" id="ID" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('ID', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Customer</td>
                                <td>
                                    <input type="checkbox" value="Customer" id="Customer" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('Customer', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Supplier</td>
                                <td>
                                    <input type="checkbox" value="Supplier" id="Supplier" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('Supplier', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Product</td>
                                <td>
                                    <input type="checkbox" value="Product" id="Product" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('Product', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Buying Price</td>
                                <td>
                                    <input type="checkbox" value="Buying Price" id="Buying_Price" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('Buying Price', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Selling Price (EUR)</td>
                                <td>
                                    <input type="checkbox" value="Selling Price EUR" id="Selling_Price_EUR" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('Selling Price EUR', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Selling price</td>
                                <td>
                                    <input type="checkbox" value="Selling price" id="Selling_price" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('Selling price', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Order Date</td>
                                <td>
                                    <input type="checkbox" value="Order Date" id="Order_Date" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('Order Date', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Del Date</td>
                                <td>
                                    <input type="checkbox" value="Del Date" id="Del_Date" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('Del Date', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Inv Status</td>
                                <td>
                                    <input type="checkbox" value="Inv Status" id="Inv_Status" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('Inv Status', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    <input type="checkbox" value="Status" id="Status" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('Status', $dynamicColumnsToShowPp)) checked @endif>
                                </td>
                            </tr>
                            <tr>
                                <td>Action</td>
                                <td>
                                    <input type="checkbox" value="Action" id="Action" name="column_pp[]" @if (!empty($dynamicColumnsToShowPp) && in_array('Action', $dynamicColumnsToShowPp)) checked @endif>
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