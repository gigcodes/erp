<div id="newFlowChartModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="mt-0">Add new flow chart</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form style="padding:10px;" action="{{ route('vendor.flowchart.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" class="form-control" name="name" placeholder="Name" value="{{ old('name') }}" required>

                    @if ($errors->has('name'))
                        <div class="alert alert-danger">{{$errors->first('name')}}</div>
                    @endif
                </div>

                <div class="form-group">
                    <input type="number" class="form-control" name="sorting" placeholder="Sorting" value="{{ old('sorting') }}" required>

                    @if ($errors->has('sorting'))
                        <div class="alert alert-danger">{{$errors->first('sorting')}}</div>
                    @endif
                </div>

                <button type="submit" class="btn btn-secondary">Add Flow Chart</button>
            </form>

            <div class="form-group col-md-12">
                <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                    <tr>
                        <td class="text-center"><b>Flow Chart</b></td>
                        <td class="text-center"><b>Sorting</b></td>
                    </tr>
                    <?php
                    foreach ($vendor_flow_charts as $vendorflowchart) { ?>
                    <tr>
                        <td><?php echo $vendorflowchart->name; ?></td>
                        
                        <td>
                            <?php echo $vendorflowchart->sorting; ?>
                        </td>                              
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>