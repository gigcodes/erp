@if(isset($data) && count($data) > 0)
@foreach($data as $value)
<tr>
<td>{{$value->title}}</td>
<td>{{$value->filename}}</td>
<td><a href="<?php echo "http://localhost/erp/storage/global_files_and_attachments_file/".''.$value->filename ?>" target="_blank" download="download"><?php echo "http://localhost/erp/storage/global_files_and_attachments_file/".''.$value->filename; ?></a></td>
<td>{{$value->user->name}}</td>
<td>{{$value->created_at}}</td>
</tr>
@endforeach
@else
<tr><td colspan="5">No Data Found.</td></tr>
@endif