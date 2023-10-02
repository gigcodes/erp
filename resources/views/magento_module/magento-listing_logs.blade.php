@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', "Magento Modules")

@section('content')
<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Magento Modules Logs ({{$magento_modules_count}})</h2>
    </div>
    <br>
    @if(session()->has('success'))
	    <div class="col-lg-12 margin-tb">
		    <div class="alert alert-success">
		        {{ session()->get('success') }}
		    </div>
		</div>    
	@endif
    <div class="col-lg-12 margin-tb">
		<div class="col-md-12 margin-tb" id="page-view-result">
			<div class="row table-horizontal-scroll">
				<table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Module Name</th>
                            <th>Command</th>
							<th>Job Id</th>
							<th>Status</th>
							<th>Response</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody id="preview-history-tbody">

				    <tbody id="environment_data">
						<?php 
						if($magento_modules) {
							foreach($magento_modules as $mmkey => $magento_module) {
						?>
						<tr class="trrow">
							<td width="10%" class="expand-row">
								{{$magento_module->magento_module_id}}
							</td>
							<td width="10%" class="expand-row">
								{{$magento_module->module}}
							</td>
							<td width="10%" class="expand-row">
								{{$magento_module->command}}
							</td>
							<td width="10%" class="expand-row">
								{{$magento_module->job_id}}
							</td>
							<td width="10%" class="expand-row">
								{{$magento_module->status}}
							</td>
							<td width="10%" class="expand-row">
								{{$magento_module->response}}
							</td>
							<td width="10%" class="expand-row">
								{{$magento_module->updated_at}}
							</td>
						</tr>
						<?php } ?>
						<?php } ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection

