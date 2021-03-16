<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped">
            <tr>
                <th width="1%">No</th>
                <th width="1%">Brand ID</th>
                <th width="5%">Brand</th>
                <th width="15%">Total</th>
            </tr>
            <tbody class="">
                <?php foreach($inventory as $c => $i) { ?>
                    <tr style="<?php echo ($i->total <= 10 ) ? 'background: red' : ''; ?>">
                        <td><?php echo $c+1 ?></td>  
                        <td><?php echo $i->brand ?></td>  
                        <td><?php echo $i->name ?></td>  
                        <td><?php echo $i->total ?></td>  
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>